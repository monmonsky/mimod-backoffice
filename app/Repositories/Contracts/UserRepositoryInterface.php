<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    public function findByEmail(string $email);
    public function findById(string $id);
    public function updateLastLogin(string $userId, string $ip);
    public function hasActiveRole(string $userId): bool;
}
