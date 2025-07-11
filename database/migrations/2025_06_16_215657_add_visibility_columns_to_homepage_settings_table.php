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
        Schema::table('homepage_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('homepage_settings', 'show_featured_products')) {
                $table->boolean('show_featured_products')->default(true)->after('show_offers');
            }
            if (!Schema::hasColumn('homepage_settings', 'show_new_arrivals')) {
                $table->boolean('show_new_arrivals')->default(true)->after('show_featured_products');
            }
            if (!Schema::hasColumn('homepage_settings', 'show_categories')) {
                $table->boolean('show_categories')->default(true)->after('show_new_arrivals');
            }
            if (!Schema::hasColumn('homepage_settings', 'show_our_story')) {
                $table->boolean('show_our_story')->default(true)->after('show_categories');
            }
            if (!Schema::hasColumn('homepage_settings', 'show_testimonials')) {
                $table->boolean('show_testimonials')->default(true)->after('show_our_story');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('homepage_settings', function (Blueprint $table) {
            $columnsToDrop = [
                'show_featured_products',
                'show_new_arrivals',
                'show_categories',
                'show_our_story',
                'show_testimonials',
            ];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('homepage_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
