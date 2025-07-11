<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shippingSettings = [
            [
                'key' => 'shipping_default_fee',
                'display_name' => 'Default Shipping Fee',
                'value' => '10.00',
                'group' => 'shipping',
                'type' => 'number',
                'options' => null,
                'description' => 'Default shipping fee in store currency',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'shipping_free_threshold',
                'display_name' => 'Free Shipping Threshold',
                'value' => '50.00',
                'group' => 'shipping',
                'type' => 'number',
                'options' => null,
                'description' => 'Order amount to qualify for free shipping (0 to disable)',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'shipping_enable_free',
                'display_name' => 'Enable Free Shipping Option',
                'value' => '1',
                'group' => 'shipping',
                'type' => 'boolean',
                'options' => null,
                'description' => 'Enable free shipping for orders above the threshold',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'shipping_methods',
                'display_name' => 'Available Shipping Methods',
                'value' => json_encode([
                    [
                        'name' => 'Standard Shipping',
                        'code' => 'standard',
                        'fee' => 10.00,
                        'estimated_days' => '3-5',
                        'is_active' => true
                    ],
                    [
                        'name' => 'Express Shipping',
                        'code' => 'express',
                        'fee' => 25.00,
                        'estimated_days' => '1-2',
                        'is_active' => true
                    ]
                ]),
                'group' => 'shipping',
                'type' => 'json',
                'options' => null,
                'description' => 'Define available shipping methods and their costs',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'shipping_international_fee',
                'display_name' => 'International Shipping Fee',
                'value' => '30.00',
                'group' => 'shipping',
                'type' => 'number',
                'options' => null,
                'description' => 'Base fee for international shipping',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'shipping_country_fees',
                'display_name' => 'Country-Specific Fees',
                'value' => json_encode([
                    'CA' => 15.00,
                    'UK' => 25.00,
                    'AU' => 35.00
                ]),
                'group' => 'shipping',
                'type' => 'json',
                'options' => null,
                'description' => 'Specific shipping fees for different countries',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        // Insert settings into the database
        foreach ($shippingSettings as $setting) {
            $exists = DB::table('settings')
                ->where('key', $setting['key'])
                ->exists();
                
            if (!$exists) {
                DB::table('settings')->insert($setting);
            }
        }
        
        // Clear the settings cache if it exists
        if (function_exists('cache') && method_exists(cache(), 'forget')) {
            cache()->forget('settings');
        }

        $this->command->info('Shipping settings seeded successfully!');
    }
} 