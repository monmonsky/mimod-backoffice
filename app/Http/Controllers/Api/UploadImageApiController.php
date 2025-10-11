<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadImageApiController extends Controller
{
    protected $response;

    // Allowed upload types and their directories
    protected $allowedTypes = [
        'brand' => 'brands',
        'category' => 'categories',
        'product' => 'products',
        'user' => 'users',
        'avatar' => 'avatars',
        'banner' => 'banners',
    ];

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Generate FTP URL from path
     */
    private function getFtpUrl($path)
    {
        return env('FTP_URL') . '/' . ltrim($path, '/');
    }

    /**
     * Extract path from FTP URL
     */
    private function getPathFromUrl($url)
    {
        $ftpUrl = env('FTP_URL');
        return str_replace($ftpUrl . '/', '', $url);
    }

    /**
     * Upload image
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        try {
            $validated = $request->validate([
                'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp,svg|max:20480', // 10MB max
                'type' => 'required|string|in:' . implode(',', array_keys($this->allowedTypes)),
                'path' => 'nullable|string', // Optional custom path
            ]);

            if (!$request->hasFile('image')) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('No image file provided')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $type = $request->type;
            $image = $request->file('image');

            // Determine directory path
            $directory = $request->filled('path')
                ? $request->path
                : $this->allowedTypes[$type];

            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Store the image
            $path = $image->storeAs($directory, $filename, 'ftp');

            // Generate URL
            $url = $this->getFtpUrl($path);

            // Log activity
            logActivity('create', "Uploaded {$type} image: {$filename}", 'upload', null);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Image uploaded successfully')
                ->setData([
                    'url' => $url,
                    'path' => $path,
                    'filename' => $filename,
                    'type' => $type,
                    'size' => $image->getSize(),
                    'mime_type' => $image->getMimeType(),
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
                ->setMessage('Failed to upload image: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Upload multiple images
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadMultiple(Request $request)
    {
        try {
            $validated = $request->validate([
                'images' => 'required|array|min:1|max:10',
                'images.*' => 'required|image|mimes:jpeg,jpg,png,gif,webp,svg|max:20480', // 10MB max
                'type' => 'required|string|in:' . implode(',', array_keys($this->allowedTypes)),
                'path' => 'nullable|string',
            ]);

            $type = $request->type;
            $uploadedFiles = [];

            // Determine directory path
            $directory = $request->filled('path')
                ? $request->path
                : $this->allowedTypes[$type];

            foreach ($request->file('images') as $image) {
                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                // Store the image
                $path = $image->storeAs($directory, $filename, 'ftp');

                // Generate URL
                $url = $this->getFtpUrl($path);

                $uploadedFiles[] = [
                    'url' => $url,
                    'path' => $path,
                    'filename' => $filename,
                    'size' => $image->getSize(),
                    'mime_type' => $image->getMimeType(),
                ];
            }

            // Log activity
            logActivity('create', "Uploaded {$type} images: " . count($uploadedFiles) . " files", 'upload', null);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Images uploaded successfully')
                ->setData([
                    'files' => $uploadedFiles,
                    'count' => count($uploadedFiles),
                    'type' => $type,
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
                ->setMessage('Failed to upload images: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete image
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        try {
            $validated = $request->validate([
                'path' => 'required|string',
            ]);

            $path = $request->path;

            // Check if file exists
            if (!Storage::disk('ftp')->exists($path)) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Image not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Delete the file
            Storage::disk('ftp')->delete($path);

            // Log activity
            logActivity('delete', "Deleted image: {$path}", 'upload', null);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Image deleted successfully')
                ->setData([]);

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
                ->setMessage('Failed to delete image: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Upload bulk images for product or variant
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadBulk(Request $request)
    {
        try {
            $validated = $request->validate([
                'images' => 'required|array|min:1|max:20',
                'images.*' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:10024',
                'type' => 'required|string|in:product,variant',
                'product_id' => 'required|integer|exists:products,id',
                'variant_id' => 'required_if:type,variant|integer|exists:product_variants,id',
            ]);

            $type = $request->type;
            $productId = $request->product_id;
            $variantId = $request->variant_id;

            // Get product info for folder naming
            $product = \DB::table('products')->where('id', $productId)->first();
            if (!$product) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Product not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Determine directory based on type
            $folderName = $product->slug ?? \Str::slug($product->name);
            if ($type === 'product') {
                $directory = 'products/' . $folderName;
                $table = 'product_images';
                $foreignKey = 'product_id';
                $foreignId = $productId;
            } else {
                // Variant
                $variant = \DB::table('product_variants')->where('id', $variantId)->first();
                if (!$variant) {
                    $result = (new ResultBuilder())
                        ->setStatus(false)
                        ->setStatusCode('404')
                        ->setMessage('Variant not found');

                    return response()->json($this->response->generateResponse($result), 404);
                }

                $directory = 'products/' . $folderName . '/variants';
                $table = 'product_variant_images';
                $foreignKey = 'variant_id';
                $foreignId = $variantId;
            }

            // Get existing images count for sort_order
            $existingCount = \DB::table($table)->where($foreignKey, $foreignId)->count();
            $sortOrder = $existingCount + 1;

            $uploadedImages = [];

            foreach ($request->file('images') as $index => $image) {
                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                // Store the image
                $path = $image->storeAs($directory, $filename, 'ftp');

                // Generate URL
                $url = $this->getFtpUrl($path);

                // Determine if this is primary (first image and no existing images)
                $isPrimary = ($existingCount === 0 && $index === 0);

                // Insert to database with full URL
                $imageId = \DB::table($table)->insertGetId([
                    $foreignKey => $foreignId,
                    'url' => $url,
                    'alt_text' => null,
                    'is_primary' => $isPrimary,
                    'sort_order' => $sortOrder,
                    'created_at' => now(),
                ]);

                $uploadedImages[] = [
                    'id' => $imageId,
                    'url' => $url,
                    'path' => $path,
                    'filename' => $filename,
                    'is_primary' => $isPrimary,
                    'sort_order' => $sortOrder,
                ];

                $sortOrder++;
            }

            // Log activity
            $entityType = $type === 'product' ? 'Product' : 'Product Variant';
            logActivity(
                'create',
                "Uploaded {$type} images: " . count($uploadedImages) . " files for {$entityType} ID: {$foreignId}",
                'upload',
                (int) $foreignId
            );

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Images uploaded successfully')
                ->setData([
                    'images' => $uploadedImages,
                    'count' => count($uploadedImages),
                    'type' => $type,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
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
                ->setMessage('Failed to upload images: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Upload temporary images (before product/variant is created)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadTemporary(Request $request)
    {
        try {
            $validated = $request->validate([
                'images' => 'required|array|min:1|max:20',
                'images.*' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:20480', // 20MB max
                'type' => 'required|string|in:product,variant',
                'session_id' => 'nullable|string', // Optional unique identifier
                'product_id' => 'nullable|integer|exists:products,id', // If editing existing product
                'variant_id' => 'nullable|integer|exists:product_variants,id', // If editing existing variant
                'alt_text' => 'nullable|string|max:255', // Alt text for all images
            ]);

            $type = $request->type;
            $productId = $request->product_id;
            $variantId = $request->variant_id;
            $sessionId = $request->session_id ?? uniqid('temp_', true);
            $altText = $request->alt_text;

            // If product_id exists, upload directly to permanent folder
            if ($productId && $type === 'product') {
                $product = \DB::table('products')->where('id', $productId)->first();
                if (!$product) {
                    $result = (new ResultBuilder())
                        ->setStatus(false)
                        ->setStatusCode('404')
                        ->setMessage('Product not found');
                    return response()->json($this->response->generateResponse($result), 404);
                }

                $folderName = $product->slug ?? \Str::slug($product->name);
                $directory = 'products/' . $folderName;
                $isTemp = false;
            } elseif ($variantId && $type === 'variant') {
                // If variant_id exists, get product and upload to variant folder
                $variant = \DB::table('product_variants')->where('id', $variantId)->first();
                if (!$variant) {
                    $result = (new ResultBuilder())
                        ->setStatus(false)
                        ->setStatusCode('404')
                        ->setMessage('Variant not found');
                    return response()->json($this->response->generateResponse($result), 404);
                }

                $product = \DB::table('products')->where('id', $variant->product_id)->first();
                $folderName = $product->slug ?? \Str::slug($product->name);
                $directory = 'products/' . $folderName . '/variants';
                $isTemp = false;
            } else {
                // No product_id or variant_id, upload to temp folder
                $directory = 'temp/' . $type . 's/' . $sessionId;
                $isTemp = true;
            }

            $uploadedImages = [];
            $existingCount = 0;

            if ($productId && $type === 'product') {
                $existingCount = \DB::table('product_images')->where('product_id', $productId)->count();
            } elseif ($variantId && $type === 'variant') {
                $existingCount = \DB::table('product_variant_images')->where('variant_id', $variantId)->count();
            }

            $sortOrder = $existingCount + 1;

            foreach ($request->file('images') as $index => $image) {
                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                // Store the image
                $path = $image->storeAs($directory, $filename, 'ftp');

                // Generate URL
                $url = $this->getFtpUrl($path);

                $imageData = [
                    'url' => $url,
                    'path' => $path,
                    'filename' => $filename,
                    'temp' => $isTemp,
                ];

                // If product_id or variant_id exists, save directly to database
                if ($productId && $type === 'product') {
                    $isPrimary = ($existingCount === 0 && $index === 0);

                    $imageId = \DB::table('product_images')->insertGetId([
                        'product_id' => $productId,
                        'url' => $url,
                        'alt_text' => $altText,
                        'is_primary' => $isPrimary,
                        'sort_order' => $sortOrder,
                        'created_at' => now(),
                    ]);

                    $imageData['id'] = $imageId;
                    $imageData['is_primary'] = $isPrimary;
                    $imageData['sort_order'] = $sortOrder;
                    $imageData['alt_text'] = $altText;

                    $sortOrder++;
                } elseif ($variantId && $type === 'variant') {
                    $isPrimary = ($existingCount === 0 && $index === 0);

                    $imageId = \DB::table('product_variant_images')->insertGetId([
                        'variant_id' => $variantId,
                        'url' => $url,
                        'alt_text' => $altText,
                        'is_primary' => $isPrimary,
                        'sort_order' => $sortOrder,
                        'created_at' => now(),
                    ]);

                    $imageData['id'] = $imageId;
                    $imageData['is_primary'] = $isPrimary;
                    $imageData['sort_order'] = $sortOrder;
                    $imageData['alt_text'] = $altText;

                    $sortOrder++;
                }

                $uploadedImages[] = $imageData;
            }

            // Log activity
            $foreignId = null;
            if ($productId) {
                $foreignId = $productId;
                $logMessage = $isTemp
                    ? "Uploaded temporary {$type} images: " . count($uploadedImages) . " files"
                    : "Uploaded {$type} images directly to product {$productId}: " . count($uploadedImages) . " files";
            } elseif ($variantId) {
                $foreignId = $variantId;
                $logMessage = $isTemp
                    ? "Uploaded temporary {$type} images: " . count($uploadedImages) . " files"
                    : "Uploaded {$type} images directly to variant {$variantId}: " . count($uploadedImages) . " files";
            } else {
                $logMessage = "Uploaded temporary {$type} images: " . count($uploadedImages) . " files";
            }

            logActivity(
                'create',
                $logMessage,
                'upload',
                $foreignId
            );

            $resultMessage = $isTemp
                ? 'Temporary images uploaded successfully'
                : 'Images uploaded and saved successfully';

            $resultData = [
                'images' => $uploadedImages,
                'count' => count($uploadedImages),
                'type' => $type,
                'is_temp' => $isTemp,
            ];

            if ($isTemp) {
                $resultData['session_id'] = $sessionId;
                $resultData['note'] = 'These images are temporary and will be moved when product/variant is saved';
            } else {
                if ($productId) {
                    $resultData['product_id'] = $productId;
                }
                if ($variantId) {
                    $resultData['variant_id'] = $variantId;
                }
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage($resultMessage)
                ->setData($resultData);

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
                ->setMessage('Failed to upload temporary images: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Move temporary images to permanent location and save to database
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function moveFromTemp(Request $request)
    {
        try {
            $validated = $request->validate([
                'temp_paths' => 'required|array|min:1',
                'temp_paths.*' => 'required|string',
                'type' => 'required|string|in:product,variant',
                'product_id' => 'required|integer|exists:products,id',
                'variant_id' => 'required_if:type,variant|integer|exists:product_variants,id',
            ]);

            $type = $request->type;
            $productId = $request->product_id;
            $variantId = $request->variant_id;
            $tempPaths = $request->temp_paths;

            // Get product info for folder naming
            $product = \DB::table('products')->where('id', $productId)->first();
            if (!$product) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Product not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Determine permanent directory and table
            $folderName = $product->slug ?? \Str::slug($product->name);
            if ($type === 'product') {
                $permanentDir = 'products/' . $folderName;
                $table = 'product_images';
                $foreignKey = 'product_id';
                $foreignId = $productId;
            } else {
                // Variant
                $variant = \DB::table('product_variants')->where('id', $variantId)->first();
                if (!$variant) {
                    $result = (new ResultBuilder())
                        ->setStatus(false)
                        ->setStatusCode('404')
                        ->setMessage('Variant not found');

                    return response()->json($this->response->generateResponse($result), 404);
                }

                $permanentDir = 'products/' . $folderName . '/variants';
                $table = 'product_variant_images';
                $foreignKey = 'variant_id';
                $foreignId = $variantId;
            }

            // Get existing images count for sort_order
            $existingCount = \DB::table($table)->where($foreignKey, $foreignId)->count();
            $sortOrder = $existingCount + 1;

            $movedImages = [];

            foreach ($tempPaths as $index => $tempPath) {
                // Check if temp file exists
                if (!Storage::disk('ftp')->exists($tempPath)) {
                    continue; // Skip if file doesn't exist
                }

                // Get file info
                $filename = basename($tempPath);
                $newFilename = time() . '_' . uniqid() . '.' . pathinfo($filename, PATHINFO_EXTENSION);
                $newPath = $permanentDir . '/' . $newFilename;

                // Move file from temp to permanent location
                Storage::disk('ftp')->move($tempPath, $newPath);

                // Generate URL
                $url = $this->getFtpUrl($newPath);

                // Determine if this is primary
                $isPrimary = ($existingCount === 0 && $index === 0);

                // Insert to database with full URL
                $imageId = \DB::table($table)->insertGetId([
                    $foreignKey => $foreignId,
                    'url' => $url,
                    'alt_text' => null,
                    'is_primary' => $isPrimary,
                    'sort_order' => $sortOrder,
                    'created_at' => now(),
                ]);

                $movedImages[] = [
                    'id' => $imageId,
                    'url' => $url,
                    'path' => $newPath,
                    'is_primary' => $isPrimary,
                    'sort_order' => $sortOrder,
                ];

                $sortOrder++;
            }

            // Cleanup empty temp directories
            $this->cleanupTempDirectory($tempPaths[0] ?? null);

            // Log activity
            $entityType = $type === 'product' ? 'Product' : 'Product Variant';
            logActivity(
                'create',
                "Moved temporary images to permanent location for {$entityType} ID: {$foreignId}",
                'upload',
                (int) $foreignId
            );

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Images moved successfully')
                ->setData([
                    'images' => $movedImages,
                    'count' => count($movedImages),
                    'type' => $type,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
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
                ->setMessage('Failed to move images: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete product image by ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteProductImage($id)
    {
        try {
            // Get image from database
            $image = \DB::table('product_images')->where('id', $id)->first();

            if (!$image) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Image not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Extract path from URL
            $path = $this->getPathFromUrl($image->url);

            // Delete file from storage
            if (Storage::disk('ftp')->exists($path)) {
                Storage::disk('ftp')->delete($path);
            }

            // Delete from database
            \DB::table('product_images')->where('id', $id)->delete();

            // If deleted image was primary, set the next image (by sort_order) as primary
            if ($image->is_primary) {
                // Get the next image after the deleted one by sort_order
                $newPrimary = \DB::table('product_images')
                    ->where('product_id', $image->product_id)
                    ->where('sort_order', '>', $image->sort_order)
                    ->orderBy('sort_order', 'asc')
                    ->first();

                // If no image after, get the first one
                if (!$newPrimary) {
                    $newPrimary = \DB::table('product_images')
                        ->where('product_id', $image->product_id)
                        ->orderBy('sort_order', 'asc')
                        ->first();
                }

                if ($newPrimary) {
                    \DB::table('product_images')
                        ->where('id', $newPrimary->id)
                        ->update(['is_primary' => true]);
                }
            }

            // Log activity
            logActivity(
                'delete',
                "Deleted product image ID: {$id}",
                'product_image',
                (int) $image->product_id
            );

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Image deleted successfully')
                ->setData([
                    'deleted_image_id' => $id,
                    'product_id' => $image->product_id
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to delete image: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Set product image as primary
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function setPrimaryImage($id)
    {
        try {
            // Get image from database
            $image = \DB::table('product_images')->where('id', $id)->first();

            if (!$image) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Image not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            // If already primary, no need to update
            if ($image->is_primary) {
                $result = (new ResultBuilder())
                    ->setStatus(true)
                    ->setStatusCode('200')
                    ->setMessage('Image is already primary')
                    ->setData([
                        'image_id' => $id,
                        'product_id' => $image->product_id
                    ]);

                return response()->json($this->response->generateResponse($result), 200);
            }

            // Set all images of this product to not primary
            \DB::table('product_images')
                ->where('product_id', $image->product_id)
                ->update(['is_primary' => false]);

            // Set this image as primary
            \DB::table('product_images')
                ->where('id', $id)
                ->update(['is_primary' => true]);

            // Log activity
            logActivity(
                'update',
                "Set image ID: {$id} as primary for product {$image->product_id}",
                'product_image',
                (int) $image->product_id
            );

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Primary image updated successfully')
                ->setData([
                    'image_id' => $id,
                    'product_id' => $image->product_id
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to set primary image: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Cleanup empty temporary directories
     *
     * @param string|null $tempPath
     * @return void
     */
    private function cleanupTempDirectory($tempPath)
    {
        if (!$tempPath) {
            return;
        }

        try {
            // Extract directory from path (e.g., temp/products/session_123)
            $directory = dirname($tempPath);

            // Check if directory is empty
            $files = Storage::disk('ftp')->files($directory);

            if (empty($files)) {
                // Delete the empty directory
                Storage::disk('ftp')->deleteDirectory($directory);
            }
        } catch (\Exception $e) {
            // Silently fail cleanup
            \Log::warning('Failed to cleanup temp directory: ' . $e->getMessage());
        }
    }
}
