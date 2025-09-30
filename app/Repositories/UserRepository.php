<?php

namespace App\Repositories;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UserRepository implements UserRepositoryInterface
{
    protected $tableName = 'users';

    private function table()
    {
        return DB::table($this->tableName);
    }

    public function findByEmail(string $email)
    {
        return $this->table()
            ->where('email', $email)
            ->first();
    }

    public function findById(string $id)
    {
        return $this->table()
            ->where('id', $id)
            ->first();
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
}
