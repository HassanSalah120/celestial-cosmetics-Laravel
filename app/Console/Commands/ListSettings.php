<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Models\SettingTranslation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class ListSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all settings in the database with their translations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Listing all settings:');
        
        if (!Schema::hasTable('settings')) {
            $this->error('Settings table does not exist!');
            return 1;
        }
        
        $settings = Setting::all();
        
        if ($settings->count() === 0) {
            $this->warn('No settings found!');
            return 0;
        }
        
        $headers = ['ID', 'Group', 'Key', 'Value', 'Display Name'];
        $rows = [];
        
        foreach ($settings as $setting) {
            $rows[] = [
                $setting->id,
                $setting->group,
                $setting->key,
                $setting->value ? (strlen($setting->value) > 30 ? substr($setting->value, 0, 30) . '...' : $setting->value) : 'NULL',
                $setting->display_name
            ];
        }
        
        $this->table($headers, $rows);
        
        // Check for translations
        if (Schema::hasTable('setting_translations')) {
            $this->info('Checking for setting translations:');
            
            $translations = SettingTranslation::with('setting')->get();
            
            if ($translations->count() === 0) {
                $this->warn('No setting translations found!');
                return 0;
            }
            
            $headers = ['ID', 'Setting Key', 'Locale', 'Value'];
            $rows = [];
            
            foreach ($translations as $translation) {
                $rows[] = [
                    $translation->id,
                    $translation->setting ? $translation->setting->key : 'N/A',
                    $translation->locale ?? 'N/A',
                    $translation->value ? (strlen($translation->value) > 30 ? substr($translation->value, 0, 30) . '...' : $translation->value) : 'NULL'
                ];
            }
            
            $this->table($headers, $rows);
            
            // Show schema information
            $this->info('Setting Translations Table Schema:');
            $columns = Schema::getColumnListing('setting_translations');
            $this->line(implode(', ', $columns));
        } else {
            $this->warn('Setting translations table does not exist!');
        }
        
        return 0;
    }
}
