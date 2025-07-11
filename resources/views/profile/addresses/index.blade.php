@extends('layouts.app')

@php
use App\Helpers\TranslationHelper;
@endphp

@section('content')
<div class="min-h-screen bg-gradient-to-b from-primary/5 to-secondary/5 py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-display font-bold text-primary">{{ TranslationHelper::get('my_addresses', 'My Addresses') }}</h1>
            <p class="mt-2 text-gray-600">{{ TranslationHelper::get('manage_shipping_addresses', 'Manage your shipping addresses for faster checkout.') }}</p>
        </div>

        @if (session('status'))
            <div class="mb-8 bg-accent/10 border border-accent text-accent px-4 py-3 rounded-lg">
                {{ session('status') }}
            </div>
        @endif

        <!-- Actions -->
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('profile') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ TranslationHelper::get('back_to_profile', 'Back to Profile') }}
            </a>
            <a href="{{ route('addresses.create') }}" class="inline-flex items-center px-4 py-2 bg-accent text-white rounded-lg hover:bg-accent-dark transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ TranslationHelper::get('add_new_address', 'Add New Address') }}
            </a>
        </div>

        <!-- Addresses List -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($addresses as $address)
                <div class="bg-white rounded-lg shadow-md border {{ $address->is_default ? 'border-accent' : 'border-gray-200' }} overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                @if($address->name)
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $address->name }}</h3>
                                @else
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $address->first_name }} {{ $address->last_name }}</h3>
                                @endif
                                
                                @if($address->is_default)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-accent/10 text-accent mt-1">
                                        {{ TranslationHelper::get('default', 'Default') }}
                                    </span>
                                @endif
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('addresses.edit', $address->id) }}" class="text-gray-500 hover:text-gray-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('addresses.destroy', $address->id) }}" method="POST" onsubmit="return confirm('{{ TranslationHelper::get('confirm_delete_address', 'Are you sure you want to delete this address?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="space-y-2 text-sm text-gray-600">
                            <p>{{ $address->address_line1 }}</p>
                            @if($address->address_line2)
                                <p>{{ $address->address_line2 }}</p>
                            @endif
                            <p>
                                {{ $address->city }}
                                @if($address->state), {{ $address->state }}@endif
                                @if($address->postal_code), {{ $address->postal_code }}@endif
                            </p>
                            <p>{{ \App\Helpers\CountryHelper::getCountryName($address->country) }}</p>
                            <p>{{ $address->phone }}</p>
                            <p>{{ $address->email }}</p>
                        </div>
                        
                        @if(!$address->is_default)
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <form action="{{ route('addresses.default', $address->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-sm text-primary hover:text-primary-dark">
                                        {{ TranslationHelper::get('set_as_default', 'Set as Default') }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-gray-50 rounded-lg p-8 text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <p class="text-gray-600 mb-4">{{ TranslationHelper::get('no_addresses_yet', 'You haven\'t added any addresses yet.') }}</p>
                    <a href="{{ route('addresses.create') }}" class="inline-flex items-center px-4 py-2 bg-accent text-white rounded-lg hover:bg-accent-dark transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        {{ TranslationHelper::get('add_your_first_address', 'Add Your First Address') }}
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection 