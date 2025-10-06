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
     * Get all products with filters
     */
    public function index(Request $request)
    {
        try {
            $query = $this->productRepo->query();

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by featured
            if ($request->has('is_featured')) {
                $query->where('is_featured', $request->is_featured);
            }

            // Filter by brand
            if ($request->has('brand_id')) {
                $query->where('brand_id', $request->brand_id);
            }

            // Search by name
            if ($request->has('search')) {
                $query->where('name', 'ILIKE', '%' . $request->search . '%');
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $products = $query->orderBy('created_at', 'desc')->paginate($perPage);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Products retrieved successfully')
                ->setData($products);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve products: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
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
                $product = $this->productRepo->findBySlug($identifier);
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
}
