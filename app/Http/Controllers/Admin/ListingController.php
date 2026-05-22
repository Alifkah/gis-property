<?php

namespace App\Http\Controllers\Admin;

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
    public function index(Request $request): View
    {
        $query = Property::query()
            ->with(['user', 'images'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type')->toString());
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where('title', 'like', "%{$search}%");
        }

        $properties = $query->paginate(15)->withQueryString();

        return view('admin.listings.index', ['properties' => $properties]);
    }

    public function destroy(Request $request, Property $property): RedirectResponse
    {
        $title = $property->title;

        foreach ($property->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $property->delete();

        return redirect()->route('admin.listings.index', $request->only(['status', 'type', 'search', 'page']))
            ->with('success', "Listing \"{$title}\" berhasil dihapus.");
    }
}
