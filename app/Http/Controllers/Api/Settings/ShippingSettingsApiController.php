<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Contracts\ShippingSettingsRepositoryInterface;
use Illuminate\Http\Request;

class ShippingSettingsApiController extends Controller
{
    protected $settingsRepo;
    protected $response;

    public function __construct(
        ShippingSettingsRepositoryInterface $settingsRepository,
        Response $response
    ) {
        $this->settingsRepo = $settingsRepository;
        $this->response = $response;
    }

    /**
     * Get all shipping settings
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $settings = $this->settingsRepo->getAllShippingSettings();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Shipping settings retrieved successfully')
                ->setData($settings->toArray());

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve shipping settings: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get specific setting by key
     *
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($key)
    {
        try {
            $value = $this->settingsRepo->getValue($key);

            if ($value === null) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('204')
                    ->setMessage('Setting not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Setting retrieved successfully')
                ->setData([
                    'key' => $key,
                    'value' => $value
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve setting: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update setting
     *
     * @param \Illuminate\Http\Request $request
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $key)
    {
        try {
            $validated = $request->validate([
                'value' => 'required|array'
            ]);

            $this->settingsRepo->updateValue($key, $validated['value']);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Setting updated successfully')
                ->setData([
                    'key' => $key,
                    'value' => $validated['value']
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('422')
                ->setMessage('Validation failed')
                ->setData(['errors' => $e->errors()]);

            return response()->json($this->response->generateResponse($result), 422);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update setting: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get origin address settings
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOriginAddress()
    {
        try {
            $originAddress = $this->settingsRepo->getValue('shipping.origin_address');

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Origin address retrieved successfully')
                ->setData([
                    'origin_address' => $originAddress
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve origin address: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get RajaOngkir configuration
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRajaOngkirConfig()
    {
        try {
            $rajaongkirApi = $this->settingsRepo->getValue('shipping.rajaongkir.api');
            $rajaongkirCouriers = $this->settingsRepo->getValue('shipping.rajaongkir.couriers');

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('RajaOngkir configuration retrieved successfully')
                ->setData([
                    'api' => $rajaongkirApi,
                    'couriers' => $rajaongkirCouriers
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve RajaOngkir configuration: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get shipping methods
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getShippingMethods()
    {
        try {
            $shippingMethods = $this->settingsRepo->getValue('shipping.methods');

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Shipping methods retrieved successfully')
                ->setData([
                    'methods' => $shippingMethods
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve shipping methods: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
