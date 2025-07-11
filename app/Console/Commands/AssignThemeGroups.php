<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Theme;

class AssignThemeGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'themes:assign-groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign groups to existing themes based on their names and characteristics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting theme group assignment...');
        
        // Group associations - common keywords that might appear in theme names
        $darkThemeKeywords = ['dark', 'night', 'black', 'shadow', 'galactic', 'starlight', 'midnight'];
        $lightThemeKeywords = ['light', 'bright', 'white', 'day', 'fresh', 'pastel'];
        $natureThemeKeywords = ['forest', 'ocean', 'sea', 'sky', 'earth', 'nature', 'mint', 'coral'];
        $seasonalThemeKeywords = ['summer', 'winter', 'spring', 'autumn', 'fall', 'holiday'];
        
        // Get all themes without a group
        $themes = Theme::whereNull('group')->get();
        
        $this->info('Found ' . $themes->count() . ' themes without a group');
        
        foreach ($themes as $theme) {
            $themeName = strtolower($theme->name);
            $group = null;
            
            // Check for dark themes
            foreach ($darkThemeKeywords as $keyword) {
                if (str_contains($themeName, $keyword)) {
                    $group = 'Dark';
                    break;
                }
            }
            
            // Check for light themes if no group found yet
            if (!$group) {
                foreach ($lightThemeKeywords as $keyword) {
                    if (str_contains($themeName, $keyword)) {
                        $group = 'Light';
                        break;
                    }
                }
            }
            
            // Check for nature themes if no group found yet
            if (!$group) {
                foreach ($natureThemeKeywords as $keyword) {
                    if (str_contains($themeName, $keyword)) {
                        $group = 'Nature';
                        break;
                    }
                }
            }
            
            // Check for seasonal themes if no group found yet
            if (!$group) {
                foreach ($seasonalThemeKeywords as $keyword) {
                    if (str_contains($themeName, $keyword)) {
                        $group = 'Seasonal';
                        break;
                    }
                }
            }
            
            // If no group found based on name, let's check the colors
            if (!$group && isset($theme->colors['primary'])) {
                $primaryColor = $theme->colors['primary'];
                
                // Check if primary color is dark (simple heuristic)
                if (preg_match('/^#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i', $primaryColor, $matches)) {
                    $r = hexdec($matches[1]);
                    $g = hexdec($matches[2]);
                    $b = hexdec($matches[3]);
                    
                    // Calculate perceived brightness
                    $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
                    
                    if ($brightness < 128) {
                        $group = 'Dark';
                    } else {
                        $group = 'Light';
                    }
                }
            }
            
            // Default group if we still couldn't determine one
            if (!$group) {
                $group = 'Basic';
            }
            
            // Update the theme with the determined group
            $theme->group = $group;
            $theme->save();
            
            $this->info("Assigned group '{$group}' to theme '{$theme->name}'");
        }
        
        $this->info('Theme group assignment complete!');
    }
} 