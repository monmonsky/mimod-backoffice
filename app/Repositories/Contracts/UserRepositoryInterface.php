<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    public function findByEmail(string $email);
    public function findById(string $id);
    public function updateLastLogin(string $userId, string $ip);
    public function hasActiveRole(string $userId): bool;

    // Token methods
    public function createToken(string $userId, string $tokenName = 'auth-token', array $abilities = ['*']): string;
    public function findByToken(string $token);
    public function revokeToken(string $token): bool;
    public function revokeAllUserTokens(string $userId): bool;
    public function updateTokenLastUsed(string $hashedToken): bool;
    public function cleanExpiredTokens(): int;
    public function getUserTokens(string $userId);
}
