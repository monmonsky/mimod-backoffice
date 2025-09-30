<?php

namespace App\Services\Shipping;

class RajaOngkirService
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get API URL based on account type
     */
    protected function getApiUrl(): string
    {
        $accountType = $this->config['account_type'] ?? 'starter';

        switch ($accountType) {
            case 'pro':
                return 'https://pro.rajaongkir.com/api';
            case 'basic':
                return 'https://api.rajaongkir.com/basic';
            case 'starter':
            default:
                return 'https://api.rajaongkir.com/starter';
        }
    }

    /**
     * Get API key
     */
    protected function getApiKey(): string
    {
        return $this->config['api_key'] ?? '';
    }

    /**
     * Make HTTP request to RajaOngkir API
     */
    protected function makeRequest(string $endpoint, string $method = 'GET', array $data = []): array
    {
        $apiKey = $this->getApiKey();
        $apiUrl = $this->getApiUrl() . $endpoint;

        try {
            $ch = curl_init();

            $headers = [
                'key: ' . $apiKey,
            ];

            if ($method === 'POST') {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                $headers[] = 'content-type: application/x-www-form-urlencoded';
            } else {
                if (!empty($data)) {
                    $apiUrl .= '?' . http_build_query($data);
                }
            }

            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                return [
                    'success' => false,
                    'message' => 'CURL Error: ' . $error,
                ];
            }

            $result = json_decode($response, true);

            if ($httpCode === 200 && isset($result['rajaongkir']['status']['code']) && $result['rajaongkir']['status']['code'] === 200) {
                return [
                    'success' => true,
                    'data' => $result['rajaongkir'],
                ];
            }

            return [
                'success' => false,
                'message' => $result['rajaongkir']['status']['description'] ?? 'Request failed',
                'data' => $result,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Request failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Test connection to RajaOngkir API
     */
    public function testConnection(): array
    {
        $apiKey = $this->getApiKey();

        if (empty($apiKey)) {
            return [
                'success' => false,
                'message' => 'API key not configured',
            ];
        }

        // Test by getting province list (simple API call)
        $result = $this->getProvinces();

        if ($result['success']) {
            return [
                'success' => true,
                'message' => 'Connection successful! RajaOngkir API is reachable.',
                'account_type' => $this->config['account_type'] ?? 'starter',
            ];
        }

        return [
            'success' => false,
            'message' => $result['message'] ?? 'Failed to connect to RajaOngkir API',
        ];
    }

    /**
     * Get all provinces
     */
    public function getProvinces(): array
    {
        return $this->makeRequest('/province', 'GET');
    }

    /**
     * Get province by ID
     */
    public function getProvince(int $provinceId): array
    {
        return $this->makeRequest('/province', 'GET', ['id' => $provinceId]);
    }

    /**
     * Get all cities
     */
    public function getCities(?int $provinceId = null): array
    {
        $params = [];
        if ($provinceId) {
            $params['province'] = $provinceId;
        }
        return $this->makeRequest('/city', 'GET', $params);
    }

    /**
     * Get city by ID
     */
    public function getCity(int $cityId): array
    {
        return $this->makeRequest('/city', 'GET', ['id' => $cityId]);
    }

    /**
     * Get all subdistricts (kecamatan) - only for Pro account
     */
    public function getSubdistricts(int $cityId): array
    {
        return $this->makeRequest('/subdistrict', 'GET', ['city' => $cityId]);
    }

    /**
     * Calculate shipping cost
     */
    public function calculateCost(array $params): array
    {
        // Required: origin, destination, weight, courier
        $required = ['origin', 'destination', 'weight', 'courier'];

        foreach ($required as $field) {
            if (!isset($params[$field])) {
                return [
                    'success' => false,
                    'message' => "Field '{$field}' is required",
                ];
            }
        }

        return $this->makeRequest('/cost', 'POST', $params);
    }

    /**
     * Get international shipping cost (Pro only)
     */
    public function calculateInternationalCost(array $params): array
    {
        return $this->makeRequest('/v2/internationalCost', 'POST', $params);
    }

    /**
     * Get waybill/tracking info
     */
    public function trackWaybill(string $waybill, string $courier): array
    {
        return $this->makeRequest('/waybill', 'POST', [
            'waybill' => $waybill,
            'courier' => $courier,
        ]);
    }

    /**
     * Get available couriers based on account type
     */
    public function getAvailableCouriers(): array
    {
        $accountType = $this->config['account_type'] ?? 'starter';

        $couriers = [
            'jne' => 'JNE',
            'pos' => 'POS Indonesia',
            'tiki' => 'TIKI',
        ];

        if (in_array($accountType, ['basic', 'pro'])) {
            $couriers = array_merge($couriers, [
                'rpx' => 'RPX',
                'esl' => 'ESL',
                'pcp' => 'PCP',
                'pandu' => 'Pandu Logistics',
                'wahana' => 'Wahana',
                'sicepat' => 'SiCepat',
                'jnt' => 'J&T Express',
                'pahala' => 'Pahala',
                'sap' => 'SAP',
                'jet' => 'JET Express',
                'indah' => 'Indah Logistic',
                'dse' => 'DSE',
                'slis' => 'SLIS',
                'first' => 'First Logistics',
                'ncs' => 'NCS',
                'star' => 'Star Cargo',
            ]);
        }

        if ($accountType === 'pro') {
            $couriers = array_merge($couriers, [
                'ninja' => 'Ninja Express',
                'lion' => 'Lion Parcel',
                'idl' => 'IDL',
                'rex' => 'REX',
                'ide' => 'ID Express',
                'sentral' => 'Sentral Cargo',
            ]);
        }

        return $couriers;
    }
}