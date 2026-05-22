<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE INDEX IF NOT EXISTS properties_geom_gist_idx ON properties USING gist(geom)');
            DB::statement('CREATE INDEX IF NOT EXISTS districts_geom_gist_idx ON districts USING gist(geom)');
            DB::statement('CREATE INDEX IF NOT EXISTS amenities_geom_gist_idx ON amenities USING gist(geom)');
            DB::statement('CREATE INDEX IF NOT EXISTS flood_zones_geom_gist_idx ON flood_zones USING gist(geom)');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS properties_geom_gist_idx');
            DB::statement('DROP INDEX IF EXISTS districts_geom_gist_idx');
            DB::statement('DROP INDEX IF EXISTS amenities_geom_gist_idx');
            DB::statement('DROP INDEX IF EXISTS flood_zones_geom_gist_idx');
        }
    }
};
