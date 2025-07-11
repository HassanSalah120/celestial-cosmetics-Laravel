<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListTestUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:test-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all test users created for permission testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::where('email', 'like', '%@test.celestialcosmetics.com')
            ->get();

        $rows = [];
        foreach ($users as $user) {
            $permissions = is_string($user->permissions) 
                ? json_decode($user->permissions, true) 
                : ($user->permissions ?? []);
                
            $permissionsDisplay = is_array($permissions) 
                ? implode(', ', $permissions) 
                : $permissions;
                
            $rows[] = [
                $user->id,
                $user->name,
                $user->email,
                $user->role,
                $permissionsDisplay ?: 'None'
            ];
        }

        $this->table(
            ['ID', 'Name', 'Email', 'Role', 'Permissions'],
            $rows
        );

        $this->info("Found " . count($users) . " test users");
        
        return Command::SUCCESS;
    }
}
