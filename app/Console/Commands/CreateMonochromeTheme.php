<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Theme;

class CreateMonochromeTheme extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:create-monochrome';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a monochrome theme';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if theme already exists
        if (Theme::where('name', 'Monochrome Elegance')->exists()) {
            $this->info('Monochrome Elegance theme already exists.');
            return;
        }
        
        // Create the theme
        Theme::create([
            'name' => 'Monochrome Elegance',
            'colors' => [
                'primary' => '#212121',
                'primary-light' => '#484848',
                'primary-dark' => '#000000',
                'secondary' => '#757575',
                'secondary-light' => '#a4a4a4',
                'secondary-dark' => '#494949',
                'accent' => '#ffffff',
                'accent-light' => '#ffffff',
                'accent-dark' => '#e0e0e0',
            ],
            'is_active' => false,
        ]);
        
        $this->info('Monochrome Elegance theme created successfully.');
    }
} 