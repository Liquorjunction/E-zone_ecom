<?php

namespace App\Services;

use App\Models\Cart;
use App\Repositories\Client\Interfaces\CartRepositoryInterface;
use App\Repositories\Client\Interfaces\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;
use App\Services\PaymentService;
use App\Services\CouponService;
use App\Services\PriceCalculatorService;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    protected OrderRepositoryInterface $orders;
    protected CartRepositoryInterface $cartRepo;
    protected PriceCalculatorService $priceCalculator;
    protected LoggerInterface $logger;
    protected PaymentService $paymentService;
    protected CouponService $couponService;

    public function __construct(OrderRepositoryInterface $orders, CartRepositoryInterface $cartRepo, PaymentService $paymentService, PriceCalculatorService $priceCalculator, CouponService $couponService, LoggerInterface $logger)
    {
        $this->orders = $orders;
        $this->cartRepo = $cartRepo;
        $this->priceCalculator = $priceCalculator;
        $this->paymentService = $paymentService;
        $this->couponService = $couponService;
        $this->logger = $logger;
    }

    public function createFromCart(Cart $cart, array $data = [])
    {
        $this->logger->info('Creating order from cart', ['cart_id' => $cart->id]);

        $order = $this->orders->createOrderFromCart($cart, $data, $this->cartRepo, $this->priceCalculator);

        $this->logger->info('Order created', ['order_id' => $order->id]);

        // Record coupon usage if a coupon was applied
        if ($cart->coupon_id) {
            try {
                $this->couponService->recordUsage(
                    $cart->coupon_id,
                    $order->id,
                    Auth::id()
                );
                $this->logger->info('Coupon usage recorded', [
                    'coupon_id' => $cart->coupon_id,
                    'order_id' => $order->id,
                ]);
            } catch (\Exception $e) {
                $this->logger->error('Failed to record coupon usage', [
                    'coupon_id' => $cart->coupon_id,
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

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
