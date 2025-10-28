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

        // Decode JSON fields
        if (isset($product->tags) && is_string($product->tags)) {
            $product->tags = json_decode($product->tags);
        }
        if (isset($product->seo_meta) && is_string($product->seo_meta)) {
            $product->seo_meta = json_decode($product->seo_meta);
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

        // Load variant images for each variant
        foreach ($product->variants as $variant) {
            $variant->images = DB::table('product_variant_images')
                ->where('variant_id', $variant->id)
                ->orderBy('is_primary', 'desc')
                ->orderBy('sort_order', 'asc')
                ->get();
        }

        // Get images (including videos)
        $product->images = DB::table('product_images')
            ->select('id', 'product_id', 'url', 'media_type', 'thumbnail_url', 'duration', 'file_size', 'alt_text', 'is_primary', 'sort_order', 'created_at')
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
        $variants = DB::table('product_variants')
            ->where('product_id', $productId)
            ->orderBy('id', 'asc')
            ->get();

        // Attach variant images and attributes for each variant
        foreach ($variants as $variant) {
            // Get variant images
            $variant->images = DB::table('product_variant_images')
                ->where('variant_id', $variant->id)
                ->orderBy('is_primary', 'desc')
                ->orderBy('sort_order', 'asc')
                ->get();

            // Get variant attributes with their values
            $variant->attributes = $this->getVariantAttributes($variant->id);
        }

        return $variants;
    }

    /**
     * Get variant attributes with their values
     */
    protected function getVariantAttributes($variantId)
    {
        return DB::table('product_variant_attributes as pva')
            ->join('product_attributes as pa', 'pva.product_attribute_id', '=', 'pa.id')
            ->join('product_attribute_values as pav', 'pva.product_attribute_value_id', '=', 'pav.id')
            ->where('pva.product_variant_id', $variantId)
            ->select(
                'pva.id as pivot_id',
                'pa.id as attribute_id',
                'pa.name as attribute_name',
                'pa.slug as attribute_slug',
                'pa.type as attribute_type',
                'pav.id as value_id',
                'pav.value',
                'pav.slug as value_slug',
                'pav.meta'
            )
            ->orderBy('pa.sort_order', 'asc')
            ->get()
            ->map(function ($attr) {
                // Decode meta if it's JSON string
                if (isset($attr->meta) && is_string($attr->meta)) {
                    $attr->meta = json_decode($attr->meta);
                }
                return $attr;
            });
    }

    public function getProductImages($productId)
    {
        return DB::table('product_images')
            ->select('id', 'product_id', 'url', 'media_type', 'thumbnail_url', 'duration', 'file_size', 'alt_text', 'is_primary', 'sort_order', 'created_at')
            ->where('product_id', $productId)
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    public function getProductsWithFilters($filters = [], $perPage = 15)
    {
        $query = $this->table();

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'ILIKE', '%' . $search . '%')
                  ->orWhere('slug', 'ILIKE', '%' . $search . '%')
                  ->orWhere('description', 'ILIKE', '%' . $search . '%');
            });
        }

        // Status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Brand filter
        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        // Category filter
        if (!empty($filters['category_id'])) {
            $productIds = DB::table('product_categories')
                ->where('category_id', $filters['category_id'])
                ->pluck('product_id');
            $query->whereIn('id', $productIds);
        }

        // Stock status filter
        if (!empty($filters['stock_status'])) {
            if ($filters['stock_status'] === 'in_stock') {
                $query->whereExists(function($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('product_variants')
                        ->whereColumn('product_variants.product_id', 'products.id')
                        ->where('stock_quantity', '>', 0);
                });
            } elseif ($filters['stock_status'] === 'low_stock') {
                $query->whereExists(function($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('product_variants')
                        ->whereColumn('product_variants.product_id', 'products.id')
                        ->where('stock_quantity', '>', 0)
                        ->where('stock_quantity', '<=', 10);
                });
            } elseif ($filters['stock_status'] === 'out_of_stock') {
                $query->whereNotExists(function($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('product_variants')
                        ->whereColumn('product_variants.product_id', 'products.id')
                        ->where('stock_quantity', '>', 0);
                });
            }
        }

        // Price filter
        if (!empty($filters['min_price']) || !empty($filters['max_price'])) {
            $query->whereExists(function($subQuery) use ($filters) {
                $subQuery->select(DB::raw(1))
                    ->from('product_variants')
                    ->whereColumn('product_variants.product_id', 'products.id');

                if (!empty($filters['min_price'])) {
                    $subQuery->where('price', '>=', $filters['min_price']);
                }
                if (!empty($filters['max_price'])) {
                    $subQuery->where('price', '<=', $filters['max_price']);
                }
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function attachRelations(&$products)
    {
        foreach ($products as $product) {
            // Attach brand
            if ($product->brand_id) {
                $product->brand = DB::table('brands')->where('id', $product->brand_id)->first();
            }

            // Attach categories
            $product->categories = DB::table('product_categories')
                ->join('categories', 'product_categories.category_id', '=', 'categories.id')
                ->where('product_categories.product_id', $product->id)
                ->select('categories.*')
                ->get();

            // Attach images (including videos)
            $product->images = DB::table('product_images')
                ->select('id', 'product_id', 'url', 'media_type', 'thumbnail_url', 'duration', 'file_size', 'alt_text', 'is_primary', 'sort_order', 'created_at')
                ->where('product_id', $product->id)
                ->orderBy('is_primary', 'desc')
                ->orderBy('sort_order', 'asc')
                ->get();

            // Attach variants
            $product->variants = DB::table('product_variants')
                ->where('product_id', $product->id)
                ->get();
        }
    }

    public function getOutOfStockCount()
    {
        return DB::table('products')
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('product_variants')
                    ->whereColumn('product_variants.product_id', 'products.id')
                    ->where('stock_quantity', '>', 0);
            })
            ->count();
    }

    public function getVariantImages($variantId)
    {
        return DB::table('product_variant_images')
            ->where('variant_id', $variantId)
            ->orderBy('is_primary', 'desc')
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    public function attachCategoryProducts($categoryId, array $productIds)
    {
        $data = [];
        foreach ($productIds as $productId) {
            $data[] = [
                'product_id' => $productId,
                'category_id' => $categoryId
            ];
        }

        if (!empty($data)) {
            DB::table('product_categories')->insert($data);
        }
    }

    public function detachCategoryProducts($categoryId, array $productIds)
    {
        DB::table('product_categories')
            ->where('category_id', $categoryId)
            ->whereIn('product_id', $productIds)
            ->delete();
    }

    public function hasVariantsInOrders($productId)
    {
        $variantsInOrders = DB::table('product_variants')
            ->where('product_id', $productId)
            ->whereExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('order_items')
                    ->whereColumn('order_items.product_variant_id', 'product_variants.id');
            })
            ->count();

        return $variantsInOrders > 0;
    }

    public function insertImage(array $data)
    {
        // Validate: video cannot be set as primary
        if (isset($data['media_type']) && $data['media_type'] === 'video' && isset($data['is_primary']) && $data['is_primary']) {
            throw new \Exception('Cannot set video as primary image. Only images can be set as primary.');
        }

        // If media_type not set, default to 'image'
        if (!isset($data['media_type'])) {
            $data['media_type'] = 'image';
        }

        return DB::table('product_images')->insertGetId($data);
    }

    public function deleteImage($imageId)
    {
        return DB::table('product_images')->where('id', $imageId)->delete();
    }

    public function getImage($imageId)
    {
        return DB::table('product_images')->where('id', $imageId)->first();
    }

    public function setPrimaryImage($productId, $imageId)
    {
        // Check if the image is actually an image, not a video
        $image = DB::table('product_images')->where('id', $imageId)->first();

        if (!$image || $image->media_type === 'video') {
            throw new \Exception('Cannot set video as primary image. Only images can be set as primary.');
        }

        // Remove primary from all images (only for media_type = 'image')
        DB::table('product_images')
            ->where('product_id', $productId)
            ->where('media_type', 'image')
            ->update(['is_primary' => false]);

        // Set new primary (only if media_type is 'image')
        DB::table('product_images')
            ->where('id', $imageId)
            ->where('media_type', 'image')
            ->update(['is_primary' => true]);
    }

    public function updateImagesOrder(array $imageIds)
    {
        foreach ($imageIds as $index => $imageId) {
            DB::table('product_images')
                ->where('id', $imageId)
                ->update(['sort_order' => $index + 1]);
        }
    }

    public function createVariant(array $data)
    {
        $data['created_at'] = now();
        $data['updated_at'] = now();
        return DB::table('product_variants')->insertGetId($data);
    }

    public function updateVariant($variantId, array $data)
    {
        $data['updated_at'] = now();
        return DB::table('product_variants')
            ->where('id', $variantId)
            ->update($data);
    }

    public function deleteVariant($variantId)
    {
        return DB::table('product_variants')->where('id', $variantId)->delete();
    }

    public function getVariant($variantId)
    {
        return DB::table('product_variants')->where('id', $variantId)->first();
    }

    public function insertVariantImage(array $data)
    {
        // Validate: video cannot be set as primary
        if (isset($data['media_type']) && $data['media_type'] === 'video' && isset($data['is_primary']) && $data['is_primary']) {
            throw new \Exception('Cannot set video as primary image. Only images can be set as primary.');
        }

        // If media_type not set, default to 'image'
        if (!isset($data['media_type'])) {
            $data['media_type'] = 'image';
        }

        return DB::table('product_variant_images')->insertGetId($data);
    }

    public function deleteVariantImage($imageId)
    {
        return DB::table('product_variant_images')->where('id', $imageId)->delete();
    }

    public function getVariantImage($imageId)
    {
        return DB::table('product_variant_images')->where('id', $imageId)->first();
    }

    public function setPrimaryVariantImage($variantId, $imageId)
    {
        // Check if the image is actually an image, not a video
        $image = DB::table('product_variant_images')->where('id', $imageId)->first();

        if (!$image || $image->media_type === 'video') {
            throw new \Exception('Cannot set video as primary image. Only images can be set as primary.');
        }

        // Remove primary from all images (only for media_type = 'image')
        DB::table('product_variant_images')
            ->where('variant_id', $variantId)
            ->where('media_type', 'image')
            ->update(['is_primary' => false]);

        // Set new primary (only if media_type is 'image')
        DB::table('product_variant_images')
            ->where('id', $imageId)
            ->where('media_type', 'image')
            ->update(['is_primary' => true]);
    }

    public function getImagesCount($productId)
    {
        return DB::table('product_images')
            ->where('product_id', $productId)
            ->count();
    }

    public function getProductIdsByCategory($categoryId)
    {
        // Support both single category and multiple categories (array)
        if (is_array($categoryId)) {
            return DB::table('product_categories')
                ->whereIn('category_id', $categoryId)
                ->distinct()
                ->pluck('product_id');
        }

        return DB::table('product_categories')
            ->where('category_id', $categoryId)
            ->pluck('product_id');
    }

    public function getBrand($brandId)
    {
        return DB::table('brands')->where('id', $brandId)->first();
    }

    public function getProductCategories($productId)
    {
        return DB::table('product_categories')
            ->join('categories', 'product_categories.category_id', '=', 'categories.id')
            ->where('product_categories.product_id', $productId)
            ->select('categories.*')
            ->get();
    }

    public function getProductImagesByProductId($productId)
    {
        return DB::table('product_images')
            ->where('product_id', $productId)
            ->orderBy('is_primary', 'desc')
            ->get();
    }

    public function getProductVariantsByProductId($productId)
    {
        return DB::table('product_variants')
            ->where('product_id', $productId)
            ->select('id', 'sku', 'price', 'stock_quantity')
            ->get();
    }

    public function findBySlugWithRelations($slug)
    {
        $product = DB::table('products')->where('slug', $slug)->first();

        if (!$product) {
            return null;
        }

        // Decode JSON fields
        if (isset($product->tags) && is_string($product->tags)) {
            $product->tags = json_decode($product->tags);
        }
        if (isset($product->seo_meta) && is_string($product->seo_meta)) {
            $product->seo_meta = json_decode($product->seo_meta);
        }

        // Get brand
        if ($product->brand_id) {
            $product->brand = $this->getBrand($product->brand_id);
        }

        // Get categories
        $product->categories = $this->getProductCategories($product->id);

        // Get images
        $product->images = DB::table('product_images')
            ->where('product_id', $product->id)
            ->orderBy('is_primary', 'desc')
            ->get();

        // Get variants
        $variants = DB::table('product_variants')
            ->where('product_id', $product->id)
            ->get();

        // Load variant images for each variant
        foreach ($variants as $variant) {
            $variant->images = DB::table('product_variant_images')
                ->where('variant_id', $variant->id)
                ->orderBy('is_primary', 'desc')
                ->orderBy('sort_order', 'asc')
                ->get();
        }

        $product->variants = $variants;

        return $product;
    }

    public function deleteProductCategories($productId)
    {
        return DB::table('product_categories')->where('product_id', $productId)->delete();
    }

    public function deleteProductImages($productId)
    {
        return DB::table('product_images')->where('product_id', $productId)->delete();
    }

    public function deleteProductVariants($productId)
    {
        return DB::table('product_variants')->where('product_id', $productId)->delete();
    }

    public function insertProductCategories($productId, array $categoryIds)
    {
        $pivotData = [];
        foreach ($categoryIds as $categoryId) {
            $pivotData[] = [
                'product_id' => $productId,
                'category_id' => $categoryId
            ];
        }
        return DB::table('product_categories')->insert($pivotData);
    }

    public function getVariantImagesCount($variantId)
    {
        return DB::table('product_variant_images')->where('variant_id', $variantId)->count();
    }

    public function checkVariantsInOrders($productId)
    {
        return DB::table('product_variants')
            ->join('order_items', 'product_variants.id', '=', 'order_items.variant_id')
            ->where('product_variants.product_id', $productId)
            ->exists();
    }

    public function attachRelationsOptimized($products)
    {
        if ($products->isEmpty()) {
            return;
        }

        $productIds = $products->pluck('id')->toArray();

        // Batch load brands
        $brands = DB::table('brands')
            ->whereIn('id', $products->pluck('brand_id')->filter()->unique())
            ->get()
            ->keyBy('id');

        // Batch load categories with grouping
        $categories = DB::table('product_categories')
            ->join('categories', 'product_categories.category_id', '=', 'categories.id')
            ->whereIn('product_categories.product_id', $productIds)
            ->select('product_categories.product_id', 'categories.name')
            ->get()
            ->groupBy('product_id')
            ->map(function($items) {
                return $items->pluck('name')->toArray();
            });

        // Batch load primary images only
        $images = DB::table('product_images')
            ->whereIn('product_id', $productIds)
            ->where('is_primary', true)
            ->select('product_id', 'url')
            ->get()
            ->keyBy('product_id');

        // Batch load variant counts
        $variantCounts = DB::table('product_variants')
            ->whereIn('product_id', $productIds)
            ->select('product_id', DB::raw('COUNT(*) as total_variants'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        // Attach data to products
        foreach ($products as $product) {
            // Attach brand name
            $product->brand_name = isset($brands[$product->brand_id])
                ? $brands[$product->brand_id]->name
                : null;

            // Attach category names
            $product->category_names = $categories->get($product->id, []);

            // Attach primary image URL
            $product->image_url = isset($images[$product->id])
                ? $images[$product->id]->url
                : null;

            // Attach total variants
            $product->total_variants = isset($variantCounts[$product->id])
                ? (int) $variantCounts[$product->id]->total_variants
                : 0;
        }
    }

    /**
     * Attach full relations to products (categories, variants with images, images)
     * Similar to findByIdWithRelations but for multiple products
     */
    public function attachFullRelations($products)
    {
        if ($products->isEmpty()) {
            return;
        }

        $productIds = $products->pluck('id')->toArray();

        // Batch load brands
        $brands = DB::table('brands')
            ->whereIn('id', $products->pluck('brand_id')->filter()->unique())
            ->get()
            ->keyBy('id');

        // Batch load categories (full object, not just names)
        $categoriesByProduct = DB::table('product_categories')
            ->join('categories', 'product_categories.category_id', '=', 'categories.id')
            ->whereIn('product_categories.product_id', $productIds)
            ->select('product_categories.product_id', 'categories.*')
            ->get()
            ->groupBy('product_id');

        // Batch load all variants
        $variantsByProduct = DB::table('product_variants')
            ->whereIn('product_id', $productIds)
            ->orderBy('id', 'asc')
            ->get()
            ->groupBy('product_id');

        // Batch load all variant images
        $variantIds = $variantsByProduct->flatten(1)->pluck('id')->toArray();
        $variantImagesByVariant = [];
        if (!empty($variantIds)) {
            $variantImagesByVariant = DB::table('product_variant_images')
                ->whereIn('variant_id', $variantIds)
                ->orderBy('is_primary', 'desc')
                ->orderBy('sort_order', 'asc')
                ->get()
                ->groupBy('variant_id');
        }

        // Batch load all product images
        $imagesByProduct = DB::table('product_images')
            ->whereIn('product_id', $productIds)
            ->orderBy('sort_order', 'asc')
            ->get()
            ->groupBy('product_id');

        // Attach data to products
        foreach ($products as $product) {
            // Decode JSON fields
            if (isset($product->tags) && is_string($product->tags)) {
                $product->tags = json_decode($product->tags);
            }
            if (isset($product->seo_meta) && is_string($product->seo_meta)) {
                $product->seo_meta = json_decode($product->seo_meta);
            }

            // Attach brand name
            $product->brand_name = isset($brands[$product->brand_id])
                ? $brands[$product->brand_id]->name
                : null;

            // Attach categories
            $product->categories = $categoriesByProduct->get($product->id, collect([]))->values();

            // Attach variants with their images
            $variants = $variantsByProduct->get($product->id, collect([]));
            foreach ($variants as $variant) {
                $variant->images = $variantImagesByVariant[$variant->id] ?? collect([]);
            }
            $product->variants = $variants->values();

            // Attach images
            $product->images = $imagesByProduct->get($product->id, collect([]))->values();
        }
    }
}
