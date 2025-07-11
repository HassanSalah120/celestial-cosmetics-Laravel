<?php

namespace App\Services;

use App\Helpers\CountryHelper;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\Auth;

class AddressService
{
    /**
     * Get data for the address form.
     *
     * @return array
     */
    public function getAddressFormData(): array
    {
        $countries = CountryHelper::getCountries();
        $shippingCountriesStr = SettingsHelper::get('shipping_countries', 'US, EG, UK, CA, AU');
        $shippingCountries = array_map('trim', explode(',', $shippingCountriesStr));
        
        return compact('countries', 'shippingCountries');
    }

    /**
     * Get data for editing an address.
     *
     * @param string $id
     * @return array
     */
    public function getAddressEditData(string $id): array
    {
        $address = Auth::user()->addresses()->findOrFail($id);
        $countries = CountryHelper::getCountries();
        $shippingCountriesStr = SettingsHelper::get('shipping_countries', 'US, EG, UK, CA, AU');
        $shippingCountries = array_map('trim', explode(',', $shippingCountriesStr));
        
        return compact('address', 'countries', 'shippingCountries');
    }

    /**
     * Create a new address.
     *
     * @param array $validated
     * @return \App\Models\Address
     */
    public function createAddress(array $validated)
    {
        $user = Auth::user();
        
        // If this is the first address or set as default, make sure it's the only default
        if (($user->addresses()->count() === 0) || (isset($validated['is_default']) && $validated['is_default'])) {
            $user->addresses()->update(['is_default' => false]);
            $validated['is_default'] = true;
        }
        
        return $user->addresses()->create($validated);
    }

    /**
     * Update an existing address.
     *
     * @param string $id
     * @param array $validated
     * @return bool
     */
    public function updateAddress(string $id, array $validated)
    {
        $user = Auth::user();
        $address = $user->addresses()->findOrFail($id);
        
        // If set as default, make sure it's the only default
        if (isset($validated['is_default']) && $validated['is_default']) {
            $user->addresses()->where('id', '!=', $id)->update(['is_default' => false]);
            $validated['is_default'] = true;
        }
        
        return $address->update($validated);
    }

    /**
     * Delete an address.
     *
     * @param string $id
     * @return bool
     */
    public function deleteAddress(string $id)
    {
        $address = Auth::user()->addresses()->findOrFail($id);
        $wasDefault = $address->is_default;
        
        $address->delete();
        
        // If the deleted address was the default, set another address as default if available
        if ($wasDefault) {
            $newDefault = Auth::user()->addresses()->first();
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }
        
        return true;
    }

    /**
     * Set an address as the default.
     *
     * @param string $id
     * @return bool
     */
    public function setDefaultAddress(string $id)
    {
        $user = Auth::user();
        
        // Reset all addresses to non-default
        $user->addresses()->update(['is_default' => false]);
        
        // Set the selected address as default
        $address = $user->addresses()->findOrFail($id);
        return $address->update(['is_default' => true]);
    }
} 