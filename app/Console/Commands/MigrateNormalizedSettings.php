<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrateNormalizedSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:migrate-normalized';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations for normalized settings tables and seed them with existing data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to migrate normalized settings tables...');

        // 1. Run the migrations
        $this->info('Running migrations...');
        Artisan::call('migrate', ['--path' => 'database/migrations/2025_04_06_000001_create_normalized_settings_tables.php']);
        $this->info(Artisan::output());
        
        Artisan::call('migrate', ['--path' => 'database/migrations/2025_04_06_000002_create_more_normalized_settings_tables.php']);
        $this->info(Artisan::output());

        // 2. Run the seeder
        $this->info('Running the NormalizedSettingsSeeder...');
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\NormalizedSettingsSeeder']);
        $this->info(Artisan::output());

        $this->info('Migration and seeding of normalized settings completed successfully!');
        $this->info('The application now supports both old and new settings structure.');
        $this->info('You can gradually update your controllers to use the new normalized tables directly.');

        return Command::SUCCESS;
    }
} 