<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create a new table for global header settings
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
        
        // Migrate data from header_settings to global_header_settings
        $settings = DB::table('header_settings')->get();
        $globalSettings = [];
        
        foreach ($settings as $setting) {
            $key = $setting->key;
            $value = $setting->value;
            
            // Convert string boolean values to actual booleans
            if ($setting->type === 'boolean') {
                $value = $value === '1' || $value === 'true' || $value === true;
            }
            
            $globalSettings[$key] = $value;
        }
        
        // Insert the settings into the new table
        DB::table('global_header_settings')->insert([
            'show_logo' => $globalSettings['show_logo'] ?? true,
            'show_profile' => $globalSettings['show_profile'] ?? true,
            'show_store_hours' => $globalSettings['show_store_hours'] ?? true,
            'show_search' => $globalSettings['show_search'] ?? true,
            'show_cart' => $globalSettings['show_cart'] ?? true,
            'show_language_switcher' => $globalSettings['show_language_switcher'] ?? true,
            'show_auth_links' => $globalSettings['show_auth_links'] ?? true,
            'sticky_header' => $globalSettings['sticky_header'] ?? true,
            'header_style' => $globalSettings['header_style'] ?? 'default',
            'logo' => $globalSettings['logo'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Add settings fields to header_navigation_items
        Schema::table('header_navigation_items', function (Blueprint $table) {
            $table->boolean('show_in_header')->default(true)->after('has_dropdown');
            $table->boolean('show_in_footer')->default(false)->after('show_in_header');
            $table->boolean('show_in_mobile')->default(true)->after('show_in_footer');
            $table->string('icon')->nullable()->after('show_in_mobile');
            $table->string('badge_text')->nullable()->after('icon');
            $table->string('badge_color')->nullable()->after('badge_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new columns from header_navigation_items
        Schema::table('header_navigation_items', function (Blueprint $table) {
            $table->dropColumn([
                'show_in_header',
                'show_in_footer',
                'show_in_mobile',
                'icon',
                'badge_text',
                'badge_color',
            ]);
        });
        
        // Drop the new global_header_settings table
        Schema::dropIfExists('global_header_settings');
    }
};
