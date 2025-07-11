<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Helpers\SettingsHelper;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

/**
 * Note: shipping_configs table has been merged into shipping_config.
 * All functionality now uses the shipping_config table.
 */
class ShippingController extends Controller
{
    /**
     * Display the shipping settings page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            // Try to get data from normalized tables first
            $shippingConfig = \App\Models\ShippingConfig::first();
            $shippingMethods = \App\Models\ShippingMethod::orderBy('sort_order')->get();
            $countryFees = \App\Models\CountryShippingFee::all();
        } catch (\Exception $e) {
            // If there's an error (like model not found), initialize with empty data
            $shippingConfig = null;
            $shippingMethods = collect();
            $countryFees = collect();
        }
        
        // If we don't have normalized data, fall back to old settings
        if (!$shippingConfig || $shippingMethods->isEmpty()) {
            // Check if settings table exists before querying it
            $settingsTableExists = Schema::hasTable('settings');
            
            if ($settingsTableExists) {
                // Get all shipping settings
                try {
                    $shippingSettings = Setting::where('group', 'shipping')->get();
                
                    // Transform settings into key-value array for easier access in view
                    $settings = [];
                    foreach ($shippingSettings as $setting) {
                        $settings[$setting->key] = $setting->value;
                    }
                    
                    // Parse JSON settings
                    $oldShippingMethods = json_decode($settings['shipping_methods'] ?? '[]', true) ?: [];
                    $oldCountryFees = json_decode($settings['shipping_country_fees'] ?? '{}', true) ?: [];
                    
                    // Convert to compatible format for view if needed
                    if ($shippingMethods->isEmpty() && !empty($oldShippingMethods)) {
                        $shippingMethods = collect($oldShippingMethods)->map(function($method) {
                            return (object) $method;
                        });
                    }
                    
                    if ($countryFees->isEmpty() && !empty($oldCountryFees)) {
                        $formattedCountryFees = [];
                        foreach ($oldCountryFees as $code => $fee) {
                            $formattedCountryFees[] = (object)[
                                'country_code' => $code,
                                'country_name' => $this->getCountryName($code),
                                'fee' => $fee
                            ];
                        }
                        $countryFees = collect($formattedCountryFees);
                    }
                } catch (\Exception $e) {
                    // Handle any database exceptions
                    $shippingSettings = collect();
                    $settings = [];
                }
            } else {
                // Settings table doesn't exist, use empty collections
                $shippingSettings = collect();
                $settings = [
                    'shipping_default_fee' => 10.00,
                    'shipping_free_threshold' => 50.00,
                    'shipping_enable_free' => true,
                    'shipping_enable_local_pickup' => false,
                    'local_pickup_cost' => 0,
                    'pickup_address' => '',
                    'pickup_instructions' => ''
                ];
            }
            
            return view('admin.shipping.index', compact('shippingSettings', 'settings', 'shippingMethods', 'countryFees'));
        }
        
        // Use normalized data
        $settings = [
            'shipping_default_fee' => $shippingConfig->shipping_flat_rate,
            'shipping_free_threshold' => $shippingConfig->free_shipping_min,
            'shipping_enable_free' => $shippingConfig->enable_shipping,
            'shipping_enable_local_pickup' => $shippingConfig->enable_local_pickup,
            'local_pickup_cost' => $shippingConfig->local_pickup_cost,
            'pickup_address' => $shippingConfig->pickup_address,
            'pickup_instructions' => $shippingConfig->pickup_instructions,
        ];
        
        $shippingSettings = collect(); // Empty collection, not needed when using normalized tables
        
        return view('admin.shipping.index', compact('shippingSettings', 'settings', 'shippingMethods', 'countryFees', 'shippingConfig'));
    }
    
    /**
     * Update general shipping settings
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'shipping_default_fee' => 'required|numeric|min:0',
            'shipping_free_threshold' => 'required|numeric|min:0',
            'shipping_enable_free' => 'sometimes|boolean',
            'shipping_enable_local_pickup' => 'sometimes|boolean',
            'local_pickup_cost' => 'required|numeric|min:0',
            'pickup_address' => 'nullable|string',
            'pickup_instructions' => 'nullable|string',
        ]);
        
        try {
            // Try to update normalized table first
            $shippingConfig = \App\Models\ShippingConfig::first();
            
            if ($shippingConfig) {
                $shippingConfig->shipping_flat_rate = $validated['shipping_default_fee'];
                $shippingConfig->free_shipping_min = $validated['shipping_free_threshold'];
                $shippingConfig->enable_shipping = $request->has('shipping_enable_free');
                $shippingConfig->enable_local_pickup = $request->has('shipping_enable_local_pickup');
                $shippingConfig->local_pickup_cost = $validated['local_pickup_cost'];
                $shippingConfig->pickup_address = $validated['pickup_address'];
                $shippingConfig->pickup_instructions = $validated['pickup_instructions'];
                $shippingConfig->save();
            } else {
                throw new \Exception('ShippingConfig model not found.');
            }
        } catch (\Exception $e) {
            // Fall back to old settings approach
            foreach ($validated as $key => $value) {
                if (in_array($key, ['shipping_enable_free', 'shipping_enable_local_pickup'])) {
                    $value = $request->has($key) ? '1' : '0';
                }
                
                Setting::updateOrCreate(
                    ['key' => $key, 'group' => 'shipping'],
                    ['value' => $value]
                );
            }
        }
        
        return redirect()->route('admin.shipping.index')
            ->with('success', 'Shipping settings updated successfully.');
    }
    
    /**
     * Update shipping methods
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateMethods(Request $request)
    {
        $methods = $request->input('methods', []);
        
        try {
            // Try to update normalized tables first
            $useNormalizedTables = Schema::hasTable('shipping_methods');
            
            if ($useNormalizedTables) {
                // First, delete removed methods
                $existingMethodIds = \App\Models\ShippingMethod::pluck('id')->toArray();
                $updatedMethodIds = array_column($methods, 'id');
                
                foreach ($existingMethodIds as $id) {
                    if (!in_array($id, $updatedMethodIds) && !empty($id)) {
                        \App\Models\ShippingMethod::find($id)->delete();
                    }
                }
                
                // Then update or create methods
                foreach ($methods as $index => $method) {
                    $data = [
                        'name' => $method['name'],
                        'code' => $method['code'] ?? 'method_' . ($index + 1),
                        'fee' => $method['fee'],
                        'estimated_days' => $method['estimated_days'] ?? null,
                        'is_active' => isset($method['is_active']),
                        'sort_order' => $index,
                    ];
                    
                    if (!empty($method['id'])) {
                        \App\Models\ShippingMethod::where('id', $method['id'])->update($data);
                    } else {
                        \App\Models\ShippingMethod::create($data);
                    }
                }
            } else {
                throw new \Exception('Normalized tables not found.');
            }
        } catch (\Exception $e) {
            // Use old settings approach
            // Format methods for storage
            $formattedMethods = array_map(function($method, $index) {
                return [
                    'name' => $method['name'],
                    'code' => $method['code'],
                    'fee' => (float) $method['fee'],
                    'estimated_days' => $method['estimated_days'] ?? null,
                    'is_active' => isset($method['is_active']),
                    'sort_order' => $index,
                ];
            }, $methods, array_keys($methods));
            
            // Save to settings
            Setting::updateOrCreate(
                ['key' => 'shipping_methods', 'group' => 'shipping'],
                ['value' => json_encode($formattedMethods)]
            );
        }
        
        return redirect()->route('admin.shipping.index')
            ->with('success', 'Shipping methods updated successfully.');
    }
    
    /**
     * Update country-specific shipping fees
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateCountryFees(Request $request)
    {
        $countryFees = $request->input('country_fees', []);
        
        try {
            // Try to update normalized tables first
            $useNormalizedTables = Schema::hasTable('country_shipping_fees');
            
            if ($useNormalizedTables) {
                // First, delete all existing fees
                \App\Models\CountryShippingFee::truncate();
                
                // Then create new fees
                foreach ($countryFees as $fee) {
                    if (!empty($fee['country_code']) && isset($fee['fee'])) {
                        \App\Models\CountryShippingFee::create([
                            'country_code' => $fee['country_code'],
                            'country_name' => $this->getCountryName($fee['country_code']),
                            'fee' => (float) $fee['fee'],
                        ]);
                    }
                }
            } else {
                throw new \Exception('Normalized tables not found.');
            }
        } catch (\Exception $e) {
            // Use old settings approach
            $formattedFees = [];
            foreach ($countryFees as $fee) {
                if (!empty($fee['country_code']) && isset($fee['fee'])) {
                    $formattedFees[$fee['country_code']] = (float) $fee['fee'];
                }
            }
            
            Setting::updateOrCreate(
                ['key' => 'shipping_country_fees', 'group' => 'shipping'],
                ['value' => json_encode($formattedFees)]
            );
        }
        
        return redirect()->route('admin.shipping.index')
            ->with('success', 'Country shipping fees updated successfully.');
    }
    
    /**
     * Get country name from country code
     * 
     * @param string $code
     * @return string
     */
    private function getCountryName($code)
    {
        $countries = [
            'US' => 'United States',
            'CA' => 'Canada',
            'UK' => 'United Kingdom',
            'AU' => 'Australia',
            'DE' => 'Germany',
            'FR' => 'France',
            'JP' => 'Japan',
            'CN' => 'China',
            'IN' => 'India',
            'BR' => 'Brazil',
            'EG' => 'Egypt',
            // Add more as needed
        ];
        
        return $countries[$code] ?? $code;
    }
} 