<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Store Information
            [
                'key' => 'store.info',
                'value' => json_encode([
                    'name' => 'Minimoda',
                    'tagline' => 'Fashion for Little Stars',
                    'description' => 'Premium children fashion e-commerce',
                    'logo' => null,
                    'favicon' => null,
                ]),
                'description' => 'Store basic information and branding',
            ],
            [
                'key' => 'store.contact',
                'value' => json_encode([
                    'email' => 'info@minimoda.com',
                    'phone' => '+62 21 1234 5678',
                    'whatsapp' => '+62 812 3456 7890',
                ]),
                'description' => 'Store contact information',
            ],
            [
                'key' => 'store.address',
                'value' => json_encode([
                    'street' => 'Jl. Raya No. 123',
                    'city' => 'Jakarta',
                    'state' => 'DKI Jakarta',
                    'postal_code' => '12345',
                    'country' => 'Indonesia',
                ]),
                'description' => 'Store physical address',
            ],
            [
                'key' => 'store.social',
                'value' => json_encode([
                    'facebook' => '',
                    'instagram' => '',
                    'twitter' => '',
                    'tiktok' => '',
                    'youtube' => '',
                ]),
                'description' => 'Social media links',
            ],
            [
                'key' => 'store.operating_hours',
                'value' => json_encode([
                    'hours' => "Monday - Friday: 09:00 - 18:00\nSaturday: 09:00 - 15:00\nSunday: Closed",
                ]),
                'description' => 'Store operating hours',
            ],

            // Email Settings
            [
                'key' => 'email.smtp',
                'value' => json_encode([
                    'host' => 'smtp.mailtrap.io',
                    'port' => 587,
                    'username' => '',
                    'password' => '',
                    'encryption' => 'tls',
                    'from_email' => 'noreply@minimoda.com',
                    'from_name' => 'Minimoda',
                ]),
                'description' => 'SMTP email configuration',
            ],
            [
                'key' => 'email.notifications',
                'value' => json_encode([
                    'order_confirmation' => true,
                    'order_shipped' => true,
                    'order_delivered' => true,
                    'password_reset' => true,
                    'welcome_email' => true,
                    'newsletter' => false,
                ]),
                'description' => 'Email notification settings',
            ],

            // SEO & Meta
            [
                'key' => 'seo.basic',
                'value' => json_encode([
                    'site_title' => 'Minimoda - Fashion for Little Stars',
                    'meta_description' => 'Premium children fashion e-commerce platform in Indonesia',
                    'meta_keywords' => 'kids fashion, children clothing, baby clothes, fashion anak',
                    'google_analytics_id' => '',
                    'google_search_console' => '',
                    'facebook_pixel_id' => '',
                ]),
                'description' => 'Basic SEO settings',
            ],
            [
                'key' => 'seo.opengraph',
                'value' => json_encode([
                    'og_title' => 'Minimoda - Fashion for Little Stars',
                    'og_description' => 'Premium children fashion e-commerce',
                    'og_image' => '',
                    'og_type' => 'website',
                ]),
                'description' => 'Open Graph meta tags for social sharing',
            ],
            [
                'key' => 'seo.twitter',
                'value' => json_encode([
                    'twitter_card' => 'summary_large_image',
                    'twitter_site' => '@minimoda',
                    'twitter_title' => 'Minimoda - Fashion for Little Stars',
                    'twitter_description' => 'Premium children fashion e-commerce',
                    'twitter_image' => '',
                ]),
                'description' => 'Twitter Card meta tags',
            ],
            [
                'key' => 'seo.schema',
                'value' => json_encode([
                    'organization_name' => 'Minimoda',
                    'organization_url' => 'https://minimoda.com',
                    'organization_logo' => '',
                    'contact_type' => 'Customer Service',
                    'contact_phone' => '+62 21 1234 5678',
                ]),
                'description' => 'Schema.org structured data',
            ],
            [
                'key' => 'seo.robots',
                'value' => json_encode([
                    'robots_txt' => "User-agent: *\nDisallow: /admin/\nDisallow: /cart/\nDisallow: /checkout/\nAllow: /\n\nSitemap: https://minimoda.com/sitemap.xml",
                ]),
                'description' => 'Robots.txt configuration',
            ],

            // System Configuration
            [
                'key' => 'system.general',
                'value' => json_encode([
                    'timezone' => 'Asia/Jakarta',
                    'date_format' => 'Y-m-d',
                    'time_format' => 'H:i:s',
                    'default_language' => 'id',
                    'currency' => 'IDR',
                    'currency_symbol' => 'Rp',
                    'currency_position' => 'before',
                    'decimal_separator' => ',',
                    'thousand_separator' => '.',
                ]),
                'description' => 'General system settings',
            ],
            [
                'key' => 'system.security',
                'value' => json_encode([
                    'min_password_length' => 8,
                    'require_uppercase' => true,
                    'require_number' => true,
                    'require_special_char' => false,
                    'enable_2fa' => false,
                    'session_timeout' => 120,
                    'max_login_attempts' => 5,
                    'lockout_duration' => 15,
                ]),
                'description' => 'Security settings',
            ],
            [
                'key' => 'system.order',
                'value' => json_encode([
                    'order_prefix' => 'ORD',
                    'auto_cancel_unpaid' => true,
                    'unpaid_timeout_hours' => 24,
                    'enable_guest_checkout' => true,
                    'min_order_amount' => 0,
                    'allow_backorders' => false,
                ]),
                'description' => 'Order processing settings',
            ],
            [
                'key' => 'system.inventory',
                'value' => json_encode([
                    'track_inventory' => true,
                    'low_stock_threshold' => 5,
                    'out_of_stock_threshold' => 0,
                    'enable_stock_notification' => true,
                    'stock_notification_email' => 'inventory@minimoda.com',
                ]),
                'description' => 'Inventory management settings',
            ],
            [
                'key' => 'system.cache',
                'value' => json_encode([
                    'enable_cache' => true,
                    'cache_driver' => 'redis',
                    'cache_lifetime' => 3600,
                ]),
                'description' => 'Cache configuration',
            ],
            [
                'key' => 'system.maintenance',
                'value' => json_encode([
                    'maintenance_mode' => false,
                    'maintenance_message' => 'We are currently performing maintenance. Please check back soon.',
                    'maintenance_end_time' => null,
                ]),
                'description' => 'Maintenance mode settings',
            ],

            // Payment Settings
            [
                'key' => 'payment.midtrans',
                'value' => json_encode([
                    'enabled' => true,
                    'environment' => 'sandbox', // sandbox or production
                    'merchant_id' => '',
                    'client_key_sandbox' => '',
                    'server_key_sandbox' => '',
                    'client_key_production' => '',
                    'server_key_production' => '',
                    'snap_url_sandbox' => 'https://app.sandbox.midtrans.com/snap/snap.js',
                    'snap_url_production' => 'https://app.midtrans.com/snap/snap.js',
                    // Payment Methods
                    'enable_credit_card' => true,
                    'enable_bca_va' => true,
                    'enable_bni_va' => true,
                    'enable_bri_va' => true,
                    'enable_mandiri_va' => true,
                    'enable_gopay' => true,
                    'enable_shopeepay' => true,
                    'enable_qris' => true,
                    'enable_indomaret' => true,
                    'enable_alfamart' => true,
                    // Transaction Settings
                    'sanitize' => true,
                    'enable_3d_secure' => true,
                    'max_installment_tenor' => 12,
                    'transaction_timeout_hours' => 24,
                ]),
                'description' => 'Midtrans payment gateway configuration',
            ],
            [
                'key' => 'payment.bank_transfer',
                'value' => json_encode([
                    'enabled' => true,
                    'accounts' => [
                        [
                            'bank_name' => 'BCA',
                            'account_number' => '1234567890',
                            'account_holder' => 'PT Minimoda Indonesia',
                        ],
                        [
                            'bank_name' => 'Mandiri',
                            'account_number' => '0987654321',
                            'account_holder' => 'PT Minimoda Indonesia',
                        ],
                    ],
                    'upload_proof_required' => true,
                    'auto_verify' => false,
                ]),
                'description' => 'Manual bank transfer settings',
            ],
            [
                'key' => 'payment.cod',
                'value' => json_encode([
                    'enabled' => true,
                    'flat_fee' => 5000,
                    'max_order_amount' => 5000000,
                    'available_areas' => ['Jakarta', 'Bandung', 'Surabaya'],
                ]),
                'description' => 'Cash on Delivery (COD) settings',
            ],
            [
                'key' => 'payment.tax',
                'value' => json_encode([
                    'enable_tax' => true,
                    'tax_included_in_price' => false,
                    'default_tax_rate' => 11, // PPN 11%
                    'tax_rates' => [
                        [
                            'name' => 'PPN',
                            'rate' => 11,
                            'country' => 'Indonesia',
                            'state' => 'All',
                            'is_active' => true,
                        ],
                    ],
                    'tax_classes' => [
                        [
                            'name' => 'Standard',
                            'description' => 'Standard tax rate for all products',
                            'rate' => 11,
                        ],
                        [
                            'name' => 'Reduced',
                            'description' => 'Reduced tax rate',
                            'rate' => 5,
                        ],
                        [
                            'name' => 'Zero',
                            'description' => 'No tax',
                            'rate' => 0,
                        ],
                    ],
                ]),
                'description' => 'Tax configuration',
            ],

            // Shipping Settings
            [
                'key' => 'shipping.rajaongkir',
                'value' => json_encode([
                    'enabled' => true,
                    'account_type' => 'starter', // starter, basic, pro
                    'api_key' => '',
                    'base_url' => 'https://api.rajaongkir.com/starter',
                    // Available Couriers
                    'couriers' => [
                        'jne' => ['enabled' => true, 'name' => 'JNE'],
                        'pos' => ['enabled' => true, 'name' => 'POS Indonesia'],
                        'tiki' => ['enabled' => true, 'name' => 'TIKI'],
                        'jnt' => ['enabled' => true, 'name' => 'J&T Express'],
                        'sicepat' => ['enabled' => true, 'name' => 'SiCepat'],
                        'anteraja' => ['enabled' => true, 'name' => 'AnterAja'],
                    ],
                    // Cache Settings
                    'cache_enabled' => true,
                    'cache_duration' => 24, // hours
                    // Additional Settings
                    'enable_insurance' => true,
                    'insurance_rate' => 0.002, // 0.2%
                    'price_markup_type' => 'percentage', // percentage or flat
                    'price_markup_value' => 0, // 10% or 5000
                    'round_up_cost' => true,
                ]),
                'description' => 'RajaOngkir shipping API configuration',
            ],
            [
                'key' => 'shipping.origin',
                'value' => json_encode([
                    'primary' => [
                        'name' => 'Main Warehouse',
                        'province_id' => 6,
                        'province_name' => 'DKI Jakarta',
                        'city_id' => 152,
                        'city_name' => 'Jakarta Selatan',
                        'address' => 'Jl. Raya No. 123',
                        'postal_code' => '12345',
                        'phone' => '+62 21 1234 5678',
                        'is_active' => true,
                    ],
                    'additional' => [],
                ]),
                'description' => 'Shipping origin addresses (warehouses)',
            ],
            [
                'key' => 'shipping.methods',
                'value' => json_encode([
                    'flat_rate' => [
                        'enabled' => false,
                        'name' => 'Flat Rate Shipping',
                        'cost' => 10000,
                        'tax_status' => 'taxable',
                    ],
                    'free_shipping' => [
                        'enabled' => true,
                        'min_order_amount' => 500000,
                        'name' => 'Free Shipping',
                    ],
                    'local_pickup' => [
                        'enabled' => true,
                        'name' => 'Local Pickup',
                        'cost' => 0,
                        'locations' => [
                            'Jakarta Selatan - Jl. Raya No. 123',
                        ],
                    ],
                ]),
                'description' => 'Alternative shipping methods',
            ],
        ];

        foreach ($settings as $setting) {
            \DB::table('settings')->insert([
                'key' => $setting['key'],
                'value' => $setting['value'],
                'description' => $setting['description'],
                'updated_at' => now(),
            ]);
        }
    }
}
