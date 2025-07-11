<?php

namespace App\Providers;

use App\Services\TranslationService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(TranslationService::class, function ($app) {
            return new TranslationService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Configure the translation logging channel
        $this->configureLogging();
        
        // Register blade directives
        $this->registerBladeDirectives();
    }
    
    /**
     * Configure the translation logging channel
     * 
     * @return void
     */
    protected function configureLogging()
    {
        $this->app->make('config')->set('logging.channels.translations', [
            'driver' => 'daily',
            'path' => storage_path('logs/translations.log'),
            'level' => 'debug',
            'days' => 14,
        ]);
    }
    
    /**
     * Register blade directives for translations
     * 
     * @return void
     */
    protected function registerBladeDirectives()
    {
        // Regular translations
        Blade::directive('t', function ($expression) {
            return "<?php echo t($expression); ?>";
        });
        
        // JSON translations
        Blade::directive('tj', function ($expression) {
            return "<?php echo tjs($expression); ?>";
        });
        
        // Namespace translations
        Blade::directive('tns', function ($expression) {
            return "<?php echo json_encode(tns($expression)); ?>";
        });
        
        // Direction directives
        Blade::if('rtl', function () {
            return is_rtl();
        });
        
        Blade::if('ltr', function () {
            return !is_rtl();
        });
    }
} 