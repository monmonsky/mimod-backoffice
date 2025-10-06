<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Catalog\ProductRepositoryInterface;
use App\Repositories\Contracts\Catalog\CategoryRepositoryInterface;
use App\Repositories\Contracts\Catalog\BrandRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AddProductsController extends Controller
{
    protected $productRepo;
    protected $categoryRepo;
    protected $brandRepo;

    public function __construct(
        ProductRepositoryInterface $productRepo,
        CategoryRepositoryInterface $categoryRepo,
        BrandRepositoryInterface $brandRepo
    ) {
        $this->productRepo = $productRepo;
        $this->categoryRepo = $categoryRepo;
        $this->brandRepo = $brandRepo;
    }

    public function addProducts()
    {
        $categories = $this->categoryRepo->getAllActive();
        $brands = $this->brandRepo->getAllActive();

        return view('pages.catalog.add-products.add-products', compact('categories', 'brands'));
    }

    public function edit($id)
    {
        $product = $this->productRepo->findByIdWithRelations($id);

        if (!$product) {
            return redirect()->route('catalog.products.all-products')
                ->with('error', 'Product not found');
        }

        $categories = $this->categoryRepo->getAllActive();
        $brands = $this->brandRepo->getAllActive();

        // Get selected category IDs
        $selectedCategories = collect($product->categories)->pluck('id')->toArray();

        return view('pages.catalog.add-products.add-products', compact(
            'product',
            'categories',
            'brands',
            'selectedCategories'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'brand_id' => 'nullable|exists:brands,id',
            'age_min' => 'nullable|integer|min:0',
            'age_max' => 'nullable|integer|min:0',
            'tags' => 'nullable|string',
            'status' => 'required|in:active,inactive,draft',
            'is_featured' => 'nullable|in:0,1',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        try {
            DB::beginTransaction();

            // Extract categories before creating product
            $categories = $validated['categories'] ?? [];
            unset($validated['categories']);

            // Process tags
            if (!empty($validated['tags'])) {
                $tags = array_map('trim', explode(',', $validated['tags']));
                $validated['tags'] = json_encode($tags);
            } else {
                $validated['tags'] = null;
            }

            // Add created_by
            $validated['created_by'] = auth()->id();
            $validated['is_featured'] = (int) ($validated['is_featured'] ?? 0);

            // Create product
            $productId = $this->productRepo->create($validated);

            // Sync categories
            if (!empty($categories)) {
                $this->productRepo->syncCategories($productId, $categories);
            }

            logActivity('create', "Created product: {$validated['name']}", 'product', $productId);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'product_id' => $productId
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $id,
            'description' => 'nullable|string',
            'brand_id' => 'nullable|exists:brands,id',
            'age_min' => 'nullable|integer|min:0',
            'age_max' => 'nullable|integer|min:0',
            'tags' => 'nullable|string',
            'status' => 'required|in:active,inactive,draft',
            'is_featured' => 'nullable|in:0,1',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        try {
            DB::beginTransaction();

            $product = $this->productRepo->findById($id);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            // Extract categories before updating product
            $categories = $validated['categories'] ?? [];
            unset($validated['categories']);

            // Process tags
            if (!empty($validated['tags'])) {
                $tags = array_map('trim', explode(',', $validated['tags']));
                $validated['tags'] = json_encode($tags);
            } else {
                $validated['tags'] = null;
            }

            $validated['is_featured'] = (int) ($validated['is_featured'] ?? 0);

            // Update product
            $this->productRepo->update($id, $validated);

            // Sync categories
            if (!empty($categories)) {
                $this->productRepo->syncCategories($id, $categories);
            }

            logActivity('update', "Updated product: {$validated['name']}", 'product', (int)$id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update product: ' . $e->getMessage()
            ], 500);
        }
    }

    // Product Images Management
    public function uploadImages(Request $request, $id)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $product = $this->productRepo->findById($id);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $uploadedImages = [];
            $existingImagesCount = DB::table('product_images')->where('product_id', $id)->count();
            $sortOrder = ($existingImagesCount + 1) * 10;

            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');

                $imageId = DB::table('product_images')->insertGetId([
                    'product_id' => $id,
                    'url' => $path,
                    'is_primary' => $existingImagesCount == 0, // First image is primary
                    'sort_order' => $sortOrder,
                    'created_at' => now(),
                ]);

                $uploadedImages[] = [
                    'id' => $imageId,
                    'url' => Storage::url($path),
                    'is_primary' => $existingImagesCount == 0,
                ];

                $existingImagesCount++;
                $sortOrder += 10;
            }

            logActivity('update', "Uploaded " . count($uploadedImages) . " images", 'product', (int)$id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Images uploaded successfully',
                'images' => $uploadedImages
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload images: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteImage($productId, $imageId)
    {
        try {
            DB::beginTransaction();

            $image = DB::table('product_images')
                ->where('id', $imageId)
                ->where('product_id', $productId)
                ->first();

            if (!$image) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image not found'
                ], 404);
            }

            // Delete file from storage
            if (Storage::disk('public')->exists($image->url)) {
                Storage::disk('public')->delete($image->url);
            }

            // Delete from database
            DB::table('product_images')->where('id', $imageId)->delete();

            // If deleted image was primary, set first image as primary
            if ($image->is_primary) {
                $firstImage = DB::table('product_images')
                    ->where('product_id', $productId)
                    ->orderBy('sort_order', 'asc')
                    ->first();

                if ($firstImage) {
                    DB::table('product_images')
                        ->where('id', $firstImage->id)
                        ->update(['is_primary' => true]);
                }
            }

            logActivity('update', "Deleted product image", 'product', (int)$productId);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function setPrimaryImage(Request $request, $productId, $imageId)
    {
        try {
            DB::beginTransaction();

            // Remove primary from all images
            DB::table('product_images')
                ->where('product_id', $productId)
                ->update(['is_primary' => false]);

            // Set new primary
            DB::table('product_images')
                ->where('id', $imageId)
                ->where('product_id', $productId)
                ->update(['is_primary' => true]);

            logActivity('update', "Set primary image", 'product', (int)$productId);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Primary image updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update primary image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateImagesOrder(Request $request, $productId)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|exists:product_images,id',
            'order.*.sort_order' => 'required|integer',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->input('order') as $item) {
                DB::table('product_images')
                    ->where('id', $item['id'])
                    ->where('product_id', $productId)
                    ->update(['sort_order' => $item['sort_order']]);
            }

            logActivity('update', "Reordered product images", 'product', (int)$productId);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Images order updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update images order: ' . $e->getMessage()
            ], 500);
        }
    }

    // Product Variants Management
    public function storeVariant(Request $request, $productId)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:100|unique:product_variants,sku',
            'size' => 'required|string|max:50',
            'color' => 'nullable|string|max:50',
            'weight_gram' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'barcode' => 'nullable|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            $product = $this->productRepo->findById($productId);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $validated['product_id'] = $productId;
            $validated['created_at'] = now();
            $validated['updated_at'] = now();

            $variantId = DB::table('product_variants')->insertGetId($validated);

            logActivity('create', "Created variant for product: {$product->name}", 'product_variant', $variantId);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Variant created successfully',
                'variant_id' => $variantId
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create variant: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateVariant(Request $request, $productId, $variantId)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:100|unique:product_variants,sku,' . $variantId,
            'size' => 'required|string|max:50',
            'color' => 'nullable|string|max:50',
            'weight_gram' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'barcode' => 'nullable|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            $variant = DB::table('product_variants')
                ->where('id', $variantId)
                ->where('product_id', $productId)
                ->first();

            if (!$variant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Variant not found'
                ], 404);
            }

            $validated['updated_at'] = now();

            DB::table('product_variants')
                ->where('id', $variantId)
                ->update($validated);

            logActivity('update', "Updated variant: {$validated['sku']}", 'product_variant', $variantId);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Variant updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update variant: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteVariant($productId, $variantId)
    {
        try {
            DB::beginTransaction();

            $variant = DB::table('product_variants')
                ->where('id', $variantId)
                ->where('product_id', $productId)
                ->first();

            if (!$variant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Variant not found'
                ], 404);
            }

            DB::table('product_variants')->where('id', $variantId)->delete();

            logActivity('delete', "Deleted variant: {$variant->sku}", 'product_variant', $variantId);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Variant deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete variant: ' . $e->getMessage()
            ], 500);
        }
    }
}
