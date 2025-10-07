<?php

namespace App\Repositories\Contracts\Customers;

interface LoyaltyTransactionRepositoryInterface
{
    public function query();
    public function getAll();
    public function findById($id);
    public function create(array $data);
    public function getByCustomer($customerId);
    public function getByType($type);
    public function earnPoints($customerId, $points, $programId = null, $description = null, $expiresAt = null);
    public function redeemPoints($customerId, $points, $description = null);
    public function adjustPoints($customerId, $points, $description = null);
    public function expirePoints($customerId, $points, $description = null);
    public function getCustomerBalance($customerId);
}
