<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('seller.profile', [
            'user' => auth()->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^(\+62|62|0)[0-9]{8,13}$/'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id)],
            'company_name' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'company_name' => $data['company_name'] ?? null,
            'description' => $data['description'] ?? null,
        ];

        if ($request->hasFile('logo')) {
            if ($user->logo_path) {
                Storage::disk('public')->delete($user->logo_path);
            }
            $updateData['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        $user->update($updateData);

        return redirect()->route('seller.profile.edit')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::min(8), 'confirmed'],
        ]);

        /** @var User $user */
        $user = auth()->user();

        $user->update([
            'password' => Hash::make($request->string('password')->toString()),
        ]);

        return redirect()->route('seller.profile.edit')
            ->with('success', 'Kata sandi berhasil diperbarui.');
    }

    public function showPublic(User $user): View
    {
        $isPgsql = DB::getDriverName() === 'pgsql';

        $query = Property::query()
            ->where('user_id', $user->id)
            ->where('status', 'Tersedia')
            ->with('images')
            ->orderByDesc('created_at');

        if ($isPgsql) {
            $query->select('properties.*')
                ->addSelect(DB::raw('ST_X(geom::geometry) as lng'))
                ->addSelect(DB::raw('ST_Y(geom::geometry) as lat'))
                ->addSelect(DB::raw('(select name from districts d where ST_Contains(d.geom, properties.geom) limit 1) as district_name'));
        } else {
            $query->select('properties.*');
        }

        $properties = $query->get();

        if (! $isPgsql) {
            $properties->each(function ($p) {
                if (preg_match('/POINT\\(([-0-9.]+) ([-0-9.]+)\\)/', $p->geom ?? '', $matches) === 1) {
                    $p->lat = (float) $matches[2];
                    $p->lng = (float) $matches[1];
                } else {
                    $p->lat = 0.0;
                    $p->lng = 0.0;
                }
                $p->district_name = 'Samarinda';
            });
        }

        $geojson = [
            'type' => 'FeatureCollection',
            'features' => $properties->map(function ($p) {
                $firstImage = $p->images->first();
                $imageUrl = $firstImage ? Storage::disk('public')->url($firstImage->path) : null;

                return [
                    'type' => 'Feature',
                    'properties' => [
                        'id' => $p->id,
                        'slug' => $p->slug,
                        'title' => $p->title,
                        'price' => (float) $p->price,
                        'type' => $p->type,
                        'district' => $p->district_name ?? 'Samarinda',
                        'image_url' => $imageUrl,
                    ],
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [$p->lng, $p->lat],
                    ],
                ];
            })->values()->all(),
        ];

        return view('sellers.show', [
            'seller' => $user,
            'properties' => $properties,
            'geojson' => $geojson,
        ]);
    }
}
