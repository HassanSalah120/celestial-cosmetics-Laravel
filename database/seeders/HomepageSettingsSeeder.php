<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class HomepageSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hero section settings
        $homepageHero = [
            'title' => 'Discover Celestial Beauty',
            'description' => 'Explore our range of premium cosmetics inspired by the cosmos.',
            'button_text' => 'Shop Now',
            'secondary_button_text' => 'Learn More',
            'button_url' => '/products',
            'secondary_button_url' => '/about',
            'image' => '/images/hero-product.png',
            'scroll_indicator_text' => 'Scroll to explore',
            'title_ar' => 'اكتشف جمال سيليستيال',
            'description_ar' => 'استكشف مجموعتنا من مستحضرات التجميل الفاخرة المستوحاة من الكون.',
            'button_text_ar' => 'تسوق الآن',
            'secondary_button_text_ar' => 'اعرف المزيد',
            'scroll_indicator_text_ar' => 'مرر للاستكشاف'
        ];

        // Homepage sections visibility
        $homepageSections = [
            'enable_hero' => true,
            'enable_offers' => true,
            'enable_featured_products' => true,
            'enable_new_arrivals' => true,
            'enable_our_story' => true,
            'enable_categories' => true,
            'enable_testimonials' => true
        ];

        // Homepage general settings
        $homepageSettings = [
            'animation_enabled' => true,
            'featured_products_count' => 8,
            'new_arrivals_count' => 4,
            'testimonials_count' => 3
        ];

        // Add to database
        $this->addSetting('homepage_hero', json_encode($homepageHero), 'homepage', 'json', 'Hero section configuration');
        $this->addSetting('homepage_sections', json_encode($homepageSections), 'homepage', 'json', 'Section visibility settings');
        $this->addSetting('homepage_settings', json_encode($homepageSettings), 'homepage', 'json', 'General homepage display settings');
    }

    /**
     * Add a setting to the database
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @param string $type
     * @param string $description
     * @return void
     */
    private function addSetting($key, $value, $group, $type, $description)
    {
        Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
                'type' => $type,
                'description' => $description,
                'is_public' => false
            ]
        );
    }
} 