<?php

namespace App\Http\Controllers\AccessControl;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\AccessControl\RoleRepositoryInterface;
use App\Repositories\Contracts\AccessControl\ModuleRepositoryInterface;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Repositories\Contracts\PermissionGroupRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    protected $roleRepo;
    protected $moduleRepo;
    protected $permissionRepo;
    protected $permissionGroupRepo;

    public function __construct(
        RoleRepositoryInterface $roleRepository,
        ModuleRepositoryInterface $moduleRepository,
        PermissionRepositoryInterface $permissionRepository,
        PermissionGroupRepositoryInterface $permissionGroupRepository
    ) {
        $this->roleRepo = $roleRepository;
        $this->moduleRepo = $moduleRepository;
        $this->permissionRepo = $permissionRepository;
        $this->permissionGroupRepo = $permissionGroupRepository;
    }

    public function index()
    {
        $roles = $this->roleRepo->getAllWithCounts();
        $statistics = $this->roleRepo->getStatistics();

        return view('pages.access-control.roles.index', compact('roles', 'statistics'));
    }

    public function create()
    {
        $modules = $this->moduleRepo->getAllWithChildren();
        $permissionGroups = $this->permissionGroupRepo->getAll();
        $permissions = $this->permissionRepo->getAll();

        // Eager load permission group items to avoid N+1 queries
        $permissionGroupItems = DB::table('permission_group_items')
            ->select('group_id', 'permission_id')
            ->get()
            ->groupBy('group_id')
            ->map(function ($items) {
                return $items->pluck('permission_id')->toArray();
            });

        return view('pages.access-control.roles.create', compact('modules', 'permissionGroups', 'permissions', 'permissionGroupItems'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|unique:roles,name|max:100',
                'display_name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'priority' => 'required|integer|min:0|max:100',
                'is_active' => 'nullable|boolean',
                'is_system' => 'nullable|boolean',
                'modules' => 'array',
                'permissions' => 'array',
            ]);

            // is_active dan is_system sudah di-handle oleh JavaScript
            // Tidak perlu override lagi di sini

            // Extract modules and permissions before creating role
            $modules = $request->input('modules', []);
            $permissions = $request->input('permissions', []);

            // Remove modules and permissions from validated data
            unset($validated['modules']);
            unset($validated['permissions']);

            DB::beginTransaction();

            // Create role
            $role = $this->roleRepo->create($validated);

            // Assign permissions
            if (!empty($permissions)) {
                $this->roleRepo->syncPermissions($role->id, $permissions);
            }

            DB::commit();

            // Log activity
            logActivity('create', 'Created new role: ' . $role->display_name, 'Role', $role->id);

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully',
                'data' => $role
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $role = $this->roleRepo->findById($id);
        $modules = $this->moduleRepo->getAllWithChildren();
        $permissionGroups = $this->permissionGroupRepo->getAll();
        $permissions = $this->permissionRepo->getAll();

        // Get assigned modules and permissions
        $assignedModules = $this->roleRepo->getRoleModules($id);
        $assignedPermissions = $this->roleRepo->getRolePermissions($id);

        // Eager load permission group items to avoid N+1 queries
        $permissionGroupItems = DB::table('permission_group_items')
            ->select('group_id', 'permission_id')
            ->get()
            ->groupBy('group_id')
            ->map(function ($items) {
                return $items->pluck('permission_id')->toArray();
            });

        return view('pages.access-control.roles.edit', compact('role', 'modules', 'permissionGroups', 'permissions', 'assignedModules', 'assignedPermissions', 'permissionGroupItems'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100|unique:roles,name,' . $id,
                'display_name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'priority' => 'required|integer|min:0|max:100',
                'is_active' => 'nullable|boolean',
                'is_system' => 'nullable|boolean',
                'modules' => 'array',
                'permissions' => 'array',
            ]);

            // is_active dan is_system sudah di-handle oleh JavaScript
            // Tidak perlu override lagi di sini

            // Extract permissions before updating role
            $permissions = $request->input('permissions', []);

            // Remove modules and permissions from validated data
            unset($validated['modules']);
            unset($validated['permissions']);

            DB::beginTransaction();

            // Update role
            $role = $this->roleRepo->update($id, $validated);

            // Sync permissions
            if (!empty($permissions)) {
                $this->roleRepo->syncPermissions($id, $permissions);
            } else {
                // If no permissions provided, clear all permissions
                $this->roleRepo->syncPermissions($id, []);
            }

            DB::commit();

            // Log activity
            logActivity('update', 'Updated role: ' . $role->display_name, 'Role', $role->id);

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully',
                'data' => $role
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $role = $this->roleRepo->findById($id);
            $roleName = $role->display_name;

            $this->roleRepo->delete($id);

            // Log activity
            logActivity('delete', 'Deleted role: ' . $roleName, 'Role', $id);

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleActive($id)
    {
        try {
            $role = $this->roleRepo->toggleActive($id);

            // Log activity
            $status = $role->is_active ? 'activated' : 'deactivated';
            logActivity('update', 'Role ' . $status . ': ' . $role->display_name, 'Role', $role->id);

            return response()->json([
                'success' => true,
                'message' => 'Role status updated successfully',
                'data' => $role
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function detail($id)
    {
        try {
            $role = $this->roleRepo->findById($id);

            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role not found'
                ], 404);
            }

            // Get role permissions with details
            $permissions = $this->roleRepo->getRolePermissionsWithDetails($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $role->display_name,
                    'description' => $role->description,
                    'priority' => $role->priority,
                    'is_active' => $role->is_active,
                    'is_system' => $role->is_system,
                    'permissions' => $permissions
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
