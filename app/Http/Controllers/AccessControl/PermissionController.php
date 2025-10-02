<?php

namespace App\Http\Controllers\AccessControl;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Repositories\Contracts\PermissionGroupRepositoryInterface;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    protected $permissionRepo;
    protected $permissionGroupRepo;

    public function __construct(
        PermissionRepositoryInterface $permissionRepository,
        PermissionGroupRepositoryInterface $permissionGroupRepository
    ) {
        $this->permissionRepo = $permissionRepository;
        $this->permissionGroupRepo = $permissionGroupRepository;
    }

    public function index()
    {
        $permissions = $this->permissionRepo->getAllWithGroup();
        $statistics = $this->permissionRepo->getStatistics();
        return view('pages.access-control.permissions.index', compact('permissions', 'statistics'));
    }

    public function create()
    {
        $groups = $this->permissionGroupRepo->getAll();
        return view('pages.access-control.permissions.create', compact('groups'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|unique:permissions,name|max:255',
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'permission_group_id' => 'nullable|exists:permission_groups,id',
            ]);

            $permission = $this->permissionRepo->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Permission created successfully',
                'data' => $permission
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
                'message' => 'Failed to create permission: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $permission = $this->permissionRepo->findById($id);
        $groups = $this->permissionGroupRepo->getAll();
        return view('pages.access-control.permissions.edit', compact('permission', 'groups'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:permissions,name,' . $id,
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'permission_group_id' => 'nullable|exists:permission_groups,id',
            ]);

            $permission = $this->permissionRepo->update($id, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully',
                'data' => $permission
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
                'message' => 'Failed to update permission: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->permissionRepo->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete permission: ' . $e->getMessage()
            ], 500);
        }
    }
}
