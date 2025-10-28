<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class ShippingMethodConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // =================================================================
        // PROVIDER-LEVEL CONFIGS (SHARED ACROSS ALL METHODS)
        // =================================================================

        // Get one RajaOngkir method to attach provider config
        $rajaOngkirMethod = DB::table('shipping_methods')->where('provider', 'rajaongkir')->first();

        if ($rajaOngkirMethod) {
            // RajaOngkir Shared Provider Config
            $this->createConfig($rajaOngkirMethod->id, 'provider_config', 'true', false); // Mark as provider-level config
            $this->createConfig($rajaOngkirMethod->id, 'api_key', 'YOUR_RAJAONGKIR_API_KEY_HERE', true);
            $this->createConfig($rajaOngkirMethod->id, 'account_type', 'starter', false); // starter, basic, or pro
            $this->createConfig($rajaOngkirMethod->id, 'origin_city_id', '501', false); // Jakarta (501) - Change to your warehouse city
            $this->createConfig($rajaOngkirMethod->id, 'origin_province_id', '6', false); // DKI Jakarta (6)

            $this->command->info('✓ RajaOngkir provider config created (shared across all RajaOngkir methods)');
        }

        // Get one JNE method to attach provider config (shared tracking URL)
        $jneMethod = DB::table('shipping_methods')->where('provider', 'jne')->first();
        if ($jneMethod) {
            $this->createConfig($jneMethod->id, 'provider_config', 'true', false);
            $this->createConfig($jneMethod->id, 'tracking_url', 'https://www.jne.co.id/id/tracking/trace', false);
            $this->command->info('✓ JNE provider config created');
        }

        // Get one J&T method to attach provider config
        $jntMethod = DB::table('shipping_methods')->where('provider', 'jnt')->first();
        if ($jntMethod) {
            $this->createConfig($jntMethod->id, 'provider_config', 'true', false);
            $this->createConfig($jntMethod->id, 'tracking_url', 'https://www.jet.co.id/track', false);
            $this->command->info('✓ J&T provider config created');
        }

        // Get one SiCepat method to attach provider config
        $sicepatMethod = DB::table('shipping_methods')->where('provider', 'sicepat')->first();
        if ($sicepatMethod) {
            $this->createConfig($sicepatMethod->id, 'provider_config', 'true', false);
            $this->createConfig($sicepatMethod->id, 'tracking_url', 'https://sicepat.com/checkAwb', false);
            $this->command->info('✓ SiCepat provider config created');
        }

        // Get one POS method to attach provider config
        $posMethod = DB::table('shipping_methods')->where('provider', 'pos')->first();
        if ($posMethod) {
            $this->createConfig($posMethod->id, 'provider_config', 'true', false);
            $this->createConfig($posMethod->id, 'tracking_url', 'https://www.posindonesia.co.id/id/tracking', false);
            $this->command->info('✓ POS Indonesia provider config created');
        }

        // Get GoSend method for API config
        $gosendMethod = DB::table('shipping_methods')->where('provider', 'gosend')->first();
        if ($gosendMethod) {
            $this->createConfig($gosendMethod->id, 'provider_config', 'true', false);
            $this->createConfig($gosendMethod->id, 'api_key', 'YOUR_GOJEK_API_KEY_HERE', true);
            $this->createConfig($gosendMethod->id, 'merchant_id', 'YOUR_GOJEK_MERCHANT_ID', false);
            $this->createConfig($gosendMethod->id, 'max_distance_km', '25', false);
            $this->command->info('✓ GoSend provider config created');
        }

        // Get Grab method for API config
        $grabMethod = DB::table('shipping_methods')->where('provider', 'grab')->first();
        if ($grabMethod) {
            $this->createConfig($grabMethod->id, 'provider_config', 'true', false);
            $this->createConfig($grabMethod->id, 'api_key', 'YOUR_GRAB_API_KEY_HERE', true);
            $this->createConfig($grabMethod->id, 'merchant_id', 'YOUR_GRAB_MERCHANT_ID', false);
            $this->createConfig($grabMethod->id, 'max_distance_km', '25', false);
            $this->command->info('✓ Grab provider config created');
        }

        // =================================================================
        // METHOD-SPECIFIC CONFIGS (ONLY UNIQUE SETTINGS)
        // =================================================================

        // RajaOngkir methods - unique courier codes
        $rajaOngkirJne = DB::table('shipping_methods')->where('code', 'rajaongkir_jne')->first();
        if ($rajaOngkirJne) {
            $this->createConfig($rajaOngkirJne->id, 'courier_code', 'jne', false);
        }

        $rajaOngkirTiki = DB::table('shipping_methods')->where('code', 'rajaongkir_tiki')->first();
        if ($rajaOngkirTiki) {
            $this->createConfig($rajaOngkirTiki->id, 'courier_code', 'tiki', false);
        }

        $rajaOngkirPos = DB::table('shipping_methods')->where('code', 'rajaongkir_pos')->first();
        if ($rajaOngkirPos) {
            $this->createConfig($rajaOngkirPos->id, 'courier_code', 'pos', false);
        }

        // Store Courier - unique settings
        $storeCourier = DB::table('shipping_methods')->where('code', 'store_courier')->first();
        if ($storeCourier) {
            $this->createConfig($storeCourier->id, 'coverage_area', 'Jakarta, Tangerang, Bekasi', false);
            $this->createConfig($storeCourier->id, 'phone_number', '081234567890', false);
            $this->createConfig($storeCourier->id, 'notes', 'Pengiriman hanya untuk area Jabodetabek', false);
        }

        // Free Shipping - unique settings
        $freeShipping = DB::table('shipping_methods')->where('code', 'free_shipping')->first();
        if ($freeShipping) {
            $this->createConfig($freeShipping->id, 'min_purchase', '500000', false); // Minimal belanja Rp 500,000
            $this->createConfig($freeShipping->id, 'max_weight', '5000', false); // Maksimal 5 kg
            $this->createConfig($freeShipping->id, 'notes', 'Gratis ongkir untuk pembelian minimal Rp 500,000', false);
        }

        $this->command->info('✓ Shipping method configurations seeded successfully!');
    }

    /**
     * Helper function to create config
     */
    private function createConfig($shippingMethodId, $key, $value, $isEncrypted = false)
    {
        // Encrypt if needed
        $configValue = $isEncrypted ? Crypt::encryptString($value) : $value;

        DB::table('shipping_method_config')->insert([
            'shipping_method_id' => $shippingMethodId,
            'key' => $key,
            'value' => $configValue,
            'is_encrypted' => $isEncrypted,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
