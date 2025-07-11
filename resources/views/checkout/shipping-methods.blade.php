@php
use App\Helpers\TranslationHelper;
@endphp

<div class="mb-6 bg-white rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ TranslationHelper::get('shipping_method', 'Shipping Method') }}</h3>
    
    <div class="space-y-3" id="shipping-methods-container">
        @if(!empty($shippingMethods))
            @php
                // Check if free shipping is available
                $freeShippingAvailable = false;
                $freeShippingMethod = null;
                foreach ($shippingMethods as $method) {
                    if (isset($method['is_free']) && $method['is_free']) {
                        $freeShippingAvailable = true;
                        $freeShippingMethod = $method;
                        break;
                    }
                }
                
                // If free shipping is available, set it as selected
                if ($freeShippingAvailable) {
                    $selectedShippingMethod = $freeShippingMethod['code'];
                }
            @endphp
            
            @if($freeShippingAvailable)
                <div class="bg-green-50 border border-green-200 rounded-md p-3 mb-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">{{ TranslationHelper::get('free_shipping_applied', 'Free Shipping Applied') }}</h3>
                            <p class="text-sm text-green-700">{{ TranslationHelper::get('order_qualifies_free_shipping', 'Your order qualifies for free shipping!') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            @foreach($shippingMethods as $method)
                <div class="relative p-4 border border-gray-200 rounded-lg transition-all duration-200 {{ $selectedShippingMethod == $method['code'] ? 'border-primary bg-primary-50' : 'hover:border-gray-300' }} {{ $freeShippingAvailable && !isset($method['is_free']) ? 'opacity-50' : '' }}">
                    <div class="flex items-start">
                        <div class="flex items-center h-5 mt-1">
                            <input 
                                id="shipping-{{ $method['code'] }}" 
                                name="shipping_method" 
                                type="radio" 
                                value="{{ $method['code'] }}" 
                                class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded cursor-pointer shipping-method-radio"
                                {{ $selectedShippingMethod == $method['code'] ? 'checked' : '' }}
                                {{ $freeShippingAvailable && !isset($method['is_free']) ? 'disabled' : '' }}
                            >
                        </div>
                        <div class="ml-3 flex flex-col flex-grow">
                            <div class="flex justify-between items-start">
                                <label for="shipping-{{ $method['code'] }}" class="text-sm font-medium text-gray-700 cursor-pointer">
                                    {{ $method['name'] }}
                                </label>
                                <span class="text-sm font-medium {{ isset($method['is_free']) && $method['is_free'] ? 'text-green-600' : 'text-gray-900' }}">
                                    @if(isset($method['is_free']) && $method['is_free'])
                                        {{ TranslationHelper::get('free', 'Free') }}
                                    @else
                                        {{ \App\Helpers\SettingsHelper::formatPrice($method['fee']) }}
                                    @endif
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ TranslationHelper::get('estimated_delivery', 'Estimated delivery') }}: {{ $method['estimated_days'] ?? '3-5' }} {{ TranslationHelper::get('business_days', 'business days') }}
                            </p>
                            @if(isset($method['description']) && !empty($method['description']))
                                <p class="text-xs text-gray-500 mt-1">{{ $method['description'] }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="p-4 border border-yellow-200 bg-yellow-50 rounded-md">
                <p class="text-sm text-yellow-800">{{ TranslationHelper::get('no_shipping_methods', 'No shipping methods available') }}</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle shipping method changes
    const shippingMethodRadios = document.querySelectorAll('.shipping-method-radio:not([disabled])');
    
    shippingMethodRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updateShippingMethod(this.value);
        });
    });
    
    function updateShippingMethod(shippingMethod) {
        // Show loading state
        const orderSummary = document.getElementById('order-summary') || document.querySelector('.lg\\:col-span-5');
        if (orderSummary) {
            orderSummary.classList.add('opacity-50');
        }
        
        // Get shipping country if available
        const shippingCountrySelect = document.getElementById('shipping_country');
        const shippingCountry = shippingCountrySelect ? shippingCountrySelect.value : null;
        
        // Get currency symbol from parent page if available
        let currencySymbol = '$';
        if (window.currencySymbol) {
            currencySymbol = window.currencySymbol;
        }
        
        // Send AJAX request to update shipping method
        fetch('{{ route('checkout.update-shipping') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                shipping_method: shippingMethod,
                shipping_country: shippingCountry
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update currency symbol if provided
                if (data.currency_symbol) {
                    currencySymbol = data.currency_symbol;
                    if (window.currencySymbol) {
                        window.currencySymbol = currencySymbol;
                    }
                }
                
                // Update shipping cost display
                const shippingCostElement = document.getElementById('shipping-cost');
                const orderTotalElement = document.getElementById('order-total');
                
                // Update shipping cost display
                if (shippingCostElement) {
                    if (parseFloat(data.shipping) === 0) {
                        shippingCostElement.innerHTML = '<span class="text-green-600">' + 
                            '{{ TranslationHelper::get('free', 'Free') }}' + '</span>';
                    } else {
                        shippingCostElement.textContent = formatPrice(parseFloat(data.shipping));
                    }
                }
                
                // Update total cost
                if (orderTotalElement) {
                    const total = parseFloat(data.total);
                    orderTotalElement.textContent = formatPrice(total);
                }
                
                // Highlight selected shipping method
                document.querySelectorAll('.shipping-method-radio').forEach(radio => {
                    const container = radio.closest('.border');
                    if (container) {
                        if (radio.checked) {
                            container.classList.add('border-primary', 'bg-primary-50');
                            container.classList.remove('hover:border-gray-300');
                        } else {
                            container.classList.remove('border-primary', 'bg-primary-50');
                            container.classList.add('hover:border-gray-300');
                        }
                    }
                });
            } else {
                // Show error message
                console.error('Failed to update shipping method');
            }
        })
        .catch(error => {
            console.error('Error updating shipping method:', error);
        })
        .finally(() => {
            // Remove loading state
            if (orderSummary) {
                orderSummary.classList.remove('opacity-50');
            }
        });
    }

    // Function to format price according to settings
    function formatPrice(price) {
        // Get settings
        const currencySymbol = window.currencySymbol || '{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'ج.م') }}';
        const currencyPosition = '{{ \App\Helpers\SettingsHelper::get('currency_position', 'right') }}';
        const thousandSeparator = '{{ \App\Helpers\SettingsHelper::get('thousand_separator', ',') }}';
        const decimalSeparator = '{{ \App\Helpers\SettingsHelper::get('decimal_separator', '.') }}';
        const decimalDigits = {{ \App\Helpers\SettingsHelper::get('decimal_digits', 2) }};
        
        // Format number with proper separators
        let formattedNumber = price.toFixed(decimalDigits);
        
        // Add thousand separators if needed
        if (thousandSeparator) {
            const parts = formattedNumber.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandSeparator);
            formattedNumber = parts.join(decimalSeparator);
        }
        
        // Position currency symbol according to settings
        switch (currencyPosition) {
            case 'left':
                return currencySymbol + formattedNumber;
            case 'right':
                return formattedNumber + currencySymbol;
            case 'left_with_space':
                return currencySymbol + ' ' + formattedNumber;
            case 'right_with_space':
                return formattedNumber + ' ' + currencySymbol;
            default:
                return formattedNumber + currencySymbol;
        }
    }
});
</script>
@endpush 