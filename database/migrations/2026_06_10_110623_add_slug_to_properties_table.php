<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('slug', 191)->nullable()->index();
        });

        // Populate slugs for existing properties
        $properties = DB::table('properties')->select('id', 'title')->get();
        foreach ($properties as $property) {
            $slug = Str::slug($property->title).'-'.$property->id;
            DB::table('properties')->where('id', $property->id)->update(['slug' => $slug]);
        }

        // Apply unique and non-nullable constraint
        Schema::table('properties', function (Blueprint $table) {
            $table->string('slug', 191)->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
