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
                $query->where('products.status', $request->status);
            }

            // Filter by featured
            if ($request->filled('is_featured')) {
                $query->where('products.is_featured', $request->is_featured);
            }

            // Filter by brand
            if ($request->filled('brand_id')) {
                $query->where('products.brand_id', $request->brand_id);
            }

            // Filter by category
            if ($request->filled('category_id')) {
                $productIds = $this->productRepo->getProductIdsByCategory($request->category_id);
                $query->whereIn('products.id', $productIds);
            }

            // Search by name or SKU (SKU is in product_variants table)
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('products.name', 'ILIKE', '%' . $search . '%')
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
            $products = $query->select('products.*', 'brands.name as brand_name')
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                ->orderBy('products.created_at', 'desc')
                ->paginate($perPage);

            // Load full relationships (categories, variants with images, images)
            $items = $products->items();
            $this->productRepo->attachFullRelations(collect($items));

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
                    ->setMessage('Product not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $validated = $request->validate([
                'status' => 'required|in:draft,active,inactive'
            ]);

            $this->productRepo->update($id, ['status' => $validated['status']]);

            // Log activity
            logActivity(
                'update',
                "Updated product status to {$validated['status']}: {$product->name}",
                'product',
                (int) $id
            );

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Product status updated successfully')
                ->setData([
                    'product_id' => $id,
                    'status' => $validated['status']
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update product status: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Set product as active
     */
    public function setActive($id)
    {
        try {
            $product = $this->productRepo->findById($id);

            if (!$product) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Product not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $this->productRepo->update($id, ['status' => 'active']);

            // Log activity
            logActivity(
                'update',
                "Set product as active: {$product->name}",
                'product',
                (int) $id
            );

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Product set as active successfully')
                ->setData([
                    'product_id' => $id,
                    'status' => 'active'
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to set product as active: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Set product as inactive
     */
    public function setInactive($id)
    {
        try {
            $product = $this->productRepo->findById($id);

            if (!$product) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Product not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $this->productRepo->update($id, ['status' => 'inactive']);

            // Log activity
            logActivity(
                'update',
                "Set product as inactive: {$product->name}",
                'product',
                (int) $id
            );

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Product set as inactive successfully')
                ->setData([
                    'product_id' => $id,
                    'status' => 'inactive'
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to set product as inactive: ' . $e->getMessage())
                ->setData([]);

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

            // Store product name for logging
            $productName = $product->name;

            // Delete related data first
            $this->productRepo->deleteProductCategories($id);
            $this->productRepo->deleteProductImages($id);
            $this->productRepo->deleteProductVariants($id);

            // Delete product
            $this->productRepo->delete($id);

            // Log activity
            logActivity(
                'delete',
                "Deleted product: {$productName}",
                'product',
                (int) $id
            );

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
                'brand_id' => 'required|exists:brands,id',
                'status' => 'required|in:draft,active,inactive',
                'age_min' => 'nullable|integer|min:0',
                'age_max' => 'nullable|integer|min:0',
                'tags' => 'nullable',
                'is_featured' => 'nullable|boolean',
                'seo_meta' => 'nullable|array',
                'seo_meta.title' => 'nullable|string|max:255',
                'seo_meta.description' => 'nullable|string|max:500',
                'seo_meta.keywords' => 'nullable|string|max:500',
                'category_ids' => 'required|array',
                'category_ids.*' => 'exists:categories,id',
                'images' => 'required|array',
                'images.*' => 'required|string'
            ]);

            // Convert is_featured to boolean
            $validated['is_featured'] = isset($validated['is_featured']) && $validated['is_featured'] ? true : false;

            // Handle tags - support both array and string
            if (isset($validated['tags'])) {
                if (is_array($validated['tags'])) {
                    // Already an array, just encode to JSON
                    $validated['tags'] = json_encode($validated['tags']);
                } elseif (is_string($validated['tags'])) {
                    // String, split by comma
                    $tagsArray = array_map('trim', explode(',', $validated['tags']));
                    $validated['tags'] = json_encode($tagsArray);
                }
            }

            // Handle seo_meta - encode to JSON if present
            if (isset($validated['seo_meta'])) {
                $validated['seo_meta'] = json_encode($validated['seo_meta']);
            }

            // Extract categories and images before creating product
            $categories = $validated['category_ids'] ?? [];
            $imageUrls = $validated['images'] ?? [];
            unset($validated['category_ids']);
            unset($validated['images']);

            // Create product and get ID
            $productId = $this->productRepo->create($validated);

            // Get product for folder naming
            $product = $this->productRepo->findById($productId);
            $folderName = $product->slug ?? \Str::slug($product->name);

            // Attach categories if any
            if (!empty($categories)) {
                $this->productRepo->insertProductCategories($productId, $categories);
            }

            // Move images from temp to permanent location
            $movedImages = [];
            if (!empty($imageUrls)) {
                $permanentDir = 'products/' . $folderName;
                $sortOrder = 1;

                foreach ($imageUrls as $index => $imageData) {
                    // Handle both string (URL only) and object (URL + alt_text) format
                    if (is_string($imageData)) {
                        $imageUrl = $imageData;
                        $altText = null;
                    } else {
                        $imageUrl = $imageData['url'] ?? null;
                        $altText = $imageData['alt_text'] ?? null;
                    }

                    if (!$imageUrl) {
                        continue;
                    }

                    // Check if this is a temp URL
                    if (strpos($imageUrl, '/temp/') === false) {
                        // This is an existing image, skip it
                        continue;
                    }

                    // Extract path from URL (remove domain part)
                    $tempPath = str_replace(env('FTP_URL') . '/', '', $imageUrl);

                    // Check if temp file exists
                    if (!\Storage::disk('ftp')->exists($tempPath)) {
                        continue;
                    }

                    // Get file info
                    $filename = basename($tempPath);
                    $newFilename = time() . '_' . uniqid() . '.' . pathinfo($filename, PATHINFO_EXTENSION);
                    $newPath = $permanentDir . '/' . $newFilename;

                    // Move file from temp to permanent location
                    \Storage::disk('ftp')->move($tempPath, $newPath);

                    // Generate full URL
                    $fullUrl = env('FTP_URL') . '/' . $newPath;

                    // Determine if this is primary
                    $isPrimary = ($index === 0);

                    // Insert to database with full URL
                    $imageId = \DB::table('product_images')->insertGetId([
                        'product_id' => $productId,
                        'url' => $fullUrl,
                        'alt_text' => $altText,
                        'is_primary' => $isPrimary,
                        'sort_order' => $sortOrder,
                        'created_at' => now(),
                    ]);

                    $movedImages[] = [
                        'id' => $imageId,
                        'url' => $fullUrl,
                        'is_primary' => $isPrimary,
                    ];

                    $sortOrder++;
                }

                // Cleanup temp directory
                $tempUrls = array_filter($imageUrls, function($data) {
                    $url = is_string($data) ? $data : ($data['url'] ?? null);
                    return $url && strpos($url, '/temp/') !== false;
                });

                if (!empty($tempUrls)) {
                    $firstTempUrl = is_string($tempUrls[0]) ? $tempUrls[0] : $tempUrls[0]['url'];
                    $firstTempPath = str_replace(env('FTP_URL') . '/', '', $firstTempUrl);
                    $tempDirectory = dirname($firstTempPath);
                    $remainingFiles = \Storage::disk('ftp')->files($tempDirectory);
                    if (empty($remainingFiles)) {
                        \Storage::disk('ftp')->deleteDirectory($tempDirectory);
                    }
                }
            }

            // Get created product with full relations
            $createdProduct = $this->productRepo->findByIdWithRelations($productId);

            // Log activity
            logActivity(
                'create',
                "Created product: {$product->name}",
                'product',
                (int) $productId
            );

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('201')
                ->setMessage('Product created successfully')
                ->setData($createdProduct);

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
                'brand_id' => 'required|exists:brands,id',
                'status' => 'required|in:draft,active,inactive',
                'age_min' => 'nullable|integer|min:0',
                'age_max' => 'nullable|integer|min:0',
                'tags' => 'nullable',
                'is_featured' => 'nullable|boolean',
                'seo_meta' => 'nullable|array',
                'seo_meta.title' => 'nullable|string|max:255',
                'seo_meta.description' => 'nullable|string|max:500',
                'seo_meta.keywords' => 'nullable|string|max:500',
                'category_ids' => 'required|array',
                'category_ids.*' => 'exists:categories,id',
                'images' => 'required|array',
                'images.*' => 'required|string'
            ]);

            // Convert is_featured to boolean
            $validated['is_featured'] = isset($validated['is_featured']) && $validated['is_featured'] ? true : false;

            // Handle tags - support both array and string
            if (isset($validated['tags'])) {
                if (is_array($validated['tags'])) {
                    // Already an array, just encode to JSON
                    $validated['tags'] = json_encode($validated['tags']);
                } elseif (is_string($validated['tags'])) {
                    // String, split by comma
                    $tagsArray = array_map('trim', explode(',', $validated['tags']));
                    $validated['tags'] = json_encode($tagsArray);
                }
            }

            // Handle seo_meta - encode to JSON if present
            if (isset($validated['seo_meta'])) {
                $validated['seo_meta'] = json_encode($validated['seo_meta']);
            }

            // Extract categories and images before updating product
            $categories = $validated['category_ids'] ?? [];
            $imageUrls = $validated['images'] ?? [];
            unset($validated['category_ids']);
            unset($validated['images']);

            // Update product
            $this->productRepo->update($id, $validated);

            // Get updated product for folder naming
            $product = $this->productRepo->findById($id);
            $folderName = $product->slug ?? \Str::slug($product->name);

            // Sync categories
            $this->productRepo->syncCategories($id, $categories);

            // Process images - only move new images from temp
            $movedImages = [];
            if (!empty($imageUrls)) {
                $permanentDir = 'products/' . $folderName;

                // Get existing images count for sort_order
                $existingCount = \DB::table('product_images')->where('product_id', $id)->count();
                $sortOrder = $existingCount + 1;

                $tempUrls = [];
                foreach ($imageUrls as $index => $imageUrl) {
                    // Check if this is a temp URL (contains /temp/)
                    if (strpos($imageUrl, '/temp/') === false) {
                        // This is an existing image, skip it
                        continue;
                    }

                    // This is a temp image, process it
                    $tempUrls[] = $imageUrl;

                    // Extract path from URL (remove domain part)
                    $tempPath = str_replace(env('FTP_URL') . '/', '', $imageUrl);

                    // Check if temp file exists
                    if (!\Storage::disk('ftp')->exists($tempPath)) {
                        continue;
                    }

                    // Get file info
                    $filename = basename($tempPath);
                    $newFilename = time() . '_' . uniqid() . '.' . pathinfo($filename, PATHINFO_EXTENSION);
                    $newPath = $permanentDir . '/' . $newFilename;

                    // Move file from temp to permanent location
                    \Storage::disk('ftp')->move($tempPath, $newPath);

                    // Generate full URL
                    $fullUrl = env('FTP_URL') . '/' . $newPath;

                    // Determine if this is primary (first image and no existing images)
                    $isPrimary = ($existingCount === 0 && $index === 0);

                    // Insert to database with full URL
                    $imageId = \DB::table('product_images')->insertGetId([
                        'product_id' => $id,
                        'url' => $fullUrl,
                        'alt_text' => null,
                        'is_primary' => $isPrimary,
                        'sort_order' => $sortOrder,
                        'created_at' => now(),
                    ]);

                    $movedImages[] = [
                        'id' => $imageId,
                        'url' => $fullUrl,
                        'is_primary' => $isPrimary,
                    ];

                    $sortOrder++;
                }

                // Cleanup temp directory
                if (!empty($tempUrls)) {
                    $firstTempPath = str_replace(env('FTP_URL') . '/', '', $tempUrls[0]);
                    $tempDirectory = dirname($firstTempPath);
                    $remainingFiles = \Storage::disk('ftp')->files($tempDirectory);
                    if (empty($remainingFiles)) {
                        \Storage::disk('ftp')->deleteDirectory($tempDirectory);
                    }
                }
            }

            // Log activity
            logActivity(
                'update',
                "Updated product: {$product->name}",
                'product',
                (int) $id
            );

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Product updated successfully')
                ->setData([
                    'product_id' => $id,
                    'images' => $movedImages,
                    'images_count' => count($movedImages)
                ]);

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

            // Log activity
            logActivity('update', "Updated categories for product: {$product->name}", 'product', (int) $id);

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

            // Log activity
            logActivity('create', "Uploaded " . count($uploadedImages) . " images for product: {$product->name}", 'product', (int) $id);

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
            if (\Storage::disk('ftp')->exists($image->url)) {
                \Storage::disk('ftp')->delete($image->url);
            }

            $this->productRepo->deleteImage($imageId);

            // Log activity
            logActivity('delete', "Deleted image from product: {$product->name}", 'product', (int) $id);

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

            // Log activity
            logActivity('update', "Set primary image for product: {$product->name}", 'product', (int) $id);

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

}
