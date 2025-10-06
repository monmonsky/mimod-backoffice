<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Catalog\ProductRepositoryInterface;
use Illuminate\Http\Request;

class AllProductsController extends Controller
{
    protected $productRepo;

    public function __construct(ProductRepositoryInterface $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    public function allProducts()
    {
        $products = $this->productRepo->getAllWithRelations();
        $statistics = $this->productRepo->getStatistics();

        return view('pages.catalog.all-products.all-products', compact('products', 'statistics'));
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
