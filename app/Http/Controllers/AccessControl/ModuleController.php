<?php

namespace App\Http\Controllers\AccessControl;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\AccessControl\ModuleRepositoryInterface;
use App\Repositories\Cache\ModuleCacheRepository;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    protected $moduleRepo;
    protected $moduleCache;

    public function __construct(
        ModuleRepositoryInterface $moduleRepository,
        ModuleCacheRepository $moduleCache
    ) {
        $this->moduleRepo = $moduleRepository;
        $this->moduleCache = $moduleCache;
    }

    /**
     * Display module list page
     */
    public function index()
    {
        // Use cache for better performance
        $modules = $this->moduleCache->getAllWithChildren();
        $statistics = $this->moduleRepo->getStatistics();
        return view('pages.access-control.modules.index', compact('modules', 'statistics'));
    }

    /**
     * Show create module page
     */
    public function create()
    {
        // Use cache for better performance
        $parents = $this->moduleCache->getParents();
        return view('pages.access-control.modules.create', compact('parents'));
    }

    /**
     * Show edit module page
     */
    public function edit($id)
    {
        // Use cache for finding module (lazy loaded if not in cache)
        $module = $this->moduleCache->findById($id);
        $parents = $this->moduleCache->getParents();
        return view('pages.access-control.modules.edit', compact('module', 'parents'));
    }

    /**
     * Get all modules (API)
     */
    public function getAll()
    {
        try {
            // Use cache for better performance
            $modules = $this->moduleCache->getAllWithChildren();

            return response()->json([
                'success' => true,
                'message' => 'Modules retrieved successfully',
                'data' => $modules
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve modules: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store new module
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|unique:modules,name|max:255',
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'icon' => 'nullable|string|max:255',
                'parent_id' => 'nullable|exists:modules,id',
                'route' => 'nullable|string|max:255',
                'component' => 'nullable|string|max:255',
                'sort_order' => 'nullable|integer',
                'is_active' => 'nullable|boolean',
                'is_visible' => 'nullable|boolean',
            ]);

            $module = $this->moduleRepo->create($validated);

            // Clear cache (will be lazy loaded on next request)
            $this->moduleCache->clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Module created successfully',
                'data' => $module
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create module: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update module
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:modules,name,' . $id,
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'icon' => 'nullable|string|max:255',
                'parent_id' => 'nullable|exists:modules,id',
                'route' => 'nullable|string|max:255',
                'component' => 'nullable|string|max:255',
                'sort_order' => 'nullable|integer',
                'is_active' => 'nullable|boolean',
                'is_visible' => 'nullable|boolean',
            ]);

            $module = $this->moduleRepo->update($id, $validated);

            // Clear cache (will be lazy loaded on next request)
            $this->moduleCache->clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Module updated successfully',
                'data' => $module
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update module: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete module
     */
    public function destroy($id)
    {
        try {
            $this->moduleRepo->delete($id);

            // Clear cache (will be lazy loaded on next request)
            $this->moduleCache->clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Module deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete module: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        try {
            $module = $this->moduleRepo->toggleActive($id);

            // Clear cache (will be lazy loaded on next request)
            $this->moduleCache->clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Module status updated successfully',
                'data' => $module
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle visible status
     */
    public function toggleVisible($id)
    {
        try {
            $module = $this->moduleRepo->toggleVisible($id);

            // Clear cache (will be lazy loaded on next request)
            $this->moduleCache->clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Module visibility updated successfully',
                'data' => $module
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update visibility: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update module order
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

            foreach ($order as $item) {
                if (isset($item['id']) && isset($item['sort_order'])) {
                    $this->moduleRepo->updateSortOrder($item['id'], $item['sort_order']);
                }
            }

            // Clear cache (will be lazy loaded on next request)
            $this->moduleCache->clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Module order updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order: ' . $e->getMessage()
            ], 500);
        }
    }
}
