<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\CurrencyConfig;
use App\Models\GeneralSetting;
use App\Models\SeoDefaults;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;

class Settings
{
    /**
     * Check if we're currently running migrations
     */
    protected function isRunningMigrations(): bool
    {
        if (!App::runningInConsole()) {
            return false;
        }

        if (!isset($_SERVER['argv'])) {
            return false;
        }

        $command = implode(' ', $_SERVER['argv'] ?? []);
        return str_contains($command, 'migrate') || 
               str_contains($command, 'migration') || 
               str_contains($command, 'db:seed');
    }

    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        // Skip database checks during migrations
        if ($this->isRunningMigrations()) {
            return $default;
        }

        // Check if this is a currency setting
        if (in_array($key, ['currency_symbol', 'currency_position', 'default_currency', 'thousand_separator', 'decimal_separator', 'decimal_digits'])) {
            return $this->getCurrencySetting($key, $default);
        }
        
        // Check if this is a general setting
        if (in_array($key, ['site_name', 'site_logo', 'site_favicon', 'site_description', 'enable_language_switcher', 'available_languages', 'default_language'])) {
            return $this->getGeneralSetting($key, $default);
        }
        
        // Check if this is an SEO setting
        if (in_array($key, ['default_meta_title', 'default_meta_description', 'default_meta_keywords', 'og_default_image', 'enable_structured_data'])) {
            return $this->getSeoSetting($key, $default);
        }
        
        // Fall back to old settings table if it exists
        if (Schema::hasTable('settings')) {
            $locale = app()->getLocale();
            $cacheKey = "setting_{$key}_{$locale}";
    
            return Cache::rememberForever($cacheKey, function () use ($key, $default) {
                $setting = Setting::where('key', $key)->first();
                
                if (!$setting) {
                    return $default;
                }
    
                return $setting->value;
            });
        }
        
        return $default;
    }
    
    /**
     * Get a currency setting
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getCurrencySetting(string $key, mixed $default = null): mixed
    {
        // Use the mappings from the key to the property names in the Currency class
        $keyMethodMap = [
            'currency_symbol' => 'getSymbol',
            'currency_position' => 'getPosition',
            'thousand_separator' => 'getThousandSeparator',
            'decimal_separator' => 'getDecimalSeparator',
            'decimal_digits' => 'getDecimalDigits'
        ];
        
        // If there's a direct mapping, use the corresponding method from Currency
        if (isset($keyMethodMap[$key])) {
            $method = $keyMethodMap[$key];
            return \App\Helpers\Currency::$method();
        }
        
        // Fall back to the config object for other keys
        $config = \App\Helpers\Currency::getConfig();
        $normalizedKey = str_replace('currency_', '', $key);
        
        if (isset($config->$normalizedKey)) {
            return $config->$normalizedKey;
        }
        
        // If all else fails, return the default
        return $default;
    }
    
    /**
     * Get a general setting
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getGeneralSetting(string $key, mixed $default = null): mixed
    {
        if (Schema::hasTable('general_settings')) {
            $settings = Cache::remember('general_settings_obj', 600, function () {
                return GeneralSetting::first();
            });
            
            if ($settings) {
                // Map old keys to new keys if needed
                $keyMap = [
                    'site_name' => 'site_name',
                    'site_logo' => 'site_logo',
                    'site_favicon' => 'site_favicon',
                    'site_description' => 'site_description',
                    'enable_language_switcher' => 'enable_language_switcher',
                    'available_languages' => 'available_languages',
                    'default_language' => 'default_language'
                ];
                
                $normalizedKey = $keyMap[$key] ?? $key;
                
                if (isset($settings->$normalizedKey)) {
                    return $settings->$normalizedKey;
                }
            }
        }
        
        // Fall back to old settings
        if (Schema::hasTable('settings')) {
            $setting = Setting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        }
        
        return $default;
    }
    
    /**
     * Get an SEO setting
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getSeoSetting(string $key, mixed $default = null): mixed
    {
        if (Schema::hasTable('seo_defaults')) {
            $settings = Cache::remember('seo_defaults_obj', 600, function () {
                return SeoDefaults::first();
            });
            
            if ($settings) {
                // Map old keys to new keys if needed
                $keyMap = [
                    'default_meta_title' => 'default_meta_title',
                    'default_meta_description' => 'default_meta_description',
                    'default_meta_keywords' => 'default_meta_keywords',
                    'og_default_image' => 'og_default_image',
                    'enable_structured_data' => 'enable_structured_data'
                ];
                
                $normalizedKey = $keyMap[$key] ?? $key;
                
                if (isset($settings->$normalizedKey)) {
                    return $settings->$normalizedKey;
                }
            }
        }
        
        // Fall back to old settings
        if (Schema::hasTable('settings')) {
            $setting = Setting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        }
        
        return $default;
    }

    /**
     * Set a setting value
     *
     * @param string $key
     * @param mixed $value
     * @param string|null $group
     * @return bool
     */
    public function set(string $key, mixed $value, ?string $group = null): bool
    {
        // Try to set in normalized tables first
        $result = $this->setInNormalizedTable($key, $value);
        
        // Fall back to settings table if needed
        if (!$result && Schema::hasTable('settings')) {
            $setting = Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'group' => $group ?? 'general'
                ]
            );
            
            $locale = app()->getLocale();
            Cache::forget("setting_{$key}_{$locale}");
            
            $result = true;
        }
        
        return $result;
    }
    
    /**
     * Set a value in the normalized tables
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    protected function setInNormalizedTable(string $key, mixed $value): bool
    {
        // Currency settings
        if (in_array($key, ['currency_symbol', 'currency_position', 'default_currency', 'thousand_separator', 'decimal_separator', 'decimal_digits'])) {
            if (Schema::hasTable('currency_config')) {
                $config = CurrencyConfig::first();
                
                if ($config) {
                    $config->$key = $value;
                    $result = $config->save();
                    Cache::forget('currency_config');
                    return $result;
                }
            }
        }
        
        // General settings
        if (in_array($key, ['site_name', 'site_logo', 'site_favicon', 'site_description', 'enable_language_switcher', 'available_languages', 'default_language'])) {
            if (Schema::hasTable('general_settings')) {
                $settings = GeneralSetting::first();
                
                if ($settings) {
                    $settings->$key = $value;
                    $result = $settings->save();
                    Cache::forget('general_settings_obj');
                    return $result;
                }
            }
        }
        
        // SEO settings
        if (in_array($key, ['default_meta_title', 'default_meta_description', 'default_meta_keywords', 'og_default_image', 'enable_structured_data'])) {
            if (Schema::hasTable('seo_defaults')) {
                $settings = SeoDefaults::first();
                
                if ($settings) {
                    $settings->$key = $value;
                    $result = $settings->save();
                    Cache::forget('seo_defaults_obj');
                    return $result;
                }
            }
        }
        
        return false;
    }

    /**
     * Get all settings for a specific group
     *
     * @param string $group
     * @return Collection
     */
    public function getGroup(string $group): Collection
    {
        // For most accurate implementation, we should return normalized data
        // when possible, but for now we'll just check if the settings table exists
        if (Schema::hasTable('settings')) {
            $locale = app()->getLocale();
            $cacheKey = "settings_group_{$group}_{$locale}";
    
            return Cache::rememberForever($cacheKey, function () use ($group) {
                return Setting::where('group', $group)->get();
            });
        }
        
        return collect(); // Return empty collection if settings table doesn't exist
    }

    /**
     * Direct access to the general_settings table, bypassing caches
     *
     * @param string $key
     * @return mixed
     */
    public function getDirectFromGeneralSettings(string $key): mixed
    {
        if (Schema::hasTable('general_settings')) {
            $setting = GeneralSetting::first();
            if ($setting && isset($setting->$key)) {
                return $setting->$key;
            }
        }
        
        return null;
    }
} 