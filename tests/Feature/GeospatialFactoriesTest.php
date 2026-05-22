<?php

use App\Models\Amenity;
use App\Models\District;
use App\Models\FloodZone;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('creates samarinda geometries with srid 4326', function () {
    $user = User::factory()->create();

    $propertyId = Property::factory()->for($user)->create()->getKey();
    $amenityId = Amenity::factory()->create()->getKey();
    $districtId = District::factory()->create()->getKey();
    $floodZoneId = FloodZone::factory()->create()->getKey();

    if (DB::getDriverName() === 'pgsql') {
        $propertySrid = DB::table('properties')
            ->where('id', $propertyId)
            ->value(DB::raw('ST_SRID(geom)'));

        $amenitySrid = DB::table('amenities')
            ->where('id', $amenityId)
            ->value(DB::raw('ST_SRID(geom)'));

        $districtSrid = DB::table('districts')
            ->where('id', $districtId)
            ->value(DB::raw('ST_SRID(geom)'));

        $floodZoneSrid = DB::table('flood_zones')
            ->where('id', $floodZoneId)
            ->value(DB::raw('ST_SRID(geom)'));

        expect($propertySrid)->toBe(4326)
            ->and($amenitySrid)->toBe(4326)
            ->and($districtSrid)->toBe(4326)
            ->and($floodZoneSrid)->toBe(4326);
    } else {
        $propertyGeom = DB::table('properties')->where('id', $propertyId)->value('geom');
        $amenityGeom = DB::table('amenities')->where('id', $amenityId)->value('geom');
        $districtGeom = DB::table('districts')->where('id', $districtId)->value('geom');
        $floodZoneGeom = DB::table('flood_zones')->where('id', $floodZoneId)->value('geom');

        expect($propertyGeom)->toStartWith('POINT(')
            ->and($amenityGeom)->toStartWith('POINT(')
            ->and($districtGeom)->toStartWith('MULTIPOLYGON(')
            ->and($floodZoneGeom)->toStartWith('POLYGON(');
    }
});
