<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;

class NormalizedSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting to seed normalized settings tables...');

        // Helper function to get a setting value
        $getSetting = function($key, $default = null) {
            $setting = Setting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        };

        // 1. General Settings
        $this->command->info('Seeding general settings...');
        DB::table('general_settings')->insert([
            'site_name' => $getSetting('site_name', 'Celestial Cosmetics'),
            'site_name_arabic' => $getSetting('site_name_arabic'),
            'site_logo' => $getSetting('site_logo'),
            'site_favicon' => $getSetting('site_favicon'),
            'enable_language_switcher' => $getSetting('enable_language_switcher', '1') === '1',
            'available_languages' => $getSetting('available_languages'),
            'default_language' => $getSetting('default_language', 'en'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. SEO Defaults
        $this->command->info('Seeding SEO defaults...');
        DB::table('seo_defaults')->insert([
            'default_meta_title' => $getSetting('default_meta_title'),
            'default_meta_description' => $getSetting('default_meta_description'),
            'default_meta_keywords' => $getSetting('default_meta_keywords'),
            'og_default_image' => $getSetting('og_default_image'),
            'og_site_name' => $getSetting('og_site_name'),
            'twitter_site' => $getSetting('twitter_site'),
            'twitter_creator' => $getSetting('twitter_creator'),
            'default_robots_content' => $getSetting('default_robots_content', 'index,follow'),
            'enable_structured_data' => $getSetting('enable_structured_data', '1') === '1',
            'enable_robots_txt' => $getSetting('enable_robots_txt', '1') === '1',
            'enable_sitemap' => $getSetting('enable_sitemap', '1') === '1',
            'sitemap_change_frequency' => $getSetting('sitemap_change_frequency', 'weekly'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Page SEO
        $this->command->info('Seeding page SEO...');
        $pageSeoData = [
            [
                'page_key' => 'homepage',
                'meta_title' => $getSetting('homepage_meta_title'),
                'meta_description' => $getSetting('homepage_meta_description'),
                'meta_keywords' => $getSetting('homepage_meta_keywords'),
                'og_image' => $getSetting('homepage_og_image'),
            ],
            [
                'page_key' => 'products',
                'meta_title' => $getSetting('products_meta_title'),
                'meta_description' => $getSetting('products_meta_description'),
                'meta_keywords' => $getSetting('products_meta_keywords'),
            ],
            [
                'page_key' => 'search',
                'meta_title' => $getSetting('search_meta_title'),
                'meta_description' => $getSetting('search_meta_description'),
                'meta_keywords' => $getSetting('search_meta_keywords'),
            ],
        ];

        foreach ($pageSeoData as $seoData) {
            $seoData['created_at'] = now();
            $seoData['updated_at'] = now();
            DB::table('page_seo')->insert($seoData);
        }

        // 4. Header Settings
        $this->command->info('Seeding header settings...');
        DB::table('header_settings')->insert([
            'bg_color' => $getSetting('header_bg_color', 'bg-primary'),
            'text_color' => $getSetting('header_text_color', 'text-white/80'),
            'hover_color' => $getSetting('header_hover_color', 'text-accent'),
            'button_bg' => $getSetting('header_button_bg', 'bg-accent'),
            'button_text' => $getSetting('header_button_text', 'text-white'),
            'button_hover' => $getSetting('header_button_hover', 'bg-accent-light'),
            'height' => $getSetting('header_height', 'h-16'),
            'show_cart' => $getSetting('show_cart_in_header', '1') === '1',
            'show_auth' => $getSetting('show_auth_in_header', '1') === '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Navigation Items
        $this->command->info('Seeding navigation items...');
        $headerMenu = json_decode($getSetting('header_menu_structure'), true);
        if (is_array($headerMenu)) {
            $this->processNavigationItems($headerMenu);
        }

        // 6. Footer Settings
        $this->command->info('Seeding footer settings...');
        DB::table('footer_settings')->insert([
            'bg_color' => $getSetting('footer_bg_color', 'bg-primary-dark'),
            'text_color' => $getSetting('footer_text_color', 'text-white/80'),
            'heading_color' => $getSetting('footer_heading_color', 'text-white'),
            'link_color' => $getSetting('footer_link_color', 'text-white/70'),
            'link_hover_color' => $getSetting('footer_link_hover_color', 'text-yellow-200'),
            'social_icon_color' => $getSetting('footer_social_icon_color', 'text-white'),
            'social_icon_hover' => $getSetting('footer_social_icon_hover', 'text-yellow-200'),
            'newsletter_input_bg' => $getSetting('footer_newsletter_input_bg', 'bg-white/10'),
            'newsletter_input_text' => $getSetting('footer_newsletter_input_text', 'text-white'),
            'newsletter_button_bg' => $getSetting('footer_newsletter_button_bg', 'bg-accent'),
            'newsletter_button_text' => $getSetting('footer_newsletter_button_text', 'text-white'),
            'newsletter_button_hover' => $getSetting('footer_newsletter_button_hover', 'bg-accent-light'),
            'copyright' => $getSetting('footer_copyright'),
            'terms_text' => $getSetting('footer_terms_text'),
            'privacy_text' => $getSetting('footer_privacy_text'),
            'shipping_text' => $getSetting('footer_shipping_text'),
            'refunds_text' => $getSetting('footer_refunds_text'),
            'tagline' => $getSetting('footer_tagline'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 7. Homepage Settings
        $this->command->info('Seeding homepage settings...');
        DB::table('homepage_settings')->insert([
            'sections_order' => $getSetting('homepage_sections_order'),
            'featured_products_count' => (int)$getSetting('homepage_featured_products_count', 6),
            'new_arrivals_count' => (int)$getSetting('homepage_new_arrivals_count', 4),
            'featured_categories_count' => (int)$getSetting('homepage_featured_categories_count', 3),
            'testimonials_count' => (int)$getSetting('homepage_testimonials_count', 3),
            'show_our_story' => $getSetting('homepage_show_our_story', '1') === '1',
            'show_testimonials' => $getSetting('homepage_show_testimonials', '1') === '1',
            'animation_enabled' => $getSetting('homepage_animation_enabled', '1') === '1',
            'featured_product_sort' => $getSetting('homepage_featured_product_sort', 'manually'),
            'new_product_days' => 30, // Set default to 30 days
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 8. Homepage Hero
        $this->command->info('Seeding homepage hero...');
        DB::table('homepage_hero')->insert([
            'title' => $getSetting('homepage_hero_title', 'Discover Celestial Beauty'),
            'title_ar' => $getSetting('homepage_hero_title_ar'),
            'description' => $getSetting('homepage_hero_description'),
            'description_ar' => $getSetting('homepage_hero_description_ar'),
            'button_text' => $getSetting('homepage_hero_button_text', 'Shop Now'),
            'button_text_ar' => $getSetting('homepage_hero_button_text_ar'),
            'button_url' => $getSetting('homepage_hero_button_url', '/products'),
            'secondary_button_text' => $getSetting('homepage_hero_secondary_button_text', 'Learn More'),
            'secondary_button_text_ar' => $getSetting('homepage_hero_secondary_button_text_ar'),
            'secondary_button_url' => $getSetting('homepage_hero_secondary_button_url', '/about'),
            'image' => $getSetting('homepage_hero_image'),
            'scroll_indicator_text' => $getSetting('homepage_scroll_indicator_text', 'Scroll to explore'),
            'scroll_indicator_text_ar' => $getSetting('homepage_scroll_indicator_text_ar'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 9. Product Display Settings
        $this->command->info('Seeding product display settings...');
        DB::table('product_display_settings')->insert([
            'show_stock_to_client' => $getSetting('show_stock_to_client', '1') === '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 10. Product Card Settings
        $this->command->info('Seeding product card settings...');
        DB::table('product_card_settings')->insert([
            'title_color' => $getSetting('product_title_color', 'text-primary'),
            'price_color' => $getSetting('product_price_color', 'text-accent'),
            'button_bg' => $getSetting('product_card_button_bg', 'bg-accent'),
            'button_text' => $getSetting('product_card_button_text', 'text-white'),
            'button_hover' => $getSetting('product_card_button_hover', 'bg-accent-dark'),
            'sale_badge_bg' => $getSetting('product_sale_badge_bg', 'bg-accent'),
            'sale_badge_text' => $getSetting('product_sale_badge_text', 'text-white'),
            'rating_star_color' => $getSetting('product_rating_star_color', 'text-yellow-400'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 11. Our Story Content
        $this->command->info('Seeding our story content...');
        DB::table('our_story_content')->insert([
            'title' => $getSetting('our_story_title', 'Beauty Inspired by the Cosmos'),
            'subtitle' => $getSetting('our_story_subtitle', 'Our Story'),
            'description' => $getSetting('our_story_description', ''),
            'image' => $getSetting('our_story_image'),
            'button_text' => $getSetting('our_story_button_text', 'Learn more about our journey'),
            'button_url' => $getSetting('our_story_button_url', '/about'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 12. Team Members
        $this->command->info('Seeding team members...');
        $teamMembers = json_decode($getSetting('about_team_members'), true);
        if (is_array($teamMembers)) {
            foreach ($teamMembers as $index => $member) {
                DB::table('team_members')->insert([
                    'name' => $member['name'] ?? '',
                    'title' => $member['title'] ?? '',
                    'bio' => $member['bio'] ?? '',
                    'image' => $member['image'] ?? '',
                    'sort_order' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 13. Corporate Values
        $this->command->info('Seeding corporate values...');
        $corpValues = json_decode($getSetting('about_our_values'), true);
        if (is_array($corpValues)) {
            foreach ($corpValues as $index => $value) {
                DB::table('corporate_values')->insert([
                    'title' => $value['title'] ?? '',
                    'description' => $value['description'] ?? '',
                    'icon' => $value['icon'] ?? '',
                    'sort_order' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 14. About Page
        $this->command->info('Seeding about page...');
        DB::table('about_page')->insert([
            'title' => $getSetting('about_page_title', 'About Celestial Cosmetics'),
            'subtitle' => $getSetting('about_page_subtitle'),
            'our_story' => $getSetting('about_our_story', ''),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 15. Contact Page
        $this->command->info('Seeding contact page...');
        DB::table('contact_page')->insert([
            'title' => $getSetting('contact_page_title', 'Contact Us'),
            'subtitle' => $getSetting('contact_page_subtitle'),
            'phone' => $getSetting('contact_phone'),
            'email' => $getSetting('contact_email'),
            'address' => $getSetting('contact_address'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 16. Store Hours
        $this->command->info('Seeding store hours...');
        $storeHours = json_decode($getSetting('contact_store_hours'), true);
        if (is_array($storeHours)) {
            foreach ($storeHours as $day => $hours) {
                DB::table('store_hours')->insert([
                    'day' => $day,
                    'hours' => $hours,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 17. Shipping Config
        $this->command->info('Seeding shipping config...');
        DB::table('shipping_config')->insert([
            'default_fee' => (float)$getSetting('shipping_default_fee', 10.00),
            'free_threshold' => (float)$getSetting('shipping_free_threshold', 50.00),
            'enable_free' => $getSetting('shipping_enable_free', '1') === '1',
            'international_fee' => (float)$getSetting('shipping_international_fee', 30.00),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 18. Shipping Methods
        $this->command->info('Seeding shipping methods...');
        $shippingMethods = json_decode($getSetting('shipping_methods'), true);
        if (is_array($shippingMethods)) {
            foreach ($shippingMethods as $index => $method) {
                DB::table('shipping_methods')->insert([
                    'name' => $method['name'] ?? '',
                    'code' => $method['code'] ?? '',
                    'fee' => $method['fee'] ?? 0,
                    'estimated_days' => $method['estimated_days'] ?? '',
                    'is_active' => ($method['is_active'] ?? true) === true,
                    'sort_order' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 19. Country Shipping Fees
        $this->command->info('Seeding country shipping fees...');
        $countryFees = json_decode($getSetting('shipping_country_fees'), true);
        if (is_array($countryFees)) {
            foreach ($countryFees as $code => $fee) {
                $countryNames = [
                    'CA' => 'Canada',
                    'UK' => 'United Kingdom',
                    'AU' => 'Australia',
                    // Add more as needed
                ];
                
                DB::table('country_shipping_fees')->insert([
                    'country_code' => $code,
                    'country_name' => $countryNames[$code] ?? $code,
                    'fee' => (float)$fee,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 20. Dashboard Visibility
        $this->command->info('Seeding dashboard visibility...');
        DB::table('dashboard_visibility')->insert([
            'show_products_stat' => $getSetting('dashboard_show_products_stat', '1') === '1',
            'show_orders_stat' => $getSetting('dashboard_show_orders_stat', '1') === '1',
            'show_users_stat' => $getSetting('dashboard_show_users_stat', '1') === '1',
            'show_revenue_stat' => $getSetting('dashboard_show_revenue_stat', '1') === '1',
            'show_sales_chart' => $getSetting('dashboard_show_sales_chart', '1') === '1',
            'show_products_chart' => $getSetting('dashboard_show_products_chart', '1') === '1',
            'show_category_chart' => $getSetting('dashboard_show_category_chart', '1') === '1',
            'show_time_chart' => $getSetting('dashboard_show_time_chart', '1') === '1',
            'show_orders_table' => $getSetting('dashboard_show_orders_table', '1') === '1',
            'show_activities_table' => $getSetting('dashboard_show_activities_table', '1') === '1',
            'elements_per_row' => (int)$getSetting('dashboard_elements_per_row', 4),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 21. Dashboard Cards
        $this->command->info('Seeding dashboard cards...');
        $dashboardCards = [
            'add_product' => $getSetting('dashboard_show_add_product_card', '1') === '1',
            'categories' => $getSetting('dashboard_show_categories_card', '1') === '1',
            'orders' => $getSetting('dashboard_show_orders_card', '1') === '1',
            'users' => $getSetting('dashboard_show_users_card', '1') === '1',
            'messages' => $getSetting('dashboard_show_messages_card', '1') === '1',
            'coupons' => $getSetting('dashboard_show_coupons_card', '1') === '1',
            'settings' => $getSetting('dashboard_show_settings_card', '1') === '1',
            'reports' => $getSetting('dashboard_show_reports_card', '1') === '1',
            'shipping' => $getSetting('dashboard_show_shipping_card', '1') === '1',
            'activities' => $getSetting('dashboard_show_activities_card', '1') === '1',
        ];
        
        $index = 0;
        foreach ($dashboardCards as $key => $isVisible) {
            DB::table('dashboard_cards')->insert([
                'card_key' => $key,
                'is_visible' => $isVisible,
                'sort_order' => $index++,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 22. Color Scheme
        $this->command->info('Seeding color scheme...');
        DB::table('color_scheme')->insert([
            'primary_text_color' => $getSetting('primary_text_color', 'text-primary'),
            'accent_text_color' => $getSetting('accent_text_color', 'text-accent'),
            'accent_bg_color' => $getSetting('accent_bg_color', 'bg-accent'),
            'link_hover_color' => $getSetting('link_hover_color', 'text-accent-dark'),
            'button_focus_ring' => $getSetting('button_focus_ring', 'ring-accent'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 23. Currency Config
        $this->command->info('Seeding currency config...');
        DB::table('currency_config')->insert([
            'default_currency' => $getSetting('default_currency', 'EGP'),
            'currency_symbol' => $getSetting('currency_symbol', 'ج.م'),
            'currency_position' => $getSetting('currency_position', 'right'),
            'thousand_separator' => $getSetting('thousand_separator', ','),
            'decimal_separator' => $getSetting('decimal_separator', '.'),
            'decimal_digits' => (int)$getSetting('decimal_digits', 2),
            'supported_currencies' => $getSetting('supported_currencies'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 24. Homepage Sections
        $this->command->info('Seeding homepage sections...');
        $sections = [
            [
                'section_key' => 'featured_products',
                'title' => $getSetting('homepage_featured_products_title', 'Featured Products'),
                'title_ar' => $getSetting('homepage_featured_products_title_ar'),
                'description' => $getSetting('homepage_featured_products_description'),
                'description_ar' => $getSetting('homepage_featured_products_description_ar'),
                'button_text' => $getSetting('homepage_view_all_products_text', 'View All Products'),
                'button_text_ar' => $getSetting('homepage_view_all_products_text_ar'),
            ],
            [
                'section_key' => 'new_arrivals',
                'title' => $getSetting('homepage_new_arrivals_title', 'New Arrivals'),
                'title_ar' => $getSetting('homepage_new_arrivals_title_ar'),
                'description' => $getSetting('homepage_new_arrivals_description'),
                'description_ar' => $getSetting('homepage_new_arrivals_description_ar'),
                'button_text' => $getSetting('homepage_explore_new_arrivals_text', 'Explore New Arrivals'),
                'button_text_ar' => $getSetting('homepage_explore_new_arrivals_text_ar'),
                'tag' => $getSetting('homepage_new_arrivals_tag', 'Just Arrived'),
                'tag_ar' => $getSetting('homepage_new_arrivals_tag_ar'),
            ],
            [
                'section_key' => 'categories',
                'title' => $getSetting('homepage_shop_by_category_title', 'Shop by Category'),
                'title_ar' => $getSetting('homepage_shop_by_category_title_ar'),
                'description' => $getSetting('homepage_shop_by_category_description'),
                'description_ar' => $getSetting('homepage_shop_by_category_description_ar'),
            ],
            [
                'section_key' => 'testimonials',
                'title' => $getSetting('homepage_testimonials_title', 'What Our Customers Say'),
                'title_ar' => $getSetting('homepage_testimonials_title_ar'),
                'description' => $getSetting('homepage_testimonials_description'),
                'description_ar' => $getSetting('homepage_testimonials_description_ar'),
            ],
            [
                'section_key' => 'our_story',
                'title' => $getSetting('homepage_our_story_title', 'Our Story'),
                'title_ar' => $getSetting('homepage_our_story_title_ar'),
                'description' => $getSetting('homepage_our_story_description'),
                'description_ar' => $getSetting('homepage_our_story_description_ar'),
                'button_text' => $getSetting('homepage_our_story_button_text', 'Read More'),
                'button_text_ar' => $getSetting('homepage_our_story_button_text_ar'),
            ],
            [
                'section_key' => 'offers',
                'title' => $getSetting('homepage_offers_title', 'Special Offers'),
                'title_ar' => $getSetting('homepage_offers_title_ar'),
                'description' => $getSetting('homepage_offers_description'),
                'description_ar' => $getSetting('homepage_offers_description_ar'),
            ],
        ];
        
        foreach ($sections as $section) {
            $section['created_at'] = now();
            $section['updated_at'] = now();
            DB::table('homepage_sections')->insert($section);
        }

        $this->command->info('All normalized settings have been seeded successfully!');
    }

    /**
     * Process navigation items and their dropdowns
     */
    private function processNavigationItems($items, $parentId = null)
    {
        foreach ($items as $index => $item) {
            $navId = DB::table('navigation_items')->insertGetId([
                'parent_id' => $parentId,
                'name' => $item['name'] ?? '',
                'name_ar' => null, // Would need separate translation processing
                'url' => $item['url'] ?? '',
                'route' => $item['route'] ?? null,
                'translation_key' => $item['translation_key'] ?? null,
                'sort_order' => $index,
                'has_dropdown' => isset($item['dropdown_items']) && is_array($item['dropdown_items']) && count($item['dropdown_items']) > 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Process dropdown items if they exist
            if (isset($item['dropdown_items']) && is_array($item['dropdown_items']) && count($item['dropdown_items']) > 0) {
                $this->processNavigationItems($item['dropdown_items'], $navId);
            }
        }
    }
} 