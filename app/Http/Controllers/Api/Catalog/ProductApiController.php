<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Catalog\ProductRepository;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    protected $productRepo;
    protected $response;

    public function __construct(ProductRepository $productRepo, Response $response)
    {
        $this->productRepo = $productRepo;
        $this->response = $response;
    }

    /**
     * Get all products with filters and statistics
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['search', 'status', 'brand_id', 'category_id', 'is_featured']);

            $query = $this->productRepo->query();

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by featured
            if ($request->filled('is_featured')) {
                $query->where('is_featured', $request->is_featured);
            }

            // Filter by brand
            if ($request->filled('brand_id')) {
                $query->where('brand_id', $request->brand_id);
            }

            // Filter by category
            if ($request->filled('category_id')) {
                $productIds = $this->productRepo->getProductIdsByCategory($request->category_id);
                $query->whereIn('id', $productIds);
            }

            // Search by name or SKU (SKU is in product_variants table)
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'ILIKE', '%' . $search . '%')
                      ->orWhereExists(function($subQuery) use ($search) {
                          $subQuery->select(\DB::raw(1))
                              ->from('product_variants')
                              ->whereRaw('product_variants.product_id = products.id')
                              ->where('product_variants.sku', 'ILIKE', '%' . $search . '%');
                      });
                });
            }

            // Filter by stock status
            if ($request->filled('stock_status')) {
                $stockStatus = $request->stock_status;
                if ($stockStatus === 'in_stock') {
                    $query->whereExists(function($subQuery) {
                        $subQuery->select(\DB::raw(1))
                            ->from('product_variants')
                            ->whereRaw('product_variants.product_id = products.id')
                            ->havingRaw('COALESCE(SUM(product_variants.stock_quantity), 0) > 10');
                    });
                } elseif ($stockStatus === 'low_stock') {
                    $query->whereExists(function($subQuery) {
                        $subQuery->select(\DB::raw(1))
                            ->from('product_variants')
                            ->whereRaw('product_variants.product_id = products.id')
                            ->havingRaw('COALESCE(SUM(product_variants.stock_quantity), 0) BETWEEN 1 AND 10');
                    });
                } elseif ($stockStatus === 'out_of_stock') {
                    $query->whereExists(function($subQuery) {
                        $subQuery->select(\DB::raw(1))
                            ->from('product_variants')
                            ->whereRaw('product_variants.product_id = products.id')
                            ->havingRaw('COALESCE(SUM(product_variants.stock_quantity), 0) <= 0');
                    });
                }
            }

            // Filter by price range
            if ($request->filled('min_price')) {
                $query->whereExists(function($subQuery) use ($request) {
                    $subQuery->select(\DB::raw(1))
                        ->from('product_variants')
                        ->whereRaw('product_variants.product_id = products.id')
                        ->where('product_variants.price', '>=', $request->min_price);
                });
            }

            if ($request->filled('max_price')) {
                $query->whereExists(function($subQuery) use ($request) {
                    $subQuery->select(\DB::raw(1))
                        ->from('product_variants')
                        ->whereRaw('product_variants.product_id = products.id')
                        ->where('product_variants.price', '<=', $request->max_price);
                });
            }

            // Pagination
            $perPage = $request->get('per_page', 20);
            $products = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);

            // Load relationships optimized (batch query to avoid N+1)
            $items = $products->items();
            $this->productRepo->attachRelationsOptimized(collect($items));

            // Get statistics
            $statistics = $this->getStatistics();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Products retrieved successfully')
                ->setData([
                    'products' => $products,
                    'statistics' => $statistics
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            \Log::error('ProductApiController@index error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve products: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get product statistics
     */
    private function getStatistics()
    {
        return $this->productRepo->getStatistics();
    }

    /**
     * Get single product by ID or slug
     */
    public function show($identifier)
    {
        try {
            // Check if identifier is numeric (ID) or string (slug)
            if (is_numeric($identifier)) {
                $product = $this->productRepo->findByIdWithRelations($identifier);
            } else {
                $product = $this->productRepo->findBySlugWithRelations($identifier);
            }

            if (!$product) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Product not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Product retrieved successfully')
                ->setData($product);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve product: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get product variants
     */
    public function variants($id)
    {
        try {
            $variants = $this->productRepo->getProductVariants($id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Product variants retrieved successfully')
                ->setData($variants);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve variants: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get product images
     */
    public function images($id)
    {
        try {
            $images = $this->productRepo->getProductImages($id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Product images retrieved successfully')
                ->setData($images);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve images: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get featured products
     */
    public function featured(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);

            $products = $this->productRepo->query()
                ->where('is_featured', true)
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Featured products retrieved successfully')
                ->setData($products);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve featured products: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get products by category
     */
    public function byCategory($categoryId, Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);

            $productIds = $this->productRepo->getProductIdsByCategory($categoryId);

            $products = $this->productRepo->query()
                ->whereIn('id', $productIds)
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Products by category retrieved successfully')
                ->setData($products);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve products by category: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get products by brand
     */
    public function byBrand($brandId, Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);

            $products = $this->productRepo->query()
                ->where('brand_id', $brandId)
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Products by brand retrieved successfully')
                ->setData($products);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve products by brand: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update product status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $product = $this->productRepo->findById($id);

            if (!$product) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Product not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            $this->productRepo->update($id, ['status' => $request->status]);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Product status updated successfully');

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update product status: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete product
     */
    public function destroy($id)
    {
        try {
            $product = $this->productRepo->findById($id);

            if (!$product) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Product not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Check if product has variants that are in orders
            if ($this->productRepo->checkVariantsInOrders($id)) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Cannot delete product: It has variants that are associated with orders. Consider deactivating the product instead.');

                return response()->json($this->response->generateResponse($result), 422);
            }

            // Delete related data first
            $this->productRepo->deleteProductCategories($id);
            $this->productRepo->deleteProductImages($id);
            $this->productRepo->deleteProductVariants($id);

            // Delete product
            $this->productRepo->delete($id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Product deleted successfully');

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to delete product: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Store new product
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:products,slug',
                'description' => 'nullable|string',
                'brand_id' => 'nullable|exists:brands,id',
                'status' => 'required|in:draft,active,inactive',
                'age_min' => 'nullable|integer|min:0',
                'age_max' => 'nullable|integer|min:0',
                'tags' => 'nullable|string',
                'is_featured' => 'nullable|boolean',
                'categories' => 'nullable|array',
                'categories.*' => 'exists:categories,id'
            ]);

            // Convert is_featured to boolean
            $validated['is_featured'] = isset($validated['is_featured']) && $validated['is_featured'] ? true : false;

            // Convert tags string to JSON array
            if (isset($validated['tags']) && is_string($validated['tags'])) {
                $tagsArray = array_map('trim', explode(',', $validated['tags']));
                $validated['tags'] = json_encode($tagsArray);
            }

            // Extract categories before creating product
            $categories = $validated['categories'] ?? [];
            unset($validated['categories']);

            // Create product and get ID
            $productId = $this->productRepo->create($validated);

            // Attach categories if any
            if (!empty($categories)) {
                $this->productRepo->insertProductCategories($productId, $categories);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('201')
                ->setMessage('Product created successfully')
                ->setData(['product_id' => $productId]);

            return response()->json($this->response->generateResponse($result), 201);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to create product: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update product
     */
    public function update(Request $request, $id)
    {
        try {
            $product = $this->productRepo->findById($id);

            if (!$product) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Product not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:products,slug,' . $id,
                'description' => 'nullable|string',
                'brand_id' => 'nullable|exists:brands,id',
                'status' => 'required|in:draft,active,inactive',
                'age_min' => 'nullable|integer|min:0',
                'age_max' => 'nullable|integer|min:0',
                'tags' => 'nullable|string',
                'is_featured' => 'nullable|boolean',
                'categories' => 'nullable|array',
                'categories.*' => 'exists:categories,id'
            ]);

            // Convert is_featured to boolean
            $validated['is_featured'] = isset($validated['is_featured']) && $validated['is_featured'] ? true : false;

            // Convert tags string to JSON array
            if (isset($validated['tags']) && is_string($validated['tags'])) {
                $tagsArray = array_map('trim', explode(',', $validated['tags']));
                $validated['tags'] = json_encode($tagsArray);
            }

            // Extract categories before updating product
            $categories = $validated['categories'] ?? [];
            unset($validated['categories']);

            // Update product
            $this->productRepo->update($id, $validated);

            // Sync categories
            $this->productRepo->syncCategories($id, $categories);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Product updated successfully');

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update product: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update product categories only
     */
    public function updateCategories(Request $request, $id)
    {
        try {
            $product = $this->productRepo->findById($id);

            if (!$product) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Product not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            $validated = $request->validate([
                'categories' => 'nullable|array',
                'categories.*' => 'exists:categories,id'
            ]);

            $categories = $validated['categories'] ?? [];

            // Sync categories
            $this->productRepo->syncCategories($id, $categories);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Categories updated successfully');

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update categories: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Upload product images
     */
    public function uploadImages(Request $request, $id)
    {
        try {
            $product = $this->productRepo->findById($id);

            if (!$product) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Product not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            $request->validate([
                'images' => 'required|array',
                'images.*' => 'image|mimes:jpeg,jpg,png,gif|max:20048'
            ]);

            // Create folder name from product slug or name
            $folderName = $product->slug ?? \Str::slug($product->name);

            $uploadedImages = [];
            $existingImagesCount = $this->productRepo->getImagesCount($id);
            $sortOrder = $existingImagesCount + 1;
            $imageCounter = $existingImagesCount + 1;

            foreach ($request->file('images') as $image) {
                $extension = $image->getClientOriginalExtension();
                $filename = $folderName . $imageCounter . '.' . $extension;
                $path = $image->storeAs('products/' . $folderName, $filename, 'public');

                $imageId = $this->productRepo->insertImage([
                    'product_id' => $id,
                    'url' => $path,
                    'alt_text' => null,
                    'is_primary' => $existingImagesCount === 0 && $sortOrder === 1,
                    'sort_order' => $sortOrder,
                    'created_at' => now()
                ]);

                $uploadedImages[] = [
                    'id' => $imageId,
                    'product_id' => $id,
                    'url' => $path,
                    'alt_text' => null,
                    'is_primary' => $existingImagesCount === 0 && $sortOrder === 1,
                    'sort_order' => $sortOrder
                ];

                $sortOrder++;
                $existingImagesCount++;
                $imageCounter++;
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Images uploaded successfully')
                ->setData(['images' => $uploadedImages]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to upload images: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete product image
     */
    public function deleteImage($id, $imageId)
    {
        try {
            $product = $this->productRepo->findById($id);

            if (!$product) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Product not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            $image = $this->productRepo->getImage($imageId);

            if (!$image || $image->product_id != $id) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Image not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Delete file from storage
            if (\Storage::disk('public')->exists($image->url)) {
                \Storage::disk('public')->delete($image->url);
            }

            $this->productRepo->deleteImage($imageId);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Image deleted successfully');

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to delete image: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Set primary image
     */
    public function setPrimaryImage($id, $imageId)
    {
        try {
            $product = $this->productRepo->findById($id);

            if (!$product) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Product not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            $image = $this->productRepo->getImage($imageId);

            if (!$image || $image->product_id != $id) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Image not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Set primary image
            $this->productRepo->setPrimaryImage($id, $imageId);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Primary image set successfully');

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to set primary image: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Store product variant
     */
    public function storeVariant(Request $request, $productId)
    {
        try {
            $product = $this->productRepo->findById($productId);

            if (!$product) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Product not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            $validated = $request->validate([
                'sku' => 'required|string|max:255|unique:product_variants,sku',
                'size' => 'required|string|max:50',
                'color' => 'nullable|string|max:50',
                'weight_gram' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
                'compare_at_price' => 'nullable|numeric|min:0',
                'stock_quantity' => 'required|integer|min:0',
                'barcode' => 'nullable|string|max:255'
            ]);

            $validated['product_id'] = $productId;

            $variantId = $this->productRepo->createVariant($validated);

            $variant = $this->productRepo->getVariant($variantId);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('201')
                ->setMessage('Variant created successfully')
                ->setData(['variant' => $variant]);

            return response()->json($this->response->generateResponse($result), 201);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to create variant: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update product variant
     */
    public function updateVariant(Request $request, $productId, $variantId)
    {
        try {
            $product = $this->productRepo->findById($productId);

            if (!$product) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Product not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            $variant = $this->productRepo->getVariant($variantId);

            if (!$variant || $variant->product_id != $productId) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Variant not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            $validated = $request->validate([
                'sku' => 'required|string|max:255|unique:product_variants,sku,' . $variantId,
                'size' => 'required|string|max:50',
                'color' => 'nullable|string|max:50',
                'weight_gram' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
                'compare_at_price' => 'nullable|numeric|min:0',
                'stock_quantity' => 'required|integer|min:0',
                'barcode' => 'nullable|string|max:255'
            ]);

            $this->productRepo->updateVariant($variantId, $validated);

            $updatedVariant = $this->productRepo->getVariant($variantId);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Variant updated successfully')
                ->setData(['variant' => $updatedVariant]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update variant: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete product variant
     */
    public function deleteVariant($productId, $variantId)
    {
        try {
            $product = $this->productRepo->findById($productId);

            if (!$product) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Product not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            $variant = $this->productRepo->getVariant($variantId);

            if (!$variant || $variant->product_id != $productId) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Variant not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            $this->productRepo->deleteVariant($variantId);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Variant deleted successfully');

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to delete variant: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Upload variant images
     */
    public function uploadVariantImages(Request $request, $productId, $variantId)
    {
        try {
            $product = $this->productRepo->findById($productId);

            if (!$product) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Product not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            $variant = $this->productRepo->getVariant($variantId);

            if (!$variant || $variant->product_id != $productId) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Variant not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            $request->validate([
                'images' => 'required|array',
                'images.*' => 'image|mimes:jpeg,jpg,png,gif|max:20048'
            ]);

            // Create folder name from product slug or name
            $productFolderName = $product->slug ?? \Str::slug($product->name);

            // Create variant filename from SKU or variant details
            $variantName = \Str::slug($variant->sku);

            $uploadedImages = [];
            $existingImagesCount = $this->productRepo->getVariantImagesCount($variantId);
            $sortOrder = $existingImagesCount + 1;
            $imageCounter = $existingImagesCount + 1;

            foreach ($request->file('images') as $image) {
                $extension = $image->getClientOriginalExtension();
                $filename = $variantName . ($imageCounter > 1 ? $imageCounter : '') . '.' . $extension;
                $path = $image->storeAs('products/' . $productFolderName . '/variant', $filename, 'public');

                $imageId = $this->productRepo->insertVariantImage([
                    'variant_id' => $variantId,
                    'url' => $path,
                    'alt_text' => null,
                    'is_primary' => $existingImagesCount === 0 && $sortOrder === 1,
                    'sort_order' => $sortOrder,
                    'created_at' => now()
                ]);

                $uploadedImages[] = [
                    'id' => $imageId,
                    'variant_id' => $variantId,
                    'url' => $path,
                    'alt_text' => null,
                    'is_primary' => $existingImagesCount === 0 && $sortOrder === 1,
                    'sort_order' => $sortOrder
                ];

                $sortOrder++;
                $existingImagesCount++;
                $imageCounter++;
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Variant images uploaded successfully')
                ->setData(['images' => $uploadedImages]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to upload variant images: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete variant image
     */
    public function deleteVariantImage($productId, $variantId, $imageId)
    {
        try {
            $product = $this->productRepo->findById($productId);

            if (!$product) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Product not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            $variant = $this->productRepo->getVariant($variantId);

            if (!$variant || $variant->product_id != $productId) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Variant not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            $image = $this->productRepo->getVariantImage($imageId);

            if (!$image || $image->variant_id != $variantId) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Image not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Delete file from storage
            if (\Storage::disk('public')->exists($image->url)) {
                \Storage::disk('public')->delete($image->url);
            }

            $this->productRepo->deleteVariantImage($imageId);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Variant image deleted successfully');

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to delete variant image: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Set primary variant image
     */
    public function setPrimaryVariantImage($productId, $variantId, $imageId)
    {
        try {
            $product = $this->productRepo->findById($productId);

            if (!$product) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Product not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            $variant = $this->productRepo->getVariant($variantId);

            if (!$variant || $variant->product_id != $productId) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Variant not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            $image = $this->productRepo->getVariantImage($imageId);

            if (!$image || $image->variant_id != $variantId) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Image not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Set primary variant image
            $this->productRepo->setPrimaryVariantImage($variantId, $imageId);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Primary variant image set successfully');

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to set primary variant image: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
