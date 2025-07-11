<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HeaderSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default header settings
        $settings = [
            [
                'key' => 'show_logo',
                'value' => '1',
                'display_name' => 'Show Logo',
                'type' => 'boolean',
                'description' => 'Display the site logo in the header',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'show_profile',
                'value' => '1',
                'display_name' => 'Show Profile Link',
                'type' => 'boolean',
                'description' => 'Display the user profile/account link in the header',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'show_store_hours',
                'value' => '1',
                'display_name' => 'Show Store Hours',
                'type' => 'boolean',
                'description' => 'Display store hours in the header',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'show_search',
                'value' => '1',
                'display_name' => 'Show Search',
                'type' => 'boolean',
                'description' => 'Display the search box in the header',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'show_cart',
                'value' => '1',
                'display_name' => 'Show Cart',
                'type' => 'boolean',
                'description' => 'Display the shopping cart in the header',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'show_language_switcher',
                'value' => '1',
                'display_name' => 'Show Language Switcher',
                'type' => 'boolean',
                'description' => 'Display the language switcher in the header',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'sticky_header',
                'value' => '1',
                'display_name' => 'Sticky Header',
                'type' => 'boolean',
                'description' => 'Make the header stick to the top when scrolling',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'header_style',
                'value' => 'default',
                'display_name' => 'Header Style',
                'type' => 'select',
                'description' => 'The style of the header',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert or update settings
        foreach ($settings as $setting) {
            DB::table('header_settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
} 