<?php

namespace App\Services;

use App\Models\Offer;
use App\Models\Product;
use App\Helpers\SettingsHelper;
use App\Helpers\TranslationHelper;
use Illuminate\Http\Request;

class CartService
{
    public function getCartContents()
    {
        $cart = session()->get('cart', []);
        $products = [];
        $offers = [];
        $total = 0;

        foreach ($cart as $id => $details) {
            if (isset($details['type']) && $details['type'] === 'offer') {
                $offer = Offer::find($details['offer_id']);
                if ($offer) {
                    $price = $details['price'] ?? ($offer->discounted_price ?? $offer->original_price);
                    $offers[] = [
                        'offer' => $offer,
                        'quantity' => $details['quantity'],
                        'price' => $price
                    ];
                    $total += $price * $details['quantity'];
                }
            } else {
                $product = Product::find($id);
                if ($product) {
                    $products[] = [
                        'product' => $product,
                        'quantity' => $details['quantity']
                    ];
                    $total += $product->price * $details['quantity'];
                }
            }
        }

        return compact('products', 'offers', 'total');
    }

    public function getMiniCartContents()
    {
        $cart = session()->get('cart', []);
        $items = [];
        $subtotal = 0;

        foreach ($cart as $id => $details) {
            if (isset($details['type']) && $details['type'] === 'offer') {
                $offer = Offer::find($details['offer_id']);
                if ($offer) {
                    $price = $details['price'] ?? ($offer->discounted_price ?? $offer->original_price);
                    $items[] = [
                        'id' => $offer->id,
                        'name' => $offer->title . ' (Bundle)',
                        'price' => SettingsHelper::formatPrice($price),
                        'quantity' => $details['quantity'],
                        'image' => asset('storage/' . $offer->image),
                        'product_url' => route('offers.index'), // Corrected route
                        'subtotal' => SettingsHelper::formatPrice($price * $details['quantity']),
                        'is_bundle' => true,
                    ];
                    $subtotal += $price * $details['quantity'];
                }
            } else {
                $product = Product::find($id);
                if ($product) {
                    $price = $product->discount_percent > 0 ? $product->final_price : $product->price;
                    $items[] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => SettingsHelper::formatPrice($price),
                        'quantity' => $details['quantity'],
                        'image' => asset('storage/' . $product->image),
                        'product_url' => route('products.show', $product->slug),
                        'subtotal' => SettingsHelper::formatPrice($price * $details['quantity']),
                        'is_bundle' => false
                    ];
                    $subtotal += $price * $details['quantity'];
                }
            }
        }

        return [
            'items' => $items,
            'subtotal' => SettingsHelper::formatPrice($subtotal),
            'cart_count' => $this->getCartCount()
        ];
    }

    public function addProduct(Product $product, int $quantity = 1, ?string $promoCode = null): array
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
            if ($promoCode) {
                $cart[$product->id]['promo_code'] = $promoCode;
            }
        } else {
            $cart[$product->id] = [
                'quantity' => $quantity,
                'promo_code' => $promoCode
            ];
        }

        session()->put('cart', $cart);

        return [
            'success' => true,
            'message' => TranslationHelper::get('product_added_to_cart', 'Product added to cart successfully!'),
            'cart_count' => $this->getCartCount()
        ];
    }

    public function addOffer(Offer $offer, int $quantity = 1): array
    {
        if ($offer->stock <= 0) {
            return [
                'success' => false,
                'message' => TranslationHelper::get('offer_out_of_stock', 'This offer is currently out of stock.'),
                'status' => 422
            ];
        }

        $cart = session()->get('cart', []);
        
        if ($quantity > $offer->stock) {
            $quantity = $offer->stock;
        }

        $offerCartId = 'offer_' . $offer->id;

        if (isset($cart[$offerCartId])) {
            $newQuantity = $cart[$offerCartId]['quantity'] + $quantity;
            if ($newQuantity > $offer->stock) {
                $newQuantity = $offer->stock;
            }
            $cart[$offerCartId]['quantity'] = $newQuantity;
        } else {
            $cart[$offerCartId] = [
                'quantity' => $quantity,
                'type' => 'offer',
                'offer_id' => $offer->id,
                'price' => $offer->discounted_price ?? $offer->original_price
            ];
        }

        session()->put('cart', $cart);

        return [
            'success' => true,
            'message' => TranslationHelper::get('offer_added_to_cart', 'Offer added to cart successfully!'),
            'cart_count' => $this->getCartCount()
        ];
    }

    public function updateProduct(Product $product, int $quantity): array
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] = $quantity;
            session()->put('cart', $cart);
        }

        return [
            'message' => TranslationHelper::get('cart_updated', 'Cart updated successfully!'),
            'cart_count' => $this->getCartCount()
        ];
    }

    public function removeProduct(Product $product): array
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$product->id])) {
            unset($cart[$product->id]);
            session()->put('cart', $cart);
        }

        return [
            'message' => TranslationHelper::get('product_removed_from_cart', 'Product removed from cart successfully!'),
            'cart_count' => $this->getCartCount()
        ];
    }

    public function removeOffer(Offer $offer): array
    {
        $cart = session()->get('cart', []);
        $offerCartId = 'offer_' . $offer->id;

        if (isset($cart[$offerCartId])) {
            unset($cart[$offerCartId]);
            session()->put('cart', $cart);
        }

        return [
            'message' => TranslationHelper::get('offer_removed_from_cart', 'Offer removed from cart successfully!'),
            'cart_count' => $this->getCartCount()
        ];
    }

    public function getCartCount(): int
    {
        $cart = session()->get('cart', []);
        return array_sum(array_column($cart, 'quantity'));
    }
} 