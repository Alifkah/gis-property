<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Properties: most-filtered columns
        Schema::table('properties', function (Blueprint $table) {
            if (! $this->indexExists('properties', 'properties_type_index')) {
                $table->index('type', 'properties_type_index');
            }
            if (! $this->indexExists('properties', 'properties_status_index')) {
                $table->index('status', 'properties_status_index');
            }
            if (! $this->indexExists('properties', 'properties_price_index')) {
                $table->index('price', 'properties_price_index');
            }
            if (! $this->indexExists('properties', 'properties_user_id_index')) {
                $table->index('user_id', 'properties_user_id_index');
            }
        });

        // Amenities: type filter
        Schema::table('amenities', function (Blueprint $table) {
            if (! $this->indexExists('amenities', 'amenities_type_index')) {
                $table->index('type', 'amenities_type_index');
            }
        });

        // Property images: property_id lookup + order
        Schema::table('property_images', function (Blueprint $table) {
            if (! $this->indexExists('property_images', 'property_images_property_id_order_index')) {
                $table->index(['property_id', 'order'], 'property_images_property_id_order_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndexIfExists('properties_type_index');
            $table->dropIndexIfExists('properties_status_index');
            $table->dropIndexIfExists('properties_price_index');
            $table->dropIndexIfExists('properties_user_id_index');
        });

        Schema::table('amenities', function (Blueprint $table) {
            $table->dropIndexIfExists('amenities_type_index');
        });

        Schema::table('property_images', function (Blueprint $table) {
            $table->dropIndexIfExists('property_images_property_id_order_index');
        });
    }

    /** Check if index already exists to avoid duplicate index error. */
    private function indexExists(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        if ($driver === 'pgsql') {
            return (bool) $connection->selectOne(
                "SELECT 1 FROM pg_indexes WHERE tablename = ? AND indexname = ?",
                [$table, $indexName]
            );
        }

        // SQLite / MySQL fallback — just return false and let it create
        return false;
    }
};
