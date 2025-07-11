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
        // Check if the header_settings table exists
        if (!Schema::hasTable('header_settings')) {
            Schema::create('header_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('display_name')->nullable();
                $table->string('type')->default('text');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // Insert default settings
        $settings = [
            [
                'key' => 'show_profile',
                'value' => '1',
                'display_name' => 'Show Profile',
                'type' => 'boolean',
                'description' => 'Whether to show the profile/account link in the header',
            ],
            [
                'key' => 'show_store_hours',
                'value' => '1',
                'display_name' => 'Show Store Hours',
                'type' => 'boolean',
                'description' => 'Whether to show store hours in the header',
            ],
            [
                'key' => 'show_search',
                'value' => '1',
                'display_name' => 'Show Search',
                'type' => 'boolean',
                'description' => 'Whether to show the search box in the header',
            ],
            [
                'key' => 'show_cart',
                'value' => '1',
                'display_name' => 'Show Cart',
                'type' => 'boolean',
                'description' => 'Whether to show the shopping cart in the header',
            ],
            [
                'key' => 'show_language_switcher',
                'value' => '1',
                'display_name' => 'Show Language Switcher',
                'type' => 'boolean',
                'description' => 'Whether to show the language switcher in the header',
            ],
            [
                'key' => 'sticky_header',
                'value' => '1',
                'display_name' => 'Sticky Header',
                'type' => 'boolean',
                'description' => 'Whether the header should stick to the top when scrolling',
            ],
            [
                'key' => 'header_style',
                'value' => 'default',
                'display_name' => 'Header Style',
                'type' => 'select',
                'description' => 'The style of the header (default, centered, minimal, full-width)',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('header_settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to drop the table or remove settings on rollback
    }
}; 