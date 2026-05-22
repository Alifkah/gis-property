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
            DB::statement('CREATE EXTENSION IF NOT EXISTS pgcrypto');
        }

        Schema::create('users', function (Blueprint $table) {
            if (DB::getDriverName() === 'pgsql') {
                $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            } else {
                $table->uuid('id')->primary();
            }

            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->string('password', 255);
            $table->string('phone', 20)->nullable();
            if (DB::getDriverName() === 'pgsql') {
                $table->timestampTz('created_at')->nullable()->default(DB::raw('NOW()'));
                $table->timestampTz('updated_at')->nullable()->default(DB::raw('NOW()'));
            } else {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
