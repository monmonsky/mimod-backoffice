<?php

namespace App\Http\Controllers\AccessControl;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\PermissionGroupRepositoryInterface;
use Illuminate\Http\Request;

class PermissionGroupController extends Controller
{
    protected $permissionGroupRepo;

    public function __construct(PermissionGroupRepositoryInterface $permissionGroupRepository)
    {
        $this->permissionGroupRepo = $permissionGroupRepository;
    }

    public function index()
    {
        $groups = $this->permissionGroupRepo->getAllWithCount();
        $statistics = $this->permissionGroupRepo->getStatistics();
        return view('pages.access-control.permission-groups.index', compact('groups', 'statistics'));
    }

    public function create()
    {
        return view('pages.access-control.permission-groups.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|unique:permission_groups,name|max:255',
                'description' => 'nullable|string',
            ]);

            $group = $this->permissionGroupRepo->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Permission group created successfully',
                'data' => $group
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
                'message' => 'Failed to create permission group: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $group = $this->permissionGroupRepo->findByIdWithCount($id);
        return view('pages.access-control.permission-groups.edit', compact('group'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:permission_groups,name,' . $id,
                'description' => 'nullable|string',
            ]);

            $group = $this->permissionGroupRepo->update($id, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Permission group updated successfully',
                'data' => $group
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
                'message' => 'Failed to update permission group: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Check if group has permissions
            if ($this->permissionGroupRepo->hasPermissions($id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete group with existing permissions'
                ], 422);
            }

            $this->permissionGroupRepo->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Permission group deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete permission group: ' . $e->getMessage()
            ], 500);
        }
    }
}
