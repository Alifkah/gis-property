<?php

use App\Http\Controllers\Admin\AmenityController as AdminAmenityController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FloodZoneController as AdminFloodZoneController;
use App\Http\Controllers\Admin\ListingController as AdminListingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Buyer\AlertController;
use App\Http\Controllers\ExploreApiController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\Seller\CompetitorAnalysisController;
use App\Http\Controllers\Seller\EstimationController;
use App\Http\Controllers\Seller\ImportController;
use App\Http\Controllers\Seller\ListingController;
use App\Http\Controllers\Seller\MarketDemandController;
use App\Http\Controllers\Seller\ProfileController;
use App\Models\Amenity;
use App\Models\District;
use App\Models\FloodZone;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $isPgsql = DB::getDriverName() === 'pgsql';
    $defaultLng = 117.15;
    $defaultLat = -0.5;

    $types = Property::query()
        ->select('type')
        ->distinct()
        ->orderBy('type')
        ->pluck('type');

    $districts = District::query()
        ->orderBy('name')
        ->get(['name']);

    if ($isPgsql) {
        $recentProperties = Property::query()
            ->select('properties.*')
            ->addSelect(DB::raw('ST_X(geom::geometry) as lng'))
            ->addSelect(DB::raw('ST_Y(geom::geometry) as lat'))
            ->addSelect(DB::raw("(properties.created_at >= NOW() - interval '14 days') as is_new"))
            ->addSelect(DB::raw('NOT EXISTS (select 1 from flood_zones f where ST_Intersects(f.geom, properties.geom)) as is_flood_safe'))
            ->addSelect(DB::raw('(select name from districts d where ST_Contains(d.geom, properties.geom) limit 1) as district_name'))
            ->whereRaw('NOT ST_Equals(properties.geom, ST_SetSRID(ST_MakePoint(?, ?), 4326))', [$defaultLng, $defaultLat])
            ->orderByDesc('properties.created_at')
            ->limit(8)
            ->get();
    } else {
        $recentProperties = Property::query()
            ->where('geom', '!=', 'POINT('.$defaultLng.' '.$defaultLat.')')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $recentProperties->each(function ($property) {
            $property->setAttribute('is_new', false);
            $property->setAttribute('is_flood_safe', true);
            $property->setAttribute('district_name', null);
        });
    }

    $recentProperties->load('images');

    return view('home', [
        'types' => $types,
        'districts' => $districts,
        'recentProperties' => $recentProperties,
    ]);
})->name('home');

Route::get('/explore', function (Request $request) {
    $isPgsql = DB::getDriverName() === 'pgsql';

    $types = Property::query()
        ->select('type')
        ->distinct()
        ->orderBy('type')
        ->pluck('type');

    $amenityTypes = Amenity::query()
        ->select('type')
        ->distinct()
        ->orderBy('type')
        ->pluck('type');

    if ($isPgsql) {
        $districtFeatures = Cache::remember('explore_district_features', 3600, function () {
            return District::query()
                ->select('id', 'name')
                ->addSelect(DB::raw('ST_AsGeoJSON(geom) as geojson'))
                ->get()
                ->map(function ($district) {
                    return [
                        'type' => 'Feature',
                        'properties' => [
                            'id' => (int) $district->id,
                            'name' => $district->name,
                        ],
                        'geometry' => json_decode($district->geojson, true),
                    ];
                })
                ->values()
                ->all();
        });

        $floodZoneFeatures = Cache::remember('explore_flood_zone_features', 3600, function () {
            return FloodZone::query()
                ->select('id', 'area_name', 'risk_level')
                ->addSelect(DB::raw('ST_AsGeoJSON(geom) as geojson'))
                ->get()
                ->map(function ($zone) {
                    return [
                        'type' => 'Feature',
                        'properties' => [
                            'id' => (int) $zone->id,
                            'area_name' => $zone->area_name,
                            'risk_level' => $zone->risk_level,
                        ],
                        'geometry' => json_decode($zone->geojson, true),
                    ];
                })
                ->values()
                ->all();
        });
    } else {
        $districtFeatures = collect();
        $floodZoneFeatures = collect();
    }

    return view('explore', [
        'types' => $types,
        'amenityTypes' => $amenityTypes,
        'districtFeatures' => [
            'type' => 'FeatureCollection',
            'features' => $districtFeatures,
        ],
        'floodZoneFeatures' => [
            'type' => 'FeatureCollection',
            'features' => $floodZoneFeatures,
        ],
    ]);
})->name('explore');

Route::prefix('api/explore')->group(function () {
    Route::get('/amenities', [ExploreApiController::class, 'amenities'])->name('api.explore.amenities');
    Route::get('/properties', [ExploreApiController::class, 'properties'])->name('api.explore.properties');
    Route::get('/properties.geojson', [ExploreApiController::class, 'propertiesGeojson'])->name('api.explore.properties.geojson');
});

Route::get('/properties', [PropertyController::class, 'browse'])->name('properties.index');
Route::get('/properties/geojson', [PropertyController::class, 'index'])->name('properties.geojson');
Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
Route::post('/properties/{property}/whatsapp-click', [PropertyController::class, 'trackWhatsappClick'])->name('properties.whatsapp-click');
Route::get('/sellers/{user}', [ProfileController::class, 'showPublic'])->name('sellers.show');

Route::middleware('auth')->group(function () {
    Route::post('/favorites/{property}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

    // Property Alerts & Notifications
    Route::post('/property-alerts', [AlertController::class, 'store'])->name('property-alerts.store');
    Route::delete('/property-alerts/{alert}', [AlertController::class, 'destroy'])->name('property-alerts.destroy');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Market Demand JSON API
    Route::get('/api/seller/market-demands', [MarketDemandController::class, 'index'])->name('api.seller.market-demands');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->prefix('seller')->name('seller.')->group(function () {
    Route::get('/listings/export', [ListingController::class, 'exportCsv'])->name('listings.export');
    Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
    Route::get('/listings/create', [ListingController::class, 'create'])->name('listings.create');
    Route::post('/listings', [ListingController::class, 'store'])->name('listings.store');
    Route::get('/listings/{property}/location', [ListingController::class, 'editLocation'])->name('listings.location.edit');
    Route::put('/listings/{property}/location', [ListingController::class, 'updateLocation'])->name('listings.location.update');
    Route::get('/listings/{property}/edit', [ListingController::class, 'edit'])->name('listings.edit');
    Route::put('/listings/{property}', [ListingController::class, 'update'])->name('listings.update');
    Route::delete('/listings/{property}', [ListingController::class, 'destroy'])->name('listings.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/listings/import', [ImportController::class, 'show'])->name('listings.import.show');
    Route::post('/listings/import', [ImportController::class, 'store'])->name('listings.import.store');
    Route::get('/listings/import/template', [ImportController::class, 'downloadTemplate'])->name('listings.import.template');

    Route::post('/estimate-price', [EstimationController::class, 'estimate'])->name('estimate-price');

    Route::get('/competitor-analysis/export/{property}', [CompetitorAnalysisController::class, 'exportCsv'])->name('competitor-analysis.export');
    Route::get('/competitor-analysis', [CompetitorAnalysisController::class, 'index'])->name('competitor-analysis.index');
    Route::get('/competitor-analysis/{property}', [CompetitorAnalysisController::class, 'analyze'])->name('competitor-analysis.analyze');

    // Market Demand Heatmap Page
    Route::get('/market-demands/heatmap', [MarketDemandController::class, 'heatmap'])->name('market-demands.heatmap');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/listings/export', [AdminDashboardController::class, 'exportCsv'])->name('listings.export');
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('amenities', AdminAmenityController::class);
    Route::resource('flood-zones', AdminFloodZoneController::class);

    Route::get('/listings', [AdminListingController::class, 'index'])->name('listings.index');
    Route::delete('/listings/{property}', [AdminListingController::class, 'destroy'])->name('listings.destroy');
});

Route::get('/sitemap.xml', function () {
    $properties = Property::where('status', 'Tersedia')->orderBy('updated_at', 'desc')->get();
    $sellers = User::whereHas('properties')->get();

    $content = view('sitemap', [
        'properties' => $properties,
        'sellers' => $sellers,
    ])->render();

    return response($content, 200)
        ->header('Content-Type', 'text/xml');
})->name('sitemap');

Route::get('/test-email', function (Request $request) {
    $to = $request->query('to');
    if (! $to) {
        return 'Silakan masukkan parameter email tujuan, contoh: /test-email?to=emailkamu@gmail.com';
    }
    try {
        Mail::raw('Halo! Ini adalah email uji coba untuk memastikan integrasi Brevo SMTP online Anda berfungsi dengan sukses.', function ($message) use ($to) {
            $message->to($to)
                ->subject('Uji Coba Brevo SMTP Online');
        });

        return 'Email berhasil dikirim ke: '.e($to).'! Silakan periksa inbox atau folder spam email Anda.';
    } catch (Exception $e) {
        return 'Gagal mengirim email. Error: '.$e->getMessage();
    }
});
