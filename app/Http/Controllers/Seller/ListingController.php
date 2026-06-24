<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Property;
use App\Models\PropertyAlert;
use App\Models\PropertyImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ListingController extends Controller
{
    private const DEFAULT_LAT = -0.5;

    private const DEFAULT_LNG = 117.15;

    public function index(): View
    {
        $sellerId = auth()->id();
        $isPgsql = DB::getDriverName() === 'pgsql';

        $query = Property::query()
            ->where('user_id', $sellerId)
            ->with('images')
            ->orderByDesc('created_at');

        if ($isPgsql) {
            $query->select('properties.*')
                ->addSelect(DB::raw('(select name from districts d where ST_Contains(d.geom, properties.geom) limit 1) as district_name'));
        }

        $properties = $query->get();

        if ($isPgsql) {
            // Chart 1: Seller's Properties per District
            $propertiesPerDistrict = DB::select('
                SELECT d.name, COUNT(p.id) as total
                FROM districts d
                INNER JOIN properties p ON ST_Contains(d.geom, p.geom) AND p.user_id = ?
                GROUP BY d.name
                ORDER BY total DESC, d.name ASC
            ', [$sellerId]);

            // Chart 2: Seller's Flood zone intersections
            $floodSafeCount = DB::table('properties')
                ->where('user_id', $sellerId)
                ->whereRaw('NOT EXISTS (SELECT 1 FROM flood_zones f WHERE ST_Intersects(f.geom, properties.geom))')
                ->count();
            $floodRiskCount = DB::table('properties')
                ->where('user_id', $sellerId)
                ->whereRaw('EXISTS (SELECT 1 FROM flood_zones f WHERE ST_Intersects(f.geom, properties.geom))')
                ->count();

            // Chart 3: Seller Average vs Market Average price per m2 trend
            $sellerPriceTrend = DB::table('properties')
                ->where('user_id', $sellerId)
                ->select(
                    DB::raw("TO_CHAR(created_at, 'Mon YYYY') as period"),
                    DB::raw("TO_CHAR(created_at, 'YYYY-MM') as sort_key"),
                    DB::raw('ROUND(AVG(price / NULLIF(land_area, 0))) as avg_price_per_sqm')
                )
                ->groupBy('period', 'sort_key')
                ->orderBy('sort_key')
                ->get();

            $marketPriceTrend = DB::table('properties')
                ->select(
                    DB::raw("TO_CHAR(created_at, 'Mon YYYY') as period"),
                    DB::raw("TO_CHAR(created_at, 'YYYY-MM') as sort_key"),
                    DB::raw('ROUND(AVG(price / NULLIF(land_area, 0))) as avg_price_per_sqm')
                )
                ->groupBy('period', 'sort_key')
                ->orderBy('sort_key')
                ->get();
        } else {
            // Chart 1: Seller's Properties per District (SQLite fallback)
            $districts = District::pluck('name')->all();
            $counts = array_fill_keys($districts, 0);
            $sellerProperties = Property::where('user_id', $sellerId)->get();
            foreach ($sellerProperties as $p) {
                $coords = $this->extractPoint($p->geom);
                $dName = $this->getDistrictNameForLatLng($coords['lat'], $coords['lng']);
                if (isset($counts[$dName])) {
                    $counts[$dName]++;
                } else {
                    $counts['Samarinda'] = ($counts['Samarinda'] ?? 0) + 1;
                }
            }
            arsort($counts);
            $propertiesPerDistrict = collect($counts)
                ->filter(fn ($total) => $total > 0)
                ->map(fn ($total, $name) => (object) ['name' => $name, 'total' => $total])
                ->values()
                ->all();

            // Chart 2: Seller's Flood zone intersections (SQLite fallback)
            $floodSafeCount = 0;
            $floodRiskCount = 0;
            foreach ($sellerProperties as $p) {
                $coords = $this->extractPoint($p->geom);
                if ($this->isPointInFloodZoneSqlite($coords['lat'], $coords['lng'])) {
                    $floodRiskCount++;
                } else {
                    $floodSafeCount++;
                }
            }

            // Chart 3: Seller vs Market price per m2 trend (SQLite fallback)
            $sellerPriceTrend = DB::table('properties')
                ->where('user_id', $sellerId)
                ->select(
                    DB::raw("strftime('%m-%Y', created_at) as period"),
                    DB::raw("strftime('%Y-%m', created_at) as sort_key"),
                    DB::raw('ROUND(AVG(price / CASE WHEN land_area = 0 THEN 1 ELSE land_area END)) as avg_price_per_sqm')
                )
                ->groupBy('period', 'sort_key')
                ->orderBy('sort_key')
                ->get();

            $marketPriceTrend = DB::table('properties')
                ->select(
                    DB::raw("strftime('%m-%Y', created_at) as period"),
                    DB::raw("strftime('%Y-%m', created_at) as sort_key"),
                    DB::raw('ROUND(AVG(price / CASE WHEN land_area = 0 THEN 1 ELSE land_area END)) as avg_price_per_sqm')
                )
                ->groupBy('period', 'sort_key')
                ->orderBy('sort_key')
                ->get();
        }

        $totalViews = $properties->sum('views_count');
        $totalClicks = $properties->sum('whatsapp_clicks_count');
        $topProperties = $properties->sortByDesc('views_count')->take(5);

        return view('seller.index', [
            'properties' => $properties,
            'propertiesPerDistrict' => $propertiesPerDistrict,
            'floodSafeCount' => $floodSafeCount,
            'floodRiskCount' => $floodRiskCount,
            'sellerPriceTrend' => $sellerPriceTrend,
            'marketPriceTrend' => $marketPriceTrend,
            'totalViews' => $totalViews,
            'totalClicks' => $totalClicks,
            'topProperties' => $topProperties,
        ]);
    }

    public function exportCsv()
    {
        $sellerId = auth()->id();
        $isPgsql = DB::getDriverName() === 'pgsql';

        $query = Property::query()->where('user_id', $sellerId);

        if ($isPgsql) {
            $query->select('properties.*')
                ->addSelect(DB::raw('(select name from districts d where ST_Contains(d.geom, properties.geom) limit 1) as district_name'))
                ->addSelect(DB::raw('NOT EXISTS (select 1 from flood_zones f where ST_Intersects(f.geom, properties.geom)) as is_flood_safe'));
        } else {
            $query->select('properties.*');
        }

        $properties = $query->orderByDesc('created_at')->get();

        if (! $isPgsql) {
            foreach ($properties as $p) {
                $coords = $this->extractPoint($p->geom);
                $p->district_name = $this->getDistrictNameForLatLng($coords['lat'], $coords['lng']);
                $p->is_flood_safe = ! $this->isPointInFloodZoneSqlite($coords['lat'], $coords['lng']);
            }
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="laporan_listing_saya_'.date('Ymd_His').'.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($properties) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

            fputcsv($file, [
                'ID Properti',
                'Judul',
                'Tipe',
                'Harga (Rp)',
                'Luas Tanah (m2)',
                'Luas Bangunan (m2)',
                'Kamar Tidur',
                'Kamar Mandi',
                'Status',
                'Kecamatan',
                'Aman Banjir',
                'Tanggal Pasang',
            ]);

            foreach ($properties as $p) {
                fputcsv($file, [
                    $p->id,
                    $p->title,
                    $p->type,
                    (float) $p->price,
                    $p->land_area,
                    $p->building_area,
                    $p->bedroom,
                    $p->bathroom,
                    $p->status,
                    $p->district_name ?? 'Samarinda',
                    ($p->is_flood_safe ?? true) ? 'Ya' : 'Tidak',
                    $p->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getDistrictNameForLatLng(float $lat, float $lng): string
    {
        if ($lng >= 117.05 && $lng < 117.15) {
            if ($lat >= -0.58 && $lat < -0.548) {
                return 'Loa Janan Ilir';
            }
            if ($lat >= -0.548 && $lat < -0.516) {
                return 'Samarinda Seberang';
            }
            if ($lat >= -0.516 && $lat < -0.484) {
                return 'Sungai Kunjang';
            }
            if ($lat >= -0.484 && $lat < -0.452) {
                return 'Samarinda Ilir';
            }
            if ($lat >= -0.452 && $lat <= -0.420) {
                return 'Samarinda Utara';
            }
        } elseif ($lng >= 117.15 && $lng <= 117.25) {
            if ($lat >= -0.58 && $lat < -0.548) {
                return 'Palaran';
            }
            if ($lat >= -0.548 && $lat < -0.516) {
                return 'Sambutan';
            }
            if ($lat >= -0.516 && $lat < -0.484) {
                return 'Samarinda Ulu';
            }
            if ($lat >= -0.484 && $lat < -0.452) {
                return 'Samarinda Kota';
            }
            if ($lat >= -0.452 && $lat <= -0.420) {
                return 'Sungai Pinang';
            }
        }

        return 'Samarinda';
    }

    private function isPointInFloodZoneSqlite(float $lat, float $lng): bool
    {
        return $lng >= 117.10 && $lng <= 117.20 && $lat >= -0.55 && $lat <= -0.47;
    }

    public function create(): View
    {
        return view('seller.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'type' => ['required', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric'],
            'land_area' => ['required', 'integer', 'min:0'],
            'building_area' => ['nullable', 'integer', 'min:0'],
            'bedroom' => ['nullable', 'integer', 'min:0'],
            'bathroom' => ['nullable', 'integer', 'min:0'],
            'images' => ['nullable', 'array', 'max:15'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);

        $isPgsql = DB::getDriverName() === 'pgsql';

        $property = Property::query()->create([
            'user_id' => auth()->id(),
            'type' => $data['type'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'land_area' => $data['land_area'],
            'building_area' => $data['building_area'] ?? 0,
            'bedroom' => $data['bedroom'] ?? 0,
            'bathroom' => $data['bathroom'] ?? 0,
            'status' => 'Tersedia',
            'geom' => $isPgsql
                ? DB::raw('ST_SetSRID(ST_MakePoint('.self::DEFAULT_LNG.', '.self::DEFAULT_LAT.'), 4326)')
                : 'POINT('.self::DEFAULT_LNG.' '.self::DEFAULT_LAT.')',
        ]);

        $this->storeImages($request, $property);

        return redirect()->route('seller.listings.location.edit', [
            'property' => $property->id,
        ]);
    }

    public function editLocation(Property $property): View
    {
        $this->authorize('update', $property);

        $isPgsql = DB::getDriverName() === 'pgsql';

        if ($isPgsql) {
            $point = DB::table('properties')
                ->where('id', $property->id)
                ->select(DB::raw('ST_X(geom::geometry) as lng'), DB::raw('ST_Y(geom::geometry) as lat'))
                ->first();

            $lat = (float) ($point->lat ?? self::DEFAULT_LAT);
            $lng = (float) ($point->lng ?? self::DEFAULT_LNG);
        } else {
            $coords = $this->extractPoint($property->geom);
            $lat = $coords['lat'];
            $lng = $coords['lng'];
        }

        return view('seller.location', [
            'property' => $property,
            'point' => [
                'lat' => $lat,
                'lng' => $lng,
            ],
        ]);
    }

    public function updateLocation(Request $request, Property $property): RedirectResponse
    {
        $this->authorize('update', $property);

        $data = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $isPgsql = DB::getDriverName() === 'pgsql';

        if ($isPgsql) {
            DB::update(
                'UPDATE properties SET geom = ST_SetSRID(ST_MakePoint(?, ?), 4326) WHERE id = ?',
                [(float) $data['lng'], (float) $data['lat'], $property->id],
            );
        } else {
            $property->update([
                'geom' => 'POINT('.(float) $data['lng'].' '.(float) $data['lat'].')',
            ]);
        }

        $property->refresh();
        PropertyAlert::checkAndNotify($property);

        return redirect()->route('properties.show', [
            'property' => $property->id,
        ])->with('success', 'Lokasi properti berhasil disimpan.');
    }

    public function edit(Property $property): View
    {
        $this->authorize('update', $property);

        $property->load('images');

        return view('seller.edit', [
            'property' => $property,
        ]);
    }

    public function update(Request $request, Property $property): RedirectResponse
    {
        $this->authorize('update', $property);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'type' => ['required', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric'],
            'land_area' => ['required', 'integer', 'min:0'],
            'building_area' => ['nullable', 'integer', 'min:0'],
            'bedroom' => ['nullable', 'integer', 'min:0'],
            'bathroom' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'string', 'max:20'],
            'images' => ['nullable', 'array', 'max:15'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'delete_images' => ['nullable', 'array'],
            'delete_images.*' => ['integer'],
        ]);

        $property->update([
            'type' => $data['type'],
            'title' => $data['title'],
            'description' => $data['description'] ?? $property->description,
            'price' => $data['price'],
            'land_area' => $data['land_area'],
            'building_area' => $data['building_area'] ?? 0,
            'bedroom' => $data['bedroom'] ?? 0,
            'bathroom' => $data['bathroom'] ?? 0,
            'status' => $data['status'] ?? $property->status,
        ]);

        if (! empty($data['delete_images'])) {
            $imagesToDelete = PropertyImage::query()
                ->where('property_id', $property->id)
                ->whereIn('id', $data['delete_images'])
                ->get();

            foreach ($imagesToDelete as $image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }
        }

        $this->storeImages($request, $property);

        return redirect()->route('seller.listings.index')
            ->with('success', 'Listing berhasil diperbarui.');
    }

    public function destroy(Property $property): RedirectResponse
    {
        $this->authorize('delete', $property);

        foreach ($property->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $property->delete();

        return redirect()->route('seller.listings.index')
            ->with('success', 'Listing berhasil dihapus.');
    }

    private function storeImages(Request $request, Property $property): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $existingCount = $property->images()->count();

        foreach ($request->file('images') as $index => $file) {
            $path = $file->store('properties', 'public');

            PropertyImage::query()->create([
                'property_id' => $property->id,
                'path' => $path,
                'order' => $existingCount + $index,
            ]);
        }
    }

    private function extractPoint(?string $wkt): array
    {
        if (! is_string($wkt)) {
            return ['lat' => self::DEFAULT_LAT, 'lng' => self::DEFAULT_LNG];
        }

        if (preg_match('/POINT\\(([-0-9.]+) ([-0-9.]+)\\)/', $wkt, $matches) !== 1) {
            return ['lat' => self::DEFAULT_LAT, 'lng' => self::DEFAULT_LNG];
        }

        return ['lat' => (float) $matches[2], 'lng' => (float) $matches[1]];
    }
}
