<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingMethodSeeder extends Seeder
{
    public function run(): void
    {
        $shippingMethods = [
            // Manual Shipping - JNE
            [
                'code' => 'jne_reg',
                'name' => 'JNE REG',
                'type' => 'manual',
                'provider' => 'jne',
                'description' => 'JNE Regular Service',
                'base_cost' => 10000,
                'cost_per_kg' => 1500,
                'min_weight' => null,
                'max_weight' => 30000,
                'estimated_delivery' => '2-3 hari',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'jne_yes',
                'name' => 'JNE YES',
                'type' => 'manual',
                'provider' => 'jne',
                'description' => 'JNE Yakin Esok Sampai',
                'base_cost' => 25000,
                'cost_per_kg' => 3000,
                'min_weight' => null,
                'max_weight' => 20000,
                'estimated_delivery' => '1 hari',
                'is_active' => true,
                'sort_order' => 2,
            ],

            // Manual Shipping - J&T
            [
                'code' => 'jnt_reg',
                'name' => 'J&T REG',
                'type' => 'manual',
                'provider' => 'jnt',
                'description' => 'J&T Regular Service',
                'base_cost' => 8000,
                'cost_per_kg' => 1200,
                'min_weight' => null,
                'max_weight' => 30000,
                'estimated_delivery' => '2-4 hari',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'code' => 'jnt_express',
                'name' => 'J&T Express',
                'type' => 'manual',
                'provider' => 'jnt',
                'description' => 'J&T Express Service',
                'base_cost' => 15000,
                'cost_per_kg' => 2000,
                'min_weight' => null,
                'max_weight' => 20000,
                'estimated_delivery' => '1-2 hari',
                'is_active' => true,
                'sort_order' => 4,
            ],

            // Manual Shipping - SiCepat
            [
                'code' => 'sicepat_reg',
                'name' => 'SiCepat REG',
                'type' => 'manual',
                'provider' => 'sicepat',
                'description' => 'SiCepat Regular',
                'base_cost' => 9000,
                'cost_per_kg' => 1300,
                'min_weight' => null,
                'max_weight' => 30000,
                'estimated_delivery' => '2-3 hari',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'code' => 'sicepat_best',
                'name' => 'SiCepat BEST',
                'type' => 'manual',
                'provider' => 'sicepat',
                'description' => 'SiCepat Best Service',
                'base_cost' => 12000,
                'cost_per_kg' => 1800,
                'min_weight' => null,
                'max_weight' => 25000,
                'estimated_delivery' => '1-2 hari',
                'is_active' => true,
                'sort_order' => 6,
            ],

            // Manual Shipping - Pos Indonesia
            [
                'code' => 'pos_reg',
                'name' => 'POS Indonesia',
                'type' => 'manual',
                'provider' => 'pos',
                'description' => 'POS Kilat Khusus',
                'base_cost' => 7000,
                'cost_per_kg' => 1000,
                'min_weight' => null,
                'max_weight' => 50000,
                'estimated_delivery' => '3-5 hari',
                'is_active' => true,
                'sort_order' => 7,
            ],

            // Instant Courier
            [
                'code' => 'gosend_instant',
                'name' => 'GoSend Instant',
                'type' => 'manual',
                'provider' => 'gosend',
                'description' => 'GoSend Same Day Delivery',
                'base_cost' => 15000,
                'cost_per_kg' => 5000,
                'min_weight' => null,
                'max_weight' => 20000,
                'estimated_delivery' => 'Hari ini',
                'is_active' => false,
                'sort_order' => 10,
            ],
            [
                'code' => 'grab_instant',
                'name' => 'Grab Express Instant',
                'type' => 'manual',
                'provider' => 'grab',
                'description' => 'Grab Same Day Delivery',
                'base_cost' => 15000,
                'cost_per_kg' => 5000,
                'min_weight' => null,
                'max_weight' => 20000,
                'estimated_delivery' => 'Hari ini',
                'is_active' => false,
                'sort_order' => 11,
            ],

            // Store Courier
            [
                'code' => 'store_courier',
                'name' => 'Kurir Toko',
                'type' => 'custom',
                'provider' => 'custom',
                'description' => 'Pengiriman menggunakan kurir toko',
                'base_cost' => 5000,
                'cost_per_kg' => 0,
                'min_weight' => null,
                'max_weight' => 10000,
                'estimated_delivery' => '1-2 hari',
                'is_active' => true,
                'sort_order' => 20,
            ],

            // Free Shipping (Promo)
            [
                'code' => 'free_shipping',
                'name' => 'Gratis Ongkir',
                'type' => 'custom',
                'provider' => 'custom',
                'description' => 'Gratis ongkir untuk pembelian tertentu',
                'base_cost' => 0,
                'cost_per_kg' => 0,
                'min_weight' => null,
                'max_weight' => 5000,
                'estimated_delivery' => '3-5 hari',
                'is_active' => false,
                'sort_order' => 30,
            ],

            // RajaOngkir Integration (Inactive - need configuration)
            [
                'code' => 'rajaongkir_jne',
                'name' => 'JNE (RajaOngkir)',
                'type' => 'rajaongkir',
                'provider' => 'rajaongkir',
                'description' => 'JNE dengan kalkulasi otomatis via RajaOngkir',
                'base_cost' => 0,
                'cost_per_kg' => 0,
                'min_weight' => null,
                'max_weight' => null,
                'estimated_delivery' => 'Varies',
                'is_active' => false,
                'sort_order' => 40,
            ],
            [
                'code' => 'rajaongkir_tiki',
                'name' => 'TIKI (RajaOngkir)',
                'type' => 'rajaongkir',
                'provider' => 'rajaongkir',
                'description' => 'TIKI dengan kalkulasi otomatis via RajaOngkir',
                'base_cost' => 0,
                'cost_per_kg' => 0,
                'min_weight' => null,
                'max_weight' => null,
                'estimated_delivery' => 'Varies',
                'is_active' => false,
                'sort_order' => 41,
            ],
            [
                'code' => 'rajaongkir_pos',
                'name' => 'POS Indonesia (RajaOngkir)',
                'type' => 'rajaongkir',
                'provider' => 'rajaongkir',
                'description' => 'POS dengan kalkulasi otomatis via RajaOngkir',
                'base_cost' => 0,
                'cost_per_kg' => 0,
                'min_weight' => null,
                'max_weight' => null,
                'estimated_delivery' => 'Varies',
                'is_active' => false,
                'sort_order' => 42,
            ],
        ];

        foreach ($shippingMethods as $method) {
            $method['created_at'] = now();
            $method['updated_at'] = now();
            DB::table('shipping_methods')->insert($method);
        }

        $this->command->info('Shipping methods seeded successfully!');
    }
}
