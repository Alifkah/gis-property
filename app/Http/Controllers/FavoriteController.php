<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /** Toggle favorite status for a property (AJAX-friendly). */
    public function toggle(Property $property): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();

        $existing = Favorite::where('user_id', $user->id)
            ->where('property_id', $property->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $isFavorited = false;
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'property_id' => $property->id,
            ]);
            $isFavorited = true;
        }

        return response()->json(['favorited' => $isFavorited]);
    }

    /** Show all favorited properties for the authenticated user. */
    public function index(Request $request)
    {
        $properties = Auth::user()
            ->favorites()
            ->with(['property' => function ($q) {
                $q->with('images');
                if (\Illuminate\Support\Facades\DB::getDriverName() === 'pgsql') {
                    $q->select('properties.*')
                        ->addSelect(\Illuminate\Support\Facades\DB::raw('(select name from districts d where ST_Contains(d.geom, properties.geom) limit 1) as district_name'));
                }
            }])
            ->latest('favorites.created_at')
            ->get()
            ->pluck('property')
            ->filter();

        return view('favorites.index', compact('properties'));
    }
}
