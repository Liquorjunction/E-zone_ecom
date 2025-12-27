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
    /**
     * Cancel an order by id. Should restore inventory and log transactions.
     * Returns true on success.
     */
    public function cancelOrder(int $orderId, ?string $reason = null);
}
