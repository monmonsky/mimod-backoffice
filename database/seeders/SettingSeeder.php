<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'general.store_name',
                'value' => json_encode('Minimoda Store'),
                'description' => 'Store name'
            ],
            [
                'key' => 'general.store_description',
                'value' => json_encode('Premium fashion store for kids'),
                'description' => 'Store description'
            ],
            [
                'key' => 'general.store_email',
                'value' => json_encode('contact@minimoda.com'),
                'description' => 'Store contact email'
            ],
            [
                'key' => 'general.store_phone',
                'value' => json_encode('+62 812-3456-7890'),
                'description' => 'Store contact phone'
            ],
            [
                'key' => 'general.store_address',
                'value' => json_encode('Jl. Contoh No. 123, Jakarta Selatan, DKI Jakarta 12345'),
                'description' => 'Store physical address'
            ],
            [
                'key' => 'general.store_logo',
                'value' => json_encode('/images/logo.png'),
                'description' => 'Store logo URL'
            ],
            [
                'key' => 'general.currency',
                'value' => json_encode('IDR'),
                'description' => 'Store currency'
            ],
            [
                'key' => 'general.timezone',
                'value' => json_encode('Asia/Jakarta'),
                'description' => 'Store timezone'
            ],

            // Email Settings (under general)
            [
                'key' => 'general.email_from_name',
                'value' => json_encode('Minimoda Store'),
                'description' => 'Email sender name'
            ],
            [
                'key' => 'general.email_from_address',
                'value' => json_encode('noreply@minimoda.com'),
                'description' => 'Email sender address'
            ],

            // SEO Settings (under general)
            [
                'key' => 'general.meta_title',
                'value' => json_encode('Minimoda - Premium Kids Fashion Store'),
                'description' => 'Website meta title'
            ],
            [
                'key' => 'general.meta_description',
                'value' => json_encode('Shop premium quality clothing and accessories for kids at Minimoda. Safe, comfortable, and stylish fashion for your little ones.'),
                'description' => 'Website meta description'
            ],
            [
                'key' => 'general.meta_keywords',
                'value' => json_encode('kids fashion, children clothing, baby clothes, kids accessories, premium kids wear'),
                'description' => 'Website meta keywords'
            ],

            // System Settings (under general)
            [
                'key' => 'general.maintenance_mode',
                'value' => json_encode(false),
                'description' => 'Enable/disable maintenance mode'
            ],
            [
                'key' => 'general.allow_registration',
                'value' => json_encode(true),
                'description' => 'Allow customer registration'
            ],
            [
                'key' => 'general.order_prefix',
                'value' => json_encode('INV'),
                'description' => 'Order number prefix'
            ],
            [
                'key' => 'general.items_per_page',
                'value' => json_encode(20),
                'description' => 'Default items per page for pagination'
            ],
            [
                'key' => 'general.low_stock_threshold',
                'value' => json_encode(10),
                'description' => 'Low stock alert threshold'
            ],

            // Payment Settings
            [
                'key' => 'payment.tax_rate',
                'value' => json_encode(11),
                'description' => 'Tax percentage rate (PPN)'
            ],
            [
                'key' => 'payment.tax_enabled',
                'value' => json_encode(true),
                'description' => 'Enable/disable tax calculation'
            ],
            [
                'key' => 'payment.midtrans_client_key',
                'value' => json_encode(''),
                'description' => 'Midtrans client key'
            ],
            [
                'key' => 'payment.midtrans_server_key',
                'value' => json_encode(''),
                'description' => 'Midtrans server key'
            ],
            [
                'key' => 'payment.midtrans_is_production',
                'value' => json_encode(false),
                'description' => 'Midtrans environment (true = production, false = sandbox)'
            ],
            [
                'key' => 'payment.payment_methods',
                'value' => json_encode([
                    'credit_card' => true,
                    'bank_transfer' => true,
                    'e_wallet' => true,
                    'cod' => true
                ]),
                'description' => 'Enabled payment methods'
            ],

            // Shipping Settings
            [
                'key' => 'shipping.origin_city',
                'value' => json_encode('Jakarta Selatan'),
                'description' => 'Shipping origin city'
            ],
            [
                'key' => 'shipping.origin_province',
                'value' => json_encode('DKI Jakarta'),
                'description' => 'Shipping origin province'
            ],
            [
                'key' => 'shipping.origin_postal_code',
                'value' => json_encode('12345'),
                'description' => 'Shipping origin postal code'
            ],
            [
                'key' => 'shipping.rajaongkir_api_key',
                'value' => json_encode(''),
                'description' => 'Raja Ongkir API key'
            ],
            [
                'key' => 'shipping.shipping_methods',
                'value' => json_encode([
                    'jne' => true,
                    'tiki' => true,
                    'pos' => true,
                    'jnt' => true
                ]),
                'description' => 'Enabled shipping couriers'
            ],
            [
                'key' => 'shipping.free_shipping_minimum',
                'value' => json_encode(500000),
                'description' => 'Minimum order amount for free shipping'
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'description' => $setting['description'],
                    'updated_at' => now()
                ]
            );
        }

        $this->command->info('Settings seeded successfully!');
    }
}
