<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateUsersRolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users with role = admin
        $adminUsers = DB::table('users')->where('role', 'admin')->get();
        
        foreach ($adminUsers as $user) {
            // Update permissions for admin users
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'permissions' => json_encode([
                        'view_dashboard',
                        'view_reports',
                        'manage_products',
                        'manage_orders', 
                        'view_orders',
                        'manage_users', 
                        'view_customers',
                        'view_activity_logs',
                        'manage_settings'  // Added manage_settings permission
                    ])
                ]);
        }
        
        // Update all staff users
        DB::table('users')
            ->where('role', 'staff')
            ->update([
                'permissions' => json_encode([
                    'view_dashboard',
                    'view_reports',
                    'manage_products',
                    'view_orders',
                    'view_customers',
                    'view_activity_logs'
                ])
            ]);

        // Create sample staff users with specific permissions
        $staffUsers = [
            [
                'name' => 'Product Manager',
                'email' => 'products@celestialcosmetics.com',
                'password' => Hash::make('staff123'),
                'role' => 'staff',
                'permissions' => json_encode([
                    'manage_products',
                    'view_dashboard',
                    'view_reports'
                ]),
                'email_verified_at' => now()
            ],
            [
                'name' => 'Order Manager',
                'email' => 'orders@celestialcosmetics.com',
                'password' => Hash::make('staff123'),
                'role' => 'staff',
                'permissions' => json_encode([
                    'manage_orders',
                    'view_dashboard',
                    'view_reports'
                ]),
                'email_verified_at' => now()
            ],
            [
                'name' => 'Customer Support',
                'email' => 'support@celestialcosmetics.com',
                'password' => Hash::make('staff123'),
                'role' => 'staff',
                'permissions' => json_encode([
                    'view_orders',
                    'view_customers',
                    'view_dashboard'
                ]),
                'email_verified_at' => now()
            ],
        ];

        foreach ($staffUsers as $staffUser) {
            // Check if user already exists, update if it does
            $existingUser = User::where('email', $staffUser['email'])->first();
            
            if ($existingUser) {
                // When updating an existing user, we need to pass permissions as an array
                // since the model will automatically JSON encode it
                $permissions = json_decode($staffUser['permissions'], true);
                
                $existingUser->update([
                    'role' => $staffUser['role'],
                    'permissions' => $permissions
                ]);
            } else {
                // When creating a new user through the User model, Laravel will handle the JSON encoding
                // So we need to pass an array here, not a JSON string
                $userData = $staffUser;
                $userData['permissions'] = json_decode($staffUser['permissions'], true);
                
                User::create($userData);
            }
        }

        $this->command->info('Users have been updated with new roles and permissions!');
    }
}
