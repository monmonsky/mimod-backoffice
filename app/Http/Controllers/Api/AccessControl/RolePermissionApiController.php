<?php

namespace App\Http\Controllers\Api\AccessControl;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Contracts\AccessControl\RoleRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RolePermissionApiController extends Controller
{
    protected $roleRepo;
    protected $response;

    public function __construct(RoleRepositoryInterface $roleRepo, Response $response)
    {
        $this->roleRepo = $roleRepo;
        $this->response = $response;
    }

    /**
     * Get permissions for a specific role
     */
    public function index($roleId)
    {
        try {
            // Check if role exists
            $role = $this->roleRepo->findById($roleId);

            if (!$role) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Role not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Get permissions for this role
            $permissions = $this->roleRepo->getRolePermissions($roleId);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Role permissions retrieved successfully')
                ->setData([
                    'role' => $role,
                    'permissions' => $permissions
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve role permissions: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get permissions for a specific role grouped by module
     */
    public function grouped($roleId)
    {
        try {
            // Check if role exists
            $role = $this->roleRepo->findById($roleId);

            if (!$role) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Role not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Get permissions with details
            $permissions = $this->roleRepo->getRolePermissionsWithDetails($roleId);

            // Group by module
            $grouped = collect($permissions)->groupBy('module')->map(function ($items, $module) {
                return [
                    'module' => $module,
                    'permissions' => $items->values()->toArray()
                ];
            })->values();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Grouped role permissions retrieved successfully')
                ->setData([
                    'role' => $role,
                    'permissions' => $grouped
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve grouped role permissions: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Sync permissions for a role
     */
    public function sync(Request $request, $roleId)
    {
        try {
            // Check if role exists
            $role = $this->roleRepo->findById($roleId);

            if (!$role) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Role not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Check if role is system role
            if ($role->is_system) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage('Cannot modify permissions for system role')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 400);
            }

            $validator = Validator::make($request->all(), [
                'permission_ids' => 'required|array',
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

            // Sync permissions
            $this->roleRepo->syncPermissions($roleId, $request->permission_ids);

            // Get updated permissions
            $updatedPermissions = $this->roleRepo->getRolePermissions($roleId);

            logActivity('update', "Synced permissions for role: {$role->display_name}", 'role', (int)$roleId, [
                'permission_count' => count($request->permission_ids)
            ]);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Role permissions synced successfully')
                ->setData([
                    'role' => $role,
                    'permissions' => $updatedPermissions
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to sync role permissions: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Add a permission to a role
     */
    public function attach(Request $request, $roleId)
    {
        try {
            // Check if role exists
            $role = $this->roleRepo->findById($roleId);

            if (!$role) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Role not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            if ($role->is_system) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage('Cannot modify permissions for system role')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 400);
            }

            $validator = Validator::make($request->all(), [
                'permission_id' => 'required|exists:permissions,id',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            // Check if permission already attached
            $existing = DB::table('role_permissions')
                ->where('role_id', $roleId)
                ->where('permission_id', $request->permission_id)
                ->exists();

            if ($existing) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage('Permission already attached to this role')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 400);
            }

            // Attach permission
            DB::table('role_permissions')->insert([
                'role_id' => $roleId,
                'permission_id' => $request->permission_id,
                'granted_by' => userId(),
                'granted_at' => now(),
            ]);

            $updatedPermissions = $this->roleRepo->getRolePermissions($roleId);

            logActivity('update', "Added permission to role: {$role->display_name}", 'role', (int)$roleId);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Permission attached successfully')
                ->setData([
                    'role' => $role,
                    'permissions' => $updatedPermissions
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to attach permission: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Remove a permission from a role
     */
    public function detach($roleId, $permissionId)
    {
        try {
            // Check if role exists
            $role = $this->roleRepo->findById($roleId);

            if (!$role) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Role not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            if ($role->is_system) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage('Cannot modify permissions for system role')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 400);
            }

            // Check if permission is attached
            $exists = DB::table('role_permissions')
                ->where('role_id', $roleId)
                ->where('permission_id', $permissionId)
                ->exists();

            if (!$exists) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Permission not attached to this role')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Detach permission
            DB::table('role_permissions')
                ->where('role_id', $roleId)
                ->where('permission_id', $permissionId)
                ->delete();

            $updatedPermissions = $this->roleRepo->getRolePermissions($roleId);

            logActivity('update', "Removed permission from role: {$role->display_name}", 'role', (int)$roleId);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Permission detached successfully')
                ->setData([
                    'role' => $role,
                    'permissions' => $updatedPermissions
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to detach permission: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
