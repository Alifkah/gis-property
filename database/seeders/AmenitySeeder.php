<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('data/amenities_samarinda.geojson');

        if (! file_exists($filePath)) {
            $this->command->error("File GeoJSON tidak ditemukan di: {$filePath}");

            return;
        }

        $geoJson = json_decode(file_get_contents($filePath), true);
        $features = $geoJson['features'] ?? [];
        $totalRaw = count($features);

        $this->command->info('Memproses '.$totalRaw.' data POI dari GeoJSON...');

        // Kosongkan tabel amenities terlebih dahulu untuk menghapus data lama/dummy
        DB::statement('TRUNCATE TABLE amenities RESTART IDENTITY CASCADE');

        $driver = DB::getDriverName();
        $records = [];

        // Trackers untuk deduplikasi
        $seenCoords = [];
        $seenNamesNear = [];
        $duplicateCount = 0;

        foreach ($features as $feature) {
            // Dapatkan koordinat [longitude, latitude]
            $coords = $feature['geometry']['coordinates'] ?? null;
            if (! $coords || $feature['geometry']['type'] !== 'Point') {
                continue;
            }

            $longitude = (float) $coords[0];
            $latitude = (float) $coords[1];

            // 1. Cek duplikasi koordinat persis (dibulatkan ke 6 angka di belakang koma, ~11 cm)
            $coordKey = round($longitude, 6).','.round($latitude, 6);
            if (isset($seenCoords[$coordKey])) {
                $duplicateCount++;

                continue;
            }

            // Tentukan nama POI
            $properties = $feature['properties'] ?? [];
            $name = $properties['name'] ?? $properties['amenity'] ?? $properties['shop'] ?? 'Fasilitas Umum';

            // Bersihkan nama dari spasi berlebih
            $name = trim(preg_replace('/\s+/', ' ', $name));
            if ($name === '') {
                $name = 'Fasilitas Umum';
            }

            // 2. Cek duplikasi nama yang berada di radius yang sangat dekat (~11 meter)
            // Membulatkan koordinat ke 4 angka di belakang koma untuk pengelompokan area dekat
            $nameNearKey = strtolower($name).'|'.round($longitude, 4).','.round($latitude, 4);
            if (isset($seenNamesNear[$nameNearKey])) {
                $duplicateCount++;

                continue;
            }

            // Tandai sebagai sudah diproses
            $seenCoords[$coordKey] = true;
            $seenNamesNear[$nameNearKey] = true;

            // Tentukan tipe fasilitas berdasarkan tag asli OSM dan sesuaikan dengan kategori aplikasi
            $typeRaw = $properties['amenity']
                ?? $properties['shop']
                ?? $properties['leisure']
                ?? $properties['highway']
                ?? $properties['office']
                ?? $properties['aeroway']
                ?? (isset($properties['harbour']) ? 'harbour' : null)
                ?? 'Lainnya';

            // Batasi panjang karakter nama agar sesuai dengan kolom tabel database (max 150)
            $name = substr($name, 0, 150);

            if ($driver === 'pgsql') {
                $records[] = [
                    'name' => $name,
                    'type' => $this->normalizeType($typeRaw),
                    'geom' => DB::raw("ST_GeomFromText('POINT({$longitude} {$latitude})', 4326)"),
                ];
            } else {
                $records[] = [
                    'name' => $name,
                    'type' => $this->normalizeType($typeRaw),
                    'geom' => "POINT({$longitude} {$latitude})",
                ];
            }
        }

        $totalImport = count($records);
        $this->command->info("Deduplikasi selesai: Menghapus {$duplicateCount} data duplikat.");
        $this->command->info("Memulai impor {$totalImport} data unik...");

        // Jalankan insert bulk dalam chunk berukuran 500 data agar efisien
        $chunks = array_chunk($records, 500);
        foreach ($chunks as $index => $chunk) {
            DB::table('amenities')->insert($chunk);
            $this->command->info('Mengimpor baris '.(($index * 500) + 1).' sampai '.min(($index + 1) * 500, $totalImport).'...');
        }

        $this->command->info('Impor data POI selesai! Berhasil memasukkan '.$totalImport.' data unik ke database.');
    }

    /**
     * Sederhanakan dan sesuaikan tipe kategori OSM ke kategori aslinya di aplikasi:
     * Kesehatan, Pendidikan, Perdagangan, Transportasi, Keamanan, Ibadah, Fasilitas Umum
     */
    private function normalizeType(string $rawType): string
    {
        $rawType = strtolower(trim($rawType));

        // 1. Pendidikan
        if (in_array($rawType, ['school', 'university', 'kindergarten', 'college', 'library'])) {
            return 'Pendidikan';
        }

        // 2. Kesehatan
        if (in_array($rawType, ['hospital', 'clinic', 'doctors', 'pharmacy', 'dentist'])) {
            return 'Kesehatan';
        }

        // 3. Ibadah
        if (in_array($rawType, ['place_of_worship', 'mosque', 'church', 'temple', 'shrine'])) {
            return 'Ibadah';
        }

        // 4. Perdagangan (Toko, Cafe, Restoran, Mall, Swalayan, Pasar, dll.)
        if (in_array($rawType, [
            'supermarket', 'mall', 'marketplace', 'convenience', 'shop',
            'bakery', 'restaurant', 'cafe', 'fast_food', 'food_court',
            'pub', 'bar', 'coffee', 'department_store',
        ])) {
            return 'Perdagangan';
        }

        // 5. Keamanan
        if (in_array($rawType, ['police', 'fire_station'])) {
            return 'Keamanan';
        }

        // 6. Transportasi
        if (in_array($rawType, [
            'fuel', 'bus_station', 'taxi', 'parking', 'bus_stop',
            'aerodrome', 'ferry_terminal', 'harbour',
        ])) {
            return 'Transportasi';
        }

        // 7. Fallback ke Fasilitas Umum (seperti bank, atm, government, townhall, park, stadium, dll.)
        return 'Fasilitas Umum';
    }
}
