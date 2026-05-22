<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
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
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['Rumah', 'Tanah', 'Ruko', 'Apartemen']),
            'title' => $this->faker->sentence(3),
            'price' => $this->faker->numberBetween(50_000_000, 2_000_000_000),
            'land_area' => $this->faker->numberBetween(60, 800),
            'building_area' => $this->faker->numberBetween(0, 600),
            'bedroom' => $this->faker->numberBetween(0, 6),
            'bathroom' => $this->faker->numberBetween(0, 4),
            'status' => $this->faker->randomElement(['Tersedia', 'Terjual']),
            'geom' => $geom,
        ];
    }
}
