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

        Schema::create('districts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE districts ADD COLUMN geom GEOMETRY(MultiPolygon, 4326) NOT NULL');
        } else {
            Schema::table('districts', function (Blueprint $table) {
                $table->text('geom');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
