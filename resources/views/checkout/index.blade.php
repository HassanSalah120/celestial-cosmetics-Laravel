@extends('layouts.app')

@php
use Illuminate\Support\Str;
use App\Helpers\TranslationHelper;
use Illuminate\Support\Facades\Auth;
use App\Helpers\SettingsHelper as Settings;
@endphp

@section('meta_tags')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="stripe-key" content="{{ isset($paymentMethods) ? collect($paymentMethods)->where('id', 'stripe')->first()->publishable_key ?? '' : '' }}">
    <x-seo :title="Settings::get('checkout_meta_title') ?? (is_rtl() ? 'الدفع' : 'Checkout') . ' | ' . config('app.name')"
           :description="Settings::get('checkout_meta_description') ?? (is_rtl() ? 'أكمل عملية الشراء بأمان. املأ معلومات الشحن وتفاصيل الدفع.' : 'Complete your purchase securely. Fill in your shipping information and payment details.')"
           :keywords="Settings::get('checkout_meta_keywords') ?? (is_rtl() ? 'الدفع، الدفع، الطلب، الشحن' : 'checkout, payment, order, shipping') . ', ' . config('app.name')"
           :ogImage="Settings::get('checkout_og_image')"
           type="website" />
    <!-- Include Stripe.js in the head section -->
    <script src="https://js.stripe.com/v3/"></script>
@endsection

<script>
// Define the function inline to ensure it's available
function stripePayNowClicked() {
    console.log('Stripe payment flow initiated');
    
    // Get necessary elements
    const stripeSubmitBtn = document.getElementById('stripe-submit');
    const cardElement = document.getElementById('card-element');
    const cardErrors = document.getElementById('card-errors');
    const checkoutForm = document.getElementById('checkout-form');
    
    // Reset error display
    if (cardErrors) {
        cardErrors.textContent = '';
        cardErrors.classList.add('hidden');
    }
    
    // Check if Stripe is loaded and initialized
    if (!window.stripeInstance || !window.stripeCardElement) {
        const errorMsg = 'Stripe is not properly initialized. Please refresh the page and try again.';
        console.error(errorMsg);
        if (cardErrors) {
            cardErrors.textContent = errorMsg;
            cardErrors.classList.remove('hidden');
        }
        return;
    }
    
    // Validate form fields first
    const requiredFields = [
        'shipping_first_name',
        'shipping_last_name',
        'shipping_email',
        'shipping_phone',
        'shipping_address_line1',
        'shipping_city',
        'shipping_country'
    ];
    
    let hasErrors = false;
    let errorMessage = 'Please fill in all required fields:\n';
    
    // Check each required field
    requiredFields.forEach(field => {
        const input = document.getElementById(field);
        if (!input || !input.value.trim()) {
            hasErrors = true;
            errorMessage += `- ${field.replace('shipping_', '').replace('_', ' ')} is required\n`;
            // Highlight the field
            if (input) {
                input.classList.add('border-red-500');
                input.addEventListener('input', function() {
                    this.classList.remove('border-red-500');
                }, { once: true });
            }
        }
    });
    
    // If there are validation errors, show them and stop
    if (hasErrors) {
        if (cardErrors) {
            cardErrors.textContent = 'Please fill in all required fields before proceeding.';
            cardErrors.classList.remove('hidden');
        }
        console.error(errorMessage);
        return;
    }
    
    // Set loading state
    if (stripeSubmitBtn) {
        stripeSubmitBtn.disabled = true;
        stripeSubmitBtn.textContent = 'Processing...';
    }
    
    try {
        // Create form data from checkout form
        const formData = new FormData(checkoutForm);
        formData.append('payment_method', 'stripe');
        
        // Add AJAX headers to ensure the response is JSON
        const headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };
        
        // Show processing message
        if (cardErrors) {
            cardErrors.textContent = 'Processing your order...';
            cardErrors.classList.remove('hidden');
            cardErrors.classList.remove('text-red-600');
            cardErrors.classList.add('text-blue-600');
        }
        
        // Create order first
        fetch(checkoutForm.action, {
            method: 'POST',
            body: formData,
            headers: headers,
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    try {
                        // Try to parse as JSON
                        const data = JSON.parse(text);
                        return { success: false, ...data };
                    } catch (e) {
                        console.error('Invalid response format:', text);
                        // If it's not valid JSON, it might be an HTML error page
                        if (text.includes('<!DOCTYPE html>')) {
                            // Check for common patterns indicating the user needs to log in
                            if (text.includes('login') || response.status === 401 || response.status === 419) {
                                window.location.href = '/login';
                                throw new Error('Session expired. Please log in and try again.');
                            }
                            throw new Error(`Server error: ${response.status}. Please refresh and try again.`);
                        }
                        throw new Error('Server error: ' + response.status);
                    }
                });
            }
            
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Error parsing JSON response:', e, text);
                    return { success: false, message: 'Invalid response from server' };
                }
            });
        })
        .then(orderData => {
            if (!orderData.success || !orderData.order_id) {
                throw new Error(orderData.message || 'Could not create order');
            }
            
            console.log('Order created successfully:', orderData.order_id);
            
            // Update processing message
            if (cardErrors) {
                cardErrors.textContent = 'Order created, processing payment...';
            }
            
            // Create payment intent
            return fetch('/checkout/stripe/create-payment-intent', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ order_id: orderData.order_id }),
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    // Try to parse as JSON first
                    return response.text().then(text => {
                        try {
                            // Try to parse as JSON
                            const data = JSON.parse(text);
                            throw new Error(data.error || `Server error: ${response.status}`);
                        } catch (e) {
                            // If it's not valid JSON, it might be an HTML error page
                            if (text.includes('<!DOCTYPE html>')) {
                                // Session likely expired, redirect to login
                                if (response.status === 401 || response.status === 419) {
                                    window.location.href = '/login?redirect=' + encodeURIComponent(window.location.href);
                                    throw new Error('Session expired. Please log in and try again.');
                                }
                                throw new Error(`Server error: ${response.status}. Please refresh the page and try again.`);
                            }
                            throw new Error(e.message || `Server error: ${response.status}`);
                        }
                    });
                }
                return response.json();
            })
            .then(paymentData => {
                console.log('Payment intent created');
                return { orderData, paymentData };
            });
        })
        .then(({ orderData, paymentData }) => {
            if (!paymentData.clientSecret) {
                throw new Error('No client secret received');
            }
            
            // Final confirmation message
            if (cardErrors) {
                cardErrors.textContent = 'Processing payment...';
            }
            
            // Confirm card payment using the same Stripe instance
            return window.stripeInstance.confirmCardPayment(paymentData.clientSecret, {
                payment_method: {
                    card: window.stripeCardElement,
                    billing_details: {
                        name: formData.get('shipping_first_name') + ' ' + formData.get('shipping_last_name'),
                        email: formData.get('shipping_email'),
                        phone: formData.get('shipping_phone'),
                        address: {
                            line1: formData.get('shipping_address_line1'),
                            line2: formData.get('shipping_address_line2') || '',
                            city: formData.get('shipping_city'),
                            state: formData.get('shipping_state') || '',
                            postal_code: formData.get('shipping_postal_code') || '',
                            country: formData.get('shipping_country')
                        }
                    }
                }
            }).then(result => {
                if (result.error) {
                    throw new Error(result.error.message);
                }
                
                console.log('Payment confirmed successfully');
                
                // Reset error state
                if (cardErrors) {
                    cardErrors.textContent = 'Payment successful! Redirecting to order confirmation...';
                    cardErrors.classList.remove('text-red-600');
                    cardErrors.classList.add('text-green-600');
                }
                
                // Redirect to success page
                window.location.href = `/checkout/success/${orderData.order_id}`;
            });
        })
        .catch(error => {
            console.error('Payment error:', error);
            
            // Reset button state
            if (stripeSubmitBtn) {
                stripeSubmitBtn.disabled = false;
                stripeSubmitBtn.textContent = 'Pay Now';
            }
            
            // Show error to user
            if (cardErrors) {
                cardErrors.textContent = error.message || 'An error occurred. Please try again.';
                cardErrors.classList.remove('hidden');
                cardErrors.classList.remove('text-blue-600');
                cardErrors.classList.remove('text-green-600');
                cardErrors.classList.add('text-red-600');
            }
        });
    } catch (error) {
        console.error('Error in payment flow:', error);
        
        // Reset button state
        if (stripeSubmitBtn) {
            stripeSubmitBtn.disabled = false;
            stripeSubmitBtn.textContent = 'Pay Now';
        }
        
        // Show error to user
        if (cardErrors) {
            cardErrors.textContent = 'An unexpected error occurred. Please try again.';
            cardErrors.classList.remove('hidden');
        }
    }
}

function testButtonClick() {
    alert('Test button clicked! Global functions are working.');
}

// Initialize the card element on page load
document.addEventListener('DOMContentLoaded', function() {
    // Store Stripe instance globally
    window.stripeInstance = null;
    window.stripeCardElement = null;

    // Check if Stripe is selected
    const stripeRadio = document.querySelector('input[name="payment_method"][value="stripe"]');
    if (stripeRadio && stripeRadio.checked) {
        initializeStripeElement();
    }
    
    // Add change handler for the payment method
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            if (this.value === 'stripe') {
                initializeStripeElement();
                
                // Hide main checkout button
                const mainCheckoutBtn = document.getElementById('main-checkout-button');
                if (mainCheckoutBtn) mainCheckoutBtn.style.display = 'none';
                
                // Show Stripe container
                const stripeContainer = document.getElementById('stripe-payment-container');
                if (stripeContainer) stripeContainer.style.display = 'block';
            } else {
                // Show main checkout button
                const mainCheckoutBtn = document.getElementById('main-checkout-button');
                if (mainCheckoutBtn) mainCheckoutBtn.style.display = 'flex';
                
                // Hide Stripe container
                const stripeContainer = document.getElementById('stripe-payment-container');
                if (stripeContainer) stripeContainer.style.display = 'none';
            }
        });
    });
    
    // Handle saved address selection
    const addressCards = document.querySelectorAll('.address-select-card');
    const addressRadios = document.querySelectorAll('.address-select-radio');
    const manualAddressForm = document.getElementById('manual-address-form');
    const useDifferentAddressCheckbox = document.getElementById('use_different_address');
    
    if (addressCards.length > 0) {
        // Add click handler for address cards
        addressCards.forEach(card => {
            card.addEventListener('click', function() {
                const addressId = this.getAttribute('data-address-id');
                const radio = document.getElementById('address-' + addressId);
                
                if (radio) {
                    radio.checked = true;
                    
                    // Update UI to show selected address
                    addressCards.forEach(c => {
                        c.classList.remove('border-accent', 'bg-accent/5');
                        c.classList.add('border-gray-200');
                    });
                    this.classList.remove('border-gray-200');
                    this.classList.add('border-accent', 'bg-accent/5');
                    
                    // Hide manual address form
                    if (manualAddressForm) {
                        manualAddressForm.classList.add('hidden');
                        
                        // Remove required attribute from form fields
                        toggleRequiredAttributes(false);
                    }
                    
                    // Reset checkbox
                    if (useDifferentAddressCheckbox) {
                        useDifferentAddressCheckbox.checked = false;
                    }
                    
                    // Send AJAX request to update selected address
                    selectAddress(addressId);
                }
            });
        });
        
        // Add change handler for address radios
        addressRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    const addressId = this.value;
                    
                    // Update UI to show selected address
                    addressCards.forEach(card => {
                        card.classList.remove('border-accent', 'bg-accent/5');
                        card.classList.add('border-gray-200');
                        
                        if (card.getAttribute('data-address-id') === addressId) {
                            card.classList.remove('border-gray-200');
                            card.classList.add('border-accent', 'bg-accent/5');
                        }
                    });
                    
                    // Hide manual address form
                    if (manualAddressForm) {
                        manualAddressForm.classList.add('hidden');
                        
                        // Remove required attribute from form fields
                        toggleRequiredAttributes(false);
                    }
                    
                    // Reset checkbox
                    if (useDifferentAddressCheckbox) {
                        useDifferentAddressCheckbox.checked = false;
                    }
                    
                    // Send AJAX request to update selected address
                    selectAddress(addressId);
                }
            });
        });
        
        // Handle "use different address" checkbox
        if (useDifferentAddressCheckbox) {
            useDifferentAddressCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    // Show manual address form
                    if (manualAddressForm) {
                        manualAddressForm.classList.remove('hidden');
                        
                        // Add required attribute to form fields
                        toggleRequiredAttributes(true);
                    }
                    
                    // Show save address section
                    const saveAddressSection = document.getElementById('save-address-section');
                    if (saveAddressSection) {
                        saveAddressSection.classList.remove('hidden');
                    }
                    
                    // Uncheck all address radios
                    addressRadios.forEach(radio => {
                        radio.checked = false;
                    });
                    
                    // Reset address card styles
                    addressCards.forEach(card => {
                        card.classList.remove('border-accent', 'bg-accent/5');
                        card.classList.add('border-gray-200');
                    });
                } else {
                    // Hide manual address form
                    if (manualAddressForm) {
                        manualAddressForm.classList.add('hidden');
                        
                        // Remove required attribute from form fields
                        toggleRequiredAttributes(false);
                    }
                    
                    // Hide save address section if an address is selected
                    if (Array.from(addressRadios).some(radio => radio.checked)) {
                        const saveAddressSection = document.getElementById('save-address-section');
                        if (saveAddressSection) {
                            saveAddressSection.classList.add('hidden');
                        }
                    }
                    
                    // Check first address radio if none selected
                    const anyChecked = Array.from(addressRadios).some(radio => radio.checked);
                    if (!anyChecked && addressRadios.length > 0) {
                        addressRadios[0].checked = true;
                        const addressId = addressRadios[0].value;
                        
                        // Update UI to show selected address
                        addressCards.forEach(card => {
                            if (card.getAttribute('data-address-id') === addressId) {
                                card.classList.remove('border-gray-200');
                                card.classList.add('border-accent', 'bg-accent/5');
                            }
                        });
                        
                        // Send AJAX request to update selected address
                        selectAddress(addressId);
                    }
                }
            });
        }
        
        // Handle save address checkbox
        const saveAddressCheckbox = document.getElementById('save_address');
        const addressNameSection = document.getElementById('address-name-section');
        
        if (saveAddressCheckbox && addressNameSection) {
            saveAddressCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    addressNameSection.classList.remove('hidden');
                } else {
                    addressNameSection.classList.add('hidden');
                }
            });
        }
    }
    
    function selectAddress(addressId) {
        // Update hidden input fields
        const billingAddressIdInput = document.getElementById('billing_address_id');
        const shippingAddressIdInput = document.getElementById('shipping_address_id');
        
        if (billingAddressIdInput) billingAddressIdInput.value = addressId;
        if (shippingAddressIdInput) shippingAddressIdInput.value = addressId;
        
        // Send AJAX request to update selected address
        fetch('{{ route('checkout.select-address') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ address_id: addressId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Address selected:', data.address);
                
                // If shipping country changed, update shipping cost and total
                if (data.shipping !== undefined) {
                    const shippingCostElement = document.getElementById('shipping-cost');
                    const orderTotalElement = document.getElementById('order-total');
                    
                    if (shippingCostElement) {
                        shippingCostElement.textContent = '{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }} ' + data.shipping.toFixed(2);
                    }
                    
                    if (orderTotalElement) {
                        orderTotalElement.textContent = '{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }} ' + data.total.toFixed(2);
                    }
                    
                    // Update country dropdown
                    const countrySelect = document.getElementById('shipping_country');
                    if (countrySelect && data.shipping_country) {
                        for (let i = 0; i < countrySelect.options.length; i++) {
                            if (countrySelect.options[i].value === data.shipping_country) {
                                countrySelect.selectedIndex = i;
                                break;
                            }
                        }
                    }
                }
                
                // Pre-fill form fields with address data
                const address = data.address;
                document.getElementById('shipping_first_name').value = address.first_name;
                document.getElementById('shipping_last_name').value = address.last_name;
                document.getElementById('shipping_email').value = address.email;
                document.getElementById('shipping_phone').value = address.phone;
                document.getElementById('shipping_address_line1').value = address.address_line1;
                document.getElementById('shipping_address_line2').value = address.address_line2 || '';
                document.getElementById('shipping_city').value = address.city;
                
                if (document.getElementById('shipping_state')) {
                    document.getElementById('shipping_state').value = address.state || '';
                }
                
                if (document.getElementById('shipping_postal_code')) {
                    document.getElementById('shipping_postal_code').value = address.postal_code || '';
                }
                
                // Update country dropdown
                const countrySelect = document.getElementById('shipping_country');
                if (countrySelect) {
                    for (let i = 0; i < countrySelect.options.length; i++) {
                        if (countrySelect.options[i].value === address.country) {
                            countrySelect.selectedIndex = i;
                            break;
                        }
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error selecting address:', error);
        });
    }
    
    function initializeStripeElement() {
        const cardElement = document.getElementById('card-element');
        const stripeKey = document.querySelector('meta[name="stripe-key"]')?.getAttribute('content');
        
        if (!cardElement || !stripeKey || cardElement.hasChildNodes()) return;
        
        try {
            // Create and store Stripe instance globally
            window.stripeInstance = Stripe(stripeKey);
            const elements = window.stripeInstance.elements();
            
            // Create card element
            window.stripeCardElement = elements.create('card', {
                style: {
                    base: {
                        color: '#32325d',
                        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                        fontSmoothing: 'antialiased',
                        fontSize: '16px',
                        '::placeholder': {
                            color: '#aab7c4'
                        }
                    },
                    invalid: {
                        color: '#fa755a',
                        iconColor: '#fa755a'
                    }
                }
            });
            
            // Mount the card element
            window.stripeCardElement.mount('#card-element');
            
            // Handle validation errors
            window.stripeCardElement.addEventListener('change', function(event) {
                const displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                    displayError.classList.remove('hidden');
                } else {
                    displayError.textContent = '';
                    displayError.classList.add('hidden');
                }
            });
            
            console.log('Stripe card element initialized successfully');
        } catch (error) {
            console.error('Error initializing Stripe card element:', error);
        }
    }

    // Function to toggle required attributes on form fields
    function toggleRequiredAttributes(isRequired) {
        const requiredFields = [
            'shipping_first_name',
            'shipping_last_name',
            'shipping_email',
            'shipping_phone',
            'shipping_address_line1',
            'shipping_city',
            'shipping_country'
        ];
        
        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                if (isRequired) {
                    field.setAttribute('required', '');
                } else {
                    field.removeAttribute('required');
                }
            }
        });
        
        // Handle conditional required fields
        const shippingState = document.getElementById('shipping_state');
        const shippingPostalCode = document.getElementById('shipping_postal_code');
        
        if (shippingState && shippingState.hasAttribute('data-required')) {
            if (isRequired) {
                shippingState.setAttribute('required', '');
            } else {
                shippingState.removeAttribute('required');
            }
        }
        
        if (shippingPostalCode && shippingPostalCode.hasAttribute('data-required')) {
            if (isRequired) {
                shippingPostalCode.setAttribute('required', '');
            } else {
                shippingPostalCode.removeAttribute('required');
            }
        }
    }
});
</script>

@section('content')
<div class="min-h-screen bg-gradient-to-b from-primary/5 to-secondary/5 py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-display font-bold text-primary">{{ is_rtl() ? 'الدفع' : 'Checkout' }}</h1>
        </div>

        <form method="POST" action="{{ route('checkout.process') }}" id="checkout-form">
                    @csrf
            <div class="lg:grid lg:grid-cols-12 lg:gap-x-12">
                <!-- Add hidden fields for address IDs -->
                <input type="hidden" name="billing_address_id" id="billing_address_id" value="{{ $selectedAddressId ?? '' }}">
                <input type="hidden" name="shipping_address_id" id="shipping_address_id" value="{{ $selectedAddressId ?? '' }}">
                
                <div class="lg:col-span-7">
                    <!-- Shipping Address -->
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ is_rtl() ? 'معلومات الشحن' : 'Shipping Information' }}</h2>
                        
                        @if(Auth::check() && count($savedAddresses) > 0)
                        <div class="mb-6">
                            <h3 class="text-md font-medium text-gray-700 mb-2">{{ is_rtl() ? 'العناوين المحفوظة' : 'Your Saved Addresses' }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($savedAddresses as $address)
                                <div class="border rounded-lg p-3 {{ $selectedAddressId == $address->id ? 'border-accent bg-accent/5' : 'border-gray-200 hover:border-gray-300' }} cursor-pointer address-select-card" data-address-id="{{ $address->id }}">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5 mt-1">
                                            <input type="radio" name="saved_address" id="address-{{ $address->id }}" value="{{ $address->id }}" class="address-select-radio h-4 w-4 text-accent border-gray-300 focus:ring-accent" {{ $selectedAddressId == $address->id ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="address-{{ $address->id }}" class="font-medium text-gray-700 cursor-pointer">
                                                @if($address->name)
                                                    {{ $address->name }}
                                                @else
                                                    {{ $address->first_name }} {{ $address->last_name }}
                                                @endif
                                                
                                                @if($address->is_default)
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-accent text-white">
                                                    {{ is_rtl() ? 'افتراضي' : 'Default' }}
                                                </span>
                                                @endif
                                            </label>
                                            <p class="text-gray-500 mt-1">{{ $address->address_line1 }}</p>
                                            <p class="text-gray-500">
                                                {{ $address->city }}, 
                                                @if($address->state){{ $address->state }}, @endif
                                                {{ \App\Helpers\CountryHelper::getCountryName($address->country) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                
                                <div class="border border-dashed border-gray-300 rounded-lg p-3 flex items-center justify-center">
                                    <a href="{{ route('addresses.create') }}" class="text-accent hover:text-accent-dark flex items-center">
                                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        {{ is_rtl() ? 'إضافة عنوان جديد' : 'Add New Address' }}
                                    </a>
                                </div>
                            </div>
                            
                            <div class="mt-4 flex items-center">
                                <input type="checkbox" name="use_different_address" id="use_different_address" class="h-4 w-4 text-accent focus:ring-accent border-gray-300 rounded">
                                <label for="use_different_address" class="ml-2 block text-sm text-gray-700">
                                    {{ is_rtl() ? 'استخدام عنوان مختلف لهذا الطلب' : 'Use a different address for this order' }}
                                </label>
                            </div>
                        </div>
                        
                        <div id="manual-address-form" class="{{ $selectedAddressId ? 'hidden' : '' }}">
                        @endif
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="shipping_first_name" class="block text-sm font-medium text-gray-700 mb-1">{{ is_rtl() ? 'الاسم الأول' : 'First Name' }}</label>
                                <input type="text" name="shipping_first_name" id="shipping_first_name" value="{{ old('shipping_first_name', $user->first_name ?? '') }}" class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" {{ Auth::check() && count($savedAddresses) > 0 && $selectedAddressId ? '' : 'required' }}>
                                @error('shipping_first_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                            <div>
                                <label for="shipping_last_name" class="block text-sm font-medium text-gray-700 mb-1">{{ is_rtl() ? 'اسم العائلة' : 'Last Name' }}</label>
                                <input type="text" name="shipping_last_name" id="shipping_last_name" value="{{ old('shipping_last_name', $user->last_name ?? '') }}" class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" {{ Auth::check() && count($savedAddresses) > 0 && $selectedAddressId ? '' : 'required' }}>
                                @error('shipping_last_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                            </div>
                                </div>

                        <div class="mb-4">
                            <label for="shipping_email" class="block text-sm font-medium text-gray-700 mb-1">{{ is_rtl() ? 'البريد الإلكتروني' : 'Email Address' }}</label>
                            <input type="email" name="shipping_email" id="shipping_email" value="{{ old('shipping_email', $user->email ?? '') }}" class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" {{ Auth::check() && count($savedAddresses) > 0 && $selectedAddressId ? '' : 'required' }}>
                            @error('shipping_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                        <div class="mb-4">
                            <label for="shipping_phone" class="block text-sm font-medium text-gray-700 mb-1">{{ is_rtl() ? 'رقم الهاتف' : 'Phone Number' }}</label>
                            <input type="text" name="shipping_phone" id="shipping_phone" value="{{ old('shipping_phone', $user->phone ?? '') }}" class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" {{ Auth::check() && count($savedAddresses) > 0 && $selectedAddressId ? '' : 'required' }}>
                            @error('shipping_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                        <div class="mb-4">
                            <label for="shipping_address_line1" class="block text-sm font-medium text-gray-700 mb-1">{{ is_rtl() ? 'العنوان السطر 1' : 'Address Line 1' }}</label>
                            <input type="text" name="shipping_address_line1" id="shipping_address_line1" value="{{ old('shipping_address_line1') }}" class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" {{ Auth::check() && count($savedAddresses) > 0 && $selectedAddressId ? '' : 'required' }}>
                            @error('shipping_address_line1')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                        <div class="mb-4">
                            <label for="shipping_address_line2" class="block text-sm font-medium text-gray-700 mb-1">{{ is_rtl() ? 'العنوان السطر 2 (اختياري)' : 'Address Line 2 (Optional)' }}</label>
                            <input type="text" name="shipping_address_line2" id="shipping_address_line2" value="{{ old('shipping_address_line2') }}" class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="shipping_city" class="block text-sm font-medium text-gray-700 mb-1">{{ is_rtl() ? 'المدينة' : 'City' }}</label>
                                <input type="text" name="shipping_city" id="shipping_city" value="{{ old('shipping_city') }}" class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" {{ Auth::check() && count($savedAddresses) > 0 && $selectedAddressId ? '' : 'required' }}>
                                @error('shipping_city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                            @if($requireState)
                            <div>
                                <label for="shipping_state" class="block text-sm font-medium text-gray-700 mb-1">{{ is_rtl() ? 'المحافظة/المقاطعة' : 'State/Province' }}</label>
                                <input type="text" name="shipping_state" id="shipping_state" value="{{ old('shipping_state') }}" class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" {{ ($requireState && !(Auth::check() && count($savedAddresses) > 0 && $selectedAddressId)) ? 'required' : '' }}>
                                @error('shipping_state')
                                    <p class="mt-1 text-sm text-red-600">{{ $message ?? 'Invalid state/province' }}</p>
                                    @enderror
                            </div>
                            @else
                                <input type="hidden" name="shipping_state" value="">
                            @endif
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($requirePostalCode)
                            <div>
                                <label for="shipping_postal_code" class="block text-sm font-medium text-gray-700 mb-1">{{ is_rtl() ? 'الرمز البريدي' : 'Postal Code' }}</label>
                                <input type="text" name="shipping_postal_code" id="shipping_postal_code" value="{{ old('shipping_postal_code') }}" class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" {{ ($requirePostalCode && !(Auth::check() && count($savedAddresses) > 0 && $selectedAddressId)) ? 'required' : '' }}>
                                @error('shipping_postal_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message ?? 'Invalid postal code' }}</p>
                                @enderror
                    </div>
                            @else
                                <input type="hidden" name="shipping_postal_code" value="">
                            @endif
                    
                            <div class="{{ $requirePostalCode ? '' : 'md:col-span-2' }}">
                                <label for="shipping_country" class="block text-sm font-medium text-gray-700 mb-1">{{ is_rtl() ? 'البلد' : 'Country' }}</label>
                                <select name="shipping_country" id="shipping_country" class="form-select block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" {{ Auth::check() && count($savedAddresses) > 0 && $selectedAddressId ? '' : 'required' }}>
                                    @foreach($shippingCountries as $country)
                                    <option value="{{ $country }}" {{ old('shipping_country', $defaultCountry) == $country ? 'selected' : '' }}>
                                        {{ \App\Helpers\CountryHelper::getCountryName($country) }}
                                    </option>
                                @endforeach
                                </select>
                                @error('shipping_country')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        @if(Auth::check() && count($savedAddresses) > 0)
                        </div>
                        @endif
                        
                        @if(Auth::check())
                        <div class="mt-4 {{ Auth::check() && count($savedAddresses) > 0 && $selectedAddressId ? 'hidden' : '' }}" id="save-address-section">
                            <div class="flex items-center">
                                <input type="checkbox" name="save_address" id="save_address" value="1" class="h-4 w-4 text-accent focus:ring-accent border-gray-300 rounded">
                                <label for="save_address" class="ml-2 block text-sm text-gray-700">
                                    {{ is_rtl() ? 'حفظ هذا العنوان للطلبات المستقبلية' : 'Save this address for future orders' }}
                                </label>
                            </div>
                            
                            <div class="mt-2 hidden" id="address-name-section">
                                <label for="address_name" class="block text-sm font-medium text-gray-700 mb-1">{{ is_rtl() ? 'اسم العنوان (مثل المنزل، العمل)' : 'Address Name (e.g. Home, Work)' }}</label>
                                <input type="text" name="address_name" id="address_name" class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Shipping Methods Section -->
                    @include('checkout.shipping-methods')
                    
                    <!-- Payment Method -->
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ is_rtl() ? 'طريقة الدفع' : 'Payment Method' }}</h2>
                        
                        <div class="space-y-4">
                            @forelse($paymentMethods as $method)
                                <div class="relative p-4 border border-gray-200 rounded-lg transition-all duration-200 {{ old('payment_method', $paymentMethod) == $method->id ? 'border-primary bg-primary-50' : 'hover:border-gray-300' }}">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5 mt-1">
                                            <input id="payment-{{ $method->id }}" name="payment_method" type="radio" value="{{ $method->id }}" class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded cursor-pointer payment-method-radio" {{ old('payment_method', $paymentMethod) == $method->id ? 'checked' : '' }}>
                                    </div>
                                        <div class="ml-3 flex-grow">
                                        <label for="payment-{{ $method->id }}" class="font-medium text-gray-700 cursor-pointer">{{ $method->name }}</label>
                                            <p class="text-sm text-gray-500">{{ $method->description }}</p>
                                        
                                            <!-- Payment method specific details -->
                                            @if($method->id == 'stripe')
                                                <div class="flex items-center mt-2">
                                                    <span class="w-8 h-5 bg-blue-500 rounded flex items-center justify-center mr-2">
                                                        <span class="text-xs text-white font-bold">S</span>
                                                    </span>
                                                    <span class="text-sm text-blue-500 font-medium">Credit/Debit Card</span>
                                                </div>
                                            @endif
                                            
                                            @if($method->id == 'cod' && isset($method->fee) && $method->fee > 0)
                                                <p class="text-sm text-primary mt-1">{{ is_rtl() ? 'رسوم إضافية' : 'Additional fee' }}: {{ Settings::formatPrice($method->fee) }}</p>
                                            @endif
                                            
                                            @if(($method->id == 'instapay' || $method->id == 'vodafone') && isset($method->number) && $method->number)
                                                <p class="text-sm text-gray-600 mt-1">{{ is_rtl() ? 'رقم الدفع' : 'Payment Number' }}: <span class="font-medium">{{ $method->number }}</span></p>
                                            @endif

                                            @if($method->id == 'instapay')
                                                <div class="flex items-center mt-2">
                                                    <span class="w-8 h-5 bg-blue-600 rounded flex items-center justify-center mr-2">
                                                        <span class="text-xs text-white font-bold">I</span>
                                                    </span>
                                                    <span class="text-sm text-blue-600 font-medium">InstaPay</span>
                                                </div>
                                            @elseif($method->id == 'vodafone')
                                                <div class="flex items-center mt-2">
                                                    <span class="w-8 h-5 bg-red-600 rounded flex items-center justify-center mr-2">
                                                        <span class="text-xs text-white font-bold">V</span>
                                                    </span>
                                                    <span class="text-sm text-red-600 font-medium">Vodafone Cash</span>
                                                </div>
                                            @elseif($method->id == 'bank_transfer')
                                                <div class="payment-method-details mt-3 p-3 bg-gray-50 rounded-md border border-gray-100 {{ old('payment_method', $paymentMethod) == $method->id ? 'block' : 'hidden' }}">
                                                    <p class="text-sm font-medium text-gray-700">{{ is_rtl() ? 'تفاصيل الحساب المصرفي' : 'Bank Account Details' }}:</p>
                                                    <pre class="text-sm text-gray-600 mt-1 whitespace-pre-wrap">{{ $method->account_details }}</pre>
                                                    
                                                    <p class="text-sm font-medium text-gray-700 mt-2">{{ is_rtl() ? 'التعليمات' : 'Instructions' }}:</p>
                                                    <p class="text-sm text-gray-600">{{ $method->instructions }}</p>
                                                </div>
                                            @elseif($method->id == 'fawry')
                                                <div class="flex items-center mt-2">
                                                    <span class="w-8 h-5 bg-orange-500 rounded flex items-center justify-center mr-2">
                                                        <span class="text-xs text-white font-bold">F</span>
                                                    </span>
                                                    <span class="text-sm text-orange-500 font-medium">Fawry</span>
                                                </div>
                                                @if(isset($method->code) && $method->code)
                                                <p class="text-sm text-gray-600 mt-1">{{ is_rtl() ? 'كود فوري' : 'Fawry Code' }}: <span class="font-medium">{{ $method->code }}</span></p>
                                                @endif
                                            @elseif($method->id == 'stc_pay')
                                                <div class="flex items-center mt-2">
                                                    <span class="w-8 h-5 bg-purple-600 rounded flex items-center justify-center mr-2">
                                                        <span class="text-xs text-white font-bold">S</span>
                                                    </span>
                                                    <span class="text-sm text-purple-600 font-medium">STC Pay</span>
                                                </div>
                                                @if(isset($method->number) && $method->number)
                                                <p class="text-sm text-gray-600 mt-1">{{ is_rtl() ? 'رقم الدفع' : 'Payment Number' }}: <span class="font-medium">{{ $method->number }}</span></p>
                                                @endif
                                            @elseif($method->id == 'benefit_pay')
                                                <div class="flex items-center mt-2">
                                                    <span class="w-8 h-5 bg-green-600 rounded flex items-center justify-center mr-2">
                                                        <span class="text-xs text-white font-bold">B</span>
                                                    </span>
                                                    <span class="text-sm text-green-600 font-medium">Benefit Pay</span>
                                                </div>
                                                @if(isset($method->number) && $method->number)
                                                <p class="text-sm text-gray-600 mt-1">{{ is_rtl() ? 'رقم الدفع' : 'Payment Number' }}: <span class="font-medium">{{ $method->number }}</span></p>
                                                @endif
                                    @endif
                        </div>
                            </div>
                                </div>
                            @empty
                                <div class="p-4 border border-yellow-200 bg-yellow-50 rounded-md">
                                    <p class="text-sm text-yellow-800">{{ is_rtl() ? 'لا توجد طرق دفع متاحة' : 'No payment methods available' }}</p>
                                </div>
                            @endforelse
                        </div>
                        
                        @error('payment_method')
                            <p class="mt-2 text-sm text-red-600">{{ $message ?? 'Please select a payment method' }}</p>
                        @enderror

                        <!-- Stripe Payment Container -->
                        <div id="stripe-payment-container" class="mt-6 p-4 border border-gray-200 bg-white rounded-md {{ $paymentMethod === 'stripe' ? '' : 'hidden' }}">
                            <h3 class="text-md font-medium text-gray-700 mb-3">Enter Card Details</h3>
                            <div class="mb-4">
                                @if(empty(session('cart')))
                                    <div class="p-3 text-red-600 bg-red-50 rounded-md">
                                        Your cart is empty. Please add products before checkout.
                                    </div>
                                @else
                                    <div id="card-element" class="p-3 border border-gray-300 rounded-md bg-gray-50"></div>
                                    <div id="card-errors" class="mt-2 text-sm text-red-600 p-2 rounded-md bg-red-50 hidden"></div>
                                @endif
                            </div>
                            
                            @if(!empty(session('cart')))
                            <button id="stripe-submit" type="button" onclick="stripePayNowClicked()" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                Pay Now
                            </button>
                            
                            <button type="button" onclick="testButtonClick()" class="mt-2 w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
                                Test Button
                            </button>
                            @else
                            <a href="{{ route('cart.index') }}" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                Go to Cart
                            </a>
                            @endif
                        </div>

                        @if(count($paymentMethods) > 0 && ($paymentMethods[0]->id == 'instapay' || $paymentMethods[0]->id == 'vodafone'))
                            <div class="mt-4 p-4 border border-blue-100 bg-blue-50 rounded-md">
                                <p class="text-sm text-blue-800">
                                    {{ is_rtl() ? 'بعد إكمال طلبك، يرجى تحويل المبلغ الإجمالي إلى الرقم المقدم وتضمين رقم طلبك كمرجع.' : 'After completing your order, please transfer the total amount to the provided number and include your order number as a reference.' }}
                                </p>
                            </div>
                        @endif
                    </div>
            </div>
            
                <div class="lg:col-span-5">
            <!-- Order Summary -->
                    <div class="space-y-3 bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between">
                            <p class="text-gray-600">{{ is_rtl() ? 'المجموع الفرعي' : 'Subtotal' }}</p>
                            <p>{{ Settings::formatPrice($subtotal) }}</p>
                        </div>
                        
                        @if($couponDiscount > 0)
                        <div class="flex justify-between text-green-600">
                            <p>{{ is_rtl() ? 'الخصم' : 'Discount' }} ({{ $couponCode }})</p>
                            <p>-{{ Settings::formatPrice($couponDiscount) }}</p>
                        </div>
                        @endif
                        
                        <div class="flex justify-between">
                            <p class="text-gray-600">{{ is_rtl() ? 'الشحن' : 'Shipping' }}</p>
                            @if($shipping == 0 && $subtotal >= Settings::get('shipping_free_threshold', 50))
                            <p class="text-green-600">{{ is_rtl() ? 'مجاني' : 'Free' }}</p>
                            @else
                            <p id="shipping-cost">{{ Settings::formatPrice($shipping) }}</p>
                            @endif
                        </div>
                        <div class="flex justify-between">
                            <p class="text-gray-600">{{ is_rtl() ? 'رسوم الدفع' : 'Payment Fee' }}</p>
                            <p id="payment-fee-amount">{{ Settings::formatPrice($paymentMethod === 'cod' ? $cod_fee : 0) }}</p>
                        </div>
                        <div class="flex justify-between font-semibold border-t border-gray-200 pt-3">
                            <p>{{ is_rtl() ? 'المجموع' : 'Total' }}</p>
                            <p id="order-total">{{ Settings::formatPrice($total + ($paymentMethod === 'cod' ? $cod_fee : 0)) }}</p>
                        </div>
                    </div>
                    
                    <!-- Coupon Code -->
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <div class="flex space-x-2">
                            <input type="text" id="coupon_code" placeholder="{{ is_rtl() ? 'كود الخصم' : 'Coupon code' }}" class="form-input flex-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm" value="{{ $couponCode }}">
                            <button type="button" id="apply-coupon-btn" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                {{ is_rtl() ? 'تطبيق' : 'Apply' }}
                            </button>
                        </div>
                        <div id="coupon-message" class="mt-2 text-sm">
                            @if($couponCode)
                            <span class="text-green-600">{{ is_rtl() ? 'تم تطبيق الكوبون' : 'Coupon applied' }}: {{ $couponCode }}</span> 
                            <button type="button" id="remove-coupon-btn" class="text-red-500 hover:text-red-700 underline">{{ is_rtl() ? 'إزالة' : 'Remove' }}</button>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Product Details -->
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ is_rtl() ? 'عناصر الطلب' : 'Order Items' }}</h2>
                        
                        <div class="space-y-4">
                            @foreach($products as $item)
                            <div class="flex items-center pb-4 border-b border-gray-200 last:border-0 last:pb-0">
                                <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-md">
                                    @if($item['product']->featured_image)
                                        <img src="{{ asset('storage/' . $item['product']->featured_image) }}" alt="{{ $item['product']->name }}" class="h-full w-full object-cover object-center">
                                    @else
                                        <div class="h-full w-full bg-gray-200 flex items-center justify-center">
                                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4 flex-1 flex flex-col">
                                    <div>
                                        <div class="flex justify-between text-base font-medium text-gray-900">
                                            <h3>{{ $item['product']->name }}</h3>
                                            <p class="ml-4">{{ Settings::formatPrice($item['product']->price * $item['quantity']) }}</p>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">{{ $item['product']->category->name }}</p>
                                    </div>
                                    <div class="flex-1 flex items-end justify-between text-sm">
                                        <p class="text-gray-500">{{ __('Qty') }} {{ $item['quantity'] }}</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            
                            @foreach($offers as $item)
                            <div class="flex items-center pb-4 border-b border-gray-200 last:border-0 last:pb-0">
                                <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-md">
                                    @if($item['offer']->image)
                                        <img src="{{ asset('storage/' . str_replace('storage/', '', $item['offer']->image)) }}" alt="{{ $item['offer']->title }}" class="h-full w-full object-cover object-center">
                                    @else
                                        <div class="h-full w-full bg-gray-200 flex items-center justify-center">
                                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4 flex-1 flex flex-col">
                                    <div>
                                        <div class="flex justify-between text-base font-medium text-gray-900">
                                            <h3>
                                                {{ $item['offer']->title }}
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-accent text-white">
                                                    {{ __('Special Offer') }}
                                                </span>
                                            </h3>
                                            <p class="ml-4">{{ Settings::formatPrice($item['price'] * $item['quantity']) }}</p>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">{{ Str::limit($item['offer']->description, 50) }}</p>
                                    </div>
                                    <div class="flex-1 flex items-end justify-between text-sm">
                                        <p class="text-gray-500">{{ __('Qty') }} {{ $item['quantity'] }}</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Order details summary -->
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <div class="bg-gray-50 rounded-md p-3">
                            <h3 class="text-sm font-medium text-gray-700 mb-2">{{ is_rtl() ? 'تفاصيل الطلب' : 'Order Details' }}</h3>
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ is_rtl() ? 'العناصر' : 'Items' }}:</span>
                                    <span class="font-medium">{{ count($products) + count($offers) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ is_rtl() ? 'طريقة الشحن' : 'Shipping Method' }}:</span>
                                    <span class="font-medium">{{ ucfirst($selectedShippingMethod) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ is_rtl() ? 'طريقة الدفع' : 'Payment Method' }}:</span>
                                    <span class="font-medium" id="payment-method-display">{{ ucfirst($paymentMethod) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ is_rtl() ? 'العملة' : 'Currency' }}:</span>
                                    <span class="font-medium">{{ $currencyCode }}</span>
                                </div>
                                @if($shipping == 0 && $subtotal >= Settings::get('shipping_free_threshold', 50))
                                <div class="flex justify-between text-green-600">
                                    <span>{{ is_rtl() ? 'حد الشحن المجاني' : 'Free Shipping Threshold' }}:</span>
                                    <span class="font-medium">{{ Settings::formatPrice(Settings::get('shipping_free_threshold', 50)) }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" id="main-checkout-button">
                            {{ is_rtl() ? 'إتمام الطلب' : 'Place Order' }}
                        </button>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <p class="text-xs text-gray-500">By placing your order, you agree to our <a href="#" class="text-primary hover:text-primary-dark">{{ is_rtl() ? 'شروط الخدمة' : 'Terms of Service' }}</a> and <a href="#" class="text-primary hover:text-primary-dark">{{ is_rtl() ? 'سياسة الخصوصية' : 'Privacy Policy' }}</a>.</p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<!-- Add any checkout-specific styles here -->
@endpush

<!-- Add jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

@section('scripts')
    <!-- Stripe already loaded in the head section -->
@endsection 

<script>
// Add form submission handler
document.addEventListener('DOMContentLoaded', function() {
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            // Only handle non-Stripe submissions
            if (document.querySelector('input[name="payment_method"]:checked')?.value !== 'stripe') {
                e.preventDefault();
                
                // Disable the submit button to prevent double submission
                const submitButton = document.getElementById('main-checkout-button');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.textContent = 'Processing...';
                }
                
                // Submit the form via AJAX
                const formData = new FormData(this);
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'An error occurred during checkout');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.order_id) {
                        // Redirect to success page
                        window.location.href = '/checkout/success/' + data.order_id;
                    } else {
                        throw new Error(data.message || 'An error occurred during checkout');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Re-enable the submit button
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = '{{ is_rtl() ? 'إتمام الطلب' : 'Place Order' }}';
                    }
                    
                    // Show error message
                    alert(error.message || 'An error occurred during checkout. Please try again.');
                });
            }
        });
    }
});
</script> 

@push('scripts')
<!-- Coupon handling script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Coupon application functionality
    const applyCouponBtn = document.getElementById('apply-coupon-btn');
    const removeCouponBtn = document.getElementById('remove-coupon-btn');
    const couponInput = document.getElementById('coupon_code');
    const couponMessage = document.getElementById('coupon-message');
    
    if (applyCouponBtn && couponInput) {
        applyCouponBtn.addEventListener('click', function() {
            const couponCode = couponInput.value.trim();
            if (!couponCode) {
                showCouponMessage('Please enter a coupon code', 'error');
                return;
            }
            
            // Disable button and show loading state
            applyCouponBtn.disabled = true;
            applyCouponBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Applying...';
            
            // Send AJAX request to apply coupon
            fetch('{{ route('checkout.apply-coupon') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ coupon_code: couponCode })
            })
            .then(response => response.json())
            .then(data => {
                // Reset button state
                applyCouponBtn.disabled = false;
                applyCouponBtn.innerHTML = '{{ is_rtl() ? 'تطبيق' : 'Apply' }}';
                
                if (data.success) {
                    // Update UI with discount
                    updateOrderSummary(data);
                    
                    // Show success message
                    let message = '{{ is_rtl() ? 'تم تطبيق الكوبون' : 'Coupon applied' }}: ' + data.coupon_code;
                    
                    // Add restrictions info if applicable
                    if (data.has_restrictions) {
                        message += '<div class="mt-1 text-xs text-blue-600">This coupon only applies to specific products</div>';
                        
                        // Mark eligible products in the cart
                        markEligibleProducts(data.eligible_products, data.eligible_categories);
                    }
                    
                    showCouponMessage(message, 'success', true);
                } else {
                    // Show error message
                    showCouponMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error applying coupon:', error);
                applyCouponBtn.disabled = false;
                applyCouponBtn.innerHTML = '{{ is_rtl() ? 'تطبيق' : 'Apply' }}';
                showCouponMessage('An error occurred. Please try again.', 'error');
            });
        });
    }
    
    // Remove coupon functionality
    if (removeCouponBtn) {
        removeCouponBtn.addEventListener('click', function() {
            // Disable button and show loading state
            removeCouponBtn.disabled = true;
            
            // Send AJAX request to remove coupon
            fetch('{{ route('checkout.remove-coupon') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear coupon input
                    if (couponInput) couponInput.value = '';
                    
                    // Update UI
                    updateOrderSummary(data);
                    showCouponMessage('', '');
                    
                    // Remove eligible product markings
                    removeEligibleProductMarkings();
                } else {
                    removeCouponBtn.disabled = false;
                    showCouponMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error removing coupon:', error);
                removeCouponBtn.disabled = false;
                showCouponMessage('An error occurred. Please try again.', 'error');
            });
        });
    }
    
    // Helper function to update order summary
    function updateOrderSummary(data) {
        // Update subtotal
        const subtotalElement = document.getElementById('order-subtotal');
        if (subtotalElement) {
            subtotalElement.textContent = '{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }} ' + data.subtotal.toFixed(2);
        }
        
        // Update discount row
        const discountRow = document.getElementById('discount-row');
        const discountValue = document.getElementById('discount-value');
        
        if (discountRow && discountValue) {
            if (data.discount > 0) {
                discountRow.classList.remove('hidden');
                discountValue.textContent = '-{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }} ' + data.discount.toFixed(2);
            } else {
                discountRow.classList.add('hidden');
            }
        }
        
        // Update shipping cost
        const shippingCostElement = document.getElementById('shipping-cost');
        if (shippingCostElement && data.shipping !== undefined) {
            shippingCostElement.textContent = '{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }} ' + data.shipping.toFixed(2);
        }
        
        // Update total
        const orderTotalElement = document.getElementById('order-total');
        if (orderTotalElement) {
            orderTotalElement.textContent = '{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }} ' + data.total.toFixed(2);
        }
    }
    
    // Helper function to show coupon messages
    function showCouponMessage(message, type, showRemoveButton = false) {
        if (!couponMessage) return;
        
        if (!message) {
            couponMessage.innerHTML = '';
            return;
        }
        
        let html = '';
        
        if (type === 'success') {
            html = '<span class="text-green-600">' + message + '</span>';
            
            if (showRemoveButton) {
                html += ' <button type="button" id="remove-coupon-btn" class="text-red-500 hover:text-red-700 underline">{{ is_rtl() ? 'إزالة' : 'Remove' }}</button>';
            }
        } else if (type === 'error') {
            html = '<span class="text-red-600">' + message + '</span>';
        }
        
        couponMessage.innerHTML = html;
        
        // Re-attach event listener to the new remove button
        const newRemoveBtn = document.getElementById('remove-coupon-btn');
        if (newRemoveBtn) {
            newRemoveBtn.addEventListener('click', function() {
                this.disabled = true;
                
                fetch('{{ route('checkout.remove-coupon') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (couponInput) couponInput.value = '';
                        updateOrderSummary(data);
                        showCouponMessage('', '');
                        removeEligibleProductMarkings();
                    } else {
                        this.disabled = false;
                        showCouponMessage(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error removing coupon:', error);
                    this.disabled = false;
                });
            });
        }
    }
    
    // Mark eligible products in the cart
    function markEligibleProducts(eligibleProductIds, eligibleCategoryIds) {
        // Remove any existing markings first
        removeEligibleProductMarkings();
        
        // Exit if no eligible products/categories
        if ((!eligibleProductIds || !eligibleProductIds.length) && 
            (!eligibleCategoryIds || !eligibleCategoryIds.length)) {
            return;
        }
        
        // Get all product items in the cart
        const productItems = document.querySelectorAll('.flex.items-center.pb-4.border-b');
        
        productItems.forEach(item => {
            // Try to find product ID in the item (might need to adjust based on your HTML structure)
            const productIdAttr = item.getAttribute('data-product-id');
            const categoryIdAttr = item.getAttribute('data-category-id');
            
            let isEligible = false;
            
            // Check if product ID is in eligible products
            if (productIdAttr && eligibleProductIds && eligibleProductIds.includes(parseInt(productIdAttr))) {
                isEligible = true;
            }
            
            // Check if category ID is in eligible categories
            if (!isEligible && categoryIdAttr && eligibleCategoryIds && eligibleCategoryIds.includes(parseInt(categoryIdAttr))) {
                isEligible = true;
            }
            
            // Mark eligible items
            if (isEligible) {
                const badge = document.createElement('div');
                badge.className = 'coupon-eligible-badge absolute top-0 right-0 bg-green-500 text-white text-xs px-2 py-1 rounded-full';
                badge.textContent = 'Coupon Applied';
                
                // Make sure the container is positioned relatively
                if (getComputedStyle(item).position === 'static') {
                    item.style.position = 'relative';
                }
                
                item.appendChild(badge);
            }
        });
    }
    
    // Remove eligible product markings
    function removeEligibleProductMarkings() {
        const badges = document.querySelectorAll('.coupon-eligible-badge');
        badges.forEach(badge => badge.remove());
    }
});
</script>
@endpush 