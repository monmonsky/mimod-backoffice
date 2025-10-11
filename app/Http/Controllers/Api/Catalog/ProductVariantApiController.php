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
                'sku' => 'required|string|max:100|unique:product_variants,sku',
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

            // Create variant
            $variantId = DB::table('product_variants')->insertGetId($validated);

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
}
