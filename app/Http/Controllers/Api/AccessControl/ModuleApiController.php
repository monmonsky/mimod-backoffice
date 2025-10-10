<?php

namespace App\Http\Controllers\Api\AccessControl;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Contracts\AccessControl\ModuleRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ModuleApiController extends Controller
{
    protected $moduleRepo;
    protected $response;

    public function __construct(ModuleRepositoryInterface $moduleRepo, Response $response)
    {
        $this->moduleRepo = $moduleRepo;
        $this->response = $response;
    }

    /**
     * Get all modules with filters
     */
    public function index(Request $request)
    {
        try {
            $query = $this->moduleRepo->query();

            // Filter by parent
            if ($request->has('parent_id')) {
                if ($request->parent_id === 'null') {
                    $query->whereNull('parent_id');
                } else {
                    $query->where('parent_id', $request->parent_id);
                }
            }

            // Filter by group
            if ($request->has('group_name')) {
                $query->where('group_name', $request->group_name);
            }

            // Filter by visibility
            if ($request->has('is_visible')) {
                $query->where('is_visible', $request->is_visible);
            }

            // Filter by active status
            if ($request->has('is_active')) {
                $query->where('is_active', $request->is_active);
            }

            // Search by name
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'ILIKE', '%' . $search . '%')
                      ->orWhere('display_name', 'ILIKE', '%' . $search . '%');
                });
            }

            $perPage = $request->get('per_page', 50);
            $modules = $query->orderBy('sort_order')->paginate($perPage);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Modules retrieved successfully')
                ->setData($modules);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve modules: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get modules with parent-child hierarchy
     */
    public function tree()
    {
        try {
            $modules = $this->moduleRepo->getAllWithChildren();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Module tree retrieved successfully')
                ->setData($modules);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve module tree: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get modules grouped by group_name, with children under parent
     */
    public function grouped()
    {
        try {
            $query = $this->moduleRepo->query();

            // Filter active and visible modules only
            $query->where('is_active', true)
                  ->where('is_visible', true);

            $modules = $query->orderBy('sort_order')->get();

            // Separate modules with group_name and without
            $withGroup = collect($modules)->filter(function ($module) {
                return !empty($module->group_name);
            });

            $withoutGroup = collect($modules)->filter(function ($module) {
                return empty($module->group_name);
            });

            // Group modules with group_name
            $grouped = $withGroup->groupBy('group_name')->map(function ($items, $groupName) use ($withoutGroup) {
                // Get parent modules in this group
                $parents = $items->filter(function ($module) {
                    return $module->parent_id === null;
                });

                // For each parent, attach children (modules without group_name but with this parent_id)
                $groupModules = $parents->map(function ($parent) use ($withoutGroup, $items) {
                    $parentArray = (array) $parent;

                    // Get children from same group
                    $childrenFromGroup = $items->filter(function ($module) use ($parent) {
                        return $module->parent_id === $parent->id;
                    })->values()->toArray();

                    // Get children without group_name but with this parent_id
                    $childrenWithoutGroup = $withoutGroup->filter(function ($module) use ($parent) {
                        return $module->parent_id === $parent->id;
                    })->values()->toArray();

                    // Merge children
                    $parentArray['children'] = array_merge($childrenFromGroup, $childrenWithoutGroup);

                    return $parentArray;
                })->values()->toArray();

                return [
                    'group_name' => $groupName,
                    'modules' => $groupModules
                ];
            })->values();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Grouped modules retrieved successfully')
                ->setData($grouped);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve grouped modules: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get modules by specific group name
     */
    public function byGroup($groupName)
    {
        try {
            $query = $this->moduleRepo->query();

            $query->where('group_name', $groupName)
                  ->where('is_active', true)
                  ->where('is_visible', true);

            $modules = $query->orderBy('sort_order')->get();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Modules retrieved successfully')
                ->setData([
                    'group_name' => $groupName,
                    'modules' => $modules
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve modules by group: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Reorder modules
     */
    public function reorder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'modules' => 'required|array|min:1',
                'modules.*.id' => 'required|exists:modules,id',
                'modules.*.sort_order' => 'required|integer|min:0',
                'modules.*.group_name' => 'nullable|string',
                'modules.*.parent_id' => 'nullable|exists:modules,id',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $modules = $request->modules;

            // Update each module's sort_order
            foreach ($modules as $moduleData) {
                $updateData = [
                    'sort_order' => $moduleData['sort_order']
                ];

                // Update group_name if provided
                if (isset($moduleData['group_name'])) {
                    $updateData['group_name'] = $moduleData['group_name'];
                }

                // Update parent_id if provided
                if (isset($moduleData['parent_id'])) {
                    $updateData['parent_id'] = $moduleData['parent_id'];
                }

                $this->moduleRepo->update($moduleData['id'], $updateData);
            }

            logActivity('update', "Reordered " . count($modules) . " modules", 'module', null);

            // Get updated modules
            $updatedModules = $this->moduleRepo->query()
                ->whereIn('id', collect($modules)->pluck('id')->toArray())
                ->orderBy('sort_order')
                ->get();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Modules reordered successfully')
                ->setData($updatedModules);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to reorder modules: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get single module by ID
     */
    public function show($id)
    {
        try {
            $module = $this->moduleRepo->findById($id);

            if (!$module) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Module not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Module retrieved successfully')
                ->setData($module);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve module: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Create new module
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100|unique:modules,name',
                'display_name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'icon' => 'nullable|string|max:100',
                'parent_id' => 'nullable|exists:modules,id',
                'group_name' => 'nullable|string|max:50',
                'route' => 'nullable|string|max:255',
                'permission_name' => 'nullable|string|max:255',
                'component' => 'nullable|string|max:255',
                'sort_order' => 'nullable|integer',
                'is_active' => 'nullable|boolean',
                'is_visible' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $module = $this->moduleRepo->create($validator->validated());

            logActivity('create', "Created module: {$module->name}", 'module', (int)$module->id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('201')
                ->setMessage('Module created successfully')
                ->setData($module);

            return response()->json($this->response->generateResponse($result), 201);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to create module: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update module
     */
    public function update(Request $request, $id)
    {
        try {
            $module = $this->moduleRepo->findById($id);

            if (!$module) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Module not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:100|unique:modules,name,' . $id,
                'display_name' => 'sometimes|required|string|max:100',
                'description' => 'nullable|string',
                'icon' => 'nullable|string|max:100',
                'parent_id' => 'nullable|exists:modules,id',
                'group_name' => 'nullable|string|max:50',
                'route' => 'nullable|string|max:255',
                'permission_name' => 'nullable|string|max:255',
                'component' => 'nullable|string|max:255',
                'sort_order' => 'nullable|integer',
                'is_active' => 'nullable|boolean',
                'is_visible' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $updatedModule = $this->moduleRepo->update($id, $validator->validated());

            logActivity('update', "Updated module: {$updatedModule->name}", 'module', (int)$id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Module updated successfully')
                ->setData($updatedModule);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update module: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete module
     */
    public function destroy($id)
    {
        try {
            $module = $this->moduleRepo->findById($id);

            if (!$module) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Module not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $this->moduleRepo->delete($id);

            logActivity('delete', "Deleted module: {$module->name}", 'module', (int)$id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Module deleted successfully')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to delete module: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Toggle module visibility
     */
    public function toggleVisible($id)
    {
        try {
            $module = $this->moduleRepo->findById($id);

            if (!$module) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Module not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $newStatus = !$module->is_visible;
            $updatedModule = $this->moduleRepo->update($id, ['is_visible' => $newStatus]);

            logActivity('update', "Changed module visibility to: " . ($newStatus ? 'visible' : 'hidden'), 'module', (int)$id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Module visibility updated successfully')
                ->setData($updatedModule);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to toggle module visibility: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Toggle module active status
     */
    public function toggleActive($id)
    {
        try {
            $module = $this->moduleRepo->findById($id);

            if (!$module) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Module not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $newStatus = !$module->is_active;
            $updatedModule = $this->moduleRepo->update($id, ['is_active' => $newStatus]);

            logActivity('update', "Changed module status to: " . ($newStatus ? 'active' : 'inactive'), 'module', (int)$id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Module status updated successfully')
                ->setData($updatedModule);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to toggle module status: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
