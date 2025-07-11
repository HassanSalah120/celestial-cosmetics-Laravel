<?php

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Lang;

class TranslationService
{
    /**
     * Cache duration in minutes
     * 
     * @var int
     */
    protected $cacheDuration = 60;

    /**
     * Get a translation with fallback and caching
     *
     * @param string $key
     * @param array $replace
     * @param string|null $locale
     * @return string
     */
    public function get(string $key, array $replace = [], string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        $cacheKey = "translations.{$locale}.{$key}";
        
        return Cache::remember($cacheKey, now()->addMinutes($this->cacheDuration), function () use ($key, $replace, $locale) {
            $translation = __($key, $replace, $locale);
            
            // If the translation is the same as the key, it may be missing
            if ($translation === $key && strpos($key, '.') !== false) {
                $this->logMissingTranslation($key, $locale);
            }
            
            return $translation;
        });
    }

    /**
     * Get all translations for a specific locale
     *
     * @param string $locale
     * @return array
     */
    public function getAllTranslations(string $locale): array
    {
        $cacheKey = "translations.{$locale}";

        // Try to get from cache first
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $translations = [];
        $path = resource_path("lang/{$locale}");
        
        // Load PHP files
        if (is_dir($path)) {
            foreach (glob($path . '/*.php') as $file) {
                $key = basename($file, '.php');
                $translations[$key] = require $file;
            }
        }
        
        // Load JSON translations
        $jsonPath = resource_path("lang/{$locale}.json");
        if (File::exists($jsonPath)) {
            $jsonTranslations = json_decode(File::get($jsonPath), true);
            if ($jsonTranslations) {
                $translations['json'] = $jsonTranslations;
            }
        }

        // Cache the result
        Cache::put($cacheKey, $translations, now()->addMinutes($this->cacheDuration));

        return $translations;
    }

    /**
     * Clear translation cache
     *
     * @param string|null $locale
     * @return void
     */
    public function clearCache(?string $locale = null): void
    {
        if ($locale) {
            Cache::forget("translations.{$locale}");
        } else {
            foreach (config('app.available_locales', ['en']) as $loc) {
                Cache::forget("translations.{$loc}");
            }
        }
    }

    /**
     * Get missing translations
     *
     * @param string $locale
     * @return array
     */
    public function getMissingTranslations(string $locale): array
    {
        $fallbackLocale = config('app.fallback_locale', 'en');
        $fallbackTranslations = $this->getAllTranslations($fallbackLocale);
        $currentTranslations = $this->getAllTranslations($locale);
        
        $missing = [];
        foreach ($fallbackTranslations as $file => $translations) {
            if (!isset($currentTranslations[$file])) {
                $missing[$file] = $translations;
            } else {
                foreach ($translations as $key => $value) {
                    if (!isset($currentTranslations[$file][$key])) {
                        $missing[$file][$key] = $value;
                    }
                }
            }
        }
        
        return $missing;
    }

    /**
     * Get all translations for a namespace in current locale
     * 
     * @param string $namespace The translation namespace (e.g., 'common', 'navigation')
     * @param string|null $locale The locale to use (defaults to current locale)
     * @return array
     */
    public function getNamespace(string $namespace, string $locale = null): array
    {
        $locale = $locale ?? App::getLocale();
        $cacheKey = "translations.{$locale}.namespace.{$namespace}";
        
        return Cache::remember($cacheKey, now()->addMinutes($this->cacheDuration), function () use ($namespace, $locale) {
            $path = resource_path("lang/{$locale}/{$namespace}.php");
            
            if (File::exists($path)) {
                return include $path;
            }
            
            // Fallback to default locale
            $fallbackLocale = config('app.fallback_locale', 'en');
            $fallbackPath = resource_path("lang/{$fallbackLocale}/{$namespace}.php");
            
            if (File::exists($fallbackPath)) {
                $this->logMissingNamespace($namespace, $locale);
                return include $fallbackPath;
            }
            
            return [];
        });
    }
    
    /**
     * Get a translation from JSON files
     * 
     * @param string $key The translation key
     * @param array $replace Values to replace placeholders
     * @param string|null $locale The locale to use (defaults to current locale)
     * @return string
     */
    public function getJson(string $key, array $replace = [], string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        $cacheKey = "translations.{$locale}.json.{$key}";
        
        return Cache::remember($cacheKey, now()->addMinutes($this->cacheDuration), function () use ($key, $replace, $locale) {
            $translation = trans($key, $replace, $locale);
            
            // If the translation is the same as the key, it may be missing
            if ($translation === $key) {
                $this->logMissingJsonTranslation($key, $locale);
            }
            
            return $translation;
        });
    }
    
    /**
     * Log a missing translation
     * 
     * @param string $key The translation key
     * @param string $locale The locale
     * @return void
     */
    protected function logMissingTranslation(string $key, string $locale): void
    {
        Log::channel('translations')->warning("Missing translation: {$key} for locale: {$locale}");
    }
    
    /**
     * Log a missing namespace
     * 
     * @param string $namespace The namespace
     * @param string $locale The locale
     * @return void
     */
    protected function logMissingNamespace(string $namespace, string $locale): void
    {
        Log::channel('translations')->warning("Missing namespace: {$namespace} for locale: {$locale}");
    }
    
    /**
     * Log a missing JSON translation
     * 
     * @param string $key The translation key
     * @param string $locale The locale
     * @return void
     */
    protected function logMissingJsonTranslation(string $key, string $locale): void
    {
        Log::channel('translations')->warning("Missing JSON translation: {$key} for locale: {$locale}");
    }
} 