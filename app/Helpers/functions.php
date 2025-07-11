<?php

/**
 * Determine if the current locale is RTL.
 *
 * @return bool
 */
if (!function_exists('is_rtl')) {
    function is_rtl() {
        // First check session locale
        if (\Illuminate\Support\Facades\Session::has('locale')) {
            $sessionLocale = \Illuminate\Support\Facades\Session::get('locale');
            if (in_array($sessionLocale, config('app.rtl_locales', ['ar']))) {
                return true;
            }
        }
        
        // Then check app locale as fallback
        return in_array(app()->getLocale(), config('app.rtl_locales', ['ar']));
    }
}

/**
 * Get the application's current locale.
 *
 * @return string
 */
if (!function_exists('current_locale')) {
    function current_locale() {
        return app()->getLocale();
    }
}

/**
 * Format a price with the current currency symbol.
 *
 * @param  float  $price
 * @param  string|null  $currencySymbol
 * @return string
 */
if (!function_exists('format_price')) {
    function format_price($price, $currencySymbol = null) {
        $symbol = $currencySymbol ?? config('app.currency_symbol', 'EGP');
        return $symbol . number_format($price, 2);
    }
}

/**
 * Determine if the current theme is dark mode.
 *
 * @return bool
 */
if (!function_exists('is_dark_mode')) {
    function is_dark_mode() {
        return session('theme', 'light') === 'dark';
    }
}

/**
 * Safely get a value from settings with a fallback default value.
 *
 * @param  string  $key
 * @param  mixed  $default
 * @return mixed
 */
if (!function_exists('settings')) {
    function settings($key, $default = null) {
        try {
            return \App\Models\Setting::get($key, $default);
        } catch (\Exception $e) {
            return $default;
        }
    }
}

/**
 * Force assets to be loaded over HTTPS.
 *
 * @param  string  $path
 * @return string
 */
if (!function_exists('force_https')) {
    function force_https($url) {
        if (!$url) return $url;
        
        // Check if the URL already starts with https
        if (strpos($url, 'https://') === 0) {
            return $url;
        }
        
        // If the URL starts with http://, replace with https://
        if (strpos($url, 'http://') === 0) {
            return str_replace('http://', 'https://', $url);
        }
        
        // If it's a relative URL, keep it as is
        return $url;
    }
} 