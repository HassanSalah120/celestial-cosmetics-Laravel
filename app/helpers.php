<?php

// Include the main helper functions file
require_once __DIR__ . '/Helpers/functions.php';

use App\Services\TranslationService;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use App\Helpers\Currency;

if (!function_exists('t')) {
    /**
     * Translate a string using the TranslationService
     *
     * @param string $key
     * @param array $replace
     * @param string|null $locale
     * @return string
     */
    function t(string $key, array $replace = [], ?string $locale = null): string
    {
        return app(TranslationService::class)->get($key, $replace, $locale);
    }
}

if (!function_exists('tns')) {
    /**
     * Get all translations for a namespace
     *
     * @param string $namespace
     * @param string|null $locale
     * @return array
     */
    function tns(string $namespace, ?string $locale = null): array
    {
        return app(TranslationService::class)->getNamespace($namespace, $locale);
    }
}

if (!function_exists('tjs')) {
    /**
     * Translate a string from JSON files
     *
     * @param string $key
     * @param array $replace
     * @param string|null $locale
     * @return string
     */
    function tjs(string $key, array $replace = [], ?string $locale = null): string
    {
        return app(TranslationService::class)->getJson($key, $replace, $locale);
    }
}

if (!function_exists('locale_direction')) {
    /**
     * Get the text direction for the current or specified locale
     *
     * @param string|null $locale
     * @return string
     */
    function locale_direction(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        return in_array($locale, ['ar']) ? 'rtl' : 'ltr';
    }
}

/**
 * Get a setting value by key.
 *
 * @param  string  $key
 * @param  mixed  $default
 * @param  string|null  $locale
 * @return mixed
 */
function setting($key, $default = null, $locale = null)
{
    // Since Setting::get() only accepts two parameters, we'll handle the locale differently
    $setting = Setting::where('key', $key)->first();
    
    if (!$setting) {
        return $default;
    }
    
    // If locale is specified, try to get the translated value
    if ($locale && method_exists($setting, 'getTranslatedValue')) {
        return $setting->getTranslatedValue($locale);
    }
    
    // Otherwise, use the default get method
    return Setting::get($key, $default);
}

/**
 * Get a homepage setting value by key with locale fallback.
 *
 * @param  string  $key
 * @param  mixed  $default
 * @return mixed
 */
function homepage_setting($key, $default = null)
{
    $locale = app()->getLocale();
    
    // Try to get the localized version first (e.g. homepage_hero_title_ar)
    $localizedKey = $key . '_' . $locale;
    $value = Setting::get($localizedKey);
    
    // If not found, fall back to the default version
    if ($value === null) {
        $value = Setting::get($key, $default);
    }
    
    return $value;
}

/**
 * Get all settings for a group.
 *
 * @param  string  $group
 * @return \Illuminate\Database\Eloquent\Collection
 */
function settings_group($group)
{
    return Setting::getGroup($group);
}

/**
 * Format a price with the current currency settings.
 *
 * @param  float  $price
 * @param  bool  $includeSymbol
 * @return string
 */
if (!function_exists('format_currency')) {
    function format_currency($price, $includeSymbol = true) {
        return Currency::format($price, $includeSymbol);
    }
}

// Example - we're just checking if the file exists and if it contains the is_rtl function 