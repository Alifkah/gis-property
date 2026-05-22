<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
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
        } else {
            $recentProperties = Property::query()
                ->with('user')
                ->orderByDesc('created_at')
                ->limit(8)
                ->get()
                ->each(fn ($p) => $p->district_name = null);
        }

        return view('admin.dashboard', [
            'totalProperties' => $totalProperties,
            'totalSellers' => $totalSellers,
            'totalAmenities' => $totalAmenities,
            'totalFloodZones' => $totalFloodZones,
            'availableProperties' => $availableProperties,
            'soldProperties' => $soldProperties,
            'recentProperties' => $recentProperties,
        ]);
    }
}
