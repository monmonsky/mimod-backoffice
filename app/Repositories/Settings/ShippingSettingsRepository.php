<?php

namespace App\Repositories\Settings;

use App\Repositories\Contracts\ShippingSettingsRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ShippingSettingsRepository implements ShippingSettingsRepositoryInterface
{
    protected $tableName = 'settings';

    /**
     * Constructor
     */
    public function __construct()
    {
        // Initialize if needed
    }

    /**
     * Get fresh query builder instance
     */
    private function table()
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
     * Get all shipping settings
     */
    public function getAllShippingSettings()
    {
        return $this->table()
            ->where('key', 'like', 'shipping.%')
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => json_decode($setting->value, true)];
            });
    }
}