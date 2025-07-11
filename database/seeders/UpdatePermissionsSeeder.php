<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdatePermissionsSeeder extends Seeder
{
    /**
     * All available permissions in the system
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
     * Role-based permission sets
     */
    private $rolePermissions = [
        'admin' => [
            'view_dashboard',
            'view_reports',
            'manage_products',
            'view_products',
            'manage_categories',
            'manage_orders',
            'view_orders',
            'manage_users',
            'view_customers',
            'view_activity_logs',
            'manage_marketing',
            'manage_settings',
            'manage_contact_messages',
            'manage_roles',
            'manage_inventory',
            'manage_shipping',
            'manage_payments',
            'manage_seo',
            'export_data'
        ],
        'manager' => [
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
        ],
        'staff' => [
            'view_dashboard',
            'view_products',
            'view_orders',
            'view_customers'
        ]
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update admin users
        $adminUsers = DB::table('users')->where('role', 'admin')->get();
        foreach ($adminUsers as $user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'permissions' => json_encode($this->rolePermissions['admin'])
                ]);
        }

        // Update manager users
        $managerUsers = DB::table('users')->where('role', 'manager')->get();
        foreach ($managerUsers as $user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'permissions' => json_encode($this->rolePermissions['manager'])
                ]);
        }

        // Update staff users with basic permissions
        DB::table('users')
            ->where('role', 'staff')
            ->update([
                'permissions' => json_encode($this->rolePermissions['staff'])
            ]);

        // Update specific staff roles
        $specializedStaff = [
            'Product Manager' => [
                'view_dashboard',
                'manage_products',
                'view_products',
                'manage_categories',
                'view_reports',
                'manage_inventory'
            ],
            'Order Manager' => [
                'view_dashboard',
                'manage_orders',
                'view_orders',
                'view_customers',
                'view_reports',
                'manage_shipping'
            ],
            'Marketing Manager' => [
                'view_dashboard',
                'view_products',
                'manage_marketing',
                'view_reports',
                'manage_seo'
            ],
            'Customer Support' => [
                'view_dashboard',
                'view_orders',
                'view_customers',
                'view_products',
                'manage_contact_messages'
            ]
        ];

        foreach ($specializedStaff as $role => $permissions) {
            User::where('name', $role)->update([
                'permissions' => $permissions
            ]);
        }

        $this->command->info('All user permissions have been updated successfully!');
    }
} 