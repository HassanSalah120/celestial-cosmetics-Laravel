<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{
    /**
     * Available languages with their RTL status
     */
    protected $languages = [
        'en' => ['name' => 'English', 'rtl' => false],
        'ar' => ['name' => 'العربية', 'rtl' => true],
        'fr' => ['name' => 'Français', 'rtl' => false],
        'de' => ['name' => 'Deutsch', 'rtl' => false],
        'es' => ['name' => 'Español', 'rtl' => false],
    ];

    /**
     * Get available languages from settings
     *
     * @return array
     */
    protected function getAvailableLanguages()
    {
        try {
            // Try to get available languages from settings
            $availableLanguages = \App\Facades\Settings::get('available_languages');
            
            // Handle different formats
            if (is_string($availableLanguages)) {
                $decoded = json_decode($availableLanguages, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $availableLanguages = $decoded;
                } else {
                    return array_keys($this->languages);
                }
            } elseif (!is_array($availableLanguages)) {
                return array_keys($this->languages);
            }
            
            // If it's a sequential array of language codes
            if (isset($availableLanguages[0])) {
                return $availableLanguages;
            }
            
            // If it's an associative array with language codes as keys
            return array_keys($availableLanguages);
        } catch (\Exception $e) {
            Log::error('Error getting available languages from settings', [
                'error' => $e->getMessage()
            ]);
            return array_keys($this->languages);
        }
    }

    /**
     * Switch the application language
     *
     * @param Request $request
     * @param string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchLanguage(Request $request, $locale)
    {
        // Get available locales from settings
        $availableLocales = $this->getAvailableLanguages();
        
        // Validate the requested locale
        if (!in_array($locale, $availableLocales)) {
            Log::warning('Invalid locale requested', ['requested' => $locale, 'available' => $availableLocales]);
            $locale = config('app.locale', 'en');
        }
        
        // Set the application locale
        App::setLocale($locale);
        
        // Store in session
        Session::put('locale', $locale);
        
        // Set text direction
        $isRtl = $this->languages[$locale]['rtl'] ?? false;
        $direction = $isRtl ? 'rtl' : 'ltr';
        Session::put('text_direction', $direction);
        
        // Set cookie (30 days)
        Cookie::queue('locale', $locale, 60 * 24 * 30);
        
        Log::info('Language switched successfully', [
            'locale' => $locale,
            'direction' => $direction,
        ]);
        
        // Redirect back or to home
        $redirect = $request->input('redirect') ? urldecode($request->input('redirect')) : null;
        if ($redirect) {
            return redirect($redirect)->withCookie(Cookie::make('locale', $locale, 60*24*30));
        }
        
        return redirect()->back()->withCookie(Cookie::make('locale', $locale, 60*24*30));
    }
    
    /**
     * Simple direct language switch that bypasses other mechanisms
     * 
     * @param Request $request
     * @param string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function directSwitch(Request $request, $locale)
    {
        // Only allow 'en' and 'ar'
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en';
        }
        
        // FORCE set application locale in ALL possible ways
        App::setLocale($locale);
        config(['app.locale' => $locale]);
        app()->setLocale($locale);
        
        // Set session values with force flag
        Session::put('locale', $locale);
        Session::save(); // Force save session immediately
        
        // Set direction
        $direction = $locale === 'ar' ? 'rtl' : 'ltr';
        Session::put('text_direction', $direction);
        
        // Set cookie with longer duration and HTTP only false to ensure it's accessible
        $cookie = Cookie::make('locale', $locale, 60 * 24 * 30, null, null, false, false);
        
        // Log this direct switch with high visibility
        Log::alert('DIRECT LANGUAGE SWITCH EXECUTED', [
            'locale' => $locale,
            'direction' => $direction,
            'app_locale_after_setting' => App::getLocale(),
            'config_locale_after_setting' => config('app.locale')
        ]);
        
        // Redirect to homepage with timestamp and explicit locale parameter to bust cache
        return redirect('/?ts=' . time() . '&explicit_locale=' . $locale)
            ->withCookie($cookie)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    /**
     * Debug language settings
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function debugLanguage(Request $request)
    {
        // Force set the application locale to match session
        if (Session::has('locale')) {
            $sessionLocale = Session::get('locale');
            App::setLocale($sessionLocale);
            app()->setLocale($sessionLocale);
            config(['app.locale' => $sessionLocale]);
            
            Log::alert('Debug page forced locale', [
                'from_session' => $sessionLocale
            ]);
        }
        
        // Get homepage hero settings for debugging
        $homepageHero = \App\Facades\Settings::get('homepage_hero');
        if (is_string($homepageHero)) {
            $homepageHero = json_decode($homepageHero, true);
        }
        
        // Get text settings for debugging
        $textSettings = [];
        $isArabic = App::getLocale() === 'ar';
        
        // Base text settings with automatic language selection
        $settingsKeys = [
            'featured_products_title',
            'featured_products_description',
            'new_arrivals_title',
            'new_arrivals_tag',
            'new_arrivals_description',
            'shop_by_category_title',
            'shop_by_category_description',
            'testimonials_title',
            'testimonials_description',
            'view_all_products_text',
            'explore_new_arrivals_text',
            'offers_title',
            'offers_description',
        ];
        
        foreach ($settingsKeys as $key) {
            $arKey = "{$key}_ar";
            $enKey = "homepage_{$key}";
            $arFullKey = "homepage_{$arKey}";
            
            // Get both English and Arabic values
            $enValue = \App\Facades\Settings::get($enKey);
            $arValue = \App\Facades\Settings::get($arFullKey);
            
            // Determine which value is active based on current locale
            $activeValue = $isArabic && !empty($arValue) ? $arValue : $enValue;
            
            $textSettings[$key] = [
                'english' => $enValue,
                'arabic' => $arValue,
                'active' => $activeValue,
                'is_arabic_available' => !empty($arValue)
            ];
        }
        
        $data = [
            'app_locale' => App::getLocale(),
            'session_locale' => Session::get('locale'),
            'config_locale' => config('app.locale'),
            'app_instance_locale' => app()->getLocale(),
            'cookie_locale' => $request->cookie('locale'),
            'dir_attribute' => Session::get('text_direction', 'ltr'),
            'translation_test_json' => __('Welcome to our store'),
            'translation_test_php' => __('messages.welcome'),
            'homepage_hero' => $homepageHero,
            'text_settings' => $textSettings,
            'is_rtl_function' => is_rtl(),
            'all_session' => Session::all(),
            'all_cookies' => $request->cookies->all()
        ];
        
        return response()->json($data);
    }

    /**
     * Detailed debug view with all settings
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function detailedDebug(Request $request)
    {
        // Force set the application locale to match session
        if (Session::has('locale')) {
            $sessionLocale = Session::get('locale');
            App::setLocale($sessionLocale);
            app()->setLocale($sessionLocale);
            config(['app.locale' => $sessionLocale]);
        }
        
        // Get homepage hero settings for debugging
        $homepageHero = \App\Facades\Settings::get('homepage_hero');
        if (is_string($homepageHero)) {
            $homepageHero = json_decode($homepageHero, true);
        }
        
        // Get all settings from Settings table
        $allSettings = \App\Models\Setting::all();
        
        // Get Arabic settings by filtering key
        $arabicSettings = $allSettings->filter(function($setting) {
            return str_ends_with($setting->key, '_ar');
        });
        
        // Get all homepage settings
        $homepageSettings = $allSettings->filter(function($setting) {
            return str_starts_with($setting->key, 'homepage_');
        });
        
        // Get current isArabic value
        $isArabic = App::getLocale() === 'ar';
        
        $data = [
            'app_locale' => App::getLocale(),
            'session_locale' => Session::get('locale'),
            'config_locale' => config('app.locale'),
            'app_instance_locale' => app()->getLocale(),
            'available_locales' => config('app.available_locales'),
            'cookie_locale' => $request->cookie('locale'),
            'request_locale_param' => $request->input('locale'),
            'url_path' => $request->path(),
            'url_full' => $request->fullUrl(),
            'all_session' => Session::all(),
            'all_cookies' => $request->cookies->all(),
            'dir_attribute' => Session::get('text_direction', 'ltr'),
            'translation_test_json' => __('Welcome to our store'),
            'translation_test_php' => __('messages.welcome'),
            'homepage_hero' => $homepageHero,
            'is_rtl_function' => is_rtl(),
            'arabic_locale_active' => $isArabic
        ];
        
        return view('debug.detailed', [
            'data' => $data,
            'allSettings' => $allSettings,
            'arabicSettings' => $arabicSettings,
            'homepageSettings' => $homepageSettings
        ]);
    }

    /**
     * Display the homepage settings for translation debugging
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function homepageSettings(Request $request)
    {
        // Force set the application locale to match session
        if (Session::has('locale')) {
            $sessionLocale = Session::get('locale');
            App::setLocale($sessionLocale);
            app()->setLocale($sessionLocale);
            config(['app.locale' => $sessionLocale]);
        }
        
        // Get all settings that start with homepage_
        $homepageSettings = \App\Models\Setting::where('key', 'like', 'homepage_%')
            ->orderBy('key')
            ->get();
            
        // Group them by base key (without _ar suffix)
        $groupedSettings = [];
        
        foreach ($homepageSettings as $setting) {
            $key = $setting->key;
            $isArabic = str_ends_with($key, '_ar');
            $baseKey = $isArabic ? substr($key, 0, -3) : $key;
            
            if (!isset($groupedSettings[$baseKey])) {
                $groupedSettings[$baseKey] = [
                    'key' => $baseKey,
                    'en_value' => null,
                    'ar_value' => null,
                    'active_value' => null,
                    'group' => $setting->group,
                    'type' => $setting->type
                ];
            }
            
            if ($isArabic) {
                $groupedSettings[$baseKey]['ar_value'] = $setting->value;
            } else {
                $groupedSettings[$baseKey]['en_value'] = $setting->value;
            }
        }
        
        // Determine active value based on current locale
        $isArabic = app()->getLocale() === 'ar';
        foreach ($groupedSettings as $key => $data) {
            $groupedSettings[$key]['active_value'] = $isArabic && !empty($data['ar_value']) 
                ? $data['ar_value'] 
                : $data['en_value'];
        }
        
        $isArabic = app()->getLocale() === 'ar';
        $data = [
            'app_locale' => App::getLocale(),
            'session_locale' => Session::get('locale'),
            'is_rtl_function' => is_rtl(),
            'is_arabic' => $isArabic,
            'settings' => $groupedSettings
        ];
        
        return view('debug.homepage_settings', [
            'data' => $data,
            'settings' => $groupedSettings
        ]);
    }
} 