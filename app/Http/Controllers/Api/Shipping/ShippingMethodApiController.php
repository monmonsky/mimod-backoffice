<?php

namespace App\Http\Controllers\Api\Shipping;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Shipping\ShippingMethodRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;

class ShippingMethodApiController extends Controller
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
     * Get all shipping methods
     * GET /api/shipping-methods
     */
    public function index(Request $request)
    {
        try {
            $query = $this->shippingMethodRepository->query();

            // Filter by active status
            if ($request->has('is_active')) {
                $query->where('is_active', $request->is_active === 'true' || $request->is_active === '1');
            }

            // Filter by type
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            // Filter by provider
            if ($request->has('provider')) {
                $query->where('provider', $request->provider);
            }

            // Search by name or code
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%")
                      ->orWhere('code', 'ILIKE', "%{$search}%");
                });
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'sort_order');
            $sortOrder = $request->input('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->input('per_page', 15);
            if ($perPage === 'all') {
                $shippingMethods = $query->get();
                $resultData = $shippingMethods;
            } else {
                $shippingMethods = $query->paginate($perPage);
                $resultData = [
                    'data' => $shippingMethods->items(),
                    'pagination' => [
                        'total' => $shippingMethods->total(),
                        'per_page' => $shippingMethods->perPage(),
                        'current_page' => $shippingMethods->currentPage(),
                        'last_page' => $shippingMethods->lastPage(),
                        'from' => $shippingMethods->firstItem(),
                        'to' => $shippingMethods->lastItem(),
                    ]
                ];
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Shipping methods retrieved successfully')
                ->setData($resultData);

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
     * Create new shipping method
     * POST /api/shipping-methods
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'code' => 'required|string|max:100|unique:shipping_methods,code',
                'name' => 'required|string|max:200',
                'type' => 'required|string|in:manual,rajaongkir,custom',
                'provider' => 'nullable|string|max:50',
                'base_cost' => 'nullable|numeric|min:0',
                'cost_per_kg' => 'nullable|numeric|min:0',
                'min_weight' => 'nullable|integer|min:0',
                'max_weight' => 'nullable|integer|min:0',
                'estimated_delivery' => 'nullable|string|max:100',
                'is_active' => 'boolean',
                'sort_order' => 'nullable|integer',
            ]);

            $shippingMethod = $this->shippingMethodRepository->create($validated);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('201')
                ->setMessage('Shipping method created successfully')
                ->setData($shippingMethod);

            return response()->json($this->response->generateResponse($result), 201);
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
                ->setMessage('Failed to create shipping method: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get shipping method by ID
     * GET /api/shipping-methods/{id}
     */
    public function show($id)
    {
        try {
            $shippingMethod = $this->shippingMethodRepository->findById($id);

            if (!$shippingMethod) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Shipping method not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            // Get config
            $config = $this->shippingMethodRepository->getConfig($id);

            $data = [
                'shipping_method' => $shippingMethod,
                'config' => $config
            ];

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Shipping method retrieved successfully')
                ->setData($data);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve shipping method: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update shipping method
     * PUT /api/shipping-methods/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $shippingMethod = $this->shippingMethodRepository->findById($id);

            if (!$shippingMethod) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Shipping method not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            $validated = $request->validate([
                'code' => 'sometimes|string|max:100|unique:shipping_methods,code,' . $id,
                'name' => 'sometimes|string|max:200',
                'type' => 'sometimes|string|in:manual,rajaongkir,custom',
                'provider' => 'nullable|string|max:50',
                'base_cost' => 'nullable|numeric|min:0',
                'cost_per_kg' => 'nullable|numeric|min:0',
                'min_weight' => 'nullable|integer|min:0',
                'max_weight' => 'nullable|integer|min:0',
                'estimated_delivery' => 'nullable|string|max:100',
                'is_active' => 'sometimes|boolean',
                'sort_order' => 'nullable|integer',
            ]);

            $updated = $this->shippingMethodRepository->update($id, $validated);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Shipping method updated successfully')
                ->setData($updated);

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
                ->setMessage('Failed to update shipping method: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete shipping method
     * DELETE /api/shipping-methods/{id}
     */
    public function destroy($id)
    {
        try {
            $this->shippingMethodRepository->delete($id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Shipping method deleted successfully')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('400')
                ->setMessage('Failed to delete shipping method: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 400);
        }
    }

    /**
     * Toggle shipping method active status
     * POST /api/shipping-methods/{id}/toggle-active
     */
    public function toggleActive($id)
    {
        try {
            $updated = $this->shippingMethodRepository->toggleActive($id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Shipping method status updated successfully')
                ->setData($updated);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('400')
                ->setMessage('Failed to toggle shipping method status: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 400);
        }
    }

    /**
     * Update shipping method configuration
     * POST /api/shipping-methods/{id}/config
     */
    public function updateConfig(Request $request, $id)
    {
        try {
            $shippingMethod = $this->shippingMethodRepository->findById($id);

            if (!$shippingMethod) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Shipping method not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            $validated = $request->validate([
                'configs' => 'required|array',
                'configs.*.key' => 'required|string',
                'configs.*.value' => 'required|string',
                'configs.*.is_encrypted' => 'boolean',
            ]);

            foreach ($validated['configs'] as $config) {
                $this->shippingMethodRepository->setConfig(
                    $id,
                    $config['key'],
                    $config['value'],
                    $config['is_encrypted'] ?? false
                );
            }

            // Get updated config
            $updatedConfig = $this->shippingMethodRepository->getConfig($id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Shipping method configuration updated successfully')
                ->setData($updatedConfig);

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
                ->setMessage('Failed to update shipping method configuration: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Calculate shipping cost
     * POST /api/shipping-methods/{id}/calculate-cost
     */
    public function calculateCost(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'weight' => 'required|integer|min:1', // in grams
                'destination_city_id' => 'nullable|integer', // for RajaOngkir
                'destination_subdistrict_id' => 'nullable|integer', // for RajaOngkir
            ]);

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
