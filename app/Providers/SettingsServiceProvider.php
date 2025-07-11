<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use App\Models\CurrencyConfig;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\App;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('settings', function ($app) {
            return new \App\Services\Settings();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Skip loading settings during migrations
        if ($this->app->runningInConsole() && $this->isRunningMigrationCommand()) {
            return;
        }

        // Try to load settings from normalized tables first
        if (Schema::hasTable('general_settings')) {
            $this->loadNormalizedSettings();
        }
        // Fall back to old settings table if it exists
        else if (Schema::hasTable('settings')) {
            $this->loadSettings();
        }

        // Only set up event listeners if the settings table exists
        if (Schema::hasTable('settings')) {
            // Clear settings cache when settings are updated
            Setting::updated(function ($setting) {
                cache()->forget('setting_' . $setting->key);
            });
        }
    }

    /**
     * Check if the current command is a migration command
     */
    protected function isRunningMigrationCommand(): bool
    {
        if (!isset($_SERVER['argv'])) {
            return false;
        }

        $command = implode(' ', $_SERVER['argv']);
        return str_contains($command, 'migrate') || 
               str_contains($command, 'migration') || 
               str_contains($command, 'db:seed');
    }

    /**
     * Load settings from the old settings table and apply to config
     */
    protected function loadSettings(): void
    {
        // Load general settings
        $generalSettings = Cache::remember('general_settings', 600, function () {
            return Setting::where('group', 'general')->get();
        });

        // Apply general settings to config
        foreach ($generalSettings as $setting) {
            switch ($setting->key) {
                case 'site_name':
                    Config::set('app.name', $setting->value);
                    break;
                
                // Add other general settings that should affect config here
            }
        }
    }
    
    /**
     * Load settings from normalized tables and apply to config
     */
    protected function loadNormalizedSettings(): void
    {
        // Load general settings
        $generalSettings = Cache::remember('normalized_general_settings', 600, function () {
            return GeneralSetting::first();
        });

        if ($generalSettings) {
            // Apply general settings to config
            Config::set('app.name', $generalSettings->site_name);
            
            // Set available locales if language switcher is enabled
            if ($generalSettings->enable_language_switcher) {
                $languages = $generalSettings->available_languages ?? ['en', 'ar'];
                Config::set('app.available_locales', $languages);
                Config::set('app.locale', $generalSettings->default_language ?? 'en');
            }
        }
        
        // Load currency settings
        if (Schema::hasTable('currency_config')) {
            $currencyConfig = Cache::remember('normalized_currency_settings', 600, function () {
                return CurrencyConfig::first();
            });
            
            if ($currencyConfig) {
                // Apply currency settings to config if needed
                // This could be used in various parts of the application
                Config::set('app.currency', $currencyConfig->default_currency ?? null);
                Config::set('app.currency_symbol', $currencyConfig->symbol ?? '$');
            }
        }
    }
}
