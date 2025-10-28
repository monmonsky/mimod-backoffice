<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $userRepository;
    protected $responseBuilder;
    protected $response;

    public function __construct(
        UserRepositoryInterface $userRepository
    ) {
        $this->userRepository = $userRepository;
        $this->responseBuilder = new ResultBuilder;
        $this->response = new Response;
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'email'],
                'password' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                $this->responseBuilder->setStatus(false);
                $this->responseBuilder->setMessage($validator->errors()->first());
                return $this->response->generateResponse($this->responseBuilder);
            }

            // Get user by email with role and modules
            $user = $this->userRepository->findByEmailWithRoleAndModules($request->email);

            // Check if user exists and password is correct
            if (!$user || !Hash::check($request->password, $user->password)) {
                $this->responseBuilder->setStatus(false);
                $this->responseBuilder->setMessage("The provided credentials do not match our records.");
                return $this->response->generateResponse($this->responseBuilder);
            }

            // Block API user from web login
            if ($user->name === 'API') {
                $this->responseBuilder->setStatus(false);
                $this->responseBuilder->setMessage('This account is for API access only. Please use API token authentication.');
                return $this->response->generateResponse($this->responseBuilder);
            }

            // Check if user is active
            if ($user->status !== 'active') {
                $this->responseBuilder->setStatus(false);
                $this->responseBuilder->setMessage('Your account has been ' . $user->status . '.');
                return $this->response->generateResponse($this->responseBuilder);
            }

            // Update last login information
            $this->userRepository->updateLastLogin($user->id, $request->ip());

            // Create token
            $deviceName = $request->device_name ?? $request->userAgent() ?? 'unknown-device';
            $token = $this->userRepository->createToken($user->id, $deviceName);

            // Log activity - pass user_id explicitly because user is not yet in request
            logActivity('login', "User logged in from {$request->ip()}", 'auth', null, null, $user->id);

            // Get full permissions with details
            $permissions = [];
            if ($user->role_id) {
                $permissionsData = DB::table('role_permissions')
                    ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
                    ->where('role_permissions.role_id', $user->role_id)
                    ->select(
                        'permissions.id',
                        'permissions.name',
                        'permissions.display_name',
                        'permissions.action',
                        'permissions.module',
                        'permissions.is_active'
                    )
                    ->orderBy('permissions.module', 'asc')
                    ->orderBy('permissions.action', 'asc')
                    ->get();

                foreach ($permissionsData as $perm) {
                    $permissions[] = [
                        'id' => $perm->id,
                        'name' => $perm->name,
                        'display_name' => $perm->display_name,
                        'action' => $perm->action,
                        'module' => $perm->module,
                        'is_active' => (bool) $perm->is_active,
                    ];
                }
            }

            // Build role object
            $role = null;
            if ($user->role_id) {
                $role = [
                    'id' => $user->role_id,
                    'name' => $user->role_name,
                    'display_name' => $user->role_display_name,
                    'modules' => $user->modules ?? [],
                ];
            }

            $this->responseBuilder->setMessage("Login successful.");
            $this->responseBuilder->setData([
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 3600, // Token expires in 1 hour (3600 seconds)
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'email_verified_at' => $user->email_verified_at,
                    'phone_verified_at' => $user->phone_verified_at,
                    'two_factor_enabled' => $user->two_factor_enabled,
                    'role' => $role,
                    'permissions' => $permissions, // NEW: Full permissions array with details
                ]
            ]);

            return $this->response->generateResponse($this->responseBuilder);
        } catch (\Throwable $th) {
            $this->responseBuilder->setStatus(false);
            $this->responseBuilder->setMessage($th->getMessage());
            return $this->response->generateResponse($this->responseBuilder);
        }
    }

    /**
     * Logout user (revoke current token)
     */
    public function logout(Request $request)
    {
        try {
            $token = $request->cookie('auth_token');

            if ($token) {
                $this->userRepository->revokeToken($token);
            }

            // Log activity
            if ($request->user) {
                logActivity('logout', "User logged out", 'auth', (int) $request->user->id);
            }

            $this->responseBuilder->setMessage("Logout successful.");
            return $this->response->generateResponse($this->responseBuilder);
        } catch (\Throwable $th) {
            $this->responseBuilder->setStatus(false);
            $this->responseBuilder->setMessage($th->getMessage());
            return $this->response->generateResponse($this->responseBuilder);
        }
    }

    /**
     * Logout from all devices (revoke all tokens)
     */
    public function logoutAll(Request $request)
    {
        try {
            $user = $request->user;

            $this->userRepository->revokeAllUserTokens($user->id);

            // Log activity
            logActivity('logout_all', "User logged out from all devices", 'auth', (int) $user->id);

            $this->responseBuilder->setMessage("Logged out from all devices successfully.");
            return $this->response->generateResponse($this->responseBuilder);
        } catch (\Throwable $th) {
            $this->responseBuilder->setStatus(false);
            $this->responseBuilder->setMessage($th->getMessage());
            return $this->response->generateResponse($this->responseBuilder);
        }
    }

    /**
     * Get current user
     */
    public function me(Request $request)
    {
        try {
            $user = $request->user;

            $this->responseBuilder->setData([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'email_verified_at' => $user->email_verified_at,
                'phone_verified_at' => $user->phone_verified_at,
                'two_factor_enabled' => $user->two_factor_enabled,
                'last_login_at' => $user->last_login_at,
                'last_login_ip' => $user->last_login_ip,
            ]);

            return $this->response->generateResponse($this->responseBuilder);
        } catch (\Throwable $th) {
            $this->responseBuilder->setStatus(false);
            $this->responseBuilder->setMessage($th->getMessage());
            return $this->response->generateResponse($this->responseBuilder);
        }
    }

    /**
     * Get all active sessions/tokens
     */
    public function sessions(Request $request)
    {
        try {
            $user = $request->user;

            $tokens = $this->userRepository->getUserTokens($user->id);

            $sessions = [];
            foreach ($tokens as $token) {
                $sessions[] = [
                    'id' => $token->id,
                    'device_name' => $token->name,
                    'last_used_at' => $token->last_used_at,
                    'created_at' => $token->created_at,
                ];
            }

            $this->responseBuilder->setData([
                'sessions' => $sessions,
                'total' => count($sessions),
            ]);

            return $this->response->generateResponse($this->responseBuilder);
        } catch (\Throwable $th) {
            $this->responseBuilder->setStatus(false);
            $this->responseBuilder->setMessage($th->getMessage());
            return $this->response->generateResponse($this->responseBuilder);
        }
    }
}
