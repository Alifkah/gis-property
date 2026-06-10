<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\MarketDemand;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExploreApiController extends Controller
{
    private const DEFAULT_LAT = -0.5;

    private const DEFAULT_LNG = 117.15;

    private const PER_PAGE = 20;

    private const MAX_PER_PAGE = 100;

    public function amenities(Request $request): JsonResponse
    {
        $isPgsql = DB::getDriverName() === 'pgsql';
        $type = $request->filled('type') ? $request->string('type')->toString() : null;

        $cacheKey = 'amenities_api_'.($type ?? 'all');
        $data = Cache::remember($cacheKey, 300, function () use ($isPgsql, $type) {
            if ($isPgsql) {
                $query = Amenity::query()
                    ->select('amenities.id', 'amenities.name', 'amenities.type')
                    ->addSelect(DB::raw('ST_X(geom::geometry) as lng'))
                    ->addSelect(DB::raw('ST_Y(geom::geometry) as lat'))
                    ->orderBy('name');

                if ($type !== null) {
                    $query->where('type', $type);
                }

                return $query->get()->map(fn ($a) => [
                    'id' => (int) $a->id,
                    'name' => $a->name,
                    'type' => $a->type,
                    'lat' => (float) $a->lat,
                    'lng' => (float) $a->lng,
                ])->values()->all();
            }

            return Amenity::query()
                ->select('id', 'name', 'type', 'geom')
                ->when($type !== null, fn ($q) => $q->where('type', $type))
                ->orderBy('name')
                ->get()
                ->map(function (Amenity $amenity) {
                    $point = $this->extractPoint($amenity->geom);

                    return [
                        'id' => (int) $amenity->id,
                        'name' => $amenity->name,
                        'type' => $amenity->type,
                        'lat' => (float) $point['lat'],
                        'lng' => (float) $point['lng'],
                    ];
                })->values()->all();
        });

        return response()->json(['data' => $data])
            ->header('Cache-Control', 'public, max-age=300');
    }

    public function properties(Request $request): JsonResponse
    {
        $isPgsql = DB::getDriverName() === 'pgsql';
        $isLocalDisk = config('filesystems.disks.public.driver') === 'local';
        $perPage = min(max((int) $request->integer('per_page', self::PER_PAGE), 1), self::MAX_PER_PAGE);
        $page = max((int) $request->integer('page', 1), 1);
        $radius = $this->parseRadiusMeters($request);
        $center = $this->parseCenter($request);

        if ($center !== null) {
            MarketDemand::query()->create([
                'latitude' => (float) $center['lat'],
                'longitude' => (float) $center['lng'],
            ]);
        }
        $amenityRadius = $this->parseRadiusMetersFromKey($request, 'amenity_radius_m');
        $amenityType = $request->filled('amenity_type') ? $request->string('amenity_type')->toString() : null;
        $amenityId = $request->filled('amenity_id') ? (int) $request->input('amenity_id') : null;

        if ($isPgsql) {
            $query = $this->basePgsqlQuery($request);

            if ($radius !== null && $center !== null) {
                $query->whereRaw(
                    'ST_DWithin(properties.geom::geography, ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography, ?)',
                    [$center['lng'], $center['lat'], $radius],
                );
            }

            if ($amenityRadius !== null && ($amenityType !== null || $amenityId !== null)) {
                if ($amenityId !== null) {
                    $query->whereRaw(
                        'ST_DWithin(properties.geom::geography, (select geom from amenities where id = ?)::geography, ?)',
                        [$amenityId, $amenityRadius],
                    );
                } else {
                    $query->whereRaw(
                        'EXISTS (select 1 from amenities a where a.type = ? and ST_DWithin(properties.geom::geography, a.geom::geography, ?))',
                        [$amenityType, $amenityRadius],
                    );
                }
            }

            $total = (clone $query)->count();

            $rows = $query
                ->select('properties.*')
                ->addSelect(DB::raw('ST_X(geom::geometry) as lng'))
                ->addSelect(DB::raw('ST_Y(geom::geometry) as lat'))
                ->addSelect(DB::raw("(properties.created_at >= NOW() - interval '14 days') as is_new"))
                ->addSelect(DB::raw('NOT EXISTS (select 1 from flood_zones f where ST_Intersects(f.geom, properties.geom)) as is_flood_safe'))
                ->addSelect(DB::raw('(select name from districts d where ST_Contains(d.geom, properties.geom) limit 1) as district_name'))
                ->addSelect(DB::raw('(select path from property_images pi where pi.property_id = properties.id order by pi.order asc limit 1) as first_image_path'))
                ->when($amenityRadius !== null && ($amenityType !== null || $amenityId !== null), function ($q) use ($amenityType, $amenityId) {
                    if ($amenityId !== null) {
                        $q->selectRaw(
                            'ST_Distance(properties.geom::geography, (select geom from amenities where id = ?)::geography) as amenity_distance_m',
                            [$amenityId],
                        );
                    } else {
                        $q->selectRaw(
                            '(select MIN(ST_Distance(properties.geom::geography, a.geom::geography)) from amenities a where a.type = ?) as amenity_distance_m',
                            [$amenityType],
                        );
                    }
                })
                ->forPage($page, $perPage)
                ->get();

            $items = $rows->map(function ($property) use ($isLocalDisk) {
                return [
                    'id' => (int) $property->id,
                    'slug' => $property->slug,
                    'type' => $property->type,
                    'title' => $property->title,
                    'price' => (float) $property->price,
                    'land_area' => (int) $property->land_area,
                    'bedroom' => (int) $property->bedroom,
                    'bathroom' => (int) $property->bathroom,
                    'lat' => (float) $property->lat,
                    'lng' => (float) $property->lng,
                    'district_name' => $property->district_name,
                    'status' => $property->status,
                    'is_new' => (bool) $property->is_new,
                    'is_flood_safe' => (bool) $property->is_flood_safe,
                    'amenity_distance_m' => isset($property->amenity_distance_m) ? (float) $property->amenity_distance_m : null,
                    'image_url' => ($property->first_image_path && (! $isLocalDisk || Storage::disk('public')->exists($property->first_image_path))) ? Storage::disk('public')->url($property->first_image_path) : null,
                ];
            })->values();
        } else {
            $query = $this->baseSqliteQuery($request);
            $total = (clone $query)->count();

            $rows = $query
                ->select('id', 'slug', 'type', 'title', 'price', 'land_area', 'bedroom', 'bathroom', 'status', 'geom')
                ->get();

            // Eager load first image to avoid N+1
            $rows->load('images');

            $rows = $rows->map(function (Property $property) use ($isLocalDisk) {
                $point = $this->extractPoint($property->geom);
                $firstImage = $property->images->first();

                return [
                    'id' => (int) $property->id,
                    'slug' => $property->slug,
                    'type' => $property->type,
                    'title' => $property->title,
                    'price' => (float) $property->price,
                    'land_area' => (int) $property->land_area,
                    'bedroom' => (int) $property->bedroom,
                    'bathroom' => (int) $property->bathroom,
                    'lat' => (float) $point['lat'],
                    'lng' => (float) $point['lng'],
                    'district_name' => null,
                    'status' => $property->status,
                    'is_new' => false,
                    'is_flood_safe' => true,
                    'amenity_distance_m' => null,
                    'image_url' => ($firstImage && (! $isLocalDisk || Storage::disk('public')->exists($firstImage->path))) ? Storage::disk('public')->url($firstImage->path) : null,
                ];
            })
                ->values();

            if ($amenityRadius !== null && ($amenityType !== null || $amenityId !== null)) {
                $amenities = $this->amenitiesForFilter($amenityType, $amenityId);

                $rows = $rows
                    ->map(function (array $item) use ($amenities) {
                        $item['amenity_distance_m'] = $this->minDistanceToAmenities(
                            (float) $item['lat'],
                            (float) $item['lng'],
                            $amenities,
                        );

                        return $item;
                    })
                    ->filter(function (array $item) use ($amenityRadius) {
                        if ($item['amenity_distance_m'] === null) {
                            return false;
                        }

                        return (float) $item['amenity_distance_m'] <= $amenityRadius;
                    })
                    ->values();
            }

            if ($radius !== null && $center !== null) {
                $rows = $rows
                    ->map(function (array $item) use ($center) {
                        $item['distance_m'] = $this->distanceMeters(
                            (float) $center['lat'],
                            (float) $center['lng'],
                            (float) $item['lat'],
                            (float) $item['lng'],
                        );

                        return $item;
                    })
                    ->filter(fn (array $item) => (float) $item['distance_m'] <= $radius)
                    ->values();
            }

            $total = $rows->count();
            $items = $rows->forPage($page, $perPage)->values();
        }

        return response()->json([
            'data' => $items,
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
            ],
        ]);
    }

    public function propertiesGeojson(Request $request): JsonResponse
    {
        $isPgsql = DB::getDriverName() === 'pgsql';
        $perPage = min(max((int) $request->integer('per_page', self::PER_PAGE), 1), self::MAX_PER_PAGE);
        $page = max((int) $request->integer('page', 1), 1);
        $radius = $this->parseRadiusMeters($request);
        $center = $this->parseCenter($request);
        $amenityRadius = $this->parseRadiusMetersFromKey($request, 'amenity_radius_m');
        $amenityType = $request->filled('amenity_type') ? $request->string('amenity_type')->toString() : null;
        $amenityId = $request->filled('amenity_id') ? (int) $request->input('amenity_id') : null;

        if ($isPgsql) {
            $query = $this->basePgsqlQuery($request);

            if ($radius !== null && $center !== null) {
                $query->whereRaw(
                    'ST_DWithin(properties.geom::geography, ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography, ?)',
                    [$center['lng'], $center['lat'], $radius],
                );
            }

            if ($amenityRadius !== null && ($amenityType !== null || $amenityId !== null)) {
                if ($amenityId !== null) {
                    $query->whereRaw(
                        'ST_DWithin(properties.geom::geography, (select geom from amenities where id = ?)::geography, ?)',
                        [$amenityId, $amenityRadius],
                    );
                } else {
                    $query->whereRaw(
                        'EXISTS (select 1 from amenities a where a.type = ? and ST_DWithin(properties.geom::geography, a.geom::geography, ?))',
                        [$amenityType, $amenityRadius],
                    );
                }
            }

            $rows = $query
                ->select('properties.id', 'properties.slug', 'properties.type', 'properties.title', 'properties.price')
                ->addSelect(DB::raw('ST_AsGeoJSON(properties.geom) as geojson'))
                ->forPage($page, $perPage)
                ->get();

            return response()->json([
                'type' => 'FeatureCollection',
                'features' => $rows->map(function ($property) {
                    return [
                        'type' => 'Feature',
                        'properties' => [
                            'id' => (int) $property->id,
                            'slug' => $property->slug,
                            'type' => $property->type,
                            'title' => $property->title,
                            'price' => (float) $property->price,
                        ],
                        'geometry' => json_decode($property->geojson, true),
                    ];
                }),
            ]);
        }

        $query = $this->baseSqliteQuery($request)
            ->select('id', 'slug', 'type', 'title', 'price', 'geom');

        $rows = $query->get();

        if ($amenityRadius !== null && ($amenityType !== null || $amenityId !== null)) {
            $amenities = $this->amenitiesForFilter($amenityType, $amenityId);

            $rows = $rows
                ->filter(function (Property $property) use ($amenities, $amenityRadius) {
                    $point = $this->extractPoint($property->geom);
                    $distance = $this->minDistanceToAmenities((float) $point['lat'], (float) $point['lng'], $amenities);

                    return $distance !== null && $distance <= $amenityRadius;
                })
                ->values();
        }

        if ($radius !== null && $center !== null) {
            $rows = $rows
                ->filter(function (Property $property) use ($center, $radius) {
                    $point = $this->extractPoint($property->geom);

                    return $this->distanceMeters(
                        (float) $center['lat'],
                        (float) $center['lng'],
                        (float) $point['lat'],
                        (float) $point['lng'],
                    ) <= $radius;
                })
                ->values();
        }

        $rows = $rows->forPage($page, $perPage)->values();

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $rows->map(function (Property $property) {
                $point = $this->extractPoint($property->geom);

                return [
                    'type' => 'Feature',
                    'properties' => [
                        'id' => (int) $property->id,
                        'slug' => $property->slug,
                        'type' => $property->type,
                        'title' => $property->title,
                        'price' => (float) $property->price,
                    ],
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [$point['lng'], $point['lat']],
                    ],
                ];
            }),
        ]);
    }

    private function basePgsqlQuery(Request $request)
    {
        $query = Property::query();

        $query->whereRaw(
            'NOT ST_Equals(properties.geom, ST_SetSRID(ST_MakePoint(?, ?), 4326))',
            [self::DEFAULT_LNG, self::DEFAULT_LAT],
        );

        if ($request->filled('type')) {
            $query->where('type', $request->string('type')->toString());
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', (float) $request->input('price_min'));
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', (float) $request->input('price_max'));
        }

        if ((string) $request->input('flood_safe') === '1') {
            $query->whereRaw('NOT EXISTS (select 1 from flood_zones f where ST_Intersects(f.geom, properties.geom))');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('district')) {
            $query->whereRaw(
                'EXISTS (select 1 from districts d where d.name = ? and ST_Contains(d.geom, properties.geom))',
                [$request->string('district')->toString()],
            );
        }

        $sort = strtolower((string) $request->input('sort', 'desc')) === 'asc' ? 'asc' : 'desc';
        $query->orderBy('price', $sort);

        return $query;
    }

    private function baseSqliteQuery(Request $request)
    {
        $query = Property::query()
            ->where('geom', '!=', 'POINT('.self::DEFAULT_LNG.' '.self::DEFAULT_LAT.')');

        if ($request->filled('type')) {
            $query->where('type', $request->string('type')->toString());
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', (float) $request->input('price_min'));
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', (float) $request->input('price_max'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        // district filter is geospatial — not supported on SQLite fallback

        $sort = strtolower((string) $request->input('sort', 'desc')) === 'asc' ? 'asc' : 'desc';
        $query->orderBy('price', $sort);

        return $query;
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

    private function parseRadiusMeters(Request $request): ?float
    {
        if (! $request->filled('radius_m')) {
            return null;
        }

        $radius = (float) $request->input('radius_m');

        if ($radius <= 0) {
            return null;
        }

        return min($radius, 50000.0);
    }

    private function parseRadiusMetersFromKey(Request $request, string $key): ?float
    {
        if (! $request->filled($key)) {
            return null;
        }

        $radius = (float) $request->input($key);

        if ($radius <= 0) {
            return null;
        }

        return min($radius, 50000.0);
    }

    /**
     * @return array{lat: float, lng: float}|null
     */
    private function parseCenter(Request $request): ?array
    {
        if (! $request->filled('center_lat') || ! $request->filled('center_lng')) {
            return null;
        }

        $lat = (float) $request->input('center_lat');
        $lng = (float) $request->input('center_lng');

        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            return null;
        }

        return ['lat' => $lat, 'lng' => $lng];
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

    /**
     * @return Collection<int, array{lat: float, lng: float}>
     */
    private function amenitiesForFilter(?string $type, ?int $id): Collection
    {
        $query = Amenity::query()->select('id', 'type', 'geom');

        if ($id !== null) {
            $query->where('id', $id);
        } elseif ($type !== null) {
            $query->where('type', $type);
        }

        return $query->get()->map(function (Amenity $amenity) {
            return $this->extractPoint($amenity->geom);
        })->values();
    }

    /**
     * @param  Collection<int, array{lat: float, lng: float}>  $amenities
     */
    private function minDistanceToAmenities(float $lat, float $lng, Collection $amenities): ?float
    {
        if ($amenities->isEmpty()) {
            return null;
        }

        $min = null;

        foreach ($amenities as $amenity) {
            $distance = $this->distanceMeters($lat, $lng, (float) $amenity['lat'], (float) $amenity['lng']);
            $min = $min === null ? $distance : min($min, $distance);
        }

        return $min;
    }
}
