@extends('layouts.admin')

@section('title', 'Create New Coupon')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-primary-light to-accent/30">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Create New Coupon</h1>
                <a href="{{ route('admin.coupons.index') }}" class="bg-white hover:bg-gray-100 text-gray-800 py-2 px-4 rounded-md font-medium flex items-center transition-colors duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to Coupons
                </a>
            </div>
            <p class="text-gray-600 mt-2">Create a new coupon to offer discounts to your customers.</p>
        </div>
        
        <form action="{{ route('admin.coupons.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Coupon Information</h3>
                
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <div class="mb-4">
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                                Coupon Code <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-3">
                                <input type="text" id="code" name="code" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('code') border-red-300 @enderror" value="{{ old('code') }}" placeholder="SUMMER25" maxlength="20" {{ old('auto_generate_code') ? 'readonly' : '' }}>
                                
                                <div class="flex items-center whitespace-nowrap">
                                    <input type="checkbox" id="auto_generate_code" name="auto_generate_code" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded" {{ old('auto_generate_code') ? 'checked' : '' }}>
                                    <label for="auto_generate_code" class="ml-2 text-sm text-gray-700">Auto-generate</label>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Unique code customers will enter to apply this coupon.</p>
                            @error('code')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4" id="code_prefix_wrapper" style="{{ old('auto_generate_code') ? '' : 'display: none;' }}">
                            <label for="code_prefix" class="block text-sm font-medium text-gray-700 mb-1">Code Prefix</label>
                            <input type="text" id="code_prefix" name="code_prefix" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" value="{{ old('code_prefix') }}" placeholder="SUMMER" maxlength="10">
                            <p class="mt-1 text-xs text-gray-500">Optional prefix for your auto-generated code (max 10 characters).</p>
                        </div>
                    
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea id="description" name="description" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('description') border-red-300 @enderror" placeholder="Summer sale discount">{{ old('description') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Brief description of this coupon.</p>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Discount Settings</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="mb-4">
                        <label for="discount_type" class="block text-sm font-medium text-gray-700 mb-1">
                            Discount Type <span class="text-red-500">*</span>
                        </label>
                        <select id="discount_type" name="discount_type" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('discount_type') border-red-300 @enderror">
                            <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage Discount</option>
                            <option value="fixed_amount" {{ old('discount_type') == 'fixed_amount' ? 'selected' : '' }}>Fixed Amount Discount</option>
                        </select>
                        @error('discount_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="discount_value" class="block text-sm font-medium text-gray-700 mb-1">
                            Discount Value <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm" id="discount-symbol">
                                    {{ old('discount_type') == 'fixed_amount' ? \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') : '%' }}
                                </span>
                            </div>
                            <input type="number" step="0.01" min="0" id="discount_value" name="discount_value" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 pl-7 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('discount_value') border-red-300 @enderror" value="{{ old('discount_value') }}">
                        </div>
                        <p class="mt-1 text-xs text-gray-500" id="discount-help-text">
                            {{ old('discount_type') == 'fixed_amount' ? 'Fixed amount to discount from the total.' : 'Percentage to discount from the total.' }}
                        </p>
                        @error('discount_value')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="minimum_order_amount" class="block text-sm font-medium text-gray-700 mb-1">
                            Minimum Order Amount <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}</span>
                            </div>
                            <input type="number" step="0.01" min="0" id="minimum_order_amount" name="minimum_order_amount" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 pl-7 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('minimum_order_amount') border-red-300 @enderror" value="{{ old('minimum_order_amount', 0) }}">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Minimum cart subtotal required to use this coupon.</p>
                        @error('minimum_order_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4" id="maximum_discount_amount_wrapper" style="{{ old('discount_type') == 'percentage' ? '' : 'display: none;' }}">
                        <label for="maximum_discount_amount" class="block text-sm font-medium text-gray-700 mb-1">Maximum Discount Amount</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}</span>
                            </div>
                            <input type="number" step="0.01" min="0" id="maximum_discount_amount" name="maximum_discount_amount" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 pl-7 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('maximum_discount_amount') border-red-300 @enderror" value="{{ old('maximum_discount_amount') }}">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Maximum amount of discount to apply (for percentage discounts).</p>
                        @error('maximum_discount_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Usage Limits</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="mb-4">
                        <label for="usage_limit_per_coupon" class="block text-sm font-medium text-gray-700 mb-1">Total Usage Limit</label>
                        <input type="number" min="1" id="usage_limit_per_coupon" name="usage_limit_per_coupon" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('usage_limit_per_coupon') border-red-300 @enderror" value="{{ old('usage_limit_per_coupon') }}">
                        <p class="mt-1 text-xs text-gray-500">Maximum number of times this coupon can be used. Leave blank for unlimited uses.</p>
                        @error('usage_limit_per_coupon')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="usage_limit_per_user" class="block text-sm font-medium text-gray-700 mb-1">Per-User Limit</label>
                        <input type="number" min="1" id="usage_limit_per_user" name="usage_limit_per_user" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('usage_limit_per_user') border-red-300 @enderror" value="{{ old('usage_limit_per_user', 1) }}">
                        <p class="mt-1 text-xs text-gray-500">Maximum number of times each user can use this coupon. Leave blank for unlimited per user.</p>
                        @error('usage_limit_per_user')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Coupon Validity</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="mb-4">
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="text" id="start_date" name="start_date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm datepicker @error('start_date') border-red-300 @enderror" value="{{ old('start_date') }}" placeholder="YYYY-MM-DD">
                        <p class="mt-1 text-xs text-gray-500">Date when this coupon becomes valid. Leave blank to make it valid immediately.</p>
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="text" id="end_date" name="end_date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm datepicker @error('end_date') border-red-300 @enderror" value="{{ old('end_date') }}" placeholder="YYYY-MM-DD">
                        <p class="mt-1 text-xs text-gray-500">Date when this coupon expires. Leave blank for no expiration.</p>
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Product & Category Restrictions</h3>
                
                <div class="grid grid-cols-1 gap-6">
                    <div class="mb-4">
                        <label for="applicable_products" class="block text-sm font-medium text-gray-700 mb-1">Apply to Specific Products</label>
                        <select id="applicable_products" name="applicable_products[]" multiple class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('applicable_products') border-red-300 @enderror" size="5">
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ (is_array(old('applicable_products')) && in_array($product->id, old('applicable_products'))) ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Select specific products this coupon applies to. Leave blank to apply to all products.</p>
                        @error('applicable_products')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="applicable_categories" class="block text-sm font-medium text-gray-700 mb-1">Apply to Specific Categories</label>
                        <select id="applicable_categories" name="applicable_categories[]" multiple class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm @error('applicable_categories') border-red-300 @enderror" size="5">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ (is_array(old('applicable_categories')) && in_array($category->id, old('applicable_categories'))) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Select specific categories this coupon applies to. Leave blank to apply to all categories.</p>
                        @error('applicable_categories')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Status</h3>
                
                <div class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" value="1" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="ml-2 block text-sm text-gray-700">
                        Activate coupon immediately
                    </label>
                </div>
                <p class="mt-1 text-xs text-gray-500">Uncheck to create the coupon but keep it inactive.</p>
            </div>
            
            <div class="flex justify-end mt-6">
                <a href="{{ route('admin.coupons.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-md font-medium mr-2">
                    Cancel
                </a>
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white py-2 px-4 rounded-md font-medium flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Create Coupon
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize date pickers
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d",
            allowInput: true
        });
        
        // Auto-generate code toggle
        const autoGenerateCheckbox = document.getElementById('auto_generate_code');
        const codeInput = document.getElementById('code');
        const codePrefixWrapper = document.getElementById('code_prefix_wrapper');
        const codePrefixInput = document.getElementById('code_prefix');
        
        // Initialize on page load
        if (autoGenerateCheckbox.checked) {
            codeInput.readOnly = true;
            codeInput.value = 'Will be auto-generated';
            codeInput.classList.add('bg-gray-100');
            codePrefixWrapper.style.display = 'block';
        } else {
            codePrefixWrapper.style.display = 'none';
        }
        
        autoGenerateCheckbox.addEventListener('change', function() {
            codeInput.readOnly = this.checked;
            
            if (this.checked) {
                codeInput.value = 'Will be auto-generated';
                codeInput.classList.add('bg-gray-100');
                codePrefixWrapper.style.display = 'block';
            } else {
                codeInput.value = '';
                codeInput.classList.remove('bg-gray-100');
                codePrefixWrapper.style.display = 'none';
            }
        });
        
        // Limit prefix length
        codePrefixInput.addEventListener('input', function() {
            if (this.value.length > 10) {
                this.value = this.value.substring(0, 10);
            }
        });
        
        // Discount type change
        const discountTypeSelect = document.getElementById('discount_type');
        const discountSymbol = document.getElementById('discount-symbol');
        const discountHelpText = document.getElementById('discount-help-text');
        const maxDiscountWrapper = document.getElementById('maximum_discount_amount_wrapper');
        
        discountTypeSelect.addEventListener('change', function() {
            const isPercentage = this.value === 'percentage';
            discountSymbol.textContent = isPercentage ? '%' : '{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}';
            discountHelpText.textContent = isPercentage 
                ? 'Percentage to discount from the total.' 
                : 'Fixed amount to discount from the total.';
            maxDiscountWrapper.style.display = isPercentage ? 'block' : 'none';
        });
        
        // Multiple select enhancement
        const multiselectFields = document.querySelectorAll('select[multiple]');
        multiselectFields.forEach(select => {
            // Add ability to select multiple options with ctrl/cmd+click
            select.addEventListener('mousedown', function(e) {
                if (e.shiftKey) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endpush 