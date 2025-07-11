<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\SettingTranslation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingTranslationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find all settings to make them translatable
        $settings = Setting::all();

        foreach ($settings as $setting) {
            // Add Arabic translation
            SettingTranslation::updateOrCreate(
                [
                    'setting_id' => $setting->id,
                    'locale' => 'ar'
                ],
                [
                    'value' => $this->getArabicTranslation($setting->key, $setting->group, $setting->value)
                ]
            );
        }

        $this->command->info('Setting translations added successfully!');
    }

    /**
     * Get Arabic translation based on the setting key and group.
     */
    private function getArabicTranslation($key, $group, $defaultValue)
    {
        // General translations
        $generalTranslations = [
            'site_name' => 'سيليستيال كوزمتكس',
            'site_description' => 'منتجات تجميل مستوحاة من السماء للمرأة العصرية',
            'address' => 'طريق النجوم، مدينة الفلك، الكون ١٢٣٤٥',
            'phone' => '+۱ (٥٥٥) ١٢٣-٤٥٦٧',
            'email' => 'info@celestial-cosmetics.com',
        ];

        // Header translations
        $headerTranslations = [
            'header_bg_color' => 'bg-primary',
            'header_text_color' => 'text-white/80',
            'header_hover_color' => 'text-accent',
            'header_button_bg' => 'bg-accent',
            'header_button_text' => 'text-white',
            'header_button_hover' => 'bg-accent-light',
            'header_height' => 'h-16',
            'show_cart_in_header' => '1',
            'show_auth_in_header' => '1',
        ];

        // Hero section translations
        $heroSectionTranslations = [
            'hero_title' => 'اكتشفي<br>جمالك السماوي',
            'hero_description' => 'استمتعي بمنتجات العناية بالبشرة الفاخرة المستوحاة من الكون، مصممة لتعزيز جمالك الطبيعي وإشراقتك.',
            'shop_now_text' => 'تسوق الآن',
            'learn_more_text' => 'اعرف المزيد',
            'scroll_indicator_text' => 'مرر للاستكشاف',
            'hero_shop_button_url' => '/products',
            'hero_learn_more_url' => '/about',
        ];

        // Our Story section translations
        $ourStoryTranslations = [
            'our_story_title' => 'الجمال المستوحى من الكون',
            'our_story_subtitle' => 'قصتنا',
            'our_story_description' => '<p class="mb-6">في سيليستيال كوزمتكس، نؤمن بقوة المكونات الطبيعية المستوحاة من الكون. تأسست شركتنا على يد خبراء في مجال العناية بالبشرة بهدف إنشاء منتجات فعالة تعزز الجمال الطبيعي.</p><p>نحن ملتزمون بتقديم منتجات عالية الجودة، خالية من المواد الضارة، ومصنوعة بشكل مستدام لنمنحك تجربة جمالية فريدة.</p>',
            'our_story_button_text' => 'تعلم المزيد عن رحلتنا',
            'our_story_button_url' => '/about',
        ];

        // Pages translations
        $pagesTranslations = [
            'about_page_title' => 'عن سيليستيال كوزمتكس',
            'about_page_subtitle' => 'الجمال المستوحى من الكون، صُنع بحب لك',
            'about_our_story' => 'ولدت سيليستيال كوزمتكس من حلم إنشاء منتجات عناية بالبشرة تجمع بين علم الكونيات والجمال. نحن نستوحي من أعجوبة النجوم والكواكب لإنشاء تركيبات فريدة تُحدث ثورة في روتين العناية بالبشرة.',
            'contact_page_title' => 'اتصل بنا',
            'contact_page_subtitle' => 'هل لديك أسئلة أو ملاحظات؟ نود أن نسمع منك',
            'contact_phone' => '(٥٥٥) ١٢٣-٤٥٦٧',
            'contact_email' => 'support@celestialcosmetics.com',
            'contact_address' => 'شارع السديم ١٢٣\nمدينة النجوم، ١٢٣٤٥',
        ];

        // Payment translations
        $paymentTranslations = [
            'currency' => 'ج.م',
            'currency_symbol' => 'ج.م',
            'enable_cash_on_delivery' => '1',
            'cod_fee' => '20',
            'enable_instapay' => '1',
            'enable_vodafone_cash' => '1',
            'payment_confirmation_instructions' => 'بعد إتمام عملية الدفع، يرجى الاتصال بنا مع تفاصيل طلبك ورقم العملية للتأكيد.',
            'payment_confirmation_contact' => 'هاتف: +20123456789\nواتساب: +20123456789\nالبريد الإلكتروني: payments@celestialcosmetics.com',
        ];

        // Product translations
        $productTranslations = [
            'show_stock_to_client' => '1',
        ];

        // SEO translations
        $seoTranslations = [
            'default_meta_title' => 'سيليستيال كوزمتكس | جمال مستوحى من الكون',
            'default_meta_description' => 'تقدم سيليستيال كوزمتكس منتجات تجميل فاخرة مستوحاة من الكون، مصممة لتعزيز جمالك الطبيعي وإشراقتك.',
            'default_meta_keywords' => 'سيليستيال كوزمتكس، منتجات تجميل، منتجات طبيعية، كون، عناية بالبشرة، عناية بالجمال',
            'twitter_site' => '@celestialcosmetics',
        ];

        // Shipping translations
        $shippingTranslations = [
            'shipping_default_fee' => '10.00',
            'shipping_free_threshold' => '50.00',
            'shipping_enable_free' => '1',
            'enable_local_pickup' => '1',
            'local_pickup_cost' => '0.00',
            'shipping_flat_rate' => '10.00',
            'free_shipping_min' => '50.00',
            'shipping_international_fee' => '30.00',
            'shipping_countries' => 'مصر',
        ];

        // Social translations
        $socialTranslations = [
            'facebook_url' => 'https://facebook.com/',
            'instagram_url' => 'https://instagram.com/',
            'twitter_url' => 'https://twitter.com/',
            'pinterest_url' => 'https://pinterest.com/',
        ];

        // Get translations based on group
        switch ($group) {
            case 'general':
                return $generalTranslations[$key] ?? $this->getFallbackTranslation($key, $defaultValue);
            case 'header':
                return $headerTranslations[$key] ?? $this->getFallbackTranslation($key, $defaultValue);
            case 'HeroSection':
                return $heroSectionTranslations[$key] ?? $this->getFallbackTranslation($key, $defaultValue);
            case 'OurStorySection':
                return $ourStoryTranslations[$key] ?? $this->getFallbackTranslation($key, $defaultValue);
            case 'pages':
                return $pagesTranslations[$key] ?? $this->getFallbackTranslation($key, $defaultValue);
            case 'payment':
                return $paymentTranslations[$key] ?? $this->getFallbackTranslation($key, $defaultValue);
            case 'product':
                return $productTranslations[$key] ?? $this->getFallbackTranslation($key, $defaultValue);
            case 'seo':
                return $seoTranslations[$key] ?? $this->getFallbackTranslation($key, $defaultValue);
            case 'shipping':
                return $shippingTranslations[$key] ?? $this->getFallbackTranslation($key, $defaultValue);
            case 'social':
                return $socialTranslations[$key] ?? $this->getFallbackTranslation($key, $defaultValue);
            default:
                return $this->getFallbackTranslation($key, $defaultValue);
        }
    }

    /**
     * Get fallback translation or default value.
     */
    private function getFallbackTranslation($key, $defaultValue)
    {
        // For complex JSON data or settings that should remain untranslated
        if (is_array($defaultValue) || $defaultValue === null || empty($defaultValue) || $this->shouldNotTranslate($key)) {
            return $defaultValue;
        }

        // Return simplified Arabic placeholder for values we don't have specific translations for
        return "ترجمة: " . $defaultValue;
    }

    /**
     * Check if a setting should NOT be translated.
     */
    private function shouldNotTranslate($key)
    {
        $nonTranslatableKeys = [
            'header_bg_color', 'header_text_color', 'header_hover_color', 'header_button_bg', 
            'header_button_text', 'header_button_hover', 'header_height', 'header_menu_structure',
            'header_nav_items', 'site_logo', 'site_favicon', 'enable_robots_txt', 'enable_sitemap',
            'enable_structured_data', 'sitemap_include_images', 'sitemap_change_frequency', 'seo_friendly_urls',
            'google_analytics_id', 'google_site_verification', 'bing_site_verification', 'facebook_app_id',
            'google_search_console_api_key', 'enable_breadcrumb_schema', 'enable_product_schema', 
            'enable_search_schema', 'enable_organization_schema', 'og_default_image',
            'hero_product_image', 'our_story_image'
        ];

        return in_array($key, $nonTranslatableKeys);
    }
}
