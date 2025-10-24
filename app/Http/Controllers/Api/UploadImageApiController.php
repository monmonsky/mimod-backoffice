<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            $product = DB::table('products')->where('id', $productId)->first();
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
                $variant = DB::table('product_variants')->where('id', $variantId)->first();
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
            $existingCount = DB::table($table)->where($foreignKey, $foreignId)->count();
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
                $imageId = DB::table($table)->insertGetId([
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
                'images.*' => 'required|file|mimes:jpeg,jpg,png,gif,webp,svg,mp4,mov,avi,webm,mkv|max:102400', // Images + Videos, 100MB max
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
                $product = DB::table('products')->where('id', $productId)->first();
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
                $variant = DB::table('product_variants')->where('id', $variantId)->first();
                if (!$variant) {
                    $result = (new ResultBuilder())
                        ->setStatus(false)
                        ->setStatusCode('404')
                        ->setMessage('Variant not found');
                    return response()->json($this->response->generateResponse($result), 404);
                }

                $product = DB::table('products')->where('id', $variant->product_id)->first();
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
                $existingCount = DB::table('product_images')->where('product_id', $productId)->count();
            } elseif ($variantId && $type === 'variant') {
                $existingCount = DB::table('product_variant_images')->where('variant_id', $variantId)->count();
            }

            $sortOrder = $existingCount + 1;

            foreach ($request->file('images') as $index => $file) {
                // Detect media type from MIME type and extension
                $mimeType = $file->getMimeType();
                $extension = strtolower($file->getClientOriginalExtension());

                // Video extensions
                $videoExtensions = ['mp4', 'mov', 'avi', 'webm', 'mkv', 'flv', 'wmv'];

                // Detect media type
                $mediaType = 'image';
                if (str_starts_with($mimeType, 'video/') || in_array($extension, $videoExtensions)) {
                    $mediaType = 'video';
                }

                Log::info('Media upload detected', [
                    'filename' => $file->getClientOriginalName(),
                    'mime_type' => $mimeType,
                    'extension' => $extension,
                    'detected_type' => $mediaType
                ]);

                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $extension;

                // Store the file
                $path = $file->storeAs($directory, $filename, 'ftp');

                // Generate URL
                $url = $this->getFtpUrl($path);

                // Get file size
                $fileSize = $file->getSize();

                // Initialize video metadata
                $duration = null;
                $thumbnailUrl = null;

                // Get video metadata if it's a video
                if ($mediaType === 'video') {
                    // Try to get duration using getID3
                    try {
                        if (class_exists('\getID3')) {
                            $getID3 = new \getID3();
                            $fileInfo = $getID3->analyze($file->getRealPath());
                            if (isset($fileInfo['playtime_seconds'])) {
                                $duration = (int) $fileInfo['playtime_seconds'];
                            }

                            Log::info('Video metadata extracted', [
                                'duration' => $duration,
                                'filename' => $filename
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::warning('Could not get video duration: ' . $e->getMessage());
                    }

                    // Generate thumbnail for video using FFmpeg (if available)
                    try {
                        $thumbnailUrl = $this->generateVideoThumbnail($file, $directory);
                        if ($thumbnailUrl) {
                            Log::info('Video thumbnail generated', [
                                'thumbnail_url' => $thumbnailUrl,
                                'video_filename' => $filename
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::warning('Could not generate video thumbnail: ' . $e->getMessage());
                    }
                }

                $imageData = [
                    'url' => $url,
                    'path' => $path,
                    'filename' => $filename,
                    'temp' => $isTemp,
                    'media_type' => $mediaType,
                    'file_size' => $fileSize,
                ];

                if ($mediaType === 'video') {
                    if ($duration) {
                        $imageData['duration'] = $duration;
                    }
                    if ($thumbnailUrl) {
                        $imageData['thumbnail_url'] = $thumbnailUrl;
                    }
                }

                // If product_id or variant_id exists, save directly to database
                if ($productId && $type === 'product') {
                    $isPrimary = ($existingCount === 0 && $index === 0);

                    $dbData = [
                        'product_id' => $productId,
                        'url' => $url,
                        'alt_text' => $altText,
                        'is_primary' => $isPrimary,
                        'sort_order' => $sortOrder,
                        'media_type' => $mediaType,
                        'file_size' => $fileSize,
                        'created_at' => now(),
                    ];

                    if ($duration) {
                        $dbData['duration'] = $duration;
                    }

                    if ($thumbnailUrl) {
                        $dbData['thumbnail_url'] = $thumbnailUrl;
                    }

                    $imageId = DB::table('product_images')->insertGetId($dbData);

                    $imageData['id'] = $imageId;
                    $imageData['is_primary'] = $isPrimary;
                    $imageData['sort_order'] = $sortOrder;
                    $imageData['alt_text'] = $altText;

                    $sortOrder++;
                } elseif ($variantId && $type === 'variant') {
                    $isPrimary = ($existingCount === 0 && $index === 0);

                    $dbData = [
                        'variant_id' => $variantId,
                        'url' => $url,
                        'alt_text' => $altText,
                        'is_primary' => $isPrimary,
                        'sort_order' => $sortOrder,
                        'media_type' => $mediaType,
                        'file_size' => $fileSize,
                        'created_at' => now(),
                    ];

                    if ($duration) {
                        $dbData['duration'] = $duration;
                    }

                    if ($thumbnailUrl) {
                        $dbData['thumbnail_url'] = $thumbnailUrl;
                    }

                    $imageId = DB::table('product_variant_images')->insertGetId($dbData);

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
                'metadata' => 'nullable|array', // Optional metadata from temp upload
                'metadata.*.duration' => 'nullable|integer',
                'metadata.*.media_type' => 'nullable|string|in:image,video',
                'metadata.*.file_size' => 'nullable|integer',
                'metadata.*.thumbnail_url' => 'nullable|string',
            ]);

            $type = $request->type;
            $productId = $request->product_id;
            $variantId = $request->variant_id;
            $tempPaths = $request->temp_paths;
            $metadata = $request->metadata ?? []; // Metadata array from frontend

            // Get product info for folder naming
            $product = DB::table('products')->where('id', $productId)->first();
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
                $variant = DB::table('product_variants')->where('id', $variantId)->first();
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
            $existingCount = DB::table($table)->where($foreignKey, $foreignId)->count();
            $sortOrder = $existingCount + 1;

            $movedImages = [];

            foreach ($tempPaths as $index => $tempPath) {
                // Check if temp file exists
                if (!Storage::disk('ftp')->exists($tempPath)) {
                    Log::warning('Temp file not found', ['path' => $tempPath]);
                    continue; // Skip if file doesn't exist
                }

                // Get file info and metadata
                $filename = basename($tempPath);
                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $newFilename = time() . '_' . uniqid() . '.' . $extension;
                $newPath = $permanentDir . '/' . $newFilename;

                // Get metadata from frontend if provided (preferred)
                $metadataItem = $metadata[$index] ?? null;

                // Detect media type from extension (fallback if not provided)
                $videoExtensions = ['mp4', 'mov', 'avi', 'webm', 'mkv', 'flv', 'wmv'];
                $mediaType = $metadataItem['media_type'] ?? (in_array($extension, $videoExtensions) ? 'video' : 'image');

                // Get file size from metadata or FTP
                $fileSize = $metadataItem['file_size'] ?? Storage::disk('ftp')->size($tempPath);

                // Get duration from metadata (only available if provided by frontend)
                $duration = $metadataItem['duration'] ?? null;

                // Initialize thumbnail URL
                $thumbnailUrl = null;

                // If video, try to move thumbnail
                if ($mediaType === 'video') {
                    // First, check if thumbnail_url provided in metadata
                    $tempThumbnailUrl = $metadataItem['thumbnail_url'] ?? null;

                    if ($tempThumbnailUrl) {
                        // Extract path from URL
                        $tempThumbnailPath = $this->getPathFromUrl($tempThumbnailUrl);

                        Log::info('Processing thumbnail from metadata', [
                            'thumbnail_url' => $tempThumbnailUrl,
                            'thumbnail_path' => $tempThumbnailPath
                        ]);

                        // Check if thumbnail file exists
                        if (Storage::disk('ftp')->exists($tempThumbnailPath)) {
                            // Move thumbnail to permanent location
                            $thumbnailPermanentDir = $permanentDir . '/thumbnails';
                            $thumbnailFilename = basename($tempThumbnailPath);
                            $thumbnailPermanentPath = $thumbnailPermanentDir . '/' . $thumbnailFilename;

                            Storage::disk('ftp')->move($tempThumbnailPath, $thumbnailPermanentPath);
                            $thumbnailUrl = $this->getFtpUrl($thumbnailPermanentPath);

                            Log::info('Moved video thumbnail from metadata', [
                                'from' => $tempThumbnailPath,
                                'to' => $thumbnailPermanentPath,
                                'new_url' => $thumbnailUrl
                            ]);
                        } else {
                            Log::warning('Thumbnail file not found in FTP', [
                                'thumbnail_path' => $tempThumbnailPath
                            ]);
                        }
                    } else {
                        // Fallback: Try to find thumbnail by searching temp directory
                        $tempDir = dirname($tempPath) . '/thumbnails/';
                        if (Storage::disk('ftp')->exists($tempDir)) {
                            $thumbnailFiles = Storage::disk('ftp')->files($tempDir);
                            $baseFilename = pathinfo($filename, PATHINFO_FILENAME);

                            foreach ($thumbnailFiles as $thumbFile) {
                                if (strpos(basename($thumbFile), $baseFilename) !== false) {
                                    // Move thumbnail to permanent location
                                    $thumbnailPermanentDir = $permanentDir . '/thumbnails';
                                    $thumbnailFilename = basename($thumbFile);
                                    $thumbnailPermanentPath = $thumbnailPermanentDir . '/' . $thumbnailFilename;

                                    Storage::disk('ftp')->move($thumbFile, $thumbnailPermanentPath);
                                    $thumbnailUrl = $this->getFtpUrl($thumbnailPermanentPath);

                                    Log::info('Moved video thumbnail (fallback search)', [
                                        'from' => $thumbFile,
                                        'to' => $thumbnailPermanentPath
                                    ]);
                                    break;
                                }
                            }
                        }
                    }
                }

                // Move file from temp to permanent location
                Storage::disk('ftp')->move($tempPath, $newPath);

                // Generate URL
                $url = $this->getFtpUrl($newPath);

                // Determine if this is primary
                $isPrimary = ($existingCount === 0 && $index === 0);

                Log::info('Moving media from temp to permanent', [
                    'from' => $tempPath,
                    'to' => $newPath,
                    'media_type' => $mediaType,
                    'file_size' => $fileSize,
                    'has_thumbnail' => $thumbnailUrl !== null
                ]);

                // Insert to database with full metadata
                $dbData = [
                    $foreignKey => $foreignId,
                    'url' => $url,
                    'alt_text' => null,
                    'is_primary' => $isPrimary,
                    'sort_order' => $sortOrder,
                    'media_type' => $mediaType,
                    'file_size' => $fileSize,
                    'created_at' => now(),
                ];

                if ($thumbnailUrl) {
                    $dbData['thumbnail_url'] = $thumbnailUrl;
                }

                if ($duration) {
                    $dbData['duration'] = $duration;
                }

                $imageId = DB::table($table)->insertGetId($dbData);

                $movedImageData = [
                    'id' => $imageId,
                    'url' => $url,
                    'path' => $newPath,
                    'is_primary' => $isPrimary,
                    'sort_order' => $sortOrder,
                    'media_type' => $mediaType,
                    'file_size' => $fileSize,
                ];

                if ($thumbnailUrl) {
                    $movedImageData['thumbnail_url'] = $thumbnailUrl;
                }

                if ($duration) {
                    $movedImageData['duration'] = $duration;
                }

                $movedImages[] = $movedImageData;

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
            $image = DB::table('product_images')->where('id', $id)->first();

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
            DB::table('product_images')->where('id', $id)->delete();

            // If deleted image was primary, set the next image (by sort_order) as primary
            if ($image->is_primary) {
                // Get the next image after the deleted one by sort_order
                $newPrimary = DB::table('product_images')
                    ->where('product_id', $image->product_id)
                    ->where('sort_order', '>', $image->sort_order)
                    ->orderBy('sort_order', 'asc')
                    ->first();

                // If no image after, get the first one
                if (!$newPrimary) {
                    $newPrimary = DB::table('product_images')
                        ->where('product_id', $image->product_id)
                        ->orderBy('sort_order', 'asc')
                        ->first();
                }

                if ($newPrimary) {
                    DB::table('product_images')
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
            $image = DB::table('product_images')->where('id', $id)->first();

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
            DB::table('product_images')
                ->where('product_id', $image->product_id)
                ->update(['is_primary' => false]);

            // Set this image as primary
            DB::table('product_images')
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
            Log::warning('Failed to cleanup temp directory: ' . $e->getMessage());
        }
    }

    /**
     * Upload media (image or video)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadMedia(Request $request)
    {
        try {
            $validated = $request->validate([
                'file' => 'required|file|mimes:jpeg,jpg,png,gif,webp,svg,mp4,mov,avi,webm|max:102400', // 100MB max
                'type' => 'required|string|in:' . implode(',', array_keys($this->allowedTypes)),
                'media_type' => 'nullable|string|in:image,video',
                'path' => 'nullable|string',
                'thumbnail' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120', // 5MB thumbnail
            ]);

            if (!$request->hasFile('file')) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('No file provided')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $type = $request->type;
            $file = $request->file('file');
            $mimeType = $file->getMimeType();

            // Auto-detect media type if not provided
            $mediaType = $request->input('media_type');
            if (!$mediaType) {
                $mediaType = str_starts_with($mimeType, 'video/') ? 'video' : 'image';
            }

            // Determine directory path
            $directory = $request->filled('path')
                ? $request->path
                : $this->allowedTypes[$type];

            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Store the file
            $path = $file->storeAs($directory, $filename, 'ftp');

            // Generate URL
            $url = $this->getFtpUrl($path);

            // Prepare response data
            $responseData = [
                'url' => $url,
                'path' => $path,
                'filename' => $filename,
                'type' => $type,
                'media_type' => $mediaType,
                'file_size' => $file->getSize(),
                'mime_type' => $mimeType,
            ];

            // Handle thumbnail for video
            if ($mediaType === 'video' && $request->hasFile('thumbnail')) {
                $thumbnail = $request->file('thumbnail');
                $thumbnailFilename = 'thumb_' . time() . '_' . uniqid() . '.' . $thumbnail->getClientOriginalExtension();
                $thumbnailPath = $thumbnail->storeAs($directory, $thumbnailFilename, 'ftp');
                $responseData['thumbnail_url'] = $this->getFtpUrl($thumbnailPath);
                $responseData['thumbnail_path'] = $thumbnailPath;
            }

            // Get video duration if available (requires ffmpeg or similar)
            if ($mediaType === 'video') {
                $responseData['duration'] = $this->getVideoDuration($file->getRealPath());
            }

            // Log activity
            logActivity('create', "Uploaded {$mediaType} for {$type}: {$filename}", 'upload', null);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage(ucfirst($mediaType) . ' uploaded successfully')
                ->setData($responseData);

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
                ->setMessage('Failed to upload media: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get video duration in seconds
     * Note: Requires getID3 library or FFmpeg
     *
     * @param string $filePath
     * @return int|null
     */
    private function getVideoDuration($filePath)
    {
        try {
            // Check if getID3 is available
            if (class_exists('\getID3')) {
                $getID3 = new \getID3();
                $fileInfo = $getID3->analyze($filePath);
                return isset($fileInfo['playtime_seconds']) ? (int) $fileInfo['playtime_seconds'] : null;
            }

            // Alternative: Use FFmpeg if available
            if (function_exists('exec')) {
                $command = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 " . escapeshellarg($filePath);
                $output = exec($command);
                if ($output && is_numeric($output)) {
                    return (int) $output;
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::warning('Failed to get video duration: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate thumbnail from video using FFmpeg
     *
     * @param \Illuminate\Http\UploadedFile $videoFile
     * @param string $directory Directory where video is stored
     * @return string|null Thumbnail URL or null if failed
     */
    private function generateVideoThumbnail($videoFile, $directory)
    {
        try {
            // Check if FFmpeg is available
            $ffmpegPath = env('FFMPEG_PATH', 'ffmpeg');
            $checkCommand = "which {$ffmpegPath} 2>&1";
            $ffmpegExists = exec($checkCommand);

            if (empty($ffmpegExists)) {
                Log::info('FFmpeg not found, skipping thumbnail generation');
                return null;
            }

            // Get video file path
            $videoPath = $videoFile->getRealPath();

            // Generate thumbnail filename
            $thumbnailFilename = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME) . '_thumb_' . time() . '.jpg';
            $thumbnailTempPath = sys_get_temp_dir() . '/' . $thumbnailFilename;

            // FFmpeg command to extract frame at 1 second
            // -ss 1: seek to 1 second
            // -i: input file
            // -vframes 1: extract 1 frame
            // -q:v 2: quality (2 is high quality)
            $command = sprintf(
                '%s -ss 1 -i %s -vframes 1 -q:v 2 %s 2>&1',
                $ffmpegPath,
                escapeshellarg($videoPath),
                escapeshellarg($thumbnailTempPath)
            );

            Log::info('Generating video thumbnail', [
                'command' => $command,
                'video_path' => $videoPath,
                'thumbnail_path' => $thumbnailTempPath
            ]);

            // Execute FFmpeg command
            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);

            if ($returnCode !== 0 || !file_exists($thumbnailTempPath)) {
                Log::warning('FFmpeg thumbnail generation failed', [
                    'return_code' => $returnCode,
                    'output' => implode("\n", $output)
                ]);
                return null;
            }

            // Upload thumbnail to FTP
            $thumbnailDirectory = $directory . '/thumbnails';
            $thumbnailStoragePath = $thumbnailDirectory . '/' . $thumbnailFilename;

            // Read thumbnail file and upload to FTP
            $thumbnailContent = file_get_contents($thumbnailTempPath);
            Storage::disk('ftp')->put($thumbnailStoragePath, $thumbnailContent);

            // Clean up temp file
            @unlink($thumbnailTempPath);

            // Generate thumbnail URL
            $thumbnailUrl = $this->getFtpUrl($thumbnailStoragePath);

            Log::info('Video thumbnail generated successfully', [
                'thumbnail_url' => $thumbnailUrl
            ]);

            return $thumbnailUrl;
        } catch (\Exception $e) {
            Log::error('Failed to generate video thumbnail: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return null;
        }
    }
}
