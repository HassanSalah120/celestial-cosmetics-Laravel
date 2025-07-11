<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SeedThemes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:themes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database with cool theme options';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding themes...');
        
        try {
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\ThemeSeeder',
                '--force' => true
            ]);
            
            $this->info('Themes seeded successfully!');
            $this->info('The following themes are now available:');
            $this->info('- Celestial Blue (Default)');
            $this->info('- Modern Purple');
            $this->info('- Emerald Green');
            $this->info('- Midnight Blue');
            $this->info('- Rose Gold');
            $this->info('- Dark Mode');
            $this->info('- Sunset Orange');
            
            $this->info("\nYou can now select these themes in the admin panel at: /admin/theme");
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error seeding themes: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 