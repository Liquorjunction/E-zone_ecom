<?php

namespace App\Services;

use App\Models\Order;
use Psr\Log\LoggerInterface;
use App\Services\MXPlusService;
use Illuminate\Support\Facades\Log;



class PaymentService
{
    protected $logger;
    protected $mxPlus;

    public function __construct(LoggerInterface $logger, MXPlusService $mxPlus)
    {
        $this->logger = $logger;
        $this->mxPlus = $mxPlus;
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
            $paymentData['orderid'] = $order->order_number;
            $paymentData['amount'] = $order->total;
            // dd($paymentData);
            $result = $this->mxPlus->authorizePayment($paymentData);
            $innerResponse = $result['response'] ?? [];
            if (empty($innerResponse) && isset($result['statuscode'])) {
                $innerResponse = $result;
            }
            Log::info('Checking extracted data', [
                'auth' => $innerResponse['authnumber'] ?? 'MISSING',
                'pid' => $innerResponse['paymentid'] ?? 'MISSING'
            ]);
            Log::error('MXPlus Payment API Response', [
                'response' => $result,
            ]);
            Log::error('MXPlus Payment API iNNER Response', [
                'innerResponse' => $innerResponse,
            ]);
            if (isset($innerResponse['statuscode']) && $innerResponse['statuscode'] === '00') {
                return [
                    'success' => true,
                    'transaction_id' => $innerResponse['paymentid'] ?? null,
                    'auth_number' => $innerResponse['authnumber'] ?? null,
                    'raw_response' => $innerResponse, // Pass this back so OrderService can use it
                    'message' => 'Payment Successful'
                ];
            }

            // Simulate successful card charge
        }

        // Support COD / other methods as successful but unpaid
        if (in_array($method, ['cod', 'cash', 'cash_on_delivery'])) {
            return ['success' => true, 'transaction_id' => null, 'message' => 'COD selected'];
        }

        // Default: mark as successful (no transaction id)
        return ['success' => true, 'transaction_id' => null, 'message' => 'Payment succeeded (default)'];
    }
}
