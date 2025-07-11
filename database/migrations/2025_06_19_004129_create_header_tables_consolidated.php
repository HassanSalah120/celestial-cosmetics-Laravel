<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This is a consolidated migration that replaces all the previous header-related migrations:
     * - 2025_06_20_000000_create_header_navigation_items_table
     * - 2025_06_20_000001_update_header_settings_table
     * - 2025_06_20_000002_add_timestamps_to_header_settings
     * - 2025_06_19_001120_consolidate_header_tables
     * - 2025_06_19_002126_drop_header_settings_table
     * - 2025_06_19_004000_drop_header_settings_backup_table
     */
    public function up(): void
    {
        // This migration is for documentation purposes only
        // All tables have already been created in previous migrations
        // This consolidates the structure for future reference
        
        // Create header_navigation_items table
        if (!Schema::hasTable('header_navigation_items')) {
            Schema::create('header_navigation_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->string('name');
                $table->string('name_ar')->nullable();
                $table->string('route')->nullable();
                $table->string('url')->nullable();
                $table->string('translation_key')->nullable();
                $table->boolean('open_in_new_tab')->default(false);
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->boolean('has_dropdown')->default(false);
                $table->boolean('show_in_header')->default(true);
                $table->boolean('show_in_footer')->default(false);
                $table->boolean('show_in_mobile')->default(true);
                $table->string('icon')->nullable();
                $table->string('badge_text')->nullable();
                $table->string('badge_color')->nullable();
                $table->timestamps();
                
                $table->foreign('parent_id')->references('id')->on('header_navigation_items')->onDelete('cascade');
            });
        }
        
        // Create global_header_settings table
        if (!Schema::hasTable('global_header_settings')) {
            Schema::create('global_header_settings', function (Blueprint $table) {
                $table->id();
                $table->boolean('show_logo')->default(true);
                $table->boolean('show_profile')->default(true);
                $table->boolean('show_store_hours')->default(true);
                $table->boolean('show_search')->default(true);
                $table->boolean('show_cart')->default(true);
                $table->boolean('show_language_switcher')->default(true);
                $table->boolean('show_auth_links')->default(true);
                $table->boolean('sticky_header')->default(true);
                $table->string('header_style')->default('default');
                $table->string('logo')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a consolidated migration, so we don't want to drop tables
        // in the down method as it would conflict with other migrations
    }
};
