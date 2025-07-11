<?php

namespace Database\Seeders;

use App\Models\PaymentConfig;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if payment config already exists
        if (PaymentConfig::count() > 0) {
            $this->command->info('Payment config already exists. Skipping...');
            return;
        }
        
        $this->command->info('Creating payment configuration...');
        
        PaymentConfig::create([
            'currency' => 'EGP',
            'currency_symbol' => 'ج.م',
            
            // Cash on Delivery
            'enable_cash_on_delivery' => true,
            'cod_fee' => 15.00,
            
            // Mobile Payment Methods (Egypt)
            'enable_instapay' => true,
            'instapay_number' => '01234567890',
            'enable_vodafone_cash' => true,
            'vodafone_cash_number' => '01234567890',
            
            // Payment Instructions
            'payment_confirmation_instructions' => 'After making your payment, please send a screenshot of the payment confirmation to our WhatsApp number.',
            'payment_confirmation_contact' => '+201234567890',
            
            // Stripe
            'enable_stripe' => false,
            'stripe_publishable_key' => '',
            'stripe_secret_key' => '',
            'stripe_webhook_secret' => '',
            'stripe_mode' => 'test',
            'stripe_capture_method' => true,
            'stripe_statement_descriptor' => 'Celestial Cosmetics',
            
            // PayPal
            'enable_paypal' => false,
            'paypal_client_id' => '',
            'paypal_secret' => '',
            'paypal_mode' => 'sandbox',
            
            // Tax
            'tax_rate' => 14.00, // Egypt VAT rate
            
            // Gulf & MENA region payment methods
            'enable_bank_transfer' => true,
            'bank_transfer_instructions' => 'Please transfer the payment to our bank account and send the receipt to our WhatsApp number.',
            'bank_account_details' => 'Bank: Example Bank\nAccount Name: Celestial Cosmetics LLC\nAccount Number: 1234567890\nIBAN: EG123456789012345678901234',
            'enable_fawry' => false,
            'fawry_code' => '',
            'enable_stc_pay' => false,
            'stc_pay_number' => '',
            'enable_mada' => false,
            'enable_knet' => false,
            'enable_benefit_pay' => false,
            'benefit_pay_number' => '',
        ]);
        
        $this->command->info('Payment configuration created successfully!');
    }
}
