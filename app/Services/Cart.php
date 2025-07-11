<?php

namespace App\Services;

class Cart
{
    /**
     * Get the number of items in the cart.
     *
     * @return int
     */
    public function count()
    {
        return session('cart') ? array_sum(array_column(session('cart'), 'quantity')) : 0;
    }

    /**
     * Get all items in the cart.
     *
     * @return array
     */
    public function items()
    {
        return session('cart', []);
    }

    /**
     * Add an item to the cart.
     *
     * @param int $productId
     * @param int $quantity
     * @return void
     */
    public function add($productId, $quantity = 1)
    {
        $cart = session('cart', []);
        
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'quantity' => $quantity
            ];
        }
        
        session(['cart' => $cart]);
    }

    /**
     * Update the quantity of an item in the cart.
     *
     * @param int $productId
     * @param int $quantity
     * @return void
     */
    public function update($productId, $quantity)
    {
        $cart = session('cart', []);
        
        if (isset($cart[$productId])) {
            if ($quantity > 0) {
                $cart[$productId]['quantity'] = $quantity;
            } else {
                unset($cart[$productId]);
            }
            
            session(['cart' => $cart]);
        }
    }

    /**
     * Remove an item from the cart.
     *
     * @param int $productId
     * @return void
     */
    public function remove($productId)
    {
        $cart = session('cart', []);
        
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session(['cart' => $cart]);
        }
    }

    /**
     * Clear all items from the cart.
     *
     * @return void
     */
    public function clear()
    {
        session(['cart' => []]);
    }
} 