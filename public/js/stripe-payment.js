/**
 * Stripe Payment Processing for Celestial Cosmetics
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Stripe script loaded');
    
    // Global variables
    let stripe = null;
    let cardElement = null;
    
    // Get DOM elements
    const stripeContainer = document.getElementById('stripe-payment-container');
    const stripeButton = document.getElementById('stripe-payment-button');
    const cardElementContainer = document.getElementById('stripe-card-element');
    const errorElement = document.getElementById('stripe-card-errors');
    const mainCheckoutButton = document.getElementById('main-checkout-button');
    
    // Initialize Stripe if the payment method is selected
    initializeStripeIfSelected();
    
    // Add event listeners to payment method radios
    const paymentMethodRadios = document.querySelectorAll('input[name="payment_method"]');
    if (paymentMethodRadios && paymentMethodRadios.length > 0) {
        paymentMethodRadios.forEach(radio => {
            radio.addEventListener('change', handlePaymentMethodChange);
        });
    }
    
    // Add click handler to the Stripe payment button
    if (stripeButton) {
        stripeButton.addEventListener('click', processStripePayment);
    }
    
    /**
     * Initialize Stripe if it's the selected payment method
     */
    function initializeStripeIfSelected() {
        const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked');
        if (selectedPaymentMethod && selectedPaymentMethod.value === 'stripe') {
            console.log('Stripe is selected, initializing...');
            
            // Show Stripe container and hide main checkout button
            if (stripeContainer) stripeContainer.style.display = 'block';
            if (mainCheckoutButton) mainCheckoutButton.style.display = 'none';
            
            // Initialize Stripe
            initializeStripe();
        }
    }
    
    /**
     * Handle payment method change
     */
    function handlePaymentMethodChange() {
        const selectedMethod = this.value;
        console.log('Payment method changed to:', selectedMethod);
        
        if (selectedMethod === 'stripe') {
            // Show Stripe container and hide main checkout button
            if (stripeContainer) stripeContainer.style.display = 'block';
            if (mainCheckoutButton) mainCheckoutButton.style.display = 'none';
            
            // Initialize Stripe if not already initialized
            if (!stripe || !cardElement) {
                initializeStripe();
            }
        } else {
            // Hide Stripe container and show main checkout button
            if (stripeContainer) stripeContainer.style.display = 'none';
            if (mainCheckoutButton) mainCheckoutButton.style.display = 'flex';
        }
    }
    
    /**
     * Initialize Stripe and create card element
     */
    function initializeStripe() {
        // Find the Stripe key
        const stripeKey = document.querySelector('meta[name="stripe-key"]')?.getAttribute('content');
        
        if (!stripeKey) {
            console.error('Stripe key not found');
            showError('Stripe API key is missing. Please contact support.');
            return;
        }
        
        try {
            // Create Stripe instance
            stripe = Stripe(stripeKey);
            
            // Create Elements instance
            const elements = stripe.elements();
            
            // Create and mount the card element
            cardElement = elements.create('card', {
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
            
            if (cardElementContainer) {
                cardElement.mount(cardElementContainer);
                
                // Listen for errors
                cardElement.on('change', function(event) {
                    if (event.error) {
                        showError(event.error.message);
                    } else {
                        clearError();
                    }
                });
            } else {
                console.error('Card element container not found');
            }
        } catch (error) {
            console.error('Error initializing Stripe:', error);
            showError('Failed to initialize payment form: ' + error.message);
        }
    }
    
    /**
     * Process the Stripe payment
     */
    function processStripePayment(event) {
        if (event) event.preventDefault();
        
        if (!stripe || !cardElement) {
            showError('Payment processing is not available. Please refresh the page and try again.');
            return;
        }
        
        // Disable the button and show loading state
        if (stripeButton) {
            stripeButton.disabled = true;
            stripeButton.textContent = 'Processing...';
        }
        
        // Get the checkout form
        const checkoutForm = document.getElementById('checkout-form');
        if (!checkoutForm) {
            showError('Checkout form not found');
            resetButton();
            return;
        }
        
        // Create FormData from the checkout form
        const formData = new FormData(checkoutForm);
        formData.append('payment_method', 'stripe');
        
        // Submit the order to create it in the database
        fetch(checkoutForm.getAttribute('action'), {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error creating order');
            }
            return response.json();
        })
        .then(orderData => {
            if (!orderData.success || !orderData.order_id) {
                throw new Error(orderData.message || 'Invalid order response');
            }
            
            // Create payment intent
            return createPaymentIntent(orderData.order_id)
                .then(paymentIntentData => ({ orderData, paymentIntentData }));
        })
        .then(({ orderData, paymentIntentData }) => {
            // Confirm card payment
            return confirmCardPayment(paymentIntentData.clientSecret, orderData);
        })
        .catch(error => {
            console.error('Payment error:', error);
            showError(error.message || 'An error occurred during payment. Please try again.');
            resetButton();
        });
    }
    
    /**
     * Create a payment intent for the order
     */
    function createPaymentIntent(orderId) {
        return fetch('/checkout/stripe/create-payment-intent', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ order_id: orderId })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error creating payment intent');
            }
            return response.json();
        })
        .then(data => {
            if (!data.clientSecret) {
                throw new Error('No client secret received');
            }
            return data;
        });
    }
    
    /**
     * Confirm the card payment with Stripe
     */
    function confirmCardPayment(clientSecret, orderData) {
        // Get the form data
        const form = document.getElementById('checkout-form');
        const formData = new FormData(form);
        
        return stripe.confirmCardPayment(clientSecret, {
            payment_method: {
                card: cardElement,
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
        })
        .then(result => {
            if (result.error) {
                throw result.error;
            }
            
            // Payment succeeded, redirect to success page
            window.location.href = orderData.redirect_url;
            return result;
        });
    }
    
    /**
     * Show an error message
     */
    function showError(message) {
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }
    
    /**
     * Clear the error message
     */
    function clearError() {
        if (errorElement) {
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }
    }
    
    /**
     * Reset the payment button
     */
    function resetButton() {
        if (stripeButton) {
            stripeButton.disabled = false;
            stripeButton.textContent = 'Pay Now';
        }
    }
}); 