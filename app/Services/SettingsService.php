<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;
use App\Models\Settings;

class SettingsService
{
    /**
     * Cache duration in seconds (24 hours)
     */
    protected const CACHE_DURATION = 86400;

    /**
     * Cache key for settings
     */
    protected const CACHE_KEY = 'app_settings';

    /**
     * Mapping of keys to their new normalized tables and fields
     */
    protected $normalizedMapping = [
        // General Settings
        'site_name' => ['table' => 'general_settings', 'field' => 'site_name'],
        'site_name_arabic' => ['table' => 'general_settings', 'field' => 'site_name_arabic'],
        'site_logo' => ['table' => 'general_settings', 'field' => 'site_logo'],
        'site_favicon' => ['table' => 'general_settings', 'field' => 'site_favicon'],
        'enable_language_switcher' => ['table' => 'general_settings', 'field' => 'enable_language_switcher', 'type' => 'boolean'],
        'available_languages' => ['table' => 'general_settings', 'field' => 'available_languages'],
        'default_language' => ['table' => 'general_settings', 'field' => 'default_language'],
        
        // SEO Settings
        'default_meta_title' => ['table' => 'seo_defaults', 'field' => 'default_meta_title'],
        'default_meta_description' => ['table' => 'seo_defaults', 'field' => 'default_meta_description'],
        'default_meta_keywords' => ['table' => 'seo_defaults', 'field' => 'default_meta_keywords'],
        'og_default_image' => ['table' => 'seo_defaults', 'field' => 'og_default_image'],
        'og_site_name' => ['table' => 'seo_defaults', 'field' => 'og_site_name'],
        'twitter_site' => ['table' => 'seo_defaults', 'field' => 'twitter_site'],
        'twitter_creator' => ['table' => 'seo_defaults', 'field' => 'twitter_creator'],
        'default_robots_content' => ['table' => 'seo_defaults', 'field' => 'default_robots_content'],
        'enable_structured_data' => ['table' => 'seo_defaults', 'field' => 'enable_structured_data', 'type' => 'boolean'],
        'enable_robots_txt' => ['table' => 'seo_defaults', 'field' => 'enable_robots_txt', 'type' => 'boolean'],
        'enable_sitemap' => ['table' => 'seo_defaults', 'field' => 'enable_sitemap', 'type' => 'boolean'],
        'sitemap_change_frequency' => ['table' => 'seo_defaults', 'field' => 'sitemap_change_frequency'],
        
        // Add more mappings for other settings
    ];

    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        // First try to get from new normalized tables
        if (array_key_exists($key, $this->normalizedMapping)) {
            $value = $this->getFromNormalizedTable($key, $default);
            if ($value !== null) {
                return $value;
            }
        }

        // Fall back to the original settings table
        return $this->getFromSettingsTable($key, $default);
    }

    /**
     * Set a setting value
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function set(string $key, $value): bool
    {
        // Try to update normalized table if mapping exists
        if (array_key_exists($key, $this->normalizedMapping)) {
            if ($this->setInNormalizedTable($key, $value)) {
                $this->flushCache();
                return true;
            }
        }

        // Fall back to original settings table
        $success = $this->setInSettingsTable($key, $value);
        if ($success) {
            $this->flushCache();
        }
        
        return $success;
    }

    /**
     * Check if a setting exists
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        // Check in normalized tables
        if (array_key_exists($key, $this->normalizedMapping)) {
            $mapping = $this->normalizedMapping[$key];
            $result = DB::table($mapping['table'])
                ->select($mapping['field'])
                ->first();
            
            if ($result && $result->{$mapping['field']} !== null) {
                return true;
            }
        }

        // Check in original settings table
        return Setting::where('key', $key)->exists();
    }

    /**
     * Get all settings
     *
     * @return array
     */
    public function all(): array
    {
        // Get cached settings or retrieve from database
        return Cache::remember(self::CACHE_KEY, self::CACHE_DURATION, function () {
            $settings = [];
            
            // Get all settings from original settings table
            $settingsFromTable = Setting::all();
            foreach ($settingsFromTable as $setting) {
                $settings[$setting->key] = $setting->value;
            }
            
            // Add/override with normalized table values
            foreach ($this->normalizedMapping as $key => $mapping) {
                $value = $this->getFromNormalizedTable($key, null);
                if ($value !== null) {
                    $settings[$key] = $value;
                }
            }
            
            return $settings;
        });
    }

    /**
     * Remove a setting
     *
     * @param string $key
     * @return bool
     */
    public function remove(string $key): bool
    {
        $result = Setting::where('key', $key)->delete();
        $this->flushCache();
        return $result > 0;
    }

    /**
     * Flush the settings cache
     *
     * @return void
     */
    public function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Get setting from normalized table
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getFromNormalizedTable(string $key, $default = null)
    {
        $mapping = $this->normalizedMapping[$key];
        $result = DB::table($mapping['table'])
            ->select($mapping['field'])
            ->first();
        
        if (!$result || $result->{$mapping['field']} === null) {
            return $default;
        }
        
        $value = $result->{$mapping['field']};
        
        // Handle type conversion
        if (isset($mapping['type'])) {
            switch ($mapping['type']) {
                case 'boolean':
                    return (bool) $value;
                case 'integer':
                    return (int) $value;
                case 'float':
                    return (float) $value;
                case 'json':
                    return json_decode($value, true);
            }
        }
        
        return $value;
    }

    /**
     * Set setting in normalized table
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    protected function setInNormalizedTable(string $key, $value): bool
    {
        $mapping = $this->normalizedMapping[$key];
        
        // Handle type conversion
        if (isset($mapping['type'])) {
            switch ($mapping['type']) {
                case 'boolean':
                    $value = (bool) $value;
                    break;
                case 'integer':
                    $value = (int) $value;
                    break;
                case 'float':
                    $value = (float) $value;
                    break;
                case 'json':
                    $value = is_array($value) ? json_encode($value) : $value;
                    break;
            }
        }
        
        // Check if the table has timestamps
        $hasTimestamps = true;
        if ($mapping['table'] === 'general_settings') {
            $hasTimestamps = false; // GeneralSetting model has $timestamps = false
        }
        
        $updateData = [
            $mapping['field'] => $value
        ];
        
        // Only add updated_at if the table has timestamps
        if ($hasTimestamps) {
            $updateData['updated_at'] = now();
        }
        
        return DB::table($mapping['table'])
            ->update($updateData) > 0;
    }

    /**
     * Get setting from the settings table
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getFromSettingsTable(string $key, $default = null)
    {
        $setting = Setting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting in the settings table
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    protected function setInSettingsTable(string $key, $value): bool
    {
        $setting = Setting::where('key', $key)->first();
        
        if ($setting) {
            $setting->value = $value;
            return $setting->save();
        } else {
            $setting = new Setting();
            $setting->key = $key;
            $setting->value = $value;
            return $setting->save();
        }
    }

    /**
     * Update the menu structure settings.
     *
     * @param string $menuStructureJson
     * @return bool
     */
    public function updateMenuStructure($menuStructureJson)
    {
        $menuStructure = json_decode($menuStructureJson, true) ?: [];
        
        // Add translation keys to menu items
        foreach ($menuStructure as &$item) {
            // Main menu items
            if (!isset($item['translation_key']) && isset($item['name'])) {
                // Convert item name to lowercase and use as translation key
                $translationKey = strtolower(str_replace(' ', '_', trim($item['name'])));
                $item['translation_key'] = $translationKey;
            }
            
            // Dropdown items
            if (isset($item['dropdown_items']) && is_array($item['dropdown_items'])) {
                foreach ($item['dropdown_items'] as &$dropdownItem) {
                    if (!isset($dropdownItem['translation_key']) && isset($dropdownItem['name'])) {
                        // Convert dropdown item name to lowercase and use as translation key
                        $translationKey = strtolower(str_replace(' ', '_', trim($dropdownItem['name'])));
                        $translationKey = str_replace('-', '_', $translationKey);
                        $dropdownItem['translation_key'] = $translationKey;
                    }
                }
            }
        }

        return Settings::set('header_menu_structure', json_encode($menuStructure));
    }

    /**
     * Seed the database with initial settings.
     *
     * @return array
     */
    public function seedInitialSettings()
    {
        // Only seed if no settings exist
        if (Setting::count() === 0) {
            $this->seedGeneralSettings();
            $this->seedSocialMediaSettings();
            $this->seedPaymentSettings();
            $this->seedHomepageContentSettings();
            
            return [
                'success' => true,
                'message' => 'Settings seeded successfully!'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Settings already exist, no seeding performed.'
        ];
    }

    /**
     * Seed homepage content settings
     * 
     * @return void
     */
    protected function seedHomepageContentSettings()
    {
        $settings = [
            'homepage_sections_order' => json_encode(['hero', 'featured_products', 'new_arrivals', 'our_story', 'categories', 'testimonials']),
            'homepage_featured_products_count' => '6',
            'homepage_new_arrivals_count' => '4',
            'homepage_featured_categories_count' => '3',
            'homepage_testimonials_count' => '3',
            'homepage_show_our_story' => '1',
            'homepage_show_testimonials' => '1',
            'homepage_view_all_products_text' => 'View All Products',
            'homepage_explore_new_arrivals_text' => 'Explore New Arrivals',
            'homepage_featured_products_title' => 'Featured Products',
            'homepage_featured_products_description' => 'Discover our carefully selected products that highlight the best of our collection.',
            'homepage_new_arrivals_title' => 'New Arrivals',
            'homepage_new_arrivals_tag' => 'Just Arrived',
            'homepage_new_arrivals_description' => 'Check out our latest products added to our collection, bringing you the newest trends and innovations.',
            'homepage_shop_by_category_title' => 'Shop by Category',
            'homepage_shop_by_category_description' => 'Explore our wide range of product categories to find exactly what you need.',
            'homepage_testimonials_title' => 'What Our Customers Say',
            'homepage_testimonials_description' => 'Hear from our satisfied customers about their experience with our products.',
            'homepage_animation_enabled' => '1',
            'homepage_featured_product_sort' => 'manually',
            'homepage_hero_title' => 'Discover Celestial Beauty',
            'homepage_hero_description' => 'Explore our range of premium cosmetics inspired by the cosmos.',
            'homepage_hero_button_text' => 'Shop Now',
            'homepage_hero_secondary_button_text' => 'Learn More',
            'homepage_hero_image' => '/images/hero-product.png',
            'homepage_seo_title' => 'Celestial Cosmetics | Premium Beauty Products',
            'homepage_seo_description' => 'Discover our range of premium cosmetics inspired by the cosmos. Ethical, cruelty-free beauty products for all skin types.',
            'homepage_seo_keywords' => 'cosmetics, beauty, makeup, skincare, cruelty-free, premium, celestial',
            'homepage_seo_og_image' => '/images/og-image.jpg',
            'homepage_our_story_title' => 'Our Story',
            'homepage_our_story_description' => 'Learn about how Celestial Cosmetics began with a passion for creating beauty products inspired by the cosmos.',
            'homepage_our_story_button_text' => 'Read More',
            'homepage_scroll_indicator_text' => 'Scroll to explore',
            
            // Arabic translations
            'homepage_view_all_products_text_ar' => 'عرض جميع المنتجات',
            'homepage_explore_new_arrivals_text_ar' => 'استكشف الوصول الجديد',
            'homepage_featured_products_title_ar' => 'منتجات مميزة',
            'homepage_featured_products_description_ar' => 'اكتشف منتجاتنا المختارة بعناية والتي تسلط الضوء على أفضل ما في مجموعتنا.',
            'homepage_new_arrivals_title_ar' => 'وصل حديثاً',
            'homepage_new_arrivals_tag_ar' => 'وصل للتو',
            'homepage_new_arrivals_description_ar' => 'تحقق من أحدث منتجاتنا المضافة إلى مجموعتنا، والتي تجلب لك أحدث الاتجاهات والابتكارات.',
            'homepage_shop_by_category_title_ar' => 'تسوق حسب الفئة',
            'homepage_shop_by_category_description_ar' => 'استكشف مجموعتنا الواسعة من فئات المنتجات للعثور على ما تحتاجه بالضبط.',
            'homepage_testimonials_title_ar' => 'ما يقوله عملاؤنا',
            'homepage_testimonials_description_ar' => 'استمع إلى عملائنا الراضين عن تجربتهم مع منتجاتنا.',
            'homepage_hero_title_ar' => 'اكتشف جمال سيليستيال',
            'homepage_hero_description_ar' => 'استكشف مجموعتنا من مستحضرات التجميل الفاخرة المستوحاة من الكون.',
            'homepage_hero_button_text_ar' => 'تسوق الآن',
            'homepage_hero_secondary_button_text_ar' => 'اعرف المزيد',
            'homepage_seo_title_ar' => 'سيليستيال كوزمتكس | منتجات تجميل فاخرة',
            'homepage_seo_description_ar' => 'اكتشف مجموعتنا من مستحضرات التجميل الفاخرة المستوحاة من الكون. منتجات تجميل أخلاقية وخالية من القسوة لجميع أنواع البشرة.',
            'homepage_seo_keywords_ar' => 'مستحضرات التجميل، الجمال، المكياج، العناية بالبشرة، خالي من القسوة، فاخر، سماوي',
            'homepage_our_story_title_ar' => 'قصتنا',
            'homepage_our_story_description_ar' => 'تعرف على كيف بدأت سيليستيال كوزمتكس بشغف لإنشاء منتجات تجميل مستوحاة من الكون.',
            'homepage_our_story_button_text_ar' => 'اقرأ المزيد',
            'homepage_scroll_indicator_text_ar' => 'مرر للاستكشاف',
        ];

        $types = [
            'homepage_sections_order' => 'json',
            'homepage_featured_products_count' => 'number',
            'homepage_new_arrivals_count' => 'number',
            'homepage_featured_categories_count' => 'number',
            'homepage_testimonials_count' => 'number',
            'homepage_show_our_story' => 'boolean',
            'homepage_show_testimonials' => 'boolean',
            'homepage_view_all_products_text' => 'text',
            'homepage_explore_new_arrivals_text' => 'text',
            'homepage_featured_products_title' => 'text',
            'homepage_featured_products_description' => 'textarea',
            'homepage_new_arrivals_title' => 'text',
            'homepage_new_arrivals_tag' => 'text',
            'homepage_new_arrivals_description' => 'textarea',
            'homepage_shop_by_category_title' => 'text',
            'homepage_shop_by_category_description' => 'textarea',
            'homepage_testimonials_title' => 'text',
            'homepage_testimonials_description' => 'textarea',
            'homepage_animation_enabled' => 'boolean',
            'homepage_featured_product_sort' => 'select',
            'homepage_hero_title' => 'text',
            'homepage_hero_description' => 'textarea',
            'homepage_hero_button_text' => 'text',
            'homepage_hero_secondary_button_text' => 'text',
            'homepage_hero_image' => 'file',
            'homepage_seo_title' => 'text',
            'homepage_seo_description' => 'textarea',
            'homepage_seo_keywords' => 'textarea',
            'homepage_seo_og_image' => 'file',
            'homepage_our_story_title' => 'text',
            'homepage_our_story_description' => 'textarea',
            'homepage_our_story_button_text' => 'text',
            'homepage_scroll_indicator_text' => 'text',
        ];

        $descriptions = [
            'homepage_sections_order' => 'The order in which sections appear on the homepage',
            'homepage_featured_products_count' => 'Number of featured products to display on homepage',
            'homepage_new_arrivals_count' => 'Number of new arrivals to display on homepage',
            'homepage_featured_categories_count' => 'Number of categories to display on homepage',
            'homepage_testimonials_count' => 'Number of testimonials to display on homepage',
            'homepage_show_our_story' => 'Whether to show the Our Story section on homepage',
            'homepage_show_testimonials' => 'Whether to show testimonials on homepage',
        ];

        $this->createSettings($settings, $types, $descriptions);
    }

    /**
     * Create settings from arrays of data, types, and descriptions
     *
     * @param array $settings
     * @param array $types
     * @param array $descriptions
     * @return void
     */
    protected function createSettings(array $settings, array $types, array $descriptions)
    {
        foreach ($settings as $key => $value) {
            // Skip if setting already exists
            if (Setting::where('key', $key)->exists()) {
                continue;
            }

            $type = $types[$key] ?? null;
            if (str_ends_with($key, '_ar') && !isset($types[$key])) {
                $originalKey = substr($key, 0, -3);
                $type = $types[$originalKey] ?? 'text';
            }

            $description = $descriptions[$key] ?? null;
            if (!$description && str_ends_with($key, '_ar')) {
                $originalKey = substr($key, 0, -3);
                $description = $descriptions[$originalKey] ?? null;
                if ($description) {
                    $description = 'Arabic ' . strtolower($description);
                }
            }

            $setting = new Setting();
            $setting->key = $key;
            $setting->value = $value;
            $setting->group = strpos($key, 'seo') !== false ? 'seo' : 'homepage';
            $setting->type = $type;
            $setting->description = $description;
            $setting->is_public = str_starts_with($key, 'homepage_seo_') || !in_array($type, ['json', 'boolean', 'number', 'select']);
            $setting->save();
        }
    }

    /**
     * Seed general settings (placeholder)
     * 
     * @return void
     */
    protected function seedGeneralSettings()
    {
        // Implementation needed
    }

    /**
     * Seed social media settings (placeholder)
     * 
     * @return void
     */
    protected function seedSocialMediaSettings()
    {
        // Implementation needed
    }

    /**
     * Seed payment settings (placeholder)
     * 
     * @return void
     */
    protected function seedPaymentSettings()
    {
        // Implementation needed
    }
} 