<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get all settings grouped by their group
        $allSettings = Setting::all()->groupBy('group');
        
        // Make a copy for display
        $settings = clone $allSettings;
        
        // Remove homepage settings from general settings
        if (isset($settings['general'])) {
            $settings['general'] = $settings['general']->filter(function($setting) {
                return !str_starts_with($setting->key, 'homepage_');
            });
        }
        
        // Remove the homepage group entirely
        unset($settings['homepage']);
        
        // Get active group from request or default to first group
        $groups = array_keys($settings->toArray());
        $activeGroup = $request->group ?? $groups[0] ?? 'general';
        
        // Get settings for active group
        $activeSettings = $settings[$activeGroup] ?? collect();
        
        // Process settings if needed for display
        $this->ensureCorrectSettingTypes($activeSettings);
        
        // Load normalized data if available
        $normalizedData = $this->loadNormalizedData($activeGroup);
        
        // Check if normalized tables have been created
        $normalizedTablesExist = $this->checkNormalizedTablesExist();
        
        return view('admin.settings.index', compact(
            'settings', 
            'allSettings', 
            'groups', 
            'activeGroup', 
            'activeSettings',
            'normalizedTablesExist',
            'normalizedData'
        ));
    }
    
    /**
     * Ensure certain settings have the correct type for display purposes
     * This doesn't modify the database, just the collection for display
     * 
     * @param \Illuminate\Support\Collection $settings
     * @return void
     */
    private function ensureCorrectSettingTypes($settings)
    {
        if (!$settings) return;
        
        foreach ($settings as $setting) {
            // Skip processing any header settings that might have slipped through
            if ($setting->group === 'header') {
                continue;
            }
            
            // Special handling for menu structure JSON settings
            if (in_array($setting->key, ['header_menu_structure', 'header_nav_items'])) {
                $setting->type = 'json';
            }
            
            // Add more special case handling here as needed...
        }
    }
    
    /**
     * Update the settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $group = $request->input('group', 'general');
        $data = $request->except(['_token', '_method', 'group']);
        
        // Check if we can use a normalized table for this group
        if ($this->updateNormalizedSettings($group, $data)) {
            return redirect()->route('admin.settings.index', ['group' => $group])
                ->with('success', 'Settings updated successfully (using normalized tables).');
        }
        
        // Fall back to the old settings table
        $settings = Setting::where('group', $group)->get();
        
        foreach ($settings as $setting) {
            // For boolean settings, handle the checkbox value
            if ($setting->type === 'boolean') {
                $setting->value = $request->has($setting->key) ? '1' : '0';
            }
            // Handle file uploads
            else if ($setting->type === 'file' && $request->hasFile($setting->key)) {
                // Delete old file if exists
                if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                    Storage::disk('public')->delete($setting->value);
                }
                
                // Upload new file
                $file = $request->file($setting->key);
                $path = $file->store('settings', 'public');
                $setting->value = $path;
            }
            // Handle other types of settings
            else if (array_key_exists($setting->key, $data)) {
                $setting->value = $data[$setting->key];
            }
            
            $setting->save();
            
            // Clear this specific setting's cache
            Cache::forget('setting_' . $setting->key);
        }
        
        // Clear all settings cache
        $this->clearSettingsCache();
        
        return redirect()->route('admin.settings.index', ['group' => $group])
            ->with('success', 'Settings updated successfully.');
    }
    
    /**
     * Update settings in normalized tables when possible
     *
     * @param string $group
     * @param array $data
     * @return bool
     */
    private function updateNormalizedSettings($group, $data)
    {
        switch ($group) {
            case 'general':
                $generalSettings = \App\Models\GeneralSetting::first();
                if (!$generalSettings) {
                    return false;
                }
                
                if (isset($data['site_name'])) {
                    $generalSettings->site_name = $data['site_name'];
                }
                
                if (isset($data['site_name_arabic'])) {
                    $generalSettings->site_name_arabic = $data['site_name_arabic'];
                }
                
                if (isset($data['site_logo']) && request()->hasFile('site_logo')) {
                    $file = request()->file('site_logo');
                    $path = $file->store('settings', 'public');
                    $generalSettings->site_logo = $path;
                }
                
                if (isset($data['site_favicon']) && request()->hasFile('site_favicon')) {
                    $file = request()->file('site_favicon');
                    $path = $file->store('settings', 'public');
                    $generalSettings->site_favicon = $path;
                }
                
                $generalSettings->enable_language_switcher = isset($data['enable_language_switcher']);
                
                if (isset($data['available_languages'])) {
                    $generalSettings->available_languages = $data['available_languages'];
                }
                
                if (isset($data['default_language'])) {
                    $generalSettings->default_language = $data['default_language'];
                }
                
                $generalSettings->save();
                return true;
                
            case 'seo':
                $seoDefaults = \App\Models\SeoDefaults::first();
                if (!$seoDefaults) {
                    return false;
                }
                
                foreach ($data as $key => $value) {
                    if (in_array($key, [
                        'default_meta_title',
                        'default_meta_description',
                        'default_meta_keywords',
                        'og_default_image',
                        'og_site_name',
                        'twitter_site',
                        'twitter_creator',
                        'default_robots_content',
                    ])) {
                        $seoDefaults->{$key} = $value;
                    }
                    
                    if (in_array($key, [
                        'enable_structured_data',
                        'enable_robots_txt',
                        'enable_sitemap',
                    ])) {
                        $seoDefaults->{$key} = isset($data[$key]);
                    }
                    
                    if ($key === 'sitemap_change_frequency' && in_array($value, ['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'])) {
                        $seoDefaults->sitemap_change_frequency = $value;
                    }
                }
                
                $seoDefaults->save();
                return true;
                
            case 'footer':
                $footerSettings = \App\Models\FooterSetting::first();
                if (!$footerSettings) {
                    return false;
                }
                
                foreach ($data as $key => $value) {
                    if (in_array($key, [
                        'bg_color', 'text_color', 'heading_color', 'link_color', 
                        'link_hover_color', 'social_icon_color', 'social_icon_hover',
                        'newsletter_input_bg', 'newsletter_input_text', 'newsletter_button_bg',
                        'newsletter_button_text', 'newsletter_button_hover', 'copyright',
                        'terms_text', 'privacy_text', 'shipping_text', 'refunds_text',
                        'tagline'
                    ])) {
                        $footerSettings->{$key} = $value;
                    }
                }
                
                $footerSettings->save();
                return true;
                
            case 'homepage':
                $homepageSettings = \App\Models\HomepageSettings::first();
                if (!$homepageSettings) {
                    return false;
                }
                
                // Homepage hero content
                $hero = \App\Models\HomepageHero::first();
                if ($hero) {
                    foreach ($data as $key => $value) {
                        if (in_array($key, [
                            'homepage_hero_title', 'homepage_hero_title_ar',
                            'homepage_hero_description', 'homepage_hero_description_ar',
                            'homepage_hero_button_text', 'homepage_hero_button_text_ar',
                            'homepage_hero_button_url', 'homepage_hero_secondary_button_text',
                            'homepage_hero_secondary_button_text_ar', 'homepage_hero_secondary_button_url'
                        ])) {
                            $fieldName = str_replace('homepage_hero_', '', $key);
                            $hero->{$fieldName} = $value;
                        }
                    }
                    
                    if (request()->hasFile('homepage_hero_image')) {
                        $file = request()->file('homepage_hero_image');
                        $path = $file->store('homepage', 'public');
                        $hero->image = $path;
                    }
                    
                    $hero->save();
                }
                
                // Homepage settings
                foreach ($data as $key => $value) {
                    if (in_array($key, ['homepage_sections_order'])) {
                        $homepageSettings->sections_order = $value;
                    }
                    
                    // Process numeric fields
                    foreach(['featured_products_count', 'new_arrivals_count', 
                            'featured_categories_count', 'testimonials_count', 'new_product_days'] as $fieldName) {
                        if (isset($data[$fieldName])) {
                            $value = $data[$fieldName];
                            $homepageSettings->{$fieldName} = (int)$value;
                        }
                    }
                    
                    if (in_array($key, [
                        'homepage_show_our_story',
                        'homepage_show_testimonials',
                        'homepage_animation_enabled'
                    ])) {
                        $fieldName = str_replace('homepage_', '', $key);
                        $homepageSettings->{$fieldName} = isset($data[$key]);
                    }
                    
                    if ($key === 'homepage_featured_product_sort') {
                        $homepageSettings->featured_product_sort = $value;
                    }
                }
                
                $homepageSettings->save();
                return true;
                
            // Add more cases for other groups as needed
            
            default:
                return false;
        }
    }
    
    /**
     * Create a new setting.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:settings,key',
            'group' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'value' => 'nullable',
            'description' => 'nullable|string',
            'options' => 'nullable|string',
            'is_public' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('admin.settings.index', ['group' => $request->group])
                ->withErrors($validator)
                ->withInput();
        }
        
        // Format options if needed
        $options = null;
        if ($request->options) {
            if ($request->type === 'select' && strpos($request->options, ':') !== false) {
                // Handle key-value pair options (format: "key1:value1, key2:value2")
                $pairs = array_map('trim', explode(',', $request->options));
                $optionsArray = [];
                
                foreach ($pairs as $pair) {
                    $keyValue = array_map('trim', explode(':', $pair, 2));
                    if (count($keyValue) === 2) {
                        $optionsArray[$keyValue[0]] = $keyValue[1];
                    }
                }
                
                $options = json_encode($optionsArray);
            } else {
                // Simple array options
                $optionsArray = array_map('trim', explode(',', $request->options));
                $options = json_encode($optionsArray);
            }
        }
        
        // Create the setting
        $setting = Setting::create([
            'key' => $request->key,
            'group' => $request->group,
            'type' => $request->type,
            'value' => $request->value,
            'description' => $request->description,
            'options' => $options,
            'is_public' => $request->has('is_public'),
        ]);

        // Clear this specific setting's cache
        Cache::forget('setting_' . $request->key);
        
        // Clear settings cache to reflect changes
        $this->clearSettingsCache();
        
        return redirect()->route('admin.settings.index', ['group' => $request->group])
            ->with('success', 'Setting created successfully.');
    }
    
    /**
     * Delete a setting.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $setting = Setting::findOrFail($id);
        $group = $setting->group;
        
        // Delete file if it's a file setting
        if ($setting->type === 'file' && $setting->value && Storage::disk('public')->exists($setting->value)) {
            Storage::disk('public')->delete($setting->value);
        }
        
        $setting->delete();
        
        // Clear settings cache
        Cache::forget('settings');
        
        return redirect()->route('admin.settings.index', ['group' => $group])
            ->with('success', 'Setting deleted successfully.');
    }
    
    /**
     * Seed general settings.
     */
    private function seedGeneralSettings()
    {
        $settings = [
            [
                'key' => 'site_name',
                'display_name' => 'Site Name',
                'value' => 'Celestial Cosmetics',
                'group' => 'general',
                'type' => 'text',
                'description' => 'The name of your website',
                'is_public' => true,
            ],
            [
                'key' => 'site_description',
                'display_name' => 'Site Description',
                'value' => 'Celestial-themed beauty products for the modern consumer',
                'group' => 'general',
                'type' => 'textarea',
                'description' => 'A short description of your website',
                'is_public' => true,
            ],
            [
                'key' => 'site_logo',
                'display_name' => 'Site Logo',
                'value' => null,
                'group' => 'general',
                'type' => 'file',
                'description' => 'Your website logo (recommended size: 200x50px)',
                'is_public' => true,
            ],
            [
                'key' => 'site_favicon',
                'display_name' => 'Favicon',
                'value' => null,
                'group' => 'general',
                'type' => 'file',
                'description' => 'Your website favicon (must be an .ico file, 16x16px or 32x32px)',
                'is_public' => true,
            ],
            [
                'key' => 'address',
                'display_name' => 'Business Address',
                'value' => '123 Cosmic Way, Starlight City, Universe 12345',
                'group' => 'general',
                'type' => 'textarea',
                'description' => 'Your business address',
                'is_public' => true,
            ],
            [
                'key' => 'phone',
                'display_name' => 'Contact Phone',
                'value' => '+1 (555) 123-4567',
                'group' => 'general',
                'type' => 'text',
                'description' => 'Main contact phone number',
                'is_public' => true,
            ],
            [
                'key' => 'enable_registration',
                'display_name' => 'Enable User Registration',
                'value' => '1',
                'group' => 'general',
                'type' => 'boolean',
                'description' => 'Allow new users to register',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }

    /**
     * Seed email settings.
     */
    private function seedEmailSettings()
    {
        // Create an informational setting explaining that email settings are in .env
        $settings = [
            [
                'key' => 'email_settings_info',
                'display_name' => 'Email Configuration',
                'value' => 'Email settings are configured through environment variables (.env file) for security reasons.',
                'group' => 'email',
                'type' => 'info',
                'description' => 'Please modify your .env file to configure email settings.',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }

    /**
     * Seed social media settings.
     */
    private function seedSocialSettings()
    {
        $settings = [
            [
                'key' => 'facebook_url',
                'display_name' => 'Facebook URL',
                'value' => 'https://facebook.com/',
                'group' => 'social',
                'type' => 'text',
                'description' => 'Your Facebook page URL',
                'is_public' => true,
            ],
            [
                'key' => 'instagram_url',
                'display_name' => 'Instagram URL',
                'value' => 'https://instagram.com/',
                'group' => 'social',
                'type' => 'text',
                'description' => 'Your Instagram profile URL',
                'is_public' => true,
            ],
            [
                'key' => 'twitter_url',
                'display_name' => 'Twitter URL',
                'value' => 'https://twitter.com/',
                'group' => 'social',
                'type' => 'text',
                'description' => 'Your Twitter profile URL',
                'is_public' => true,
            ],
            [
                'key' => 'pinterest_url',
                'display_name' => 'Pinterest URL',
                'value' => 'https://pinterest.com/',
                'group' => 'social',
                'type' => 'text',
                'description' => 'Your Pinterest profile URL',
                'is_public' => true,
            ],
            [
                'key' => 'enable_social_login',
                'display_name' => 'Enable Social Login',
                'value' => '1',
                'group' => 'social',
                'type' => 'boolean',
                'description' => 'Allow users to login with social media accounts',
                'is_public' => false,
            ],
            [
                'key' => 'google_client_id',
                'display_name' => 'Google Client ID',
                'value' => '',
                'group' => 'social',
                'type' => 'text',
                'description' => 'Google OAuth client ID',
                'is_public' => false,
            ],
            [
                'key' => 'google_client_secret',
                'display_name' => 'Google Client Secret',
                'value' => '',
                'group' => 'social',
                'type' => 'password',
                'description' => 'Google OAuth client secret',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }

    /**
     * Seed payment settings.
     */
    private function seedPaymentSettings()
    {
        $settings = [
            [
                'key' => 'currency',
                'display_name' => 'Currency',
                'value' => 'EGP',
                'group' => 'payment',
                'type' => 'select',
                'options' => json_encode(['USD', 'EUR', 'GBP', 'EGP', 'SAR', 'AED']),
                'description' => 'Default currency for your store',
                'is_public' => true,
            ],
            [
                'key' => 'currency_symbol',
                'display_name' => 'Currency Symbol',
                'value' => 'EGP',
                'group' => 'payment',
                'type' => 'text',
                'description' => 'Currency symbol',
                'is_public' => true,
            ],
            [
                'key' => 'stripe_key',
                'display_name' => 'Stripe Key',
                'value' => '',
                'group' => 'payment',
                'type' => 'text',
                'description' => 'Your Stripe publishable key',
                'is_public' => false,
            ],
            [
                'key' => 'stripe_secret',
                'display_name' => 'Stripe Secret',
                'value' => '',
                'group' => 'payment',
                'type' => 'password',
                'description' => 'Your Stripe secret key',
                'is_public' => false,
            ],
            [
                'key' => 'enable_stripe',
                'display_name' => 'Enable Stripe',
                'value' => '0',
                'group' => 'payment',
                'type' => 'boolean',
                'description' => 'Enable Stripe payments',
                'is_public' => true,
            ],
            [
                'key' => 'paypal_client_id',
                'display_name' => 'PayPal Client ID',
                'value' => '',
                'group' => 'payment',
                'type' => 'text',
                'description' => 'Your PayPal client ID',
                'is_public' => false,
            ],
            [
                'key' => 'paypal_secret',
                'display_name' => 'PayPal Secret',
                'value' => '',
                'group' => 'payment',
                'type' => 'password',
                'description' => 'Your PayPal secret',
                'is_public' => false,
            ],
            [
                'key' => 'paypal_mode',
                'display_name' => 'PayPal Mode',
                'value' => 'sandbox',
                'group' => 'payment',
                'type' => 'select',
                'options' => json_encode(['sandbox', 'live']),
                'description' => 'PayPal sandbox or live mode',
                'is_public' => false,
            ],
            [
                'key' => 'enable_paypal',
                'display_name' => 'Enable PayPal',
                'value' => '0',
                'group' => 'payment',
                'type' => 'boolean',
                'description' => 'Enable PayPal payments',
                'is_public' => true,
            ],
            [
                'key' => 'tax_rate',
                'display_name' => 'Tax Rate (%)',
                'value' => '14',
                'group' => 'payment',
                'type' => 'text',
                'description' => 'Default tax rate percentage',
                'is_public' => true,
            ],
            // Egyptian Payment Methods
            [
                'key' => 'enable_cash_on_delivery',
                'display_name' => 'Enable Cash on Delivery (COD)',
                'value' => '1',
                'group' => 'payment',
                'type' => 'boolean',
                'description' => 'Allow customers to pay with cash on delivery',
                'is_public' => true,
            ],
            [
                'key' => 'cod_fee',
                'display_name' => 'Cash on Delivery Fee',
                'value' => '20',
                'group' => 'payment',
                'type' => 'text',
                'description' => 'Additional fee for cash on delivery orders',
                'is_public' => true,
            ],
            [
                'key' => 'enable_instapay',
                'display_name' => 'Enable InstaPay',
                'value' => '1',
                'group' => 'payment',
                'type' => 'boolean',
                'description' => 'Allow customers to pay with InstaPay',
                'is_public' => true,
            ],
            [
                'key' => 'instapay_number',
                'display_name' => 'InstaPay Number',
                'value' => '',
                'group' => 'payment',
                'type' => 'text',
                'description' => 'Your InstaPay phone number or account ID',
                'is_public' => true,
            ],
            [
                'key' => 'enable_vodafone_cash',
                'display_name' => 'Enable Vodafone Cash',
                'value' => '1',
                'group' => 'payment',
                'type' => 'boolean',
                'description' => 'Allow customers to pay with Vodafone Cash',
                'is_public' => true,
            ],
            [
                'key' => 'vodafone_cash_number',
                'display_name' => 'Vodafone Cash Number',
                'value' => '',
                'group' => 'payment',
                'type' => 'text',
                'description' => 'Your Vodafone Cash phone number',
                'is_public' => true,
            ],
            [
                'key' => 'payment_confirmation_instructions',
                'display_name' => 'Payment Confirmation Instructions',
                'value' => 'After making your payment, please contact us with your order number and payment details to confirm your order.',
                'group' => 'payment',
                'type' => 'textarea',
                'description' => 'Instructions for customers to confirm their payment',
                'is_public' => true,
            ],
            [
                'key' => 'payment_confirmation_contact',
                'display_name' => 'Payment Confirmation Contact',
                'value' => 'Phone: +20123456789
WhatsApp: +20123456789
Email: payments@example.com',
                'group' => 'payment',
                'type' => 'textarea',
                'description' => 'Contact details for payment confirmation',
                'is_public' => true,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }

    /**
     * Seed shipping settings.
     */
    private function seedShippingSettings()
    {
        $settings = [
            [
                'key' => 'enable_shipping',
                'display_name' => 'Enable Shipping',
                'value' => '1',
                'group' => 'shipping',
                'type' => 'boolean',
                'description' => 'Enable shipping functionality',
                'is_public' => false,
            ],
            [
                'key' => 'shipping_flat_rate',
                'display_name' => 'Flat Rate Shipping',
                'value' => '10.00',
                'group' => 'shipping',
                'type' => 'text',
                'description' => 'Flat rate shipping cost',
                'is_public' => true,
            ],
            [
                'key' => 'free_shipping_min',
                'display_name' => 'Free Shipping Minimum',
                'value' => '50.00',
                'group' => 'shipping',
                'type' => 'text',
                'description' => 'Minimum order amount for free shipping',
                'is_public' => true,
            ],
            [
                'key' => 'shipping_countries',
                'display_name' => 'Shipping Countries',
                'value' => 'US, CA, UK, AU',
                'group' => 'shipping',
                'type' => 'textarea',
                'description' => 'Countries you ship to (comma separated)',
                'is_public' => true,
            ],
            [
                'key' => 'enable_local_pickup',
                'display_name' => 'Enable Local Pickup',
                'value' => '1',
                'group' => 'shipping',
                'type' => 'boolean',
                'description' => 'Enable local pickup option',
                'is_public' => true,
            ],
            [
                'key' => 'local_pickup_cost',
                'display_name' => 'Local Pickup Cost',
                'value' => '0.00',
                'group' => 'shipping',
                'type' => 'text',
                'description' => 'Cost for local pickup',
                'is_public' => true,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }

    /**
     * Seed homepage content settings.
     */
    private function seedHomepageContentSettings()
    {
        $settings = [
            [
                'key' => 'homepage_sections_order',
                'display_name' => 'Homepage Sections Order',
                'value' => json_encode(['hero', 'offers', 'featured_products', 'new_arrivals', 'our_story', 'categories', 'testimonials']),
                'group' => 'homepage',
                'type' => 'json',
                'description' => 'The order in which sections appear on the homepage',
                'is_public' => false,
            ],
            [
                'key' => 'homepage_featured_products_count',
                'display_name' => 'Featured Products Count',
                'value' => '6',
                'group' => 'homepage',
                'type' => 'number',
                'description' => 'Number of featured products to display on homepage',
                'is_public' => false,
            ],
            [
                'key' => 'homepage_new_arrivals_count',
                'display_name' => 'New Arrivals Count',
                'value' => '3',
                'group' => 'homepage',
                'type' => 'number',
                'description' => 'Number of new arrivals to display on homepage',
                'is_public' => false,
            ],
            [
                'key' => 'homepage_featured_categories_count',
                'display_name' => 'Featured Categories Count',
                'value' => '3',
                'group' => 'homepage',
                'type' => 'number',
                'description' => 'Number of categories to display on homepage',
                'is_public' => false,
            ],
            [
                'key' => 'homepage_testimonials_count',
                'display_name' => 'Testimonials Count',
                'value' => '3',
                'group' => 'homepage',
                'type' => 'number',
                'description' => 'Number of testimonials to display on homepage',
                'is_public' => false,
            ],
            [
                'key' => 'homepage_show_our_story',
                'display_name' => 'Show Our Story Section',
                'value' => '1',
                'group' => 'homepage',
                'type' => 'boolean',
                'description' => 'Whether to show the Our Story section on homepage',
                'is_public' => false,
            ],
            [
                'key' => 'homepage_show_testimonials',
                'display_name' => 'Show Testimonials Section',
                'value' => '1',
                'group' => 'homepage',
                'type' => 'boolean',
                'description' => 'Whether to show testimonials on homepage',
                'is_public' => false,
            ],
            [
                'key' => 'homepage_view_all_products_text',
                'display_name' => 'View All Products Button Text',
                'value' => 'View All Products',
                'group' => 'homepage',
                'type' => 'text',
                'description' => 'Text for the button to view all products',
                'is_public' => true,
            ],
            [
                'key' => 'homepage_explore_new_arrivals_text',
                'display_name' => 'Explore New Arrivals Button Text',
                'value' => 'Explore New Arrivals',
                'group' => 'homepage',
                'type' => 'text',
                'description' => 'Text for the button to explore new arrivals',
                'is_public' => true,
            ],
            [
                'key' => 'homepage_featured_products_title',
                'display_name' => 'Featured Products Title',
                'value' => 'Featured Products',
                'group' => 'homepage',
                'type' => 'text',
                'description' => 'Title for the featured products section',
                'is_public' => true,
            ],
            [
                'key' => 'homepage_featured_products_description',
                'display_name' => 'Featured Products Description',
                'value' => 'Discover our carefully selected products that highlight the best of our collection.',
                'group' => 'homepage',
                'type' => 'textarea',
                'description' => 'Description for the featured products section',
                'is_public' => true,
            ],
            [
                'key' => 'homepage_new_arrivals_title',
                'display_name' => 'New Arrivals Title',
                'value' => 'New Arrivals',
                'group' => 'homepage',
                'type' => 'text',
                'description' => 'Title for the new arrivals section',
                'is_public' => true,
            ],
            [
                'key' => 'homepage_new_arrivals_tag',
                'display_name' => 'New Arrivals Tag',
                'value' => 'Just Arrived',
                'group' => 'homepage',
                'type' => 'text',
                'description' => 'Tag displayed above the new arrivals title',
                'is_public' => true,
            ],
            [
                'key' => 'homepage_new_arrivals_description',
                'display_name' => 'New Arrivals Description',
                'value' => 'Check out our latest products added to our collection, bringing you the newest trends and innovations.',
                'group' => 'homepage',
                'type' => 'textarea',
                'description' => 'Description for the new arrivals section',
                'is_public' => true,
            ],
            [
                'key' => 'homepage_shop_by_category_title',
                'display_name' => 'Shop By Category Title',
                'value' => 'Shop by Category',
                'group' => 'homepage',
                'type' => 'text',
                'description' => 'Title for the categories section',
                'is_public' => true,
            ],
            [
                'key' => 'homepage_shop_by_category_description',
                'display_name' => 'Shop By Category Description',
                'value' => 'Explore our wide range of product categories to find exactly what you need.',
                'group' => 'homepage',
                'type' => 'textarea',
                'description' => 'Description for the categories section',
                'is_public' => true,
            ],
            [
                'key' => 'homepage_testimonials_title',
                'display_name' => 'Testimonials Title',
                'value' => 'What Our Customers Say',
                'group' => 'homepage',
                'type' => 'text',
                'description' => 'Title for the testimonials section',
                'is_public' => true,
            ],
            [
                'key' => 'homepage_testimonials_description',
                'display_name' => 'Testimonials Description',
                'value' => 'Hear from our satisfied customers about their experience with our products.',
                'group' => 'homepage',
                'type' => 'textarea',
                'description' => 'Description for the testimonials section',
                'is_public' => true,
            ],
            [
                'key' => 'homepage_animation_enabled',
                'display_name' => 'Enable Animations',
                'value' => '1',
                'group' => 'homepage',
                'type' => 'boolean',
                'description' => 'Whether to enable animations on homepage elements',
                'is_public' => false,
            ],
            [
                'key' => 'homepage_featured_product_sort',
                'display_name' => 'Featured Products Sort Order',
                'value' => 'manually', 
                'group' => 'homepage',
                'type' => 'select',
                'options' => json_encode(['manually', 'newest', 'bestselling', 'price_asc', 'price_desc']),
                'description' => 'How to sort featured products if not using manual selection',
                'is_public' => false,
            ],
            [
                'key' => 'homepage_hero_button_url',
                'display_name' => 'Shop Button URL',
                'value' => '/products',
                'group' => 'homepage',
                'type' => 'text',
                'description' => 'URL for the Shop Now button',
                'is_public' => true,
            ],
            [
                'key' => 'homepage_hero_secondary_button_url',
                'display_name' => 'Learn More Button URL',
                'value' => '/about',
                'group' => 'homepage',
                'type' => 'text',
                'description' => 'URL for the Learn More button',
                'is_public' => true,
            ],
            [
                'key' => 'homepage_offers_title',
                'display_name' => 'Offers Section Title',
                'value' => 'Special Offers',
                'group' => 'homepage',
                'type' => 'text',
                'description' => 'Title for the special offers section',
                'is_public' => true,
            ],
            [
                'key' => 'homepage_offers_title_ar',
                'display_name' => 'Offers Section Title (Arabic)',
                'value' => 'عروض خاصة',
                'group' => 'homepage',
                'type' => 'text',
                'description' => 'Arabic title for the special offers section',
                'is_public' => true,
            ],
            [
                'key' => 'homepage_offers_description',
                'display_name' => 'Offers Section Description',
                'value' => 'Take advantage of these limited-time special offers and exclusive deals.',
                'group' => 'homepage',
                'type' => 'textarea',
                'description' => 'Description for the special offers section',
                'is_public' => true,
            ],
            [
                'key' => 'homepage_offers_description_ar',
                'display_name' => 'Offers Section Description (Arabic)',
                'value' => 'استفد من هذه العروض الخاصة المحدودة والصفقات الحصرية.',
                'group' => 'homepage',
                'type' => 'textarea',
                'description' => 'Arabic description for the special offers section',
                'is_public' => true,
            ],
        ];
        
        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
    
    /**
     * Run the database migrations to set initial settings.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function seed()
    {
        // Only run if there are no settings yet
        if (Setting::count() === 0) {
            $this->seedGeneralSettings();
            $this->seedEmailSettings();
            $this->seedSocialSettings();
            $this->seedPaymentSettings();
            $this->seedShippingSettings();
            $this->seedHomepageContentSettings();
            
            return redirect()->route('admin.settings.index')
                ->with('success', 'Default settings have been created.');
        }
        
        return redirect()->route('admin.settings.index')
            ->with('info', 'Settings have already been created.');
    }

    /**
     * Clear settings cache to ensure changes take effect immediately
     */
    private function clearSettingsCache()
    {
        // We're not caching email settings anymore (using env only)
        // Cache::forget('email_settings');
        
        // Clear group caches
        Cache::forget('general_settings');
        Cache::forget('social_settings');
        Cache::forget('payment_settings');
        Cache::forget('shipping_settings');
        
        // Clear the all settings cache
        Cache::forget('settings_all');
        
        // Clear settings group caches (from SettingsHelper)
        Cache::forget('settings_group_general');
        Cache::forget('settings_group_social');
        Cache::forget('settings_group_payment');
        Cache::forget('settings_group_shipping');
        Cache::forget('settings_group_email');
        Cache::forget('settings_group_hero');
        
        // Also clear individual setting caches if a specific key is updated
        if (request()->has('settings')) {
            foreach (request()->input('settings') as $id => $value) {
                $setting = Setting::find($id);
                if ($setting) {
                    Cache::forget('setting_' . $setting->key);
                }
            }
        }
    }

    /**
     * Check if normalized tables exist in the database
     * 
     * @return bool
     */
    private function checkNormalizedTablesExist()
    {
        // Check if the general_settings table exists and has rows
        try {
            return Schema::hasTable('general_settings')
                && DB::table('general_settings')->count() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Load data from normalized tables for a specific group
     * 
     * @param string $group
     * @return array|null
     */
    private function loadNormalizedData($group)
    {
        switch ($group) {
            case 'general':
                $generalSettings = \App\Models\GeneralSetting::first();
                return $generalSettings ? $generalSettings->toArray() : null;
                
            case 'seo':
                $seoDefaults = \App\Models\SeoDefaults::first();
                return $seoDefaults ? $seoDefaults->toArray() : null;
                
            case 'footer':
                $footerSettings = \App\Models\FooterSetting::first();
                return $footerSettings ? $footerSettings->toArray() : null;
                
            case 'homepage':
                $homepageSettings = \App\Models\HomepageSettings::first();
                $hero = \App\Models\HomepageHero::first();
                $sections = \App\Models\HomepageSection::all();
                
                return [
                    'settings' => $homepageSettings ? $homepageSettings->toArray() : null,
                    'hero' => $hero ? $hero->toArray() : null,
                    'sections' => $sections ? $sections->toArray() : null,
                ];
                
            default:
                return null;
        }
    }

    /**
     * Display the general settings page.
     *
     * @return \Illuminate\View\View
     */
    public function general()
    {
        // Get general settings from normalized table if exists
        $generalSettings = null;
        if (Schema::hasTable('general_settings')) {
            $generalSettings = \App\Models\GeneralSetting::first();
        }
        
        // Fall back to old settings if needed
        if (!$generalSettings && Schema::hasTable('settings')) {
            $generalSettings = (object) [
                'site_name' => Setting::where('key', 'site_name')->first()?->value ?? 'Celestial Cosmetics',
                'site_name_arabic' => Setting::where('key', 'site_name_arabic')->first()?->value ?? 'سيليستيال كوزمتكس',
                'site_logo' => Setting::where('key', 'site_logo')->first()?->value,
                'site_favicon' => Setting::where('key', 'site_favicon')->first()?->value,
                'enable_social_login' => Setting::where('key', 'enable_social_login')->first()?->value ?? '1',
                'enable_registration' => Setting::where('key', 'enable_registration')->first()?->value ?? '1'
            ];
        }
        
        return view('admin.settings.general', compact('generalSettings'));
    }

    /**
     * Update general settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateGeneral(Request $request)
    {
        $normalizedSuccess = false;
        
        // Try to use normalized table if it exists
        if (Schema::hasTable('general_settings')) {
            $generalSettings = \App\Models\GeneralSetting::first();
            if (!$generalSettings) {
                $generalSettings = new \App\Models\GeneralSetting();
            }
            
            $generalSettings->site_name = $request->input('site_name');
            $generalSettings->site_name_arabic = $request->input('site_name_arabic');
            
            if ($request->hasFile('site_logo')) {
                $file = $request->file('site_logo');
                $path = $file->store('settings', 'public');
                $generalSettings->site_logo = $path;
            }
            
            if ($request->hasFile('site_favicon')) {
                $file = $request->file('site_favicon');
                $path = $file->store('settings', 'public');
                $generalSettings->site_favicon = $path;
            }
            
            $generalSettings->enable_social_login = $request->has('enable_social_login');
            $generalSettings->enable_registration = $request->has('enable_registration');
            
            $generalSettings->save();
            $normalizedSuccess = true;
        }
        
        // Also update old settings table if it exists (for backward compatibility)
        if (Schema::hasTable('settings')) {
            $settingsMap = [
                'site_name' => $request->input('site_name'),
                'site_name_arabic' => $request->input('site_name_arabic'),
                'enable_social_login' => $request->has('enable_social_login') ? '1' : '0',
                'enable_registration' => $request->has('enable_registration') ? '1' : '0'
            ];
            
            foreach ($settingsMap as $key => $value) {
                $setting = Setting::where('key', $key)->first();
                if ($setting) {
                    $setting->value = $value;
                    $setting->save();
                    Cache::forget('setting_' . $key);
                }
            }
            
            // Handle file uploads
            if ($request->hasFile('site_logo')) {
                $setting = Setting::where('key', 'site_logo')->first();
                if ($setting) {
                    // Delete old file if exists
                    if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                        Storage::disk('public')->delete($setting->value);
                    }
                    
                    // Use the same path as for normalized settings
                    $setting->value = $generalSettings->site_logo ?? $request->file('site_logo')->store('settings', 'public');
                    $setting->save();
                    Cache::forget('setting_site_logo');
                }
            }
            
            if ($request->hasFile('site_favicon')) {
                $setting = Setting::where('key', 'site_favicon')->first();
                if ($setting) {
                    // Delete old file if exists
                    if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                        Storage::disk('public')->delete($setting->value);
                    }
                    
                    // Use the same path as for normalized settings
                    $setting->value = $generalSettings->site_favicon ?? $request->file('site_favicon')->store('settings', 'public');
                    $setting->save();
                    Cache::forget('setting_site_favicon');
                }
            }
        }
        
        // Clear all settings cache
        $this->clearSettingsCache();
        
        return redirect()->route('admin.settings.general')
            ->with('success', 'General settings updated successfully' . ($normalizedSuccess ? ' (using normalized tables)' : '') . '.');
    }

    /**
     * Display the currency settings page.
     *
     * @return \Illuminate\View\View
     */
    public function currency()
    {
        // Get currency settings from normalized table if exists
        $currencyConfig = null;
        if (Schema::hasTable('currency_config')) {
            $currencyConfig = \App\Models\CurrencyConfig::first();
        }
        
        // Fall back to old settings if needed
        if (!$currencyConfig && Schema::hasTable('settings')) {
            $currencyConfig = (object) [
                'currency_code' => Setting::where('key', 'currency_code')->first()?->value ?? 'EGP',
                'currency_symbol' => Setting::where('key', 'currency_symbol')->first()?->value ?? 'ج.م',
                'decimal_digits' => Setting::where('key', 'decimal_digits')->first()?->value ?? '2',
                'currency_position' => Setting::where('key', 'currency_position')->first()?->value ?? 'right',
                'decimal_separator' => Setting::where('key', 'decimal_separator')->first()?->value ?? '.',
                'thousand_separator' => Setting::where('key', 'thousand_separator')->first()?->value ?? ','
            ];
        }
        
        return view('admin.settings.currency', compact('currencyConfig'));
    }

    /**
     * Update currency settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateCurrency(Request $request)
    {
        $normalizedSuccess = false;
        
        // Try to use normalized table if it exists
        if (Schema::hasTable('currency_config')) {
            $currencyConfig = \App\Models\CurrencyConfig::first();
            if (!$currencyConfig) {
                $currencyConfig = new \App\Models\CurrencyConfig();
            }
            
            $currencyConfig->currency_code = $request->input('currency_code');
            $currencyConfig->currency_symbol = $request->input('currency_symbol');
            $currencyConfig->decimal_digits = $request->input('decimal_digits');
            $currencyConfig->currency_position = $request->input('currency_position');
            $currencyConfig->decimal_separator = $request->input('decimal_separator');
            $currencyConfig->thousand_separator = $request->input('thousand_separator');
            
            $currencyConfig->save();
            $normalizedSuccess = true;
        }
        
        // Also update old settings table if it exists (for backward compatibility)
        if (Schema::hasTable('settings')) {
            $settingsMap = [
                'currency_code' => $request->input('currency_code'),
                'currency_symbol' => $request->input('currency_symbol'),
                'decimal_digits' => $request->input('decimal_digits'),
                'currency_position' => $request->input('currency_position'),
                'decimal_separator' => $request->input('decimal_separator'),
                'thousand_separator' => $request->input('thousand_separator')
            ];
            
            foreach ($settingsMap as $key => $value) {
                $setting = Setting::where('key', $key)->first();
                if ($setting) {
                    $setting->value = $value;
                    $setting->save();
                    Cache::forget('setting_' . $key);
                }
            }
        }
        
        // Clear all settings cache
        $this->clearSettingsCache();
        
        return redirect()->route('admin.settings.currency')
            ->with('success', 'Currency settings updated successfully' . ($normalizedSuccess ? ' (using normalized tables)' : '') . '.');
    }

    /**
     * Display the language settings page.
     *
     * @return \Illuminate\View\View
     */
    public function language()
    {
        // Get general settings from normalized table if exists
        $generalSettings = null;
        if (Schema::hasTable('general_settings')) {
            $generalSettings = \App\Models\GeneralSetting::first();
        }
        
        // Fall back to old settings if needed
        if (!$generalSettings && Schema::hasTable('settings')) {
            $generalSettings = (object) [
                'enable_language_switcher' => Setting::where('key', 'enable_language_switcher')->first()?->value ?? '1',
                'default_language' => Setting::where('key', 'default_language')->first()?->value ?? 'en'
            ];
            
            $availableLanguages = Setting::where('key', 'available_languages')->first()?->value;
            $generalSettings->available_languages = $availableLanguages ? json_decode($availableLanguages, true) : ['en'];
        }
        
        // Define all available languages
        $allLanguages = [
            'ar' => ['name' => 'Arabic', 'native' => 'العربية'],
            'en' => ['name' => 'English', 'native' => 'English'],
        ];
        
        // Ensure default language is either 'ar' or 'en'
        if (!isset($generalSettings->default_language) || !in_array($generalSettings->default_language, ['ar', 'en'])) {
            $generalSettings->default_language = 'en';
        }
        
        // Ensure $selectedLanguages is always an array
        $selectedLanguages = ['en']; // Default
        
        if (isset($generalSettings->available_languages)) {
            if (is_string($generalSettings->available_languages)) {
                // Try to decode JSON string if it's a string
                $decoded = json_decode($generalSettings->available_languages, true);
                if (is_array($decoded)) {
                    $selectedLanguages = $decoded;
                } elseif ($generalSettings->available_languages) {
                    // If it's a single value string, make it an array
                    $selectedLanguages = [$generalSettings->available_languages];
                }
            } elseif (is_array($generalSettings->available_languages)) {
                $selectedLanguages = $generalSettings->available_languages;
            }
        }
        
        // Filter out languages that aren't in allLanguages (only keep 'ar' and 'en')
        $selectedLanguages = array_intersect($selectedLanguages, array_keys($allLanguages));
        
        // If no languages are selected after filtering, default to English
        if (empty($selectedLanguages)) {
            $selectedLanguages = ['en'];
        }
        
        return view('admin.settings.language', compact('generalSettings', 'allLanguages', 'selectedLanguages'));
    }

    /**
     * Update language settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateLanguage(Request $request)
    {
        $normalizedSuccess = false;
        
        // Filter available languages to only include 'ar' and 'en'
        $availableLanguages = array_intersect($request->input('available_languages', ['en']), ['ar', 'en']);
        
        // If no languages are selected after filtering, default to English
        if (empty($availableLanguages)) {
            $availableLanguages = ['en'];
        }
        
        // Ensure default language is either 'ar' or 'en'
        $defaultLanguage = in_array($request->input('default_language'), ['ar', 'en']) 
            ? $request->input('default_language') 
            : 'en';
        
        // Try to use normalized table if it exists
        if (Schema::hasTable('general_settings')) {
            $generalSettings = \App\Models\GeneralSetting::first();
            if (!$generalSettings) {
                $generalSettings = new \App\Models\GeneralSetting();
            }
            
            $generalSettings->enable_language_switcher = $request->has('enable_language_switcher');
            $generalSettings->default_language = $defaultLanguage;
            $generalSettings->available_languages = $availableLanguages;
            
            $generalSettings->save();
            $normalizedSuccess = true;
        }
        
        // Also update old settings table if it exists (for backward compatibility)
        if (Schema::hasTable('settings')) {
            $settingsMap = [
                'enable_language_switcher' => $request->has('enable_language_switcher') ? '1' : '0',
                'default_language' => $defaultLanguage,
                'available_languages' => json_encode($availableLanguages)
            ];
            
            foreach ($settingsMap as $key => $value) {
                $setting = Setting::where('key', $key)->first();
                if ($setting) {
                    $setting->value = $value;
                    $setting->save();
                    Cache::forget('setting_' . $key);
                }
            }
        }
        
        // Clear all settings cache
        $this->clearSettingsCache();
        
        return redirect()->route('admin.settings.language')
            ->with('success', 'Language settings updated successfully' . ($normalizedSuccess ? ' (using normalized tables)' : '') . '.');
    }

    /**
     * Show usage examples for the Settings facade.
     *
     * @return \Illuminate\View\View
     */
    /* Removed as requested
    public function usageExample()
    {
        return view('admin.settings.usage-example');
    }
    */

    /**
     * Clear application cache manually
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearCache()
    {
        try {
            // Clear configuration cache
            \Artisan::call('config:clear');
            
            // Clear route cache
            \Artisan::call('route:clear');
            
            // Clear view cache
            \Artisan::call('view:clear');
            
            // Clear application cache
            \Artisan::call('cache:clear');
            
            // Clear the settings cache specifically
            $this->clearSettingsCache();
            
            return response()->json(['success' => true, 'message' => 'Application cache cleared successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to clear cache: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display payment settings
     * 
     * @return \Illuminate\View\View
     */
    public function payment()
    {
        // Try to get settings from normalized tables first
        $paymentSettings = [];
        try {
            if (Schema::hasTable('payment_configs')) {
                $paymentConfig = \App\Models\PaymentConfig::first();
                if ($paymentConfig) {
                    $paymentSettings = $paymentConfig->toArray();
                }
            }
        } catch (\Exception $e) {
            // Fallback to settings table or default values
        }
        
        // If we don't have normalized settings, try the settings table if it exists
        if (empty($paymentSettings)) {
            try {
                if (Schema::hasTable('settings')) {
                    $settings = Setting::where('group', 'payment')->get();
                    foreach ($settings as $setting) {
                        $paymentSettings[$setting->key] = $setting->value;
                    }
                }
            } catch (\Exception $e) {
                // If any error occurs, use default values below
            }
        }
        
        // If still empty, provide default values
        if (empty($paymentSettings)) {
            $paymentSettings = [
                'currency' => 'EGP',
                'currency_symbol' => 'ج.م',
                'enable_cash_on_delivery' => '1',
                'cod_fee' => '20.00',
                'enable_instapay' => '0',
                'instapay_number' => '',
                'enable_vodafone_cash' => '0',
                'vodafone_cash_number' => '',
                'payment_confirmation_instructions' => 'After making your payment, please contact us with your order number and payment details to confirm your order.',
                'payment_confirmation_contact' => "Phone: +20123456789\nWhatsApp: +20123456789\nEmail: payments@example.com"
            ];
        }
        
        // Get countries for dropdown (with fallback if the helper doesn't exist)
        try {
            $countries = \App\Helpers\CountryHelper::getCountries();
        } catch (\Exception $e) {
            $countries = [
                'EG' => 'Egypt',
                'US' => 'United States',
                'GB' => 'United Kingdom',
            ];
        }
        
        return view('admin.settings.payment', compact('paymentSettings', 'countries'));
    }
    
    /**
     * Update payment settings
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePayment(Request $request)
    {
        // Debug log request data
        Log::info('Payment Settings Request Data', [
            'all' => $request->all(),
            'has_enable_stripe' => $request->has('enable_stripe'),
            'enable_stripe_value' => $request->input('enable_stripe')
        ]);
        
        $validated = $request->validate([
            'currency' => 'required|string|max:10',
            'currency_symbol' => 'required|string|max:10',
            'cod_fee' => 'required|numeric|min:0',
            'instapay_number' => 'nullable|string',
            'vodafone_cash_number' => 'nullable|string',
            'payment_confirmation_instructions' => 'nullable|string',
            'payment_confirmation_contact' => 'nullable|string',
            // Gulf & MENA region payment methods
            'bank_account_details' => 'nullable|string',
            'bank_transfer_instructions' => 'nullable|string',
            'fawry_code' => 'nullable|string',
            'stc_pay_number' => 'nullable|string',
            'benefit_pay_number' => 'nullable|string',
            // Stripe fields
            'stripe_publishable_key' => 'nullable|string',
            'stripe_secret_key' => 'nullable|string',
            'stripe_webhook_secret' => 'nullable|string',
            'stripe_mode' => 'nullable|in:test,live',
            'stripe_capture_method' => 'nullable|string',
            'stripe_statement_descriptor' => 'nullable|string|max:22',
        ]);
        
        // For PaymentConfig model which has boolean casts
        $booleanFields = [
            'enable_cash_on_delivery' => $request->has('enable_cash_on_delivery'),
            'enable_instapay' => $request->has('enable_instapay'),
            'enable_vodafone_cash' => $request->has('enable_vodafone_cash'),
            // Gulf & MENA region payment methods
            'enable_bank_transfer' => $request->has('enable_bank_transfer'),
            'enable_fawry' => $request->has('enable_fawry'),
            'enable_stc_pay' => $request->has('enable_stc_pay'),
            'enable_mada' => $request->has('enable_mada'),
            'enable_knet' => $request->has('enable_knet'),
            'enable_benefit_pay' => $request->has('enable_benefit_pay'),
            // Stripe
            'enable_stripe' => $request->has('enable_stripe'),
            'stripe_capture_method' => $request->has('stripe_capture_method'),
        ];
        
        // For settings table which expects string '1'/'0'
        $stringBooleans = [
            'enable_cash_on_delivery' => $request->has('enable_cash_on_delivery') ? '1' : '0',
            'enable_instapay' => $request->has('enable_instapay') ? '1' : '0',
            'enable_vodafone_cash' => $request->has('enable_vodafone_cash') ? '1' : '0',
            // Gulf & MENA region payment methods
            'enable_bank_transfer' => $request->has('enable_bank_transfer') ? '1' : '0',
            'enable_fawry' => $request->has('enable_fawry') ? '1' : '0',
            'enable_stc_pay' => $request->has('enable_stc_pay') ? '1' : '0',
            'enable_mada' => $request->has('enable_mada') ? '1' : '0',
            'enable_knet' => $request->has('enable_knet') ? '1' : '0',
            'enable_benefit_pay' => $request->has('enable_benefit_pay') ? '1' : '0',
            // Stripe
            'enable_stripe' => $request->has('enable_stripe') ? '1' : '0',
            'stripe_capture_method' => $request->has('stripe_capture_method') ? '1' : '0',
        ];
        
        $savedToNormalizedTable = false;
        $savedToSettingsTable = false;
        
        try {
            // Try to update normalized table first
            if (Schema::hasTable('payment_configs')) {
                $config = \App\Models\PaymentConfig::first();
                if (!$config) {
                    $config = new \App\Models\PaymentConfig();
                }
                
                // Log current model state
                Log::info('PaymentConfig Before Update', [
                    'exists' => $config->exists,
                    'enable_stripe' => $config->enable_stripe,
                    'stripe_publishable_key' => $config->stripe_publishable_key,
                ]);
                
                // Add validated data to config
                foreach ($validated as $key => $value) {
                    $config->$key = $value;
                }
                
                // Add boolean values properly
                foreach ($booleanFields as $key => $value) {
                    $config->$key = $value;
                }
                
                // Log model state before saving
                Log::info('PaymentConfig After Update (Before Save)', [
                    'enable_stripe' => $config->enable_stripe,
                    'stripe_publishable_key' => $config->stripe_publishable_key,
                ]);
                
                $config->save();
                
                // Log after saving
                Log::info('PaymentConfig After Save', [
                    'id' => $config->id,
                    'enable_stripe' => $config->enable_stripe,
                    'stripe_publishable_key' => $config->stripe_publishable_key,
                ]);
                
                $savedToNormalizedTable = true;
            }
            
            // Also try to update settings table if it exists (for backwards compatibility)
            if (Schema::hasTable('settings')) {
                // Regular fields
                foreach ($validated as $key => $value) {
                    Setting::updateOrCreate(
                        ['key' => $key, 'group' => 'payment'],
                        [
                            'value' => $value, 
                            'type' => 'text',
                            'display_name' => ucwords(str_replace('_', ' ', $key)),
                            'group' => 'payment'
                        ]
                    );
                }
                
                // Handle boolean fields as strings for the settings table
                foreach ($stringBooleans as $key => $value) {
                    Setting::updateOrCreate(
                        ['key' => $key, 'group' => 'payment'],
                        [
                            'value' => $value, 
                            'type' => 'boolean',
                            'display_name' => ucwords(str_replace('_', ' ', $key)),
                            'group' => 'payment'
                        ]
                    );
                }
                
                $savedToSettingsTable = true;
            }
            
            // Clear the settings cache
            $this->clearSettingsCache();
            
            $message = 'Payment settings updated successfully';
            if ($savedToNormalizedTable && !$savedToSettingsTable) {
                $message .= ' (using normalized tables only)';
            } elseif (!$savedToNormalizedTable && $savedToSettingsTable) {
                $message .= ' (using legacy settings table only)';
            } elseif ($savedToNormalizedTable && $savedToSettingsTable) {
                $message .= ' (using both normalized and legacy tables)';
            }
            
            return redirect()->route('admin.settings.payment')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.payment')
                ->with('error', 'Failed to update payment settings: ' . $e->getMessage());
        }
    }
}
