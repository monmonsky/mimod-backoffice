<?php

namespace App\Repositories;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserRepository implements UserRepositoryInterface
{
    protected $tableName = 'users';

    protected $tokenTableName = 'personal_access_tokens';

    private function table()
    {
        return DB::table($this->tableName);
    }

    private function tokenTable()
    {
        return DB::table($this->tokenTableName);
    }

    public function findByEmail(string $email)
    {
        return $this->table()
            ->where('email', $email)
            ->first();
    }

    public function findById(string $id)
    {
        // Get user with role in single query
        $user = DB::table('users')
            ->leftJoin('user_roles', function($join) {
                $join->on('users.id', '=', 'user_roles.user_id')
                     ->where('user_roles.is_active', '=', true);
            })
            ->leftJoin('roles', 'user_roles.role_id', '=', 'roles.id')
            ->where('users.id', $id)
            ->select(
                'users.*',
                'roles.name as role',
                'roles.display_name as role_display',
                'roles.priority as role_priority'
            )
            ->first();

        if (!$user) {
            return null;
        }

        // Get permissions in single query using GROUP_CONCAT for better performance
        // This avoids N+1 query problem
        $permissionsResult = DB::table('role_permissions')
            ->join('user_roles', 'role_permissions.role_id', '=', 'user_roles.role_id')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->where('user_roles.user_id', $id)
            ->where('user_roles.is_active', true)
            ->pluck('permissions.name')
            ->toArray();

        $user->permissions = $permissionsResult;

        return $user;
    }

    public function updateLastLogin(string $userId, string $ip)
    {
        return $this->table()
            ->where('id', $userId)
            ->update([
                'last_login_at' => now(),
                'last_login_ip' => $ip,
                'updated_at' => now(),
            ]);
    }

    public function hasActiveRole(string $userId): bool
    {
        return DB::table('user_roles')
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    /**
     * Create new token for user
     */
    public function createToken(string $userId, string $tokenName = 'auth-token', array $abilities = ['*']): string
    {
        // Generate unique token
        $plainTextToken = Str::random(40);

        // Hash token for storage
        $hashedToken = hash('sha256', $plainTextToken);

        // Store in database
        $this->tokenTable()->insert([
            'tokenable_type' => 'App\\Models\\User',
            'tokenable_id' => $userId,
            'name' => $tokenName,
            'token' => $hashedToken,
            'abilities' => json_encode($abilities),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Return plain text token
        return $plainTextToken;
    }

    /**
     * Find user by token
     */
    public function findByToken(string $token)
    {
        $hashedToken = hash('sha256', $token);

        $tokenData = $this->tokenTable()
            ->where('token', $hashedToken)
            ->first();

        if (!$tokenData) {
            return null;
        }

        $user = $this->findById($tokenData->tokenable_id);

        if (!$user) {
            return null;
        }

        $this->updateTokenLastUsed($hashedToken);

        return $user;
    }

    /**
     * Revoke specific token
     */
    public function revokeToken(string $token): bool
    {
        $hashedToken = hash('sha256', $token);

        return $this->tokenTable()
            ->where('token', $hashedToken)
            ->delete() > 0;
    }

    /**
     * Revoke all user tokens
     */
    public function revokeAllUserTokens(string $userId): bool
    {
        return $this->tokenTable()
            ->where('tokenable_id', $userId)
            ->where('tokenable_type', 'App\\Models\\User')
            ->delete() > 0;
    }

    /**
     * Update token last used timestamp
     */
    public function updateTokenLastUsed(string $hashedToken): bool
    {
        return $this->tokenTable()
            ->where('token', $hashedToken)
            ->update([
                'last_used_at' => now(),
                'updated_at' => now(),
            ]) > 0;
    }

    /**
     * Clean expired tokens
     */
    public function cleanExpiredTokens(): int
    {
        return $this->tokenTable()
            ->where('expires_at', '<', now())
            ->whereNotNull('expires_at')
            ->delete();
    }

    /**
     * Get user's active tokens
     */
    public function getUserTokens(string $userId)
    {
        return $this->tokenTable()
            ->where('tokenable_id', $userId)
            ->where('tokenable_type', 'App\\Models\\User')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get all users
     */
    public function getAll()
    {
        return $this->table()
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * Get all users with their roles
     * Filtered by current user's role priority
     */
    public function getAllWithRoles()
    {
        $query = DB::table('users')
            ->leftJoin('user_roles', function($join) {
                $join->on('users.id', '=', 'user_roles.user_id')
                     ->where('user_roles.is_active', '=', true);
            })
            ->leftJoin('roles', 'user_roles.role_id', '=', 'roles.id')
            ->select(
                'users.*',
                'roles.id as role_id',
                'roles.name as role_name',
                'roles.display_name as role_display_name',
                'roles.priority as role_priority',
                'user_roles.expires_at as role_expires_at'
            );

        // Filter by role priority (user can only see users with same or lower priority)
        // Higher priority = HIGHER number (Super Admin = 100)
        // Lower priority = LOWER number (Customer = 10)
        // So we use <= to show users with same or LOWER number (lower actual priority)
        $currentUser = currentUser();
        if ($currentUser && isset($currentUser['role_priority'])) {
            $query->where(function($q) use ($currentUser) {
                $q->where('roles.priority', '<=', $currentUser['role_priority'])
                  ->orWhereNull('roles.priority'); // Include users without role
            });
        }

        return $query->orderBy('users.id', 'asc')->get();
    }

    /**
     * Create new user
     */
    public function create(array $data)
    {
        $data['created_at'] = now();
        $data['updated_at'] = now();

        // Convert is_active to status field
        if (isset($data['is_active'])) {
            $data['status'] = ($data['is_active'] === '1' || $data['is_active'] === 1 || $data['is_active'] === true) ? 'active' : 'inactive';
            unset($data['is_active']);
        } else {
            $data['status'] = 'active'; // Default to active
        }

        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        // Use insertGetId to get auto-generated ID
        $id = $this->table()->insertGetId($data);

        return $this->findById($id);
    }

    /**
     * Update user
     */
    public function update(string $id, array $data)
    {
        $data['updated_at'] = now();

        // Convert is_active to status field
        if (isset($data['is_active'])) {
            $data['status'] = ($data['is_active'] === '1' || $data['is_active'] === 1 || $data['is_active'] === true) ? 'active' : 'inactive';
            unset($data['is_active']);
        }

        // Hash password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $this->table()->where('id', $id)->update($data);

        return $this->findById($id);
    }

    /**
     * Delete user
     */
    public function delete(string $id)
    {
        // Delete user roles first
        DB::table('user_roles')->where('user_id', $id)->delete();

        // Delete user tokens
        $this->revokeAllUserTokens($id);

        // Delete user
        return $this->table()->where('id', $id)->delete();
    }

    /**
     * Toggle user active status
     */
    public function toggleActive(string $id)
    {
        $user = $this->findById($id);

        if (!$user) {
            throw new \Exception('User not found');
        }

        // Toggle between 'active' and 'inactive'
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';

        $this->table()
            ->where('id', $id)
            ->update([
                'status' => $newStatus,
                'updated_at' => now()
            ]);

        return $this->findById($id);
    }

    /**
     * Assign role to user
     */
    public function assignRole(string $userId, string $roleId, ?string $expiresAt = null)
    {
        // Deactivate all current roles
        DB::table('user_roles')
            ->where('user_id', $userId)
            ->update(['is_active' => false]);

        // Check if role already assigned
        $existing = DB::table('user_roles')
            ->where('user_id', $userId)
            ->where('role_id', $roleId)
            ->first();

        if ($existing) {
            // Reactivate existing role
            DB::table('user_roles')
                ->where('user_id', $userId)
                ->where('role_id', $roleId)
                ->update([
                    'is_active' => true,
                    'expires_at' => $expiresAt,
                    'assigned_at' => now()
                ]);
        } else {
            // Create new role assignment
            DB::table('user_roles')->insert([
                'user_id' => $userId,
                'role_id' => $roleId,
                'is_active' => true,
                'expires_at' => $expiresAt,
                'assigned_by' => null,
                'assigned_at' => now()
            ]);
        }

        return true;
    }

    /**
     * Get user's current role
     */
    public function getUserRole(string $userId)
    {
        return DB::table('user_roles')
            ->join('roles', 'user_roles.role_id', '=', 'roles.id')
            ->where('user_roles.user_id', $userId)
            ->where('user_roles.is_active', true)
            ->select('roles.*', 'user_roles.expires_at')
            ->first();
    }

    /**
     * Get user statistics
     */
    public function getStatistics()
    {
        // Optimize: Use single query with conditional aggregation instead of 4 separate queries
        $stats = DB::table('users')
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status != 'active' THEN 1 ELSE 0 END) as inactive
            ")
            ->first();

        // Get users with roles count in separate query (can't be combined efficiently)
        $usersWithRoles = DB::table('user_roles')
            ->where('is_active', true)
            ->distinct()
            ->count('user_id');

        return [
            'total' => (int) $stats->total,
            'active' => (int) $stats->active,
            'inactive' => (int) $stats->inactive,
            'with_roles' => $usersWithRoles,
        ];
    }
}
