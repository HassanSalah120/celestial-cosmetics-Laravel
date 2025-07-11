<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HomepageSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('homepage_settings')->insert([
            'sections_order' => json_encode(['hero', 'featured_products', 'offers', 'new_arrivals', 'categories', 'our_story', 'testimonials']),
            'featured_products_count' => 8,
            'new_arrivals_count' => 4,
            'featured_categories_count' => 3,
            'testimonials_count' => 3,
            'show_our_story' => true,
            'show_testimonials' => true,
            'animation_enabled' => true,
            'featured_product_sort' => 'manually',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
} 