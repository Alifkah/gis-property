<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');
        }

        Schema::create('flood_zones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('area_name', 100);
            $table->string('risk_level', 20);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE flood_zones ADD COLUMN geom GEOMETRY(Polygon, 4326) NOT NULL');
        } else {
            Schema::table('flood_zones', function (Blueprint $table) {
                $table->text('geom');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('flood_zones');
    }
};
