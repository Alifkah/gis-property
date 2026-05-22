<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AmenityController extends Controller
{
    public function index(): View
    {
        $isPgsql = DB::getDriverName() === 'pgsql';

        if ($isPgsql) {
            $amenities = Amenity::query()
                ->select('id', 'name', 'type')
                ->addSelect(DB::raw('ST_X(geom::geometry) as lng'))
                ->addSelect(DB::raw('ST_Y(geom::geometry) as lat'))
                ->orderBy('type')
                ->orderBy('name')
                ->get();
        } else {
            $amenities = Amenity::query()
                ->select('id', 'name', 'type', 'geom')
                ->orderBy('type')
                ->orderBy('name')
                ->get()
                ->map(function (Amenity $amenity) {
                    $point = $this->extractPoint($amenity->geom);
                    $amenity->lat = $point['lat'];
                    $amenity->lng = $point['lng'];

                    return $amenity;
                });
        }

        return view('admin.amenities.index', ['amenities' => $amenities]);
    }

    public function create(): View
    {
        return view('admin.amenities.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', 'string', 'max:50'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $isPgsql = DB::getDriverName() === 'pgsql';

        if ($isPgsql) {
            DB::table('amenities')->insert([
                'name' => $data['name'],
                'type' => $data['type'],
                'geom' => DB::raw("ST_SetSRID(ST_MakePoint({$data['lng']}, {$data['lat']}), 4326)"),
            ]);
        } else {
            Amenity::query()->create([
                'name' => $data['name'],
                'type' => $data['type'],
                'geom' => "POINT({$data['lng']} {$data['lat']})",
            ]);
        }

        return redirect()->route('admin.amenities.index')
            ->with('success', "Fasilitas \"{$data['name']}\" berhasil ditambahkan.");
    }

    public function edit(Amenity $amenity): View
    {
        $isPgsql = DB::getDriverName() === 'pgsql';

        if ($isPgsql) {
            $coords = DB::table('amenities')
                ->where('id', $amenity->id)
                ->select(DB::raw('ST_X(geom::geometry) as lng'), DB::raw('ST_Y(geom::geometry) as lat'))
                ->first();
            $amenity->lat = (float) $coords->lat;
            $amenity->lng = (float) $coords->lng;
        } else {
            $point = $this->extractPoint($amenity->geom);
            $amenity->lat = $point['lat'];
            $amenity->lng = $point['lng'];
        }

        return view('admin.amenities.edit', ['amenity' => $amenity]);
    }

    public function update(Request $request, Amenity $amenity): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', 'string', 'max:50'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $isPgsql = DB::getDriverName() === 'pgsql';

        if ($isPgsql) {
            DB::table('amenities')->where('id', $amenity->id)->update([
                'name' => $data['name'],
                'type' => $data['type'],
                'geom' => DB::raw("ST_SetSRID(ST_MakePoint({$data['lng']}, {$data['lat']}), 4326)"),
            ]);
        } else {
            $amenity->update([
                'name' => $data['name'],
                'type' => $data['type'],
                'geom' => "POINT({$data['lng']} {$data['lat']})",
            ]);
        }

        return redirect()->route('admin.amenities.index')
            ->with('success', "Fasilitas \"{$data['name']}\" berhasil diperbarui.");
    }

    public function destroy(Amenity $amenity): RedirectResponse
    {
        $name = $amenity->name;
        $amenity->delete();

        return redirect()->route('admin.amenities.index')
            ->with('success', "Fasilitas \"{$name}\" berhasil dihapus.");
    }

    /**
     * @return array{lat: float, lng: float}
     */
    private function extractPoint(?string $wkt): array
    {
        if (! is_string($wkt)) {
            return ['lat' => 0.0, 'lng' => 0.0];
        }

        if (preg_match('/POINT\(([-0-9.]+) ([-0-9.]+)\)/', $wkt, $matches) !== 1) {
            return ['lat' => 0.0, 'lng' => 0.0];
        }

        return ['lat' => (float) $matches[2], 'lng' => (float) $matches[1]];
    }
}
