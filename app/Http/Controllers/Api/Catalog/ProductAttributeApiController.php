<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Catalog\ProductAttributeRepository;
use Illuminate\Http\Request;

class ProductAttributeApiController extends Controller
{
    protected $attributeRepo;
    protected $response;

    public function __construct(ProductAttributeRepository $attributeRepo, Response $response)
    {
        $this->attributeRepo = $attributeRepo;
        $this->response = $response;
    }

    /**
     * Get all attributes
     */
    public function index(Request $request)
    {
        try {
            $activeOnly = $request->input('active_only', false);
            $withValues = $request->input('with_values', false);

            if ($withValues) {
                // Get attributes with their values
                $attributes = $this->attributeRepo->getAllWithValues($activeOnly);
            } else {
                // Get attributes only (backward compatibility)
                $attributes = $activeOnly
                    ? $this->attributeRepo->getAllActive()
                    : $this->attributeRepo->getAll();
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Attributes retrieved successfully')
                ->setData($attributes);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve attributes: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get attribute by ID with values
     */
    public function show($id)
    {
        try {
            $attribute = $this->attributeRepo->getWithValues($id);

            if (!$attribute) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Attribute not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Attribute retrieved successfully')
                ->setData($attribute);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve attribute: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Create new attribute
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'slug' => 'nullable|string|max:100|unique:product_attributes,slug',
                'type' => 'required|in:select,color,radio,checkbox',
                'description' => 'nullable|string',
                'is_required' => 'nullable|boolean',
                'is_active' => 'nullable|boolean',
                'sort_order' => 'nullable|integer'
            ]);

            $attribute = $this->attributeRepo->create($validated);

            logActivity(
                'create',
                "Created product attribute: {$attribute->name}",
                'product_attribute',
                (int) $attribute->id
            );

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('201')
                ->setMessage('Attribute created successfully')
                ->setData($attribute);

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
                ->setMessage('Failed to create attribute: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update attribute
     */
    public function update(Request $request, $id)
    {
        try {
            $attribute = $this->attributeRepo->findById($id);

            if (!$attribute) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Attribute not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $validated = $request->validate([
                'name' => 'nullable|string|max:100',
                'slug' => 'nullable|string|max:100|unique:product_attributes,slug,' . $id,
                'type' => 'nullable|in:select,color,radio,checkbox',
                'description' => 'nullable|string',
                'is_required' => 'nullable|boolean',
                'is_active' => 'nullable|boolean',
                'sort_order' => 'nullable|integer'
            ]);

            $updatedAttribute = $this->attributeRepo->update($id, $validated);

            logActivity(
                'update',
                "Updated product attribute: {$updatedAttribute->name}",
                'product_attribute',
                (int) $id
            );

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Attribute updated successfully')
                ->setData($updatedAttribute);

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
                ->setMessage('Failed to update attribute: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete attribute
     */
    public function destroy($id)
    {
        try {
            $attribute = $this->attributeRepo->findById($id);

            if (!$attribute) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Attribute not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $this->attributeRepo->delete($id);

            logActivity(
                'delete',
                "Deleted product attribute: {$attribute->name}",
                'product_attribute',
                (int) $id
            );

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Attribute deleted successfully')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to delete attribute: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
