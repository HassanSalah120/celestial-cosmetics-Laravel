<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            'shipping_country' => 'required|string|size:2',
            'notes' => 'nullable|string',
            'payment_method' => 'required|string|in:cod,instapay,vodafone,bank_transfer,stripe',
            'shipping_method' => 'required|string',
            'create_account' => 'nullable|boolean',
            'password' => 'nullable|required_if:create_account,true|string|min:8|confirmed',
        ];

        // Check if saved addresses are being used
        $usingSavedAddress = $this->filled('shipping_address_id') || $this->filled('billing_address_id');
        
        if ($usingSavedAddress) {
            // When using a saved address, ensure it exists
            if ($this->filled('shipping_address_id')) {
                $rules['shipping_address_id'] = 'required|exists:addresses,id';
            }
            if ($this->filled('billing_address_id')) {
                $rules['billing_address_id'] = 'required|exists:addresses,id';
            }
        } else {
            // If not using saved addresses, require all address fields
            $rules['shipping_first_name'] = 'required|string|max:255';
            $rules['shipping_last_name'] = 'required|string|max:255';
            $rules['shipping_email'] = 'required|email|max:255';
            $rules['shipping_phone'] = 'required|string|max:255';
            $rules['shipping_address_line1'] = 'required|string|max:255';
            $rules['shipping_city'] = 'required|string|max:255';
            $rules['shipping_address_line2'] = 'nullable|string|max:255';
            
            if (\App\Helpers\SettingsHelper::get('require_state', false)) {
                $rules['shipping_state'] = 'required|string|max:255';
            } else {
                $rules['shipping_state'] = 'nullable|string|max:255';
            }
    
            if (\App\Helpers\SettingsHelper::get('require_postal_code', false)) {
                $rules['shipping_postal_code'] = 'required|string|max:50';
            } else {
                $rules['shipping_postal_code'] = 'nullable|string|max:50';
            }
        }

        if ($this->get('shipping_method') === 'custom') {
            $rules['custom_shipping_fee'] = 'required|numeric|min:0';
        }
        
        if ($this->get('payment_method') === 'stripe') {
            $rules['stripeToken'] = 'required|string';
        }
        
        return $rules;
    }
} 