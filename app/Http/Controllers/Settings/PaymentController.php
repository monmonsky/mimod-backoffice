<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\PaymentSettingsRepositoryInterface;
use App\Services\Payment\MidtransService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $settingsRepo;

    public function __construct(PaymentSettingsRepositoryInterface $settingsRepository)
    {
        $this->settingsRepo = $settingsRepository;
    }

    /**
     * Display payment methods settings
     */
    public function paymentMethods()
    {
        $midtrans = $this->settingsRepo->getValue('payment.midtrans');
        $bankTransfer = $this->settingsRepo->getValue('payment.bank_transfer');
        $cod = $this->settingsRepo->getValue('payment.cod');

        return view('pages.settings.payments.payment-methods', compact('midtrans', 'bankTransfer', 'cod'));
    }

    /**
     * Display Midtrans configuration
     */
    public function midtransConfig()
    {
        $config = $this->settingsRepo->getValue('payment.midtrans');

        return view('pages.settings.payments.midtrans-config', compact('config'));
    }

    /**
     * Update Midtrans API configuration
     */
    public function updateMidtransApi(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'environment' => 'required|in:sandbox,production',
                'merchant_id' => 'nullable|string|max:255',
                'client_key_production' => 'nullable|string|max:255',
                'server_key_production' => 'nullable|string|max:255',
            ]);

            // Get current config to preserve other settings
            $currentConfig = $this->settingsRepo->getValue('payment.midtrans') ?? [];

            // Merge with API configuration
            $currentConfig['environment'] = $request->environment;
            $currentConfig['merchant_id'] = $request->merchant_id;
            $currentConfig['merchant_id_sandbox'] = $request->merchant_id_sandbox;
            $currentConfig['client_key_sandbox'] = $request->client_key_sandbox;
            $currentConfig['server_key_sandbox'] = $request->server_key_sandbox;
            $currentConfig['client_key_production'] = $request->client_key_production;
            $currentConfig['server_key_production'] = $request->server_key_production;

            $this->settingsRepo->updateValue('payment.midtrans', $currentConfig);

            // Return JSON response for AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Midtrans API configuration updated successfully',
                ], 200);
            }

            return redirect()->back()->with('success', 'Midtrans API configuration updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update Midtrans API configuration: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to update Midtrans API configuration');
        }
    }

    /**
     * Update Midtrans payment methods
     */
    public function updateMidtransPaymentMethods(Request $request)
    {
        try {
            // Get current config to preserve other settings
            $currentConfig = $this->settingsRepo->getValue('payment.midtrans') ?? [];

            // Update payment methods configuration
            $currentConfig['enable_credit_card'] = $request->has('enable_credit_card');
            $currentConfig['enable_3d_secure'] = $request->has('enable_3d_secure');
            $currentConfig['enable_installment'] = $request->has('enable_installment');
            $currentConfig['enable_bca_va'] = $request->has('enable_bca_va');
            $currentConfig['enable_mandiri_va'] = $request->has('enable_mandiri_va');
            $currentConfig['enable_bni_va'] = $request->has('enable_bni_va');
            $currentConfig['enable_bri_va'] = $request->has('enable_bri_va');
            $currentConfig['enable_permata_va'] = $request->has('enable_permata_va');
            $currentConfig['enable_gopay'] = $request->has('enable_gopay');
            $currentConfig['enable_shopeepay'] = $request->has('enable_shopeepay');
            $currentConfig['enable_ovo'] = $request->has('enable_ovo');
            $currentConfig['enable_dana'] = $request->has('enable_dana');
            $currentConfig['enable_linkaja'] = $request->has('enable_linkaja');
            $currentConfig['enable_qris'] = $request->has('enable_qris');
            $currentConfig['enable_convenience_store'] = $request->has('enable_convenience_store');
            $currentConfig['enable_akulaku'] = $request->has('enable_akulaku');

            $this->settingsRepo->updateValue('payment.midtrans', $currentConfig);

            // Return JSON response for AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment methods updated successfully',
                ], 200);
            }

            return redirect()->back()->with('success', 'Payment methods updated successfully');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update payment methods: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to update payment methods');
        }
    }

    /**
     * Update Midtrans transaction settings
     */
    public function updateMidtransTransaction(Request $request)
    {
        try {
            // Get current config to preserve other settings
            $currentConfig = $this->settingsRepo->getValue('payment.midtrans') ?? [];

            // Update transaction settings
            $currentConfig['payment_expiry_hours'] = $request->payment_expiry_hours ?? 24;
            $currentConfig['auto_capture'] = $request->has('auto_capture');
            $currentConfig['notification_url'] = $request->notification_url;
            $currentConfig['finish_redirect_url'] = $request->finish_redirect_url;
            $currentConfig['error_redirect_url'] = $request->error_redirect_url;

            $this->settingsRepo->updateValue('payment.midtrans', $currentConfig);

            // Return JSON response for AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaction settings updated successfully',
                ], 200);
            }

            return redirect()->back()->with('success', 'Transaction settings updated successfully');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update transaction settings: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to update transaction settings');
        }
    }

    /**
     * Test Midtrans connection
     */
    public function testMidtransConnection(Request $request)
    {
        try {
            // Get current Midtrans settings
            $config = $this->settingsRepo->getValue('payment.midtrans');

            if (!$config) {
                return response()->json([
                    'success' => false,
                    'message' => 'Midtrans configuration not found',
                ], 400);
            }

            // Use MidtransService to test connection
            $midtransService = new MidtransService($config);
            $result = $midtransService->testConnection();

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'environment' => $result['environment'] ?? null,
                    'http_code' => $result['http_code'] ?? null,
                    'request' => $result['request'] ?? null,
                    'response' => $result['response'] ?? null,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'request' => $result['request'] ?? null,
                'response' => $result['response'] ?? null,
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync payment methods from Midtrans API
     */
    public function syncMidtransPaymentMethods(Request $request)
    {
        try {
            // Get current Midtrans settings
            $config = $this->settingsRepo->getValue('payment.midtrans');

            if (!$config) {
                return response()->json([
                    'success' => false,
                    'message' => 'Midtrans configuration not found',
                ], 400);
            }

            // Use MidtransService to get available payment methods
            $midtransService = new MidtransService($config);
            $result = $midtransService->getAvailablePaymentMethods();

            if ($result['success']) {
                $paymentMethods = $result['data'];

                // Get current config
                $currentConfig = $this->settingsRepo->getValue('payment.midtrans');

                // Update available payment methods in config
                $currentConfig['available_payment_methods'] = $paymentMethods;
                $currentConfig['last_sync'] = now()->toDateTimeString();

                // Auto-enable methods that are available
                foreach ($paymentMethods as $code => $method) {
                    $enableKey = 'enable_' . $code;
                    // Only auto-enable if not already configured
                    if (!isset($currentConfig[$enableKey])) {
                        $currentConfig[$enableKey] = $method['available'];
                    }
                }

                // Save to database
                $this->settingsRepo->updateValue('payment.midtrans', $currentConfig);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment methods synced successfully from Midtrans',
                    'data' => [
                        'total_methods' => count($paymentMethods),
                        'methods' => $paymentMethods,
                        'last_sync' => $currentConfig['last_sync'],
                    ],
                    'note' => $result['note'] ?? null,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to sync payment methods',
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display tax settings
     */
    public function taxSettings()
    {
        $taxConfig = $this->settingsRepo->getValue('payment.tax');

        return view('pages.settings.payments.tax-settings', compact('taxConfig'));
    }

    /**
     * Update tax settings
     */
    public function updateTaxSettings(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'price_display_mode' => 'required|in:including,excluding,both',
                'calculation_based_on' => 'required|in:shipping_address,billing_address,store_address',
                'tax_rounding' => 'required|in:round_up,round_down,standard,none',
            ]);

            $this->settingsRepo->updateValue('payment.tax', [
                'enabled' => $request->has('enabled'),
                'price_display_mode' => $request->price_display_mode,
                'calculation_based_on' => $request->calculation_based_on,
                'tax_rounding' => $request->tax_rounding,
            ]);

            // Return JSON response for AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tax settings updated successfully',
                ], 200);
            }

            return redirect()->back()->with('success', 'Tax settings updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update tax settings: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to update tax settings');
        }
    }
}