<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OurStoryContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data first
        DB::table('our_story_content')->truncate();
        
        // Insert sample data
        DB::table('our_story_content')->insert([
            'title' => 'Our Cosmic Journey',
            'title_ar' => 'رحلتنا الكونية',
            'subtitle' => 'About Us',
            'subtitle_ar' => 'معلومات عنا',
            'description' => 'Celestial Cosmetics was founded with a vision to create beauty products inspired by the cosmos. Our journey began with a passion for natural ingredients and a commitment to sustainability. Today, we continue to innovate and create products that not only enhance your natural beauty but also respect our planet.',
            'description_ar' => 'تأسست سيليستيال كوزمتيكس برؤية لإنشاء منتجات تجميل مستوحاة من الكون. بدأت رحلتنا بشغف للمكونات الطبيعية والتزام بالاستدامة. اليوم، نواصل الابتكار وإنشاء منتجات لا تعزز جمالك الطبيعي فحسب، بل تحترم أيضًا كوكبنا.',
            'image' => '/images/our-story.jpg',
            'button_text' => 'Read Our Full Story',
            'button_text_ar' => 'اقرأ قصتنا الكاملة',
            'button_url' => '/about',
            
            'feature1_title' => 'Natural Ingredients',
            'feature1_title_ar' => 'مكونات طبيعية',
            'feature1_text' => 'We use only the finest natural ingredients',
            'feature1_text_ar' => 'نستخدم فقط أفضل المكونات الطبيعية',
            'feature1_icon' => 'fa-leaf',
            
            'feature2_title' => 'Cruelty Free',
            'feature2_title_ar' => 'خالي من القسوة',
            'feature2_text' => 'We never test on animals',
            'feature2_text_ar' => 'نحن لا نختبر أبدًا على الحيوانات',
            'feature2_icon' => 'fa-heart',
            
            'secondary_button_text' => 'Contact Us',
            'secondary_button_text_ar' => 'اتصل بنا',
            'secondary_button_url' => '/contact',
            
            'year_founded' => '2018',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
