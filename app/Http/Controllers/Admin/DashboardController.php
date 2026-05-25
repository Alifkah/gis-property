<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\District;
use App\Models\FloodZone;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $isPgsql = DB::getDriverName() === 'pgsql';

        $totalProperties = Property::query()->count();
        $totalSellers = User::query()->where('is_admin', false)->count();
        $totalAmenities = Amenity::query()->count();
        $totalFloodZones = FloodZone::query()->count();

        $availableProperties = Property::query()->where('status', 'Tersedia')->count();
        $soldProperties = Property::query()->where('status', 'Terjual')->count();

        if ($isPgsql) {
            $recentProperties = Property::query()
                ->with('user')
                ->select('properties.*')
                ->addSelect(DB::raw('(select name from districts d where ST_Contains(d.geom, properties.geom) limit 1) as district_name'))
                ->orderByDesc('created_at')
                ->limit(8)
                ->get();

            // Chart 1: Properties per District
            $propertiesPerDistrict = DB::select('
                SELECT d.name, COUNT(p.id) as total
                FROM districts d
                INNER JOIN properties p ON ST_Contains(d.geom, p.geom)
                GROUP BY d.name
                ORDER BY total DESC, d.name ASC
            ');

            // Chart 2: Flood zone intersections
            $floodSafeCount = DB::table('properties')
                ->whereRaw('NOT EXISTS (SELECT 1 FROM flood_zones f WHERE ST_Intersects(f.geom, properties.geom))')
                ->count();
            $floodRiskCount = DB::table('properties')
                ->whereRaw('EXISTS (SELECT 1 FROM flood_zones f WHERE ST_Intersects(f.geom, properties.geom))')
                ->count();

            // Chart 3: Average price per m2 trend
            $priceTrend = DB::table('properties')
                ->select(
                    DB::raw("TO_CHAR(created_at, 'Mon YYYY') as period"),
                    DB::raw("TO_CHAR(created_at, 'YYYY-MM') as sort_key"),
                    DB::raw('ROUND(AVG(price / NULLIF(land_area, 0))) as avg_price_per_sqm')
                )
                ->groupBy('period', 'sort_key')
                ->orderBy('sort_key')
                ->get();
        } else {
            $recentProperties = Property::query()
                ->with('user')
                ->orderByDesc('created_at')
                ->limit(8)
                ->get()
                ->each(fn ($p) => $p->district_name = null);

            // Chart 1: Properties per District (SQLite fallback)
            $districts = District::pluck('name')->all();
            $counts = array_fill_keys($districts, 0);
            $properties = Property::all();
            foreach ($properties as $p) {
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

            // Chart 2: Flood zone intersections (SQLite fallback)
            $floodSafeCount = 0;
            $floodRiskCount = 0;
            foreach ($properties as $p) {
                $coords = $this->extractPoint($p->geom);
                if ($this->isPointInFloodZoneSqlite($coords['lat'], $coords['lng'])) {
                    $floodRiskCount++;
                } else {
                    $floodSafeCount++;
                }
            }

            // Chart 3: Average price per m2 trend (SQLite fallback)
            $priceTrend = DB::table('properties')
                ->select(
                    DB::raw("strftime('%m-%Y', created_at) as period"),
                    DB::raw("strftime('%Y-%m', created_at) as sort_key"),
                    DB::raw('ROUND(AVG(price / CASE WHEN land_area = 0 THEN 1 ELSE land_area END)) as avg_price_per_sqm')
                )
                ->groupBy('period', 'sort_key')
                ->orderBy('sort_key')
                ->get();
        }

        return view('admin.dashboard', [
            'totalProperties' => $totalProperties,
            'totalSellers' => $totalSellers,
            'totalAmenities' => $totalAmenities,
            'totalFloodZones' => $totalFloodZones,
            'availableProperties' => $availableProperties,
            'soldProperties' => $soldProperties,
            'recentProperties' => $recentProperties,
            'propertiesPerDistrict' => $propertiesPerDistrict,
            'floodSafeCount' => $floodSafeCount,
            'floodRiskCount' => $floodRiskCount,
            'priceTrend' => $priceTrend,
        ]);
    }

    public function exportCsv()
    {
        $isPgsql = DB::getDriverName() === 'pgsql';
        $query = Property::query()->with('user');

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
            'Content-Disposition' => 'attachment; filename="laporan_semua_properti_'.date('Ymd_His').'.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($properties) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

            fputcsv($file, [
                'ID Properti', 'Judul', 'Tipe', 'Harga (Rp)', 'Luas Tanah (m2)',
                'Luas Bangunan (m2)', 'Kamar Tidur', 'Kamar Mandi', 'Status',
                'Kecamatan', 'Aman Banjir', 'Tanggal Pasang', 'Penjual', 'Email Penjual',
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
                    $p->user->name ?? '-',
                    $p->user->email ?? '-',
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
}
