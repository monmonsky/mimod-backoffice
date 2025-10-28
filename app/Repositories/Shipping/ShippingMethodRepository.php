<?php

namespace App\Repositories\Shipping;

use App\Repositories\Contracts\Shipping\ShippingMethodRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class ShippingMethodRepository implements ShippingMethodRepositoryInterface
{
    protected $tableName = 'shipping_methods';
    protected $configTableName = 'shipping_method_configs'; // Global config table
    protected $configItemsTableName = 'shipping_method_config_items'; // Config items
    protected $overrideTableName = 'shipping_method_config_overrides'; // Old table (for method-specific overrides)

    public function table()
    {
        return DB::table($this->tableName);
    }

    public function query()
    {
        return $this->table();
    }

    public function getAll()
    {
        return $this->table()
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();
    }

    public function getAllActive()
    {
        return $this->table()
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Get active shipping methods that are valid for the given weight
     */
    public function getAllActiveForCustomer($weight = 0)
    {
        $query = $this->table()
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc');

        if ($weight > 0) {
            $query->where(function($q) use ($weight) {
                $q->where(function($q2) use ($weight) {
                    // Check min_weight
                    $q2->whereNull('min_weight')
                       ->orWhere('min_weight', '<=', $weight);
                })->where(function($q2) use ($weight) {
                    // Check max_weight
                    $q2->whereNull('max_weight')
                       ->orWhere('max_weight', '>=', $weight);
                });
            });
        }

        return $query->get();
    }

    public function findById($id)
    {
        return $this->table()->where('id', $id)->first();
    }

    public function findByCode($code)
    {
        return $this->table()->where('code', $code)->first();
    }

    public function create(array $data)
    {
        $data['created_at'] = now();
        $data['updated_at'] = now();

        $id = $this->table()->insertGetId($data);

        return $this->findById($id);
    }

    public function update($id, array $data)
    {
        $data['updated_at'] = now();

        $this->table()->where('id', $id)->update($data);

        return $this->findById($id);
    }

    public function delete($id)
    {
        // Check if shipping method is used in orders
        $usedInOrders = DB::table('order_shipments')
            ->where('shipping_method_id', $id)
            ->exists();

        if ($usedInOrders) {
            throw new \Exception('Cannot delete shipping method that has been used in orders. Please deactivate it instead.');
        }

        // Delete method-specific config overrides
        DB::table($this->overrideTableName)->where('shipping_method_id', $id)->delete();

        // Delete shipping method
        return $this->table()->where('id', $id)->delete();
    }

    public function toggleActive($id)
    {
        $shippingMethod = $this->findById($id);

        if (!$shippingMethod) {
            throw new \Exception('Shipping method not found');
        }

        $newStatus = !$shippingMethod->is_active;

        return $this->update($id, ['is_active' => $newStatus]);
    }

    /**
     * Get shipping method configuration with provider-level fallback
     *
     * Priority:
     * 1. Method-specific config (from the method's own config)
     * 2. Provider-level config (from the first method with same provider that has provider_config=true)
     *
     * If $key is null, return all configs (merged)
     * If $key is provided, return specific config value (decrypted if needed)
     */
    public function getConfig($shippingMethodId, $key = null)
    {
        $shippingMethod = $this->findById($shippingMethodId);

        if (!$shippingMethod) {
            return $key === null ? [] : null;
        }

        if ($key === null) {
            // Get all configs - merge global + method-specific
            $globalConfigs = [];
            $methodOverrides = [];

            // First, get global configs from shipping_method_config_id
            if ($shippingMethod->shipping_method_config_id) {
                $configItems = DB::table($this->configItemsTableName)
                    ->where('shipping_method_config_id', $shippingMethod->shipping_method_config_id)
                    ->get();

                foreach ($configItems as $item) {
                    $value = $item->is_encrypted ? Crypt::decryptString($item->value) : $item->value;
                    $globalConfigs[$item->key] = $value;
                }
            }

            // Then, get method-specific overrides from old config table
            $overrides = DB::table($this->overrideTableName)
                ->where('shipping_method_id', $shippingMethodId)
                ->where('key', '!=', 'provider_config') // Exclude old flag
                ->get();

            foreach ($overrides as $override) {
                $value = $override->is_encrypted ? Crypt::decryptString($override->value) : $override->value;
                $methodOverrides[$override->key] = $value;
            }

            // Merge: method-specific overrides global config
            return array_merge($globalConfigs, $methodOverrides);
        } else {
            // Get specific config - check method override first, then global
            $override = DB::table($this->overrideTableName)
                ->where('shipping_method_id', $shippingMethodId)
                ->where('key', $key)
                ->first();

            if ($override) {
                return $override->is_encrypted ? Crypt::decryptString($override->value) : $override->value;
            }

            // Fallback to global config
            if ($shippingMethod->shipping_method_config_id) {
                $configItem = DB::table($this->configItemsTableName)
                    ->where('shipping_method_config_id', $shippingMethod->shipping_method_config_id)
                    ->where('key', $key)
                    ->first();

                if ($configItem) {
                    return $configItem->is_encrypted ? Crypt::decryptString($configItem->value) : $configItem->value;
                }
            }

            return null;
        }
    }

    /**
     * Set shipping method configuration (as override)
     */
    public function setConfig($shippingMethodId, $key, $value, $isEncrypted = false)
    {
        $encryptedValue = $isEncrypted ? Crypt::encryptString($value) : $value;

        $existing = DB::table($this->overrideTableName)
            ->where('shipping_method_id', $shippingMethodId)
            ->where('key', $key)
            ->first();

        if ($existing) {
            // Update
            DB::table($this->overrideTableName)
                ->where('id', $existing->id)
                ->update([
                    'value' => $encryptedValue,
                    'is_encrypted' => $isEncrypted,
                    'updated_at' => now()
                ]);
        } else {
            // Insert as override
            DB::table($this->overrideTableName)->insert([
                'shipping_method_id' => $shippingMethodId,
                'key' => $key,
                'value' => $encryptedValue,
                'is_encrypted' => $isEncrypted,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return true;
    }

    /**
     * Update global config (affects all methods with this config)
     */
    public function updateGlobalConfig($globalConfigId, $key, $value, $isEncrypted = false)
    {
        $encryptedValue = $isEncrypted ? Crypt::encryptString($value) : $value;

        $existing = DB::table($this->configItemsTableName)
            ->where('shipping_method_config_id', $globalConfigId)
            ->where('key', $key)
            ->first();

        if ($existing) {
            // Update
            DB::table($this->configItemsTableName)
                ->where('id', $existing->id)
                ->update([
                    'value' => $encryptedValue,
                    'is_encrypted' => $isEncrypted,
                    'updated_at' => now()
                ]);
        } else {
            // Insert new config item
            DB::table($this->configItemsTableName)->insert([
                'shipping_method_config_id' => $globalConfigId,
                'key' => $key,
                'value' => $encryptedValue,
                'is_encrypted' => $isEncrypted,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return true;
    }

    /**
     * Get global config by provider
     */
    public function getGlobalConfigByProvider($provider)
    {
        return DB::table($this->configTableName)
            ->where('provider', $provider)
            ->first();
    }

    /**
     * Delete method-specific override (will fallback to global config)
     */
    public function deleteConfigOverride($shippingMethodId, $key)
    {
        return DB::table($this->overrideTableName)
            ->where('shipping_method_id', $shippingMethodId)
            ->where('key', $key)
            ->delete();
    }

    /**
     * Calculate shipping cost based on weight
     * For manual/custom methods, uses base_cost + (cost_per_kg * weight)
     * For API-based methods (rajaongkir), would call the API
     */
    public function calculateCost($shippingMethodId, $weight)
    {
        $method = $this->findById($shippingMethodId);

        if (!$method) {
            throw new \Exception('Shipping method not found');
        }

        // For manual/custom methods, calculate from base_cost and cost_per_kg
        if ($method->type === 'manual' || $method->type === 'custom') {
            $weightInKg = $weight / 1000; // Convert grams to kg
            $cost = $method->base_cost + ($method->cost_per_kg * $weightInKg);

            return [
                'cost' => $cost,
                'estimated_delivery' => $method->estimated_delivery,
                'type' => 'manual'
            ];
        }

        // For RajaOngkir, would integrate with API
        if ($method->type === 'rajaongkir') {
            // TODO: Implement RajaOngkir API integration
            // For now, return error
            throw new \Exception('RajaOngkir integration not yet implemented. Please configure API in settings.');
        }

        throw new \Exception('Unknown shipping method type');
    }
}
