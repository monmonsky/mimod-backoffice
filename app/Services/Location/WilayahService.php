<?php

namespace App\Services\Location;

use Illuminate\Support\Facades\Cache;

/**
 * Service for interacting with Wilayah.id API
 * API Documentation: https://wilayah.id/
 */
class WilayahService
{
    protected $baseUrl = 'https://wilayah.id/api';
    protected $cacheDuration = 86400; // 24 hours in seconds

    /**
     * Make HTTP request to Wilayah.id API
     */
    protected function makeRequest(string $endpoint): array
    {
        $url = $this->baseUrl . $endpoint;

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                \Log::error('WilayahService: CURL error', [
                    'endpoint' => $endpoint,
                    'error' => $error,
                ]);
                return [
                    'success' => false,
                    'message' => 'Request failed: ' . $error,
                ];
            }

            $result = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                \Log::error('WilayahService: JSON decode error', [
                    'endpoint' => $endpoint,
                    'http_code' => $httpCode,
                    'json_error' => json_last_error_msg(),
                ]);
                return [
                    'success' => false,
                    'message' => 'JSON decode error: ' . json_last_error_msg(),
                ];
            }

            if ($httpCode === 200 && isset($result['data'])) {
                return [
                    'success' => true,
                    'data' => $result['data'],
                    'meta' => $result['meta'] ?? null,
                ];
            }

            return [
                'success' => false,
                'message' => 'Unexpected response format',
                'http_code' => $httpCode,
            ];

        } catch (\Exception $e) {
            \Log::error('WilayahService: Request exception', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'message' => 'Request failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get all provinces
     * Endpoint: /provinces.json
     */
    public function getProvinces(): array
    {
        $cacheKey = 'wilayah_provinces';

        return Cache::remember($cacheKey, $this->cacheDuration, function () {
            return $this->makeRequest('/provinces.json');
        });
    }

    /**
     * Get regencies (cities/kabupaten) by province code
     * Endpoint: /regencies/{province_code}.json
     *
     * @param string $provinceCode Province code (e.g., "32" for Jawa Barat)
     */
    public function getRegencies(string $provinceCode): array
    {
        $cacheKey = "wilayah_regencies_{$provinceCode}";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($provinceCode) {
            return $this->makeRequest("/regencies/{$provinceCode}.json");
        });
    }

    /**
     * Get districts (kecamatan) by regency code
     * Endpoint: /districts/{regency_code}.json
     *
     * @param string $regencyCode Regency code (e.g., "32.73" for Kota Bandung)
     */
    public function getDistricts(string $regencyCode): array
    {
        $cacheKey = "wilayah_districts_{$regencyCode}";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($regencyCode) {
            return $this->makeRequest("/districts/{$regencyCode}.json");
        });
    }

    /**
     * Get villages (kelurahan/desa) by district code
     * Endpoint: /villages/{district_code}.json
     *
     * @param string $districtCode District code (e.g., "32.73.01" for Bandung Wetan)
     */
    public function getVillages(string $districtCode): array
    {
        $cacheKey = "wilayah_villages_{$districtCode}";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($districtCode) {
            return $this->makeRequest("/villages/{$districtCode}.json");
        });
    }

    /**
     * Clear all cached data
     */
    public function clearCache(): bool
    {
        try {
            // Clear provinces cache
            Cache::forget('wilayah_provinces');

            // Clear regencies cache (for common provinces)
            $commonProvinces = ['11', '12', '13', '14', '15', '16', '17', '18', '19', '21', '31', '32', '33', '34', '35', '36'];
            foreach ($commonProvinces as $code) {
                Cache::forget("wilayah_regencies_{$code}");
            }

            return true;
        } catch (\Exception $e) {
            \Log::error('WilayahService: Failed to clear cache', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Test connection to Wilayah.id API
     */
    public function testConnection(): array
    {
        try {
            $startTime = microtime(true);
            $result = $this->makeRequest('/provinces.json');
            $responseTime = (microtime(true) - $startTime) * 1000;

            if ($result['success']) {
                return [
                    'success' => true,
                    'message' => 'Connection successful! API is reachable.',
                    'province_count' => count($result['data']),
                    'response_time' => round($responseTime, 2) . ' ms',
                    'sample_data' => array_slice($result['data'], 0, 3),
                ];
            }

            return [
                'success' => false,
                'message' => $result['message'] ?? 'Connection failed',
                'response_time' => round($responseTime, 2) . ' ms',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage(),
            ];
        }
    }
}