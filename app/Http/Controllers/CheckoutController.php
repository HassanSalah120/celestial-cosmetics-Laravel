<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCheckoutRequest;
use App\Models\Offer;
use App\Services\OrderService;
use App\Services\ShippingService;
use App\Services\MarketingService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;
use App\Helpers\SettingsHelper;
use App\Helpers\TranslationHelper;

class CheckoutController extends Controller
{
    protected $orderService;
    protected $shippingService;
    protected $marketingService;
    protected $stripeService;

    public function __construct(
        OrderService $orderService,
        ShippingService $shippingService,
        MarketingService $marketingService,
        StripeService $stripeService
    ) {
        $this->orderService = $orderService;
        $this->shippingService = $shippingService;
        $this->marketingService = $marketingService;
        $this->stripeService = $stripeService;
    }

    /**
     * Display the checkout page with cart items
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get cart items
        $cart = session()->get('cart', []);
        $products = [];
        $offers = [];
        $total = 0;
        $subtotal = 0;

        // If cart is empty, redirect to cart page
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty. Please add products before checkout.');
        }

        // Get product details and calculate total
        foreach ($cart as $id => $details) {
            // Check if this is an offer
            if (isset($details['type']) && $details['type'] === 'offer') {
                $offer = \App\Models\Offer::find($details['offer_id']);
                if ($offer) {
                    $offers[] = [
                        'offer' => $offer,
                        'quantity' => $details['quantity'],
                        'price' => $details['price'] ?? ($offer->discounted_price ?? $offer->original_price)
                    ];
                    $subtotal += ($details['price'] ?? ($offer->discounted_price ?? $offer->original_price)) * $details['quantity'];
                }
            } else {
                // Regular product
                $product = Product::find($id);
                if ($product) {
                    $products[] = [
                        'product' => $product,
                        'quantity' => $details['quantity']
                    ];
                    $subtotal += $product->price * $details['quantity'];
                }
            }
        }
        
        // Get selected shipping method or default
        $selectedShippingMethod = session()->get('shipping_method', 'standard');
        
        // Get selected payment method or default
        $paymentMethod = session()->get('payment_method', 'instapay');
        
        // Get shipping country (defaulting to the one from settings if not set)
        $defaultCountry = \App\Helpers\SettingsHelper::get('default_country', 'EG');
        $shippingCountry = session()->get('shipping_country', $defaultCountry);
        
        // Get shipping form field requirements
        $requireState = (bool)\App\Helpers\SettingsHelper::get('require_state', false);
        $requirePostalCode = (bool)\App\Helpers\SettingsHelper::get('require_postal_code', false);
        
        // Calculate shipping based on cart subtotal and selected method
        $shipping = $this->shippingService->calculateShippingFee($subtotal, $shippingCountry, $selectedShippingMethod);
        
        // Get COD fee from settings or PaymentConfig
        $cod_fee = 0;
        try {
            if (Schema::hasTable('payment_configs')) {
                $paymentConfig = \App\Models\PaymentConfig::first();
                if ($paymentConfig) {
                    $cod_fee = $paymentConfig->cod_fee;
                }
            } else {
                $cod_fee = floatval(\App\Helpers\SettingsHelper::get('cod_fee', '0'));
            }
        } catch (\Exception $e) {
            $cod_fee = floatval(\App\Helpers\SettingsHelper::get('cod_fee', '0'));
        }
        
        // Get coupon discount if applied
        $couponDiscount = 0;
        $couponCode = session()->get('coupon_code', null);
        
        if ($couponCode) {
            $coupon = $this->marketingService->getCouponByCode($couponCode);
            if ($coupon) {
                $couponDiscount = $coupon->calculateDiscount($subtotal);
            } else {
                // If coupon is no longer valid, remove it from session
                session()->forget('coupon_code');
            }
        }
        
        // Calculate total with discounts and applicable fees
        $total = $subtotal - $couponDiscount + $shipping;
        // Cash on delivery fee is calculated separately in the view

        // Get user details if logged in
        $user = Auth::user();
        
        // Get user's saved addresses if authenticated
        $savedAddresses = [];
        $selectedAddressId = session()->get('selected_address_id');
        
        if (Auth::check()) {
            $savedAddresses = Auth::user()->addresses()->get();
            
            // If no address is selected but user has addresses, select the default one
            if (!$selectedAddressId && $savedAddresses->count() > 0) {
                $defaultAddress = $savedAddresses->where('is_default', true)->first();
                if ($defaultAddress) {
                    $selectedAddressId = $defaultAddress->id;
                    session()->put('selected_address_id', $selectedAddressId);
                }
            }
        }

        // Get available shipping methods
        $shippingMethods = $this->shippingService->getAvailableShippingMethods($subtotal, $shippingCountry);
        
        // Get payment methods dynamically from PaymentConfig or fallback to defaults
        $paymentMethods = [];
        try {
            if (Schema::hasTable('payment_configs')) {
                $paymentConfig = \App\Models\PaymentConfig::first();
                
                if ($paymentConfig) {
                    // Debug Stripe configuration
                    \Illuminate\Support\Facades\Log::info('Stripe configuration', [
                        'enable_stripe' => $paymentConfig->enable_stripe ?? false,
                        'has_stripe_publishable_key' => !empty($paymentConfig->stripe_publishable_key),
                        'stripe_key_length' => $paymentConfig->stripe_publishable_key ? strlen($paymentConfig->stripe_publishable_key) : 0
                    ]);
                    
                    if ($paymentConfig->enable_cash_on_delivery) {
                        $paymentMethods[] = (object) [
                            'id' => 'cod',
                            'name' => TranslationHelper::get('cash_on_delivery', 'Cash on Delivery'),
                            'description' => TranslationHelper::get('cod_description', 'Pay with cash when your order is delivered'),
                            'fee' => $paymentConfig->cod_fee
                        ];
                    }
                    
                    if ($paymentConfig->enable_instapay) {
                        $paymentMethods[] = (object) [
                            'id' => 'instapay',
                            'name' => 'InstaPay',
                            'description' => TranslationHelper::get('instapay_description', 'Pay securely using InstaPay'),
                            'number' => $paymentConfig->instapay_number
                        ];
                    }
                    
                    if ($paymentConfig->enable_vodafone_cash) {
                        $paymentMethods[] = (object) [
                            'id' => 'vodafone',
                            'name' => 'Vodafone Cash',
                            'description' => TranslationHelper::get('vodafone_cash_description', 'Pay using Vodafone Cash'),
                            'number' => $paymentConfig->vodafone_cash_number
                        ];
                    }
                    
                    // Gulf & MENA region payment methods
                    if ($paymentConfig->enable_bank_transfer) {
                        $paymentMethods[] = (object) [
                            'id' => 'bank_transfer',
                            'name' => TranslationHelper::get('bank_transfer', 'Bank Transfer'),
                            'description' => TranslationHelper::get('bank_transfer_description', 'Pay by transferring funds to our bank account'),
                            'instructions' => $paymentConfig->bank_transfer_instructions,
                            'account_details' => $paymentConfig->bank_account_details
                        ];
                    }
                    
                    if ($paymentConfig->enable_fawry) {
                        $paymentMethods[] = (object) [
                            'id' => 'fawry',
                            'name' => 'Fawry',
                            'description' => TranslationHelper::get('fawry_description', 'Pay using Fawry at any service point'),
                            'code' => $paymentConfig->fawry_code
                        ];
                    }
                    
                    if ($paymentConfig->enable_stc_pay) {
                        $paymentMethods[] = (object) [
                            'id' => 'stc_pay',
                            'name' => 'STC Pay',
                            'description' => TranslationHelper::get('stc_pay_description', 'Pay using STC Pay mobile wallet'),
                            'number' => $paymentConfig->stc_pay_number
                        ];
                    }
                    
                    if ($paymentConfig->enable_benefit_pay) {
                        $paymentMethods[] = (object) [
                            'id' => 'benefit_pay',
                            'name' => 'Benefit Pay',
                            'description' => TranslationHelper::get('benefit_pay_description', 'Pay using Benefit Pay in Bahrain'),
                            'number' => $paymentConfig->benefit_pay_number
                        ];
                    }
                    
                    // Add Stripe payment method if enabled
                    if ($paymentConfig->enable_stripe) {
                        $paymentMethods[] = (object) [
                            'id' => 'stripe',
                            'name' => 'Credit / Debit Card',
                            'description' => TranslationHelper::get('stripe_description', 'Pay securely with your credit or debit card'),
                            'publishable_key' => $paymentConfig->stripe_publishable_key
                        ];
                    }
                }
            } 
        } catch (\Exception $e) {
            // Fallback if there's an error
        }
        
        // Use default payment methods if none found
        if (empty($paymentMethods)) {
            $paymentMethods = [
                (object) [
                    'id' => 'cod',
                    'name' => TranslationHelper::get('cash_on_delivery', 'Cash on Delivery'),
                    'description' => TranslationHelper::get('cod_description', 'Pay with cash when your order is delivered'),
                    'fee' => $cod_fee
                ],
                (object) [
                    'id' => 'instapay',
                    'name' => 'InstaPay',
                    'description' => TranslationHelper::get('instapay_description', 'Pay securely using InstaPay')
                ],
                (object) [
                    'id' => 'vodafone',
                    'name' => 'Vodafone Cash',
                    'description' => TranslationHelper::get('vodafone_cash_description', 'Pay using Vodafone Cash')
                ]
            ];
        }
        
        // Get currency information
        $currencySymbol = \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP');
        $currencyCode = \App\Helpers\SettingsHelper::get('currency', 'EGP');
        
        // Get available shipping countries
        $shippingCountriesStr = \App\Helpers\SettingsHelper::get('shipping_countries', 'US, EG, UK, CA, AU');
        $shippingCountries = array_map('trim', explode(',', $shippingCountriesStr));
        
        // Get country names for display
        $countries = \App\Helpers\CountryHelper::getCountries();

        // Debug payment methods
        \Illuminate\Support\Facades\Log::info('Payment methods', [
            'count' => count($paymentMethods),
            'methods' => collect($paymentMethods)->pluck('id')->toArray(),
            'stripe_method' => collect($paymentMethods)->where('id', 'stripe')->first()
        ]);

        return view('checkout.index', compact(
            'products', 
            'offers',
            'total', 
            'subtotal', 
            'shipping', 
            'cod_fee', 
            'user', 
            'paymentMethods',
            'paymentMethod',
            'couponDiscount', 
            'couponCode',
            'shippingMethods',
            'selectedShippingMethod',
            'currencySymbol',
            'currencyCode',
            'defaultCountry',
            'requireState',
            'requirePostalCode',
            'shippingCountries',
            'countries',
            'savedAddresses',
            'selectedAddressId'
        ));
    }

    /**
     * Update the selected shipping method
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateShippingMethod(Request $request)
    {
        $request->validate([
            'shipping_method' => 'required|string',
            'shipping_country' => 'nullable|string|max:255',
        ]);
        
        // Get cart items
        $cart = session()->get('cart', []);
        $subtotal = 0;
        
        foreach ($cart as $id => $details) {
            $product = Product::find($id);
            if ($product) {
                $subtotal += $product->price * $details['quantity'];
            }
        }
        
        // Update shipping method in session
        session()->put('shipping_method', $request->shipping_method);
        
        // Update shipping country if provided
        if ($request->has('shipping_country')) {
            session()->put('shipping_country', $request->shipping_country);
        }
        
        // Get default country from settings
        $defaultCountry = \App\Helpers\SettingsHelper::get('default_country', 'EG');
        
        // Get shipping country or default to the one from settings
        $shippingCountry = session()->get('shipping_country', $defaultCountry);
        
        // Get shipping settings to check free shipping threshold
        $freeThreshold = (float) \App\Helpers\SettingsHelper::get('shipping_free_threshold', 50.00);
        $enableFreeShipping = (bool) \App\Helpers\SettingsHelper::get('shipping_enable_free', true);
        
        // Calculate shipping fee - set to 0 if free shipping applies
        $shipping = 0;
        if (!($enableFreeShipping && $freeThreshold > 0 && $subtotal >= $freeThreshold) && $request->shipping_method !== 'free') {
            $shipping = $this->shippingService->calculateShippingFee(
                $subtotal, 
                $shippingCountry, 
                $request->shipping_method
            );
        }
        
        // Get payment method fee
        $paymentFee = 0;
        $paymentMethod = session()->get('payment_method', 'instapay');
        if ($paymentMethod === 'cod') {
            $paymentFee = floatval(\App\Helpers\SettingsHelper::get('cod_fee', '0'));
        }
        
        // Get coupon discount if applied
        $couponDiscount = 0;
        $couponCode = session()->get('coupon_code', null);
        
        if ($couponCode) {
            $coupon = $this->marketingService->getCouponByCode($couponCode);
            if ($coupon) {
                $couponDiscount = $coupon->calculateDiscount($subtotal);
            }
        }
        
        // Calculate new total
        $total = $subtotal - $couponDiscount + $shipping + $paymentFee;
        
        // Get currency info
        $currencySymbol = \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP');
        $currencyCode = \App\Helpers\SettingsHelper::get('currency', 'EGP');
        
        return response()->json([
            'success' => true,
            'shipping' => $shipping,
            'payment_fee' => $paymentFee,
            'coupon_discount' => $couponDiscount,
            'subtotal' => $subtotal,
            'total' => $total,
            'shipping_method' => $request->shipping_method,
            'free_shipping_applied' => ($enableFreeShipping && $freeThreshold > 0 && $subtotal >= $freeThreshold),
            'currency_symbol' => $currencySymbol,
            'currency_code' => $currencyCode
        ]);
    }

    /**
     * Process the checkout and create an order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function processCheckout(StoreCheckoutRequest $request)
    {
        $validatedData = $request->validated();

        try {
            $order = $this->orderService->createOrder($validatedData);

            if ($validatedData['payment_method'] === 'stripe') {
                // The payment is already handled via Payment Intent on the frontend
                // We just need to confirm it was successful and update the order
                // This logic might need to be adjusted based on StripeService implementation
                $order->update(['payment_status' => 'paid']);
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order placed successfully!',
                    'toast' => true,
                    'redirect_url' => route('checkout.confirmation', ['id' => $order->id])
                ]);
            }

            return redirect()->route('checkout.confirmation', ['id' => $order->id])
                ->with('toast', 'Order placed successfully!');

        } catch (\Exception $e) {
            Log::error('Checkout Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

            if ($request->wantsJson()) {
                $errorMessage = 'An unexpected error occurred. Please try again.';
                // If app is in debug mode, provide more details
                if (config('app.debug')) {
                    $errorMessage = $e->getMessage();
                }
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'toast' => true,
                    'toast_type' => 'error'
                ], 500);
            }

            // Optionally, handle specific exceptions differently
            return redirect()->route('checkout.index')
                ->with('toast_error', 'An unexpected error occurred. Please try again.');
        }
    }

    /**
     * Display order confirmation page
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function confirmation($id)
    {
        $order = Order::with(['items.product'])->findOrFail($id);
        $userId = Auth::id();

        // For authenticated users, ensure the order belongs to the current user
        // For guest checkouts, allow access if the order has no user_id
        if ($userId && $order->user_id && $order->user_id !== $userId) {
            Log::warning('Unauthorized attempt to access confirmation page', [
                'order_id' => $id,
                'authenticated_user' => $userId,
                'order_user_id' => $order->user_id
            ]);
            abort(403, 'Unauthorized action.');
        }

        return view('checkout.confirmation', compact('order'));
    }

    /**
     * Display successful payment page for Stripe payments
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function success($id)
    {
        $order = Order::with(['items.product'])->findOrFail($id);
        $userId = Auth::id();

        // For authenticated users, ensure the order belongs to the current user
        // For guest checkouts, allow access if the order has no user_id or checking for a session token
        if ($userId && $order->user_id && $order->user_id !== $userId) {
            // For additional security, you could store order IDs in the session for guest checkout
            // and check against that here
            Log::warning('Unauthorized attempt to access success page', [
                'order_id' => $id,
                'authenticated_user' => $userId,
                'order_user_id' => $order->user_id
            ]);
            abort(403, 'Unauthorized action.');
        }

        return view('checkout.success', compact('order'));
    }

    /**
     * Apply a coupon code to the checkout session
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50'
        ]);
        
        // Get coupon
        $coupon = $this->marketingService->getCouponByCode($request->coupon_code);
        
        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code. Please try again.',
                'toast' => true,
                'toast_type' => 'error'
            ]);
        }
        
        // Check if coupon is valid
        if (!$coupon->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon has expired or is not active.',
                'toast' => true,
                'toast_type' => 'error'
            ]);
        }
        
        // Get cart items
        $cart = session()->get('cart', []);
        $cartItems = [];
        $subtotal = 0;
        $eligibleProducts = [];
        $eligibleCategories = [];
        
        foreach ($cart as $id => $details) {
            if (isset($details['type']) && $details['type'] === 'offer') {
                $offer = \App\Models\Offer::find($details['offer_id']);
                if ($offer) {
                    $price = $details['price'] ?? ($offer->discounted_price ?? $offer->original_price);
                    $subtotal += $price * $details['quantity'];
                    
                    // Add to cart items for discount calculation
                    $cartItems[] = [
                        'product_id' => null,
                        'offer_id' => $offer->id,
                        'price' => $price,
                        'quantity' => $details['quantity']
                    ];
                }
            } else {
                $product = Product::find($id);
                if ($product) {
                    $subtotal += $product->price * $details['quantity'];
                    
                    // Add to cart items for discount calculation
                    $cartItems[] = [
                        'product_id' => $product->id,
                        'product' => $product,
                        'price' => $product->price,
                        'quantity' => $details['quantity'],
                        'category_id' => $product->category_id
                    ];
                    
                    // Check if product is eligible for this coupon
                    if (!empty($coupon->applicable_products) && in_array($product->id, $coupon->applicable_products)) {
                        $eligibleProducts[] = $product->id;
                    }
                    
                    // Check if product's category is eligible
                    if (!empty($coupon->applicable_categories) && in_array($product->category_id, $coupon->applicable_categories)) {
                        $eligibleCategories[] = $product->category_id;
                    }
                }
            }
        }
        
        // Check minimum order amount
        if ($coupon->minimum_order_amount > $subtotal) {
            $currencySymbol = \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP');
            return response()->json([
                'success' => false,
                'message' => 'This coupon requires a minimum order of ' . $currencySymbol . number_format($coupon->minimum_order_amount, 2) . '.',
                'toast' => true,
                'toast_type' => 'error'
            ]);
        }
        
        // Check if any products in cart are eligible for this coupon
        if (!empty($coupon->applicable_products) || !empty($coupon->applicable_categories)) {
            if (empty($eligibleProducts) && empty($eligibleCategories)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This coupon is not applicable to the products in your cart.',
                    'toast' => true,
                    'toast_type' => 'error'
                ]);
            }
        }
        
        // Calculate discount with cart items
        $discount = $coupon->calculateDiscount($subtotal, $cartItems);
        
        // Store coupon in session
        session()->put('coupon_code', $coupon->code);
        session()->put('coupon_applied_at', now()->timestamp);
        
        // Store eligible products/categories for display
        if (!empty($eligibleProducts)) {
            session()->put('coupon_eligible_products', $eligibleProducts);
        }
        
        if (!empty($eligibleCategories)) {
            session()->put('coupon_eligible_categories', $eligibleCategories);
        }
        
        // Calculate total with shipping
        $shippingMethod = session()->get('shipping_method', 'standard');
        $defaultCountry = \App\Helpers\SettingsHelper::get('default_country', 'EG');
        $shippingCountry = session()->get('shipping_country', $defaultCountry);
        
        // Get shipping fee
        $shipping = $this->shippingService->calculateShippingFee($subtotal, $shippingCountry, $shippingMethod);
        
        // Calculate total
        $total = $subtotal - $discount + $shipping;
        
        // Get currency info
        $currencySymbol = \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP');
        $currencyCode = \App\Helpers\SettingsHelper::get('currency', 'EGP');
        
        // Prepare response data
        $responseData = [
            'success' => true,
            'coupon_code' => $coupon->code,
            'discount' => $discount,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
            'currency_symbol' => $currencySymbol,
            'currency_code' => $currencyCode
        ];
        
        // Add coupon restriction info if applicable
        if (!empty($coupon->applicable_products) || !empty($coupon->applicable_categories)) {
            $responseData['has_restrictions'] = true;
            
            if (!empty($eligibleProducts)) {
                $responseData['eligible_products'] = $eligibleProducts;
            }
            
            if (!empty($eligibleCategories)) {
                $responseData['eligible_categories'] = $eligibleCategories;
            }
        }
        
        return response()->json($responseData);
    }
    
    /**
     * Update the payment method
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePaymentMethod(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cod,instapay,vodafone,bank_transfer,fawry,stc_pay,benefit_pay,mada,knet,stripe',
        ]);
        
        // Update payment method in session
        session()->put('payment_method', $request->payment_method);
        
        // Get cart items
        $cart = session()->get('cart', []);
        $subtotal = 0;
        
        foreach ($cart as $id => $details) {
            $product = Product::find($id);
            if ($product) {
                $subtotal += $product->price * $details['quantity'];
            }
        }
        
        // Get shipping method and country
        $shippingMethod = session()->get('shipping_method', 'standard');
        $defaultCountry = \App\Helpers\SettingsHelper::get('default_country', 'EG');
        $shippingCountry = session()->get('shipping_country', $defaultCountry);
        
        // Get shipping settings to check free shipping threshold
        $freeThreshold = (float) \App\Helpers\SettingsHelper::get('shipping_free_threshold', 50.00);
        $enableFreeShipping = (bool) \App\Helpers\SettingsHelper::get('shipping_enable_free', true);
        
        // Calculate shipping fee - set to 0 if free shipping applies
        $shipping = 0;
        if (!($enableFreeShipping && $freeThreshold > 0 && $subtotal >= $freeThreshold) && $shippingMethod !== 'free') {
            $shipping = $this->shippingService->calculateShippingFee(
                $subtotal, 
                $shippingCountry, 
                $shippingMethod
            );
        }
        
        // Calculate payment method fee
        $paymentFee = 0;
        if ($request->payment_method === 'cod') {
            $paymentFee = floatval(\App\Helpers\SettingsHelper::get('cod_fee', '0'));
        }
        
        // Get coupon discount if applied
        $couponDiscount = 0;
        $couponCode = session()->get('coupon_code', null);
        
        if ($couponCode) {
            $coupon = $this->marketingService->getCouponByCode($couponCode);
            if ($coupon) {
                $couponDiscount = $coupon->calculateDiscount($subtotal);
            }
        }
        
        // Calculate total
        $total = $subtotal - $couponDiscount + $shipping + $paymentFee;
        
        // Get currency info
        $currencySymbol = \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP');
        $currencyCode = \App\Helpers\SettingsHelper::get('currency', 'EGP');
        
        return response()->json([
            'success' => true,
            'payment_method' => $request->payment_method,
            'payment_fee' => $paymentFee,
            'shipping' => $shipping,
            'coupon_discount' => $couponDiscount,
            'subtotal' => $subtotal,
            'total' => $total,
            'currency_symbol' => $currencySymbol,
            'currency_code' => $currencyCode
        ]);
    }

    /**
     * Remove a coupon code from the checkout session
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeCoupon()
    {
        session()->forget(['coupon_code', 'coupon_applied_at', 'coupon_eligible_products', 'coupon_eligible_categories']);
        
        // Recalculate cart totals
        $cart = session()->get('cart', []);
        $subtotal = 0;
        
        foreach ($cart as $id => $details) {
            if (isset($details['type']) && $details['type'] === 'offer') {
                $offer = \App\Models\Offer::find($details['offer_id']);
                if ($offer) {
                    $price = $details['price'] ?? ($offer->discounted_price ?? $offer->original_price);
                    $subtotal += $price * $details['quantity'];
                }
            } else {
                $product = Product::find($id);
                if ($product) {
                    $subtotal += $product->price * $details['quantity'];
                }
            }
        }
        
        // Get shipping country and method
        $shippingCountry = session()->get('shipping_country', \App\Helpers\SettingsHelper::get('default_country', 'EG'));
        $shippingMethod = session()->get('shipping_method', 'standard');
        
        // Calculate shipping fee
        $shipping = $this->shippingService->calculateShippingFee($subtotal, $shippingCountry, $shippingMethod);
        
        // Get payment method fee
        $paymentFee = 0;
        $paymentMethod = session()->get('payment_method', 'instapay');
        if ($paymentMethod === 'cod') {
            $paymentFee = floatval(\App\Helpers\SettingsHelper::get('cod_fee', '0'));
        }
        
        // Calculate total
        $total = $subtotal + $shipping + $paymentFee;
        
        return response()->json([
            'success' => true,
            'message' => 'Coupon removed successfully.',
            'toast' => true,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'payment_fee' => $paymentFee,
            'total' => $total,
            'currency_symbol' => \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP')
        ]);
    }

    /**
     * Select a saved address for checkout
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function selectAddress(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
        ]);
        
        // Verify the address belongs to the authenticated user
        $address = Auth::user()->addresses()->findOrFail($request->address_id);
        
        // Store the selected address ID in the session
        session()->put('selected_address_id', $address->id);
        
        // Update shipping country if different
        if (session()->get('shipping_country') !== $address->country) {
            session()->put('shipping_country', $address->country);
            
            // Recalculate shipping based on new country
            $cart = session()->get('cart', []);
            $subtotal = 0;
            
            foreach ($cart as $id => $details) {
                if (isset($details['type']) && $details['type'] === 'offer') {
                    $offer = \App\Models\Offer::find($details['offer_id']);
                    if ($offer) {
                        $price = $details['price'] ?? ($offer->discounted_price ?? $offer->original_price);
                        $subtotal += $price * $details['quantity'];
                    }
                } else {
                    $product = Product::find($id);
                    if ($product) {
                        $subtotal += $product->price * $details['quantity'];
                    }
                }
            }
            
            $shippingMethod = session()->get('shipping_method', 'standard');
            $shipping = $this->shippingService->calculateShippingFee($subtotal, $address->country, $shippingMethod);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Address selected successfully.',
                    'toast' => true,
                    'address' => $address,
                    'shipping' => $shipping,
                    'shipping_country' => $address->country,
                    'total' => $subtotal + $shipping
                ]);
            }
        }
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Address selected successfully.',
                'toast' => true,
                'address' => $address
            ]);
        }
        
        return redirect()->back()->with('toast', 'Address selected successfully.');
    }

    /**
     * Generate a random tracking number for orders
     * 
     * @return string
     */
    private function generateTrackingNumber()
    {
        $prefix = 'TRK';
        $timestamp = substr(now()->timestamp, -6);
        $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        
        $trackingNumber = $prefix . $timestamp . $random;
        
        // Ensure it's unique
        while (Order::where('tracking_number', $trackingNumber)->exists()) {
            $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
            $trackingNumber = $prefix . $timestamp . $random;
        }
        
        return $trackingNumber;
    }

    /**
     * Generate a unique order number
     * 
     * @return string
     */
    private function generateOrderNumber()
    {
        $prefix = 'CC';
        $timestamp = now()->format('YmdHi');
        $random = rand(1000, 9999);
        
        $orderNumber = $prefix . $timestamp . $random;
        
        // Ensure it's unique
        while (Order::where('order_number', $orderNumber)->exists()) {
            $random = rand(1000, 9999);
            $orderNumber = $prefix . $timestamp . $random;
        }
        
        return $orderNumber;
    }

    /**
     * Create a Stripe payment intent for the current order
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createStripePaymentIntent(Request $request)
    {
        // Check for both authenticated users and guest checkout
        $userId = Auth::id();
        $isGuest = !$userId;
        
        Log::info('Stripe payment intent request', [
            'order_id' => $request->order_id,
            'is_ajax' => $request->ajax(),
            'content_type' => $request->header('Content-Type'),
            'user_id' => $userId ?? 'guest',
            'is_guest' => $isGuest,
            'session_id' => session()->getId()
        ]);

        // Debug cart session state
        $cartItems = session()->get('cart', []);
        Log::info('Cart status during payment intent creation', [
            'has_items' => !empty($cartItems),
            'item_count' => count($cartItems)
        ]);

        // Validate order ID
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id'
        ]);
        
        if ($validator->fails()) {
            Log::warning('Invalid order ID for Stripe payment intent', [
                'errors' => $validator->errors()->toArray()
            ]);
            return response()->json(['error' => 'Invalid order ID', 'errors' => $validator->errors()], 422);
        }
        
        try {
            $order = Order::findOrFail($request->order_id);
            
            // Log order details
            Log::info('Order details for Stripe payment', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'auth_id' => $userId,
                'total_amount' => $order->total_amount,
                'currency' => $order->currency
            ]);
            
            // Security check - allow guest checkout or ensure the order belongs to the current user
            if (!$isGuest && $order->user_id && $order->user_id !== $userId) {
                Log::warning('Unauthorized Stripe payment attempt', [
                    'order_id' => $order->id,
                    'order_user_id' => $order->user_id,
                    'auth_user_id' => $userId
                ]);
                return response()->json(['error' => 'Unauthorized access to order'], 403);
            }
            
            // Create payment intent using Stripe service
            $paymentIntent = $this->stripeService->createPaymentIntent($order);
            
            if (!$paymentIntent) {
                Log::error('Failed to create Stripe payment intent', [
                    'order_id' => $order->id
                ]);
                return response()->json(['error' => 'Failed to create payment intent'], 500);
            }
            
            Log::info('Stripe payment intent created', [
                'order_id' => $order->id,
                'intent_id' => $paymentIntent['paymentIntentId'] ?? 'unknown'
            ]);
            
            return response()->json($paymentIntent);
        } catch (\Exception $e) {
            Log::error('Exception during Stripe payment intent creation', [
                'order_id' => $request->order_id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Error creating payment intent', 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process payment for an order
     *
     * @param Order $order
     * @param string $paymentMethod
     * @return array
     */
    private function processPayment($order, $paymentMethod)
    {
        // Default response structure
        $response = [
            'success' => true,
            'status' => 'pending',
            'payment_id' => null,
            'message' => 'Payment processed successfully'
        ];
        
        try {
            switch ($paymentMethod) {
                case 'stripe':
                    // Stripe payments are handled separately via the Stripe API
                    // Just mark as pending here, actual payment happens client-side
                    $response['status'] = 'pending';
                    $response['message'] = 'Stripe payment pending confirmation';
                    break;
                    
                case 'paypal':
                    // PayPal payments would be handled via the PayPal API
                    $response['status'] = 'pending';
                    $response['message'] = 'PayPal payment pending confirmation';
                    break;
                    
                case 'cod':
                    // Cash on Delivery - no processing needed
                    $response['status'] = 'pending';
                    $response['message'] = 'Cash on Delivery payment will be collected upon delivery';
                    break;
                    
                case 'instapay':
                case 'vodafone':
                case 'bank_transfer':
                case 'fawry':
                case 'stc_pay':
                case 'benefit_pay':
                default:
                    // For manual payment methods, just mark as pending
                    $response['status'] = 'pending';
                    $response['message'] = ucfirst($paymentMethod) . ' payment pending confirmation';
                    break;
            }
            
            // Log the payment attempt
            \Illuminate\Support\Facades\Log::info('Payment processed', [
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'status' => $response['status']
            ]);
            
            return $response;
        } catch (\Exception $e) {
            // Log the error
            \Illuminate\Support\Facades\Log::error('Payment processing failed', [
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'error' => $e->getMessage()
            ]);
            
            // Return error response
            return [
                'success' => false,
                'status' => 'failed',
                'payment_id' => null,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ];
        }
    }
} 