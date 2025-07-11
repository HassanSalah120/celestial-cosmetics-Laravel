<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;

class CheckSettings extends Command
{
    protected $signature = 'settings:check';
    protected $description = 'Check current settings in the database';

    public function handle()
    {
        $this->info('Checking SEO settings...');
        
        $settings = Setting::where('group', 'seo')->get();
        
        foreach ($settings as $setting) {
            $this->line("Key: {$setting->key}");
            $this->line("Value: {$setting->value}");
            $this->line("Group: {$setting->group}");
            $this->line("-------------------");
        }
        
        return Command::SUCCESS;
    }
} 