<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\District;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstimationController extends Controller
{
    private const DEFAULT_LAT = -0.5;

    private const DEFAULT_LNG = 117.15;

    public function estimate(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'type' => ['required', 'string', 'in:Rumah,Tanah'],
            'land_area' => ['required', 'integer', 'min:1'],
        ]);

        $lat = (float) $request->input('lat');
        $lng = (float) $request->input('lng');
        $type = $request->input('type');
        $landArea = (int) $request->input('land_area');

        $isPgsql = DB::getDriverName() === 'pgsql';

        if ($isPgsql) {
            return $this->estimatePgsql($lat, $lng, $type, $landArea);
        }

        return $this->estimateSqlite($lat, $lng, $type, $landArea);
    }

    private function estimatePgsql(float $lat, float $lng, string $type, int $landArea): JsonResponse
    {
        // 1. Get District containing point
        $district = DB::table('districts')
            ->whereRaw('ST_Contains(geom, ST_SetSRID(ST_MakePoint(?, ?), 4326))', [$lng, $lat])
            ->select('name')
            ->first();

        $districtName = $district ? $district->name : 'Samarinda';

        // 2. Calculate average price per m2 in the district
        $avgPriceRow = DB::table('properties')
            ->where('type', $type)
            ->whereRaw('ST_Contains((SELECT geom FROM districts WHERE name = ? LIMIT 1), geom)', [$districtName])
            ->where('land_area', '>', 0)
            ->whereRaw('NOT ST_Equals(geom, ST_SetSRID(ST_MakePoint(?, ?), 4326))', [self::DEFAULT_LNG, self::DEFAULT_LAT])
            ->select(DB::raw('AVG(price / land_area) as avg_price'))
            ->first();

        $avgPricePerSqm = $avgPriceRow && $avgPriceRow->avg_price
            ? (float) $avgPriceRow->avg_price
            : ($type === 'Rumah' ? 5000000 : 1500000);

        // 3. Base pricing estimation
        $basePrice = $avgPricePerSqm * $landArea;
        $factors = [];
        $multiplier = 1.0;

        // 4. Flood Risk Factor
        $floodZone = DB::table('flood_zones')
            ->whereRaw('ST_Intersects(geom, ST_SetSRID(ST_MakePoint(?, ?), 4326))', [$lng, $lat])
            ->select('risk_level')
            ->first();

        if ($floodZone) {
            $risk = $floodZone->risk_level;
            if ($risk === 'Tinggi') {
                $multiplier -= 0.15;
                $factors[] = ['name' => 'Kawasan Rawan Banjir (Risiko Tinggi)', 'impact' => '-15%', 'positive' => false];
            } elseif ($risk === 'Sedang') {
                $multiplier -= 0.10;
                $factors[] = ['name' => 'Kawasan Rawan Banjir (Risiko Sedang)', 'impact' => '-10%', 'positive' => false];
            } else {
                $multiplier -= 0.05;
                $factors[] = ['name' => 'Kawasan Rawan Banjir (Risiko Rendah)', 'impact' => '-5%', 'positive' => false];
            }
        } else {
            $multiplier += 0.10;
            $factors[] = ['name' => 'Kawasan Bebas Banjir', 'impact' => '+10%', 'positive' => true];
        }

        // 5. Proximity to POIs (within 1km)
        $poiCount = DB::table('amenities')
            ->whereRaw('ST_DWithin(geom::geography, ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography, 1000)', [$lng, $lat])
            ->count();

        if ($poiCount > 0) {
            $poiBonus = min($poiCount * 0.02, 0.10);
            $multiplier += $poiBonus;
            $factors[] = [
                'name' => 'Kedekatan Fasilitas Publik ('.$poiCount.' POI)',
                'impact' => '+'.($poiBonus * 100).'%',
                'positive' => true,
            ];
        }

        $estimatedPrice = $basePrice * $multiplier;

        return response()->json([
            'district_name' => $districtName,
            'avg_price_per_sqm' => (int) $avgPricePerSqm,
            'base_price' => (int) $basePrice,
            'estimated_price' => (int) $estimatedPrice,
            'min_price' => (int) ($estimatedPrice * 0.9),
            'max_price' => (int) ($estimatedPrice * 1.1),
            'factors' => $factors,
        ]);
    }

    private function estimateSqlite(float $lat, float $lng, string $type, int $landArea): JsonResponse
    {
        // Fallback pricing estimation for SQLite
        $districtName = 'Samarinda';
        $avgPricePerSqm = $type === 'Rumah' ? 4500000 : 1200000;
        $basePrice = $avgPricePerSqm * $landArea;

        $factors = [
            ['name' => 'Estimasi Regional (Default)', 'impact' => '0%', 'positive' => true],
        ];

        return response()->json([
            'district_name' => $districtName,
            'avg_price_per_sqm' => $avgPricePerSqm,
            'base_price' => $basePrice,
            'estimated_price' => $basePrice,
            'min_price' => (int) ($basePrice * 0.9),
            'max_price' => (int) ($basePrice * 1.1),
            'factors' => $factors,
        ]);
    }
}
