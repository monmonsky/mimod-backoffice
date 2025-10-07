<?php

namespace App\Repositories\Customers;

use App\Repositories\Contracts\Customers\LoyaltyTransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class LoyaltyTransactionRepository implements LoyaltyTransactionRepositoryInterface
{
    protected $tableName = 'loyalty_transactions';

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

    public function create(array $data)
    {
        $data['created_at'] = now();
        $data['updated_at'] = now();
        $id = $this->table()->insertGetId($data);
        return $this->findById($id);
    }

    public function getByCustomer($customerId)
    {
        return $this->table()
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByType($type)
    {
        return $this->table()
            ->where('transaction_type', $type)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function earnPoints($customerId, $points, $programId = null, $description = null, $expiresAt = null)
    {
        $currentBalance = $this->getCustomerBalance($customerId);
        $newBalance = $currentBalance + $points;

        $data = [
            'customer_id' => $customerId,
            'loyalty_program_id' => $programId,
            'transaction_type' => 'earn',
            'points' => $points,
            'balance_after' => $newBalance,
            'description' => $description ?? 'Points earned',
            'expires_at' => $expiresAt,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $transaction = $this->create($data);

        // Update customer loyalty points
        DB::table('customers')
            ->where('id', $customerId)
            ->update(['loyalty_points' => $newBalance]);

        return $transaction;
    }

    public function redeemPoints($customerId, $points, $description = null)
    {
        $currentBalance = $this->getCustomerBalance($customerId);

        if ($currentBalance < $points) {
            throw new \Exception('Insufficient loyalty points');
        }

        $newBalance = $currentBalance - $points;

        $data = [
            'customer_id' => $customerId,
            'transaction_type' => 'redeem',
            'points' => -$points, // Negative for redemption
            'balance_after' => $newBalance,
            'description' => $description ?? 'Points redeemed',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $transaction = $this->create($data);

        // Update customer loyalty points
        DB::table('customers')
            ->where('id', $customerId)
            ->update(['loyalty_points' => $newBalance]);

        return $transaction;
    }

    public function adjustPoints($customerId, $points, $description = null)
    {
        $currentBalance = $this->getCustomerBalance($customerId);
        $newBalance = $currentBalance + $points;

        $data = [
            'customer_id' => $customerId,
            'transaction_type' => 'adjust',
            'points' => $points,
            'balance_after' => $newBalance,
            'description' => $description ?? 'Points adjusted',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $transaction = $this->create($data);

        // Update customer loyalty points
        DB::table('customers')
            ->where('id', $customerId)
            ->update(['loyalty_points' => $newBalance]);

        return $transaction;
    }

    public function expirePoints($customerId, $points, $description = null)
    {
        $currentBalance = $this->getCustomerBalance($customerId);
        $newBalance = max(0, $currentBalance - $points);

        $data = [
            'customer_id' => $customerId,
            'transaction_type' => 'expire',
            'points' => -$points, // Negative for expiry
            'balance_after' => $newBalance,
            'description' => $description ?? 'Points expired',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $transaction = $this->create($data);

        // Update customer loyalty points
        DB::table('customers')
            ->where('id', $customerId)
            ->update(['loyalty_points' => $newBalance]);

        return $transaction;
    }

    public function getCustomerBalance($customerId)
    {
        $customer = DB::table('customers')
            ->where('id', $customerId)
            ->first();

        return $customer ? ($customer->loyalty_points ?? 0) : 0;
    }
}
