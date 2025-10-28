<?php

namespace App\Repositories;

use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function findById($id)
    {
        return DB::table('customers')->where('id', $id)->first();
    }

    public function findByEmail($email)
    {
        return DB::table('customers')->where('email', $email)->first();
    }

    public function findByPhone($phone)
    {
        return DB::table('customers')->where('phone', $phone)->first();
    }

    public function findByCustomerCode($customerCode)
    {
        return DB::table('customers')->where('customer_code', $customerCode)->first();
    }

    public function create(array $data)
    {
        // Generate customer code if not provided
        if (!isset($data['customer_code'])) {
            $data['customer_code'] = $this->generateCustomerCode();
        }

        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $data['created_at'] = now();
        $data['updated_at'] = now();

        $id = DB::table('customers')->insertGetId($data);

        return $this->findById($id);
    }

    public function update($id, array $data)
    {
        // Hash password if being updated
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $data['updated_at'] = now();

        DB::table('customers')->where('id', $id)->update($data);

        return $this->findById($id);
    }

    public function delete($id)
    {
        return DB::table('customers')->where('id', $id)->delete();
    }

    // Authentication Methods
    public function createToken($customerId, $deviceName = 'web')
    {
        $customer = $this->findById($customerId);

        if (!$customer) {
            throw new \Exception('Customer not found');
        }

        // Create token
        $token = hash('sha256', uniqid() . time());

        DB::table('customer_tokens')->insert([
            'customer_id' => $customerId,
            'token' => $token,
            'name' => $deviceName,
            'abilities' => json_encode(['*']),
            'last_used_at' => now(),
            'expires_at' => now()->addDays(30), // Token valid for 30 days
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $token;
    }

    public function revokeToken($token)
    {
        return DB::table('customer_tokens')->where('token', $token)->delete();
    }

    public function revokeAllCustomerTokens($customerId)
    {
        return DB::table('customer_tokens')->where('customer_id', $customerId)->delete();
    }

    public function getCustomerTokens($customerId)
    {
        return DB::table('customer_tokens')
            ->where('customer_id', $customerId)
            ->orderBy('last_used_at', 'desc')
            ->get();
    }

    // Email/Phone Verification
    public function markEmailAsVerified($customerId)
    {
        return DB::table('customers')
            ->where('id', $customerId)
            ->update([
                'email_verified_at' => now(),
                'updated_at' => now(),
            ]);
    }

    public function markPhoneAsVerified($customerId)
    {
        return DB::table('customers')
            ->where('id', $customerId)
            ->update([
                'phone_verified_at' => now(),
                'updated_at' => now(),
            ]);
    }

    // OTP Methods
    public function storeOtp($customerId, $otp, $type = 'email')
    {
        // Delete old OTPs for this customer and type
        DB::table('customer_otps')
            ->where('customer_id', $customerId)
            ->where('type', $type)
            ->delete();

        // Create new OTP
        DB::table('customer_otps')->insert([
            'customer_id' => $customerId,
            'otp' => $otp,
            'type' => $type,
            'expires_at' => now()->addMinutes(10), // OTP valid for 10 minutes
            'created_at' => now(),
        ]);
    }

    public function verifyOtp($customerId, $otp, $type = 'email')
    {
        $otpRecord = DB::table('customer_otps')
            ->where('customer_id', $customerId)
            ->where('otp', $otp)
            ->where('type', $type)
            ->where('expires_at', '>', now())
            ->first();

        if ($otpRecord) {
            // Delete used OTP
            DB::table('customer_otps')->where('id', $otpRecord->id)->delete();
            return true;
        }

        return false;
    }

    // Last Login
    public function updateLastLogin($customerId, $ip = null)
    {
        $data = [
            'last_login_at' => now(),
            'updated_at' => now(),
        ];

        if ($ip) {
            $data['last_login_ip'] = $ip;
        }

        return DB::table('customers')
            ->where('id', $customerId)
            ->update($data);
    }

    // Loyalty Points
    public function addLoyaltyPoints($customerId, $points, $description = null)
    {
        DB::beginTransaction();

        try {
            // Update customer loyalty points
            DB::table('customers')
                ->where('id', $customerId)
                ->increment('loyalty_points', $points);

            // Record transaction
            DB::table('customer_loyalty_transactions')->insert([
                'customer_id' => $customerId,
                'points' => $points,
                'type' => 'earned',
                'description' => $description ?? 'Points earned',
                'created_at' => now(),
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deductLoyaltyPoints($customerId, $points, $description = null)
    {
        DB::beginTransaction();

        try {
            // Check if customer has enough points
            $customer = $this->findById($customerId);
            if ($customer->loyalty_points < $points) {
                throw new \Exception('Insufficient loyalty points');
            }

            // Update customer loyalty points
            DB::table('customers')
                ->where('id', $customerId)
                ->decrement('loyalty_points', $points);

            // Record transaction
            DB::table('customer_loyalty_transactions')->insert([
                'customer_id' => $customerId,
                'points' => -$points,
                'type' => 'redeemed',
                'description' => $description ?? 'Points redeemed',
                'created_at' => now(),
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getLoyaltyHistory($customerId, $limit = 20)
    {
        return DB::table('customer_loyalty_transactions')
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    // Statistics
    public function updateOrderStats($customerId, $orderAmount)
    {
        $customer = $this->findById($customerId);

        if (!$customer) {
            return false;
        }

        $totalOrders = $customer->total_orders + 1;
        $totalSpent = $customer->total_spent + $orderAmount;
        $averageOrderValue = $totalSpent / $totalOrders;

        return DB::table('customers')
            ->where('id', $customerId)
            ->update([
                'total_orders' => $totalOrders,
                'total_spent' => $totalSpent,
                'average_order_value' => $averageOrderValue,
                'last_order_at' => now(),
                'updated_at' => now(),
            ]);
    }

    // Generate Customer Code
    public function generateCustomerCode()
    {
        $prefix = 'CUST';
        $lastCustomer = DB::table('customers')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastCustomer) {
            $lastNumber = (int) substr($lastCustomer->customer_code, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }
}
