<?php

namespace App\Repositories\Contracts\Payment;

interface PaymentMethodRepositoryInterface
{
    public function query();
    public function getAll();
    public function getAllActive();
    public function getAllActiveForCustomer($orderAmount = 0);
    public function findById($id);
    public function findByCode($code);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function toggleActive($id);
    public function getConfig($paymentMethodId, $key = null);
    public function setConfig($paymentMethodId, $key, $value, $isEncrypted = false);
}
