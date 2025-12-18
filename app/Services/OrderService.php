<?php

namespace App\Services;

use App\Models\Cart;
use App\Repositories\Client\Interfaces\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class OrderService
{
    protected $orders;
    protected $logger;

    public function __construct(OrderRepositoryInterface $orders, LoggerInterface $logger)
    {
        $this->orders = $orders;
        $this->logger = $logger;
    }

    public function createFromCart(Cart $cart, array $data = [])
    {
        $this->logger->info('Creating order from cart', ['cart_id' => $cart->id]);

        $order = $this->orders->createOrderFromCart($cart, $data);

        $this->logger->info('Order created', ['order_id' => $order->id]);

        return $order;
    }
}
