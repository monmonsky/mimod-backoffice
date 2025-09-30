<?php

namespace App\Repositories\Settings;

use Illuminate\Support\Facades\DB;

class PaymentSettingsRepository
{
    protected $tableName = 'settings';

    /**
     * Get fresh query builder instance
     */
    protected function table()
    {
        return DB::table($this->tableName);
    }

    /**
     * Get setting by key
     */
    public function getByKey(string $key)
    {
        return $this->table()->where('key', $key)->first();
    }

    /**
     * Get setting value by key
     */
    public function getValue(string $key, $default = null)
    {
        $setting = $this->getByKey($key);
        if (!$setting) {
            return $default;
        }
        return json_decode($setting->value, true);
    }

    /**
     * Update setting value
     */
    public function updateValue(string $key, array $value)
    {
        return $this->table()
            ->where('key', $key)
            ->update([
                'value' => json_encode($value),
                'updated_at' => now(),
            ]);
    }

    /**
     * Get all payment settings
     */
    public function getAllPaymentSettings()
    {
        return $this->table()
            ->where('key', 'like', 'payment.%')
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => json_decode($setting->value, true)];
            });
    }
}