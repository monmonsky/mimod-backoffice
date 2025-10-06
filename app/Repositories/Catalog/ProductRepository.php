<?php

namespace App\Repositories\Catalog;

use App\Repositories\Contracts\Catalog\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProductRepository implements ProductRepositoryInterface
{
    protected function table()
    {
        return DB::table('products');
    }

    public function query()
    {
        return $this->table();
    }

    public function getAll()
    {
        return $this->table()
            ->select('products.*', 'brands.name as brand_name')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->orderBy('products.created_at', 'desc')
            ->get();
    }

    public function getAllWithRelations()
    {
        $products = $this->getAll();

        if ($products->isEmpty()) {
            return $products;
        }

        $productIds = $products->pluck('id')->toArray();

        // Batch load categories
        $categoriesMap = DB::table('product_categories')
            ->join('categories', 'product_categories.category_id', '=', 'categories.id')
            ->whereIn('product_categories.product_id', $productIds)
            ->select('product_categories.product_id', 'categories.*')
            ->get()
            ->groupBy('product_id');

        // Batch load variant stats
        $variantStats = DB::table('product_variants')
            ->whereIn('product_id', $productIds)
            ->select(
                'product_id',
                DB::raw('COUNT(*) as variants_count'),
                DB::raw('SUM(stock_quantity) as total_stock'),
                DB::raw('MIN(price) as min_price'),
                DB::raw('MAX(price) as max_price')
            )
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        // Batch load primary images
        $primaryImages = DB::table('product_images')
            ->whereIn('product_id', $productIds)
            ->where('is_primary', true)
            ->select('product_id', 'url', 'id')
            ->get()
            ->keyBy('product_id');

        // Batch load images count
        $imagesCount = DB::table('product_images')
            ->whereIn('product_id', $productIds)
            ->select('product_id', DB::raw('COUNT(*) as images_count'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        // Assign data to products
        foreach ($products as $product) {
            $product->categories = isset($categoriesMap[$product->id])
                ? $categoriesMap[$product->id]->toArray()
                : [];

            $stats = $variantStats[$product->id] ?? null;
            $product->variants_count = $stats->variants_count ?? 0;
            $product->total_stock = $stats->total_stock ?? 0;
            $product->min_price = $stats->min_price ?? 0;
            $product->max_price = $stats->max_price ?? 0;

            $product->primary_image = $primaryImages[$product->id] ?? null;
            $product->images_count = isset($imagesCount[$product->id])
                ? $imagesCount[$product->id]->images_count
                : 0;
        }

        return $products;
    }

    public function findById($id)
    {
        return $this->table()->where('id', $id)->first();
    }

    public function findByIdWithRelations($id)
    {
        $product = $this->table()
            ->select('products.*', 'brands.name as brand_name')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->where('products.id', $id)
            ->first();

        if (!$product) {
            return null;
        }

        // Get categories
        $product->categories = DB::table('product_categories')
            ->join('categories', 'product_categories.category_id', '=', 'categories.id')
            ->where('product_categories.product_id', $product->id)
            ->select('categories.*')
            ->get();

        // Get variants
        $product->variants = DB::table('product_variants')
            ->where('product_id', $product->id)
            ->orderBy('id', 'asc')
            ->get();

        // Get images
        $product->images = DB::table('product_images')
            ->where('product_id', $product->id)
            ->orderBy('sort_order', 'asc')
            ->get();

        return $product;
    }

    public function findBySlug($slug)
    {
        return $this->table()->where('slug', $slug)->first();
    }

    public function create(array $data)
    {
        $data['created_at'] = now();
        $data['updated_at'] = now();

        return $this->table()->insertGetId($data);
    }

    public function update($id, array $data)
    {
        $data['updated_at'] = now();
        return $this->table()->where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return $this->table()->where('id', $id)->delete();
    }

    public function toggleStatus($id, $status)
    {
        return $this->table()->where('id', $id)->update([
            'status' => $status,
            'updated_at' => now()
        ]);
    }

    public function toggleFeatured($id)
    {
        $product = $this->findById($id);
        return $this->table()->where('id', $id)->update([
            'is_featured' => !$product->is_featured,
            'updated_at' => now()
        ]);
    }

    public function getStatistics()
    {
        $total = $this->table()->count();
        $active = $this->table()->where('status', 'active')->count();
        $inactive = $this->table()->where('status', 'inactive')->count();
        $draft = $this->table()->where('status', 'draft')->count();
        $featured = $this->table()->where('is_featured', true)->count();

        // Total variants
        $totalVariants = DB::table('product_variants')->count();

        // Total stock
        $totalStock = DB::table('product_variants')->sum('stock_quantity');

        // Low stock (less than 10)
        $lowStock = DB::table('product_variants')
            ->where('stock_quantity', '<', 10)
            ->count();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'draft' => $draft,
            'featured' => $featured,
            'total_variants' => $totalVariants,
            'total_stock' => $totalStock,
            'low_stock' => $lowStock,
        ];
    }

    public function syncCategories($productId, array $categoryIds)
    {
        // Delete existing categories
        DB::table('product_categories')
            ->where('product_id', $productId)
            ->delete();

        // Insert new categories
        if (!empty($categoryIds)) {
            $data = [];
            foreach ($categoryIds as $categoryId) {
                $data[] = [
                    'product_id' => $productId,
                    'category_id' => $categoryId,
                ];
            }
            DB::table('product_categories')->insert($data);
        }

        return true;
    }

    public function getProductVariants($productId)
    {
        return DB::table('product_variants')
            ->where('product_id', $productId)
            ->orderBy('id', 'asc')
            ->get();
    }

    public function getProductImages($productId)
    {
        return DB::table('product_images')
            ->where('product_id', $productId)
            ->orderBy('sort_order', 'asc')
            ->get();
    }
}
