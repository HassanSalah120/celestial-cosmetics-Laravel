<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class MissingSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Adding missing settings...');

        $settings = [
            // SEO Settings
            [
                'key' => 'og_site_name',
                'value' => 'Celestial Cosmetics',
                'group' => 'seo',
                'type' => 'text',
                'description' => 'Site name for Open Graph tags',
                'is_public' => true,
            ],
            [
                'key' => 'homepage_og_image',
                'value' => null,
                'group' => 'seo',
                'type' => 'file',
                'description' => 'Open Graph image specifically for homepage',
                'is_public' => true,
            ],
            [
                'key' => 'twitter_creator',
                'value' => '@celestialbeauty',
                'group' => 'seo',
                'type' => 'text',
                'description' => 'Twitter creator handle',
                'is_public' => true,
            ],
            [
                'key' => 'default_robots_content',
                'value' => 'index,follow',
                'group' => 'seo',
                'type' => 'text',
                'description' => 'Default robots meta tag content',
                'is_public' => true,
            ],

            // Footer Settings
            [
                'key' => 'footer_copyright',
                'value' => '© ' . date('Y') . ' Celestial Cosmetics. All rights reserved.',
                'group' => 'footer',
                'type' => 'text',
                'description' => 'Footer copyright text',
                'is_public' => true,
            ],
            [
                'key' => 'footer_terms_text',
                'value' => 'Terms of Service',
                'group' => 'footer',
                'type' => 'text',
                'description' => 'Text for Terms link',
                'is_public' => true,
            ],
            [
                'key' => 'footer_privacy_text',
                'value' => 'Privacy Policy',
                'group' => 'footer',
                'type' => 'text',
                'description' => 'Text for Privacy link',
                'is_public' => true,
            ],
            [
                'key' => 'footer_shipping_text',
                'value' => 'Shipping Policy',
                'group' => 'footer',
                'type' => 'text',
                'description' => 'Text for Shipping link',
                'is_public' => true,
            ],
            [
                'key' => 'footer_refunds_text',
                'value' => 'Refunds',
                'group' => 'footer',
                'type' => 'text',
                'description' => 'Text for Refunds link',
                'is_public' => true,
            ],
            [
                'key' => 'footer_tagline',
                'value' => 'Elevate your beauty with our cosmic collection of premium cosmetics.',
                'group' => 'footer',
                'type' => 'text',
                'description' => 'Footer tagline text',
                'is_public' => true,
            ],

            // Hero Settings
            [
                'key' => 'hero_title',
                'value' => 'Discover Your<br>Celestial Beauty',
                'group' => 'hero',
                'type' => 'textarea',
                'description' => 'Hero section main title',
                'is_public' => true,
            ],
            [
                'key' => 'hero_description',
                'value' => 'Experience luxury skincare inspired by the cosmos. Each product is crafted to bring out your inner radiance.',
                'group' => 'hero',
                'type' => 'textarea',
                'description' => 'Hero section description',
                'is_public' => true,
            ],
            [
                'key' => 'shop_now_text',
                'value' => 'Shop Now',
                'group' => 'hero',
                'type' => 'text',
                'description' => 'Shop now button text',
                'is_public' => true,
            ],
            [
                'key' => 'learn_more_text',
                'value' => 'Learn More',
                'group' => 'hero',
                'type' => 'text',
                'description' => 'Learn more button text',
                'is_public' => true,
            ],
            [
                'key' => 'hero_product_image',
                'value' => null,
                'group' => 'hero',
                'type' => 'file',
                'description' => 'Hero section product image',
                'is_public' => true,
            ],
            [
                'key' => 'scroll_indicator_text',
                'value' => 'Scroll to explore',
                'group' => 'hero',
                'type' => 'text',
                'description' => 'Scroll indicator text',
                'is_public' => true,
            ],
            [
                'key' => 'hero_shop_button_url',
                'value' => '/products',
                'group' => 'hero',
                'type' => 'text',
                'description' => 'URL for shop now button',
                'is_public' => true,
            ],
            [
                'key' => 'hero_learn_more_url',
                'value' => '/about',
                'group' => 'hero',
                'type' => 'text',
                'description' => 'URL for learn more button',
                'is_public' => true,
            ],

            // Arabic versions of Hero Settings
            [
                'key' => 'hero_title_ar',
                'value' => 'اكتشف جمالك<br>السماوي',
                'group' => 'hero',
                'type' => 'textarea',
                'description' => 'Hero section main title (Arabic)',
                'is_public' => true,
            ],
            [
                'key' => 'hero_description_ar',
                'value' => 'استمتع بتجربة العناية بالبشرة الفاخرة المستوحاة من الكون. تم تصميم كل منتج لإظهار إشراقتك الداخلية.',
                'group' => 'hero',
                'type' => 'textarea',
                'description' => 'Hero section description (Arabic)',
                'is_public' => true,
            ],
            [
                'key' => 'shop_now_text_ar',
                'value' => 'تسوق الآن',
                'group' => 'hero',
                'type' => 'text',
                'description' => 'Shop now button text (Arabic)',
                'is_public' => true,
            ],
            [
                'key' => 'learn_more_text_ar',
                'value' => 'اعرف المزيد',
                'group' => 'hero',
                'type' => 'text',
                'description' => 'Learn more button text (Arabic)',
                'is_public' => true,
            ],
            [
                'key' => 'scroll_indicator_text_ar',
                'value' => 'قم بالتمرير لاستكشاف المزيد',
                'group' => 'hero',
                'type' => 'text',
                'description' => 'Scroll indicator text (Arabic)',
                'is_public' => true,
            ],
            [
                'key' => 'site_name_arabic',
                'value' => 'مستحضرات التجميل السماوية',
                'group' => 'general',
                'type' => 'text',
                'description' => 'Site name in Arabic',
                'is_public' => true,
            ],
            
            // Site Branding
            [
                'key' => 'site_logo',
                'value' => null, 
                'group' => 'general',
                'type' => 'file',
                'description' => 'Site logo image',
                'is_public' => true,
            ],
            [
                'key' => 'site_favicon',
                'value' => null,
                'group' => 'general',
                'type' => 'file',
                'description' => 'Site favicon',
                'is_public' => true,
            ],
            
            // Language Settings
            [
                'key' => 'enable_language_switcher',
                'value' => '1',
                'group' => 'general',
                'type' => 'boolean',
                'description' => 'Enable language switcher in header',
                'is_public' => true,
            ],
            [
                'key' => 'available_languages',
                'value' => json_encode(['en' => 'English', 'ar' => 'العربية']),
                'group' => 'general',
                'type' => 'json',
                'description' => 'Available languages for the site',
                'is_public' => true,
            ],
            [
                'key' => 'default_language',
                'value' => 'en',
                'group' => 'general',
                'type' => 'text',
                'description' => 'Default language for the site',
                'is_public' => true,
            ],
        ];

        foreach ($settings as $setting) {
            $exists = DB::table('settings')->where('key', $setting['key'])->exists();
            
            if (!$exists) {
                $this->command->info("Adding missing setting: {$setting['key']}");
                DB::table('settings')->insert(array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            } else {
                $this->command->info("Setting already exists: {$setting['key']}");
            }
        }

        $this->command->info('Missing settings have been added successfully!');
    }
} 