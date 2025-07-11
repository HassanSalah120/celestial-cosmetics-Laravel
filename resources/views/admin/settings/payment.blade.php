@extends('layouts.admin')

@section('content')
    <div class="pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-semibold text-gray-900 mb-6">Payment Method Settings</h1>
            
            @include('admin.partials.alerts')
            
            <!-- Status Notification Card -->
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                <div>
                    <p class="text-sm text-blue-700">Fields marked with <span class="text-red-500">*</span> are required when the payment method is enabled.</p>
                    <p class="text-xs text-blue-600 mt-1">Sensitive fields can be locked/unlocked with the <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg> icon.</p>
                </div>
            </div>
            
            <div class="bg-white shadow-sm rounded-lg">
                <form action="{{ route('admin.settings.payment.update') }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')
                    
                    <!-- Currency Settings Card -->
                    <div class="mb-8 bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <div class="flex items-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h2 class="text-lg font-medium text-gray-800">Currency Settings</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Currency -->
                            <div>
                                <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">
                                    Currency <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="currency" id="currency" 
                                    value="{{ $paymentSettings['currency'] ?? old('currency', 'EGP') }}" 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary" required>
                                <p class="mt-1 text-xs text-gray-500">Currency code (e.g., EGP, USD, EUR)</p>
                            </div>
                            
                            <!-- Currency Symbol -->
                            <div>
                                <label for="currency_symbol" class="block text-sm font-medium text-gray-700 mb-1">
                                    Currency Symbol <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="currency_symbol" id="currency_symbol" 
                                    value="{{ $paymentSettings['currency_symbol'] ?? old('currency_symbol', 'ج.م') }}" 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary" required>
                                <p class="mt-1 text-xs text-gray-500">Symbol displayed with prices</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Methods Tabs -->
                    <div class="mb-8">
                        <!-- Tabs Navigation -->
                        <div class="border-b border-gray-200">
                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                <button type="button" class="payment-tab active border-primary text-primary py-4 px-1 border-b-2 font-medium text-sm" data-tab="local" aria-current="page">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Local Payment Methods
                                </button>
                                <button type="button" class="payment-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 border-b-2 font-medium text-sm" data-tab="gulf">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Gulf & MENA Region
                                </button>
                                <button type="button" class="payment-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 border-b-2 font-medium text-sm" data-tab="global">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                    </svg>
                                    Global Payment Methods
                                </button>
                            </nav>
                        </div>
                        
                        <!-- Local Payment Methods Tab Content -->
                        <div id="local-tab" class="payment-tab-content pt-6">
                            <!-- COD -->
                            <div class="mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <h2 class="text-lg font-medium text-gray-800">Cash on Delivery</h2>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 mr-2">Popular</span>
                                        <span class="text-xs text-gray-500">Most used</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Enable COD -->
                                    <div>
                                        <div class="flex items-center mb-1">
                                            <input type="checkbox" name="enable_cash_on_delivery" id="enable_cash_on_delivery" value="1"
                                                {{ isset($paymentSettings['enable_cash_on_delivery']) && 
                                                ($paymentSettings['enable_cash_on_delivery'] === true || 
                                                    $paymentSettings['enable_cash_on_delivery'] === 1 || 
                                                    $paymentSettings['enable_cash_on_delivery'] === '1') ? 'checked' : '' }}
                                                class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                            <label for="enable_cash_on_delivery" class="ml-2 block text-sm font-medium text-gray-700">Enable Cash on Delivery</label>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">Allow customers to pay with cash upon delivery</p>
                                    </div>
                                    
                                    <!-- COD Fee -->
                                    <div>
                                        <label for="cod_fee" class="block text-sm font-medium text-gray-700 mb-1">
                                            Cash on Delivery Fee <span class="text-red-500 cod-required-field" style="display: {{ isset($paymentSettings['enable_cash_on_delivery']) && $paymentSettings['enable_cash_on_delivery'] ? 'inline' : 'none' }}">*</span>
                                        </label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">{{ $paymentSettings['currency_symbol'] ?? 'ج.م' }}</span>
                                            </div>
                                            <input type="number" step="0.01" min="0" name="cod_fee" id="cod_fee" 
                                                value="{{ $paymentSettings['cod_fee'] ?? old('cod_fee', '20') }}" 
                                                class="block w-full pl-10 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary" 
                                                {{ isset($paymentSettings['enable_cash_on_delivery']) && $paymentSettings['enable_cash_on_delivery'] ? 'required' : '' }}>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">Additional fee for cash on delivery</p>
                                    </div>
                                </div>
                                
                                <!-- Preview Box -->
                                <div class="mt-6 border border-gray-200 rounded-md p-4 bg-gray-50">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Preview on checkout:</h4>
                                    <div class="flex items-center p-3 bg-white border border-gray-200 rounded-md">
                                        <div class="h-5 w-5 rounded-full border-2 border-primary flex items-center justify-center mr-3">
                                            <div class="h-2 w-2 rounded-full bg-primary"></div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">Cash on Delivery</p>
                                            <p class="text-xs text-gray-500">Pay cash when your order is delivered</p>
                                        </div>
                                        <div class="ml-auto text-sm font-medium text-primary">
                                            +{{ $paymentSettings['currency_symbol'] ?? 'ج.م' }} {{ $paymentSettings['cod_fee'] ?? '20' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Egyptian Mobile Payment Methods -->
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-8">
                                <div class="p-4 border-b border-gray-200 bg-gray-50">
                                    <h3 class="text-base font-medium text-gray-800">Egyptian Mobile Payment Methods</h3>
                                </div>
                                <div class="p-6">
                                    <!-- InstaPay -->
                                    <div class="mb-8 pb-6 border-b border-gray-200">
                                        <div class="flex items-center mb-4">
                                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold mr-2">I</div>
                                            <h3 class="text-base font-medium text-gray-800">InstaPay</h3>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Enable InstaPay -->
                                            <div>
                                                <div class="flex items-center mb-1">
                                                    <input type="checkbox" name="enable_instapay" id="enable_instapay" value="1"
                                                        {{ isset($paymentSettings['enable_instapay']) && 
                                                        ($paymentSettings['enable_instapay'] === true || 
                                                            $paymentSettings['enable_instapay'] === 1 || 
                                                            $paymentSettings['enable_instapay'] === '1') ? 'checked' : '' }}
                                                        class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded payment-method-toggle">
                                                    <label for="enable_instapay" class="ml-2 block text-sm font-medium text-gray-700">Enable InstaPay</label>
                                                </div>
                                                <p class="mt-1 text-xs text-gray-500">Allow customers to pay with InstaPay</p>
                                            </div>
                                            
                                            <!-- InstaPay Number -->
                                            <div class="relative">
                                                <div class="flex items-center mb-1">
                                                    <label for="instapay_number" class="block text-sm font-medium text-gray-700">
                                                        InstaPay Number <span class="text-red-500 instapay-required-field" style="display: {{ isset($paymentSettings['enable_instapay']) && $paymentSettings['enable_instapay'] ? 'inline' : 'none' }}">*</span>
                                                    </label>
                                                    <button type="button" class="ml-2 toggle-field-lock" data-target="instapay_number">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <input type="text" name="instapay_number" id="instapay_number" 
                                                    value="{{ $paymentSettings['instapay_number'] ?? old('instapay_number', '') }}" 
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sensitive-field" 
                                                    {{ isset($paymentSettings['enable_instapay']) && $paymentSettings['enable_instapay'] ? 'required' : '' }}>
                                                <p class="mt-1 text-xs text-gray-500">Your InstaPay phone number or account ID</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Vodafone Cash -->
                                    <div>
                                        <div class="flex items-center mb-4">
                                            <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center text-white font-bold mr-2">V</div>
                                            <h3 class="text-base font-medium text-gray-800">Vodafone Cash</h3>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Enable Vodafone Cash -->
                                            <div>
                                                <div class="flex items-center mb-1">
                                                    <input type="checkbox" name="enable_vodafone_cash" id="enable_vodafone_cash" value="1"
                                                        {{ isset($paymentSettings['enable_vodafone_cash']) && 
                                                        ($paymentSettings['enable_vodafone_cash'] === true || 
                                                            $paymentSettings['enable_vodafone_cash'] === 1 || 
                                                            $paymentSettings['enable_vodafone_cash'] === '1') ? 'checked' : '' }}
                                                        class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded payment-method-toggle">
                                                    <label for="enable_vodafone_cash" class="ml-2 block text-sm font-medium text-gray-700">Enable Vodafone Cash</label>
                                                </div>
                                                <p class="mt-1 text-xs text-gray-500">Allow customers to pay with Vodafone Cash</p>
                                            </div>
                                            
                                            <!-- Vodafone Cash Number -->
                                            <div class="relative">
                                                <div class="flex items-center mb-1">
                                                    <label for="vodafone_cash_number" class="block text-sm font-medium text-gray-700">
                                                        Vodafone Cash Number <span class="text-red-500 vodafone-required-field" style="display: {{ isset($paymentSettings['enable_vodafone_cash']) && $paymentSettings['enable_vodafone_cash'] ? 'inline' : 'none' }}">*</span>
                                                    </label>
                                                    <button type="button" class="ml-2 toggle-field-lock" data-target="vodafone_cash_number">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <input type="text" name="vodafone_cash_number" id="vodafone_cash_number" 
                                                    value="{{ $paymentSettings['vodafone_cash_number'] ?? old('vodafone_cash_number', '') }}" 
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sensitive-field" 
                                                    {{ isset($paymentSettings['enable_vodafone_cash']) && $paymentSettings['enable_vodafone_cash'] ? 'required' : '' }}>
                                                <p class="mt-1 text-xs text-gray-500">Your Vodafone Cash phone number</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Gulf & MENA Region Tab Content -->
                        <div id="gulf-tab" class="payment-tab-content pt-6 hidden">
                            <!-- Bank Transfer -->
                            <div class="mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                                <div class="flex items-center mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                    </svg>
                                    <h2 class="text-lg font-medium text-gray-800">Bank Transfer</h2>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                    <!-- Enable Bank Transfer -->
                                    <div>
                                        <div class="flex items-center mb-1">
                                            <input type="checkbox" name="enable_bank_transfer" id="enable_bank_transfer" value="1"
                                                {{ isset($paymentSettings['enable_bank_transfer']) && 
                                                ($paymentSettings['enable_bank_transfer'] === true || 
                                                    $paymentSettings['enable_bank_transfer'] === 1 || 
                                                    $paymentSettings['enable_bank_transfer'] === '1') ? 'checked' : '' }}
                                                class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                            <label for="enable_bank_transfer" class="ml-2 block text-sm font-medium text-gray-700">Enable Bank Transfer</label>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">Allow customers to pay via bank transfer</p>
                                    </div>
                                </div>
                                
                                <div class="p-4 bg-blue-50 rounded-lg border border-blue-100 mb-4">
                                    <label for="bank_account_details" class="block text-sm font-medium text-gray-700 mb-2">Bank Account Details</label>
                                    <textarea name="bank_account_details" id="bank_account_details" rows="4"
                                        class="block w-full rounded-md border-blue-200 shadow-sm focus:border-primary focus:ring-primary bg-white">{{ $paymentSettings['bank_account_details'] ?? old('bank_account_details', '') }}</textarea>
                                    <p class="mt-1 text-xs text-gray-600">Your bank account information (IBAN, account number, bank name, etc.)</p>
                                </div>
                                
                                <div>
                                    <label for="bank_transfer_instructions" class="block text-sm font-medium text-gray-700 mb-2">Bank Transfer Instructions</label>
                                    <textarea name="bank_transfer_instructions" id="bank_transfer_instructions" rows="3"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">{{ $paymentSettings['bank_transfer_instructions'] ?? old('bank_transfer_instructions', 'Please transfer the full amount to our bank account and include your order number as the reference.') }}</textarea>
                                    <p class="mt-1 text-xs text-gray-500">Instructions for customers to complete bank transfers</p>
                                </div>
                            </div>
                            
                            <!-- Regional Mobile Wallets -->
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-8">
                                <div class="p-4 border-b border-gray-200 bg-gray-50">
                                    <h3 class="text-base font-medium text-gray-800">Regional Mobile Wallets</h3>
                                </div>
                                <div class="p-6">
                                    <!-- Fawry (Egypt) -->
                                    <div class="mb-8 pb-6 border-b border-gray-200">
                                        <div class="flex items-center mb-4">
                                            <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white font-bold mr-2">F</div>
                                            <h3 class="text-base font-medium text-gray-800">Fawry (Egypt)</h3>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Enable Fawry -->
                                            <div>
                                                <div class="flex items-center mb-1">
                                                    <input type="checkbox" name="enable_fawry" id="enable_fawry" value="1"
                                                        {{ isset($paymentSettings['enable_fawry']) && 
                                                        ($paymentSettings['enable_fawry'] === true || 
                                                            $paymentSettings['enable_fawry'] === 1 || 
                                                            $paymentSettings['enable_fawry'] === '1') ? 'checked' : '' }}
                                                        class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                                    <label for="enable_fawry" class="ml-2 block text-sm font-medium text-gray-700">Enable Fawry</label>
                                                </div>
                                                <p class="mt-1 text-xs text-gray-500">Allow customers to pay with Fawry</p>
                                            </div>
                                            
                                            <!-- Fawry Code -->
                                            <div>
                                                <label for="fawry_code" class="block text-sm font-medium text-gray-700 mb-1">Fawry Code</label>
                                                <input type="text" name="fawry_code" id="fawry_code" 
                                                    value="{{ $paymentSettings['fawry_code'] ?? old('fawry_code', '') }}" 
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                                <p class="mt-1 text-xs text-gray-500">Your Fawry merchant code</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- STC Pay (Saudi Arabia) -->
                                    <div class="mb-8 pb-6 border-b border-gray-200">
                                        <div class="flex items-center mb-4">
                                            <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center text-white font-bold mr-2">S</div>
                                            <h3 class="text-base font-medium text-gray-800">STC Pay (Saudi Arabia)</h3>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Enable STC Pay -->
                                            <div>
                                                <div class="flex items-center mb-1">
                                                    <input type="checkbox" name="enable_stc_pay" id="enable_stc_pay" value="1"
                                                        {{ isset($paymentSettings['enable_stc_pay']) && 
                                                        ($paymentSettings['enable_stc_pay'] === true || 
                                                            $paymentSettings['enable_stc_pay'] === 1 || 
                                                            $paymentSettings['enable_stc_pay'] === '1') ? 'checked' : '' }}
                                                        class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                                    <label for="enable_stc_pay" class="ml-2 block text-sm font-medium text-gray-700">Enable STC Pay</label>
                                                </div>
                                                <p class="mt-1 text-xs text-gray-500">Allow customers to pay with STC Pay</p>
                                            </div>
                                            
                                            <!-- STC Pay Number -->
                                            <div>
                                                <label for="stc_pay_number" class="block text-sm font-medium text-gray-700 mb-1">STC Pay Number</label>
                                                <input type="text" name="stc_pay_number" id="stc_pay_number" 
                                                    value="{{ $paymentSettings['stc_pay_number'] ?? old('stc_pay_number', '') }}" 
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                                <p class="mt-1 text-xs text-gray-500">Your STC Pay phone number</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Benefit Pay (Bahrain) -->
                                    <div>
                                        <div class="flex items-center mb-4">
                                            <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white font-bold mr-2">B</div>
                                            <h3 class="text-base font-medium text-gray-800">Benefit Pay (Bahrain)</h3>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Enable Benefit Pay -->
                                            <div>
                                                <div class="flex items-center mb-1">
                                                    <input type="checkbox" name="enable_benefit_pay" id="enable_benefit_pay" value="1"
                                                        {{ isset($paymentSettings['enable_benefit_pay']) && 
                                                        ($paymentSettings['enable_benefit_pay'] === true || 
                                                            $paymentSettings['enable_benefit_pay'] === 1 || 
                                                            $paymentSettings['enable_benefit_pay'] === '1') ? 'checked' : '' }}
                                                        class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                                    <label for="enable_benefit_pay" class="ml-2 block text-sm font-medium text-gray-700">Enable Benefit Pay</label>
                                                </div>
                                                <p class="mt-1 text-xs text-gray-500">Allow customers to pay with Benefit Pay</p>
                                            </div>
                                            
                                            <!-- Benefit Pay Number -->
                                            <div>
                                                <label for="benefit_pay_number" class="block text-sm font-medium text-gray-700 mb-1">Benefit Pay Number</label>
                                                <input type="text" name="benefit_pay_number" id="benefit_pay_number" 
                                                    value="{{ $paymentSettings['benefit_pay_number'] ?? old('benefit_pay_number', '') }}" 
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                                <p class="mt-1 text-xs text-gray-500">Your Benefit Pay account number</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Local Card Payment Systems -->
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-8">
                                <div class="p-4 border-b border-gray-200 bg-gray-50">
                                    <h3 class="text-base font-medium text-gray-800">Local Card Payment Systems</h3>
                                </div>
                                <div class="p-6">
                                    <p class="mb-4 text-sm text-gray-600">These payment methods require integration with a payment gateway that supports them.</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Enable MADA (Saudi Arabia) -->
                                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                            <div class="flex items-center mb-1">
                                                <input type="checkbox" name="enable_mada" id="enable_mada" value="1"
                                                    {{ isset($paymentSettings['enable_mada']) && 
                                                    ($paymentSettings['enable_mada'] === true || 
                                                        $paymentSettings['enable_mada'] === 1 || 
                                                        $paymentSettings['enable_mada'] === '1') ? 'checked' : '' }}
                                                    class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                                <label for="enable_mada" class="ml-2 block text-sm font-medium text-gray-700">Enable MADA (Saudi Arabia)</label>
                                            </div>
                                            <p class="mt-1 text-xs text-gray-500">Enable MADA debit card payments</p>
                                            <div class="mt-2 max-w-16">
                                                <img src="https://upload.wikimedia.org/wikipedia/en/thumb/0/03/Mada_Logo.svg/1200px-Mada_Logo.svg.png" alt="MADA logo" class="w-full h-auto">
                                            </div>
                                        </div>
                                        
                                        <!-- Enable KNET (Kuwait) -->
                                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                            <div class="flex items-center mb-1">
                                                <input type="checkbox" name="enable_knet" id="enable_knet" value="1"
                                                    {{ isset($paymentSettings['enable_knet']) && 
                                                    ($paymentSettings['enable_knet'] === true || 
                                                        $paymentSettings['enable_knet'] === 1 || 
                                                        $paymentSettings['enable_knet'] === '1') ? 'checked' : '' }}
                                                    class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                                <label for="enable_knet" class="ml-2 block text-sm font-medium text-gray-700">Enable KNET (Kuwait)</label>
                                            </div>
                                            <p class="mt-1 text-xs text-gray-500">Enable KNET payments</p>
                                            <div class="mt-2 max-w-16">
                                                <img src="https://www.knet.com.kw/Content/PublicWebsite/img/knet-logo.svg" alt="KNET logo" class="w-full h-auto">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Global Payment Methods Tab Content -->
                        <div id="global-tab" class="payment-tab-content pt-6 hidden">
                            <!-- Stripe -->
                            <div class="mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                            <line x1="1" y1="10" x2="23" y2="10"></line>
                                        </svg>
                                        <h2 class="text-lg font-medium text-gray-800">Stripe</h2>
                                    </div>
                                    <div class="flex items-center">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/b/ba/Stripe_Logo%2C_revised_2016.svg" alt="Stripe logo" class="h-6">
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                    <!-- Enable Stripe -->
                                    <div>
                                        <div class="flex items-center mb-1">
                                            <input type="checkbox" name="enable_stripe" id="enable_stripe" value="1"
                                                {{ isset($paymentSettings['enable_stripe']) && 
                                                ($paymentSettings['enable_stripe'] === true || 
                                                    $paymentSettings['enable_stripe'] === 1 || 
                                                    $paymentSettings['enable_stripe'] === '1') ? 'checked' : '' }}
                                                class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded payment-method-toggle">
                                            <label for="enable_stripe" class="ml-2 block text-sm font-medium text-gray-700">Enable Stripe</label>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">Allow customers to pay with credit/debit cards via Stripe</p>
                                    </div>
                                    
                                    <!-- Stripe Mode -->
                                    <div>
                                        <label for="stripe_mode" class="block text-sm font-medium text-gray-700 mb-1">
                                            Stripe Mode <span class="text-red-500 stripe-required-field" style="display: {{ isset($paymentSettings['enable_stripe']) && $paymentSettings['enable_stripe'] ? 'inline' : 'none' }}">*</span>
                                        </label>
                                        <select name="stripe_mode" id="stripe_mode" 
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                                            {{ isset($paymentSettings['enable_stripe']) && $paymentSettings['enable_stripe'] ? 'required' : '' }}>
                                            <option value="test" {{ isset($paymentSettings['stripe_mode']) && $paymentSettings['stripe_mode'] === 'test' ? 'selected' : '' }}>Test Mode</option>
                                            <option value="live" {{ isset($paymentSettings['stripe_mode']) && $paymentSettings['stripe_mode'] === 'live' ? 'selected' : '' }}>Live Mode</option>
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500">Use test mode for development, live mode for production</p>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                    <!-- Stripe Publishable Key -->
                                    <div class="relative">
                                        <div class="flex items-center mb-1">
                                            <label for="stripe_publishable_key" class="block text-sm font-medium text-gray-700">
                                                Publishable Key <span class="text-red-500 stripe-required-field" style="display: {{ isset($paymentSettings['enable_stripe']) && $paymentSettings['enable_stripe'] ? 'inline' : 'none' }}">*</span>
                                            </label>
                                            <button type="button" class="ml-2 toggle-field-lock" data-target="stripe_publishable_key">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                </svg>
                                            </button>
                                        </div>
                                        <input type="text" name="stripe_publishable_key" id="stripe_publishable_key" 
                                            value="{{ $paymentSettings['stripe_publishable_key'] ?? old('stripe_publishable_key', '') }}" 
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sensitive-field" 
                                            {{ isset($paymentSettings['enable_stripe']) && $paymentSettings['enable_stripe'] ? 'required' : '' }}>
                                        <p class="mt-1 text-xs text-gray-500">Your Stripe publishable key (starts with pk_)</p>
                                    </div>
                                    
                                    <!-- Stripe Secret Key -->
                                    <div class="relative">
                                        <div class="flex items-center mb-1">
                                            <label for="stripe_secret_key" class="block text-sm font-medium text-gray-700">
                                                Secret Key <span class="text-red-500 stripe-required-field" style="display: {{ isset($paymentSettings['enable_stripe']) && $paymentSettings['enable_stripe'] ? 'inline' : 'none' }}">*</span>
                                            </label>
                                            <button type="button" class="ml-2 toggle-field-lock" data-target="stripe_secret_key">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                </svg>
                                            </button>
                                        </div>
                                        <input type="text" name="stripe_secret_key" id="stripe_secret_key" 
                                            value="{{ $paymentSettings['stripe_secret_key'] ?? old('stripe_secret_key', '') }}" 
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sensitive-field" 
                                            {{ isset($paymentSettings['enable_stripe']) && $paymentSettings['enable_stripe'] ? 'required' : '' }}>
                                        <p class="mt-1 text-xs text-gray-500">Your Stripe secret key (starts with sk_)</p>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Webhook Secret -->
                                    <div class="relative">
                                        <div class="flex items-center mb-1">
                                            <label for="stripe_webhook_secret" class="block text-sm font-medium text-gray-700">
                                                Webhook Secret <span class="text-yellow-500">*</span>
                                            </label>
                                            <button type="button" class="ml-2 toggle-field-lock" data-target="stripe_webhook_secret">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                </svg>
                                            </button>
                                        </div>
                                        <input type="text" name="stripe_webhook_secret" id="stripe_webhook_secret" 
                                            value="{{ $paymentSettings['stripe_webhook_secret'] ?? old('stripe_webhook_secret', '') }}" 
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sensitive-field">
                                        <p class="mt-1 text-xs text-gray-500">Your webhook signing secret (starts with whsec_) <span class="text-yellow-600">Recommended for automatic order updates</span></p>
                                    </div>
                                    
                                    <!-- Statement Descriptor -->
                                    <div>
                                        <label for="stripe_statement_descriptor" class="block text-sm font-medium text-gray-700 mb-1">
                                            Statement Descriptor
                                        </label>
                                        <input type="text" name="stripe_statement_descriptor" id="stripe_statement_descriptor" 
                                            value="{{ $paymentSettings['stripe_statement_descriptor'] ?? old('stripe_statement_descriptor', '') }}" 
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                                            maxlength="22">
                                        <p class="mt-1 text-xs text-gray-500">How the charge will appear on customers' statements (max 22 characters)</p>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <div class="flex items-center mb-1">
                                        <input type="checkbox" name="stripe_capture_method" id="stripe_capture_method" value="1"
                                            {{ isset($paymentSettings['stripe_capture_method']) && 
                                            ($paymentSettings['stripe_capture_method'] === true || 
                                                $paymentSettings['stripe_capture_method'] === 1 || 
                                                $paymentSettings['stripe_capture_method'] === '1') ? 'checked' : '' }}
                                            class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                        <label for="stripe_capture_method" class="ml-2 block text-sm font-medium text-gray-700">Automatically capture payment</label>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">When disabled, payments will be authorized but require manual capture in Stripe dashboard</p>
                                </div>
                                
                                <!-- Webhook Setup Info -->
                                <div class="mt-6 border border-blue-200 rounded-md p-4 bg-blue-50">
                                    <h4 class="text-sm font-medium text-blue-700 mb-2">Webhook Setup</h4>
                                    <p class="text-xs text-blue-600 mb-2">For automatic order status updates, set up a webhook in your Stripe dashboard with this endpoint:</p>
                                    <div class="bg-white p-2 rounded border border-blue-200 font-mono text-xs break-all">
                                        {{ route('stripe.webhook') }}
                                    </div>
                                    <p class="mt-2 text-xs text-blue-600">Required events: <span class="font-medium">payment_intent.succeeded, payment_intent.payment_failed</span></p>
                                </div>
                            </div>
                            
                            <div class="p-6 mb-6 bg-gray-50 border border-gray-200 rounded-lg">
                                <div class="flex items-center mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="text-base font-medium text-gray-800">Supported Payment Methods</h3>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div class="p-2 bg-white rounded-lg border border-gray-200 flex items-center justify-center">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Visa_Inc._logo.svg/800px-Visa_Inc._logo.svg.png" alt="Visa" class="h-6">
                                    </div>
                                    <div class="p-2 bg-white rounded-lg border border-gray-200 flex items-center justify-center">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a4/Mastercard_2019_logo.svg/800px-Mastercard_2019_logo.svg.png" alt="Mastercard" class="h-6">
                                    </div>
                                    <div class="p-2 bg-white rounded-lg border border-gray-200 flex items-center justify-center">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/American_Express_logo_%282018%29.svg/800px-American_Express_logo_%282018%29.svg.png" alt="American Express" class="h-6">
                                    </div>
                                    <div class="p-2 bg-white rounded-lg border border-gray-200 flex items-center justify-center text-xs text-gray-500 font-medium">
                                        And many more...
                                    </div>
                                </div>
                                <p class="mt-3 text-xs text-gray-500">Additional payment methods are supported based on your Stripe account settings and customer location.</p>
                            </div>
                            
                            <!-- PayPal (Coming Soon) -->
                            <div class="p-6 mb-6 bg-gray-50 border border-gray-200 rounded-lg text-center">
                                <div class="flex items-center justify-center mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <h3 class="text-base font-medium text-gray-800">PayPal Integration Coming Soon</h3>
                                </div>
                                <p class="text-gray-500">PayPal integration will be available in future updates.</p>
                                <p class="mt-2 text-sm text-gray-500">Please reach out to our support team to learn more about upcoming payment options.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Instructions -->
                    <div class="mb-8 bg-white border border-gray-200 rounded-lg shadow-sm">
                        <div class="p-4 border-b border-gray-200 bg-gray-50">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h2 class="text-lg font-medium text-gray-800">Payment Instructions</h2>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="mb-6">
                                <label for="payment_confirmation_instructions" class="block text-sm font-medium text-gray-700 mb-2">Payment Confirmation Instructions</label>
                                <textarea name="payment_confirmation_instructions" id="payment_confirmation_instructions" rows="3"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">{{ $paymentSettings['payment_confirmation_instructions'] ?? old('payment_confirmation_instructions', 'After making your payment, please contact us with your order number and payment details to confirm your order.') }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">Instructions for customers to confirm their payment</p>
                            </div>
                            
                            <div>
                                <label for="payment_confirmation_contact" class="block text-sm font-medium text-gray-700 mb-2">Payment Confirmation Contact</label>
                                <textarea name="payment_confirmation_contact" id="payment_confirmation_contact" rows="3"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">{{ $paymentSettings['payment_confirmation_contact'] ?? old('payment_confirmation_contact', "Phone: +20123456789\nWhatsApp: +20123456789\nEmail: payments@example.com") }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">Contact details for payment confirmation</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Save Payment Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabs = document.querySelectorAll('.payment-tab');
            const tabContents = document.querySelectorAll('.payment-tab-content');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Remove active class from all tabs
                    tabs.forEach(t => {
                        t.classList.remove('active', 'border-primary', 'text-primary');
                        t.classList.add('border-transparent', 'text-gray-500');
                    });
                    
                    // Add active class to clicked tab
                    tab.classList.add('active', 'border-primary', 'text-primary');
                    tab.classList.remove('border-transparent', 'text-gray-500');
                    
                    // Hide all tab contents
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });
                    
                    // Show the selected tab content
                    const tabId = tab.getAttribute('data-tab');
                    document.getElementById(tabId + '-tab').classList.remove('hidden');
                });
            });

            // Toggle required fields based on payment method checkboxes
            const paymentMethodToggles = document.querySelectorAll('.payment-method-toggle');
            paymentMethodToggles.forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const methodId = this.id.replace('enable_', '');
                    const requiredFields = document.querySelectorAll('.' + methodId + '-required-field');
                    const inputFields = document.querySelectorAll('[id^="' + methodId + '_"]');
                    
                    requiredFields.forEach(field => {
                        field.style.display = this.checked ? 'inline' : 'none';
                    });
                    
                    inputFields.forEach(field => {
                        if (this.checked) {
                            field.setAttribute('required', '');
                        } else {
                            field.removeAttribute('required');
                        }
                    });
                });
            });

            // Handle Cash on Delivery separately (different naming)
            const codToggle = document.getElementById('enable_cash_on_delivery');
            const codRequiredFields = document.querySelectorAll('.cod-required-field');
            const codFeeField = document.getElementById('cod_fee');
            
            if (codToggle) {
                codToggle.addEventListener('change', function() {
                    codRequiredFields.forEach(field => {
                        field.style.display = this.checked ? 'inline' : 'none';
                    });
                    
                    if (this.checked) {
                        codFeeField.setAttribute('required', '');
                    } else {
                        codFeeField.removeAttribute('required');
                    }
                });
            }

            // Toggle field lock functionality
            const lockButtons = document.querySelectorAll('.toggle-field-lock');
            lockButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const field = document.getElementById(targetId);
                    
                    if (field.getAttribute('type') === 'password') {
                        field.setAttribute('type', 'text');
                        this.querySelector('svg').innerHTML = `
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        `;
                    } else {
                        field.setAttribute('type', 'password');
                        this.querySelector('svg').innerHTML = `
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                        `;
                    }
                });
            });

            // Initialize sensitive fields as password
            const sensitiveFields = document.querySelectorAll('.sensitive-field');
            sensitiveFields.forEach(field => {
                if (field.value) {
                    field.setAttribute('type', 'password');
                }
            });
        });
    </script>
    @endpush
@endsection 