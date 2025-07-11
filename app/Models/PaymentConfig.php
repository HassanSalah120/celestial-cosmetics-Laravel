<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentConfig extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'currency',
        'currency_symbol',
        'enable_cash_on_delivery',
        'cod_fee',
        'enable_instapay',
        'instapay_number',
        'enable_vodafone_cash',
        'vodafone_cash_number',
        'payment_confirmation_instructions',
        'payment_confirmation_contact',
        'enable_stripe',
        'stripe_publishable_key',
        'stripe_secret_key',
        'stripe_webhook_secret',
        'stripe_mode',
        'stripe_capture_method',
        'stripe_statement_descriptor',
        'enable_paypal',
        'paypal_client_id',
        'paypal_secret',
        'paypal_mode',
        'tax_rate',
        // Gulf & MENA region payment methods
        'enable_bank_transfer',
        'bank_transfer_instructions',
        'bank_account_details',
        'enable_fawry',
        'fawry_code',
        'enable_stc_pay',
        'stc_pay_number',
        'enable_mada',
        'enable_knet',
        'enable_benefit_pay',
        'benefit_pay_number',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'enable_cash_on_delivery' => 'boolean',
        'cod_fee' => 'float',
        'enable_instapay' => 'boolean',
        'enable_vodafone_cash' => 'boolean',
        'enable_stripe' => 'boolean',
        'stripe_capture_method' => 'boolean',
        'enable_paypal' => 'boolean',
        'tax_rate' => 'float',
        // Gulf & MENA region payment methods
        'enable_bank_transfer' => 'boolean',
        'enable_fawry' => 'boolean',
        'enable_stc_pay' => 'boolean',
        'enable_mada' => 'boolean',
        'enable_knet' => 'boolean',
        'enable_benefit_pay' => 'boolean',
    ];
}
