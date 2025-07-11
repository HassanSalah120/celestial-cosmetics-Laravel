<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use App\Facades\Settings;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Share settings with all views
        View::composer('*', function ($view) {
            // This creates a settings array that can also be called as a function
            $settingsData = new class implements \ArrayAccess {
                // Store cached settings to avoid repeated database calls
                private $cache = [];
                
                // Allow array access: $settings['key']
                public function __get($key) {
                    if (!isset($this->cache[$key])) {
                        $this->cache[$key] = Settings::get($key);
                    }
                    return $this->cache[$key];
                }
                
                // Also allow function call: $settings('key', 'default')
                public function __invoke($key, $default = null) {
                    if (!isset($this->cache[$key])) {
                        $this->cache[$key] = Settings::get($key, $default);
                    }
                    return $this->cache[$key];
                }
                
                // Allow isset checks: isset($settings['key'])
                public function __isset($key) {
                    if (!isset($this->cache[$key])) {
                        $this->cache[$key] = Settings::get($key);
                    }
                    return $this->cache[$key] !== null;
                }
                
                // Allow array access explicitly
                public function offsetExists($offset): bool {
                    return $this->__isset($offset);
                }
                
                public function offsetGet($offset): mixed {
                    return $this->__get($offset);
                }
                
                public function offsetSet($offset, $value): void {
                    $this->cache[$offset] = $value;
                }
                
                public function offsetUnset($offset): void {
                    unset($this->cache[$offset]);
                }
            };
            
            $view->with('settings', $settingsData);
        });

        // Debug info - share with all views
        View::composer('*', function ($view) {
            $view->with('debug', [
                'route' => Route::currentRouteName(),
                'path' => request()->path(),
                'url' => url()->current(),
                'time' => now()->format('Y-m-d H:i:s'),
            ]);
        });
    }
} 