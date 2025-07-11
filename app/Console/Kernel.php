<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CheckSettings::class,
        Commands\AssignThemeGroups::class,
        \App\Console\Commands\UpdateOfferImageCommand::class,
        \App\Console\Commands\MigrateNormalizedSettings::class,
        \App\Console\Commands\RemoveOldSettingsTable::class,
        \App\Console\Commands\RemoveSettingsTableCompletely::class,
        \App\Console\Commands\ScanBacklinks::class,
        \App\Console\Commands\FixProductImagePaths::class,
        \App\Console\Commands\SecurityAudit::class,
        \App\Console\Commands\CheckExternalDependencies::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * These schedules are run in the console when an event is triggered.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // Check for Laravel security updates weekly
        $schedule->exec('composer audit --format=json > storage/logs/security-audit-'.date('Y-m-d').'.json')
                ->weekly()
                ->emailOutputTo(config('app.admin_email'))
                ->environments(['production']);
                
        // Clean up old security logs (keep last 3 months)
        $schedule->exec('find storage/logs/security-audit-* -mtime +90 -delete')
                ->monthly()
                ->environments(['production']);
        
        // Run security audit weekly
        $schedule->command('security:audit')
                ->weekly()
                ->sundays()
                ->at('01:00')
                ->emailOutputTo(config('app.admin_email'))
                ->environments(['production']);
        
        // Check external dependencies for vulnerabilities twice a week
        $schedule->command('security:dependencies --notify')
                ->twiceWeekly(1, 4)
                ->at('02:00')
                ->environments(['production']);
        
        // Schedule backlink scanning every week
        $schedule->command('backlinks:scan')->weekly();

        // Check for products with low stock daily
        $schedule->command('products:check-low-stock')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 