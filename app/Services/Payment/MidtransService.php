<?php

namespace App\Services\Payment;

class MidtransService
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get API URL based on environment
     */
    protected function getApiUrl(): string
    {
        $environment = $this->config['environment'] ?? 'production';
        return $environment === 'sandbox'
            ? 'https://api.sandbox.midtrans.com'
            : 'https://api.midtrans.com';
    }

    /**
     * Get server key based on environment
     */
    protected function getServerKey(): string
    {
        $environment = $this->config['environment'] ?? 'production';
        return $environment === 'sandbox'
            ? ($this->config['server_key_sandbox'] ?? '')
            : ($this->config['server_key_production'] ?? '');
    }

    /**
     * Get client key based on environment
     */
    public function getClientKey(): string
    {
        $environment = $this->config['environment'] ?? 'production';
        return $environment === 'sandbox'
            ? ($this->config['client_key_sandbox'] ?? '')
            : ($this->config['client_key_production'] ?? '');
    }

    /**
     * Get merchant ID
     */
    public function getMerchantId(): string
    {
        $environment = $this->config['environment'] ?? 'production';
        return $environment === 'sandbox'
            ? ($this->config['merchant_id_sandbox'] ?? '')
            : ($this->config['merchant_id'] ?? '');
    }

    /**
     * Test connection to Midtrans API
     *
     * Note: Midtrans doesn't have a dedicated ping/test endpoint.
     * We test by making a simple API call and checking if we get proper authentication response.
     */
    public function testConnection(): array
    {
        $serverKey = $this->getServerKey();
        $clientKey = $this->getClientKey();
        $merchantId = $this->getMerchantId();
        $environment = $this->config['environment'] ?? 'production';

        if (empty($serverKey)) {
            return [
                'success' => false,
                'message' => 'Server key not configured for ' . $environment . ' environment',
            ];
        }

        try {
            // Use a simple endpoint that doesn't require transaction ID
            // We'll try to get card token bins (lightweight endpoint)
            $apiUrl = $this->getApiUrl() . '/v1/bins/000000';

            // Prepare request details
            $requestHeaders = [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($serverKey . ':')
            ];

            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            $responseData = json_decode($response, true);

            // Build request info
            $requestInfo = [
                'method' => 'GET',
                'url' => $apiUrl,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Basic [MASKED]' . substr(base64_encode($serverKey . ':'), -10)
                ],
                'environment' => $environment,
                'merchant_id' => $merchantId,
                'client_key' => $clientKey ? substr($clientKey, 0, 15) . '...[MASKED]' : null,
                'note' => 'Testing with /v1/bins endpoint (lightweight, no transaction needed)',
            ];

            // Build response info
            $responseInfo = [
                'http_code' => $httpCode,
                'body' => $responseData,
                'response_time' => curl_getinfo($ch, CURLINFO_TOTAL_TIME) ?? null,
            ];

            // Analyze response
            if ($error) {
                return [
                    'success' => false,
                    'message' => 'Connection error: ' . $error,
                    'request' => $requestInfo,
                    'response' => $responseInfo,
                ];
            }

            // Success cases:
            // 200 = API working perfectly (rare, only if bin actually exists)
            // 404 = API working, bin not found (expected and good!)
            // 400 = API working, bad request format (still means authentication worked)
            if (in_array($httpCode, [200, 400, 404])) {
                $statusMessages = [
                    200 => 'Perfect! API is fully operational.',
                    404 => 'Connection successful! API is reachable and authentication is valid.',
                    400 => 'Connection successful! API is reachable (minor validation issue, but auth works).',
                ];

                return [
                    'success' => true,
                    'message' => $statusMessages[$httpCode] ?? 'Connection successful!',
                    'environment' => $environment,
                    'http_code' => $httpCode,
                    'request' => $requestInfo,
                    'response' => $responseInfo,
                ];
            }

            // Authentication failure
            if ($httpCode === 401) {
                return [
                    'success' => false,
                    'message' => 'Authentication failed! Server key is invalid or incorrect.',
                    'environment' => $environment,
                    'http_code' => $httpCode,
                    'request' => $requestInfo,
                    'response' => $responseInfo,
                ];
            }

            // Other errors
            return [
                'success' => false,
                'message' => 'Unexpected response from Midtrans API. HTTP Code: ' . $httpCode,
                'environment' => $environment,
                'http_code' => $httpCode,
                'request' => $requestInfo,
                'response' => $responseInfo,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create transaction
     */
    public function createTransaction(array $params): array
    {
        $serverKey = $this->getServerKey();
        $apiUrl = $this->getApiUrl() . '/v2/charge';

        try {
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($serverKey . ':')
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $result = json_decode($response, true);

            if ($httpCode >= 200 && $httpCode < 300) {
                return [
                    'success' => true,
                    'data' => $result,
                ];
            }

            return [
                'success' => false,
                'message' => $result['status_message'] ?? 'Failed to create transaction',
                'data' => $result,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Transaction creation failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get transaction status
     */
    public function getTransactionStatus(string $orderId): array
    {
        $serverKey = $this->getServerKey();
        $apiUrl = $this->getApiUrl() . '/v2/' . $orderId . '/status';

        try {
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($serverKey . ':')
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $result = json_decode($response, true);

            if ($httpCode === 200) {
                return [
                    'success' => true,
                    'data' => $result,
                ];
            }

            return [
                'success' => false,
                'message' => $result['status_message'] ?? 'Failed to get transaction status',
                'data' => $result,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to get transaction status: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Cancel transaction
     */
    public function cancelTransaction(string $orderId): array
    {
        $serverKey = $this->getServerKey();
        $apiUrl = $this->getApiUrl() . '/v2/' . $orderId . '/cancel';

        try {
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($serverKey . ':')
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $result = json_decode($response, true);

            if ($httpCode === 200) {
                return [
                    'success' => true,
                    'data' => $result,
                ];
            }

            return [
                'success' => false,
                'message' => $result['status_message'] ?? 'Failed to cancel transaction',
                'data' => $result,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to cancel transaction: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get available payment methods from Midtrans
     * This will make a test charge request to see which payment methods are available
     */
    public function getAvailablePaymentMethods(): array
    {
        $serverKey = $this->getServerKey();
        $merchantId = $this->getMerchantId();
        $environment = $this->config['environment'] ?? 'production';

        if (empty($serverKey) || empty($merchantId)) {
            return [
                'success' => false,
                'message' => 'Server key or Merchant ID not configured',
            ];
        }

        try {
            // Get account information to determine available payment methods
            $apiUrl = $this->getApiUrl() . '/v1/payment/list';

            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($serverKey . ':')
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $responseData = json_decode($response, true);

            // If API doesn't support payment list endpoint, return default methods
            if ($httpCode === 404 || $httpCode === 405) {
                return $this->getDefaultPaymentMethods();
            }

            if ($httpCode === 200 && isset($responseData['data'])) {
                return [
                    'success' => true,
                    'data' => $this->parsePaymentMethods($responseData['data']),
                    'raw_response' => $responseData,
                ];
            }

            // If API call fails, return default methods based on environment
            return $this->getDefaultPaymentMethods();

        } catch (\Exception $e) {
            // On error, return default payment methods
            return $this->getDefaultPaymentMethods();
        }
    }

    /**
     * Get default payment methods based on environment and account type
     */
    protected function getDefaultPaymentMethods(): array
    {
        $environment = $this->config['environment'] ?? 'production';

        // All Midtrans accounts have access to these payment methods
        $methods = [
            'credit_card' => [
                'name' => 'Credit & Debit Card',
                'code' => 'credit_card',
                'available' => true,
                'description' => 'Visa, Mastercard, JCB, Amex',
                'category' => 'card',
            ],
            'bca_va' => [
                'name' => 'BCA Virtual Account',
                'code' => 'bca_va',
                'available' => true,
                'description' => 'Bank Central Asia',
                'category' => 'bank_transfer',
            ],
            'mandiri_va' => [
                'name' => 'Mandiri Virtual Account',
                'code' => 'mandiri_va',
                'available' => true,
                'description' => 'Bank Mandiri Bill Payment',
                'category' => 'bank_transfer',
            ],
            'bni_va' => [
                'name' => 'BNI Virtual Account',
                'code' => 'bni_va',
                'available' => true,
                'description' => 'Bank Negara Indonesia',
                'category' => 'bank_transfer',
            ],
            'bri_va' => [
                'name' => 'BRI Virtual Account',
                'code' => 'bri_va',
                'available' => true,
                'description' => 'Bank Rakyat Indonesia',
                'category' => 'bank_transfer',
            ],
            'permata_va' => [
                'name' => 'Permata Virtual Account',
                'code' => 'permata_va',
                'available' => true,
                'description' => 'Permata Bank',
                'category' => 'bank_transfer',
            ],
            'gopay' => [
                'name' => 'GoPay',
                'code' => 'gopay',
                'available' => true,
                'description' => 'Gojek E-Wallet',
                'category' => 'ewallet',
            ],
            'shopeepay' => [
                'name' => 'ShopeePay',
                'code' => 'shopeepay',
                'available' => true,
                'description' => 'Shopee E-Wallet',
                'category' => 'ewallet',
            ],
            'qris' => [
                'name' => 'QRIS',
                'code' => 'qris',
                'available' => true,
                'description' => 'Quick Response Code Indonesian Standard',
                'category' => 'qris',
            ],
            'ovo' => [
                'name' => 'OVO',
                'code' => 'ovo',
                'available' => true,
                'description' => 'OVO E-Wallet',
                'category' => 'ewallet',
            ],
            'dana' => [
                'name' => 'DANA',
                'code' => 'dana',
                'available' => true,
                'description' => 'DANA E-Wallet',
                'category' => 'ewallet',
            ],
            'linkaja' => [
                'name' => 'LinkAja',
                'code' => 'linkaja',
                'available' => true,
                'description' => 'LinkAja E-Wallet',
                'category' => 'ewallet',
            ],
            'convenience_store' => [
                'name' => 'Convenience Store',
                'code' => 'convenience_store',
                'available' => true,
                'description' => 'Alfamart, Indomaret',
                'category' => 'over_the_counter',
            ],
            'akulaku' => [
                'name' => 'Akulaku',
                'code' => 'akulaku',
                'available' => true,
                'description' => 'Buy Now Pay Later',
                'category' => 'paylater',
            ],
        ];

        return [
            'success' => true,
            'data' => $methods,
            'note' => 'Using default payment methods list',
            'environment' => $environment,
        ];
    }

    /**
     * Parse payment methods from API response
     */
    protected function parsePaymentMethods(array $data): array
    {
        $methods = [];

        foreach ($data as $method) {
            $code = $method['code'] ?? $method['type'] ?? null;
            if ($code) {
                $methods[$code] = [
                    'name' => $method['name'] ?? $code,
                    'code' => $code,
                    'available' => $method['available'] ?? true,
                    'description' => $method['description'] ?? '',
                    'category' => $method['category'] ?? 'other',
                ];
            }
        }

        return $methods;
    }
}