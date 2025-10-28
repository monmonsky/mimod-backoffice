<?php

namespace App\Repositories\Contracts\Customers;

interface CustomerRepositoryInterface
{
    public function query();
    public function getAll();
    public function getAllWithAddresses();
    public function findById($id);
    public function findByIdWithAddresses($id);
    public function findByEmail($email);
    public function findByCustomerCode($code);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getBySegment($segment);
    public function getVipCustomers();
    public function getStatistics();
    public function updateSegment($id, $segment);
    public function toggleVipStatus($id);
    public function getAddresses($customerId);
    public function createAddress($customerId, array $data);
    public function updateAddress($addressId, array $data);
    public function deleteAddress($addressId);
    public function setDefaultAddress($customerId, $addressId);
}
