<?php

namespace App\Services;

use App\Models\Cart;
use App\Repositories\Client\Interfaces\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;
use App\Services\PaymentService;

class OrderService
{
    protected $orders;
    protected $logger;
    protected $paymentService;

    public function __construct(OrderRepositoryInterface $orders, PaymentService $paymentService, LoggerInterface $logger)
    {
        $this->orders = $orders;
        $this->paymentService = $paymentService;
        $this->logger = $logger;
    }

    public function createFromCart(Cart $cart, array $data = [])
    {
        $this->logger->info('Creating order from cart', ['cart_id' => $cart->id]);

        $order = $this->orders->createOrderFromCart($cart, $data);

        $this->logger->info('Order created', ['order_id' => $order->id]);

        // Attempt payment processing (stub). Real integration should be implemented here.
        try {
            $paymentResult = $this->paymentService->process($data, $order);

            if (!empty($paymentResult['success'])) {
                // Save payment method if provided
                $order->payment_method = $data['payment_method'] ?? $data['payment'] ?? $order->payment_method;

                // If a transaction_id exists, consider the order paid
                $order->status = !empty($paymentResult['transaction_id']) ? 'paid' : 'processing';
                $order->save();

                $this->logger->info('Payment processed', ['order_id' => $order->id, 'payment' => $paymentResult]);
            } else {
                $order->status = 'failed';
                $order->save();
                $this->logger->error('Payment failed', ['order_id' => $order->id, 'reason' => $paymentResult['message'] ?? null]);
                throw new \Exception('Payment failed: ' . ($paymentResult['message'] ?? 'unknown'));
            }
        } catch (\Throwable $e) {
            $this->logger->error('Payment exception', ['exception' => $e]);
            // keep order created but mark failed
            $order->status = $order->status ?? 'failed';
            $order->save();
            throw $e;
        }

        return $order;
    }
}
