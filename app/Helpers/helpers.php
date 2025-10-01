<?php

if (!function_exists('currentUser')) {
    /**
     * Get current authenticated user data from request
     *
     * @param string|null $key Specific key to get (e.g., 'name', 'email')
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    function currentUser(?string $key = null, $default = null)
    {
        $user = request()->get('user');

        if (!$user) {
            return $default;
        }

        if ($key === null) {
            return $user;
        }

        return $user[$key] ?? $default;
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
