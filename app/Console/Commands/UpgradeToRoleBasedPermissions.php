<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class UpgradeToRoleBasedPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:upgrade-to-role-based-permissions {--force : Force the operation to run without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upgrade the application to use role-based permissions instead of is_admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Display warning
        $this->info('This command will upgrade the application to use role-based permissions.');
        $this->info('It will modify the database schema and update existing users.');
        $this->info('Make sure you have a backup of your database before proceeding.');
        
        if (!$this->option('force') && !$this->confirm('Do you wish to continue?', true)) {
            $this->info('Operation cancelled.');
            return;
        }
        
        $this->info('Starting upgrade process...');
        
        // Check if migration is needed
        $isAdminExists = Schema::hasColumn('users', 'is_admin');
        $roleExists = Schema::hasColumn('users', 'role');
        
        if (!$isAdminExists && $roleExists) {
            $this->info('The migration appears to have already been applied (is_admin column removed, role column exists).');
            $this->info('Skipping migration step and proceeding with the seeder to ensure proper roles and permissions.');
        } else {
            // Step 1: Run the migration to modify the users table
            $this->info('Step 1: Running migrations to update users table...');
            Artisan::call('migrate', [
                '--path' => 'database/migrations/2025_03_22_161308_update_users_for_roles_and_permissions.php'
            ]);
            $this->info(Artisan::output());
        }
        
        // Step 2: Run the seeder to update existing users
        $this->info('Step 2: Updating users with roles and permissions...');
        try {
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\UpdateUsersRolesPermissionsSeeder',
                '--force' => true
            ]);
            $this->info(Artisan::output());
        } catch (\Exception $e) {
            $this->error('An error occurred while updating user roles and permissions:');
            $this->error($e->getMessage());
            $this->error('You may need to manually set roles for users.');
        }
        
        // Step 3: Clear cache
        $this->info('Step 3: Clearing application cache...');
        Artisan::call('optimize:clear');
        $this->info(Artisan::output());
        
        $this->info('Upgrade completed!');
        $this->info('The application now uses role-based permissions with three roles:');
        $this->info('1. admin - Full access to all features');
        $this->info('2. staff - Access based on assigned permissions');
        $this->info('3. user - Regular user access');
        $this->info('');
        $this->info('Sample staff accounts have been created:');
        $this->info('- products@celestialcosmetics.com (Product Manager)');
        $this->info('- orders@celestialcosmetics.com (Order Manager)');
        $this->info('- support@celestialcosmetics.com (Customer Support)');
        $this->info('All with password: staff123');
    }
}
