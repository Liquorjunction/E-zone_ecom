<?php

namespace App\Services;

use App\Models\NapsApiLog;
use Illuminate\Support\Facades\Log;

class NapsApiLogService
{
    /**
     * Log the API response to the database.
     *
     * @param string|null $orderId The order ID (nullable if not available)
     * @param array $response The full API response
     * @param string $apiType The type of API call (token, authorization, refund, status)
     * @return NapsApiLog
     */
    public function logApiResponse(?string $orderId, array $response, string $apiType): NapsApiLog
    {
        try {
            $napsApiLog = NapsApiLog::create([
                'order_id' => $orderId,
                'api_response' => json_encode($response),
                'api_type' => $apiType,
            ]);

            Log::info('NAPS API Logged', [
                'order_id' => $orderId,
                'api_type' => $apiType,
                'log_id' => $napsApiLog->id,
            ]);

            return $napsApiLog;
        } catch (\Exception $e) {
            Log::error('Failed to log NAPS API response', [
                'order_id' => $orderId,
                'api_type' => $apiType,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}

