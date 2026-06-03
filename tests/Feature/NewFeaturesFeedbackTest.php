<?php

use App\Models\MarketDemand;
use App\Models\Notification;
use App\Models\Property;
use App\Models\PropertyAlert;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

it('records market demand when a property detail page is viewed', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create([
        'user_id' => $owner->id,
        'geom' => 'POINT(117.15 -0.5)',
    ]);

    expect(MarketDemand::count())->toBe(0);

    $this->get(route('properties.show', $property->id))
        ->assertStatus(200);

    expect(MarketDemand::count())->toBe(1);
    $demand = MarketDemand::first();
    expect((float) $demand->latitude)->toBe(-0.5)
        ->and((float) $demand->longitude)->toBe(117.15);
});

it('records market demand when explore api is searched with center coordinates', function () {
    expect(MarketDemand::count())->toBe(0);

    $response = $this->getJson(route('api.explore.properties', [
        'center_lat' => -0.51,
        'center_lng' => 117.16,
    ]));

    $response->assertStatus(200);
    expect(MarketDemand::count())->toBe(1);
    $demand = MarketDemand::first();
    expect((float) $demand->latitude)->toBe(-0.51)
        ->and((float) $demand->longitude)->toBe(117.16);
});

it('allows buyer to create and delete a search criteria property alert', function () {
    $buyer = User::factory()->create();
    $this->actingAs($buyer);

    $response = $this->postJson(route('property-alerts.store'), [
        'type' => 'Rumah',
        'min_price' => 200000000,
        'max_price' => 800000000,
        'district_name' => 'Samarinda Ulu',
    ]);

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    $alert = PropertyAlert::first();
    expect($alert->user_id)->toBe($buyer->id)
        ->and($alert->type)->toBe('Rumah')
        ->and((float) $alert->min_price)->toBe(200000000.0)
        ->and((float) $alert->max_price)->toBe(800000000.0)
        ->and($alert->district_name)->toBe('Samarinda Ulu');

    // Delete alert
    $deleteResponse = $this->deleteJson(route('property-alerts.destroy', $alert->id));
    $deleteResponse->assertStatus(200)
        ->assertJson(['success' => true]);

    expect(PropertyAlert::count())->toBe(0);
});

it('triggers automatic notification to matching buyer when a new listing is created and positioned', function () {
    $buyer = User::factory()->create();
    $seller = User::factory()->create();

    // Buyer creates an alert criteria
    PropertyAlert::create([
        'user_id' => $buyer->id,
        'type' => 'Rumah',
        'min_price' => 100000000,
        'max_price' => 600000000,
        'district_name' => 'Samarinda Ulu', // lat/lng -0.5, 117.15 maps to Samarinda Ulu in tests
    ]);

    $this->actingAs($seller);

    // Seller creates listing (initially default coordinates)
    $property = Property::factory()->create([
        'user_id' => $seller->id,
        'type' => 'Rumah',
        'title' => 'Rumah Impian Murah',
        'price' => 500000000,
        'geom' => 'POINT(117.15 -0.5)',
    ]);

    expect(Notification::count())->toBe(0);

    // Seller sets the exact location on map (triggers match notification check)
    $this->put(route('seller.listings.location.update', $property->id), [
        'lat' => -0.5,
        'lng' => 117.15,
    ])->assertRedirect();

    expect(Notification::count())->toBe(1);
    $notification = Notification::first();
    expect($notification->user_id)->toBe($buyer->id)
        ->and($notification->title)->toContain('Properti Baru yang Cocok')
        ->and($notification->is_read)->toBeFalse();
});

it('triggers automatic notification to matching buyer when listing is imported via CSV', function () {
    $buyer = User::factory()->create();
    $seller = User::factory()->create();

    PropertyAlert::create([
        'user_id' => $buyer->id,
        'type' => 'Rumah',
        'min_price' => 400000000,
        'max_price' => 900000000,
        'district_name' => 'Samarinda Ulu',
    ]);

    $this->actingAs($seller);

    $csvContent = "judul,tipe,deskripsi,harga,luas_tanah,luas_bangunan,kamar_tidur,kamar_mandi,latitude,longitude\n".
        "Rumah Baru Keren Ulu,Rumah,Dekat Mall Mulia,650000000,100,80,3,2,-0.5,117.15\n"; // Samarinda Ulu

    $file = UploadedFile::fake()->createWithContent('impor.csv', $csvContent);

    expect(Notification::count())->toBe(0);

    $response = $this->post(route('seller.listings.import.store'), [
        'csv_file' => $file,
    ]);

    $response->assertRedirect(route('seller.listings.index'));

    expect(Notification::count())->toBe(1);
    $notification = Notification::first();
    expect($notification->user_id)->toBe($buyer->id)
        ->and($notification->title)->toContain('Properti Baru yang Cocok');
});

it('allows user to mark a notification as read and redirect to target URL', function () {
    $user = User::factory()->create();
    $notification = Notification::create([
        'user_id' => $user->id,
        'title' => 'Test Notification',
        'message' => 'Hello World',
        'url' => '/explore',
        'is_read' => false,
    ]);

    $this->actingAs($user);

    $response = $this->post(route('notifications.read', $notification->id));
    $response->assertRedirect('/explore');

    $notification->refresh();
    expect($notification->is_read)->toBeTrue();
});

it('allows seller to load the market demand heatmap and view coordinate points', function () {
    $seller = User::factory()->create();
    $this->actingAs($seller);

    MarketDemand::create(['latitude' => -0.5, 'longitude' => 117.15]);
    MarketDemand::create(['latitude' => -0.51, 'longitude' => 117.16]);

    // Test Heatmap HTML View
    $this->get(route('seller.market-demands.heatmap'))
        ->assertStatus(200)
        ->assertSee('Peta Panas Permintaan Pasar');

    // Test Heatmap JSON endpoint
    $response = $this->getJson(route('api.seller.market-demands'));
    $response->assertStatus(200)
        ->assertJsonCount(2)
        ->assertJsonFragment(['lat' => -0.5, 'lng' => 117.15])
        ->assertJsonFragment(['lat' => -0.51, 'lng' => 117.16]);
});
