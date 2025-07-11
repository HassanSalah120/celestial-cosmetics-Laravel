<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only run if there are no settings yet
        if (DB::table('settings')->count() === 0) {
            $this->seedGeneralSettings();
            $this->seedEmailSettings();
            $this->seedSocialSettings();
            $this->seedPaymentSettings();
            $this->seedShippingSettings();
            $this->seedHeroSettings();
            
            $this->command->info('Default settings have been created.');
        } else {
            $this->command->info('Settings have already been created.');
        }

        $settings = [
            [
                'key' => 'default_meta_title',
                'value' => 'Celestial Cosmetics - Premium Beauty Products',
                'group' => 'seo'
            ],
            [
                'key' => 'homepage_meta_title',
                'value' => 'Celestial Cosmetics - Discover Your Cosmic Beauty',
                'group' => 'seo'
            ],
            [
                'key' => 'default_meta_description',
                'value' => 'Celestial Cosmetics offers premium beauty products inspired by the cosmos. Explore our range of sustainable, cruelty-free skincare and makeup.',
                'group' => 'seo'
            ],
            [
                'key' => 'homepage_meta_description',
                'value' => 'Discover your cosmic beauty with Celestial Cosmetics. Premium, sustainable, and cruelty-free beauty products inspired by the wonders of the universe.',
                'group' => 'seo'
            ],
            [
                'key' => 'default_meta_keywords',
                'value' => 'celestial cosmetics, beauty products, cosmos, skincare, makeup, sustainable, cruelty-free',
                'group' => 'seo'
            ],
            [
                'key' => 'homepage_meta_keywords',
                'value' => 'celestial beauty, cosmic skincare, premium cosmetics, sustainable beauty, cruelty-free makeup',
                'group' => 'seo'
            ],
            // Add products page SEO settings
            [
                'key' => 'products_meta_title',
                'value' => 'Our Products | Celestial Cosmetics',
                'group' => 'seo'
            ],
            [
                'key' => 'products_meta_description',
                'value' => 'Explore our collection of high-quality cosmetic products. Find the perfect beauty products for your skincare and makeup needs.',
                'group' => 'seo'
            ],
            [
                'key' => 'products_meta_keywords',
                'value' => 'cosmetics, beauty products, skincare, makeup, beauty care, celestial cosmetics products',
                'group' => 'seo'
            ],
            // Add search page SEO settings
            [
                'key' => 'search_meta_title',
                'value' => 'Search Products | Celestial Cosmetics',
                'group' => 'seo'
            ],
            [
                'key' => 'search_meta_description',
                'value' => 'Search our collection of premium beauty products. Find the perfect cosmetics and skincare items for your beauty routine.',
                'group' => 'seo'
            ],
            [
                'key' => 'search_meta_keywords',
                'value' => 'search cosmetics, find beauty products, celestial cosmetics search, beauty product search',
                'group' => 'seo'
            ],
            // Homepage settings
            [
                'key' => 'homepage_hero_enabled',
                'display_name' => 'Enable Hero Section',
                'value' => 'true',
                'group' => 'homepage',
                'type' => 'boolean',
                'description' => 'Show or hide the homepage hero section',
                'is_public' => true,
            ],
            [
                'key' => 'new_product_days',
                'display_name' => 'New Product Days',
                'value' => '30',
                'group' => 'homepage',
                'type' => 'number',
                'description' => 'Number of days a product is considered "new" after creation',
                'is_public' => true,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
    
    /**
     * Seed general settings.
     */
    private function seedGeneralSettings()
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'Celestial Cosmetics',
                'group' => 'general',
                'type' => 'text',
                'options' => null,
                'description' => 'site_name',
                'is_public' => true,
            ],
            [
                'key' => 'site_description',
                'value' => 'Celestial-themed beauty products for the modern consumer',
                'group' => 'general',
                'type' => 'textarea',
                'options' => null,
                'description' => 'site_description',
                'is_public' => true,
            ],
            [
                'key' => 'site_logo',
                'value' => null,
                'group' => 'general',
                'type' => 'file',
                'options' => null,
                'description' => 'site_logo',
                'is_public' => true,
            ],
            [
                'key' => 'site_favicon',
                'value' => null,
                'group' => 'general',
                'type' => 'file',
                'options' => null,
                'description' => 'site_favicon',
                'is_public' => true,
            ],
            [
                'key' => 'address',
                'value' => '123 Cosmic Way, Starlight City, Universe 12345',
                'group' => 'general',
                'type' => 'textarea',
                'options' => null,
                'description' => 'address',
                'is_public' => true,
            ],
            [
                'key' => 'phone',
                'value' => '+1 (555) 123-4567',
                'group' => 'general',
                'type' => 'text',
                'options' => null,
                'description' => 'phone',
                'is_public' => true,
            ],
            [
                'key' => 'email',
                'value' => 'info@celestial-cosmetics.com',
                'group' => 'general',
                'type' => 'text',
                'options' => null,
                'description' => 'email',
                'is_public' => true,
            ],
            [
                'key' => 'enable_registration',
                'value' => '1',
                'group' => 'Authentication',
                'type' => 'boolean',
                'options' => null,
                'description' => 'enable_registration',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
        $this->command->info('General settings created.');
    }

    /**
     * Seed email settings.
     */
    private function seedEmailSettings()
    {
        $settings = [
            [
                'key' => 'email_settings_info',
                'value' => 'Email settings are configured through environment variables (.env file) for security reasons.',
                'group' => 'email',
                'type' => 'info',
                'options' => null,
                'description' => 'email_settings_info',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
        $this->command->info('Email settings created.');
    }

    /**
     * Seed social media settings.
     */
    private function seedSocialSettings()
    {
        $settings = [
            [
                'key' => 'facebook_url',
                'value' => 'https://facebook.com/',
                'group' => 'social',
                'type' => 'text',
                'options' => null,
                'description' => 'facebook_url',
                'is_public' => true,
            ],
            [
                'key' => 'instagram_url',
                'value' => 'https://instagram.com/',
                'group' => 'social',
                'type' => 'text',
                'options' => null,
                'description' => 'instagram_url',
                'is_public' => true,
            ],
            [
                'key' => 'twitter_url',
                'value' => 'https://twitter.com/',
                'group' => 'social',
                'type' => 'text',
                'options' => null,
                'description' => 'twitter_url',
                'is_public' => true,
            ],
            [
                'key' => 'pinterest_url',
                'value' => 'https://pinterest.com/',
                'group' => 'social',
                'type' => 'text',
                'options' => null,
                'description' => 'pinterest_url',
                'is_public' => true,
            ],
            [
                'key' => 'enable_social_login',
                'value' => '1',
                'group' => 'Authentication',
                'type' => 'boolean',
                'options' => null,
                'description' => 'enable_social_login',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
        $this->command->info('Social settings created.');
    }

    /**
     * Seed payment settings.
     */
    private function seedPaymentSettings()
    {
        $settings = [
            [
                'key' => 'currency',
                'value' => 'EGP',
                'group' => 'payment',
                'type' => 'select',
                'options' => json_encode(['USD', 'EUR', 'GBP', 'EGP', 'SAR', 'AED']),
                'description' => 'currency',
                'is_public' => true,
            ],
            [
                'key' => 'currency_symbol',
                'value' => 'EGP',
                'group' => 'payment',
                'type' => 'text',
                'options' => null,
                'description' => 'currency_symbol',
                'is_public' => true,
            ],
            
            // Egyptian Payment Methods
            [
                'key' => 'enable_cash_on_delivery',
                'value' => '1',
                'group' => 'payment',
                'type' => 'boolean',
                'options' => null,
                'description' => 'enable_cash_on_delivery',
                'is_public' => true,
            ],
            [
                'key' => 'cod_fee',
                'value' => '20',
                'group' => 'payment',
                'type' => 'text',
                'options' => null,
                'description' => 'cod_fee',
                'is_public' => true,
            ],
            [
                'key' => 'enable_instapay',
                'value' => '1',
                'group' => 'payment',
                'type' => 'boolean',
                'options' => null,
                'description' => 'enable_instapay',
                'is_public' => true,
            ],
            [
                'key' => 'instapay_number',
                'value' => '',
                'group' => 'payment',
                'type' => 'text',
                'options' => null,
                'description' => 'instapay_number',
                'is_public' => true,
            ],
            [
                'key' => 'enable_vodafone_cash',
                'value' => '1',
                'group' => 'payment',
                'type' => 'boolean',
                'options' => null,
                'description' => 'enable_vodafone_cash',
                'is_public' => true,
            ],
            [
                'key' => 'vodafone_cash_number',
                'value' => '',
                'group' => 'payment',
                'type' => 'text',
                'options' => null,
                'description' => 'vodafone_cash_number',
                'is_public' => true,
            ],
            [
                'key' => 'payment_confirmation_instructions',
                'value' => 'After making your payment, please contact us with your order number and payment details to confirm your order.',
                'group' => 'payment',
                'type' => 'textarea',
                'options' => null,
                'description' => 'payment_confirmation_instructions',
                'is_public' => true,
            ],
            [
                'key' => 'payment_confirmation_contact',
                'value' => 'Phone: +20123456789\nWhatsApp: +20123456789\nEmail: payments@example.com',
                'group' => 'payment',
                'type' => 'textarea',
                'options' => null,
                'description' => 'payment_confirmation_contact',
                'is_public' => true,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
        $this->command->info('Payment settings created.');
    }

    /**
     * Seed shipping settings.
     */
    private function seedShippingSettings()
    {
        $settings = [
            [
                'key' => 'enable_shipping',
                'value' => '1',
                'group' => 'shipping',
                'type' => 'boolean',
                'options' => null,
                'description' => 'enable_shipping',
                'is_public' => false,
            ],
            [
                'key' => 'shipping_flat_rate',
                'value' => '10.00',
                'group' => 'shipping',
                'type' => 'text',
                'options' => null,
                'description' => 'shipping_flat_rate',
                'is_public' => true,
            ],
            [
                'key' => 'free_shipping_min',
                'value' => '50.00',
                'group' => 'shipping',
                'type' => 'text',
                'options' => null,
                'description' => 'free_shipping_min',
                'is_public' => true,
            ],
            [
                'key' => 'shipping_countries',
                'value' => 'US, CA, UK, AU',
                'group' => 'shipping',
                'type' => 'textarea',
                'options' => null,
                'description' => 'shipping_countries',
                'is_public' => true,
            ],
            [
                'key' => 'enable_local_pickup',
                'value' => '1',
                'group' => 'shipping',
                'type' => 'boolean',
                'options' => null,
                'description' => 'enable_local_pickup',
                'is_public' => true,
            ],
            [
                'key' => 'local_pickup_cost',
                'value' => '0.00',
                'group' => 'shipping',
                'type' => 'text',
                'options' => null,
                'description' => 'local_pickup_cost',
                'is_public' => true,
            ],
            [
                'key' => 'default_country',
                'value' => 'EG',
                'group' => 'shipping',
                'type' => 'select',
                'options' => json_encode(['US', 'EG', 'UK', 'CA', 'AU', 'AE', 'SA']),
                'description' => 'default_country',
                'is_public' => true,
            ],
            [
                'key' => 'require_state',
                'value' => '0',
                'group' => 'shipping',
                'type' => 'boolean',
                'options' => null,
                'description' => 'require_state',
                'is_public' => false,
            ],
            [
                'key' => 'require_postal_code',
                'value' => '0',
                'group' => 'shipping',
                'type' => 'boolean',
                'options' => null,
                'description' => 'require_postal_code',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
        $this->command->info('Shipping settings created.');
    }
    
    /**
     * Seed hero settings for the homepage.
     */
    private function seedHeroSettings()
    {
        $settings = [
            [
                'key' => 'hero_title',
                'value' => 'Discover Your<br>Celestial Beauty',
                'group' => 'hero',
                'type' => 'textarea',
                'options' => null,
                'description' => 'hero_title',
                'is_public' => true,
            ],
            [
                'key' => 'hero_description',
                'value' => 'Experience luxury skincare inspired by the cosmos. Each product is crafted to bring out your inner radiance.',
                'group' => 'hero',
                'type' => 'textarea',
                'options' => null,
                'description' => 'hero_description',
                'is_public' => true,
            ],
            [
                'key' => 'shop_now_text',
                'value' => 'Shop Now',
                'group' => 'hero',
                'type' => 'text',
                'options' => null,
                'description' => 'shop_now_text',
                'is_public' => true,
            ],
            [
                'key' => 'learn_more_text',
                'value' => 'Learn More',
                'group' => 'hero',
                'type' => 'text',
                'options' => null,
                'description' => 'learn_more_text',
                'is_public' => true,
            ],
            
            [
                'key' => 'hero_product_image',
                'value' => 'hero-product.png',
                'group' => 'hero',
                'type' => 'file',
                'options' => null,
                'description' => 'hero_product_image',
                'is_public' => true,
            ],
            [
                'key' => 'scroll_indicator_text',
                'value' => 'Scroll to explore',
                'group' => 'hero',
                'type' => 'text',
                'options' => null,
                'description' => 'scroll_indicator_text',
                'is_public' => true,
            ],
            [
                'key' => 'hero_shop_button_url',
                'value' => '/products',
                'group' => 'hero',
                'type' => 'text',
                'options' => null,
                'description' => 'hero_shop_button_url',
                'is_public' => true,
            ],
            [
                'key' => 'hero_learn_more_url',
                'value' => '/about',
                'group' => 'hero',
                'type' => 'text',
                'options' => null,
                'description' => 'hero_learn_more_url',
                'is_public' => true,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
        $this->command->info('Hero settings created.');
    }
}
