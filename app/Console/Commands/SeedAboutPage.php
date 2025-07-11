<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\AboutPageSeeder;

class SeedAboutPage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:about-page';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the about page data into the new normalized tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding about page data...');
        
        // Run the seeder
        $seeder = new AboutPageSeeder();
        $seeder->run();
        
        $this->info('About page data seeded successfully!');
        
        return Command::SUCCESS;
    }
}
