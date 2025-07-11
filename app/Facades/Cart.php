<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Cart extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'cart';
    }

    /**
     * Get the number of items in the cart.
     *
     * @return int
     */
    public static function count()
    {
        return session('cart') ? array_sum(array_column(session('cart'), 'quantity')) : 0;
    }
} 