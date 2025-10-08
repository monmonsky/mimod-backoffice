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
                $productIds = \DB::table('product_categories')
                    ->where('category_id', $request->category_id)
                    ->pluck('product_id');
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

            // Load relationships manually for each product
            foreach ($products as $product) {
                // Get brand
                if ($product->brand_id) {
                    $product->brand = \DB::table('brands')->where('id', $product->brand_id)->first();
                }

                // Get categories
                $product->categories = \DB::table('product_categories')
                    ->join('categories', 'product_categories.category_id', '=', 'categories.id')
                    ->where('product_categories.product_id', $product->id)
                    ->select('categories.*')
                    ->get();

                // Get images
                $product->images = \DB::table('product_images')
                    ->where('product_id', $product->id)
                    ->orderBy('is_primary', 'desc')
                    ->get();

                // Get variants for stock and price
                $product->variants = \DB::table('product_variants')
                    ->where('product_id', $product->id)
                    ->select('id', 'sku', 'price', 'stock_quantity')
                    ->get();
            }

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
        $total = $this->productRepo->query()->count();
        $active = $this->productRepo->query()->where('status', 'active')->count();
        $inactive = $this->productRepo->query()->where('status', 'inactive')->count();

        // Out of stock: products that have no variants with stock > 0
        $outOfStock = \DB::table('products')
            ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->select('products.id')
            ->groupBy('products.id')
            ->havingRaw('COALESCE(SUM(product_variants.stock_quantity), 0) <= 0')
            ->count();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'out_of_stock' => $outOfStock
        ];
    }

    /**
     * Get single product by ID or slug
     */
    public function show($identifier)
    {
        try {
            // Check if identifier is numeric (ID) or string (slug)
            if (is_numeric($identifier)) {
                $product = $this->productRepo->findById($identifier);
            } else {
                $product = \DB::table('products')->where('slug', $identifier)->first();
            }

            if (!$product) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Product not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Load relationships manually
            if ($product->brand_id) {
                $product->brand = \DB::table('brands')->where('id', $product->brand_id)->first();
            }

            $product->categories = \DB::table('product_categories')
                ->join('categories', 'product_categories.category_id', '=', 'categories.id')
                ->where('product_categories.product_id', $product->id)
                ->select('categories.*')
                ->get();

            $product->images = \DB::table('product_images')
                ->where('product_id', $product->id)
                ->orderBy('is_primary', 'desc')
                ->get();

            $product->variants = \DB::table('product_variants')
                ->where('product_id', $product->id)
                ->get();

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

            $productIds = \DB::table('product_categories')
                ->where('category_id', $categoryId)
                ->pluck('product_id');

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
}
