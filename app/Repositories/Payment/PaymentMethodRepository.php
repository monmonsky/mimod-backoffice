<?php

namespace App\Repositories\Payment;

use App\Repositories\Contracts\Payment\PaymentMethodRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class PaymentMethodRepository implements PaymentMethodRepositoryInterface
{
    protected $tableName = 'payment_methods';
    protected $configTableName = 'payment_method_configs'; // Global config table
    protected $configItemsTableName = 'payment_method_config_items'; // Config items
    protected $overrideTableName = 'payment_method_config_overrides'; // Old table (for method-specific overrides)

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
     * Get active payment methods that are valid for the given order amount
     */
    public function getAllActiveForCustomer($orderAmount = 0)
    {
        $query = $this->table()
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc');

        if ($orderAmount > 0) {
            $query->where(function($q) use ($orderAmount) {
                $q->where(function($q2) use ($orderAmount) {
                    // Check min_amount
                    $q2->whereNull('min_amount')
                       ->orWhere('min_amount', '<=', $orderAmount);
                })->where(function($q2) use ($orderAmount) {
                    // Check max_amount
                    $q2->whereNull('max_amount')
                       ->orWhere('max_amount', '>=', $orderAmount);
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
        // Check if payment method is used in orders
        $usedInOrders = DB::table('order_payments')
            ->where('payment_method_id', $id)
            ->exists();

        if ($usedInOrders) {
            throw new \Exception('Cannot delete payment method that has been used in orders. Please deactivate it instead.');
        }

        // Delete method-specific config overrides
        DB::table($this->overrideTableName)->where('payment_method_id', $id)->delete();

        // Delete payment method
        return $this->table()->where('id', $id)->delete();
    }

    public function toggleActive($id)
    {
        $paymentMethod = $this->findById($id);

        if (!$paymentMethod) {
            throw new \Exception('Payment method not found');
        }

        $newStatus = !$paymentMethod->is_active;

        return $this->update($id, ['is_active' => $newStatus]);
    }

    /**
     * Get payment method configuration with provider-level fallback
     *
     * NEW STRUCTURE:
     * Priority:
     * 1. Method-specific config (from old config table - overrides)
     * 2. Global config (from payment_method_config_id foreign key)
     *
     * If $key is null, return all configs (merged)
     * If $key is provided, return specific config value (decrypted if needed)
     */
    public function getConfig($paymentMethodId, $key = null)
    {
        $paymentMethod = $this->findById($paymentMethodId);

        if (!$paymentMethod) {
            return $key === null ? [] : null;
        }

        if ($key === null) {
            // Get all configs - merge global + method-specific
            $globalConfigs = [];
            $methodOverrides = [];

            // First, get global configs from payment_method_config_id
            if ($paymentMethod->payment_method_config_id) {
                $configItems = DB::table($this->configItemsTableName)
                    ->where('payment_method_config_id', $paymentMethod->payment_method_config_id)
                    ->get();

                foreach ($configItems as $item) {
                    $value = $item->is_encrypted ? Crypt::decryptString($item->value) : $item->value;
                    $globalConfigs[$item->key] = $value;
                }
            }

            // Then, get method-specific overrides from old config table
            $overrides = DB::table($this->overrideTableName)
                ->where('payment_method_id', $paymentMethodId)
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
                ->where('payment_method_id', $paymentMethodId)
                ->where('key', $key)
                ->first();

            if ($override) {
                return $override->is_encrypted ? Crypt::decryptString($override->value) : $override->value;
            }

            // Fallback to global config
            if ($paymentMethod->payment_method_config_id) {
                $configItem = DB::table($this->configItemsTableName)
                    ->where('payment_method_config_id', $paymentMethod->payment_method_config_id)
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
     * Set payment method configuration
     *
     * NEW STRUCTURE:
     * This will set config as method-specific override (stored in old config table)
     * To update global config, use updateGlobalConfig() method instead
     */
    public function setConfig($paymentMethodId, $key, $value, $isEncrypted = false)
    {
        $encryptedValue = $isEncrypted ? Crypt::encryptString($value) : $value;

        $existing = DB::table($this->overrideTableName)
            ->where('payment_method_id', $paymentMethodId)
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
                'payment_method_id' => $paymentMethodId,
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
            ->where('payment_method_config_id', $globalConfigId)
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
                'payment_method_config_id' => $globalConfigId,
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
    public function deleteConfigOverride($paymentMethodId, $key)
    {
        return DB::table($this->overrideTableName)
            ->where('payment_method_id', $paymentMethodId)
            ->where('key', $key)
            ->delete();
    }
}
