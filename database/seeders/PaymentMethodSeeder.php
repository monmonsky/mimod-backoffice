<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            // Manual Bank Transfer
            [
                'code' => 'bank_transfer_bca',
                'name' => 'Transfer Bank BCA',
                'type' => 'bank_transfer',
                'provider' => 'manual',
                'description' => 'Transfer manual ke rekening BCA',
                'instructions' => '<p>Silakan transfer ke rekening BCA berikut:</p><p><strong>Bank:</strong> BCA<br><strong>No. Rekening:</strong> 1234567890<br><strong>Atas Nama:</strong> PT Minimoda Indonesia</p><p>Setelah transfer, mohon konfirmasi pembayaran dengan mengirimkan bukti transfer.</p>',
                'fee_percentage' => 0,
                'fee_fixed' => 0,
                'min_amount' => 10000,
                'max_amount' => null,
                'expired_duration' => 1440, // 24 hours
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'bank_transfer_mandiri',
                'name' => 'Transfer Bank Mandiri',
                'type' => 'bank_transfer',
                'provider' => 'manual',
                'description' => 'Transfer manual ke rekening Mandiri',
                'instructions' => '<p>Silakan transfer ke rekening Mandiri berikut:</p><p><strong>Bank:</strong> Mandiri<br><strong>No. Rekening:</strong> 0987654321<br><strong>Atas Nama:</strong> PT Minimoda Indonesia</p><p>Setelah transfer, mohon konfirmasi pembayaran dengan mengirimkan bukti transfer.</p>',
                'fee_percentage' => 0,
                'fee_fixed' => 0,
                'min_amount' => 10000,
                'max_amount' => null,
                'expired_duration' => 1440,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'bank_transfer_bni',
                'name' => 'Transfer Bank BNI',
                'type' => 'bank_transfer',
                'provider' => 'manual',
                'description' => 'Transfer manual ke rekening BNI',
                'instructions' => '<p>Silakan transfer ke rekening BNI berikut:</p><p><strong>Bank:</strong> BNI<br><strong>No. Rekening:</strong> 1122334455<br><strong>Atas Nama:</strong> PT Minimoda Indonesia</p><p>Setelah transfer, mohon konfirmasi pembayaran dengan mengirimkan bukti transfer.</p>',
                'fee_percentage' => 0,
                'fee_fixed' => 0,
                'min_amount' => 10000,
                'max_amount' => null,
                'expired_duration' => 1440,
                'is_active' => true,
                'sort_order' => 3,
            ],

            // Midtrans - Virtual Account
            [
                'code' => 'midtrans_bca_va',
                'name' => 'BCA Virtual Account',
                'type' => 'virtual_account',
                'provider' => 'midtrans',
                'description' => 'Bayar menggunakan BCA Virtual Account melalui Midtrans',
                'instructions' => '<p>Nomor Virtual Account akan dikirimkan setelah order dibuat.</p><p>Anda dapat melakukan pembayaran melalui:</p><ul><li>ATM BCA</li><li>Internet Banking BCA (KlikBCA)</li><li>Mobile Banking BCA (m-BCA)</li></ul>',
                'fee_percentage' => 0,
                'fee_fixed' => 4000,
                'min_amount' => 10000,
                'max_amount' => 50000000,
                'expired_duration' => 1440,
                'is_active' => false, // Inactive until Midtrans configured
                'sort_order' => 10,
            ],
            [
                'code' => 'midtrans_bni_va',
                'name' => 'BNI Virtual Account',
                'type' => 'virtual_account',
                'provider' => 'midtrans',
                'description' => 'Bayar menggunakan BNI Virtual Account melalui Midtrans',
                'instructions' => '<p>Nomor Virtual Account akan dikirimkan setelah order dibuat.</p><p>Anda dapat melakukan pembayaran melalui:</p><ul><li>ATM BNI</li><li>Internet Banking BNI</li><li>Mobile Banking BNI</li></ul>',
                'fee_percentage' => 0,
                'fee_fixed' => 4000,
                'min_amount' => 10000,
                'max_amount' => 50000000,
                'expired_duration' => 1440,
                'is_active' => false,
                'sort_order' => 11,
            ],
            [
                'code' => 'midtrans_mandiri_va',
                'name' => 'Mandiri Virtual Account',
                'type' => 'virtual_account',
                'provider' => 'midtrans',
                'description' => 'Bayar menggunakan Mandiri Virtual Account melalui Midtrans',
                'instructions' => '<p>Nomor Virtual Account akan dikirimkan setelah order dibuat.</p><p>Anda dapat melakukan pembayaran melalui:</p><ul><li>ATM Mandiri</li><li>Internet Banking Mandiri</li><li>Livin by Mandiri</li></ul>',
                'fee_percentage' => 0,
                'fee_fixed' => 4000,
                'min_amount' => 10000,
                'max_amount' => 50000000,
                'expired_duration' => 1440,
                'is_active' => false,
                'sort_order' => 12,
            ],
            [
                'code' => 'midtrans_permata_va',
                'name' => 'Permata Virtual Account',
                'type' => 'virtual_account',
                'provider' => 'midtrans',
                'description' => 'Bayar menggunakan Permata Virtual Account melalui Midtrans',
                'instructions' => '<p>Nomor Virtual Account akan dikirimkan setelah order dibuat.</p><p>Anda dapat melakukan pembayaran melalui:</p><ul><li>ATM Permata</li><li>PermataNet</li><li>PermataMobile</li></ul>',
                'fee_percentage' => 0,
                'fee_fixed' => 4000,
                'min_amount' => 10000,
                'max_amount' => 50000000,
                'expired_duration' => 1440,
                'is_active' => false,
                'sort_order' => 13,
            ],

            // Midtrans - E-Wallet
            [
                'code' => 'midtrans_gopay',
                'name' => 'GoPay',
                'type' => 'e_wallet',
                'provider' => 'midtrans',
                'description' => 'Bayar menggunakan GoPay melalui Midtrans',
                'instructions' => '<p>Anda akan diarahkan ke aplikasi Gojek untuk menyelesaikan pembayaran.</p><p>Pastikan aplikasi Gojek sudah terinstall di smartphone Anda.</p>',
                'fee_percentage' => 2,
                'fee_fixed' => 0,
                'min_amount' => 10000,
                'max_amount' => 10000000,
                'expired_duration' => 15, // 15 minutes
                'is_active' => false,
                'sort_order' => 20,
            ],
            [
                'code' => 'midtrans_shopeepay',
                'name' => 'ShopeePay',
                'type' => 'e_wallet',
                'provider' => 'midtrans',
                'description' => 'Bayar menggunakan ShopeePay melalui Midtrans',
                'instructions' => '<p>Anda akan diarahkan ke aplikasi Shopee untuk menyelesaikan pembayaran.</p><p>Pastikan aplikasi Shopee sudah terinstall di smartphone Anda.</p>',
                'fee_percentage' => 2,
                'fee_fixed' => 0,
                'min_amount' => 10000,
                'max_amount' => 10000000,
                'expired_duration' => 15,
                'is_active' => false,
                'sort_order' => 21,
            ],

            // Midtrans - QRIS
            [
                'code' => 'midtrans_qris',
                'name' => 'QRIS',
                'type' => 'qris',
                'provider' => 'midtrans',
                'description' => 'Bayar menggunakan QRIS melalui berbagai aplikasi pembayaran',
                'instructions' => '<p>Scan QR Code yang diberikan menggunakan aplikasi pembayaran favorit Anda:</p><ul><li>GoPay</li><li>OVO</li><li>DANA</li><li>ShopeePay</li><li>LinkAja</li><li>Dan aplikasi pembayaran lainnya yang mendukung QRIS</li></ul>',
                'fee_percentage' => 0.7,
                'fee_fixed' => 0,
                'min_amount' => 1500,
                'max_amount' => 10000000,
                'expired_duration' => 30, // 30 minutes
                'is_active' => false,
                'sort_order' => 30,
            ],

            // Midtrans - Credit Card
            [
                'code' => 'midtrans_credit_card',
                'name' => 'Credit Card',
                'type' => 'credit_card',
                'provider' => 'midtrans',
                'description' => 'Bayar menggunakan Kartu Kredit (Visa, MasterCard, JCB)',
                'instructions' => '<p>Anda dapat melakukan pembayaran menggunakan:</p><ul><li>Visa</li><li>MasterCard</li><li>JCB</li><li>Amex</li></ul><p>Transaksi Anda dilindungi dengan 3D Secure.</p>',
                'fee_percentage' => 2.9,
                'fee_fixed' => 2000,
                'min_amount' => 10000,
                'max_amount' => null,
                'expired_duration' => 60, // 60 minutes
                'is_active' => false,
                'sort_order' => 40,
            ],

            // COD
            [
                'code' => 'cod',
                'name' => 'COD (Cash on Delivery)',
                'type' => 'cod',
                'provider' => null,
                'description' => 'Bayar di tempat saat barang diterima',
                'instructions' => '<p>Pembayaran dilakukan saat barang diterima.</p><p>Pastikan Anda menyiapkan uang pas untuk mempermudah transaksi.</p><p><strong>Catatan:</strong> COD hanya tersedia untuk area tertentu.</p>',
                'fee_percentage' => 0,
                'fee_fixed' => 5000, // COD handling fee
                'min_amount' => 50000,
                'max_amount' => 5000000,
                'expired_duration' => 10080, // 7 days (for order confirmation)
                'is_active' => true,
                'sort_order' => 50,
            ],
        ];

        foreach ($paymentMethods as $method) {
            $method['created_at'] = now();
            $method['updated_at'] = now();
            DB::table('payment_methods')->insert($method);
        }

        $this->command->info('Payment methods seeded successfully!');
    }
}
