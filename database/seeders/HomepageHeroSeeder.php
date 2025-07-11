<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HomepageHeroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('homepage_hero')->insert([
            'title' => 'Discover Celestial Beauty',
            'title_ar' => 'اكتشف جمال سيليستيال',
            'description' => 'Explore our range of premium cosmetics inspired by the cosmos.',
            'description_ar' => 'استكشف مجموعتنا من مستحضرات التجميل الفاخرة المستوحاة من الكون.',
            'button_text' => 'Shop Now',
            'button_text_ar' => 'تسوق الآن',
            'button_url' => '/products',
            'secondary_button_text' => 'Learn More',
            'secondary_button_text_ar' => 'اعرف المزيد',
            'secondary_button_url' => '/about',
            'image' => '/images/hero-product.png',
            'scroll_indicator_text' => 'Scroll to explore',
            'scroll_indicator_text_ar' => 'مرر للاستكشاف',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
} 