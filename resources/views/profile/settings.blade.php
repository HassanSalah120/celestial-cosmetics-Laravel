@extends('layouts.app')

@php
use Illuminate\Support\Facades\Auth;
use App\Helpers\TranslationHelper;
@endphp

@section('content')
<div class="min-h-screen bg-gradient-to-b from-primary/5 to-secondary/5 py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-display font-bold text-primary">{{ TranslationHelper::get('account_settings', 'Account Settings') }}</h1>
            <p class="mt-2 text-gray-600">{{ TranslationHelper::get('manage_your_account_preferences', 'Manage your account preferences, profile information, and addresses.') }}</p>
        </div>

        @if (session('status'))
            <div class="mb-8 bg-accent/10 border border-accent text-accent px-4 py-3 rounded-lg">
                {{ session('status') }}
            </div>
        @endif

        <!-- Profile Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="p-6 sm:p-8">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="bg-primary/10 rounded-full p-3">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-display font-semibold text-primary">{{ TranslationHelper::get('personal_information', 'Personal Information') }}</h2>
                    </div>

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Profile Image -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ TranslationHelper::get('profile_image', 'Profile Image') }}</label>
                            <div class="flex items-start space-x-6">
                                <div class="shrink-0">
                                    <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-100 border">
                                        @if(auth()->user()->profile_image)
                                            <img id="preview-image" src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                                        @else
                                            <img id="preview-image" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&color=7F9CF5&background=EBF4FF" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="relative">
                                        <input type="file" name="profile_image" id="profile_image" accept="image/*" 
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                            onchange="document.getElementById('preview-image').src = window.URL.createObjectURL(this.files[0])">
                                        <label for="profile_image" 
                                            class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 cursor-pointer">
                                            <svg class="h-5 w-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ TranslationHelper::get('choose_image', 'Choose image') }}
                                        </label>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ TranslationHelper::get('jpg_png_gif_max_2mb', 'JPG, PNG or GIF. Max size 2MB. Recommended square image for best results.') }}
                                    </p>
                                    @error('profile_image')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">{{ TranslationHelper::get('name', 'Name') }}</label>
                            <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-accent focus:ring-accent">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">{{ TranslationHelper::get('email', 'Email') }}</label>
                            <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-accent focus:ring-accent">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end">
                            <button type="submit" class="px-4 py-2 bg-accent text-white rounded-lg hover:bg-accent-dark transition-colors duration-200">
                                {{ TranslationHelper::get('save_changes', 'Save Changes') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Update -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="p-6 sm:p-8">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="bg-primary/10 rounded-full p-3">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-display font-semibold text-primary">{{ TranslationHelper::get('update_password', 'Update Password') }}</h2>
                    </div>

                    <form method="POST" action="{{ route('profile.update.password') }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700">{{ TranslationHelper::get('current_password', 'Current Password') }}</label>
                            <input type="password" name="current_password" id="current_password" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-accent focus:ring-accent">
                            @error('current_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">{{ TranslationHelper::get('new_password', 'New Password') }}</label>
                            <input type="password" name="password" id="password" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-accent focus:ring-accent">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">{{ TranslationHelper::get('confirm_new_password', 'Confirm New Password') }}</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-accent focus:ring-accent">
                        </div>

                        <div class="flex items-center justify-end">
                            <button type="submit" class="px-4 py-2 bg-accent text-white rounded-lg hover:bg-accent-dark transition-colors duration-200">
                                {{ TranslationHelper::get('update_password', 'Update Password') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Account Status -->
        <div class="mt-8 bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <div class="p-6 sm:p-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="bg-green-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ TranslationHelper::get('account_status', 'Account Status') }}</h3>
                            <p class="text-sm text-gray-500">{{ TranslationHelper::get('your_account_is_active_and_verified', 'Your account is active and verified') }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 text-sm text-green-800 bg-green-100 rounded-full">{{ TranslationHelper::get('active', 'Active') }}</span>
                </div>
            </div>
        </div>
        
        <!-- Shipping Addresses -->
        <div class="mt-8 bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <div class="p-6 sm:p-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <div class="bg-primary/10 rounded-full p-3">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ TranslationHelper::get('shipping_addresses', 'Shipping Addresses') }}</h3>
                            <p class="text-sm text-gray-500">{{ TranslationHelper::get('manage_your_saved_addresses', 'Manage your saved addresses for faster checkout') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('addresses.index') }}" class="px-4 py-2 bg-accent text-white rounded-lg hover:bg-accent-dark transition-colors duration-200">
                        {{ TranslationHelper::get('manage_addresses', 'Manage Addresses') }}
                    </a>
                </div>
                
                @php
                    $defaultAddress = auth()->user()->defaultAddress;
                @endphp
                
                @if($defaultAddress)
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-sm font-medium text-gray-700">{{ TranslationHelper::get('default_address', 'Default Address') }}</h4>
                            <div class="mt-2 text-sm text-gray-600">
                                <p>{{ $defaultAddress->first_name }} {{ $defaultAddress->last_name }}</p>
                                <p>{{ $defaultAddress->address_line1 }}</p>
                                @if($defaultAddress->address_line2)
                                    <p>{{ $defaultAddress->address_line2 }}</p>
                                @endif
                                <p>
                                    {{ $defaultAddress->city }}
                                    @if($defaultAddress->state), {{ $defaultAddress->state }}@endif
                                    @if($defaultAddress->postal_code), {{ $defaultAddress->postal_code }}@endif
                                </p>
                                <p>{{ \App\Helpers\CountryHelper::getCountryName($defaultAddress->country) }}</p>
                            </div>
                        </div>
                        <a href="{{ route('addresses.edit', $defaultAddress->id) }}" class="text-primary hover:text-primary-dark">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                @else
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <p class="text-sm text-gray-600">{{ TranslationHelper::get('no_addresses_yet', 'You haven\'t added any addresses yet.') }}</p>
                    <a href="{{ route('addresses.create') }}" class="inline-flex items-center mt-2 text-sm text-primary hover:text-primary-dark">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        {{ TranslationHelper::get('add_address', 'Add Address') }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Alpine.js
    document.addEventListener('alpine:init', () => {
        // Alpine component code can go here
    });
</script>
@endpush 