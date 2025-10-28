<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Catalog\ProductAttributeValueRepository;
use App\Repositories\Catalog\ProductAttributeRepository;
use Illuminate\Http\Request;

class ProductAttributeValueApiController extends Controller
{
    protected $attributeValueRepo;
    protected $attributeRepo;
    protected $response;

    public function __construct(
        ProductAttributeValueRepository $attributeValueRepo,
        ProductAttributeRepository $attributeRepo,
        Response $response
    ) {
        $this->attributeValueRepo = $attributeValueRepo;
        $this->attributeRepo = $attributeRepo;
        $this->response = $response;
    }

    /**
     * Get all attribute values or by attribute ID
     */
    public function index(Request $request)
    {
        try {
            $attributeId = $request->input('attribute_id');

            if ($attributeId) {
                $values = $this->attributeValueRepo->getByAttributeId($attributeId);
            } else {
                $activeOnly = $request->input('active_only', false);
                $values = $activeOnly
                    ? $this->attributeValueRepo->getAllActive()
                    : $this->attributeValueRepo->getAll();
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Attribute values retrieved successfully')
                ->setData($values);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve attribute values: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get attribute value by ID
     */
    public function show($id)
    {
        try {
            $value = $this->attributeValueRepo->findById($id);

            if (!$value) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Attribute value not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Attribute value retrieved successfully')
                ->setData($value);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve attribute value: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Create new attribute value
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_attribute_id' => 'required|exists:product_attributes,id',
                'value' => 'required|string|max:100',
                'slug' => 'nullable|string|max:100',
                'meta' => 'nullable|json',
                'is_active' => 'nullable|boolean',
                'sort_order' => 'nullable|integer'
            ]);

            // Decode JSON string to array if meta is provided
            if (isset($validated['meta']) && is_string($validated['meta'])) {
                $validated['meta'] = json_decode($validated['meta'], true);
            }

            $value = $this->attributeValueRepo->create($validated);

            logActivity(
                'create',
                "Created attribute value: {$value->value}",
                'product_attribute_value',
                (int) $value->id
            );

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('201')
                ->setMessage('Attribute value created successfully')
                ->setData($value);

            return response()->json($this->response->generateResponse($result), 201);
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
                ->setMessage('Failed to create attribute value: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Bulk create attribute values
     */
    public function bulkStore(Request $request)
    {
        try {
            // First, check if values is array of strings or array of objects
            $inputValues = $request->input('values', []);
            $isSimpleArray = !empty($inputValues) && is_string($inputValues[0]);

            if ($isSimpleArray) {
                // If simple array of strings, validate differently
                $validated = $request->validate([
                    'product_attribute_id' => 'required|exists:product_attributes,id',
                    'values' => 'required|array|min:1',
                    'values.*' => 'required|string|max:100'
                ]);

                // Convert simple array to array of objects
                $values = [];
                foreach ($validated['values'] as $index => $value) {
                    $values[] = [
                        'value' => $value,
                        'slug' => \Illuminate\Support\Str::slug($value),
                        'is_active' => true,
                        'sort_order' => $index + 1
                    ];
                }
                $validated['values'] = $values;
            } else {
                // Array of objects
                $validated = $request->validate([
                    'product_attribute_id' => 'required|exists:product_attributes,id',
                    'values' => 'required|array|min:1',
                    'values.*.value' => 'required|string|max:100',
                    'values.*.slug' => 'nullable|string|max:100',
                    'values.*.meta' => 'nullable', // Accept both array and JSON string
                    'values.*.is_active' => 'nullable|boolean',
                    'values.*.sort_order' => 'nullable|integer'
                ]);

                // Decode JSON strings to arrays if meta is provided
                if (isset($validated['values'])) {
                    foreach ($validated['values'] as &$value) {
                        if (isset($value['meta']) && is_string($value['meta'])) {
                            $value['meta'] = json_decode($value['meta'], true);
                        }
                    }
                }
            }

            $createdValues = $this->attributeValueRepo->bulkCreate(
                $validated['product_attribute_id'],
                $validated['values']
            );

            logActivity(
                'create',
                "Bulk created " . count($createdValues) . " attribute values",
                'product_attribute_value',
                null
            );

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('201')
                ->setMessage('Attribute values created successfully')
                ->setData($createdValues);

            return response()->json($this->response->generateResponse($result), 201);
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
                ->setMessage('Failed to create attribute values: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update attribute value
     */
    public function update(Request $request, $id)
    {
        try {
            $value = $this->attributeValueRepo->findById($id);

            if (!$value) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Attribute value not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $validated = $request->validate([
                'value' => 'nullable|string|max:100',
                'slug' => 'nullable|string|max:100',
                'meta' => 'nullable|json',
                'is_active' => 'nullable|boolean',
                'sort_order' => 'nullable|integer'
            ]);

            // Decode JSON string to array if meta is provided
            if (isset($validated['meta']) && is_string($validated['meta'])) {
                $validated['meta'] = json_decode($validated['meta'], true);
            }

            $updatedValue = $this->attributeValueRepo->update($id, $validated);

            logActivity(
                'update',
                "Updated attribute value: {$updatedValue->value}",
                'product_attribute_value',
                (int) $id
            );

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Attribute value updated successfully')
                ->setData($updatedValue);

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
                ->setMessage('Failed to update attribute value: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete attribute value
     */
    public function destroy($id)
    {
        try {
            $value = $this->attributeValueRepo->findById($id);

            if (!$value) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Attribute value not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $this->attributeValueRepo->delete($id);

            logActivity(
                'delete',
                "Deleted attribute value: {$value->value}",
                'product_attribute_value',
                (int) $id
            );

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Attribute value deleted successfully')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to delete attribute value: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
