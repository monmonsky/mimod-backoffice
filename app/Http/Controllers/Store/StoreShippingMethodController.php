<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Shipping\ShippingMethodRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;

class StoreShippingMethodController extends Controller
{
    protected $shippingMethodRepository;
    protected $response;

    public function __construct(
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        Response $response
    ) {
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->response = $response;
    }

    /**
     * Get all active shipping methods for customer
     * GET /store-api/shipping-methods
     *
     * Optional params:
     * - weight: Filter by weight (in grams)
     */
    public function index(Request $request)
    {
        try {
            $weight = $request->input('weight', 0);

            $shippingMethods = $this->shippingMethodRepository->getAllActiveForCustomer($weight);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Shipping methods retrieved successfully')
                ->setData($shippingMethods);

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

    /**
     * Calculate shipping cost
     * POST /store-api/shipping-methods/{id}/calculate-cost
     *
     * Request body:
     * - weight: required (in grams)
     * - destination_city_id: optional (for RajaOngkir)
     * - destination_subdistrict_id: optional (for RajaOngkir)
     */
    public function calculateCost(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'weight' => 'required|integer|min:1', // in grams
                'destination_city_id' => 'nullable|integer', // for RajaOngkir
                'destination_subdistrict_id' => 'nullable|integer', // for RajaOngkir
            ]);

            $shippingMethod = $this->shippingMethodRepository->findById($id);

            if (!$shippingMethod) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Shipping method not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            if (!$shippingMethod->is_active) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage('Shipping method is not active')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 400);
            }

            $resultData = $this->shippingMethodRepository->calculateCost($id, $validated['weight']);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Shipping cost calculated successfully')
                ->setData($resultData);

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
                ->setStatusCode('400')
                ->setMessage('Failed to calculate shipping cost: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 400);
        }
    }
}
