<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MXPlusService
{
    protected $config;
    protected $napsApiLogService;

    public function __construct(NapsApiLogService $napsApiLogService)
    {
        $this->config = config('mxplus');
        $this->napsApiLogService = $napsApiLogService;
    }

    /**
     * Get the 24h security token from cache or fetch a new one.
     */
    public function getSecurityToken()
    {
        return Cache::remember('mxplus_token', now()->addHours(23), function () {
            $url = $this->config['base_url'] . 'napspayment/createtocken24';
            $macValue = md5($this->config['institution_id'] . $this->config['cx_user']);
            $payload = [
    "cx_user" => "NAPS",
    "cx_password" => "2e6a98abb5b23339ad14601d3bedc1d23847498cb18daf8cfc98c2a2095ec8",
     "cx_reason" => "00",
    "mac_value" => "d8be7c5638519aac35506c0ea82ae2d9",
    "institution_id" => "11010"
];
 
            

            
            // [
            //     'institution_id' => $this->config['institution_id'],
            //     'cx_user' => $this->config['cx_user'],
            //     'cx_password' => $this->config['cx_password'],
            //     'cx_reason' => '00',
            //     'mac_value' => $macValue,
            // ];
            
            
            Log::info('request payload for create token API', [
            'payload' => $payload,
        ]);
            
            $response = Http::post($url, $payload);
            Log::info('response data for create token API', [
            'response' => $response,
        ]);
             Log::error('MXPlus Token Generation Failed', [
                'response' => $response,
                'url' => $url,
                'payload' => $payload,
            ]);
            $responseData = $response->json() ?? [];

            // Log the API response
            $this->napsApiLogService->logApiResponse(null, $responseData, 'token');

            if ($response->successful()) {
                $token = $response->json('securtoken_24') ?? $response->json('securtoken_24');

                if ($token) {
                    return $token;
                }
            }

            Log::error('MXPlus Token Generation Failed', [
                'status' => $response->status(),
                'error_msg' => $response->json('error_msg'),
                'error_code' => $response->json('error_code'),
            ]);

            throw new \Exception("MXPlus Authentication Failed: " . ($response->json('error_msg') ?? 'Unknown Error'));
        });
    }

    /**
     * Process a payment authorization request.
     */
    public function authorizePayment(array $data)
    {
        $token = $this->getSecurityToken();
        $url = $this->config['base_url'] . 'napspayment/authorization';
        $txMac = md5($data['orderid'] . $data['amount']);
        $expiry = str_replace('/', '', $data['expiry']); // becomes 1225
        $formattedExpiry = substr($expiry, 2, 2) . substr($expiry, 0, 2);

        $payload = [
            // Static / Config Values
            'capture' => 'Y',
            'transactiontype' => '0',
            'currency' => '504',
            'recurring' => 'N',
            'auth3ds' => 'N',
            'merchantid' => $this->config['merchant_id'],
            'merchantname' => config('app.name'),
            'websitename' => url('/'),
            'websiteid' => $this->config['website_id'],

            // Dynamic Transaction Data
            'orderid' => $data['orderid'],
            'amount' => $data['amount'],
            'securtoken24' => $token,
            'mac_value' => $txMac,

            // Card Data
            'cardnumber' => $data['cardNumber'],
            'expirydate' => $formattedExpiry,
            'cvv' => $data['cvv'],
            'holdername' => $data['firstName'] . ' ' . $data['lastName'],

            // Customer Info
            'fname' => $data['firstName'],
            'lname' => $data['lastName'],
            'email' => $data['email'],
            'country' => '504', // Morocco Code
            'phone' => $data['phone'] ?? '',
            'city' => $data['city'] ?? '',
            'state' => $data['state'] ?? '',
            'zipcode' => $data['zip'] ?? '',
        ];

Log::info('request data for make payment API', [
            'payload' => $payload,
        ]);
        
        $response = Http::timeout(30)
    ->retry(3, 2000)
    ->asJson()
    ->post($url, $payload);
    Log::info('response data for make payment API', [
            'response' => $response,
        ]);
        $responseData = $response->json() ?? [];

        // Log the API response
        $this->napsApiLogService->logApiResponse($data['orderid'] ?? null, $responseData, 'authorization');

        if ($response->failed()) {
            Log::error('MXPlus Payment Call Failed', ['res' => $response->body()]);
        }

        return $responseData;
    }

    /**
     * Process a Refund (Cancellation)
     */
    public function refund(array $data)
    {
        $token = $this->getSecurityToken();
        $url = rtrim($this->config['base_url'], '/') . '/napspayment/refund';

        // Per Spec: MD5(orderid + amount)
        $macValue = md5($data['orderid'] . $data['amount']);

        $payload = [
            // Transaction Info
            'orderid' => $data['orderid'],
            'amount' => $data['amount'],
            'authnumber' => $data['authnumber'], // From original payment
            'paymentid' => $data['paymentid'],  // From original payment
            'securtoken24' => $token,
            'mac_value' => $macValue,

            // Merchant Info
            'merchantid' => $this->config['merchant_id'],
            'merchantname' => config('app.name'),
            'websiteid' => $this->config['website_id'],

            // Optional Client Info (Helps NAPS track the refund)
            'fname' => $data['firstName'] ?? '',
            'lname' => $data['lastName'] ?? '',
            'email' => $data['email'] ?? '',
        ];

        $response = Http::post($url, $payload);

        $responseData = $response->json() ?? [];

        // Log the API response
        $this->napsApiLogService->logApiResponse($data['orderid'] ?? null, $responseData, 'refund');

        if ($response->failed()) {
            Log::error('MXPlus Refund Failed', ['res' => $response->body()]);
        }

        return $responseData;
    }

    public function getPaymentStatus(array $data)
    {
        $token = $this->getSecurityToken();
        $url = rtrim($this->config['base_url'], '/') . '/napspayment/status';

        // Per Spec: MD5(orderid + amount)
        $macValue = md5($data['orderid'] . $data['amount']);

        $payload = [
            'orderid' => $data['orderid'],
            'authnumber' => $data['authnumber'], // Received from the original authorizePayment response
            'paymentid' => $data['paymentid'] ?? '', // Optional
            'amount' => $data['amount'],
            'transactionid' => $data['transactionid'] ?? '', // Optional
            'securtoken24' => $token,
            'mac_value' => $macValue,
            'merchantid' => $this->config['merchant_id'],
        ];

        $response = Http::post($url, $payload);

        $responseData = $response->json() ?? [];

        // Log the API response
        $this->napsApiLogService->logApiResponse($data['orderid'] ?? null, $responseData, 'status');

        if ($response->failed()) {
            Log::error('MXPlus Status Check Failed', ['res' => $response->body()]);
        }

        return $responseData;
    }
}
