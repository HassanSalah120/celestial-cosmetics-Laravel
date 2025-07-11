<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin user already exists
        if (!User::where('email', 'admin@celestialcosmetics.com')->exists()) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@celestialcosmetics.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
            $this->command->info('Admin user created.');
        } else {
            $this->command->info('Admin user already exists, skipping creation.');
        }
    }
}
