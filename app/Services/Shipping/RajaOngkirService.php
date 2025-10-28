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
     * Get API URL based on account type or custom base URL
     */
    protected function getApiUrl(): string
    {
        // If custom base URL is provided, use it
        if (!empty($this->config['base_url'])) {
            return rtrim($this->config['base_url'], '/');
        }

        // Check if using new Komerce platform (detect by api_key format or explicit flag)
        if (!empty($this->config['use_komerce_api'])) {
            return 'https://rajaongkir.komerce.id/api/v1';
        }

        // Otherwise, use default RajaOngkir endpoints (deprecated)
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
     * Check if using new Komerce API format
     */
    protected function isKomerceApi(): bool
    {
        // Check if custom base URL contains 'komerce'
        if (!empty($this->config['base_url']) && stripos($this->config['base_url'], 'komerce') !== false) {
            return true;
        }

        // Check if explicitly set to use Komerce API
        return !empty($this->config['use_komerce_api']);
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
    public function makeRequest(string $endpoint, string $method = 'GET', array $data = []): array
    {
        $apiKey = $this->getApiKey();
        $isKomerce = $this->isKomerceApi();

        // Adjust endpoint for Komerce API if needed
        if ($isKomerce) {
            // Komerce API uses /destination/ prefix for location endpoints
            if (strpos($endpoint, '/province') === 0 || strpos($endpoint, '/city') === 0) {
                $endpoint = '/destination' . $endpoint;
            }
        }

        $apiUrl = $this->getApiUrl() . $endpoint;

        try {
            $ch = curl_init();

            // Use correct header key based on API platform
            $headers = $isKomerce ? [
                'Key: ' . $apiKey, // Capital K for Komerce
            ] : [
                'key: ' . $apiKey, // Lowercase k for old RajaOngkir
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

            // If JSON decode failed, log the raw response
            if (json_last_error() !== JSON_ERROR_NONE) {
                \Log::error('RajaOngkirService: JSON decode error', [
                    'endpoint' => $endpoint,
                    'http_code' => $httpCode,
                    'raw_response' => substr($response, 0, 1000),
                    'json_error' => json_last_error_msg(),
                ]);
                return [
                    'success' => false,
                    'message' => 'JSON decode error: ' . json_last_error_msg(),
                    'raw_response' => substr($response, 0, 1000), // First 1000 chars
                    'http_code' => $httpCode,
                ];
            }

            // Log successful responses for debugging
            if ($httpCode === 200) {
                \Log::debug('RajaOngkirService: API response', [
                    'endpoint' => $endpoint,
                    'method' => $method,
                    'http_code' => $httpCode,
                    'result_keys' => array_keys($result),
                    'has_meta' => isset($result['meta']),
                    'has_rajaongkir' => isset($result['rajaongkir']),
                    'result' => $result,
                ]);
            }

            // Check for new Komerce API format
            if ($httpCode === 200 && isset($result['meta']) && $result['meta']['code'] === 200) {
                return [
                    'success' => true,
                    'data' => $result['data'] ?? [],
                ];
            }

            // Check for old RajaOngkir API format
            if ($httpCode === 200 && isset($result['rajaongkir']['status']['code']) && $result['rajaongkir']['status']['code'] === 200) {
                return [
                    'success' => true,
                    'data' => ['results' => $result['rajaongkir']['results'] ?? []],
                ];
            }

            // Error response - handle both formats
            $errorMessage = 'Request failed';
            if (isset($result['meta']['message'])) {
                $errorMessage = $result['meta']['message'];
            } elseif (isset($result['rajaongkir']['status']['description'])) {
                $errorMessage = $result['rajaongkir']['status']['description'];
            }

            return [
                'success' => false,
                'message' => $errorMessage,
                'data' => $result,
                'http_code' => $httpCode,
                'url' => $apiUrl, // Include URL for debugging
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
        $accountType = $this->config['account_type'] ?? 'starter';

        if (empty($apiKey)) {
            return [
                'success' => false,
                'message' => 'API key not configured',
            ];
        }

        try {
            // Detect API format and use correct endpoint
            $isKomerce = $this->isKomerceApi();

            // Use correct endpoint based on API version
            if ($isKomerce) {
                // New Komerce API: /api/v1/destination/province
                $apiUrl = $this->getApiUrl() . '/destination/province';
                $requestHeaders = [
                    'Key: ' . $apiKey, // Capital K for Komerce
                ];
            } else {
                // Old RajaOngkir API: /province
                $apiUrl = $this->getApiUrl() . '/province';
                $requestHeaders = [
                    'key: ' . $apiKey, // Lowercase k for old API
                ];
            }

            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $startTime = microtime(true);
            $response = curl_exec($ch);
            $responseTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            // Try to decode JSON response
            $responseData = json_decode($response, true);

            // If JSON decode failed, store raw response for debugging
            if (json_last_error() !== JSON_ERROR_NONE) {
                $responseData = [
                    'raw_response' => $response,
                    'json_error' => json_last_error_msg(),
                ];
            }

            // Build request info
            $requestInfo = [
                'method' => 'GET',
                'url' => $apiUrl,
                'headers' => $isKomerce ? [
                    'Key' => substr($apiKey, 0, 10) . '...[MASKED]',
                ] : [
                    'key' => substr($apiKey, 0, 10) . '...[MASKED]',
                ],
                'account_type' => $accountType,
                'api_platform' => $isKomerce ? 'Komerce (New)' : 'RajaOngkir (Old/Deprecated)',
                'note' => $isKomerce
                    ? 'Testing with Komerce /api/v1/destination/province endpoint'
                    : 'Testing with RajaOngkir /province endpoint (deprecated)',
            ];

            // Build response info
            $responseInfo = [
                'http_code' => $httpCode,
                'body' => $responseData,
                'response_time' => round($responseTime, 2) . ' ms',
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

            // Success case - check multiple response formats

            // Format 1: RajaOngkir old format
            if ($httpCode === 200 && isset($responseData['rajaongkir']['status']['code']) && $responseData['rajaongkir']['status']['code'] === 200) {
                $provinceCount = count($responseData['rajaongkir']['results'] ?? []);

                return [
                    'success' => true,
                    'message' => 'Connection successful! API is reachable and authentication is valid. (RajaOngkir format)',
                    'account_type' => $accountType,
                    'http_code' => $httpCode,
                    'province_count' => $provinceCount,
                    'request' => $requestInfo,
                    'response' => $responseInfo,
                ];
            }

            // Format 2: Komerce new format (might be different)
            if ($httpCode === 200 && (isset($responseData['success']) || isset($responseData['data']) || is_array($responseData))) {
                $provinceCount = 0;

                // Try to count provinces in different possible formats
                if (isset($responseData['data']) && is_array($responseData['data'])) {
                    $provinceCount = count($responseData['data']);
                } elseif (isset($responseData['provinces']) && is_array($responseData['provinces'])) {
                    $provinceCount = count($responseData['provinces']);
                } elseif (is_array($responseData)) {
                    $provinceCount = count($responseData);
                }

                return [
                    'success' => true,
                    'message' => 'Connection successful! API is reachable and authentication is valid. (New Komerce format)',
                    'account_type' => $accountType,
                    'http_code' => $httpCode,
                    'province_count' => $provinceCount,
                    'request' => $requestInfo,
                    'response' => $responseInfo,
                    'note' => 'API responded with new format. You may need to verify the response structure.',
                ];
            }

            // Format 3: HTTP 200 but empty/unexpected response
            if ($httpCode === 200) {
                return [
                    'success' => false,
                    'message' => 'API returned HTTP 200 but response format is unexpected. Please check the raw response below.',
                    'account_type' => $accountType,
                    'http_code' => $httpCode,
                    'request' => $requestInfo,
                    'response' => $responseInfo,
                    'note' => 'The API endpoint might be valid but the response format has changed. Check response body for details.',
                ];
            }

            // API Deprecated (410)
            if ($httpCode === 410) {
                $message = 'RajaOngkir API endpoint is deprecated! ';
                if (isset($responseData['message'])) {
                    $message .= $responseData['message'];
                } else {
                    $message .= 'Please migrate to the new platform at https://collaborator.komerce.id';
                }

                return [
                    'success' => false,
                    'message' => $message,
                    'http_code' => $httpCode,
                    'request' => $requestInfo,
                    'response' => $responseInfo,
                ];
            }

            // Authentication failure
            if ($httpCode === 401 || (isset($responseData['rajaongkir']['status']['code']) && $responseData['rajaongkir']['status']['code'] === 401)) {
                return [
                    'success' => false,
                    'message' => 'Authentication failed! API key is invalid or incorrect.',
                    'http_code' => $httpCode,
                    'request' => $requestInfo,
                    'response' => $responseInfo,
                ];
            }

            // Other errors
            return [
                'success' => false,
                'message' => $responseData['rajaongkir']['status']['description'] ?? $responseData['message'] ?? 'Unexpected response from RajaOngkir API',
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
     * Get all cities (optionally filtered by province)
     * Komerce API: /destination/city/{province_id}
     */
    public function getCities(?int $provinceId = null): array
    {
        $isKomerce = $this->isKomerceApi();

        // Komerce API uses path parameter
        if ($isKomerce) {
            if (!$provinceId) {
                return [
                    'success' => false,
                    'message' => 'Province ID is required for Komerce API',
                ];
            }

            // Use path parameter: /destination/city/{province_id}
            $endpoint = "/destination/city/{$provinceId}";
            return $this->makeRequest($endpoint, 'GET');
        }

        // For old RajaOngkir API - uses query parameter
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
     * Get districts for a city
     * Komerce API: /destination/district/{city_id}
     */
    public function getDistricts(int $cityId): array
    {
        $isKomerce = $this->isKomerceApi();

        // Komerce API uses path parameter
        if ($isKomerce) {
            $endpoint = "/destination/district/{$cityId}";
            return $this->makeRequest($endpoint, 'GET');
        }

        // For old RajaOngkir API
        return $this->makeRequest('/subdistrict', 'GET', ['city' => $cityId]);
    }

    /**
     * Get sub-districts for a district
     * Komerce API: /destination/sub-district/{district_id}
     */
    public function getSubDistricts(int $districtId): array
    {
        $isKomerce = $this->isKomerceApi();

        // Komerce API uses path parameter
        if ($isKomerce) {
            $endpoint = "/destination/sub-district/{$districtId}";
            return $this->makeRequest($endpoint, 'GET');
        }

        // Old API doesn't support sub-districts
        return [
            'success' => false,
            'message' => 'Sub-districts not supported in old RajaOngkir API',
        ];
    }

    /**
     * Calculate shipping cost
     * Komerce API: /calculate/domestic-cost
     */
    public function calculateCost(array $params): array
    {
        $isKomerce = $this->isKomerceApi();

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

        \Log::info('RajaOngkirService::calculateCost', [
            'params' => $params,
            'is_komerce' => $isKomerce,
            'api_url' => $this->getApiUrl(),
        ]);

        // For Komerce API, use domestic-cost calculation
        if ($isKomerce) {
            $result = $this->makeRequest('/calculate/domestic-cost', 'POST', $params);
            \Log::info('RajaOngkirService::calculateCost result (Komerce)', [
                'result' => $result,
            ]);
            return $result;
        }

        // For old RajaOngkir API
        $result = $this->makeRequest('/cost', 'POST', $params);
        \Log::info('RajaOngkirService::calculateCost result (Old API)', [
            'result' => $result,
        ]);
        return $result;
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

    /**
     * Sync provinces from RajaOngkir API to database
     */
    public function syncProvinces(): array
    {
        $result = $this->getProvinces();

        if (!$result['success']) {
            return [
                'success' => false,
                'message' => 'Failed to fetch provinces: ' . ($result['message'] ?? 'Unknown error'),
                'debug' => $result,
            ];
        }

        try {
            // Get provinces data - already formatted by makeRequest
            $provinces = $result['data'] ?? [];

            if (empty($provinces) || !is_array($provinces)) {
                return [
                    'success' => false,
                    'message' => 'No provinces data found in API response',
                    'debug' => $result,
                ];
            }

            $synced = 0;
            $updated = 0;

            foreach ($provinces as $province) {
                // Handle both old and new formats
                // New Komerce: id, name
                // Old RajaOngkir: province_id, province
                $provinceId = $province['id'] ?? $province['province_id'] ?? null;
                $provinceName = $province['name'] ?? $province['province'] ?? null;

                if (!$provinceId || !$provinceName) {
                    continue;
                }

                $existing = \App\Models\ShippingLocation::where('type', 'province')
                    ->where('rajaongkir_id', $provinceId)
                    ->first();

                if ($existing) {
                    $existing->update([
                        'name' => $provinceName,
                    ]);
                    $updated++;
                } else {
                    \App\Models\ShippingLocation::create([
                        'type' => 'province',
                        'rajaongkir_id' => $provinceId,
                        'name' => $provinceName,
                    ]);
                    $synced++;
                }
            }

            return [
                'success' => true,
                'message' => "Successfully synced {$synced} new provinces and updated {$updated} existing provinces",
                'synced' => $synced,
                'updated' => $updated,
                'total' => count($provinces),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to sync provinces: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Sync cities from RajaOngkir API to database
     */
    public function syncCities(): array
    {
        $isKomerce = $this->isKomerceApi();

        try {
            $synced = 0;
            $updated = 0;
            $skipped = 0;
            $totalCities = 0;

            // For Komerce API, we need to fetch cities per province
            if ($isKomerce) {
                // Get all provinces from database
                $provinces = \App\Models\ShippingLocation::where('type', 'province')->get();

                if ($provinces->isEmpty()) {
                    return [
                        'success' => false,
                        'message' => 'No provinces found in database. Please sync provinces first.',
                    ];
                }

                // Fetch cities for each province
                foreach ($provinces as $province) {
                    $result = $this->getCities($province->rajaongkir_id);

                    if (!$result['success']) {
                        continue; // Skip this province if fetch fails
                    }

                    $cities = $result['data'] ?? [];
                    $totalCities += count($cities);

                    foreach ($cities as $city) {
                        $cityId = $city['id'] ?? $city['city_id'] ?? null;
                        $cityName = $city['name'] ?? $city['city_name'] ?? null;
                        $provinceId = $city['province_id'] ?? $province->rajaongkir_id;
                        $cityType = $city['type'] ?? null;
                        $postalCode = $city['postal_code'] ?? null;

                        if (!$cityId || !$cityName) {
                            $skipped++;
                            continue;
                        }

                        $existing = \App\Models\ShippingLocation::where('type', 'city')
                            ->where('rajaongkir_id', $cityId)
                            ->first();

                        if ($existing) {
                            $existing->update([
                                'name' => $cityName,
                                'province_id' => $province->id,
                                'type_name' => $cityType,
                                'postal_code' => $postalCode,
                            ]);
                            $updated++;
                        } else {
                            \App\Models\ShippingLocation::create([
                                'type' => 'city',
                                'rajaongkir_id' => $cityId,
                                'name' => $cityName,
                                'province_id' => $province->id,
                                'type_name' => $cityType,
                                'postal_code' => $postalCode,
                            ]);
                            $synced++;
                        }
                    }
                }
            } else {
                // For old RajaOngkir API, fetch all cities at once
                $result = $this->getCities();

                if (!$result['success']) {
                    return [
                        'success' => false,
                        'message' => 'Failed to fetch cities: ' . ($result['message'] ?? 'Unknown error'),
                        'debug' => $result,
                    ];
                }

                $cities = $result['data'] ?? [];
                $totalCities = count($cities);

                if (empty($cities) || !is_array($cities)) {
                    return [
                        'success' => false,
                        'message' => 'No cities data found in API response',
                        'debug' => $result,
                    ];
                }

                foreach ($cities as $city) {
                    $cityId = $city['id'] ?? $city['city_id'] ?? null;
                    $cityName = $city['name'] ?? $city['city_name'] ?? null;
                    $provinceId = $city['province_id'] ?? null;
                    $cityType = $city['type'] ?? null;
                    $postalCode = $city['postal_code'] ?? null;

                    if (!$cityId || !$cityName || !$provinceId) {
                        $skipped++;
                        continue;
                    }

                    $province = \App\Models\ShippingLocation::where('type', 'province')
                        ->where('rajaongkir_id', $provinceId)
                        ->first();

                    if (!$province) {
                        $skipped++;
                        continue;
                    }

                    $existing = \App\Models\ShippingLocation::where('type', 'city')
                        ->where('rajaongkir_id', $cityId)
                        ->first();

                    if ($existing) {
                        $existing->update([
                            'name' => $cityName,
                            'province_id' => $province->id,
                            'type_name' => $cityType,
                            'postal_code' => $postalCode,
                        ]);
                        $updated++;
                    } else {
                        \App\Models\ShippingLocation::create([
                            'type' => 'city',
                            'rajaongkir_id' => $cityId,
                            'name' => $cityName,
                            'province_id' => $province->id,
                            'type_name' => $cityType,
                            'postal_code' => $postalCode,
                        ]);
                        $synced++;
                    }
                }
            }

            return [
                'success' => true,
                'message' => "Successfully synced {$synced} new cities and updated {$updated} existing cities" . ($skipped > 0 ? " ({$skipped} skipped)" : ""),
                'synced' => $synced,
                'updated' => $updated,
                'skipped' => $skipped,
                'total' => $totalCities,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to sync cities: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Sync all locations (provinces and cities) from RajaOngkir API
     */
    public function syncAllLocations(): array
    {
        // First sync provinces
        $provincesResult = $this->syncProvinces();

        if (!$provincesResult['success']) {
            return $provincesResult;
        }

        // Then sync cities
        $citiesResult = $this->syncCities();

        if (!$citiesResult['success']) {
            return $citiesResult;
        }

        return [
            'success' => true,
            'message' => 'Successfully synced all locations',
            'provinces' => $provincesResult,
            'cities' => $citiesResult,
        ];
    }
}