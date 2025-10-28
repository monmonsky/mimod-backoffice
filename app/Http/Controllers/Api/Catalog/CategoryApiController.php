<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Catalog\CategoryRepository;
use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    protected $categoryRepo;
    protected $response;

    public function __construct(CategoryRepository $categoryRepo, Response $response)
    {
        $this->categoryRepo = $categoryRepo;
        $this->response = $response;
    }

    /**
     * Get all categories with pagination and filters
     */
    public function index(Request $request)
    {
        try {
            $filters = [
                'search' => $request->input('search'),
                'status' => $request->input('status'),
                'parent_id' => $request->input('parent_id')
            ];

            $perPage = $request->input('per_page', 15);
            $categories = $this->categoryRepo->getAllWithFilters($filters, $perPage);

            // Add product count for each category
            $items = $categories->items();
            $this->categoryRepo->attachProductCounts($items);

            // Get statistics
            $statistics = $this->categoryRepo->getStatistics();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Categories retrieved successfully')
                ->setData([$categories]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve categories: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }


    /**
     * Get category tree
     */
    public function tree()
    {
        try {
            $categories = $this->categoryRepo->getAllActive();

            $tree = [];
            $categoryMap = [];

            foreach ($categories as $category) {
                $categoryMap[$category->id] = $category;
                $categoryMap[$category->id]->children = [];
            }

            foreach ($categoryMap as $category) {
                if ($category->parent_id && isset($categoryMap[$category->parent_id])) {
                    $categoryMap[$category->parent_id]->children[] = $category;
                } elseif (!$category->parent_id) {
                    $tree[] = $category;
                }
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Category tree retrieved successfully')
                ->setData($tree);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve category tree: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get single category
     */
    public function show($id)
    {
        try {
            $category = $this->categoryRepo->findById($id);

            if (!$category) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Category not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Category retrieved successfully')
                ->setData($category);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve category: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get parent categories only
     */
    public function parents()
    {
        try {
            $categories = $this->categoryRepo->getParentCategories();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Parent categories retrieved successfully')
                ->setData($categories);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve parent categories: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get children of a category
     */
    public function children($parentId)
    {
        try {
            $children = $this->categoryRepo->getChildren($parentId);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Category children retrieved successfully')
                ->setData($children);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve category children: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Create new category
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:categories,slug',
                'description' => 'nullable|string',
                'parent_id' => 'nullable|exists:categories,id',
                'image' => 'nullable',
                'is_active' => 'nullable|boolean'
            ]);

            \DB::beginTransaction();

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs('categories', $filename, 'public');
                $validated['image'] = $path;
            }

            $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : true;
            $validated['sort_order'] = $this->categoryRepo->getMaxSortOrder() + 1;

            $category = $this->categoryRepo->create($validated);

            \DB::commit();

            // Log activity
            logActivity('create', 'Created category: ' . $category->name, 'category', (int)$category->id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('201')
                ->setMessage('Category created successfully')
                ->setData($category);

            return response()->json($this->response->generateResponse($result), 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('422')
                ->setMessage('Validation failed')
                ->setData(['errors' => $e->errors()]);

            return response()->json($this->response->generateResponse($result), 422);
        } catch (\Exception $e) {
            \DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to create category: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update category
     */
    public function update(Request $request, $id)
    {
        try {
            $category = $this->categoryRepo->findById($id);

            if (!$category) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Category not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:categories,slug,' . $id,
                'description' => 'nullable|string',
                'parent_id' => 'nullable|exists:categories,id',
                'image' => 'nullable',
                'is_active' => 'nullable|boolean'
            ]);

            // Prevent category from being its own parent
            if (isset($validated['parent_id']) && $validated['parent_id'] == $id) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Category cannot be its own parent')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            \DB::beginTransaction();

            // Handle image change (URL from upload API or file upload)
            if ($request->filled('image') || $request->hasFile('image')) {
                // Delete old image if exists
                if ($category->image) {
                    $oldImagePath = $category->image;

                    // Extract path from URL if contains full URL
                    if (str_contains($oldImagePath, '/storage/')) {
                        $oldImagePath = str_replace('/storage/', '', parse_url($oldImagePath, PHP_URL_PATH));
                    }

                    if (\Storage::disk('ftp')->exists($oldImagePath)) {
                        \Storage::disk('ftp')->delete($oldImagePath);
                    }
                }

                // If uploading new file directly
                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $filename = time() . '_' . $image->getClientOriginalName();
                    $path = $image->storeAs('categories', $filename, 'public');
                    $validated['image'] = env('FTP_URL') . '/' . $path;
                }
                // If using URL from upload API
                else if ($request->filled('image')) {
                    $validated['image'] = $request->image;
                }
            }

            $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : $category->is_active;

            $category = $this->categoryRepo->update($id, $validated);

            \DB::commit();

            // Log activity
            logActivity('update', 'Updated category: ' . $category->name, 'category', (int)$category->id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Category updated successfully')
                ->setData($category);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('422')
                ->setMessage('Validation failed')
                ->setData(['errors' => $e->errors()]);

            return response()->json($this->response->generateResponse($result), 422);
        } catch (\Exception $e) {
            \DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update category: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update category status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'is_active' => 'required|boolean'
            ]);

            $category = $this->categoryRepo->update($id, [
                'is_active' => $request->is_active
            ]);

            // Log activity
            $status = $request->is_active ? 'activated' : 'deactivated';
            logActivity('update', 'Category ' . $status . ': ' . $category->name, 'category', (int)$category->id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Category status updated successfully')
                ->setData($category);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update category status: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete category
     */
    public function destroy($id)
    {
        try {
            $category = $this->categoryRepo->findById($id);

            if (!$category) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Category not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $categoryName = $category->name;

            // Check if has children
            if ($this->categoryRepo->hasChildren($id)) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Cannot delete category with sub-categories. Please delete child categories first.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            // Check if has products
            if ($this->categoryRepo->hasProducts($id)) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Cannot delete category with assigned products. Please remove products first.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            // Delete image if exists
            if ($category->image) {
                // Extract path from URL if image contains full URL
                // e.g., http://127.0.0.1:8000/storage/categories/1760023261_68e7d2dda9ba7.png
                $imagePath = $category->image;

                // If image contains full URL, extract the path
                if (str_contains($imagePath, '/storage/')) {
                    $imagePath = str_replace('/storage/', '', parse_url($imagePath, PHP_URL_PATH));
                }

                // Delete file if exists
                if (\Storage::disk('ftp')->exists($imagePath)) {
                    \Storage::disk('ftp')->delete($imagePath);
                }
            }

            \DB::beginTransaction();
            $this->categoryRepo->delete($id);
            \DB::commit();

            // Log activity
            logActivity('delete', 'Deleted category: ' . $categoryName, 'category', (int)$id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Category deleted successfully')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to delete category: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
