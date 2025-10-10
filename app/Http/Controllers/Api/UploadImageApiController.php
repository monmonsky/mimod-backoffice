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
     * Upload image
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        try {
            $validated = $request->validate([
                'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp,svg|max:5120', // 5MB max
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
            $path = $image->storeAs($directory, $filename, 'public');

            // Generate URL
            $url = url('storage/' . $path);

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
                'images.*' => 'required|image|mimes:jpeg,jpg,png,gif,webp,svg|max:5120',
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
                $path = $image->storeAs($directory, $filename, 'public');

                // Generate URL
                $url = url('storage/' . $path);

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
            if (!Storage::disk('public')->exists($path)) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Image not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Delete the file
            Storage::disk('public')->delete($path);

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
}
