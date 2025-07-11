<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class GlobalHeaderSetting extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'global_header_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'show_logo',
        'show_profile',
        'show_store_hours',
        'show_search',
        'show_cart',
        'show_language_switcher',
        'show_auth_links',
        'sticky_header',
        'header_style',
        'logo',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'show_logo' => 'boolean',
        'show_profile' => 'boolean',
        'show_store_hours' => 'boolean',
        'show_search' => 'boolean',
        'show_cart' => 'boolean',
        'show_language_switcher' => 'boolean',
        'show_auth_links' => 'boolean',
        'sticky_header' => 'boolean',
    ];

    /**
     * Get the global header settings
     *
     * @return self
     */
    public static function getSettings()
    {
        return Cache::remember('global_header_settings', 60 * 60, function () {
            return self::firstOrCreate([]);
        });
    }

    /**
     * Get a specific setting
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getSetting($key, $default = null)
    {
        $settings = self::getSettings();
        return $settings->$key ?? $default;
    }

    /**
     * Update a setting
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public static function updateSetting($key, $value)
    {
        $settings = self::getSettings();
        $settings->$key = $value;
        $result = $settings->save();
        
        // Clear the cache
        Cache::forget('global_header_settings');
        
        return $result;
    }

    /**
     * Update multiple settings
     *
     * @param array $data
     * @return bool
     */
    public static function updateSettings(array $data)
    {
        $settings = self::getSettings();
        $settings->fill($data);
        $result = $settings->save();
        
        // Clear the cache
        Cache::forget('global_header_settings');
        
        return $result;
    }
} 