<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class UpdatePaymentSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:update-payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update or create payment settings with Egyptian payment methods';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating payment settings...');

        // Add Egyptian currency
        $this->updateCurrencySettings();
        
        // Add Egyptian payment methods
        $this->updatePaymentMethodSettings();
        
        // Clear all relevant caches
        $this->clearCaches();
        
        $this->info('Payment settings have been updated successfully!');
        
        return Command::SUCCESS;
    }
    
    /**
     * Update currency settings
     */
    private function updateCurrencySettings()
    {
        $settings = [
            [
                'key' => 'currency',
                'display_name' => 'Currency',
                'value' => 'EGP',
                'group' => 'payment',
                'type' => 'select',
                'options' => json_encode(['USD', 'EUR', 'GBP', 'EGP', 'SAR', 'AED']),
                'description' => 'Default currency for your store',
                'is_public' => true,
            ],
            [
                'key' => 'currency_symbol',
                'display_name' => 'Currency Symbol',
                'value' => 'EGP',
                'group' => 'payment',
                'type' => 'text',
                'options' => null,
                'description' => 'Currency symbol',
                'is_public' => true,
            ],
        ];
        
        $this->updateSettings($settings);
    }
    
    /**
     * Update payment method settings
     */
    private function updatePaymentMethodSettings()
    {
        $settings = [
            [
                'key' => 'enable_cash_on_delivery',
                'display_name' => 'Enable Cash on Delivery (COD)',
                'value' => '1',
                'group' => 'payment',
                'type' => 'boolean',
                'options' => null,
                'description' => 'Allow customers to pay with cash on delivery',
                'is_public' => true,
            ],
            [
                'key' => 'cod_fee',
                'display_name' => 'Cash on Delivery Fee',
                'value' => '20',
                'group' => 'payment',
                'type' => 'text',
                'options' => null,
                'description' => 'Additional fee for cash on delivery orders',
                'is_public' => true,
            ],
            [
                'key' => 'enable_instapay',
                'display_name' => 'Enable InstaPay',
                'value' => '1',
                'group' => 'payment',
                'type' => 'boolean',
                'options' => null,
                'description' => 'Allow customers to pay with InstaPay',
                'is_public' => true,
            ],
            [
                'key' => 'instapay_number',
                'display_name' => 'InstaPay Number',
                'value' => '',
                'group' => 'payment',
                'type' => 'text',
                'options' => null,
                'description' => 'Your InstaPay phone number or account ID',
                'is_public' => true,
            ],
            [
                'key' => 'enable_vodafone_cash',
                'display_name' => 'Enable Vodafone Cash',
                'value' => '1',
                'group' => 'payment',
                'type' => 'boolean',
                'options' => null,
                'description' => 'Allow customers to pay with Vodafone Cash',
                'is_public' => true,
            ],
            [
                'key' => 'vodafone_cash_number',
                'display_name' => 'Vodafone Cash Number',
                'value' => '',
                'group' => 'payment',
                'type' => 'text',
                'options' => null,
                'description' => 'Your Vodafone Cash phone number',
                'is_public' => true,
            ],
            [
                'key' => 'payment_confirmation_instructions',
                'display_name' => 'Payment Confirmation Instructions',
                'value' => 'After making your payment, please contact us with your order number and payment details to confirm your order.',
                'group' => 'payment',
                'type' => 'textarea',
                'options' => null,
                'description' => 'Instructions for customers to confirm their payment',
                'is_public' => true,
            ],
            [
                'key' => 'payment_confirmation_contact',
                'display_name' => 'Payment Confirmation Contact',
                'value' => 'Phone: +20123456789\nWhatsApp: +20123456789\nEmail: payments@example.com',
                'group' => 'payment',
                'type' => 'textarea',
                'options' => null,
                'description' => 'Contact details for payment confirmation',
                'is_public' => true,
            ],
        ];
        
        $this->updateSettings($settings);
    }
    
    /**
     * Update settings in the database
     */
    private function updateSettings($settings)
    {
        foreach ($settings as $setting) {
            // Try to find existing setting
            $existingSetting = Setting::where('key', $setting['key'])->first();
            
            if ($existingSetting) {
                $this->info("Updating existing setting: {$setting['key']}");
                $existingSetting->update([
                    'display_name' => $setting['display_name'],
                    'group' => $setting['group'],
                    'type' => $setting['type'],
                    'description' => $setting['description'],
                    'options' => $setting['options'] ?? null,
                    'is_public' => $setting['is_public'],
                ]);
                
                // Only update the value if it's empty in the existing setting
                if (empty($existingSetting->value)) {
                    $existingSetting->update(['value' => $setting['value']]);
                }
            } else {
                $this->info("Creating new setting: {$setting['key']}");
                Setting::create($setting);
            }
            
            // Clear cache for this setting
            Cache::forget('setting_' . $setting['key']);
        }
    }
    
    /**
     * Clear relevant caches
     */
    private function clearCaches()
    {
        Cache::forget('payment_settings');
        Cache::forget('settings_group_payment');
        Cache::forget('settings_all');
        Cache::forget('general_settings');
        
        $this->info('Cache cleared for payment settings');
    }
} 