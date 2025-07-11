<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class CleanupTeamMemberImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'team:fix-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix team member images by ensuring they use the correct path format and exist in the right directories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting team member image cleanup...');

        // 1. Make sure the team directory exists in public storage
        if (!Storage::disk('public')->exists('team')) {
            Storage::disk('public')->makeDirectory('team');
            $this->info('Created public/storage/team directory');
        }

        // 2. Copy sample team member images to storage
        $sourceFiles = [
            '1_(4).jpg' => '1__4_.jpg', 
            '1_(5).jpg' => '1__5_.jpg',
            '1_(6).jpg' => '1__6_.jpg',
            '1_(7).jpg' => '1__7_.jpg', 
            '1_(8).jpg' => '1__8_.jpg',
            '1_(9).jpg' => '1__9_.jpg',
        ];

        $this->info('Copying default team member images to storage...');
        // Try multiple source directories
        $sourceDirectories = [
            public_path('storage'),
            storage_path('app/public'),
            public_path()
        ];
        
        foreach ($sourceFiles as $source => $destination) {
            $foundSource = false;
            
            // Try different source file names
            $possibleSources = [
                '1 (4).jpg', '1 (5).jpg', '1 (6).jpg', 
                '1 (7).jpg', '1 (8).jpg', '1 (9).jpg'
            ];
            
            // Check each possible directory and file name
            foreach ($sourceDirectories as $dir) {
                foreach ($possibleSources as $index => $fileName) {
                    if ($index + 4 == intval(substr($destination, 3, 1))) {
                        $sourcePath = $dir . '/' . $fileName;
                        if (File::exists($sourcePath)) {
                            // Copy to public storage team directory
                            if (Storage::disk('public')->put('team/' . $destination, File::get($sourcePath))) {
                                $this->info("Copied {$sourcePath} to public storage team/{$destination}");
                                $foundSource = true;
                                break 2; // Break both loops
                            }
                        }
                    }
                }
            }
            
            if (!$foundSource) {
                $this->warn("Could not find source file for destination: {$destination}");
            }
        }

        // 3. Update team member image paths in the database
        $teamMembers = DB::table('team_members')->get();
        $this->info("Found {$teamMembers->count()} team members to update");

        foreach ($teamMembers as $member) {
            // Determine the correct image path
            $newPath = 'team/1__' . $member->id . '_.jpg';
            
            $this->info("Updating member #{$member->id} ({$member->name}): {$member->image} -> {$newPath}");
            
            // Update the database
            DB::table('team_members')
                ->where('id', $member->id)
                ->update(['image' => $newPath]);
        }

        $this->info('Team member image cleanup completed successfully!');
        
        return Command::SUCCESS;
    }
}
