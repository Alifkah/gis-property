<?php

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('requires authentication for seller listings', function () {
    $this->get(route('seller.listings.index'))->assertRedirect(route('login'));
});

it('can create a listing and set location', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $createResponse = $this->post(route('seller.listings.store'), [
        'title' => 'Rumah Minimalis',
        'type' => 'Rumah',
        'price' => 500000000,
        'land_area' => 120,
        'building_area' => 90,
        'bedroom' => 3,
        'bathroom' => 2,
    ]);

    $property = Property::query()->firstOrFail();
    $createResponse->assertRedirect(route('seller.listings.location.edit', ['property' => $property->id]));

    $updateResponse = $this->put(route('seller.listings.location.update', ['property' => $property->id]), [
        'lat' => -0.5,
        'lng' => 117.15,
    ]);

    $updateResponse->assertRedirect(route('properties.show', ['property' => $property->id]));

    if (DB::getDriverName() === 'pgsql') {
        $srid = DB::table('properties')
            ->where('id', $property->id)
            ->selectRaw('ST_SRID(geom) as srid')
            ->value('srid');

        expect((int) $srid)->toBe(4326);
    } else {
        $property->refresh();
        expect($property->geom)->toStartWith('POINT(');
    }
});

it('prevents other users from deleting a listing', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    $property = Property::factory()->create([
        'user_id' => $owner->id,
    ]);

    $this->actingAs($other);
    $this->delete(route('seller.listings.destroy', ['property' => $property->id]))->assertForbidden();
});
