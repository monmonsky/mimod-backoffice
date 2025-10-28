<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    public function query();
    public function findByEmail(string $email);
    public function findById(string $id);
    public function updateLastLogin(string $userId, string $ip);
    public function hasActiveRole(string $userId): bool;

    // CRUD methods
    public function getAll();
    public function getAllWithRoles();
    public function create(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);
    public function toggleActive(string $id);
    public function assignRole(string $userId, string $roleId, ?string $expiresAt = null);
    public function getUserRole(string $userId);
    public function getStatistics();

    // Token methods
    public function createToken(string $userId, string $tokenName = 'auth-token', array $abilities = ['*']): string;
    public function findByToken(string $token);
    public function revokeToken(string $token): bool;
    public function revokeAllUserTokens(string $userId): bool;
    public function updateTokenLastUsed(string $hashedToken): bool;
    public function cleanExpiredTokens(): int;
    public function getUserTokens(string $userId);
}
