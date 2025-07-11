<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GlobalHeaderSetting;
use Illuminate\Support\Facades\DB;

class GlobalHeaderSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if we already have settings
        if (DB::table('global_header_settings')->count() === 0) {
            // Create default settings
            GlobalHeaderSetting::create([
                'show_logo' => true,
                'show_profile' => true,
                'show_store_hours' => true,
                'show_search' => true,
                'show_cart' => true,
                'show_language_switcher' => true,
                'show_auth_links' => true,
                'sticky_header' => true,
                'header_style' => 'default',
                'logo' => null,
            ]);
            
            $this->command->info('Global header settings created successfully.');
        } else {
            $this->command->info('Global header settings already exist. Skipping...');
        }
    }
}
