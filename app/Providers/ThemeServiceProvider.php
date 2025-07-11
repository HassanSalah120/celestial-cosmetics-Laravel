<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Theme;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;

class ThemeServiceProvider extends ServiceProvider
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
        if (Schema::hasTable('themes')) {
            View::composer(['layouts.app', 'layouts.admin'], function ($view) {
                // Use a short cache time to ensure theme changes are reflected quickly
                $themeColors = Cache::remember('active_theme_colors', 60, function () {
                    $activeTheme = Theme::where('is_active', true)->first();
                    return $activeTheme ? $activeTheme->colors : [];
                });
                
                $view->with('themeColors', $themeColors);
            });
        }
    }
}
