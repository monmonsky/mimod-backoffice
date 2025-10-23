<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ShippingApiController extends Controller
{
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Get list of provinces
     */
    public function getProvinces()
    {
        try {
            $apiKey = config('services.rajaongkir.api_key');

            if (!$apiKey) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('500')
                    ->setMessage('RajaOngkir API key not configured')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 500);
            }

            // Cache for 24 hours
            $provinces = Cache::remember('rajaongkir_provinces', 86400, function () use ($apiKey) {
                $response = Http::withHeaders([
                    'key' => $apiKey
                ])->get('https://api.rajaongkir.com/starter/province');

                if ($response->successful()) {
                    $data = $response->json();
                    return $data['rajaongkir']['results'] ?? [];
                }

                return [];
            });

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Provinces retrieved successfully')
                ->setData($provinces);

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
     * Get list of cities by province
     */
    public function getCities(Request $request)
    {
        try {
            $validated = $request->validate([
                'province_id' => 'required|integer'
            ]);

            $apiKey = config('services.rajaongkir.api_key');

            if (!$apiKey) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('500')
                    ->setMessage('RajaOngkir API key not configured')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 500);
            }

            $provinceId = $validated['province_id'];

            // Cache for 24 hours
            $cities = Cache::remember("rajaongkir_cities_{$provinceId}", 86400, function () use ($apiKey, $provinceId) {
                $response = Http::withHeaders([
                    'key' => $apiKey
                ])->get('https://api.rajaongkir.com/starter/city', [
                    'province' => $provinceId
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $data['rajaongkir']['results'] ?? [];
                }

                return [];
            });

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Cities retrieved successfully')
                ->setData($cities);

            return response()->json($this->response->generateResponse($result), 200);

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
     * Calculate shipping cost
     */
    public function calculateCost(Request $request)
    {
        try {
            $validated = $request->validate([
                'origin' => 'required|integer',          // City ID of origin
                'destination' => 'required|integer',     // City ID of destination
                'weight' => 'required|integer|min:1',    // Weight in grams
                'courier' => 'required|string|in:jne,pos,tiki' // Courier code
            ]);

            $apiKey = config('services.rajaongkir.api_key');

            if (!$apiKey) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('500')
                    ->setMessage('RajaOngkir API key not configured')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 500);
            }

            $response = Http::withHeaders([
                'key' => $apiKey
            ])->post('https://api.rajaongkir.com/starter/cost', [
                'origin' => $validated['origin'],
                'destination' => $validated['destination'],
                'weight' => $validated['weight'],
                'courier' => $validated['courier']
            ]);

            if ($response->failed()) {
                throw new \Exception('Failed to get shipping cost from RajaOngkir: ' . $response->body());
            }

            $data = $response->json();
            $results = $data['rajaongkir']['results'] ?? [];

            // Format response
            $shippingOptions = [];
            foreach ($results as $result) {
                $courierCode = $result['code'] ?? '';
                $courierName = $result['name'] ?? '';

                foreach ($result['costs'] ?? [] as $cost) {
                    $shippingOptions[] = [
                        'courier_code' => $courierCode,
                        'courier_name' => $courierName,
                        'service' => $cost['service'] ?? '',
                        'description' => $cost['description'] ?? '',
                        'cost' => $cost['cost'][0]['value'] ?? 0,
                        'etd' => $cost['cost'][0]['etd'] ?? '',
                        'note' => $cost['cost'][0]['note'] ?? ''
                    ];
                }
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Shipping cost calculated successfully')
                ->setData([
                    'origin' => $data['rajaongkir']['origin_details'] ?? null,
                    'destination' => $data['rajaongkir']['destination_details'] ?? null,
                    'weight' => $validated['weight'],
                    'shipping_options' => $shippingOptions
                ]);

            return response()->json($this->response->generateResponse($result), 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('422')
                ->setMessage($e->validator->errors()->first())
                ->setData(['errors' => $e->validator->errors()]);

            return response()->json($this->response->generateResponse($result), 422);

        } catch (\Exception $e) {
            \Log::error('Shipping cost calculation failed: ' . $e->getMessage());

            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to calculate shipping cost: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get waybill/tracking information
     */
    public function trackShipment(Request $request)
    {
        try {
            $validated = $request->validate([
                'waybill' => 'required|string',           // Tracking number
                'courier' => 'required|string|in:jne,pos,tiki' // Courier code
            ]);

            $apiKey = config('services.rajaongkir.api_key');

            if (!$apiKey) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('500')
                    ->setMessage('RajaOngkir API key not configured')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 500);
            }

            $response = Http::withHeaders([
                'key' => $apiKey
            ])->post('https://api.rajaongkir.com/starter/waybill', [
                'waybill' => $validated['waybill'],
                'courier' => $validated['courier']
            ]);

            if ($response->failed()) {
                throw new \Exception('Failed to track shipment from RajaOngkir: ' . $response->body());
            }

            $data = $response->json();
            $trackingResult = $data['rajaongkir']['result'] ?? null;

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Tracking information retrieved successfully')
                ->setData($trackingResult);

            return response()->json($this->response->generateResponse($result), 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('422')
                ->setMessage($e->validator->errors()->first())
                ->setData(['errors' => $e->validator->errors()]);

            return response()->json($this->response->generateResponse($result), 422);

        } catch (\Exception $e) {
            \Log::error('Shipment tracking failed: ' . $e->getMessage());

            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to track shipment: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
