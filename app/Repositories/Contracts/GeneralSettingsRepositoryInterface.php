<?php

namespace App\Repositories\Contracts;

interface GeneralSettingsRepositoryInterface
{
    public function getByKey(string $key);
    public function getValue(string $key, $default = null);
    public function updateValue(string $key, array $value);
    public function upsert(string $key, array $value, ?string $description = null);
    public function exists(string $key): bool;
    public function delete(string $key);
    public function getByPrefix(string $prefix);
}
