<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Helpers\SettingsHelper;
use App\Models\Settings;
use App\Helpers\TranslationHelper;
use App\Helpers\TimeHelper;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Settings Service
        $this->app->singleton('settings', function () {
            return new SettingsService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set debug mode based on environment
        if (config('app.env') === 'production') {
            config(['app.debug' => false]);
        }
        
        // Force HTTPS in all environments
        URL::forceScheme('https');
        
        // Get locale information
        $locale = Session::get('locale', Config::get('app.locale', 'en'));
        $availableLocales = Config::get('app.available_locales', ['en']);
        $requestLocale = Request::get('locale_change', null);
        $cookieLocale = Request::cookie('locale', null);
        
        // Verify language files exist
        $localeExists = function($locale) {
            $path = resource_path("lang/{$locale}");
            $messagesPath = resource_path("lang/{$locale}/messages.php");
            return file_exists($path) && file_exists($messagesPath);
        };
        
        // Map of which locales have valid files
        $localeFilesExist = [];
        foreach ($availableLocales as $checkLocale) {
            $localeFilesExist[$checkLocale] = $localeExists($checkLocale);
        }
        
        // Priority 1: URL parameter (highest priority)
        if ($requestLocale && in_array($requestLocale, $availableLocales) && $localeFilesExist[$requestLocale]) {
            $locale = $requestLocale;
        } 
        // Priority 2: Session
        else if (Session::has('locale') && in_array(Session::get('locale'), $availableLocales) && $localeFilesExist[Session::get('locale')]) {
            $locale = Session::get('locale');
        } 
        // Priority 3: Cookie
        else if ($cookieLocale && in_array($cookieLocale, $availableLocales) && $localeFilesExist[$cookieLocale]) {
            $locale = $cookieLocale;
        } 
        // Priority 4: Default from config
        else {
            $locale = Config::get('app.locale', 'en');
        }
        
        // Apply the locale in all places
        App::setLocale($locale);
        Config::set('app.locale', $locale);
        Lang::setLocale($locale);
        Session::put('locale', $locale);
        
        // Set text direction
        $rtlLanguages = ['ar']; // Arabic
        $direction = in_array($locale, $rtlLanguages) ? 'rtl' : 'ltr';
        Session::put('text_direction', $direction);
        
        // Double check that the application locale matches what we want
        if (App::getLocale() !== $locale) {
            // Try one more time
            App::setLocale($locale);
        }

        // Force set locale from session on every request
        $this->app->rebinding('request', function () {
            if (Session::has('locale')) {
                $locale = Session::get('locale');
                App::setLocale($locale);
                config(['app.locale' => $locale]);
                
                // Log application locale being set
                Log::debug('AppServiceProvider set locale', [
                    'locale' => $locale,
                    'app_locale' => App::getLocale(),
                    'config_locale' => config('app.locale')
                ]);
            }
        });

        // Add this code to register a new Blade directive for currency formatting
        if (class_exists(\Illuminate\Support\Facades\Blade::class)) {
            \Illuminate\Support\Facades\Blade::directive('currency', function ($expression) {
                return "<?php echo \App\Helpers\Currency::format($expression); ?>";
            });
        }

        // Fix for MySQL < 5.7.7 and MariaDB < 10.2.2
        Schema::defaultStringLength(191);
        
        // Make Settings helper available in all views
        View::share('Settings', new SettingsHelper());
        
        // Make TranslationHelper available in all views
        View::share('TranslationHelper', new TranslationHelper());
        
        // Make TimeHelper available in all views
        View::share('TimeHelper', new TimeHelper());
        
        // Check if we need to override the locale from session
        if (Session::has('locale')) {
            $sessionLocale = Session::get('locale');
            
            // Make sure APP_LOCALE is not overriding our session setting
            config(['app.locale' => $sessionLocale]);
            App::setLocale($sessionLocale);
            app()->setLocale($sessionLocale);
            
            Log::debug('AppServiceProvider overrode app.locale from session', [
                'session_locale' => $sessionLocale,
                'app_locale_after' => App::getLocale()
            ]);
        }
        
        // Add is_rtl helper function
        if (!function_exists('is_rtl')) {
            function is_rtl() {
                // FIRST check session data directly (most reliable)
                if (\Illuminate\Support\Facades\Session::has('locale')) {
                    $sessionLocale = \Illuminate\Support\Facades\Session::get('locale');
                    return $sessionLocale === 'ar';
                }
                
                // SECOND check app locale as fallback
                $currentLocale = app()->getLocale();
                $isRtl = ($currentLocale === 'ar');
                
                \Illuminate\Support\Facades\Log::debug('is_rtl() function called', [
                    'current_locale' => $currentLocale,
                    'session_locale' => \Illuminate\Support\Facades\Session::get('locale', 'not set'),
                    'is_rtl_result' => $isRtl
                ]);
                
                return $isRtl;
            }
        }
        
        // Create a new Blade directive for improved translation
        Blade::directive('t', function ($expression) {
            return "<?php echo \\App\\Helpers\\TranslationHelper::get($expression); ?>";
        });
        
        // Share current locale with all views
        View::composer('*', function ($view) {
            $view->with('currentLocale', App::getLocale());
            $view->with('isRtl', is_rtl());
        });
    }
}
