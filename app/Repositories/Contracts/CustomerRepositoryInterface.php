<?php

namespace App\Repositories\Contracts;

interface CustomerRepositoryInterface
{
    public function findById($id);
    public function findByEmail($email);
    public function findByPhone($phone);
    public function findByCustomerCode($customerCode);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);

    // Authentication
    public function createToken($customerId, $deviceName = 'web');
    public function revokeToken($token);
    public function revokeAllCustomerTokens($customerId);
    public function getCustomerTokens($customerId);

    // Email/Phone Verification
    public function markEmailAsVerified($customerId);
    public function markPhoneAsVerified($customerId);

    // OTP
    public function storeOtp($customerId, $otp, $type = 'email');
    public function verifyOtp($customerId, $otp, $type = 'email');

    // Last Login
    public function updateLastLogin($customerId, $ip = null);

    // Loyalty Points
    public function addLoyaltyPoints($customerId, $points, $description = null);
    public function deductLoyaltyPoints($customerId, $points, $description = null);
    public function getLoyaltyHistory($customerId, $limit = 20);

    // Statistics
    public function updateOrderStats($customerId, $orderAmount);

    // Generate customer code
    public function generateCustomerCode();
}
