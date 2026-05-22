<?php

namespace Database\Factories;

use App\Models\Amenity;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends Factory<Amenity>
 */
class AmenityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $longitude = $this->faker->randomFloat(6, 117.05, 117.25);
        $latitude = $this->faker->randomFloat(6, -0.58, -0.42);
        $geom = DB::getDriverName() === 'pgsql'
            ? DB::raw("ST_SetSRID(ST_MakePoint($longitude, $latitude), 4326)")
            : "POINT($longitude $latitude)";

        return [
            'name' => $this->faker->randomElement([
                'Rumah Sakit',
                'Sekolah',
                'Pasar',
                'SPBU',
                'Kantor Polisi',
                'Masjid',
            ]),
            'type' => $this->faker->randomElement(['Kesehatan', 'Pendidikan', 'Perdagangan', 'Transportasi', 'Keamanan', 'Ibadah']),
            'geom' => $geom,
        ];
    }
}
