<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\GlobalHeaderSetting;
use App\Facades\Settings;

class HeaderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register a macro for the Settings facade if the method exists
        if (method_exists(Settings::getFacadeRoot(), 'macro')) {
            Settings::macro('getHeaderSetting', function ($key, $default = null) {
                return GlobalHeaderSetting::getSetting($key, $default);
            });
        }
    }
} 