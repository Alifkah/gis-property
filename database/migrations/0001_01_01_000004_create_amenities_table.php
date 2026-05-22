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

        Schema::create('amenities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 150);
            $table->string('type', 50);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE amenities ADD COLUMN geom GEOMETRY(Point, 4326) NOT NULL');
        } else {
            Schema::table('amenities', function (Blueprint $table) {
                $table->text('geom');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('amenities');
    }
};
