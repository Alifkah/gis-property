<?php

namespace Database\Factories;

use App\Models\FloodZone;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends Factory<FloodZone>
 */
class FloodZoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $geom = DB::getDriverName() === 'pgsql'
            ? DB::raw("ST_SetSRID(ST_GeomFromText('POLYGON((117.10 -0.55, 117.20 -0.55, 117.20 -0.47, 117.10 -0.47, 117.10 -0.55))'), 4326)")
            : 'POLYGON((117.10 -0.55, 117.20 -0.55, 117.20 -0.47, 117.10 -0.47, 117.10 -0.55))';

        return [
            'area_name' => $this->faker->randomElement([
                'Bantaran Sungai Mahakam',
                'Dataran Rendah Samarinda',
                'Area Rawan Genangan',
            ]),
            'risk_level' => $this->faker->randomElement(['Rendah', 'Sedang', 'Tinggi']),
            'geom' => $geom,
        ];
    }
}
