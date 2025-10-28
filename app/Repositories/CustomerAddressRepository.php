<?php

namespace App\Repositories;

use App\Repositories\Contracts\CustomerAddressRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CustomerAddressRepository implements CustomerAddressRepositoryInterface
{
    protected $table = 'customer_addresses';

    /**
     * Create new customer address
     */
    public function create(array $data)
    {
        // If this is set as default, unset other defaults for this customer
        if (isset($data['is_default']) && $data['is_default']) {
            $this->unsetDefaultForCustomer($data['customer_id']);
        }

        $addressId = DB::table($this->table)->insertGetId([
            'customer_id' => $data['customer_id'],
            'label' => $data['label'],
            'recipient_name' => $data['recipient_name'],
            'phone' => $data['phone'],
            'address_line' => $data['address_line'],
            'city' => $data['city'],
            'province' => $data['province'],
            'postal_code' => $data['postal_code'],
            'country' => $data['country'] ?? 'Indonesia',
            'is_default' => $data['is_default'] ?? false,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $this->findById($addressId);
    }

    /**
     * Get all addresses for a customer
     */
    public function getByCustomerId(int $customerId)
    {
        return DB::table($this->table)
            ->where('customer_id', $customerId)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get address by ID
     */
    public function findById(int $id)
    {
        return DB::table($this->table)->where('id', $id)->first();
    }

    /**
     * Update address
     */
    public function update(int $id, array $data)
    {
        $address = $this->findById($id);

        if (!$address) {
            return null;
        }

        // If this is set as default, unset other defaults for this customer
        if (isset($data['is_default']) && $data['is_default']) {
            $this->unsetDefaultForCustomer($address->customer_id);
        }

        $updateData = array_filter([
            'label' => $data['label'] ?? null,
            'recipient_name' => $data['recipient_name'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address_line' => $data['address_line'] ?? null,
            'city' => $data['city'] ?? null,
            'province' => $data['province'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'country' => $data['country'] ?? null,
            'is_default' => isset($data['is_default']) ? $data['is_default'] : null,
            'latitude' => isset($data['latitude']) ? $data['latitude'] : null,
            'longitude' => isset($data['longitude']) ? $data['longitude'] : null,
            'notes' => isset($data['notes']) ? $data['notes'] : null,
        ], function ($value) {
            return $value !== null;
        });

        $updateData['updated_at'] = now();

        DB::table($this->table)->where('id', $id)->update($updateData);

        return $this->findById($id);
    }

    /**
     * Delete address
     */
    public function delete(int $id)
    {
        return DB::table($this->table)->where('id', $id)->delete();
    }

    /**
     * Set address as default
     */
    public function setAsDefault(int $customerId, int $addressId)
    {
        // First, unset all defaults for this customer
        $this->unsetDefaultForCustomer($customerId);

        // Then set the specified address as default
        DB::table($this->table)
            ->where('id', $addressId)
            ->where('customer_id', $customerId)
            ->update([
                'is_default' => true,
                'updated_at' => now(),
            ]);

        return $this->findById($addressId);
    }

    /**
     * Get default address for customer
     */
    public function getDefaultAddress(int $customerId)
    {
        return DB::table($this->table)
            ->where('customer_id', $customerId)
            ->where('is_default', true)
            ->first();
    }

    /**
     * Unset default flag for all addresses of a customer
     */
    private function unsetDefaultForCustomer(int $customerId)
    {
        DB::table($this->table)
            ->where('customer_id', $customerId)
            ->update(['is_default' => false, 'updated_at' => now()]);
    }
}
