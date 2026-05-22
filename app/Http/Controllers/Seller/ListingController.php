<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Property;
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
        $query = Property::query()
            ->where('user_id', auth()->id())
            ->with('images')
            ->orderByDesc('created_at');

        if (DB::getDriverName() === 'pgsql') {
            $query->select('properties.*')
                ->addSelect(DB::raw('(select name from districts d where ST_Contains(d.geom, properties.geom) limit 1) as district_name'));
        }

        $properties = $query->get();

        return view('seller.index', [
            'properties' => $properties,
        ]);
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
            'images' => ['nullable', 'array', 'max:5'],
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
        ]);    }

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
            [$lat, $lng] = $this->extractPoint($property->geom);
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
            'images' => ['nullable', 'array', 'max:5'],
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
            return [self::DEFAULT_LAT, self::DEFAULT_LNG];
        }

        if (preg_match('/POINT\\(([-0-9.]+) ([-0-9.]+)\\)/', $wkt, $matches) !== 1) {
            return [self::DEFAULT_LAT, self::DEFAULT_LNG];
        }

        return [(float) $matches[2], (float) $matches[1]];
    }
}
