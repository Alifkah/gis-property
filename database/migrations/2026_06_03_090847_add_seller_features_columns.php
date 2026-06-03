<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->integer('views_count')->default(0);
            $table->integer('whatsapp_clicks_count')->default(0);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('company_name', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('logo_path', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['views_count', 'whatsapp_clicks_count']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'description', 'logo_path']);
        });
    }
};
