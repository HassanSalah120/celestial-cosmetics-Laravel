<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class TranslationHelper
{
    /**
     * Get a translation for the given key.
     *
     * @param string $key The translation key
     * @param string $default Default value if translation is not found
     * @param array $replace Values to replace in the message
     * @param string|null $locale The locale to use
     * @return string
     */
    public static function get($key, $default = null, array $replace = [], $locale = null)
    {
        $locale = $locale ?: App::getLocale();
        $isArabic = $locale === 'ar';
        
        // Log for debugging
        Log::debug('TranslationHelper::get', [
            'key' => $key,
            'default' => $default,
            'locale' => $locale,
            'isArabic' => $isArabic
        ]);
        
        // Try to get the translation using Laravel's built-in function
        $translation = __($key, $replace, $locale);
        
        // If the translation key is the same as the result and we have a default, use the default
        if ($translation === $key && $default !== null) {
            // Try to get the translation for the default text
            $defaultTranslation = __($default, $replace, $locale);
            
            // If the default was translated, use that, otherwise use the default text
            return ($defaultTranslation !== $default) ? $defaultTranslation : $default;
        }
        
        return $translation;
    }
    
    /**
     * Load JSON translations for a specific locale
     *
     * @param string $locale
     * @return array
     */
    private static function loadJsonTranslations($locale)
    {
        $path = resource_path("lang/{$locale}.json");
        
        if (file_exists($path)) {
            $translations = json_decode(file_get_contents($path), true);
            return $translations ?: [];
        }
        
        return [];
    }
} 