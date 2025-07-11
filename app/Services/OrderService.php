<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Address;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\OrderConfirmationMail;
use App\Mail\NewOrderNotificationMail;

class OrderService
{
    protected $inventoryService;
    protected $marketingService;
    protected $shippingService;
    protected $offerInventoryService;

    public function __construct(
        InventoryService $inventoryService,
        MarketingService $marketingService,
        ShippingService $shippingService,
        OfferInventoryService $offerInventoryService
    ) {
        $this->inventoryService = $inventoryService;
        $this->marketingService = $marketingService;
        $this->shippingService = $shippingService;
        $this->offerInventoryService = $offerInventoryService;
    }

    public function createOrder(array $validatedData): Order
    {
        return DB::transaction(function () use ($validatedData) {
            $user = Auth::user();
            
            // When creating an account during checkout without using saved address
            if (!$user && !empty($validatedData['create_account']) && isset($validatedData['shipping_first_name'])) {
                $user = User::create([
                    'name' => $validatedData['shipping_first_name'] . ' ' . $validatedData['shipping_last_name'],
                    'email' => $validatedData['shipping_email'],
                    'password' => Hash::make($validatedData['password']),
                ]);
                Auth::login($user);
            }

            // If a saved address is used, fetch it with all its details.
            // Otherwise, construct a new one from the form data.
            if (!empty($validatedData['shipping_address_id'])) {
                $shippingAddress = Address::findOrFail($validatedData['shipping_address_id']);
                // Update the country if provided in the form (needed for shipping calculation)
                if (isset($validatedData['shipping_country'])) {
                    $shippingAddress->country = $validatedData['shipping_country'];
                }
            } else {
                // When not using saved address, all fields should be in the request data
                $shippingAddress = new Address([
                    'first_name' => $validatedData['shipping_first_name'],
                    'last_name' => $validatedData['shipping_last_name'],
                    'email' => $validatedData['shipping_email'],
                    'phone' => $validatedData['shipping_phone'],
                    'address_line1' => $validatedData['shipping_address_line1'],
                    'address_line2' => $validatedData['shipping_address_line2'] ?? null,
                    'city' => $validatedData['shipping_city'],
                    'state' => $validatedData['shipping_state'] ?? null,
                    'postal_code' => $validatedData['shipping_postal_code'] ?? null,
                    'country' => $validatedData['shipping_country'],
                ]);
            }

            $cart = session()->get('cart', []);
            $subtotal = $this->calculateSubtotal($cart);
            
            $shippingFee = $this->shippingService->calculateShippingFee($subtotal, $validatedData['shipping_country'], $validatedData['shipping_method']);
            if ($validatedData['shipping_method'] === 'custom' && isset($validatedData['custom_shipping_fee'])) {
                $shippingFee = $validatedData['custom_shipping_fee'];
            }

            $couponDiscount = $this->getCouponDiscount($subtotal);
            $codFee = ($validatedData['payment_method'] === 'cod') ? (float)SettingsHelper::get('cod_fee', 0) : 0;
            $total = $subtotal - $couponDiscount + $shippingFee + $codFee;
            
            $order = Order::create([
                'user_id' => $user ? $user->id : null,
                'order_number' => $this->generateOrderNumber(),
                'tracking_number' => $this->generateTrackingNumber(),
                'first_name' => $shippingAddress->first_name,
                'last_name' => $shippingAddress->last_name,
                'email' => $shippingAddress->email,
                'phone' => $shippingAddress->phone,
                'address_line1' => $shippingAddress->address_line1,
                'address_line2' => $shippingAddress->address_line2,
                'city' => $shippingAddress->city,
                'state' => $shippingAddress->state,
                'postal_code' => $shippingAddress->postal_code,
                'country' => $shippingAddress->country,
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount' => $couponDiscount,
                'total' => $total,
                'payment_method' => $validatedData['payment_method'],
                'payment_status' => 'pending',
                'shipping_method' => $validatedData['shipping_method'],
                'status' => 'pending',
                'notes' => $validatedData['notes'] ?? null,
            ]);

            $this->createOrderItems($order, $cart);
            $this->updateInventory($cart);
            $this->sendOrderEmails($order);
            
            // Only save address if using a new address (not a saved one) and user requests to save it
            if ($user && !empty($validatedData['save_address']) && 
                empty($validatedData['shipping_address_id']) && 
                !empty($validatedData['shipping_address_line1'])) {
                $this->saveUserAddress($user, $validatedData);
            }

            session()->forget(['cart', 'coupon_code', 'shipping_method']);

            return $order;
        });
    }

    private function calculateSubtotal(array $cart): float
    {
        $subtotal = 0;
        foreach ($cart as $id => $details) {
            if (isset($details['type']) && $details['type'] === 'offer') {
                $offer = \App\Models\Offer::find($details['offer_id']);
                if ($offer) {
                    $subtotal += ($details['price'] ?? ($offer->discounted_price ?? $offer->original_price)) * $details['quantity'];
                }
            } else {
                $product = \App\Models\Product::find($id);
                if ($product) {
                    $subtotal += $product->price * $details['quantity'];
                }
            }
        }
        return $subtotal;
    }
    
    private function getCouponDiscount(float $subtotal): float
    {
        $couponCode = session()->get('coupon_code');
        if (!$couponCode) {
            return 0;
        }

        $coupon = $this->marketingService->getCouponByCode($couponCode);
        if ($coupon) {
            return $coupon->calculateDiscount($subtotal);
        }

        return 0;
    }

    private function createOrderItems(Order $order, array $cart): void
    {
        foreach ($cart as $id => $details) {
            if (isset($details['type']) && $details['type'] === 'offer') {
                $offer = \App\Models\Offer::find($details['offer_id']);
                if ($offer) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => null,
                        'offer_id' => $offer->id,
                        'name' => $offer->title,
                        'quantity' => $details['quantity'],
                        'price' => $details['price'] ?? ($offer->discounted_price ?? $offer->original_price),
                    ]);
                }
            } else {
                $product = \App\Models\Product::find($id);
                if ($product) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'offer_id' => null,
                        'name' => $product->name,
                        'quantity' => $details['quantity'],
                        'price' => $product->price,
                    ]);
                }
            }
        }
    }

    private function updateInventory(array $cart): void
    {
        foreach ($cart as $id => $details) {
            if (isset($details['type']) && $details['type'] === 'offer') {
                $this->offerInventoryService->reduceStock($details['offer_id'], $details['quantity']);
            } else {
                $this->inventoryService->reduceStock($id, $details['quantity']);
            }
        }
    }

    private function sendOrderEmails(Order $order): void
    {
        try {
            // Send order confirmation to customer
            Mail::to($order->email)->send(new OrderConfirmationMail($order));

            // Send new order notification to admin
            $adminEmail = SettingsHelper::get('admin_email');
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new NewOrderNotificationMail($order));
            }
        } catch (\Exception $e) {
            Log::error("Failed to send order confirmation email for order {$order->id}: " . $e->getMessage());
        }
    }

    private function saveUserAddress(User $user, array $data): void
    {
        $address = $user->addresses()->updateOrCreate(
            ['address_line1' => $data['shipping_address_line1'], 'postal_code' => $data['shipping_postal_code'] ?? null],
            [
                'first_name' => $data['shipping_first_name'],
                'last_name' => $data['shipping_last_name'],
                'email' => $data['shipping_email'],
                'phone' => $data['shipping_phone'],
                'address_line2' => $data['shipping_address_line2'] ?? null,
                'city' => $data['shipping_city'],
                'state' => $data['shipping_state'] ?? null,
                'country' => $data['shipping_country'],
                'is_default' => $user->addresses()->count() === 0, // Make first address default
            ]
        );
    }
    
    private function generateTrackingNumber(): string
    {
        return 'CC-' . strtoupper(Str::random(10));
    }

    private function generateOrderNumber(): string
    {
        return date('Ymd') . '-' . mt_rand(1000, 9999);
    }
} 