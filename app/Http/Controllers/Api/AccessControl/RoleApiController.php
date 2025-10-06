<?php

namespace App\Http\Controllers\Api\AccessControl;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RoleApiController extends Controller
{
    protected $roleRepo;
    protected $permissionRepo;
    protected $response;

    public function __construct(
        RoleRepositoryInterface $roleRepo,
        PermissionRepositoryInterface $permissionRepo,
        Response $response
    ) {
        $this->roleRepo = $roleRepo;
        $this->permissionRepo = $permissionRepo;
        $this->response = $response;
    }

    /**
     * Get all roles with filters
     */
    public function index(Request $request)
    {
        try {
            $query = $this->roleRepo->query();

            // Filter by status
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

            // Pagination
            $perPage = $request->get('per_page', 15);
            $roles = $query->orderBy('priority', 'desc')->paginate($perPage);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Roles retrieved successfully')
                ->setData($roles);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve roles: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get single role by ID with permissions
     */
    public function show($id)
    {
        try {
            $role = $this->roleRepo->findById($id);

            if (!$role) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Role not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Get role permissions
            $permissions = $this->roleRepo->getRolePermissions($id);
            $role->permissions = $permissions;

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Role retrieved successfully')
                ->setData($role);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve role: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Create new role
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100|unique:roles,name',
                'display_name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'priority' => 'nullable|integer|min:1|max:99',
                'is_active' => 'nullable|boolean',
                'permission_ids' => 'nullable|array',
                'permission_ids.*' => 'exists:permissions,id',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            DB::beginTransaction();

            $data = $validator->validated();
            $permissionIds = $data['permission_ids'] ?? [];
            unset($data['permission_ids']);

            $data['is_system'] = false;

            $role = $this->roleRepo->create($data);

            // Sync permissions
            if (!empty($permissionIds)) {
                $this->roleRepo->syncPermissions($role->id, $permissionIds);
            }

            DB::commit();

            logActivity('create', 'role', $role->id, "Created role: {$role->name}");

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('201')
                ->setMessage('Role created successfully')
                ->setData($role);

            return response()->json($this->response->generateResponse($result), 201);
        } catch (\Exception $e) {
            DB::rollBack();

            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to create role: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update role
     */
    public function update(Request $request, $id)
    {
        try {
            $role = $this->roleRepo->findById($id);

            if (!$role) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Role not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Prevent editing system roles
            if ($role->is_system) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage('Cannot edit system roles')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 400);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:100|unique:roles,name,' . $id,
                'display_name' => 'sometimes|required|string|max:100',
                'description' => 'nullable|string',
                'priority' => 'nullable|integer|min:1|max:99',
                'is_active' => 'nullable|boolean',
                'permission_ids' => 'nullable|array',
                'permission_ids.*' => 'exists:permissions,id',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            DB::beginTransaction();

            $data = $validator->validated();
            $permissionIds = $data['permission_ids'] ?? null;
            unset($data['permission_ids']);

            $updatedRole = $this->roleRepo->update($id, $data);

            // Sync permissions if provided
            if ($permissionIds !== null) {
                $this->roleRepo->syncPermissions($id, $permissionIds);
            }

            DB::commit();

            logActivity('update', 'role', $id, "Updated role: {$updatedRole->name}");

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Role updated successfully')
                ->setData($updatedRole);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            DB::rollBack();

            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update role: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete role
     */
    public function destroy($id)
    {
        try {
            $role = $this->roleRepo->findById($id);

            if (!$role) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Role not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Prevent deleting system roles
            if ($role->is_system) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage('Cannot delete system roles')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 400);
            }

            // Check if role has users
            $userCount = DB::table('users')->where('role_id', $id)->count();
            if ($userCount > 0) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage("Cannot delete role with {$userCount} assigned users")
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 400);
            }

            $this->roleRepo->delete($id);

            logActivity('delete', 'role', $id, "Deleted role: {$role->name}");

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Role deleted successfully')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to delete role: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Toggle role active status
     */
    public function toggleActive($id)
    {
        try {
            $role = $this->roleRepo->findById($id);

            if (!$role) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Role not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $newStatus = !$role->is_active;
            $updatedRole = $this->roleRepo->update($id, ['is_active' => $newStatus]);

            logActivity('update', 'role', $id, "Changed role status to: " . ($newStatus ? 'active' : 'inactive'));

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Role status updated successfully')
                ->setData($updatedRole);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to toggle role status: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
