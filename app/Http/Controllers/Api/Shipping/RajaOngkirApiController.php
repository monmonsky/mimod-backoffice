<?php

namespace App\Http\Controllers\Api\Shipping;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Services\Shipping\RajaOngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RajaOngkirApiController extends Controller
{
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Get RajaOngkir service instance with config
     * Priority: 1. Database config, 2. .env fallback
     */
    protected function getRajaOngkirService()
    {
        // Try to get RajaOngkir config from database first
        $config = DB::table('shipping_method_configs')
            ->where('provider', 'rajaongkir')
            ->first();

        if ($config) {
            // Get config items from database
            $configItems = DB::table('shipping_method_config_items')
                ->where('shipping_method_config_id', $config->id)
                ->get()
                ->mapWithKeys(function($item) {
                    $value = $item->is_encrypted
                        ? \Illuminate\Support\Facades\Crypt::decryptString($item->value)
                        : $item->value;
                    return [$item->key => $value];
                })
                ->toArray();

            // Add flag to use Komerce API (new format)
            $configItems['use_komerce_api'] = true;

            return new RajaOngkirService($configItems);
        }

        // Fallback to .env if database config not found
        $apiKey = env('RAJAONGKIR_API_KEY');

        if (!$apiKey || $apiKey === 'your_rajaongkir_api_key_here') {
            return null;
        }

        // Use .env config
        $configItems = [
            'api_key' => $apiKey,
            'account_type' => env('RAJAONGKIR_ACCOUNT_TYPE', 'starter'),
            'use_komerce_api' => env('RAJAONGKIR_USE_KOMERCE', true),
        ];

        return new RajaOngkirService($configItems);
    }

    /**
     * Search domestic destination (Komerce API)
     * GET /api/rajaongkir/search-destination?search=jakarta&limit=10
     */
    public function searchDestination(Request $request)
    {
        try {
            $validated = $request->validate([
                'search' => 'required|string|min:3',
                'limit' => 'nullable|integer|min:1|max:999',
                'offset' => 'nullable|integer|min:0',
            ]);

            $service = $this->getRajaOngkirService();

            if (!$service) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('RajaOngkir configuration not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            // Call Komerce API endpoint for search
            $params = [
                'search' => $validated['search'],
                'limit' => $validated['limit'] ?? 999,
                'offset' => $validated['offset'] ?? 0,
            ];

            $apiResponse = $service->makeRequest('/destination/domestic-destination', 'GET', $params);

            if (!$apiResponse['success']) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage($apiResponse['message'] ?? 'Failed to search destination')
                    ->setData($apiResponse);
                return response()->json($this->response->generateResponse($result), 400);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Destination search results')
                ->setData($apiResponse['data']);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('422')
                ->setMessage('Validation error')
                ->setData($e->errors());
            return response()->json($this->response->generateResponse($result), 422);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to search destination: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Calculate domestic shipping cost (Komerce API)
     * POST /api/rajaongkir/calculate-cost
     *
     * Body:
     * {
     *   "origin": 123,
     *   "destination": 456,
     *   "weight": 1000,
     *   "courier": "jne",
     *   "price": "lowest" // or "highest"
     * }
     */
    public function calculateCost(Request $request)
    {
        try {
            $validated = $request->validate([
                'origin' => 'required|integer',
                'destination' => 'required|integer',
                'weight' => 'required|integer|min:1', // in grams
                'courier' => 'required|string',
                'price' => 'nullable|string|in:lowest,highest',
            ]);

            $service = $this->getRajaOngkirService();

            if (!$service) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('RajaOngkir configuration not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            $apiResponse = $service->calculateCost($validated);

            if (!$apiResponse['success']) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage($apiResponse['message'] ?? 'Failed to calculate shipping cost')
                    ->setData($apiResponse);
                return response()->json($this->response->generateResponse($result), 400);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Shipping cost calculated successfully')
                ->setData($apiResponse['data']);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('422')
                ->setMessage('Validation error')
                ->setData($e->errors());
            return response()->json($this->response->generateResponse($result), 422);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to calculate cost: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get all provinces
     * GET /api/rajaongkir/provinces
     */
    public function getProvinces()
    {
        try {
            $service = $this->getRajaOngkirService();

            if (!$service) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('RajaOngkir configuration not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            $apiResponse = $service->getProvinces();

            if (!$apiResponse['success']) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage($apiResponse['message'] ?? 'Failed to get provinces')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 400);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Provinces retrieved successfully')
                ->setData($apiResponse['data']);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to get provinces: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get cities by province
     * GET /api/rajaongkir/cities?province_id=1
     */
    public function getCities(Request $request)
    {
        try {
            $validated = $request->validate([
                'province_id' => 'required|integer',
            ]);

            $service = $this->getRajaOngkirService();

            if (!$service) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('RajaOngkir configuration not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            $apiResponse = $service->getCities($validated['province_id']);

            if (!$apiResponse['success']) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage($apiResponse['message'] ?? 'Failed to get cities')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 400);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Cities retrieved successfully')
                ->setData($apiResponse['data']);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('422')
                ->setMessage('Validation error')
                ->setData($e->errors());
            return response()->json($this->response->generateResponse($result), 422);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to get cities: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get districts by city
     * GET /api/rajaongkir/districts?city_id=1
     */
    public function getDistricts(Request $request)
    {
        try {
            $validated = $request->validate([
                'city_id' => 'required|integer',
            ]);

            $service = $this->getRajaOngkirService();

            if (!$service) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('RajaOngkir configuration not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            $apiResponse = $service->getDistricts($validated['city_id']);

            if (!$apiResponse['success']) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage($apiResponse['message'] ?? 'Failed to get districts')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 400);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Districts retrieved successfully')
                ->setData($apiResponse['data']);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('422')
                ->setMessage('Validation error')
                ->setData($e->errors());
            return response()->json($this->response->generateResponse($result), 422);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to get districts: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get sub-districts by district
     * GET /api/rajaongkir/sub-districts?district_id=1
     */
    public function getSubDistricts(Request $request)
    {
        try {
            $validated = $request->validate([
                'district_id' => 'required|integer',
            ]);

            $service = $this->getRajaOngkirService();

            if (!$service) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('RajaOngkir configuration not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            $apiResponse = $service->getSubDistricts($validated['district_id']);

            if (!$apiResponse['success']) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage($apiResponse['message'] ?? 'Failed to get sub-districts')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 400);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Sub-districts retrieved successfully')
                ->setData($apiResponse['data']);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('422')
                ->setMessage('Validation error')
                ->setData($e->errors());
            return response()->json($this->response->generateResponse($result), 422);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to get sub-districts: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Test RajaOngkir API connection
     * GET /api/rajaongkir/test-connection
     */
    public function testConnection()
    {
        try {
            $service = $this->getRajaOngkirService();

            if (!$service) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('RajaOngkir configuration not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            $testResult = $service->testConnection();

            $statusCode = $testResult['success'] ? 200 : 400;

            $result = (new ResultBuilder())
                ->setStatus($testResult['success'])
                ->setStatusCode((string) $statusCode)
                ->setMessage($testResult['message'])
                ->setData($testResult);

            return response()->json($this->response->generateResponse($result), $statusCode);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to test connection: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
