<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FloodZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FloodZoneController extends Controller
{
    public function index(): View
    {
        $floodZones = FloodZone::query()
            ->select('id', 'area_name', 'risk_level')
            ->orderBy('risk_level')
            ->orderBy('area_name')
            ->get();

        return view('admin.flood-zones.index', ['floodZones' => $floodZones]);
    }

    public function create(): View
    {
        return view('admin.flood-zones.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'area_name' => ['required', 'string', 'max:100'],
            'risk_level' => ['required', 'string', 'in:Rendah,Sedang,Tinggi'],
            'geojson' => ['required', 'string'],
        ]);

        $isPgsql = DB::getDriverName() === 'pgsql';

        $geojsonDecoded = json_decode($data['geojson'], true);

        if (! $geojsonDecoded || ! isset($geojsonDecoded['coordinates'])) {
            return back()->withInput()->withErrors(['geojson' => 'Data polygon tidak valid. Pastikan Anda sudah menggambar polygon di peta.']);
        }

        if ($isPgsql) {
            DB::statement(
                'INSERT INTO flood_zones (area_name, risk_level, geom) VALUES (?, ?, ST_SetSRID(ST_GeomFromGeoJSON(?), 4326))',
                [$data['area_name'], $data['risk_level'], $data['geojson']],
            );
        } else {
            DB::table('flood_zones')->insert([
                'area_name' => $data['area_name'],
                'risk_level' => $data['risk_level'],
                'geom' => $this->geojsonToWkt($geojsonDecoded),
            ]);
        }

        \Illuminate\Support\Facades\Cache::forget('explore_flood_zone_features');

        return redirect()->route('admin.flood-zones.index')
            ->with('success', "Zona banjir \"{$data['area_name']}\" berhasil ditambahkan.");
    }

    public function edit(FloodZone $floodZone): View
    {
        $isPgsql = DB::getDriverName() === 'pgsql';

        $existingGeojson = null;

        if ($isPgsql) {
            $row = DB::table('flood_zones')
                ->where('id', $floodZone->id)
                ->select(DB::raw('ST_AsGeoJSON(geom) as geojson'))
                ->first();
            $existingGeojson = $row?->geojson;
        }

        return view('admin.flood-zones.edit', [
            'floodZone' => $floodZone,
            'existingGeojson' => $existingGeojson,
        ]);
    }

    public function update(Request $request, FloodZone $floodZone): RedirectResponse
    {
        $data = $request->validate([
            'area_name' => ['required', 'string', 'max:100'],
            'risk_level' => ['required', 'string', 'in:Rendah,Sedang,Tinggi'],
            'geojson' => ['required', 'string'],
        ]);

        $isPgsql = DB::getDriverName() === 'pgsql';

        $geojsonDecoded = json_decode($data['geojson'], true);

        if (! $geojsonDecoded || ! isset($geojsonDecoded['coordinates'])) {
            return back()->withInput()->withErrors(['geojson' => 'Data polygon tidak valid. Pastikan Anda sudah menggambar polygon di peta.']);
        }

        if ($isPgsql) {
            DB::statement(
                'UPDATE flood_zones SET area_name = ?, risk_level = ?, geom = ST_SetSRID(ST_GeomFromGeoJSON(?), 4326) WHERE id = ?',
                [$data['area_name'], $data['risk_level'], $data['geojson'], $floodZone->id],
            );
        } else {
            DB::table('flood_zones')->where('id', $floodZone->id)->update([
                'area_name' => $data['area_name'],
                'risk_level' => $data['risk_level'],
                'geom' => $this->geojsonToWkt($geojsonDecoded),
            ]);
        }

        \Illuminate\Support\Facades\Cache::forget('explore_flood_zone_features');

        return redirect()->route('admin.flood-zones.index')
            ->with('success', "Zona banjir \"{$data['area_name']}\" berhasil diperbarui.");
    }

    public function destroy(FloodZone $floodZone): RedirectResponse
    {
        $name = $floodZone->area_name;
        $floodZone->delete();

        \Illuminate\Support\Facades\Cache::forget('explore_flood_zone_features');

        return redirect()->route('admin.flood-zones.index')
            ->with('success', "Zona banjir \"{$name}\" berhasil dihapus.");
    }

    /**
     * Convert a GeoJSON Polygon geometry to WKT (for SQLite fallback).
     *
     * @param  array<string, mixed>  $geojson
     */
    private function geojsonToWkt(array $geojson): string
    {
        $rings = $geojson['coordinates'] ?? [];

        if (empty($rings)) {
            return 'POLYGON(())';
        }

        $ringStrings = array_map(function (array $ring): string {
            $pairs = array_map(fn (array $coord) => "{$coord[0]} {$coord[1]}", $ring);

            return '(' . implode(', ', $pairs) . ')';
        }, $rings);

        return 'POLYGON(' . implode(', ', $ringStrings) . ')';
    }
}
