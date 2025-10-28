<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all payment methods with their old configs
        $providers = DB::table('payment_methods')
            ->select('provider')
            ->distinct()
            ->whereNotNull('provider')
            ->get();

        foreach ($providers as $providerRow) {
            $provider = $providerRow->provider;

            // Find the config holder (method with provider_config: true)
            $configHolderId = DB::table('payment_method_config_old')
                ->where('payment_method_id', function($query) use ($provider) {
                    $query->select('id')
                        ->from('payment_methods')
                        ->where('provider', $provider)
                        ->orderBy('id')
                        ->limit(1);
                })
                ->where('key', 'provider_config')
                ->where('value', 'true')
                ->value('payment_method_id');

            if (!$configHolderId) {
                // If no config holder found, use first method with this provider
                $configHolderId = DB::table('payment_methods')
                    ->where('provider', $provider)
                    ->orderBy('id')
                    ->value('id');
            }

            if (!$configHolderId) {
                continue;
            }

            // Get all configs from the holder
            $configs = DB::table('payment_method_config_old')
                ->where('payment_method_id', $configHolderId)
                ->where('key', '!=', 'provider_config') // exclude the flag
                ->get();

            if ($configs->isEmpty()) {
                continue;
            }

            // Create global config
            $globalConfigId = DB::table('payment_method_configs')->insertGetId([
                'name' => ucfirst($provider) . ' Config',
                'provider' => $provider,
                'description' => "Shared configuration for all {$provider} payment methods",
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert config items
            foreach ($configs as $config) {
                DB::table('payment_method_config_items')->insert([
                    'payment_method_config_id' => $globalConfigId,
                    'key' => $config->key,
                    'value' => $config->value,
                    'is_encrypted' => $config->is_encrypted,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Update all payment methods with this provider to reference the global config
            DB::table('payment_methods')
                ->where('provider', $provider)
                ->update(['payment_method_config_id' => $globalConfigId]);
        }

        // Handle method-specific configs (configs that are not on the holder)
        // For now, we'll keep them in the old table for reference
        // Frontend can still use them as overrides
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear foreign keys
        DB::table('payment_methods')->update(['payment_method_config_id' => null]);

        // Clear new tables
        DB::table('payment_method_config_items')->truncate();
        DB::table('payment_method_configs')->truncate();
    }
};
