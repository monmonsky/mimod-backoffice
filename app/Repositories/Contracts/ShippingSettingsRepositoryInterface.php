<?php

namespace App\Repositories\Contracts;

interface ShippingSettingsRepositoryInterface
{
    public function getByKey(string $key);
    public function getValue(string $key, $default = null);
    public function updateValue(string $key, array $value);
    public function getAllShippingSettings();
}
