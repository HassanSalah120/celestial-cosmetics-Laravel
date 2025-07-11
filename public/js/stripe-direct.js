/**
 * Stripe Direct Payment Processing
 * This file handles Stripe payment processing directly from global scope
 */

// Global variables for Stripe processing
var globalStripe = null;
var globalCard = null;

// Global function that can be called directly from buttons
function stripePayNowClicked() {
    console.log('stripePayNowClicked called directly');
    alert('Pay Now button clicked via global handler');
    
    const stripeSubmitBtn = document.getElementById('stripe-submit');
    const cardErrors = document.getElementById('card-errors');
    const checkoutForm = document.getElementById('checkout-form');
    
    if (!globalStripe || !globalCard) {
        const errorMsg = 'Payment system is not initialized. Please refresh and try again.';
        console.error(errorMsg);
        alert(errorMsg);
        if (cardErrors) {
            cardErrors.textContent = errorMsg;
            cardErrors.style.display = 'block';
        }
        return;
    }
    
    // Set loading state
    if (stripeSubmitBtn) {
        stripeSubmitBtn.disabled = true;
        stripeSubmitBtn.textContent = 'Processing...';
    }
    
    // Create form data from checkout form
    const formData = new FormData(checkoutForm);
    formData.append('payment_method', 'stripe');
    
    console.log('Form data created, submitting order');
    
    // Create order first
    fetch(checkoutForm.action, {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Order creation response received', response);
        if (!response.ok) {
            throw new Error('Server error while creating order');
        }
        return response.json();
    })
    .then(orderData => {
        console.log('Order data received:', orderData);
        
        if (!orderData.success) {
            throw new Error(orderData.message || 'Could not create order');
        }
        
        console.log('Order created successfully with ID:', orderData.order_id);
        
        // Now create payment intent
        console.log('Creating payment intent for order:', orderData.order_id);
        return fetch('/checkout/stripe/create-payment-intent', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ order_id: orderData.order_id })
        }).then(response => {
            console.log('Payment intent response received', response);
            if (!response.ok) {
                throw new Error('Could not create payment intent');
            }
            return response.json().then(paymentData => {
                console.log('Payment intent data:', paymentData);
                return { orderData, paymentData };
            });
        });
    })
    .then(({ orderData, paymentData }) => {
        if (!paymentData.clientSecret) {
            throw new Error('No client secret received');
        }
        
        console.log('Confirming card payment with secret');
        
        // Confirm card payment
        return globalStripe.confirmCardPayment(paymentData.clientSecret, {
            payment_method: {
                card: globalCard,
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
            console.log('Payment confirmation result:', result);
            
            if (result.error) {
                throw result.error;
            }
            
            console.log('Payment confirmed successfully, redirecting to:', orderData.redirect_url);
            
            // Payment successful, redirect to success page
            window.location.href = orderData.redirect_url;
        });
    })
    .catch(error => {
        console.error('Payment error:', error);
        alert('Payment error: ' + error.message);
        if (cardErrors) {
            cardErrors.textContent = error.message || 'An error occurred during payment processing';
            cardErrors.style.display = 'block';
        }
        
        // Reset button
        if (stripeSubmitBtn) {
            stripeSubmitBtn.disabled = false;
            stripeSubmitBtn.textContent = 'Pay Now';
        }
    });
}

// Initialize Stripe when the document is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Stripe Direct: DOM content loaded');
    
    // Get Stripe key from meta tag
    const stripeKey = document.querySelector('meta[name="stripe-key"]')?.getAttribute('content');
    console.log('Stripe key found:', stripeKey);
    
    // Get DOM elements
    const stripeContainer = document.getElementById('stripe-payment-container');
    const cardElement = document.getElementById('card-element');
    const cardErrors = document.getElementById('card-errors');
    const stripeSubmitBtn = document.getElementById('stripe-submit');
    const checkoutForm = document.getElementById('checkout-form');
    const mainCheckoutBtn = document.getElementById('main-checkout-button');
    
    console.log('DOM elements found:', {
        stripeContainer: !!stripeContainer,
        cardElement: !!cardElement,
        cardErrors: !!cardErrors,
        stripeSubmitBtn: !!stripeSubmitBtn,
        checkoutForm: !!checkoutForm,
        mainCheckoutBtn: !!mainCheckoutBtn
    });
    
    // Payment method selection
    const paymentMethodRadios = document.querySelectorAll('input[name="payment_method"]');
    console.log('Payment radios found:', paymentMethodRadios.length);
    
    paymentMethodRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            console.log('Payment method changed to:', this.value);
            if (this.value === 'stripe') {
                initializeStripe();
                if (stripeContainer) stripeContainer.style.display = 'block';
                if (mainCheckoutBtn) mainCheckoutBtn.style.display = 'none';
            } else {
                if (stripeContainer) stripeContainer.style.display = 'none';
                if (mainCheckoutBtn) mainCheckoutBtn.style.display = 'flex';
            }
        });
    });
    
    // Check if Stripe is already selected
    const selectedPayment = document.querySelector('input[name="payment_method"]:checked');
    console.log('Selected payment method:', selectedPayment?.value);
    
    if (selectedPayment && selectedPayment.value === 'stripe') {
        console.log('Stripe is pre-selected, initializing');
        initializeStripe();
        if (mainCheckoutBtn) mainCheckoutBtn.style.display = 'none';
    }
    
    // Initialize Stripe
    function initializeStripe() {
        console.log('initializeStripe called');
        if (!globalStripe && stripeKey) {
            console.log('Creating Stripe instance with key:', stripeKey);
            
            try {
                globalStripe = Stripe(stripeKey);
                console.log('Stripe instance created successfully');
                
                const elements = globalStripe.elements();
                console.log('Stripe elements created');
                
                // Create card element
                globalCard = elements.create('card', {
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
                console.log('Card element created');
                
                // Mount card element
                if (cardElement) {
                    globalCard.mount('#card-element');
                    console.log('Card element mounted successfully');
                } else {
                    console.error('Card element mount point not found');
                }
                
                // Handle card errors
                globalCard.addEventListener('change', function(event) {
                    console.log('Card input changed', event);
                    if (event.error) {
                        showError(event.error.message);
                    } else {
                        clearError();
                    }
                });
                
                console.log('Stripe initialized successfully');
            } catch (error) {
                console.error('Stripe initialization error:', error);
                alert('Stripe initialization error: ' + error.message);
                showError('Could not initialize payment form: ' + error.message);
            }
        } else {
            console.log('Stripe already initialized or key missing');
        }
    }
    
    // Show error message
    function showError(message) {
        console.log('Showing error:', message);
        if (cardErrors) {
            cardErrors.textContent = message;
            cardErrors.style.display = 'block';
        }
    }
    
    // Clear error message
    function clearError() {
        if (cardErrors) {
            cardErrors.textContent = '';
            cardErrors.style.display = 'none';
        }
    }
}); 