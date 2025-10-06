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
        $groupedModules = $this->moduleCache->getGroupedModules();
        $statistics = $this->moduleCache->getStatistics();

        // Get all modules (cached) for the blade to use
        $allModules = $this->moduleCache->getAll();

        return view('pages.access-control.modules.index', compact('groupedModules', 'statistics', 'allModules'));
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

            // Log activity
            logActivity('create', 'Created new module: ' . $module->display_name, 'Module', $module->id);

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
                'is_active' => 'nullable|boolean',
                'is_visible' => 'nullable|boolean',
            ]);

            // Remove sort_order from validated data - sort_order should only be changed via drag & drop
            unset($validated['sort_order']);

            $module = $this->moduleRepo->update($id, $validated);

            // Clear cache (will be lazy loaded on next request)
            $this->moduleCache->clearCache();

            // Log activity
            logActivity('update', 'Updated module: ' . $module->display_name, 'Module', $module->id);

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
            $module = $this->moduleRepo->findById($id);
            $moduleName = $module->display_name;

            $this->moduleRepo->delete($id);

            // Clear cache (will be lazy loaded on next request)
            $this->moduleCache->clearCache();

            // Log activity
            logActivity('delete', 'Deleted module: ' . $moduleName, 'Module', $id);

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

            // Log activity
            $status = $module->is_active ? 'activated' : 'deactivated';
            logActivity('update', 'Module ' . $status . ': ' . $module->display_name, 'Module', $module->id);

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

            // Log activity
            $status = $module->is_visible ? 'shown' : 'hidden';
            logActivity('update', 'Module visibility ' . $status . ': ' . $module->display_name, 'Module', $module->id);

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

            \Log::info('Updating module order', ['count' => count($order), 'data' => $order]);

            $updatedCount = 0;
            foreach ($order as $item) {
                if (isset($item['id']) && isset($item['sort_order'])) {
                    $this->moduleRepo->updateSortOrder($item['id'], $item['sort_order']);
                    $updatedCount++;
                }
            }

            \Log::info('Module order updated', ['updated_count' => $updatedCount]);

            // Clear cache (will be lazy loaded on next request)
            $this->moduleCache->clearCache();

            // Log activity
            logActivity('update', 'Updated module order (' . $updatedCount . ' modules)', 'Module');

            return response()->json([
                'success' => true,
                'message' => 'Module order updated successfully',
                'updated_count' => $updatedCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to update module order', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update module group order
     * When a group is moved, all modules in that group are updated
     */
    public function updateGroupOrder(Request $request)
    {
        try {
            $groups = $request->input('groups');

            if (!$groups || !is_array($groups)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid group data'
                ], 422);
            }

            // Update sort_order for all modules in each group
            foreach ($groups as $index => $groupData) {
                if (isset($groupData['group_name'])) {
                    $this->moduleRepo->updateGroupSortOrder($groupData['group_name'], ($index + 1) * 10);
                }
            }

            // Clear cache (will be lazy loaded on next request)
            $this->moduleCache->clearCache();

            // Log activity
            logActivity('update', 'Updated module group ordering', 'Module');

            return response()->json([
                'success' => true,
                'message' => 'Module group order updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update group order: ' . $e->getMessage()
            ], 500);
        }
    }
}
