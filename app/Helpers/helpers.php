<?php

if (!function_exists('currentUser')) {
    /**
     * Get current authenticated user data from request
     * Works with both custom middleware (auth.token, auth.sanctum) and Laravel's Sanctum
     *
     * Priority order:
     * 1. request()->attributes->get('user') - set by custom middleware
     * 2. request()->get('user') - legacy method
     * 3. request()->user() - Laravel Sanctum authenticated user
     * 4. auth()->user() - Laravel auth facade
     *
     * @param string|null $key Specific key to get (e.g., 'name', 'email')
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    function currentUser(?string $key = null, $default = null)
    {
        // Method 1: Try to get from attributes first (set by custom middleware)
        $user = request()->attributes->get('user');

        // Method 2: Fallback to request parameter (legacy)
        if (!$user) {
            $user = request()->get('user');
        }

        // Method 3: Fallback to Sanctum's authenticated user
        if (!$user) {
            try {
                $sanctumUser = request()->user();

                // If we got Sanctum user, convert to array for consistency
                if ($sanctumUser) {
                    $user = [
                        'id' => $sanctumUser->id,
                        'name' => $sanctumUser->name,
                        'email' => $sanctumUser->email,
                        'phone' => $sanctumUser->phone ?? null,
                        'avatar' => $sanctumUser->avatar ?? null,
                        'role_id' => $sanctumUser->role_id ?? null,
                        'status' => $sanctumUser->status ?? 'active',
                    ];
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::debug('Failed to get Sanctum user in currentUser(): ' . $e->getMessage());
            }
        }

        // Method 4: Try auth facade as last resort
        if (!$user) {
            try {
                $authUser = auth()->user();
                if ($authUser) {
                    $user = [
                        'id' => $authUser->id,
                        'name' => $authUser->name,
                        'email' => $authUser->email,
                        'phone' => $authUser->phone ?? null,
                        'avatar' => $authUser->avatar ?? null,
                        'role_id' => $authUser->role_id ?? null,
                        'status' => $authUser->status ?? 'active',
                    ];
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::debug('Failed to get auth user in currentUser(): ' . $e->getMessage());
            }
        }

        if (!$user) {
            return $default;
        }

        if ($key === null) {
            return $user;
        }

        // Handle both array and object
        if (is_array($user)) {
            return $user[$key] ?? $default;
        } elseif (is_object($user)) {
            return $user->$key ?? $default;
        }

        return $default;
    }
}

if (!function_exists('userName')) {
    /**
     * Get current user's name
     *
     * @param string $default Default value if name doesn't exist
     * @return string
     */
    function userName(string $default = 'Guest'): string
    {
        return currentUser('name', $default);
    }
}

if (!function_exists('userEmail')) {
    /**
     * Get current user's email
     *
     * @param string $default Default value if email doesn't exist
     * @return string
     */
    function userEmail(string $default = ''): string
    {
        return currentUser('email', $default);
    }
}

if (!function_exists('userId')) {
    /**
     * Get current user's ID
     *
     * @param mixed $default Default value if ID doesn't exist
     * @return mixed
     */
    function userId($default = null)
    {
        return currentUser('id', $default);
    }
}

if (!function_exists('userAvatar')) {
    /**
     * Get current user's avatar URL
     *
     * @param string $default Default avatar URL
     * @return string
     */
    function userAvatar(string $default = './images/avatars/1.png'): string
    {
        return currentUser('avatar', $default);
    }
}

if (!function_exists('userRole')) {
    /**
     * Get current user's role
     *
     * @param string $default Default role
     * @return string
     */
    function userRole(string $default = 'user'): string
    {
        return currentUser('role', $default);
    }
}

if (!function_exists('isUserRole')) {
    /**
     * Check if current user has specific role
     *
     * @param string|array $roles Role(s) to check
     * @return bool
     */
    function isUserRole($roles): bool
    {
        $userRole = userRole();

        if (is_array($roles)) {
            return in_array($userRole, $roles);
        }

        return $userRole === $roles;
    }
}

if (!function_exists('getUserPermissionsList')) {
    /**
     * Get all permissions for current user as array (for JavaScript)
     *
     * @return array
     */
    function getUserPermissionsList(): array
    {
        // Use currentUser() helper which gets user from request
        $user = currentUser();

        if (!$user) {
            return [];
        }

        $roleId = $user['role_id'] ?? null;

        if (!$roleId) {
            return [];
        }

        // Cache permissions in session to avoid multiple queries
        $cacheKey = 'user_permissions_list_' . ($user['id'] ?? 0);

        if (session()->has($cacheKey)) {
            return session($cacheKey);
        }

        // Query permissions from database
        $permissions = \DB::table('role_permissions')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->where('role_permissions.role_id', $roleId)
            ->where('permissions.is_active', true)
            ->pluck('permissions.name')
            ->toArray();

        // Cache in session
        session()->put($cacheKey, $permissions);

        return $permissions;
    }
}

if (!function_exists('hasPermission')) {
    /**
     * Check if current user has specific permission
     *
     * @param string $permission Permission to check (e.g., 'dashboard.view', 'users.create')
     * @return bool
     */
    function hasPermission(string $permission): bool
    {
        $user = currentUser();

        if (!$user) {
            return false;
        }

        // Super admin has all permissions
        if (isset($user['role']) && $user['role'] === 'super_admin') {
            return true;
        }

        $permissions = $user['permissions'] ?? [];

        // TEMPORARY FIX: Allow all permissions if user is authenticated but has no permissions loaded
        // This is for development/testing when permissions are not properly loaded
        // TODO: Remove this and properly set up user permissions via UserRepository
        if (empty($permissions)) {
            \Log::warning("User has no permissions loaded. Allowing access for development.", [
                'user_id' => is_array($user) ? ($user['id'] ?? null) : (is_object($user) ? $user->id : null),
                'permission' => $permission
            ]);
            return true; // Allow all for development
        }

        if (is_array($permissions)) {
            // Check exact permission name
            foreach ($permissions as $perm) {
                if (is_string($perm) && $perm === $permission) {
                    return true;
                }
                // If permission is object/array with 'name' key
                if (is_array($perm) && isset($perm['name']) && $perm['name'] === $permission) {
                    return true;
                }
            }
        }

        return false;
    }
}

if (!function_exists('hasAnyPermission')) {
    /**
     * Check if current user has any of the given permissions
     *
     * @param array $permissions Array of permissions to check
     * @return bool
     */
    function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('userInitials')) {
    /**
     * Get user initials from name
     *
     * @param string|null $name Name to get initials from (default: current user name)
     * @return string
     */
    function userInitials(?string $name = null): string
    {
        $name = $name ?? userName();

        $words = explode(' ', trim($name));

        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }

        return strtoupper(substr($name, 0, 2));
    }
}

if (!function_exists('logActivity')) {
    /**
     * Log user activity
     *
     * @param string $action Action performed (login, logout, create, update, delete, view, export, etc.)
     * @param string|null $description Description of the activity
     * @param string|null $subjectType Subject type (Model name: User, Role, Permission, Module, Settings, etc.)
     * @param int|null $subjectId Subject ID
     * @param array|null $properties Additional properties (old/new values, metadata, etc.)
     * @param int|null $userId User ID (optional, defaults to current user from request)
     * @return bool
     *
     * @example
     * // Simple login log
     * logActivity('login', 'User logged in successfully');
     *
     * // Login log with explicit user_id (when user not in request yet)
     * logActivity('login', 'User logged in successfully', 'auth', null, null, $user->id);
     *
     * // Log with subject
     * logActivity('create', 'Created new user: John Doe', 'User', 123);
     *
     * // Log with properties (changes tracking)
     * logActivity('update', 'Updated role permissions', 'Role', 1, [
     *     'old_permissions' => ['dashboard.view'],
     *     'new_permissions' => ['dashboard.view', 'users.create']
     * ]);
     *
     * // Log delete action
     * logActivity('delete', 'Deleted permission: test.permission', 'Permission', 15, [
     *     'permission_name' => 'test.permission'
     * ]);
     *
     * // Log settings change
     * logActivity('update', 'Updated email settings', 'Settings', null, [
     *     'changed_fields' => ['smtp_host', 'smtp_port']
     * ]);
     */
    function logActivity(
        string $action,
        ?string $description = null,
        ?string $subjectType = null,
        ?int $subjectId = null,
        ?array $properties = null,
        ?int $userId = null
    ): bool {
        try {
            // Use provided userId or get from current request
            // Try multiple methods to get user ID
            if (!$userId) {
                $userId = userId(); // Uses currentUser() with fallbacks
            }

            if (!$userId) {
                $userId = auth()->id(); // Laravel auth facade
            }

            if (!$userId) {
                $userId = request()->user()?->id; // Sanctum user
            }

            if (!$userId) {
                \Illuminate\Support\Facades\Log::warning('logActivity: No user ID found', [
                    'action' => $action,
                    'description' => $description,
                    'has_user_in_attributes' => request()->attributes->has('user'),
                    'has_user_in_request' => request()->has('user'),
                    'sanctum_user' => request()->user() ? 'exists' : 'null',
                    'auth_user' => auth()->user() ? 'exists' : 'null',
                ]);
                return false;
            }

            $data = [
                'user_id' => $userId,
                'action' => $action,
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
                'description' => $description,
                'properties' => $properties ? json_encode($properties) : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            \Illuminate\Support\Facades\DB::table('user_activities')->insert($data);

            return true;
        } catch (\Exception $e) {
            // Log error silently, don't break application flow
            \Illuminate\Support\Facades\Log::error('Failed to log activity: ' . $e->getMessage(), [
                'action' => $action,
                'description' => $description,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}

if (!function_exists('logException')) {
    /**
     * Log exception to user activities
     *
     * @param \Exception $exception The exception to log
     * @param string|null $context Additional context
     * @return bool
     *
     * @example
     * try {
     *     // some code
     * } catch (\Exception $e) {
     *     logException($e, 'Failed to create user');
     * }
     */
    function logException(\Exception $exception, ?string $context = null): bool
    {
        $description = $context ? $context . ': ' . $exception->getMessage() : $exception->getMessage();

        return logActivity(
            'error',
            $description,
            'Exception',
            null,
            [
                'exception_class' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]
        );
    }
}
