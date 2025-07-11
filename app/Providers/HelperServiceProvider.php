<?php

namespace App\Providers;

use App\Helpers\SettingsHelper;
use App\Helpers\TranslationHelper;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use App\Facades\Settings;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Require helpers.php file
        require_once app_path('helpers.php');
        
        // Register the SettingsHelper as a singleton
        $this->app->singleton('settings', function ($app) {
            return new SettingsHelper();
        });
        
        // Register the TranslationHelper for use in views
        $this->app->singleton('translation_helper', function ($app) {
            return new TranslationHelper();
        });
        
        // Register the Settings facade
        if (class_exists('Illuminate\Foundation\AliasLoader')) {
            // For Laravel 11, register the alias directly
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Settings', Settings::class);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
