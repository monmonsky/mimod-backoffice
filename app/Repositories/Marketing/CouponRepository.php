<?php

namespace App\Repositories\Marketing;

use App\Repositories\Contracts\Marketing\CouponRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CouponRepository implements CouponRepositoryInterface
{
    protected $tableName = 'coupons';
    protected $usageTable = 'coupon_usage';

    private function table()
    {
        return DB::table($this->tableName);
    }

    public function query()
    {
        return $this->table();
    }

    public function getAll()
    {
        return $this->table()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findById($id)
    {
        return $this->table()->where('id', $id)->first();
    }

    public function findByCode($code)
    {
        return $this->table()->where('code', $code)->first();
    }

    public function create(array $data)
    {
        $data['created_at'] = now();
        $data['updated_at'] = now();
        $id = $this->table()->insertGetId($data);
        return $this->findById($id);
    }

    public function update($id, array $data)
    {
        $data['updated_at'] = now();
        $this->table()->where('id', $id)->update($data);
        return $this->findById($id);
    }

    public function delete($id)
    {
        return $this->table()->where('id', $id)->delete();
    }

    public function getActive()
    {
        return $this->table()
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getExpired()
    {
        return $this->table()
            ->where('end_date', '<', now())
            ->orderBy('end_date', 'desc')
            ->get();
    }

    public function validateCoupon($code, $customerId, $cartAmount)
    {
        $coupon = $this->findByCode($code);

        if (!$coupon) {
            return ['valid' => false, 'message' => 'Coupon not found'];
        }

        if (!$coupon->is_active) {
            return ['valid' => false, 'message' => 'Coupon is not active'];
        }

        $now = now();
        if ($now < $coupon->start_date || $now > $coupon->end_date) {
            return ['valid' => false, 'message' => 'Coupon has expired or not yet started'];
        }

        if ($coupon->usage_limit && $coupon->usage_count >= $coupon->usage_limit) {
            return ['valid' => false, 'message' => 'Coupon usage limit reached'];
        }

        $customerUsage = $this->getCustomerUsageCount($coupon->id, $customerId);
        if ($customerUsage >= $coupon->usage_limit_per_customer) {
            return ['valid' => false, 'message' => 'You have reached the usage limit for this coupon'];
        }

        if ($coupon->min_purchase && $cartAmount < $coupon->min_purchase) {
            return ['valid' => false, 'message' => 'Minimum purchase amount not met'];
        }

        return ['valid' => true, 'coupon' => $coupon];
    }

    public function recordUsage($couponId, $customerId, $orderId, $discountAmount)
    {
        $data = [
            'coupon_id' => $couponId,
            'customer_id' => $customerId,
            'order_id' => $orderId,
            'discount_amount' => $discountAmount,
            'used_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table($this->usageTable)->insert($data);
        $this->incrementUsageCount($couponId);
    }

    public function getUsageHistory($couponId)
    {
        return DB::table($this->usageTable)
            ->where('coupon_id', $couponId)
            ->orderBy('used_at', 'desc')
            ->get();
    }

    public function getCustomerUsageCount($couponId, $customerId)
    {
        return DB::table($this->usageTable)
            ->where('coupon_id', $couponId)
            ->where('customer_id', $customerId)
            ->count();
    }

    public function incrementUsageCount($couponId)
    {
        $this->table()
            ->where('id', $couponId)
            ->increment('usage_count');
    }

    public function getStatistics()
    {
        $totalCoupons = $this->table()->count();
        $activeCoupons = $this->table()
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();
        $totalUsage = DB::table($this->usageTable)->count();
        $totalDiscount = DB::table($this->usageTable)->sum('discount_amount');

        return (object) [
            'total_coupons' => $totalCoupons,
            'active_coupons' => $activeCoupons,
            'total_usage' => $totalUsage,
            'total_discount' => $totalDiscount ?? 0,
        ];
    }
}
