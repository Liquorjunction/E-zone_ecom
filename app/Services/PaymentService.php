<?php

namespace App\Services;

use App\Models\Order;
use Psr\Log\LoggerInterface;

class PaymentService
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Process payment for an order.
     * This is a minimal stub implementation — replace with real gateway integration.
     * Returns ['success' => bool, 'transaction_id' => string|null, 'message' => string|null]
     */
    public function process(array $paymentData, Order $order): array
    {
        $method = $paymentData['payment_method'] ?? $paymentData['payment'] ?? null;
        $this->logger->info('Processing payment', ['method' => $method, 'order_id' => $order->id]);

        if ($method === 'card') {
            // Basic validation for demo purposes
            if (empty($paymentData['cardNumber'])) {
                return ['success' => false, 'transaction_id' => null, 'message' => 'Card number missing'];
            }

            // Simulate successful card charge
            return ['success' => true, 'transaction_id' => 'TX-' . uniqid(), 'message' => 'Charged (simulated)'];
        }

        // Support COD / other methods as successful but unpaid
        if (in_array($method, ['cod', 'cash', 'cash_on_delivery'])) {
            return ['success' => true, 'transaction_id' => null, 'message' => 'COD selected'];
        }

        // Default: mark as successful (no transaction id)
        return ['success' => true, 'transaction_id' => null, 'message' => 'Payment succeeded (default)'];
    }
}
