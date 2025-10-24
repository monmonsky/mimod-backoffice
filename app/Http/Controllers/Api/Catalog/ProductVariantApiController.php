<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductVariantApiController extends Controller
{
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Get variant by ID with images
     */
    public function show($id)
    {
        try {
            $variant = DB::table('product_variants')
                ->where('id', $id)
                ->first();

            if (!$variant) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Variant not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Get variant images
            $variant->images = DB::table('product_variant_images')
                ->where('variant_id', $id)
                ->orderBy('is_primary', 'desc')
                ->orderBy('sort_order', 'asc')
                ->get();

            // Get product info
            $variant->product = DB::table('products')
                ->where('id', $variant->product_id)
                ->select('id', 'name', 'slug')
                ->first();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Variant retrieved successfully')
                ->setData($variant);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve variant: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Create new variant
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'sku' => 'nullable|string|max:100|unique:product_variants,sku',
                'size' => 'required|string|max:50',
                'color' => 'nullable|string|max:50',
                'weight_gram' => 'required|integer|min:0',
                'price' => 'required|numeric|min:0',
                'compare_at_price' => 'nullable|numeric|min:0',
                'stock_quantity' => 'required|integer|min:0',
                'barcode' => 'nullable|string|max:100',
                'images' => 'nullable|array'
            ]);

            // Extract images before creating variant
            $imageUrls = $validated['images'] ?? [];
            unset($validated['images']);

            // Set default values
            $validated['reserved_quantity'] = 0;
            $validated['created_at'] = now();
            $validated['updated_at'] = now();

            // Auto-generate SKU if not provided
            $autoGenerateSku = empty($validated['sku']) && config('app.barcode_auto_generate', true);

            if ($autoGenerateSku) {
                // Get product to get brand and category
                $product = DB::table('products')
                    ->where('id', $validated['product_id'])
                    ->first();

                // Get category (first category)
                $category = DB::table('product_categories')
                    ->join('categories', 'categories.id', '=', 'product_categories.category_id')
                    ->where('product_categories.product_id', $product->id)
                    ->select('categories.id')
                    ->first();

                $categoryId = $category ? $category->id : null;

                // Generate temporary SKU (will be updated after variant is created)
                $tempSku = 'TEMP-' . time() . '-' . uniqid();
                $validated['sku'] = $tempSku;
            }

            // Create variant
            $variantId = DB::table('product_variants')->insertGetId($validated);

            // If auto-generate is enabled, generate and update SKU & Barcode
            if ($autoGenerateSku) {
                $product = DB::table('products')
                    ->where('id', $validated['product_id'])
                    ->first();

                $category = DB::table('product_categories')
                    ->join('categories', 'categories.id', '=', 'product_categories.category_id')
                    ->where('product_categories.product_id', $product->id)
                    ->select('categories.id')
                    ->first();

                $categoryId = $category ? $category->id : null;

                // Generate SKU with stock quantity
                $sku = \App\Helpers\SkuBarcodeGenerator::generateSKU(
                    $product->id,
                    $product->brand_id,
                    $categoryId,
                    $validated['color'],
                    $validated['size'],
                    $validated['stock_quantity']
                );
                $uniqueSku = \App\Helpers\SkuBarcodeGenerator::ensureUniqueSKU($sku, $variantId);

                // Generate Barcode if not provided
                $barcode = !empty($validated['barcode'])
                    ? $validated['barcode']
                    : \App\Helpers\SkuBarcodeGenerator::generateBarcode($variantId);

                // Update variant with generated SKU & Barcode
                DB::table('product_variants')
                    ->where('id', $variantId)
                    ->update([
                        'sku' => $uniqueSku,
                        'barcode' => $barcode,
                        'updated_at' => now()
                    ]);

                \Log::info('Auto-generated SKU & Barcode', [
                    'variant_id' => $variantId,
                    'sku' => $uniqueSku,
                    'barcode' => $barcode
                ]);
            }

            // Get product for folder naming
            $product = DB::table('products')->where('id', $validated['product_id'])->first();
            $folderName = $product->slug ?? \Str::slug($product->name);

            // Move images from temp to permanent location
            $movedImages = [];
            if (!empty($imageUrls)) {
                $permanentDir = 'products/' . $folderName . '/variants';

                // Get existing images count for sort_order
                $existingCount = DB::table('product_variant_images')->where('variant_id', $variantId)->count();
                $sortOrder = $existingCount + 1;

                \Log::info('Processing variant images', [
                    'variant_id' => $variantId,
                    'image_urls' => $imageUrls,
                    'folder_name' => $folderName
                ]);

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
                        \Log::warning('Empty image URL at index ' . $index);
                        continue;
                    }

                    // Check if this is a temp URL
                    if (strpos($imageUrl, '/temp/') === false) {
                        \Log::warning('Not a temp URL, skipping', ['url' => $imageUrl]);
                        continue;
                    }

                    // Extract path from URL
                    $tempPath = str_replace(env('FTP_URL') . '/', '', $imageUrl);

                    \Log::info('Checking temp file', [
                        'temp_path' => $tempPath,
                        'exists' => \Storage::disk('ftp')->exists($tempPath)
                    ]);

                    // Check if temp file exists
                    if (!\Storage::disk('ftp')->exists($tempPath)) {
                        \Log::warning('Temp file not found', ['temp_path' => $tempPath]);
                        continue;
                    }

                    // Generate new filename
                    $filename = basename($tempPath);
                    $newFilename = time() . '_' . uniqid() . '.' . pathinfo($filename, PATHINFO_EXTENSION);
                    $newPath = $permanentDir . '/' . $newFilename;

                    // Move file
                    \Storage::disk('ftp')->move($tempPath, $newPath);

                    // Generate full URL
                    $fullUrl = env('FTP_URL') . '/' . $newPath;

                    // Determine if primary
                    $isPrimary = ($existingCount === 0 && $index === 0);

                    // Insert to database
                    $imageId = DB::table('product_variant_images')->insertGetId([
                        'variant_id' => $variantId,
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
                    $firstTempUrl = is_string(reset($tempUrls)) ? reset($tempUrls) : reset($tempUrls)['url'];
                    $firstTempPath = str_replace(env('FTP_URL') . '/', '', $firstTempUrl);
                    $tempDirectory = dirname($firstTempPath);
                    $remainingFiles = \Storage::disk('ftp')->files($tempDirectory);
                    if (empty($remainingFiles)) {
                        \Storage::disk('ftp')->deleteDirectory($tempDirectory);
                    }
                }
            }

            // Log activity
            logActivity(
                'create',
                "Created variant: {$validated['sku']}",
                'product_variant',
                (int) $variantId
            );

            // Get created variant with all data
            $createdVariant = DB::table('product_variants')
                ->where('id', $variantId)
                ->first();

            // Get variant images
            $createdVariant->images = DB::table('product_variant_images')
                ->where('variant_id', $variantId)
                ->orderBy('is_primary', 'desc')
                ->orderBy('sort_order', 'asc')
                ->get();

            // Get product info
            $createdVariant->product = DB::table('products')
                ->where('id', $createdVariant->product_id)
                ->select('id', 'name', 'slug')
                ->first();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('201')
                ->setMessage('Variant created successfully')
                ->setData($createdVariant);

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
                ->setMessage('Failed to create variant: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update variant
     */
    public function update(Request $request, $id)
    {
        try {
            $variant = DB::table('product_variants')->where('id', $id)->first();

            if (!$variant) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Variant not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $validated = $request->validate([
                'sku' => 'required|string|max:100|unique:product_variants,sku,' . $id,
                'size' => 'required|string|max:50',
                'color' => 'nullable|string|max:50',
                'weight_gram' => 'required|integer|min:0',
                'price' => 'required|numeric|min:0',
                'compare_at_price' => 'nullable|numeric|min:0',
                'stock_quantity' => 'required|integer|min:0',
                'barcode' => 'nullable|string|max:100',
                'images' => 'nullable|array'
            ]);

            // Extract images
            $imageUrls = $validated['images'] ?? [];
            unset($validated['images']);

            // Update variant
            $validated['updated_at'] = now();
            DB::table('product_variants')->where('id', $id)->update($validated);

            // Get product for folder naming
            $product = DB::table('products')->where('id', $variant->product_id)->first();
            $folderName = $product->slug ?? \Str::slug($product->name);

            // Process images - only move new images from temp
            $movedImages = [];
            if (!empty($imageUrls)) {
                $permanentDir = 'products/' . $folderName . '/variants';

                // Get existing images count for sort_order
                $existingCount = DB::table('product_variant_images')->where('variant_id', $id)->count();
                $sortOrder = $existingCount + 1;

                $tempUrls = [];
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

                    // Check if this is a temp URL (contains /temp/)
                    if (strpos($imageUrl, '/temp/') === false) {
                        // This is an existing image, skip it
                        continue;
                    }

                    // This is a temp image, process it
                    $tempUrls[] = $imageUrl;

                    // Extract path from URL
                    $tempPath = str_replace(env('FTP_URL') . '/', '', $imageUrl);

                    // Check if temp file exists
                    if (!\Storage::disk('ftp')->exists($tempPath)) {
                        continue;
                    }

                    // Generate new filename
                    $filename = basename($tempPath);
                    $newFilename = time() . '_' . uniqid() . '.' . pathinfo($filename, PATHINFO_EXTENSION);
                    $newPath = $permanentDir . '/' . $newFilename;

                    // Move file
                    \Storage::disk('ftp')->move($tempPath, $newPath);

                    // Generate full URL
                    $fullUrl = env('FTP_URL') . '/' . $newPath;

                    // Determine if primary
                    $isPrimary = ($existingCount === 0 && $index === 0);

                    // Insert to database
                    $imageId = DB::table('product_variant_images')->insertGetId([
                        'variant_id' => $id,
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
                "Updated variant: {$validated['sku']}",
                'product_variant',
                (int) $id
            );

            // Get updated variant with all data
            $updatedVariant = DB::table('product_variants')
                ->where('id', $id)
                ->first();

            // Get variant images
            $updatedVariant->images = DB::table('product_variant_images')
                ->where('variant_id', $id)
                ->orderBy('is_primary', 'desc')
                ->orderBy('sort_order', 'asc')
                ->get();

            // Get product info
            $updatedVariant->product = DB::table('products')
                ->where('id', $updatedVariant->product_id)
                ->select('id', 'name', 'slug')
                ->first();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Variant updated successfully')
                ->setData($updatedVariant);

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
                ->setMessage('Failed to update variant: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete variant
     */
    public function destroy($id)
    {
        try {
            $variant = DB::table('product_variants')->where('id', $id)->first();

            if (!$variant) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Variant not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Check if variant is in orders
            $inOrders = DB::table('order_items')->where('variant_id', $id)->exists();
            if ($inOrders) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Cannot delete variant: It is associated with orders')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            // Get variant images for deletion
            $images = DB::table('product_variant_images')->where('variant_id', $id)->get();

            // Delete image files from storage
            foreach ($images as $image) {
                $path = str_replace(env('FTP_URL') . '/', '', $image->url);
                if (\Storage::disk('ftp')->exists($path)) {
                    \Storage::disk('ftp')->delete($path);
                }
            }

            // Delete image records
            DB::table('product_variant_images')->where('variant_id', $id)->delete();

            // Delete variant
            DB::table('product_variants')->where('id', $id)->delete();

            // Log activity
            logActivity(
                'delete',
                "Deleted variant: {$variant->sku}",
                'product_variant',
                (int) $id
            );

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Variant deleted successfully')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to delete variant: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Generate SKU for a variant
     *
     * @param Request $request
     * @param int $variantId
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateSku(Request $request, $variantId)
    {
        try {
            // Get variant with product info
            $variant = DB::table('product_variants')
                ->where('id', $variantId)
                ->first();

            if (!$variant) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Variant not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Get product to get brand and category
            $product = DB::table('products')
                ->where('id', $variant->product_id)
                ->first();

            // Get category (first category)
            $category = DB::table('product_categories')
                ->join('categories', 'categories.id', '=', 'product_categories.category_id')
                ->where('product_categories.product_id', $product->id)
                ->select('categories.id')
                ->first();

            $categoryId = $category ? $category->id : null;

            // Generate SKU
            $sku = \App\Helpers\SkuBarcodeGenerator::generateSKU(
                $product->id,
                $product->brand_id,
                $categoryId,
                $variant->color,
                $variant->size,
                $variant->stock_quantity
            );

            // Ensure uniqueness
            $uniqueSku = \App\Helpers\SkuBarcodeGenerator::ensureUniqueSKU($sku, $variantId);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('SKU generated successfully')
                ->setData([
                    'variant_id' => $variantId,
                    'sku' => $uniqueSku,
                    'format' => config('app.sku_format')
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to generate SKU: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Generate Barcode for a variant
     *
     * @param Request $request
     * @param int $variantId
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateBarcode(Request $request, $variantId)
    {
        try {
            // Check if variant exists
            $variant = DB::table('product_variants')
                ->where('id', $variantId)
                ->first();

            if (!$variant) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Variant not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Generate Barcode
            $barcode = \App\Helpers\SkuBarcodeGenerator::generateBarcode($variantId);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Barcode generated successfully')
                ->setData([
                    'variant_id' => $variantId,
                    'barcode' => $barcode,
                    'type' => config('app.barcode_type')
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to generate barcode: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Generate both SKU and Barcode for a variant
     *
     * @param Request $request
     * @param int $variantId
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateSkuAndBarcode(Request $request, $variantId)
    {
        try {
            // Get variant with product info
            $variant = DB::table('product_variants')
                ->where('id', $variantId)
                ->first();

            if (!$variant) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Variant not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Get product
            $product = DB::table('products')
                ->where('id', $variant->product_id)
                ->first();

            // Get category
            $category = DB::table('product_categories')
                ->join('categories', 'categories.id', '=', 'product_categories.category_id')
                ->where('product_categories.product_id', $product->id)
                ->select('categories.id')
                ->first();

            $categoryId = $category ? $category->id : null;

            // Generate SKU
            $sku = \App\Helpers\SkuBarcodeGenerator::generateSKU(
                $product->id,
                $product->brand_id,
                $categoryId,
                $variant->color,
                $variant->size,
                $variant->stock_quantity
            );
            $uniqueSku = \App\Helpers\SkuBarcodeGenerator::ensureUniqueSKU($sku, $variantId);

            // Generate Barcode
            $barcode = \App\Helpers\SkuBarcodeGenerator::generateBarcode($variantId);

            // Update variant
            DB::table('product_variants')
                ->where('id', $variantId)
                ->update([
                    'sku' => $uniqueSku,
                    'barcode' => $barcode,
                    'updated_at' => now()
                ]);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('SKU and Barcode generated and saved successfully')
                ->setData([
                    'variant_id' => $variantId,
                    'sku' => $uniqueSku,
                    'barcode' => $barcode,
                    'sku_format' => config('app.sku_format'),
                    'barcode_type' => config('app.barcode_type')
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to generate SKU and barcode: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Batch generate SKU and Barcode for multiple variants or all variants of a product
     *
     * @param Request $request
     * @param int $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function batchGenerate(Request $request, $productId)
    {
        try {
            // Validate
            $validated = $request->validate([
                'variant_ids' => 'nullable|array',
                'variant_ids.*' => 'integer|exists:product_variants,id',
                'generate_all' => 'nullable|boolean',
            ]);

            // Get product
            $product = DB::table('products')->where('id', $productId)->first();

            if (!$product) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Product not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Get category
            $category = DB::table('product_categories')
                ->join('categories', 'categories.id', '=', 'product_categories.category_id')
                ->where('product_categories.product_id', $productId)
                ->select('categories.id')
                ->first();

            $categoryId = $category ? $category->id : null;

            // Get variants to process
            if ($request->input('generate_all', false)) {
                $variants = DB::table('product_variants')
                    ->where('product_id', $productId)
                    ->get();
            } elseif (!empty($validated['variant_ids'])) {
                $variants = DB::table('product_variants')
                    ->whereIn('id', $validated['variant_ids'])
                    ->where('product_id', $productId)
                    ->get();
            } else {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Please provide variant_ids or set generate_all to true')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $generated = [];

            foreach ($variants as $variant) {
                // Generate SKU
                $sku = \App\Helpers\SkuBarcodeGenerator::generateSKU(
                    $productId,
                    $product->brand_id,
                    $categoryId,
                    $variant->color,
                    $variant->size,
                    $variant->stock_quantity
                );
                $uniqueSku = \App\Helpers\SkuBarcodeGenerator::ensureUniqueSKU($sku, $variant->id);

                // Generate Barcode
                $barcode = \App\Helpers\SkuBarcodeGenerator::generateBarcode($variant->id);

                // Update variant
                DB::table('product_variants')
                    ->where('id', $variant->id)
                    ->update([
                        'sku' => $uniqueSku,
                        'barcode' => $barcode,
                        'updated_at' => now()
                    ]);

                $generated[] = [
                    'variant_id' => $variant->id,
                    'sku' => $uniqueSku,
                    'barcode' => $barcode,
                    'color' => $variant->color,
                    'size' => $variant->size
                ];
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage(count($generated) . ' variants processed successfully')
                ->setData([
                    'product_id' => $productId,
                    'total_processed' => count($generated),
                    'variants' => $generated
                ]);

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
                ->setMessage('Failed to batch generate: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
