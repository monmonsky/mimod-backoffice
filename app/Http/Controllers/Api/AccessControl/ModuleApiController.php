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

            logActivity('create', 'module', $module->id, "Created module: {$module->name}");

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

            logActivity('update', 'module', $id, "Updated module: {$updatedModule->name}");

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

            logActivity('delete', 'module', $id, "Deleted module: {$module->name}");

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

            logActivity('update', 'module', $id, "Changed module visibility to: " . ($newStatus ? 'visible' : 'hidden'));

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

            logActivity('update', 'module', $id, "Changed module status to: " . ($newStatus ? 'active' : 'inactive'));

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
