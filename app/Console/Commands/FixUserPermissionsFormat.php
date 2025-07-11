<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FixUserPermissionsFormat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-user-permissions-format';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix user permissions format to ensure they are stored as JSON arrays';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Looking for staff users with permissions...');
        
        // Get all staff users
        $staffUsers = User::where('role', 'staff')->get();
        
        if ($staffUsers->isEmpty()) {
            $this->info('No staff users found.');
            return 0;
        }
        
        $this->info('Found ' . $staffUsers->count() . ' staff users. Checking permissions format...');
        $fixedCount = 0;
        
        foreach ($staffUsers as $user) {
            $permissions = $user->permissions;
            
            // Check if permissions is a string but not a valid JSON array
            if (is_string($permissions) && !$this->isJsonArray($permissions)) {
                $this->warn("User {$user->email} has invalid permissions format: " . json_encode($permissions));
                
                try {
                    // Try to convert to a proper array format
                    if (!empty($permissions)) {
                        // If it's a comma-separated string, convert to array
                        if (strpos($permissions, ',') !== false) {
                            $permissionsArray = array_map('trim', explode(',', $permissions));
                        } else {
                            // Single permission as string
                            $permissionsArray = [$permissions];
                        }
                        
                        $user->permissions = $permissionsArray;
                        $user->save();
                        
                        $this->info("Fixed permissions for user {$user->email}: " . json_encode($permissionsArray));
                        $fixedCount++;
                    } else {
                        // Empty permissions, set to empty array
                        $user->permissions = [];
                        $user->save();
                        $this->info("Set empty permissions array for user {$user->email}");
                        $fixedCount++;
                    }
                } catch (\Exception $e) {
                    $this->error("Error fixing permissions for user {$user->email}: " . $e->getMessage());
                }
            } elseif (is_null($permissions) && $user->role === 'staff') {
                // Staff users with null permissions should have an empty array
                $user->permissions = [];
                $user->save();
                $this->info("Set empty permissions array for user {$user->email} (was null)");
                $fixedCount++;
            } else {
                $this->line("User {$user->email} has valid permissions format: " . json_encode($permissions));
            }
        }
        
        $this->info("Completed! Fixed permissions format for {$fixedCount} users.");
        return 0;
    }
    
    /**
     * Check if a string is a valid JSON array
     */
    private function isJsonArray($string) 
    {
        if (!is_string($string)) {
            return false;
        }
        
        try {
            $decoded = json_decode($string, true);
            return (json_last_error() === JSON_ERROR_NONE) && is_array($decoded);
        } catch (\Exception $e) {
            return false;
        }
    }
}
