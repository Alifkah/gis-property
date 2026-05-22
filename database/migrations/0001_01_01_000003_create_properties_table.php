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

        Schema::create('properties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 20);
            $table->string('title', 150);
            $table->decimal('price', 15, 2);
            $table->integer('land_area');
            $table->integer('building_area')->default(0);
            $table->integer('bedroom')->default(0);
            $table->integer('bathroom')->default(0);
            $table->string('status', 20)->default('Tersedia');
            if (DB::getDriverName() === 'pgsql') {
                $table->timestampTz('created_at')->nullable()->default(DB::raw('NOW()'));
                $table->timestampTz('updated_at')->nullable()->default(DB::raw('NOW()'));
            } else {
                $table->timestamps();
            }
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE properties ADD COLUMN geom GEOMETRY(Point, 4326) NOT NULL');
        } else {
            Schema::table('properties', function (Blueprint $table) {
                $table->text('geom');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
