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
        $user = $this->table()
            ->where('id', $id)
            ->first();

        if (!$user) {
            return null;
        }

        // Get user role
        $role = DB::table('user_roles')
            ->join('roles', 'user_roles.role_id', '=', 'roles.id')
            ->where('user_roles.user_id', $id)
            ->where('user_roles.is_active', true)
            ->select('roles.name', 'roles.display_name')
            ->first();

        // Get user permissions
        $permissions = DB::table('role_permissions')
            ->join('user_roles', 'role_permissions.role_id', '=', 'user_roles.role_id')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->where('user_roles.user_id', $id)
            ->where('user_roles.is_active', true)
            ->select('permissions.name', 'permissions.display_name')
            ->get();

        // Attach role and permissions to user object
        $user->role = $role ? $role->name : null;
        $user->role_display = $role ? $role->display_name : null;
        $user->permissions = $permissions->pluck('name')->toArray();

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
            'tokenable_type' => 'App\\Models\\User', // atau sesuaikan dengan namespace
            'tokenable_id' => $userId,
            'name' => $tokenName,
            'token' => $hashedToken,
            'abilities' => json_encode($abilities),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Return plain text token (format: {id}|{plainTextToken})
        return $plainTextToken;
    }

    /**
     * Find user by token
     */
    public function findByToken(string $token)
    {
        // Hash token untuk mencari di database
        $hashedToken = hash('sha256', $token);

        // Find token
        $tokenData = $this->tokenTable()
            ->where('token', $hashedToken)
            ->first();

        if (!$tokenData) {
            return null;
        }

        // Get user
        $user = $this->findById($tokenData->tokenable_id);

        if (!$user) {
            return null;
        }

        // Update last used
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
}
