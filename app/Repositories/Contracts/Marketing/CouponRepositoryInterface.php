<?php

namespace App\Repositories\Contracts\Marketing;

interface CouponRepositoryInterface
{
    public function query();
    public function getAll();
    public function findById($id);
    public function findByCode($code);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getActive();
    public function getExpired();
    public function validateCoupon($code, $customerId, $cartAmount);
    public function recordUsage($couponId, $customerId, $orderId, $discountAmount);
    public function getUsageHistory($couponId);
    public function getCustomerUsageCount($couponId, $customerId);
    public function incrementUsageCount($couponId);
    public function getStatistics();
}
