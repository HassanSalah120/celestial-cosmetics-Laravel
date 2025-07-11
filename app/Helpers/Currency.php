<?php

namespace App\Helpers;

use App\Models\CurrencyConfig;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\App;

class Currency
{
    /**
     * Cache duration in seconds
     */
    const CACHE_DURATION = 600; // 10 minutes
    
    /**
     * Check if we're currently running migrations
     */
    protected static function isRunningMigrations(): bool
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
     * Get all currency settings at once
     * 
     * @return object
     */
    public static function getConfig()
    {
        // Skip database checks during migrations
        if (self::isRunningMigrations()) {
            return (object)[
                'symbol' => 'ج.م',
                'position' => 'right',
                'thousand_separator' => ',',
                'decimal_separator' => '.',
                'decimal_digits' => 2,
            ];
        }
        
        return Cache::remember('currency_config_settings', self::CACHE_DURATION, function () {
            // Try to get from CurrencyConfig table first
            if (Schema::hasTable('currency_config')) {
                $config = CurrencyConfig::first();
                if ($config) {
                    return (object)[
                        'symbol' => $config->symbol ?? 'ج.م',
                        'position' => $config->position ?? 'right',
                        'thousand_separator' => $config->thousand_separator ?? ',',
                        'decimal_separator' => $config->decimal_separator ?? '.',
                        'decimal_digits' => $config->decimal_digits ?? 2,
                    ];
                }
            }
            
            // Fallback to Settings table
            if (Schema::hasTable('settings')) {
                return (object)[
                    'symbol' => SettingsHelper::get('currency_symbol', 'ج.م'),
                    'position' => SettingsHelper::get('currency_position', 'right'),
                    'thousand_separator' => SettingsHelper::get('thousand_separator', ','),
                    'decimal_separator' => SettingsHelper::get('decimal_separator', '.'),
                    'decimal_digits' => (int)SettingsHelper::get('decimal_digits', 2),
                ];
            }
            
            // Default values if no tables exist
            return (object)[
                'symbol' => 'ج.م',
                'position' => 'right',
                'thousand_separator' => ',',
                'decimal_separator' => '.',
                'decimal_digits' => 2,
            ];
        });
    }
    
    /**
     * Format a price with the correct currency symbol and format
     * 
     * @param float $price The price to format
     * @param bool $includeSymbol Whether to include the currency symbol
     * @return string The formatted price
     */
    public static function format($price, $includeSymbol = true)
    {
        $config = self::getConfig();
        
        // Format the number with proper separators and decimal places
        $formattedNumber = number_format(
            (float)$price, 
            $config->decimal_digits, 
            $config->decimal_separator, 
            $config->thousand_separator
        );
        
        // Return just the number if symbol isn't needed
        if (!$includeSymbol) {
            return $formattedNumber;
        }
        
        // Add currency symbol based on position
        switch ($config->position) {
            case 'left':
                return $config->symbol . $formattedNumber;
            case 'right':
                return $formattedNumber . $config->symbol;
            case 'left_with_space':
                return $config->symbol . ' ' . $formattedNumber;
            case 'right_with_space':
                return $formattedNumber . ' ' . $config->symbol;
            default:
                return $formattedNumber . $config->symbol;
        }
    }
    
    /**
     * Get symbol
     * 
     * @return string
     */
    public static function getSymbol()
    {
        return self::getConfig()->symbol;
    }
    
    /**
     * Get position
     * 
     * @return string
     */
    public static function getPosition()
    {
        return self::getConfig()->position;
    }
    
    /**
     * Get thousand separator
     * 
     * @return string
     */
    public static function getThousandSeparator()
    {
        return self::getConfig()->thousand_separator;
    }
    
    /**
     * Get decimal separator
     * 
     * @return string
     */
    public static function getDecimalSeparator()
    {
        return self::getConfig()->decimal_separator;
    }
    
    /**
     * Get decimal digits
     * 
     * @return int
     */
    public static function getDecimalDigits()
    {
        return self::getConfig()->decimal_digits;
    }
} 