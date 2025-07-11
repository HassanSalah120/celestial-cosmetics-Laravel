<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\Schema;
use App\Models\ShippingMethod;
use App\Models\CountryShippingFee;

class ShippingService
{
    /**
     * Calculate shipping fee based on order details and settings
     *
     * @param float $subtotal Cart subtotal amount
     * @param string $country Destination country code
     * @param string $shippingMethod Shipping method code
     * @return float
     */
    public function calculateShippingFee(float $subtotal, string $country = 'US', string $shippingMethod = 'standard'): float
    {
        try {
            // Get shipping settings
            $defaultFee = (float) SettingsHelper::get('shipping_default_fee', 10.00);
            $freeThreshold = (float) SettingsHelper::get('shipping_free_threshold', 50.00);
            $enableFreeShipping = (bool) SettingsHelper::get('shipping_enable_free', true);
            
            // Fix: Check if shipping_methods is already an array before trying to decode it
            $shippingMethodsValue = SettingsHelper::get('shipping_methods', '[]');
            $shippingMethods = is_array($shippingMethodsValue) ? $shippingMethodsValue : (json_decode($shippingMethodsValue, true) ?: []);
            
            $internationalFee = (float) SettingsHelper::get('shipping_international_fee', 30.00);
            
            // Fix: Check if shipping_country_fees is already an array before trying to decode it
            $countryFeesValue = SettingsHelper::get('shipping_country_fees', '{}');
            $countryFees = is_array($countryFeesValue) ? $countryFeesValue : (json_decode($countryFeesValue, true) ?: []);
            
            // Check if order qualifies for free shipping
            if ($enableFreeShipping && $freeThreshold > 0 && $subtotal >= $freeThreshold) {
                return 0;
            }
            
            // If method is explicitly "free", return 0
            if ($shippingMethod === 'free') {
                return 0;
            }
            
            // Get shipping method fee if available
            $methodFee = null;
            foreach ($shippingMethods as $method) {
                if (isset($method['code']) && $method['code'] === $shippingMethod && ($method['is_active'] ?? true)) {
                    $methodFee = (float) ($method['fee'] ?? $defaultFee);
                    break;
                }
            }
            
            // If no valid shipping method found, use default fee
            if ($methodFee === null) {
                $methodFee = $defaultFee;
            }
            
            // Apply country-specific fees for international shipping
            if ($country !== 'US') {
                // If country has specific fee, use it
                if (isset($countryFees[$country])) {
                    return (float) $countryFees[$country];
                }
                
                // Otherwise use international fee
                return $internationalFee;
            }
            
            return $methodFee;
        } catch (\Exception $e) {
            Log::error('Error calculating shipping fee: ' . $e->getMessage());
            return (float) SettingsHelper::get('shipping_default_fee', 10.00);
        }
    }
    
    /**
     * Get available shipping methods from settings
     *
     * @param float $subtotal Cart subtotal
     * @param string $country Destination country
     * @return array
     */
    public function getAvailableShippingMethods(float $subtotal, string $country = 'US'): array
    {
        try {
            // Try to get shipping methods from normalized tables first
            $methods = [];
            if (Schema::hasTable('shipping_methods')) {
                $query = ShippingMethod::query();
                
                // Only filter by is_active if the column exists
                if (Schema::hasColumn('shipping_methods', 'is_active')) {
                    $query->where('is_active', true);
                }
                
                // Only sort by sort_order if the column exists
                if (Schema::hasColumn('shipping_methods', 'sort_order')) {
                    $query->orderBy('sort_order');
                }
                
                $shippingMethods = $query->get();
                    
                foreach ($shippingMethods as $method) {
                    $methodFee = $method->fee ?? 0;
                    
                    // Check if there's a country-specific fee
                    if (Schema::hasTable('country_shipping_fees')) {
                        $countryFee = null;
                        
                        // Try to get country fee with shipping_method_id if the column exists
                        if (Schema::hasColumn('country_shipping_fees', 'shipping_method_id')) {
                            $countryFee = CountryShippingFee::where('shipping_method_id', $method->id)
                                ->where('country_code', $country)
                                ->first();
                        } else {
                            // Fallback to just getting the country fee without the shipping method constraint
                            $countryFee = CountryShippingFee::where('country_code', $country)
                                ->first();
                        }
                        
                        if ($countryFee) {
                            $methodFee = $countryFee->fee;
                        }
                    }
                    
                    $methods[] = [
                        'name' => $method->name ?? 'Shipping',
                        'code' => $method->code ?? ('method_' . $method->id),
                        'fee' => $methodFee,
                        'estimated_days' => $method->delivery_time ?? '3-5',
                        'is_active' => true,
                        'description' => $method->description ?? ''
                    ];
                }
                
                if (count($methods) > 0) {
                    // We found methods in the database, now check for free shipping
                    $enableFreeShipping = (bool) SettingsHelper::get('shipping_enable_free', true);
                    $freeThreshold = (float) SettingsHelper::get('shipping_free_threshold', 50.00);
                    
                    // Add free shipping option if eligible
                    if ($enableFreeShipping && $freeThreshold > 0 && $subtotal >= $freeThreshold) {
                        $methods[] = [
                            'name' => 'Free Shipping',
                            'code' => 'free',
                            'fee' => 0,
                            'estimated_days' => '5-7',
                            'is_active' => true,
                            'is_free' => true
                        ];
                    }
                    
                    return $methods;
                }
            }
            
            // Fall back to settings-based shipping methods if no database methods found
            $methodsValue = SettingsHelper::get('shipping_methods', '[]');
            $methods = is_array($methodsValue) ? $methodsValue : (json_decode($methodsValue, true) ?: []);
            $enableFreeShipping = (bool) SettingsHelper::get('shipping_enable_free', true);
            $freeThreshold = (float) SettingsHelper::get('shipping_free_threshold', 50.00);
            
            // Filter out inactive methods
            $methods = array_filter($methods, function($method) {
                return isset($method['is_active']) && $method['is_active'] === true;
            });
            
            // Add free shipping option if eligible
            if ($enableFreeShipping && $freeThreshold > 0 && $subtotal >= $freeThreshold) {
                $methods[] = [
                    'name' => 'Free Shipping',
                    'code' => 'free',
                    'fee' => 0,
                    'estimated_days' => '5-7',
                    'is_active' => true,
                    'is_free' => true
                ];
            }
            
            return $methods;
        } catch (\Exception $e) {
            Log::error('Error getting shipping methods: ' . $e->getMessage());
            // Always return at least one shipping method to avoid checkout errors
            return [
                [
                    'name' => 'Standard Shipping',
                    'code' => 'standard',
                    'fee' => (float) SettingsHelper::get('shipping_default_fee', 10.00),
                    'estimated_days' => '3-5',
                    'is_active' => true
                ]
            ];
        }
    }
} 