<?php

namespace App\Repositories\Contracts;

interface CustomerAddressRepositoryInterface
{
    /**
     * Create new customer address
     */
    public function create(array $data);

    /**
     * Get all addresses for a customer
     */
    public function getByCustomerId(int $customerId);

    /**
     * Get address by ID
     */
    public function findById(int $id);

    /**
     * Update address
     */
    public function update(int $id, array $data);

    /**
     * Delete address
     */
    public function delete(int $id);

    /**
     * Set address as default
     */
    public function setAsDefault(int $customerId, int $addressId);

    /**
     * Get default address for customer
     */
    public function getDefaultAddress(int $customerId);
}
