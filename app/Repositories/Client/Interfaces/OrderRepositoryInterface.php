<?php

namespace App\Repositories\Client\Interfaces;

use App\Models\Cart;

interface OrderRepositoryInterface
{
    /**
     * Create an order (and order items) from a cart inside a transaction.
     * Returns the created Order instance.
     */
    public function createOrderFromCart(Cart $cart, array $data);
}
