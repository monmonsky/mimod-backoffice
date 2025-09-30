<?php

namespace App\Repositories\Settings;

use App\Repositories\Contracts\GeneralSettingsRepositoryInterface;
use Illuminate\Support\Facades\DB;

class GeneralSettingsRepository implements GeneralSettingsRepositoryInterface
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
     * Create or update setting
     */
    public function upsert(string $key, array $value, ?string $description = null)
    {
        $exists = $this->exists($key);

        if ($exists) {
            return $this->updateValue($key, $value);
        }

        return $this->table()->insert([
            'key' => $key,
            'value' => json_encode($value),
            'description' => $description,
            'updated_at' => now(),
        ]);
    }

    /**
     * Check if setting exists
     */
    public function exists(string $key): bool
    {
        return $this->table()->where('key', $key)->exists();
    }

    /**
     * Delete setting
     */
    public function delete(string $key)
    {
        return $this->table()->where('key', $key)->delete();
    }

    /**
     * Get all settings with specific prefix
     */
    public function getByPrefix(string $prefix)
    {
        return $this->table()
            ->where('key', 'like', $prefix . '%')
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => json_decode($setting->value, true)];
            });
    }
}