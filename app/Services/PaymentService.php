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
        * Map MXPlus status code to user-friendly message.
    */
    public static function getStatusMessage($statusCode)
    {
        $map = [
            '00' => 'Approved',
            '01' => 'Refer to Card issuer',
            '02' => 'Refer to Card issuer (Special)',
            '07' => 'Pick Up Card (Special)',
            '13' => 'Invalid Amount',
            '21' => 'No Action Taken',
            '25' => 'Unable to locate record',
            '43' => 'Pick Up Stolen Card',
            '51' => 'Insufficient Funds',
            '52' => 'No checking account',
            '53' => 'No saving account',
            '54' => 'Expired card',
            '55' => 'Incorrect Pin',
            '57' => 'Transaction not permitted',
            '58' => 'Transaction not allowed',
            '61' => 'Exceeds withdrawal limit',
            '62' => 'Restricted card',
            '63' => 'Security violation',
            '65' => 'Activity count limit exceeded',
            '75' => 'Allowable number of PIN tries exceeded',
            '76' => 'Unable to locate message',
            '80' => 'Invalid date',
            '81' => 'Pin Cryptographic error',
            '82' => 'Incorrect CVV',
            '83' => 'Unable to Verify Pin',
            '85' => 'No Reason to decline',
            '91' => 'Issuer unavailable',
            '92' => 'Destination cannot be found',
            '93' => 'Transaction cannot be completed',
            '96' => 'System Malfunction',
            '16' => 'Order already used',
            '15' => 'Merchant does not exist',
            '17' => 'Data invalid',
        ];
        return $map[$statusCode] ?? 'Unknown error';
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
            $statusCode = $innerResponse['statuscode'] ?? null;
            $statusMessage = $statusCode ? self::getStatusMessage($statusCode) : 'Unknown error';
            if ($statusCode === '00') {
                return [
                    'success' => true,
                    'transaction_id' => $innerResponse['paymentid'] ?? null,
                    'auth_number' => $innerResponse['authnumber'] ?? null,
                    'raw_response' => $innerResponse, // Pass this back so OrderService can use it
                    'message' => $statusMessage
                ];
            }
            return [
                'success' => false,
                'transaction_id' => $innerResponse['paymentid'] ?? null,
                'auth_number' => $innerResponse['authnumber'] ?? null,
                'raw_response' => $innerResponse,
                'message' => $statusMessage
            ];

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
