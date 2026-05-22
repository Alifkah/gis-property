<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CompetitorAnalysisController extends Controller
{
    private const DEFAULT_LAT = -0.5;
    private const DEFAULT_LNG = 117.15;

    public function index(): View
    {
        $properties = Property::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();

        return view('seller.competitor-analysis', [
            'properties' => $properties,
        ]);
    }

    public function analyze(Request $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        $radius = (float) $request->input('radius', 1000);
        $radius = min(max($radius, 100), 10000);

        $isPgsql = DB::getDriverName() === 'pgsql';

        if ($isPgsql) {
            return $this->analyzePgsql($property, $radius);
        }

        return $this->analyzeSqlite($property, $radius);
    }

    private function analyzePgsql(Property $property, float $radius): JsonResponse
    {
        $point = DB::table('properties')
            ->where('id', $property->id)
            ->select(DB::raw('ST_X(geom::geometry) as lng'), DB::raw('ST_Y(geom::geometry) as lat'))
            ->first();

        if (! $point) {
            return response()->json(['error' => 'Property location not found'], 404);
        }

        $lat = (float) $point->lat;
        $lng = (float) $point->lng;

        $competitors = Property::query()
            ->where('properties.id', '!=', $property->id)
            ->where('properties.type', $property->type)
            ->whereRaw(
                'ST_DWithin(properties.geom::geography, ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography, ?)',
                [$lng, $lat, $radius]
            )
            ->whereRaw(
                'NOT ST_Equals(properties.geom, ST_SetSRID(ST_MakePoint(?, ?), 4326))',
                [self::DEFAULT_LNG, self::DEFAULT_LAT]
            )
            ->with('user')
            ->select('properties.*')
            ->addSelect(DB::raw('ST_X(geom::geometry) as lng'))
            ->addSelect(DB::raw('ST_Y(geom::geometry) as lat'))
            ->addSelect(DB::raw('ST_Distance(properties.geom::geography, ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography) as distance_m'))
            ->addSelect(DB::raw('(select name from districts d where ST_Contains(d.geom, properties.geom) limit 1) as district_name'))
            ->addSelect(DB::raw('NOT EXISTS (select 1 from flood_zones f where ST_Intersects(f.geom, properties.geom)) as is_flood_safe'))
            ->setBindings([$lng, $lat], 'select')
            ->orderBy('distance_m')
            ->limit(20)
            ->get();

        $avgPrice = $competitors->avg('price');
        $minPrice = $competitors->min('price');
        $maxPrice = $competitors->max('price');
        $avgLandArea = $competitors->avg('land_area');
        $avgPricePerSqm = $competitors->filter(fn ($c) => $c->land_area > 0)
            ->avg(fn ($c) => $c->price / $c->land_area);

        $myPricePerSqm = $property->land_area > 0 ? $property->price / $property->land_area : 0;

        $pricePosition = 'kompetitif';
        if ($avgPrice > 0) {
            $diff = (($property->price - $avgPrice) / $avgPrice) * 100;
            if ($diff > 20) {
                $pricePosition = 'di atas rata-rata';
            } elseif ($diff < -20) {
                $pricePosition = 'di bawah rata-rata';
            }
        }

        return response()->json([
            'property' => [
                'id' => $property->id,
                'title' => $property->title,
                'type' => $property->type,
                'price' => (float) $property->price,
                'land_area' => (int) $property->land_area,
                'building_area' => (int) $property->building_area,
                'bedroom' => (int) $property->bedroom,
                'bathroom' => (int) $property->bathroom,
                'status' => $property->status,
                'lat' => $lat,
                'lng' => $lng,
                'price_per_sqm' => $myPricePerSqm,
            ],
            'competitors' => $competitors->map(function ($comp) {
                $pricePerSqm = $comp->land_area > 0 ? $comp->price / $comp->land_area : 0;

                return [
                    'id' => (int) $comp->id,
                    'title' => $comp->title,
                    'type' => $comp->type,
                    'price' => (float) $comp->price,
                    'land_area' => (int) $comp->land_area,
                    'building_area' => (int) $comp->building_area,
                    'bedroom' => (int) $comp->bedroom,
                    'bathroom' => (int) $comp->bathroom,
                    'status' => $comp->status,
                    'lat' => (float) $comp->lat,
                    'lng' => (float) $comp->lng,
                    'distance_m' => (float) $comp->distance_m,
                    'district_name' => $comp->district_name,
                    'is_flood_safe' => (bool) $comp->is_flood_safe,
                    'price_per_sqm' => $pricePerSqm,
                    'owner_name' => $comp->user->name ?? 'Unknown',
                    'owner_phone' => $comp->user->phone ?? null,
                ];
            })->values(),
            'statistics' => [
                'total_competitors' => $competitors->count(),
                'avg_price' => $avgPrice,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'avg_land_area' => $avgLandArea,
                'avg_price_per_sqm' => $avgPricePerSqm,
                'price_position' => $pricePosition,
                'radius_m' => $radius,
            ],
        ]);
    }

    private function analyzeSqlite(Property $property, float $radius): JsonResponse
    {
        $point = $this->extractPoint($property->geom);

        if (! $point) {
            return response()->json(['error' => 'Property location not found'], 404);
        }

        $lat = $point['lat'];
        $lng = $point['lng'];

        $allCompetitors = Property::query()
            ->where('id', '!=', $property->id)
            ->where('type', $property->type)
            ->where('geom', '!=', 'POINT('.self::DEFAULT_LNG.' '.self::DEFAULT_LAT.')')
            ->with('user')
            ->get();

        $competitors = $allCompetitors
            ->map(function ($comp) use ($lat, $lng) {
                $compPoint = $this->extractPoint($comp->geom);
                $distance = $this->distanceMeters($lat, $lng, $compPoint['lat'], $compPoint['lng']);

                $comp->setAttribute('lat', $compPoint['lat']);
                $comp->setAttribute('lng', $compPoint['lng']);
                $comp->setAttribute('distance_m', $distance);
                $comp->setAttribute('district_name', null);
                $comp->setAttribute('is_flood_safe', true);

                return $comp;
            })
            ->filter(fn ($comp) => $comp->distance_m <= $radius)
            ->sortBy('distance_m')
            ->take(20)
            ->values();

        $avgPrice = $competitors->avg('price');
        $minPrice = $competitors->min('price');
        $maxPrice = $competitors->max('price');
        $avgLandArea = $competitors->avg('land_area');
        $avgPricePerSqm = $competitors->filter(fn ($c) => $c->land_area > 0)
            ->avg(fn ($c) => $c->price / $c->land_area);

        $myPricePerSqm = $property->land_area > 0 ? $property->price / $property->land_area : 0;

        $pricePosition = 'kompetitif';
        if ($avgPrice > 0) {
            $diff = (($property->price - $avgPrice) / $avgPrice) * 100;
            if ($diff > 20) {
                $pricePosition = 'di atas rata-rata';
            } elseif ($diff < -20) {
                $pricePosition = 'di bawah rata-rata';
            }
        }

        return response()->json([
            'property' => [
                'id' => $property->id,
                'title' => $property->title,
                'type' => $property->type,
                'price' => (float) $property->price,
                'land_area' => (int) $property->land_area,
                'building_area' => (int) $property->building_area,
                'bedroom' => (int) $property->bedroom,
                'bathroom' => (int) $property->bathroom,
                'status' => $property->status,
                'lat' => $lat,
                'lng' => $lng,
                'price_per_sqm' => $myPricePerSqm,
            ],
            'competitors' => $competitors->map(function ($comp) {
                $pricePerSqm = $comp->land_area > 0 ? $comp->price / $comp->land_area : 0;

                return [
                    'id' => (int) $comp->id,
                    'title' => $comp->title,
                    'type' => $comp->type,
                    'price' => (float) $comp->price,
                    'land_area' => (int) $comp->land_area,
                    'building_area' => (int) $comp->building_area,
                    'bedroom' => (int) $comp->bedroom,
                    'bathroom' => (int) $comp->bathroom,
                    'status' => $comp->status,
                    'lat' => (float) $comp->lat,
                    'lng' => (float) $comp->lng,
                    'distance_m' => (float) $comp->distance_m,
                    'district_name' => $comp->district_name,
                    'is_flood_safe' => (bool) $comp->is_flood_safe,
                    'price_per_sqm' => $pricePerSqm,
                    'owner_name' => $comp->user->name ?? 'Unknown',
                    'owner_phone' => $comp->user->phone ?? null,
                ];
            })->values(),
            'statistics' => [
                'total_competitors' => $competitors->count(),
                'avg_price' => $avgPrice,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'avg_land_area' => $avgLandArea,
                'avg_price_per_sqm' => $avgPricePerSqm,
                'price_position' => $pricePosition,
                'radius_m' => $radius,
            ],
        ]);
    }

    /**
     * @return array{lat: float, lng: float}|null
     */
    private function extractPoint(?string $wkt): ?array
    {
        if (! is_string($wkt)) {
            return null;
        }

        if (preg_match('/POINT\\(([-0-9.]+) ([-0-9.]+)\\)/', $wkt, $matches) !== 1) {
            return null;
        }

        return ['lat' => (float) $matches[2], 'lng' => (float) $matches[1]];
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
