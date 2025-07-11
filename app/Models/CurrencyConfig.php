<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyConfig extends Model
{
    use HasFactory;

    protected $table = 'currency_config';

    protected $fillable = [
        'default_currency',
        'currency_symbol',
        'currency_position',
        'thousand_separator',
        'decimal_separator',
        'decimal_digits',
        'supported_currencies',
    ];

    protected $casts = [
        'decimal_digits' => 'integer',
        'supported_currencies' => 'array',
    ];
    
    /**
     * Get the currency symbol.
     *
     * @return string
     */
    public static function getSymbol()
    {
        $config = self::first();
        return $config ? $config->currency_symbol : 'ج.م';
    }
    
    /**
     * Get the currency position.
     *
     * @return string
     */
    public static function getPosition()
    {
        $config = self::first();
        return $config ? $config->currency_position : 'right';
    }
    
    /**
     * Get the thousand separator.
     *
     * @return string
     */
    public static function getThousandSeparator()
    {
        $config = self::first();
        return $config ? $config->thousand_separator : ',';
    }
    
    /**
     * Get the decimal separator.
     *
     * @return string
     */
    public static function getDecimalSeparator()
    {
        $config = self::first();
        return $config ? $config->decimal_separator : '.';
    }
    
    /**
     * Get the number of decimal digits.
     *
     * @return int
     */
    public static function getDecimalDigits()
    {
        $config = self::first();
        return $config ? $config->decimal_digits : 2;
    }
    
    /**
     * Format a price with the correct currency symbol and format.
     *
     * @param float $price
     * @param bool $includeSymbol
     * @return string
     */
    public static function formatPrice($price, $includeSymbol = true)
    {
        // Use the centralized Currency helper
        return \App\Helpers\Currency::format($price, $includeSymbol);
    }
} 