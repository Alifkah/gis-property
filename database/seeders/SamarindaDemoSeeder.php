<?php

namespace Database\Seeders;

use App\Models\FloodZone;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Seeder;

class SamarindaDemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Samarinda Demo User',
            'email' => 'demo@samarinda.test',
        ]);

        // Seeding 10 kecamatan asli Kota Samarinda dengan boundary polygon terpisah
        $this->call(DistrictSeeder::class);

        Property::factory()->count(25)->for($user)->create();
        FloodZone::factory()->count(3)->create();
    }
}
