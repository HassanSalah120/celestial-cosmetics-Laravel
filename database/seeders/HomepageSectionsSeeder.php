<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HomepageSectionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            [
                'section_key' => 'hero',
                'title' => 'Discover Celestial Beauty',
                'title_ar' => 'اكتشف جمال سيليستيال',
                'description' => 'Explore our range of premium cosmetics inspired by the cosmos.',
                'description_ar' => 'استكشف مجموعتنا من مستحضرات التجميل الفاخرة المستوحاة من الكون.',
                'button_text' => 'Shop Now',
                'button_text_ar' => 'تسوق الآن',
                'button_url' => '/products',
                'tag' => null,
                'tag_ar' => null,
                'image' => '/images/hero-product.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_key' => 'featured_products',
                'title' => 'Featured Products',
                'title_ar' => 'المنتجات المميزة',
                'description' => 'Our most loved products, handpicked for your beauty journey.',
                'description_ar' => 'منتجاتنا الأكثر محبة، مختارة يدويًا لرحلة جمالك.',
                'button_text' => 'View All',
                'button_text_ar' => 'عرض الكل',
                'button_url' => '/products',
                'tag' => null,
                'tag_ar' => null,
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_key' => 'new_arrivals',
                'title' => 'New Arrivals',
                'title_ar' => 'وصل حديثاً',
                'description' => 'The latest additions to our celestial collection.',
                'description_ar' => 'أحدث الإضافات إلى مجموعتنا السماوية.',
                'button_text' => 'View All',
                'button_text_ar' => 'عرض الكل',
                'button_url' => '/products?sort=newest',
                'tag' => 'NEW',
                'tag_ar' => 'جديد',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_key' => 'categories',
                'title' => 'Shop By Category',
                'title_ar' => 'تسوق حسب الفئة',
                'description' => 'Find your perfect products by browsing our collections.',
                'description_ar' => 'ابحث عن منتجاتك المثالية من خلال تصفح مجموعاتنا.',
                'button_text' => null,
                'button_text_ar' => null,
                'button_url' => null,
                'tag' => null,
                'tag_ar' => null,
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_key' => 'our_story',
                'title' => 'Our Story',
                'title_ar' => 'قصتنا',
                'description' => 'Learn about the journey of Celestial Cosmetics and our commitment to beauty inspired by the cosmos.',
                'description_ar' => 'تعرف على رحلة مستحضرات التجميل السماوية والتزامنا بالجمال المستوحى من الكون.',
                'button_text' => 'Read More',
                'button_text_ar' => 'اقرأ المزيد',
                'button_url' => '/about',
                'tag' => null,
                'tag_ar' => null,
                'image' => '/images/our-story.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_key' => 'testimonials',
                'title' => 'What Our Customers Say',
                'title_ar' => 'ما يقوله عملاؤنا',
                'description' => 'Read reviews from customers who have experienced the magic of Celestial Cosmetics.',
                'description_ar' => 'اقرأ تقييمات العملاء الذين جربوا سحر مستحضرات التجميل السماوية.',
                'button_text' => null,
                'button_text_ar' => null,
                'button_url' => null,
                'tag' => null,
                'tag_ar' => null,
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($sections as $section) {
            // Check if section with this key already exists
            $existingSection = DB::table('homepage_sections')
                ->where('section_key', $section['section_key'])
                ->first();
                
            if (!$existingSection) {
                DB::table('homepage_sections')->insert($section);
                $this->command->info("Added homepage section: {$section['section_key']}");
            } else {
                $this->command->info("Section {$section['section_key']} already exists - skipping");
            }
        }
    }
} 