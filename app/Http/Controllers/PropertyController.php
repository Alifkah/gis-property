<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\District;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PropertyController extends Controller
{
    /** Public browsable listing page with filters. */
    public function browse(Request $request): View
    {
        $query = Property::query()->with('images');

        if (DB::getDriverName() === 'pgsql') {
            $query->select('properties.*')
                ->addSelect(DB::raw('(select name from districts d where ST_Contains(d.geom, properties.geom) limit 1) as district_name'));
        }

        // Keyword search
        if ($q = $request->get('q')) {
            $query->where('title', 'ilike', "%{$q}%");
        }

        // Type filter
        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        // Status filter
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Price range
        if ($price = $request->get('price')) {
            [$min, $max] = explode('-', $price);
            $query->whereBetween('price', [(float) $min, (float) $max]);
        }

        // District filter (PostGIS only)
        if ($district = $request->get('district')) {
            if (DB::getDriverName() === 'pgsql') {
                $query->whereRaw(
                    'EXISTS (SELECT 1 FROM districts d WHERE d.name = ? AND ST_Contains(d.geom, properties.geom))',
                    [$district]
                );
            }
        }

        // Sort
        match ($request->get('sort', 'newest')) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            default => $query->orderByDesc('created_at'),
        };

        $properties = $query->paginate(12)->withQueryString();

        // IDs favorit user yang sedang login
        $favoritedIds = Auth::check()
            ? Auth::user()->favorites()->pluck('property_id')->all()
            : [];

        $types = Property::distinct()->orderBy('type')->pluck('type');
        $districts = District::orderBy('name')->get(['name']);

        return view('properties.index', compact('properties', 'favoritedIds', 'types', 'districts'));
    }

    public function index(Request $request): JsonResponse
    {
        $isPgsql = DB::getDriverName() === 'pgsql';

        if ($isPgsql) {
            $rows = Property::query()
                ->select('properties.id', 'properties.type', 'properties.title', 'properties.price')
                ->addSelect(DB::raw('ST_AsGeoJSON(properties.geom) as geojson'))
                ->get();

            $features = $rows->map(function ($property) {
                return [
                    'type' => 'Feature',
                    'properties' => [
                        'id' => (int) $property->id,
                        'type' => $property->type,
                        'title' => $property->title,
                        'price' => (float) $property->price,
                    ],
                    'geometry' => json_decode($property->geojson, true),
                ];
            });

            return response()->json([
                'type' => 'FeatureCollection',
                'features' => $features,
            ]);
        }

        $rows = Property::query()
            ->select('id', 'type', 'title', 'price', 'geom')
            ->get();

        $features = $rows->map(function (Property $property) {
            $point = $this->extractPoint($property->geom);

            return [
                'type' => 'Feature',
                'properties' => [
                    'id' => (int) $property->id,
                    'type' => $property->type,
                    'title' => $property->title,
                    'price' => (float) $property->price,
                ],
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [$point['lng'], $point['lat']],
                ],
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    public function show(Property $property): View
    {
        $isPgsql = DB::getDriverName() === 'pgsql';

        $property->load(['images', 'user']);

        if ($isPgsql) {
            $point = DB::table('properties')
                ->where('id', $property->id)
                ->select(DB::raw('ST_X(geom::geometry) as lng'), DB::raw('ST_Y(geom::geometry) as lat'))
                ->first();

            $districtName = DB::table('districts')
                ->whereRaw('ST_Contains(districts.geom, (select geom from properties where id = ?))', [$property->id])
                ->value('name');

            $floodZone = DB::table('flood_zones')
                ->select('area_name', 'risk_level')
                ->whereRaw('ST_Intersects(flood_zones.geom, (select geom from properties where id = ?))', [$property->id])
                ->first();

            $isFloodSafe = $floodZone === null;

            $isNew = DB::table('properties')
                ->where('id', $property->id)
                ->whereRaw("created_at >= NOW() - interval '14 days'")
                ->exists();

            $nearestAmenities = DB::table('amenities')
                ->select('id', 'name', 'type')
                ->selectRaw('ST_Distance(amenities.geom::geography, ((select geom from properties where id = ?))::geography) as distance_m', [
                    $property->id,
                ])
                ->orderBy('distance_m')
                ->limit(5)
                ->get();
        } else {
            $point = (object) $this->extractPoint($property->geom);
            $districtName = null;
            $isFloodSafe = true;
            $isNew = false;
            $nearestAmenities = $this->nearestAmenitiesForPoint(
                Amenity::query()->get(['id', 'name', 'type', 'geom']),
                (float) $point->lat,
                (float) $point->lng,
            );
        }

        return view('properties.show', [
            'property' => $property,
            'point' => [
                'lat' => (float) $point->lat,
                'lng' => (float) $point->lng,
            ],
            'districtName' => $districtName,
            'isNew' => $isNew,
            'isFloodSafe' => $isFloodSafe,
            'nearestAmenities' => $nearestAmenities,
        ]);
    }

    /**
     * @return array{lat: float, lng: float}
     */
    private function extractPoint(?string $wkt): array
    {
        if (! is_string($wkt)) {
            return ['lat' => 0.0, 'lng' => 0.0];
        }

        if (preg_match('/POINT\\(([-0-9.]+) ([-0-9.]+)\\)/', $wkt, $matches) !== 1) {
            return ['lat' => 0.0, 'lng' => 0.0];
        }

        return ['lat' => (float) $matches[2], 'lng' => (float) $matches[1]];
    }

    /**
     * @param  Collection<int, Amenity>  $amenities
     */
    private function nearestAmenitiesForPoint(Collection $amenities, float $lat, float $lng): Collection
    {
        return $amenities
            ->map(function (Amenity $amenity) use ($lat, $lng) {
                $point = $this->extractPoint($amenity->geom);
                $amenity->distance_m = $this->distanceMeters($lat, $lng, (float) $point['lat'], (float) $point['lng']);

                return $amenity;
            })
            ->sortBy('distance_m')
            ->values()
            ->take(5)
            ->values();
    }

    private function distanceMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earthRadius * 2 * asin(min(1.0, sqrt($a)));
    }
}
