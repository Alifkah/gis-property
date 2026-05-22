<?php

namespace Database\Factories;

use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends Factory<District>
 */
class DistrictFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $geom = DB::getDriverName() === 'pgsql'
            ? DB::raw("ST_SetSRID(ST_GeomFromText('MULTIPOLYGON(((117.05 -0.58, 117.25 -0.58, 117.25 -0.42, 117.05 -0.42, 117.05 -0.58)))'), 4326)")
            : 'MULTIPOLYGON(((117.05 -0.58, 117.25 -0.58, 117.25 -0.42, 117.05 -0.42, 117.05 -0.58)))';

        return [
            'name' => $this->faker->randomElement([
                'Samarinda Ulu',
                'Samarinda Ilir',
                'Samarinda Seberang',
                'Samarinda Utara',
                'Sungai Kunjang',
                'Loa Janan Ilir',
            ]),
            'geom' => $geom,
        ];
    }
}
