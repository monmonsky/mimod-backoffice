<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class PaymentMethodConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // =================================================================
        // PROVIDER-LEVEL CONFIGS (SHARED ACROSS ALL METHODS)
        // =================================================================

        // Get one Midtrans method to attach provider config
        $midtransMethod = DB::table('payment_methods')->where('provider', 'midtrans')->first();

        if ($midtransMethod) {
            // Midtrans Shared Provider Config
            $this->createConfig($midtransMethod->id, 'provider_config', 'true', false); // Mark as provider-level config
            $this->createConfig($midtransMethod->id, 'environment', 'sandbox', false); // sandbox or production
            $this->createConfig($midtransMethod->id, 'sandbox_server_key', 'SB-Mid-server-YOUR_SANDBOX_SERVER_KEY_HERE', true);
            $this->createConfig($midtransMethod->id, 'sandbox_client_key', 'SB-Mid-client-YOUR_SANDBOX_CLIENT_KEY_HERE', false);
            $this->createConfig($midtransMethod->id, 'sandbox_merchant_id', 'YOUR_SANDBOX_MERCHANT_ID_HERE', false);
            $this->createConfig($midtransMethod->id, 'production_server_key', 'YOUR_PRODUCTION_SERVER_KEY_HERE', true);
            $this->createConfig($midtransMethod->id, 'production_client_key', 'YOUR_PRODUCTION_CLIENT_KEY_HERE', false);
            $this->createConfig($midtransMethod->id, 'production_merchant_id', 'YOUR_PRODUCTION_MERCHANT_ID_HERE', false);
            $this->createConfig($midtransMethod->id, 'enable_3ds', 'true', false);

            $this->command->info('✓ Midtrans provider config created (shared across all Midtrans methods)');
        }

        // =================================================================
        // METHOD-SPECIFIC CONFIGS (ONLY UNIQUE SETTINGS)
        // =================================================================

        // Midtrans QRIS - unique acquirer setting
        $midtransQris = DB::table('payment_methods')->where('code', 'midtrans_qris')->first();
        if ($midtransQris) {
            $this->createConfig($midtransQris->id, 'acquirer', 'gopay', false); // gopay or airpay
        }

        // Midtrans Credit Card - unique feature settings
        $midtransCreditCard = DB::table('payment_methods')->where('code', 'midtrans_credit_card')->first();
        if ($midtransCreditCard) {
            $this->createConfig($midtransCreditCard->id, 'enable_installment', 'false', false);
            $this->createConfig($midtransCreditCard->id, 'enable_saved_card', 'true', false);
        }

        // =================================================================
        // MANUAL BANK TRANSFER CONFIGS
        // =================================================================

        // Get one manual bank transfer method to attach provider config
        $manualBankTransfer = DB::table('payment_methods')
            ->where('provider', 'manual')
            ->where('type', 'bank_transfer')
            ->first();

        if ($manualBankTransfer) {
            // Shared config for all manual bank transfers
            $this->createConfig($manualBankTransfer->id, 'provider_config', 'true', false);
            $this->createConfig($manualBankTransfer->id, 'account_name', 'PT Minimoda Indonesia', false);

            $this->command->info('✓ Manual bank transfer provider config created');
        }

        // Bank-specific configs (account numbers)
        $bcaTransfer = DB::table('payment_methods')->where('code', 'bank_transfer_bca')->first();
        if ($bcaTransfer) {
            $this->createConfig($bcaTransfer->id, 'bank_name', 'Bank BCA', false);
            $this->createConfig($bcaTransfer->id, 'account_number', '1234567890', false);
        }

        $mandiriTransfer = DB::table('payment_methods')->where('code', 'bank_transfer_mandiri')->first();
        if ($mandiriTransfer) {
            $this->createConfig($mandiriTransfer->id, 'bank_name', 'Bank Mandiri', false);
            $this->createConfig($mandiriTransfer->id, 'account_number', '0987654321', false);
        }

        $bniTransfer = DB::table('payment_methods')->where('code', 'bank_transfer_bni')->first();
        if ($bniTransfer) {
            $this->createConfig($bniTransfer->id, 'bank_name', 'Bank BNI', false);
            $this->createConfig($bniTransfer->id, 'account_number', '1122334455', false);
        }

        $this->command->info('✓ Payment method configurations seeded successfully!');
    }

    /**
     * Helper function to create config
     */
    private function createConfig($paymentMethodId, $key, $value, $isEncrypted = false)
    {
        // Encrypt if needed
        $configValue = $isEncrypted ? Crypt::encryptString($value) : $value;

        DB::table('payment_method_config')->insert([
            'payment_method_id' => $paymentMethodId,
            'key' => $key,
            'value' => $configValue,
            'is_encrypted' => $isEncrypted,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
