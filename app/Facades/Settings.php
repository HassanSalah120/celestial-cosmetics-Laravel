<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Services\SettingsService;

/**
 * @method static mixed get(string $key, mixed $default = null)
 * @method static bool set(string $key, mixed $value)
 * @method static bool has(string $key)
 * @method static array all()
 * @method static bool remove(string $key)
 * @method static void flushCache()
 * @method static mixed getDirectFromGeneralSettings(string $key)
 * 
 * @see \App\Services\SettingsService
 */
class Settings extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'settings';
    }
} 