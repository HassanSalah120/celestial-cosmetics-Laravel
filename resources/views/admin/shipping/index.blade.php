@extends('layouts.admin')

@section('title', 'Shipping Settings')

@section('content')
<div class="container mx-auto">
    <!-- Page Heading -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-semibold text-gray-800">Shipping Settings</h1>
    </div>

    @include('admin.partials.alerts')

    <!-- Tabs Navigation -->
    <div class="mb-8" x-data="{ activeTab: window.location.hash ? window.location.hash.substring(1) : 'general' }">
        <div class="border-b border-gray-200 bg-white rounded-t-lg shadow-sm">
            <nav class="flex">
                <a @click.prevent="activeTab = 'general'; window.location.hash = 'general'" href="#general" 
                   :class="{
                       'bg-white text-primary border-primary': activeTab === 'general',
                       'bg-gray-50 text-gray-500 hover:text-gray-700 border-transparent hover:border-gray-300': activeTab !== 'general'
                   }"
                   class="flex items-center px-6 py-4 border-b-2 font-medium text-sm transition-all duration-200 ease-in-out">
                    <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    General Settings
                </a>
                <a @click.prevent="activeTab = 'methods'; window.location.hash = 'methods'" href="#methods"
                   :class="{
                       'bg-white text-primary border-primary': activeTab === 'methods',
                       'bg-gray-50 text-gray-500 hover:text-gray-700 border-transparent hover:border-gray-300': activeTab !== 'methods'
                   }" 
                   class="flex items-center px-6 py-4 border-b-2 font-medium text-sm transition-all duration-200 ease-in-out">
                    <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    Shipping Methods
                </a>
            </nav>
        </div>

        <!-- Tabs Content -->
        <div>
            <!-- General Settings Tab -->
            <div x-show="activeTab === 'general'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" class="bg-white rounded-b-lg shadow-md p-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">General Shipping Settings</h2>
                
                <form action="{{ route('admin.shipping.update-general') }}" method="POST">
                    @csrf
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                        <label for="shipping_default_fee" class="block text-sm font-medium text-gray-700">
                            Default Shipping Fee
                        </label>
                        <div class="md:col-span-2">
                            <div class="flex items-center">
                                <div class="mr-3 text-gray-700 font-medium">{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}</div>
                                <input type="number" class="focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md" 
                                    id="shipping_default_fee" name="shipping_default_fee" value="{{ $settings['shipping_default_fee'] ?? '10.00' }}" 
                                    step="0.01" min="0" required>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Default shipping fee applied when no other rules match</p>
                        </div>
                    </div>

                    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                        <label for="shipping_free_threshold" class="block text-sm font-medium text-gray-700">
                            Free Shipping Threshold
                        </label>
                        <div class="md:col-span-2">
                            <div class="flex items-center">
                                <div class="mr-3 text-gray-700 font-medium">{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}</div>
                                <input type="number" class="focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md" 
                                    id="shipping_free_threshold" name="shipping_free_threshold" value="{{ $settings['shipping_free_threshold'] ?? '50.00' }}" 
                                    step="0.01" min="0" required>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Orders above this amount qualify for free shipping (set to 0 to disable)</p>
                        </div>
                    </div>

                    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                        <div class="block text-sm font-medium text-gray-700">Enable Free Shipping</div>
                        <div class="md:col-span-2">
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary h-5 w-5" 
                                    id="shipping_enable_free" name="shipping_enable_free" 
                                    {{ ($settings['shipping_enable_free'] ?? '1') == '1' ? 'checked' : '' }}>
                                <span class="ml-3 text-sm text-gray-700">Enable free shipping for orders above the threshold</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit" class="inline-flex justify-center items-center py-2.5 px-5 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-150">
                            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Save General Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Shipping Methods Tab -->
            <div x-show="activeTab === 'methods'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" class="bg-white rounded-b-lg shadow-md p-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Shipping Methods</h2>
                
                <form action="{{ route('admin.shipping.update-methods') }}" method="POST">
                    @csrf
                    <div class="overflow-x-auto mb-8 ring-1 ring-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee</th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Time</th>
                                    <th scope="col" class="px-6 py-3.5 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Active</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($shippingMethods as $index => $method)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="hidden" name="methods[{{ $index }}][id]" value="{{ is_object($method) && isset($method->id) ? $method->id : (is_array($method) && isset($method['id']) ? $method['id'] : '') }}">
                                        <input type="text" class="focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md" 
                                            name="methods[{{ $index }}][name]" value="{{ is_object($method) ? $method->name : (is_array($method) ? $method['name'] : '') }}" required>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="text" class="focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md" 
                                            name="methods[{{ $index }}][code]" value="{{ is_object($method) ? ($method->code ?? ($method->id ?? '')) : (is_array($method) && isset($method['code']) ? $method['code'] : '') }}" required>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="mr-2 text-gray-700 font-medium">{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}</div>
                                            <input type="number" class="focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md" 
                                                name="methods[{{ $index }}][fee]" value="{{ is_object($method) ? ($method->fee ?? ($method->price ?? 0)) : (is_array($method) && isset($method['fee']) ? $method['fee'] : 0) }}" step="0.01" min="0" required>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="text" class="focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md" 
                                            name="methods[{{ $index }}][estimated_days]" value="{{ is_object($method) ? ($method->estimated_days ?? ($method->delivery_time ?? '3-5 days')) : (is_array($method) && isset($method['estimated_days']) ? $method['estimated_days'] : '3-5 days') }}" placeholder="e.g. 3-5 days" required>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <input type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary h-5 w-5"
                                            name="methods[{{ $index }}][is_active]" value="1" {{ (is_object($method) ? ($method->is_active ?? true) : (is_array($method) && isset($method['is_active']) ? $method['is_active'] : true)) ? 'checked' : '' }}>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No shipping methods found. Add your first shipping method below.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t border-gray-200 pt-8 mt-8">
                        <h3 class="text-lg font-medium text-gray-800 mb-6">Add New Shipping Method</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div>
                                <label for="new_method_name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                                <input type="text" class="focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md shadow-sm" 
                                    id="new_method_name" name="new_method_name" placeholder="e.g. Express Shipping">
                            </div>
                            <div>
                                <label for="new_method_code" class="block text-sm font-medium text-gray-700 mb-2">Code</label>
                                <input type="text" class="focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md shadow-sm" 
                                    id="new_method_code" name="new_method_code" placeholder="e.g. express">
                            </div>
                            <div>
                                <label for="new_method_fee" class="block text-sm font-medium text-gray-700 mb-2">Fee</label>
                                <div class="flex items-center">
                                    <div class="mr-2 text-gray-700 font-medium">{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}</div>
                                    <input type="number" class="focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md shadow-sm" 
                                        id="new_method_fee" name="new_method_fee" placeholder="0.00" step="0.01" min="0">
                                </div>
                            </div>
                            <div>
                                <label for="new_method_days" class="block text-sm font-medium text-gray-700 mb-2">Delivery Time</label>
                                <input type="text" class="focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md shadow-sm" 
                                    id="new_method_days" name="new_method_days" placeholder="e.g. 1-2 days">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit" class="inline-flex justify-center items-center py-2.5 px-5 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-150">
                            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Save Shipping Methods
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        // Nothing needed here - the Alpine.js state is now properly managed in the markup
    });
</script>
@endpush 