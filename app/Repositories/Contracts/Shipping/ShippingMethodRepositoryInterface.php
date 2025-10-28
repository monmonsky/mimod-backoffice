<?php

namespace App\Repositories\Contracts\Shipping;

interface ShippingMethodRepositoryInterface
{
    public function table();
    public function query();
    public function getAll();
    public function getAllActive();
    public function getAllActiveForCustomer($weight = 0);
    public function findById($id);
    public function findByCode($code);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function toggleActive($id);
    public function getConfig($shippingMethodId, $key = null);
    public function setConfig($shippingMethodId, $key, $value, $isEncrypted = false);
    public function calculateCost($shippingMethodId, $weight);
}
