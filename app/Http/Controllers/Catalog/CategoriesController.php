<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Catalog\CategoryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{
    protected $categoryRepo;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepo = $categoryRepository;
    }

    /**
     * Display categories page
     */
    public function categories(Request $request)
    {
        // Get filter parameters
        $search = $request->input('search');
        $status = $request->input('status');
        $parent = $request->input('parent');

        // Build query
        $query = DB::table('categories');

        // Apply search filter
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Apply status filter
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        // Apply parent filter
        if ($parent === '0') {
            $query->whereNull('parent_id');
        } elseif ($parent) {
            $query->where('parent_id', $parent);
        }

        // Order by sort_order and get with pagination
        $categories = $query->orderBy('sort_order', 'asc')
                           ->orderBy('name', 'asc')
                           ->paginate(15)
                           ->appends($request->query());

        // Add product count for each category
        foreach ($categories as $category) {
            $category->product_count = DB::table('product_categories')
                ->where('category_id', $category->id)
                ->count();
        }

        $statistics = $this->categoryRepo->getStatistics();
        $parents = $this->categoryRepo->getParents();

        return view('pages.catalog.categories.categories', compact('categories', 'statistics', 'parents'));
    }

    /**
     * Get category tree (for API/AJAX)
     */
    public function getTree()
    {
        try {
            $tree = $this->categoryRepo->getTree();

            return response()->json([
                'success' => true,
                'data' => $tree
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve category tree: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store new category
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'nullable|string|unique:categories,slug|max:255',
                'parent_id' => 'nullable|exists:categories,id',
                'image' => 'nullable|image|max:2048',
                'description' => 'nullable|string',
                'sort_order' => 'nullable|integer',
                'is_active' => 'nullable|boolean',
            ]);

            // Auto-generate slug if not provided
            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . Str::slug($validated['name']) . '.' . $image->extension();
                $imagePath = $image->storeAs('categories', $imageName, 'public');
                $validated['image'] = $imagePath;
            }

            // Set defaults
            $validated['is_active'] = $validated['is_active'] ?? true;
            $validated['sort_order'] = $validated['sort_order'] ?? 0;

            DB::beginTransaction();

            $category = $this->categoryRepo->create($validated);

            DB::commit();

            // Log activity
            logActivity('create', 'Created new category: ' . $category->name, 'Category', $category->id);

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update category
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:categories,slug,' . $id,
                'parent_id' => 'nullable|exists:categories,id',
                'image' => 'nullable|image|max:2048',
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ]);

            // Prevent circular reference
            if (isset($validated['parent_id']) && $validated['parent_id'] == $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category cannot be parent of itself'
                ], 422);
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                $oldCategory = $this->categoryRepo->findById($id);
                if ($oldCategory->image && \Storage::disk('public')->exists($oldCategory->image)) {
                    \Storage::disk('public')->delete($oldCategory->image);
                }

                $image = $request->file('image');
                $imageName = time() . '_' . Str::slug($validated['name']) . '.' . $image->extension();
                $imagePath = $image->storeAs('categories', $imageName, 'public');
                $validated['image'] = $imagePath;
            }

            DB::beginTransaction();

            $category = $this->categoryRepo->update($id, $validated);

            DB::commit();

            // Log activity
            logActivity('update', 'Updated category: ' . $category->name, 'Category', $category->id);

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => $category
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete category
     */
    public function destroy($id)
    {
        try {
            $category = $this->categoryRepo->findById($id);
            $categoryName = $category->name;

            // Check if has children
            if ($this->categoryRepo->hasChildren($id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category with sub-categories. Please delete child categories first.'
                ], 422);
            }

            // Check if has products
            if ($this->categoryRepo->hasProducts($id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category with assigned products. Please remove products first.'
                ], 422);
            }

            // Delete image if exists
            if ($category->image && \Storage::disk('public')->exists($category->image)) {
                \Storage::disk('public')->delete($category->image);
            }

            DB::beginTransaction();

            $this->categoryRepo->delete($id);

            DB::commit();

            // Log activity
            logActivity('delete', 'Deleted category: ' . $categoryName, 'Category', $id);

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        try {
            $category = $this->categoryRepo->toggleActive($id);

            // Log activity
            $status = $category->is_active ? 'activated' : 'deactivated';
            logActivity('update', 'Category ' . $status . ': ' . $category->name, 'Category', $category->id);

            return response()->json([
                'success' => true,
                'message' => 'Category status updated successfully',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update category order (drag & drop)
     */
    public function updateOrder(Request $request)
    {
        try {
            $order = $request->input('order');

            if (!$order || !is_array($order)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid order data'
                ], 422);
            }

            DB::beginTransaction();

            $updatedCount = 0;
            foreach ($order as $item) {
                if (isset($item['id']) && isset($item['sort_order'])) {
                    $this->categoryRepo->updateSortOrder($item['id'], $item['sort_order']);
                    $updatedCount++;
                }
            }

            DB::commit();

            // Log activity
            logActivity('update', 'Updated category order (' . $updatedCount . ' categories)', 'Category');

            return response()->json([
                'success' => true,
                'message' => 'Category order updated successfully',
                'updated_count' => $updatedCount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete category image
     */
    public function deleteImage($id)
    {
        try {
            $category = $this->categoryRepo->findById($id);

            if (!$category->image) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category has no image'
                ], 422);
            }

            // Delete image file
            if (\Storage::disk('public')->exists($category->image)) {
                \Storage::disk('public')->delete($category->image);
            }

            // Update database
            $this->categoryRepo->update($id, ['image' => null]);

            // Log activity
            logActivity('update', 'Deleted image for category: ' . $category->name, 'Category', $category->id);

            return response()->json([
                'success' => true,
                'message' => 'Category image deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image: ' . $e->getMessage()
            ], 500);
        }
    }
}
