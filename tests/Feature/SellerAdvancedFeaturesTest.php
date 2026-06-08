<?php

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('increments views_count when a property detail page is viewed', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create([
        'user_id' => $owner->id,
        'views_count' => 0,
    ]);

    $this->get(route('properties.show', $property->id))
        ->assertStatus(200);

    $property->refresh();
    expect($property->views_count)->toBe(1);
});

it('increments whatsapp_clicks_count when user clicks contact whatsapp', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create([
        'user_id' => $owner->id,
        'whatsapp_clicks_count' => 0,
    ]);

    $this->post(route('properties.whatsapp-click', $property->id))
        ->assertStatus(200)
        ->assertJson(['success' => true]);

    $property->refresh();
    expect($property->whatsapp_clicks_count)->toBe(1);
});

it('displays analytics stats on the seller dashboard index', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Property::factory()->create([
        'user_id' => $user->id,
        'views_count' => 10,
        'whatsapp_clicks_count' => 5,
    ]);

    $this->get(route('seller.listings.index'))
        ->assertStatus(200)
        ->assertSee('10') // Total Views
        ->assertSee('5'); // Total Whatsapp Clicks
});

it('can calculate smart pricing estimation', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson(route('seller.estimate-price'), [
        'lat' => -0.5,
        'lng' => 117.15,
        'type' => 'Rumah',
        'land_area' => 100,
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'district_name',
            'avg_price_per_sqm',
            'base_price',
            'estimated_price',
            'min_price',
            'max_price',
            'factors',
        ]);
});

it('can update seller branded public profile details and logo', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $this->actingAs($user);

    $logo = UploadedFile::fake()->create('agency_logo.png', 100, 'image/png');

    $response = $this->put(route('seller.profile.update'), [
        'name' => 'John Doe Updated',
        'phone' => '08123456789',
        'email' => 'updated@agency.com',
        'company_name' => 'Super Property Developer',
        'description' => 'We build the best modern homes in East Kalimantan.',
        'logo' => $logo,
    ]);

    $response->dump();
    $response->assertRedirect(route('seller.profile.edit'));

    $user->refresh();
    $this->assertEquals('John Doe Updated', $user->name);
    $this->assertEquals('Super Property Developer', $user->company_name);
    $this->assertEquals('We build the best modern homes in East Kalimantan.', $user->description);
    $this->assertNotNull($user->logo_path);

    $this->assertTrue(Storage::disk('public')->exists($user->logo_path));
});

it('displays the branded public profile page', function () {
    $seller = User::factory()->create([
        'company_name' => 'Kaltim Estate',
        'description' => 'The leading property agency in Samarinda.',
    ]);

    Property::factory()->create([
        'user_id' => $seller->id,
        'type' => 'Rumah',
        'title' => 'Rumah Mewah di Tepian',
        'status' => 'Tersedia',
    ]);

    $this->get(route('sellers.show', $seller->id))
        ->assertStatus(200)
        ->assertSee('Kaltim Estate')
        ->assertSee('The leading property agency in Samarinda.')
        ->assertSee('Rumah Mewah di Tepian');
});

it('can download the bulk property import template', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('seller.listings.import.template'));

    $response->assertStatus(200)
        ->assertHeader('Content-Disposition', 'attachment; filename="template_impor_properti.csv"')
        ->assertSee('judul,tipe,deskripsi,harga,luas_tanah,luas_bangunan,kamar_tidur,kamar_mandi,latitude,longitude');
});

it('can successfully bulk import properties via CSV', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $csvContent = "judul,tipe,deskripsi,harga,luas_tanah,luas_bangunan,kamar_tidur,kamar_mandi,latitude,longitude\n".
        "Rumah Baru Keren,Rumah,Dekat Mall Mulia,650000000,100,80,3,2,-0.49523,117.15120\n".
        'Tanah Kebun Murah,Tanah,Kondisi subur rata,120000000,300,0,0,0,-0.51012,117.19853';

    $file = UploadedFile::fake()->createWithContent('impor.csv', $csvContent);

    $response = $this->post(route('seller.listings.import.store'), [
        'csv_file' => $file,
    ]);

    $response->assertRedirect(route('seller.listings.index'));

    $this->assertDatabaseHas('properties', [
        'user_id' => $user->id,
        'title' => 'Rumah Baru Keren',
        'type' => 'Rumah',
        'price' => 650000000,
    ]);

    $this->assertDatabaseHas('properties', [
        'user_id' => $user->id,
        'title' => 'Tanah Kebun Murah',
        'type' => 'Tanah',
        'price' => 120000000,
    ]);
});

it('rejects bulk import CSV with validation errors', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Row 2 is invalid: price is missing/empty, type is invalid, and lat/lng are missing.
    $csvContent = "judul,tipe,deskripsi,harga,luas_tanah,luas_bangunan,kamar_tidur,kamar_mandi,latitude,longitude\n".
        "Rumah Salah,Ruko,Deskripsi,abc,-10,80,3,2,,\n".
        'Rumah Benar,Rumah,Deskripsi,750000000,120,90,3,2,-0.501,117.152';

    $file = UploadedFile::fake()->createWithContent('impor_salah.csv', $csvContent);

    $response = $this->post(route('seller.listings.import.store'), [
        'csv_file' => $file,
    ]);

    $response->assertSessionHasErrors(['csv_errors']);
    expect(Property::count())->toBe(0); // Nothing should be imported because of rollback
});
