<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ShippingConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if tables exist
        if (!Schema::hasTable('shipping_config')) {
            $this->command->info('shipping_config table does not exist. Skipping ShippingConfigSeeder.');
            return;
        }

        // Create default shipping config
        $existingConfig = DB::table('shipping_config')->where('id', 1)->first();
        
        if (!$existingConfig) {
            DB::table('shipping_config')->insert([
                'id' => 1,
                'enable_shipping' => true,
                'free_shipping_min' => 100.00,
                'shipping_flat_rate' => 10.00,
                'enable_local_pickup' => true,
                'local_pickup_cost' => 0.00,
                'pickup_address' => '123 Main Street, Cairo, Egypt',
                'pickup_instructions' => 'Please call ahead to arrange pickup during business hours.',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $this->command->info('Default shipping config created.');
        } else {
            $this->command->info('Shipping config already exists - skipping.');
        }

        // Check if shipping_methods table exists
        if (!Schema::hasTable('shipping_methods')) {
            $this->command->info('shipping_methods table does not exist. Skipping shipping methods seeding.');
            return;
        }
        
        // Create default shipping methods if none exist
        if (DB::table('shipping_methods')->count() === 0) {
            $methods = [
                [
                    'name' => 'Standard Shipping',
                    'description' => 'Delivery within 3-5 business days',
                    'price' => 10.00,
                    'is_active' => true,
                    'sort_order' => 1,
                    'shipping_config_id' => 1,
                    'delivery_time' => '3-5 business days',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'name' => 'Express Shipping',
                    'description' => 'Delivery within 1-2 business days',
                    'price' => 20.00,
                    'is_active' => true,
                    'sort_order' => 2,
                    'shipping_config_id' => 1,
                    'delivery_time' => '1-2 business days',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'name' => 'Local Pickup',
                    'description' => 'Pickup from our store',
                    'price' => 0.00,
                    'is_active' => true,
                    'sort_order' => 3,
                    'shipping_config_id' => 1,
                    'delivery_time' => 'Same day',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ];

            foreach ($methods as $method) {
                DB::table('shipping_methods')->insert($method);
            }
            $this->command->info('Default shipping methods created.');
        } else {
            $this->command->info('Shipping methods already exist - skipping.');
        }
    }
}
