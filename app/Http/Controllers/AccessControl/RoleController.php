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
                'is_active' => 'boolean',
                'is_system' => 'boolean',
                'modules' => 'array',
                'permissions' => 'array',
            ]);

            $validated['is_active'] = $request->has('is_active') ? true : false;
            $validated['is_system'] = $request->has('is_system') ? true : false;

            DB::beginTransaction();

            // Create role
            $role = $this->roleRepo->create($validated);

            // Assign modules
            if ($request->has('modules')) {
                $this->roleRepo->syncModules($role->id, $request->input('modules'));
            }

            // Assign permissions
            if ($request->has('permissions')) {
                $this->roleRepo->syncPermissions($role->id, $request->input('permissions'));
            }

            DB::commit();

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
                'is_active' => 'boolean',
                'is_system' => 'boolean',
                'modules' => 'array',
                'permissions' => 'array',
            ]);

            $validated['is_active'] = $request->has('is_active') ? true : false;
            $validated['is_system'] = $request->has('is_system') ? true : false;

            DB::beginTransaction();

            // Update role
            $role = $this->roleRepo->update($id, $validated);

            // Sync modules
            if ($request->has('modules')) {
                $this->roleRepo->syncModules($id, $request->input('modules'));
            }

            // Sync permissions
            if ($request->has('permissions')) {
                $this->roleRepo->syncPermissions($id, $request->input('permissions'));
            }

            DB::commit();

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
            $this->roleRepo->delete($id);

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
}
