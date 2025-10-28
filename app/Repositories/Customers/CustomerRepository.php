<?php

namespace App\Repositories\Customers;

use App\Repositories\Contracts\Customers\CustomerRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CustomerRepository implements CustomerRepositoryInterface
{
    protected $tableName = 'customers';
    protected $addressTableName = 'customer_addresses';

    private function table()
    {
        return DB::table($this->tableName);
    }

    private function addressTable()
    {
        return DB::table($this->addressTableName);
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

    public function getAllWithAddresses()
    {
        $customers = $this->getAll();
        $customerIds = $customers->pluck('id')->toArray();

        if (empty($customerIds)) {
            return $customers;
        }

        // Batch load addresses
        $addresses = $this->addressTable()
            ->whereIn('customer_id', $customerIds)
            ->get()
            ->groupBy('customer_id');

        // Attach addresses to customers
        foreach ($customers as $customer) {
            $customer->addresses = $addresses->get($customer->id, collect());
            $customer->default_address = $customer->addresses->firstWhere('is_default', true);
        }

        return $customers;
    }

    public function findById($id)
    {
        $customer = $this->table()->where('id', $id)->first();
        if (!$customer) {
            throw new \Exception("Customer not found");
        }
        return $customer;
    }

    public function findByIdWithAddresses($id)
    {
        $customer = $this->findById($id);
        $customer->addresses = $this->getAddresses($id);
        $customer->default_address = collect($customer->addresses)->firstWhere('is_default', true);
        return $customer;
    }

    public function findByEmail($email)
    {
        return $this->table()->where('email', $email)->first();
    }

    public function findByCustomerCode($code)
    {
        return $this->table()->where('customer_code', $code)->first();
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
        // Soft delete
        return $this->table()->where('id', $id)->update([
            'deleted_at' => now()
        ]);
    }

    public function getBySegment($segment)
    {
        return $this->table()
            ->where('segment', $segment)
            ->orderBy('total_spent', 'desc')
            ->get();
    }

    public function getVipCustomers()
    {
        return $this->table()
            ->where('is_vip', true)
            ->orderBy('total_spent', 'desc')
            ->get();
    }

    public function getStatistics()
    {
        $stats = $this->table()
            ->selectRaw('
                COUNT(*) as total_customers,
                COUNT(CASE WHEN status = \'active\' THEN 1 END) as active_customers,
                COUNT(CASE WHEN is_vip = true THEN 1 END) as vip_customers,
                COUNT(CASE WHEN segment = \'regular\' THEN 1 END) as regular_customers,
                COUNT(CASE WHEN segment = \'premium\' THEN 1 END) as premium_customers,
                SUM(total_orders) as total_orders,
                SUM(total_spent) as total_revenue,
                AVG(average_order_value) as avg_order_value
            ')
            ->first();

        return $stats;
    }

    public function updateSegment($id, $segment)
    {
        $isVip = $segment === 'vip';

        return $this->update($id, [
            'segment' => $segment,
            'is_vip' => $isVip
        ]);
    }

    public function toggleVipStatus($id)
    {
        $customer = $this->findById($id);
        $newVipStatus = !$customer->is_vip;
        $newSegment = $newVipStatus ? 'vip' : 'regular';

        return $this->update($id, [
            'is_vip' => $newVipStatus,
            'segment' => $newSegment
        ]);
    }

    public function getAddresses($customerId)
    {
        return $this->addressTable()
            ->where('customer_id', $customerId)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function createAddress($customerId, array $data)
    {
        $data['customer_id'] = $customerId;
        $data['created_at'] = now();
        $data['updated_at'] = now();

        // If this is the first address, make it default
        $existingCount = $this->addressTable()
            ->where('customer_id', $customerId)
            ->count();

        if ($existingCount === 0) {
            $data['is_default'] = true;
        }

        // If setting as default, unset other defaults
        if (!empty($data['is_default']) && $data['is_default']) {
            $this->addressTable()
                ->where('customer_id', $customerId)
                ->update(['is_default' => false]);
        }

        $id = $this->addressTable()->insertGetId($data);
        return $this->addressTable()->where('id', $id)->first();
    }

    public function updateAddress($addressId, array $data)
    {
        $address = $this->addressTable()->where('id', $addressId)->first();
        if (!$address) {
            throw new \Exception("Address not found");
        }

        // If setting as default, unset other defaults
        if (!empty($data['is_default']) && $data['is_default']) {
            $this->addressTable()
                ->where('customer_id', $address->customer_id)
                ->update(['is_default' => false]);
        }

        $data['updated_at'] = now();
        $this->addressTable()->where('id', $addressId)->update($data);
        return $this->addressTable()->where('id', $addressId)->first();
    }

    public function deleteAddress($addressId)
    {
        return $this->addressTable()->where('id', $addressId)->delete();
    }

    public function setDefaultAddress($customerId, $addressId)
    {
        // Unset all defaults for this customer
        $this->addressTable()
            ->where('customer_id', $customerId)
            ->update(['is_default' => false]);

        // Set the new default
        return $this->addressTable()
            ->where('id', $addressId)
            ->where('customer_id', $customerId)
            ->update(['is_default' => true]);
    }
}
