<?php

namespace App\Http\Controllers\AccessControl;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\AccessControl\RoleRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    protected $userRepo;
    protected $roleRepo;

    public function __construct(
        UserRepositoryInterface $userRepository,
        RoleRepositoryInterface $roleRepository
    ) {
        $this->userRepo = $userRepository;
        $this->roleRepo = $roleRepository;
    }

    public function index()
    {
        $users = $this->userRepo->getAllWithRoles();
        $statistics = $this->userRepo->getStatistics();

        return view('pages.access-control.users.index', compact('users', 'statistics'));
    }

    public function create()
    {
        $roles = $this->roleRepo->getAll();

        return view('pages.access-control.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email|max:255',
                'phone' => 'required|string|max:20',
                'password' => 'required|string|min:8|confirmed',
                'role_id' => 'nullable|exists:roles,id',
                'is_active' => 'boolean',
            ]);

            $validated['is_active'] = $request->has('is_active') ? true : false;

            // Extract role_id before creating user
            $roleId = $request->input('role_id');
            unset($validated['role_id']);
            unset($validated['password_confirmation']);

            DB::beginTransaction();

            // Create user
            $user = $this->userRepo->create($validated);

            // Assign role if provided
            if ($roleId) {
                $this->userRepo->assignRole($user->id, $roleId);
            }

            DB::commit();

            // Log activity
            logActivity('create', 'Created new user: ' . $user->name, 'User', $user->id);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user
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
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $user = $this->userRepo->findById($id);
        $roles = $this->roleRepo->getAll();
        $userRole = $this->userRepo->getUserRole($id);

        return view('pages.access-control.users.edit', compact('user', 'roles', 'userRole'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $id,
                'phone' => 'required|string|max:20',
                'password' => 'nullable|string|min:8|confirmed',
                'role_id' => 'nullable|exists:roles,id',
                'is_active' => 'nullable',
            ]);

            // is_active sudah di-handle oleh JavaScript (kirim '1' atau '0')
            // Tidak perlu override lagi di sini

            // Extract role_id before updating user
            $roleId = $request->input('role_id');
            unset($validated['role_id']);
            unset($validated['password_confirmation']);

            DB::beginTransaction();

            // Update user
            $user = $this->userRepo->update($id, $validated);

            // Update role
            if ($roleId) {
                $this->userRepo->assignRole($id, $roleId);
            }

            DB::commit();

            // Log activity
            logActivity('update', 'Updated user: ' . $user->name, 'User', $user->id);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user
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
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = $this->userRepo->findById($id);
            $userName = $user->name;

            $this->userRepo->delete($id);

            // Log activity
            logActivity('delete', 'Deleted user: ' . $userName, 'User', $id);

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
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
            $user = $this->userRepo->toggleActive($id);

            // Log activity
            $status = $user->is_active ? 'activated' : 'deactivated';
            logActivity('update', 'User ' . $status . ': ' . $user->name, 'User', $user->id);

            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
