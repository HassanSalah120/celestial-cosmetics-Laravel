<?php

namespace App\Helpers;

use App\Models\Setting;
use App\Models\CurrencyConfig;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use App\Helpers\Currency;

class SettingsHelper
{
    /**
     * Get a setting value by key with optional default
     * 
     * @param string $key The setting key
     * @param mixed $default Default value if setting doesn't exist
     * @return mixed The setting value or default
     */
    public static function get($key, $default = null)
    {
        // Check if we're looking for a currency setting
        if (in_array($key, ['currency_symbol', 'currency_position', 'thousand_separator', 'decimal_separator', 'decimal_digits'])) {
            return self::getCurrencySetting($key, $default);
        }
        
        // Check shipping settings first in normalized tables when appropriate
        if (strpos($key, 'shipping_') === 0) {
            $normalizedValue = self::getFromNormalizedTables($key, null);
            if ($normalizedValue !== null) {
                return $normalizedValue;
            }
        }
        
        // Check if settings table exists before querying
        if (!Schema::hasTable('settings')) {
            return self::getFallbackValue($key, $default);
        }
        
        try {
            return Setting::get($key, $default);
        } catch (\Exception $e) {
            // If there's a database error, return the fallback value
            return self::getFallbackValue($key, $default);
        }
    }
    
    /**
     * Get a currency setting value
     * 
     * @param string $key The setting key
     * @param mixed $default Default value
     * @return mixed The setting value or default
     */
    private static function getCurrencySetting($key, $default = null)
    {
        // First try to get from CurrencyConfig
        if (Schema::hasTable('currency_config')) {
            $config = CurrencyConfig::first();
            if ($config && isset($config->$key)) {
                return $config->$key;
            }
        }
        
        // If that fails and settings table exists, try the old way
        if (Schema::hasTable('settings')) {
            return Setting::get($key, $default);
        }
        
        // Otherwise return default values
        $defaults = [
            'currency_symbol' => 'ج.م',
            'currency_position' => 'right',
            'thousand_separator' => ',',
            'decimal_separator' => '.',
            'decimal_digits' => 2,
        ];
        
        return $defaults[$key] ?? $default;
    }
    
    /**
     * Get shipping settings from normalized tables
     * 
     * @param string $key The setting key
     * @param mixed $fallback Fallback value if not found
     * @return mixed
     */
    private static function getFromNormalizedTables($key, $fallback = null)
    {
        try {
            // Check if shipping_configs table exists
            if (!Schema::hasTable('shipping_configs')) {
                return $fallback;
            }
            
            // Try to get the value from normalized tables
            $config = \App\Models\ShippingConfig::first();
            
            if (!$config) {
                return $fallback;
            }
            
            // Map old keys to new model properties
            $mappings = [
                'shipping_default_fee' => 'shipping_flat_rate',
                'shipping_free_threshold' => 'free_shipping_min',
                'shipping_enable_free' => 'enable_shipping',
                'shipping_enable_local_pickup' => 'enable_local_pickup',
                'local_pickup_cost' => 'local_pickup_cost',
                'pickup_address' => 'pickup_address',
                'pickup_instructions' => 'pickup_instructions',
            ];
            
            if (isset($mappings[$key]) && isset($config->{$mappings[$key]})) {
                return $config->{$mappings[$key]};
            }
            
            // Special handling for shipping methods
            if ($key === 'shipping_methods' && Schema::hasTable('shipping_methods')) {
                $methods = \App\Models\ShippingMethod::where('is_active', true)
                    ->orderBy('sort_order')
                    ->get()
                    ->toArray();
                    
                // Return the methods array directly since the ShippingService now handles both arrays and JSON strings
                return $methods;
            }
            
            return $fallback;
        } catch (\Exception $e) {
            return $fallback;
        }
    }
    
    /**
     * Get a fallback value for a setting when the setting table doesn't exist
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    private static function getFallbackValue($key, $default)
    {
        // Initialize variables with defaults
        $currencySymbol = 'ج.م';
        $currency = 'EGP';
        
        // Try to get currency settings from database first
        try {
            if (Schema::hasTable('currency_config')) {
                $currencyConfig = \App\Models\CurrencyConfig::first();
                if ($currencyConfig) {
                    $currencySymbol = $currencyConfig->currency_symbol;
                    $currency = $currencyConfig->currency ?? 'EGP';
                }
            }
        } catch (\Exception $e) {
            // Silently handle errors
        }
        
        // Common defaults for critical settings
        $defaults = [
            // Shipping defaults
            'shipping_default_fee' => 10.00,
            'shipping_free_threshold' => 50.00,
            'shipping_enable_free' => true,
            'shipping_international_fee' => 30.00,
            'shipping_methods' => json_encode([
                [
                    'name' => 'Standard Shipping',
                    'code' => 'standard',
                    'fee' => 10.00,
                    'estimated_days' => '3-5',
                    'is_active' => true
                ]
                // Removed Express and Free shipping options
            ]),
            
            // Payment defaults
            'cod_fee' => 20.00,
            'enable_cash_on_delivery' => true,
            'currency' => $currency,
            'currency_symbol' => $currencySymbol,
            
            // Default country
            'default_country' => 'EG',
            'shipping_countries' => 'US, EG, UK, CA, AU',
        ];
        
        return $defaults[$key] ?? $default;
    }
    
    /**
     * Get all settings in a group
     * 
     * @param string $group The settings group name
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getGroup($group)
    {
        // Check if settings table exists before querying
        if (!Schema::hasTable('settings')) {
            return collect(); // Return empty collection
        }
        
        return Cache::remember('settings_group_'.$group, 600, function () use ($group) {
            return Setting::where('group', $group)->get();
        });
    }
    
    /**
     * Get all settings as a key => value array
     * 
     * @return array
     */
    public static function getAll()
    {
        // Check if settings table exists before querying
        if (!Schema::hasTable('settings')) {
            return []; // Return empty array
        }
        
        return Cache::remember('settings_all', 600, function () {
            return Setting::getAll();
        });
    }
    
    /**
     * Update a setting value
     * 
     * @param string $key The setting key
     * @param mixed $value The new value
     * @param string $group Optional group name
     * @return bool Success status
     */
    public static function set($key, $value, $group = 'general')
    {
        // Check if we're setting a currency setting
        if (in_array($key, ['currency_symbol', 'currency_position', 'thousand_separator', 'decimal_separator', 'decimal_digits'])) {
            return self::setCurrencySetting($key, $value);
        }
        
        // Check if settings table exists before updating
        if (!Schema::hasTable('settings')) {
            return false;
        }
        
        $result = Setting::set($key, $value, $group);
        
        // Clear the relevant caches
        if ($result) {
            Cache::forget('setting_'.$key);
            Cache::forget('settings_group_'.$group);
            Cache::forget('settings_all');
        }
        
        return $result;
    }
    
    /**
     * Set a currency setting value
     * 
     * @param string $key The setting key
     * @param mixed $value The new value
     * @return bool Success status
     */
    private static function setCurrencySetting($key, $value)
    {
        // First try to update in CurrencyConfig
        if (Schema::hasTable('currency_config')) {
            $config = CurrencyConfig::first();
            if ($config) {
                $config->$key = $value;
                return $config->save();
            }
        }
        
        // If that fails and settings table exists, try the old way
        if (Schema::hasTable('settings')) {
            return Setting::set($key, $value, 'payment');
        }
        
        return false;
    }
    
    /**
     * Format a price with the correct currency symbol and format
     * 
     * @param float $price The price to format
     * @param bool $includeSymbol Whether to include the currency symbol
     * @return string The formatted price
     */
    public static function formatPrice($price, $includeSymbol = true)
    {
        // Use the new centralized Currency helper
        return Currency::format($price, $includeSymbol);
    }
} 