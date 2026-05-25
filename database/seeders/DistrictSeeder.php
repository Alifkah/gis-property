<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('data/districts_samarinda.geojson');

        if (! File::exists($filePath)) {
            $this->command->warn("File GeoJSON kecamatan asli tidak ditemukan di: {$filePath}");
            $this->command->info('Menggunakan fallback grid kecamatan default...');

            // Fallback grid default agar seeder tidak error
            $this->seedDefaultGrid();

            return;
        }

        $this->command->info('Membaca file GeoJSON kecamatan asli..');
        $geoJsonData = json_decode(File::get($filePath), true);

        if (! $geoJsonData || ! isset($geoJsonData['features'])) {
            $this->command->error('Format file GeoJSON tidak valid.');

            return;
        }

        $driver = DB::getDriverName();
        if ($driver !== 'pgsql') {
            $this->command->error('Seeder GeoJSON kecamatan asli hanya didukung pada database PostgreSQL.');

            return;
        }

        DB::table('districts')->truncate();
        $count = 0;

        foreach ($geoJsonData['features'] as $feature) {
            $properties = $feature['properties'] ?? [];

            // Cari nama kecamatan dari tag OSM yang umum
            $name = $properties['name']
                ?? $properties['local_name']
                ?? $properties['official_name']
                ?? null;

            if (! $name) {
                continue;
            }

            // Bersihkan nama (misal: "Kecamatan Samarinda Ulu" -> "Samarinda Ulu")
            $name = trim(preg_replace('/^Kecamatan\s+/i', '', $name));

            $geometryJson = json_encode($feature['geometry']);

            // Simpan ke database dengan casting ke MultiPolygon menggunakan ST_Multi
            District::create([
                'name' => $name,
                'geom' => DB::raw("ST_SetSRID(ST_Multi(ST_GeomFromGeoJSON('{$geometryJson}')), 4326)"),
            ]);

            $count++;
        }

        $this->command->info("Berhasil mengimpor {$count} kecamatan asli Samarinda dari GeoJSON!");
        Cache::forget('explore_district_features');
    }

    private function seedDefaultGrid(): void
    {
        DB::table('districts')->truncate();

        $districtsData = [
            ['name' => 'Loa Janan Ilir', 'wkt' => 'MULTIPOLYGON(((117.05 -0.58, 117.15 -0.58, 117.15 -0.548, 117.05 -0.548, 117.05 -0.58)))'],
            ['name' => 'Palaran', 'wkt' => 'MULTIPOLYGON(((117.15 -0.58, 117.25 -0.58, 117.25 -0.548, 117.15 -0.548, 117.15 -0.58)))'],
            ['name' => 'Samarinda Seberang', 'wkt' => 'MULTIPOLYGON(((117.05 -0.548, 117.15 -0.548, 117.15 -0.516, 117.05 -0.516, 117.05 -0.548)))'],
            ['name' => 'Sambutan', 'wkt' => 'MULTIPOLYGON(((117.15 -0.548, 117.25 -0.548, 117.25 -0.516, 117.15 -0.516, 117.15 -0.548)))'],
            ['name' => 'Sungai Kunjang', 'wkt' => 'MULTIPOLYGON(((117.05 -0.516, 117.15 -0.516, 117.15 -0.484, 117.05 -0.484, 117.05 -0.516)))'],
            ['name' => 'Samarinda Ulu', 'wkt' => 'MULTIPOLYGON(((117.15 -0.516, 117.25 -0.516, 117.25 -0.484, 117.15 -0.484, 117.15 -0.516)))'],
            ['name' => 'Samarinda Ilir', 'wkt' => 'MULTIPOLYGON(((117.05 -0.484, 117.15 -0.484, 117.15 -0.452, 117.05 -0.452, 117.05 -0.484)))'],
            ['name' => 'Samarinda Kota', 'wkt' => 'MULTIPOLYGON(((117.15 -0.484, 117.25 -0.484, 117.25 -0.452, 117.15 -0.452, 117.15 -0.484)))'],
            ['name' => 'Samarinda Utara', 'wkt' => 'MULTIPOLYGON(((117.05 -0.452, 117.15 -0.452, 117.15 -0.420, 117.05 -0.420, 117.05 -0.452)))'],
            ['name' => 'Sungai Pinang', 'wkt' => 'MULTIPOLYGON(((117.15 -0.452, 117.25 -0.452, 117.25 -0.420, 117.15 -0.420, 117.15 -0.452)))'],
        ];

        $driver = DB::getDriverName();
        foreach ($districtsData as $d) {
            District::create([
                'name' => $d['name'],
                'geom' => $driver === 'pgsql'
                    ? DB::raw("ST_SetSRID(ST_GeomFromText('{$d['wkt']}'), 4326)")
                    : $d['wkt'],
            ]);
        }
        Cache::forget('explore_district_features');
    }
}
