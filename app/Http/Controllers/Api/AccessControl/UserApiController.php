<?php

namespace App\Http\Controllers\Api\AccessControl;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserApiController extends Controller
{
    protected $userRepo;
    protected $response;

    public function __construct(UserRepositoryInterface $userRepo, Response $response)
    {
        $this->userRepo = $userRepo;
        $this->response = $response;
    }

    /**
     * Get all users with filters
     */
    public function index(Request $request)
    {
        try {
            $query = $this->userRepo->query();

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by role
            if ($request->has('role_id')) {
                $query->where('role_id', $request->role_id);
            }

            // Search by name or email
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'ILIKE', '%' . $search . '%')
                      ->orWhere('email', 'ILIKE', '%' . $search . '%');
                });
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Users retrieved successfully')
                ->setData($users);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve users: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get single user by ID
     */
    public function show($id)
    {
        try {
            $user = $this->userRepo->findByIdWithRole($id);

            if (!$user) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('User not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('User retrieved successfully')
                ->setData($user);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve user: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Create new user
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'role_id' => 'required|exists:roles,id',
                'phone' => 'nullable|string|max:20',
                'status' => 'nullable|in:active,suspended,deleted',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $data = $validator->validated();
            $data['password'] = Hash::make($data['password']);

            $user = $this->userRepo->create($data);

            logActivity('create', 'user', $user->id, "Created user: {$user->name}");

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('201')
                ->setMessage('User created successfully')
                ->setData($user);

            return response()->json($this->response->generateResponse($result), 201);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to create user: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        try {
            $user = $this->userRepo->findById($id);

            if (!$user) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('User not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:users,email,' . $id,
                'password' => 'sometimes|nullable|string|min:8',
                'role_id' => 'sometimes|required|exists:roles,id',
                'phone' => 'nullable|string|max:20',
                'status' => 'nullable|in:active,suspended,deleted',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $data = $validator->validated();

            // Hash password if provided
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $updatedUser = $this->userRepo->update($id, $data);

            logActivity('update', 'user', $id, "Updated user: {$updatedUser->name}");

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('User updated successfully')
                ->setData($updatedUser);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update user: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        try {
            $user = $this->userRepo->findById($id);

            if (!$user) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('User not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Prevent deleting self
            if ($id == userId()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage('Cannot delete your own account')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 400);
            }

            $this->userRepo->delete($id);

            logActivity('delete', 'user', $id, "Deleted user: {$user->name}");

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('User deleted successfully')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to delete user: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Toggle user active status
     */
    public function toggleActive($id)
    {
        try {
            $user = $this->userRepo->findById($id);

            if (!$user) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('User not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $newStatus = $user->status === 'active' ? 'suspended' : 'active';
            $updatedUser = $this->userRepo->update($id, ['status' => $newStatus]);

            logActivity('update', 'user', $id, "Changed user status to: {$newStatus}");

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('User status updated successfully')
                ->setData($updatedUser);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to toggle user status: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
