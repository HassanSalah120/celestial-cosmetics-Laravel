<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class SyncLocales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-locales {locale? : The locale to set}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize locales across the application and clear caches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get locale from argument or config
        $locale = $this->argument('locale') ?? config('app.locale', 'en');
        
        // Set locale in config
        config(['app.locale' => $locale]);
        
        // Set locale in App facade
        App::setLocale($locale);
        
        // Set locale in app() helper
        app()->setLocale($locale);
        
        // Clear all caches
        $this->info('Clearing caches...');
        Artisan::call('config:clear');
        $this->info('Config cache cleared.');
        Artisan::call('cache:clear');
        $this->info('Application cache cleared.');
        Artisan::call('view:clear');
        $this->info('View cache cleared.');
        Artisan::call('route:clear');
        $this->info('Route cache cleared.');
        
        $this->info('Locale has been synchronized across the application:');
        $this->table(
            ['Config', 'App', 'app()'],
            [[config('app.locale'), App::getLocale(), app()->getLocale()]]
        );
        
        return Command::SUCCESS;
    }
} 