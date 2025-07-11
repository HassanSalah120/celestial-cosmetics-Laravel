<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestUsersPermissionsSeeder extends Seeder
{
    /**
     * All available permissions in the system and human-readable descriptions
     */
    private $allPermissions = [
        'view_dashboard' => 'Access the admin dashboard',
        'view_reports' => 'View sales and performance reports',
        'manage_products' => 'Create, edit and delete products',
        'view_products' => 'View product listings',
        'manage_categories' => 'Create, edit and delete product categories',
        'manage_orders' => 'Process and manage customer orders',
        'view_orders' => 'View order details',
        'manage_users' => 'Create, edit and delete user accounts',
        'view_customers' => 'View customer information',
        'view_activity_logs' => 'View system activity logs',
        'manage_marketing' => 'Manage promotions and discounts',
        'manage_settings' => 'Configure system settings',
        'manage_contact_messages' => 'Manage customer inquiries and messages',
        'manage_roles' => 'Create and manage user roles and permissions',
        'manage_inventory' => 'Manage product inventory and stock',
        'manage_shipping' => 'Configure shipping options and rates',
        'manage_payments' => 'Configure payment methods and settings',
        'manage_seo' => 'Manage SEO settings and metadata',
        'export_data' => 'Export reports and system data'
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an admin user with all permissions
        $this->createUser('Admin', 'admin', array_keys($this->allPermissions));

        // Create a manager user with manager permissions
        $this->createUser('Manager', 'manager', [
            'view_dashboard',
            'view_reports',
            'manage_products',
            'view_products',
            'manage_categories',
            'manage_orders',
            'view_orders',
            'view_customers',
            'view_activity_logs',
            'manage_marketing',
            'manage_contact_messages',
            'manage_inventory',
            'export_data'
        ]);

        // Create a basic staff user with minimal permissions
        $this->createUser('Basic Staff', 'staff', [
            'view_dashboard',
            'view_products',
            'view_orders',
            'view_customers'
        ]);

        // Create individual users for each permission
        foreach ($this->allPermissions as $permission => $description) {
            $name = ucwords(str_replace('_', ' ', $permission));
            $this->createUser($name, 'staff', [$permission]);
        }

        // Create users with specific domain access
        $this->createUser('Product Admin', 'staff', [
            'view_dashboard',
            'manage_products', 
            'view_products',
            'manage_categories',
            'manage_inventory'
        ]);

        $this->createUser('Order Admin', 'staff', [
            'view_dashboard',
            'manage_orders',
            'view_orders',
            'view_customers',
            'manage_shipping'
        ]);

        $this->createUser('Marketing Admin', 'staff', [
            'view_dashboard',
            'view_products',
            'manage_marketing',
            'manage_seo',
            'export_data'
        ]);

        $this->createUser('Customer Service', 'staff', [
            'view_dashboard',
            'view_orders',
            'view_customers',
            'view_products',
            'manage_contact_messages'
        ]);

        $this->createUser('Reports Analyst', 'staff', [
            'view_dashboard',
            'view_reports',
            'view_products',
            'view_orders',
            'export_data'
        ]);

        $this->command->info('Test users have been created successfully!');
    }

    /**
     * Create a user with specific permissions
     *
     * @param string $name The user's name
     * @param string $role The user's role
     * @param array $permissions The permissions to assign
     * @return User
     */
    private function createUser($name, $role, $permissions = [])
    {
        // Create a normalized email address from the name
        $email = Str::slug($name) . '@test.celestialcosmetics.com';

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        
        if ($existingUser) {
            // Update existing user
            $existingUser->update([
                'role' => $role,
                'permissions' => $permissions
            ]);
            $this->command->info("Updated existing user: {$name}");
            return $existingUser;
        }
        
        // Create a new user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => $role,
            'permissions' => $permissions,
            'email_verified_at' => now()
        ]);
        
        $this->command->info("Created new user: {$name} with role: {$role}");
        
        return $user;
    }
}
        