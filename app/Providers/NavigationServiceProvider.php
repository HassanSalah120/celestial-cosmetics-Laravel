<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\HeaderNavigationItem;
use App\Models\HeaderSetting;
use App\Models\FooterSection;
use App\Models\FooterSetting;

class NavigationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share header navigation data with all views
        View::composer('layouts.navigation', function ($view) {
            try {
                $headerItems = [];
                $headerSettings = [];
                $isArabic = app()->getLocale() === 'ar';
                
                // Only try to fetch data if the tables exist
                if (Schema::hasTable('header_navigation_items')) {
                    $headerItems = HeaderNavigationItem::getTopLevel();
                    
                    // Debug header items for troubleshooting
                    Log::info('Header Navigation Items:', [
                        'count' => count($headerItems),
                        'locale' => app()->getLocale(),
                        'items' => $headerItems->toArray()
                    ]);
                    
                    // If using Arabic, make sure we have proper translations
                    if ($isArabic) {
                        foreach ($headerItems as $item) {
                            // If name_ar is empty, use a hardcoded translation as fallback
                            if (empty($item->name_ar)) {
                                // Common navigation items hardcoded fallbacks
                                $translations = [
                                    'Home' => 'الرئيسية',
                                    'Products' => 'المنتجات',
                                    'Special Offers' => 'عروض خاصة',
                                    'About Us' => 'من نحن',
                                    'Contact' => 'اتصل بنا',
                                    'Celestial Cosmetics' => 'سيليستيال كوزمتكس'
                                ];
                                
                                if (isset($translations[$item->name])) {
                                    $item->name_ar = $translations[$item->name];
                                }
                            }
                        }
                    }
                    
                    // Manually load children for each item to ensure they're available
                    foreach ($headerItems as $item) {
                        if ($item->has_dropdown) {
                            $item->children = $item->children()->get();
                            Log::info("Children for menu item {$item->name}:", [
                                'count' => $item->children->count(),
                                'children' => $item->children->toArray()
                            ]);
                        }
                    }
                }
                
                if (Schema::hasTable('header_settings')) {
                    $headerSettings = HeaderSetting::all()->keyBy('key');
                    
                    // Process Arabic translations for header settings
                    if ($isArabic) {
                        foreach ($headerSettings as $key => $setting) {
                            // Check if there's an Arabic version of this setting
                            $arabicKey = $key . '_ar';
                            if (isset($headerSettings[$arabicKey]) && !empty(trim($headerSettings[$arabicKey]->value))) {
                                $headerSettings[$key]->value = $headerSettings[$arabicKey]->value;
                            }
                        }
                    }
                }
                
                $view->with([
                    'headerItems' => $headerItems,
                    'headerSettings' => $headerSettings,
                ]);
            } catch (\Exception $e) {
                // Log error but don't crash the app
                Log::error('Error loading header navigation: ' . $e->getMessage());
                $view->with([
                    'headerItems' => [],
                    'headerSettings' => [],
                ]);
            }
        });
        
        // Share footer navigation data with all views
        View::composer('layouts.footer', function ($view) {
            try {
                $footerSections = [];
                $footerSettings = [];
                $isArabic = is_rtl();
                
                // Only try to fetch data if the tables exist
                if (Schema::hasTable('footer_sections')) {
                    $footerSections = FooterSection::where('is_active', true)
                        ->orderBy('sort_order')
                        ->with('links')
                        ->get();
                    
                    // FooterSection already has a getLocalizedTitleAttribute accessor
                    // so we don't need to handle localization here
                }
                
                if (Schema::hasTable('footer_settings')) {
                    $footerSettings = FooterSetting::all()->keyBy('key');
                    
                    // Process Arabic translations for footer settings
                    if ($isArabic) {
                        foreach ($footerSettings as $key => $setting) {
                            // Check if there's an Arabic version of this setting
                            $arabicKey = $key . '_ar';
                            if (isset($footerSettings[$arabicKey]) && !empty(trim($footerSettings[$arabicKey]->value))) {
                                $footerSettings[$key]->value = $footerSettings[$arabicKey]->value;
                            }
                        }
                    }
                }
                
                $view->with([
                    'footerSections' => $footerSections,
                    'footerSettings' => $footerSettings,
                ]);
            } catch (\Exception $e) {
                // Log error but don't crash the app
                Log::error('Error loading footer navigation: ' . $e->getMessage());
                $view->with([
                    'footerSections' => [],
                    'footerSettings' => [],
                ]);
            }
        });
    }
}
