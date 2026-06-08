<?php

use App\Models\Amenity;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns explore properties json', function () {
    Property::factory()->count(3)->create();

    $response = $this->get(route('api.explore.properties'));
    $response->assertOk();

    $payload = $response->json();

    expect($payload)->toHaveKeys(['data', 'meta']);
    expect($payload['data'])->toBeArray();
    expect($payload['meta'])->toHaveKeys(['page', 'per_page', 'total']);
});

it('returns explore properties geojson', function () {
    Property::factory()->count(2)->create();

    $response = $this->get(route('api.explore.properties.geojson'));
    $response->assertOk();

    $payload = $response->json();
    expect($payload['type'])->toBe('FeatureCollection');
    expect($payload['features'])->toBeArray();
});

it('returns explore amenities list', function () {
    Amenity::factory()->create(['type' => 'Kampus']);
    Amenity::factory()->create(['type' => 'Rumah Sakit']);

    $response = $this->get(route('api.explore.amenities', ['type' => 'Kampus']));
    $response->assertOk();

    expect($response->json('data'))->toBeArray()->toHaveCount(1);
});

it('can filter explore properties by price range', function () {
    Property::factory()->create(['price' => 100_000_000]);
    Property::factory()->create(['price' => 900_000_000]);

    $response = $this->get(route('api.explore.properties', [
        'price_min' => 250_000_000,
        'price_max' => 750_000_000,
    ]));

    $response->assertOk();
    expect($response->json('data'))->toBeArray()->toHaveCount(0);
});

it('can filter explore properties by radius', function () {
    $near = Property::factory()->create([
        'geom' => 'POINT(117.160000 -0.500000)',
    ]);

    $far = Property::factory()->create([
        'geom' => 'POINT(117.260000 -0.500000)',
    ]);

    $response = $this->get(route('api.explore.properties', [
        'center_lat' => -0.5,
        'center_lng' => 117.16,
        'radius_m' => 2000,
    ]));

    $response->assertOk();

    $ids = collect($response->json('data'))->pluck('id')->all();
    $this->assertContains($near->id, $ids);
    $this->assertNotContains($far->id, $ids);
});

it('can filter explore properties by amenity type within radius', function () {
    $near = Property::factory()->create([
        'geom' => 'POINT(117.160000 -0.500000)',
    ]);

    $far = Property::factory()->create([
        'geom' => 'POINT(117.260000 -0.500000)',
    ]);

    Amenity::factory()->create([
        'type' => 'Kampus',
        'geom' => 'POINT(117.161000 -0.500000)',
    ]);

    $response = $this->get(route('api.explore.properties', [
        'amenity_type' => 'Kampus',
        'amenity_radius_m' => 2000,
    ]));

    $response->assertOk();

    $ids = collect($response->json('data'))->pluck('id')->all();
    $this->assertContains($near->id, $ids);
    $this->assertNotContains($far->id, $ids);
});
