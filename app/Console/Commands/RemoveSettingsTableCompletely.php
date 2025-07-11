<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class RemoveSettingsTableCompletely extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:remove-completely {--force : Force removal without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the settings table completely, handling foreign keys and dependencies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!Schema::hasTable('settings')) {
            $this->info('Settings table already removed.');
            return Command::SUCCESS;
        }

        $settingsCount = DB::table('settings')->count();
        
        $this->info("The old settings table contains {$settingsCount} records.");
        $this->warn('WARNING: This operation will permanently delete the old settings table.');
        $this->warn('Make sure you have already run `php artisan settings:migrate-normalized` successfully.');
        
        if (!$this->option('force') && !$this->confirm('Are you sure you want to remove the old settings table?', false)) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }
        
        // Create a backup first
        $date = now()->format('Y_m_d_His');
        $backupFilePath = storage_path("app/backups/settings_backup_{$date}.json");
        $settings = DB::table('settings')->get();
        
        if (!file_exists(dirname($backupFilePath))) {
            mkdir(dirname($backupFilePath), 0755, true);
        }
        
        file_put_contents($backupFilePath, json_encode($settings, JSON_PRETTY_PRINT));
        $this->info("Backup created at: {$backupFilePath}");
        
        // Step 1: Run migration to remove foreign keys
        $this->info('Removing foreign key constraints...');
        Artisan::call('migrate', ['--path' => 'database/migrations/2025_04_06_000004_remove_settings_foreign_keys.php']);
        $this->info(Artisan::output());
        
        // Step 2: Remove the settings_translations table if it exists
        if (Schema::hasTable('settings_translations')) {
            $this->info('Removing settings_translations table...');
            Schema::dropIfExists('settings_translations');
        }
        
        // Step 3: Drop the settings table
        $this->info('Removing settings table...');
        Schema::dropIfExists('settings');
        
        $this->info('Settings table and related tables successfully removed.');
        $this->info('If you need to restore the settings table, you can use the backup file.');
        
        return Command::SUCCESS;
    }
} 