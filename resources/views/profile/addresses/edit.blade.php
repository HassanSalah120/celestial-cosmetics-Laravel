@extends('layouts.app')

@php
use App\Helpers\TranslationHelper;
@endphp

@section('content')
<div class="min-h-screen bg-gradient-to-b from-primary/5 to-secondary/5 py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-display font-bold text-primary">{{ TranslationHelper::get('edit_address', 'Edit Address') }}</h1>
            <p class="mt-2 text-gray-600">{{ TranslationHelper::get('update_shipping_address', 'Update your shipping address details.') }}</p>
        </div>

        <!-- Actions -->
        <div class="mb-6">
            <a href="{{ route('addresses.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ TranslationHelper::get('back_to_addresses', 'Back to Addresses') }}
            </a>
        </div>

        <!-- Address Form -->
        <div class="bg-white rounded-lg shadow-md p-6 md:p-8">
            <form method="POST" action="{{ route('addresses.update', $address->id) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Address Nickname -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">{{ TranslationHelper::get('address_nickname', 'Address Nickname (Optional)') }}</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $address->name) }}" placeholder="{{ TranslationHelper::get('home_work_etc', 'Home, Work, etc.') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- First Name & Last Name -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">{{ TranslationHelper::get('first_name', 'First Name') }} *</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $address->first_name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">{{ TranslationHelper::get('last_name', 'Last Name') }} *</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $address->last_name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Email & Phone -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">{{ TranslationHelper::get('email', 'Email') }} *</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $address->email) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">{{ TranslationHelper::get('phone', 'Phone') }} *</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $address->phone) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Address Line 1 -->
                <div>
                    <label for="address_line1" class="block text-sm font-medium text-gray-700">{{ TranslationHelper::get('address_line1', 'Address Line 1') }} *</label>
                    <input type="text" name="address_line1" id="address_line1" value="{{ old('address_line1', $address->address_line1) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    @error('address_line1')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address Line 2 -->
                <div>
                    <label for="address_line2" class="block text-sm font-medium text-gray-700">{{ TranslationHelper::get('address_line2', 'Address Line 2 (Optional)') }}</label>
                    <input type="text" name="address_line2" id="address_line2" value="{{ old('address_line2', $address->address_line2) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    @error('address_line2')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- City & State/Province -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">{{ TranslationHelper::get('city', 'City') }} *</label>
                        <input type="text" name="city" id="city" value="{{ old('city', $address->city) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700">{{ TranslationHelper::get('state_province', 'State/Province (Optional)') }}</label>
                        <input type="text" name="state" id="state" value="{{ old('state', $address->state) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @error('state')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Postal Code & Country -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700">{{ TranslationHelper::get('postal_code', 'Postal Code (Optional)') }}</label>
                        <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $address->postal_code) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @error('postal_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700">{{ TranslationHelper::get('country', 'Country') }} *</label>
                        <select name="country" id="country" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            <option value="">{{ TranslationHelper::get('select_country', 'Select a country') }}</option>
                            @foreach($shippingCountries as $countryCode)
                                <option value="{{ $countryCode }}" {{ old('country', $address->country) == $countryCode ? 'selected' : '' }}>
                                    {{ \App\Helpers\CountryHelper::getCountryName($countryCode) }}
                                </option>
                            @endforeach
                        </select>
                        @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Default Address Checkbox -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default', $address->is_default) ? 'checked' : '' }} class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                    <label for="is_default" class="ml-2 block text-sm text-gray-700">
                        {{ TranslationHelper::get('set_as_default_address', 'Set as default address') }}
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-accent text-white rounded-lg hover:bg-accent-dark transition-colors duration-200">
                        {{ TranslationHelper::get('update_address', 'Update Address') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 