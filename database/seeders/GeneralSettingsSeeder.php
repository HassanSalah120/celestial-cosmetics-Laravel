<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class GeneralSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if general_settings table exists
        if (!Schema::hasTable('general_settings')) {
            $this->command->info('general_settings table does not exist. Skipping GeneralSettingsSeeder.');
            return;
        }
        
        // Check if general settings already exist
        if (DB::table('general_settings')->count() > 0) {
            $this->command->info('General settings already exist. Skipping...');
            return;
        }
        
        $this->command->info('Creating general settings...');
        
        // Get the column names from the table
        $columns = Schema::getColumnListing('general_settings');
        
        // Prepare the data
        $data = [
            'site_name' => 'Celestial Cosmetics',
            'site_name_arabic' => 'سيليستيال كوزمتكس',
            'site_logo' => 'logo.png',
            'site_favicon' => 'favicon.ico',
            'enable_language_switcher' => true,
            'available_languages' => json_encode(['en', 'ar']),
            'default_language' => 'en',
            'enable_social_login' => true,
            'enable_registration' => true,
        ];
        
        // Add timestamps if they exist in the table
        if (in_array('created_at', $columns)) {
            $data['created_at'] = now();
        }
        
        if (in_array('updated_at', $columns)) {
            $data['updated_at'] = now();
        }
        
        // Insert the data
        DB::table('general_settings')->insert($data);
        
        $this->command->info('General settings created successfully!');
    }
}
