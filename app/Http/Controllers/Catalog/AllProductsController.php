<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Catalog\ProductRepositoryInterface;
use App\Repositories\Contracts\Catalog\BrandRepositoryInterface;
use App\Repositories\Contracts\Catalog\CategoryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AllProductsController extends Controller
{
    protected $productRepo;
    protected $brandRepo;
    protected $categoryRepo;

    public function __construct(
        ProductRepositoryInterface $productRepo,
        BrandRepositoryInterface $brandRepo,
        CategoryRepositoryInterface $categoryRepo
    ) {
        $this->productRepo = $productRepo;
        $this->brandRepo = $brandRepo;
        $this->categoryRepo = $categoryRepo;
    }

    public function allProducts(Request $request)
    {
        // Get filter parameters
        $name = $request->input('name');
        $brand = $request->input('brand');
        $category = $request->input('category');
        $hasVariants = $request->input('has_variants');
        $stock = $request->input('stock');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $status = $request->input('status');

        // Build query
        $query = DB::table('products as p')
            ->leftJoin('brands as b', 'p.brand_id', '=', 'b.id')
            ->leftJoin('product_images as pi', function($join) {
                $join->on('p.id', '=', 'pi.product_id')
                     ->where('pi.is_primary', '=', true);
            })
            ->select(
                'p.*',
                'b.name as brand_name',
                'pi.url as primary_image_url',
                DB::raw('(SELECT COUNT(*) FROM product_variants WHERE product_id = p.id) as variants_count'),
                DB::raw('(SELECT COALESCE(SUM(stock_quantity), 0) FROM product_variants WHERE product_id = p.id) as total_stock'),
                DB::raw('(SELECT MIN(price) FROM product_variants WHERE product_id = p.id) as min_price'),
                DB::raw('(SELECT MAX(price) FROM product_variants WHERE product_id = p.id) as max_price')
            );

        // Apply name filter
        if ($name) {
            $query->where('p.name', 'like', '%' . $name . '%');
        }

        // Apply brand filter
        if ($brand) {
            $query->where('p.brand_id', $brand);
        }

        // Apply category filter
        if ($category) {
            $query->whereExists(function($q) use ($category) {
                $q->select(DB::raw(1))
                  ->from('product_categories')
                  ->whereRaw('product_categories.product_id = p.id')
                  ->where('product_categories.category_id', $category);
            });
        }

        // Apply variants filter
        if ($hasVariants === 'yes') {
            $query->whereRaw('(SELECT COUNT(*) FROM product_variants WHERE product_id = p.id) > 0');
        } elseif ($hasVariants === 'no') {
            $query->whereRaw('(SELECT COUNT(*) FROM product_variants WHERE product_id = p.id) = 0');
        }

        // Apply stock filter
        if ($stock === 'in_stock') {
            $query->whereRaw('(SELECT COALESCE(SUM(stock_quantity), 0) FROM product_variants WHERE product_id = p.id) > 10');
        } elseif ($stock === 'low_stock') {
            $query->whereRaw('(SELECT COALESCE(SUM(stock_quantity), 0) FROM product_variants WHERE product_id = p.id) BETWEEN 1 AND 10');
        } elseif ($stock === 'out_of_stock') {
            $query->whereRaw('(SELECT COALESCE(SUM(stock_quantity), 0) FROM product_variants WHERE product_id = p.id) = 0');
        }

        // Apply price range filter
        if ($minPrice) {
            $query->whereRaw('(SELECT MIN(price) FROM product_variants WHERE product_id = p.id) >= ?', [$minPrice]);
        }
        if ($maxPrice) {
            $query->whereRaw('(SELECT MAX(price) FROM product_variants WHERE product_id = p.id) <= ?', [$maxPrice]);
        }

        // Apply status filter
        if ($status) {
            $query->where('p.status', $status);
        }

        // Order and paginate
        $products = $query->orderBy('p.created_at', 'desc')
                         ->paginate(15)
                         ->appends($request->query());

        // Get categories for each product
        foreach ($products as $product) {
            $product->categories = DB::table('product_categories')
                ->join('categories', 'product_categories.category_id', '=', 'categories.id')
                ->where('product_categories.product_id', $product->id)
                ->select('categories.*')
                ->get();

            // Get primary image object
            if ($product->primary_image_url) {
                $product->primary_image = (object)[
                    'url' => $product->primary_image_url
                ];
            } else {
                $product->primary_image = null;
            }
        }

        $statistics = $this->productRepo->getStatistics();
        $brands = $this->brandRepo->getAllActive();
        $categories = $this->categoryRepo->getAllActive();

        return view('pages.catalog.all-products.all-products', compact('products', 'statistics', 'brands', 'categories'));
    }

    public function destroy($id)
    {
        try {
            // Check if product has variants
            $variants = $this->productRepo->getProductVariants($id);
            if ($variants->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete product with variants. Please delete all variants first.'
                ], 400);
            }

            $product = $this->productRepo->findById($id);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $this->productRepo->delete($id);

            logActivity('delete', 'product', $id, "Deleted product: {$product->name}");

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $product = $this->productRepo->findById($id);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $newStatus = $request->input('status', 'active');
            $this->productRepo->toggleStatus($id, $newStatus);

            logActivity('update', 'product', $id, "Changed product status to: {$newStatus}");

            return response()->json([
                'success' => true,
                'message' => 'Product status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleFeatured($id)
    {
        try {
            $product = $this->productRepo->findById($id);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $this->productRepo->toggleFeatured($id);

            $newStatus = !$product->is_featured ? 'featured' : 'unfeatured';
            logActivity('update', 'product', $id, "Toggled product featured status to: {$newStatus}");

            return response()->json([
                'success' => true,
                'message' => 'Product featured status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update featured status: ' . $e->getMessage()
            ], 500);
        }
    }
}
