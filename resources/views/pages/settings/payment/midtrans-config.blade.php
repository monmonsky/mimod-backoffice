@extends('layouts.app')

@section('title', 'Midtrans Config')
@section('page_title', 'Settings')
@section('page_subtitle', 'Midtrans Configuration')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Midtrans Payment Gateway Configuration</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Settings</li>
            <li>Payment</li>
            <li class="opacity-80">Midtrans Config</li>
        </ul>
    </div>
</div>

<div class="mt-6 space-y-6">
    <!-- Connection Status -->
    <div class="alert alert-info">
        <span class="iconify lucide--info size-5"></span>
        <div class="flex-1">
            <h4 class="font-medium">Midtrans Payment Gateway</h4>
            <p class="text-sm">Configure your Midtrans credentials to accept online payments via credit card, e-wallet, and bank transfer</p>
        </div>
        <div class="flex gap-2">
            <a href="https://dashboard.midtrans.com/" target="_blank" class="btn btn-sm">
                <span class="iconify lucide--external-link size-4"></span>
                Midtrans Dashboard
            </a>
        </div>
    </div>

    <!-- API Configuration -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">API Configuration</h2>
            <p class="text-sm text-base-content/70 mb-4">Configure Midtrans API credentials</p>

            <form id="midtransApiForm" action="{{ route('settings.payment.midtrans-config.api.update') }}" method="POST" class="space-y-6">
                @csrf
                <!-- Environment Mode -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Environment Mode <span class="text-error">*</span></span>
                    </label>
                    <select name="environment" class="select select-bordered w-full" required>
                        <option disabled>Select Environment</option>
                        <option value="sandbox" {{ ($config['environment'] ?? 'production') == 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                        <option value="production" {{ ($config['environment'] ?? 'production') == 'production' ? 'selected' : '' }}>Production (Live)</option>
                    </select>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Use Sandbox for testing, Production for live transactions</span>
                    </label>
                </div>

                <div class="divider"></div>

                <!-- Production Keys -->
                <div>
                    <h3 class="font-medium mb-3">Production Keys</h3>

                    <div class="space-y-4">
                        <!-- Merchant ID -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Merchant ID <span class="text-error">*</span></span>
                            </label>
                            <input type="text" name="merchant_id" placeholder="G123456789" class="input input-bordered w-full" value="{{ $config['merchant_id'] ?? '' }}" required />
                            <label class="label">
                                <span class="label-text-alt text-base-content/60">Your Midtrans Merchant ID</span>
                            </label>
                        </div>

                        <!-- Client Key -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Client Key <span class="text-error">*</span></span>
                            </label>
                            <input type="text" name="client_key_production" placeholder="Mid-client-..." class="input input-bordered w-full" value="{{ $config['client_key_production'] ?? '' }}" required />
                            <label class="label">
                                <span class="label-text-alt text-base-content/60">Client key for frontend integration</span>
                            </label>
                        </div>

                        <!-- Server Key -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Server Key <span class="text-error">*</span></span>
                            </label>
                            <div class="join w-full">
                                <input type="password" name="server_key_production" id="server-key-prod" placeholder="Mid-server-..." class="input input-bordered join-item flex-1" value="{{ $config['server_key_production'] ?? '' }}" required />
                                <button type="button" class="btn btn-outline join-item" onclick="togglePassword('server-key-prod')">
                                    <span class="iconify lucide--eye size-4"></span>
                                </button>
                            </div>
                            <label class="label">
                                <span class="label-text-alt text-base-content/60">Server key for backend API calls (keep this secret!)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Sandbox Keys -->
                <div>
                    <h3 class="font-medium mb-3">Sandbox Keys (Testing)</h3>

                    <div class="space-y-4">
                        <!-- Sandbox Merchant ID -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Sandbox Merchant ID</span>
                            </label>
                            <input type="text" name="merchant_id_sandbox" placeholder="G987654321" class="input input-bordered w-full" value="{{ $config['merchant_id_sandbox'] ?? '' }}" />
                        </div>

                        <!-- Sandbox Client Key -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Sandbox Client Key</span>
                            </label>
                            <input type="text" name="client_key_sandbox" placeholder="SB-Mid-client-..." class="input input-bordered w-full" value="{{ $config['client_key_sandbox'] ?? '' }}" />
                        </div>

                        <!-- Sandbox Server Key -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Sandbox Server Key</span>
                            </label>
                            <div class="join w-full">
                                <input type="password" name="server_key_sandbox" id="server-key-sandbox" placeholder="SB-Mid-server-..." class="input input-bordered join-item flex-1" value="{{ $config['server_key_sandbox'] ?? '' }}" />
                                <button type="button" class="btn btn-outline join-item" onclick="togglePassword('server-key-sandbox')">
                                    <span class="iconify lucide--eye size-4"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Test Connection -->
                <div class="alert alert-warning">
                    <span class="iconify lucide--zap size-5"></span>
                    <div class="flex-1">
                        <h4 class="font-medium">Test API Connection</h4>
                        <p class="text-sm">Verify that your API credentials are correct</p>
                    </div>
                    <button type="button" id="testMidtransBtn" class="btn btn-sm">
                        <span class="iconify lucide--play size-4"></span>
                        Test Connection
                    </button>
                </div>

                <!-- Sync Payment Methods -->
                <div class="alert alert-info">
                    <span class="iconify lucide--refresh-cw size-5"></span>
                    <div class="flex-1">
                        <h4 class="font-medium">Sync Payment Methods</h4>
                        <p class="text-sm">Fetch available payment methods from Midtrans API</p>
                        @if(isset($config['last_sync']))
                            <p class="text-xs mt-1 opacity-70">Last synced: {{ $config['last_sync'] }}</p>
                        @endif
                    </div>
                    <button type="button" id="syncPaymentMethodsBtn" class="btn btn-sm">
                        <span class="iconify lucide--cloud-download size-4"></span>
                        Sync Now
                    </button>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-4"></span>
                        Save API Configuration
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payment Methods Settings -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Enabled Payment Methods</h2>
            <p class="text-sm text-base-content/70 mb-4">Select which payment methods to enable for customers</p>

            <form id="midtransMethodsForm" action="{{ route('settings.payment.midtrans-config.methods.update') }}" method="POST" class="space-y-6">
                @csrf
                <!-- Credit/Debit Card -->
                <div>
                    <h3 class="font-medium mb-3">Card Payment</h3>
                    <div class="space-y-3">
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="enable_credit_card" class="toggle toggle-primary" {{ ($config['enable_credit_card'] ?? true) ? 'checked' : '' }} />
                                <div>
                                    <span class="label-text font-medium">Credit & Debit Card</span>
                                    <p class="text-xs text-base-content/60">Visa, Mastercard, JCB, Amex</p>
                                </div>
                            </label>
                        </div>

                        <div class="form-control ml-6">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="enable_3d_secure" class="checkbox checkbox-primary" {{ ($config['enable_3d_secure'] ?? true) ? 'checked' : '' }} />
                                <span class="label-text">Enable 3D Secure</span>
                            </label>
                        </div>

                        <div class="form-control ml-6">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="enable_installment" class="checkbox checkbox-primary" {{ ($config['enable_installment'] ?? true) ? 'checked' : '' }} />
                                <span class="label-text">Enable Installment</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- E-Wallets -->
                <div>
                    <h3 class="font-medium mb-3">E-Wallet</h3>
                    <div class="space-y-3">
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="enable_gopay" class="toggle toggle-primary" {{ ($config['enable_gopay'] ?? true) ? 'checked' : '' }} />
                                <div class="flex items-center gap-2">
                                    <span class="label-text font-medium">GoPay</span>
                                    <span class="badge badge-success badge-xs">Popular</span>
                                </div>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="enable_shopeepay" class="toggle toggle-primary" {{ ($config['enable_shopeepay'] ?? true) ? 'checked' : '' }} />
                                <div class="flex items-center gap-2">
                                    <span class="label-text font-medium">ShopeePay</span>
                                    <span class="badge badge-success badge-xs">Popular</span>
                                </div>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="enable_ovo" class="toggle toggle-primary" {{ ($config['enable_ovo'] ?? true) ? 'checked' : '' }} />
                                <span class="label-text font-medium">OVO</span>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="enable_dana" class="toggle toggle-primary" {{ ($config['enable_dana'] ?? true) ? 'checked' : '' }} />
                                <span class="label-text font-medium">DANA</span>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="enable_linkaja" class="toggle toggle-primary" {{ ($config['enable_linkaja'] ?? false) ? 'checked' : '' }} />
                                <span class="label-text font-medium">LinkAja</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Bank Transfer -->
                <div>
                    <h3 class="font-medium mb-3">Bank Transfer (Virtual Account)</h3>
                    <div class="space-y-3">
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="enable_bca_va" class="toggle toggle-primary" {{ ($config['enable_bca_va'] ?? true) ? 'checked' : '' }} />
                                <span class="label-text font-medium">BCA Virtual Account</span>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="enable_mandiri_va" class="toggle toggle-primary" {{ ($config['enable_mandiri_va'] ?? true) ? 'checked' : '' }} />
                                <span class="label-text font-medium">Mandiri Virtual Account</span>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="enable_bni_va" class="toggle toggle-primary" {{ ($config['enable_bni_va'] ?? true) ? 'checked' : '' }} />
                                <span class="label-text font-medium">BNI Virtual Account</span>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="enable_bri_va" class="toggle toggle-primary" {{ ($config['enable_bri_va'] ?? true) ? 'checked' : '' }} />
                                <span class="label-text font-medium">BRI Virtual Account</span>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="enable_permata_va" class="toggle toggle-primary" {{ ($config['enable_permata_va'] ?? false) ? 'checked' : '' }} />
                                <span class="label-text font-medium">Permata Virtual Account</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Other Methods -->
                <div>
                    <h3 class="font-medium mb-3">Other Payment Methods</h3>
                    <div class="space-y-3">
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="enable_qris" class="toggle toggle-primary" {{ ($config['enable_qris'] ?? true) ? 'checked' : '' }} />
                                <div>
                                    <span class="label-text font-medium">QRIS</span>
                                    <p class="text-xs text-base-content/60">Quick Response Code Indonesian Standard</p>
                                </div>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="enable_convenience_store" class="toggle toggle-primary" {{ ($config['enable_convenience_store'] ?? false) ? 'checked' : '' }} />
                                <div>
                                    <span class="label-text font-medium">Convenience Store</span>
                                    <p class="text-xs text-base-content/60">Alfamart, Indomaret</p>
                                </div>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="enable_akulaku" class="toggle toggle-primary" {{ ($config['enable_akulaku'] ?? false) ? 'checked' : '' }} />
                                <div>
                                    <span class="label-text font-medium">Akulaku</span>
                                    <p class="text-xs text-base-content/60">Buy now, pay later</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-4"></span>
                        Save Payment Methods
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Transaction Settings -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Transaction Settings</h2>
            <p class="text-sm text-base-content/70 mb-4">Configure transaction behavior and notifications</p>

            <form id="midtransTransactionForm" action="{{ route('settings.payment.midtrans-config.transaction.update') }}" method="POST" class="space-y-6">
                @csrf
                <!-- Payment Expiry -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Payment Expiry Time (hours)</span>
                    </label>
                    <input type="number" name="payment_expiry_hours" placeholder="24" class="input input-bordered w-full" value="{{ $config['payment_expiry_hours'] ?? 24 }}" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Time limit for customer to complete payment</span>
                    </label>
                </div>

                <!-- Auto Capture -->
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" name="auto_capture" class="toggle toggle-primary" {{ ($config['auto_capture'] ?? true) ? 'checked' : '' }} />
                        <div>
                            <span class="label-text font-medium">Auto Capture</span>
                            <p class="text-xs text-base-content/60">Automatically capture authorized transactions</p>
                        </div>
                    </label>
                </div>

                <!-- Notification URL -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Payment Notification URL</span>
                    </label>
                    <div class="flex gap-2">
                        <input type="url" name="notification_url" placeholder="https://yourstore.com/api/midtrans/notification" class="input input-bordered flex-1" value="{{ $config['notification_url'] ?? url('/api/midtrans/notification') }}" />
                        <button type="button" class="btn btn-outline" onclick="copyToClipboard(this.previousElementSibling.value)">
                            <span class="iconify lucide--copy size-4"></span>
                        </button>
                    </div>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Configure this URL in Midtrans dashboard for payment notifications</span>
                    </label>
                </div>

                <!-- Finish Redirect URL -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Finish Redirect URL</span>
                    </label>
                    <input type="url" name="finish_redirect_url" placeholder="https://yourstore.com/payment/finish" class="input input-bordered w-full" value="{{ $config['finish_redirect_url'] ?? url('/payment/finish') }}" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Redirect URL after payment is completed</span>
                    </label>
                </div>

                <!-- Error Redirect URL -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Error Redirect URL</span>
                    </label>
                    <input type="url" name="error_redirect_url" placeholder="https://yourstore.com/payment/error" class="input input-bordered w-full" value="{{ $config['error_redirect_url'] ?? url('/payment/error') }}" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Redirect URL when payment fails</span>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-4"></span>
                        Save Transaction Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('customjs')
<script>

    function togglePassword(id) {
        const input = document.getElementById(id);
        if (input.type === 'password') {
            input.type = 'text';
        } else {
            input.type = 'password';
        }
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Copied to clipboard!', 'success');
        });
    }

    // Generic form handler
    async function handleFormSubmit(e, formId) {
        e.preventDefault();

        const form = document.getElementById(formId);
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Saving...';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok && data.success) {
                showToast(data.message || 'Settings saved successfully!', 'success');
            } else {
                if (data.errors) {
                    const errorMessages = Object.values(data.errors).flat().join(', ');
                    showToast(errorMessages, 'error');
                } else {
                    showToast(data.message || 'Failed to save settings', 'error');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('An error occurred while saving settings', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    }

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        // API Configuration Form
        const apiForm = document.getElementById('midtransApiForm');
        if (apiForm) {
            apiForm.addEventListener('submit', (e) => handleFormSubmit(e, 'midtransApiForm'));
        }

        // Payment Methods Form
        const methodsForm = document.getElementById('midtransMethodsForm');
        if (methodsForm) {
            methodsForm.addEventListener('submit', (e) => handleFormSubmit(e, 'midtransMethodsForm'));
        }

        // Transaction Settings Form
        const transactionForm = document.getElementById('midtransTransactionForm');
        if (transactionForm) {
            transactionForm.addEventListener('submit', (e) => handleFormSubmit(e, 'midtransTransactionForm'));
        }

        // Test Midtrans Connection
        const testMidtransBtn = document.getElementById('testMidtransBtn');
        if (testMidtransBtn) {
            testMidtransBtn.addEventListener('click', async function() {
                const originalBtnText = testMidtransBtn.innerHTML;

                // Show loading state
                testMidtransBtn.disabled = true;
                testMidtransBtn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Testing...';

                try {
                    const response = await fetch('{{ route("settings.payment.midtrans-config.test") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        showToast(data.message || 'Connection test successful!', 'success');
                    } else {
                        showToast(data.message || 'Connection test failed', 'error');
                    }

                    // Show request and response details in modal
                    if (data.request || data.response) {
                        showTestResultModal(data);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToast('An error occurred while testing connection', 'error');
                } finally {
                    testMidtransBtn.disabled = false;
                    testMidtransBtn.innerHTML = originalBtnText;
                }
            });
        }

        // Sync Payment Methods
        const syncPaymentMethodsBtn = document.getElementById('syncPaymentMethodsBtn');
        if (syncPaymentMethodsBtn) {
            syncPaymentMethodsBtn.addEventListener('click', async function() {
                const originalBtnText = syncPaymentMethodsBtn.innerHTML;

                // Show loading state
                syncPaymentMethodsBtn.disabled = true;
                syncPaymentMethodsBtn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Syncing...';

                try {
                    const response = await fetch('{{ route("settings.payment.midtrans-config.sync") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        const methodsCount = data.data?.total_methods || 0;
                        showToast(data.message + ` (${methodsCount} methods)`, 'success');

                        // Show sync details in modal
                        if (data.data) {
                            showSyncResultModal(data);
                        }

                        // Reload page after 2 seconds to show updated timestamp
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        showToast(data.message || 'Sync failed', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToast('An error occurred while syncing payment methods', 'error');
                } finally {
                    syncPaymentMethodsBtn.disabled = false;
                    syncPaymentMethodsBtn.innerHTML = originalBtnText;
                }
            });
        }
    }

    // Show test result modal with request and response details
    function showTestResultModal(data) {
        // Remove existing modal if any
        const existingModal = document.getElementById('testResultModal');
        if (existingModal) existingModal.remove();

        const modalHtml = `
            <dialog id="testResultModal" class="modal">
                <div class="modal-box max-w-4xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-lg">API Test Result</h3>
                        <form method="dialog">
                            <button class="btn btn-sm btn-ghost btn-circle">
                                <span class="iconify lucide--x size-4"></span>
                            </button>
                        </form>
                    </div>

                    <div class="space-y-4">
                        <!-- Status -->
                        <div class="alert ${data.success ? 'alert-success' : 'alert-error'}">
                            <span class="iconify ${data.success ? 'lucide--check-circle' : 'lucide--x-circle'} size-5"></span>
                            <span>${data.message}</span>
                        </div>

                        ${data.environment ? `
                        <div class="flex gap-2 items-center text-sm">
                            <span class="badge badge-primary">${data.environment}</span>
                            ${data.http_code ? `<span class="badge badge-outline">HTTP ${data.http_code}</span>` : ''}
                        </div>
                        ` : ''}

                        <!-- Request Details -->
                        ${data.request ? `
                        <div>
                            <h4 class="font-semibold mb-2 flex items-center gap-2">
                                <span class="iconify lucide--arrow-up-right size-4"></span>
                                Request
                            </h4>
                            <div class="bg-base-200 rounded-lg p-4 overflow-x-auto">
                                <pre class="text-xs"><code>${JSON.stringify(data.request, null, 2)}</code></pre>
                            </div>
                        </div>
                        ` : ''}

                        <!-- Response Details -->
                        ${data.response ? `
                        <div>
                            <h4 class="font-semibold mb-2 flex items-center gap-2">
                                <span class="iconify lucide--arrow-down-left size-4"></span>
                                Response
                            </h4>
                            <div class="bg-base-200 rounded-lg p-4 overflow-x-auto">
                                <pre class="text-xs"><code>${JSON.stringify(data.response, null, 2)}</code></pre>
                            </div>
                        </div>
                        ` : ''}
                    </div>

                    <div class="modal-action">
                        <form method="dialog">
                            <button class="btn">Close</button>
                        </form>
                    </div>
                </div>
                <form method="dialog" class="modal-backdrop">
                    <button>close</button>
                </form>
            </dialog>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        document.getElementById('testResultModal').showModal();
    }

    // Show sync result modal with payment methods details
    function showSyncResultModal(data) {
        // Remove existing modal if any
        const existingModal = document.getElementById('syncResultModal');
        if (existingModal) existingModal.remove();

        // Group methods by category
        const methodsByCategory = {};
        if (data.data?.methods) {
            Object.values(data.data.methods).forEach(method => {
                const category = method.category || 'other';
                if (!methodsByCategory[category]) {
                    methodsByCategory[category] = [];
                }
                methodsByCategory[category].push(method);
            });
        }

        // Create category display
        let categoriesHtml = '';
        const categoryNames = {
            'card': 'Card Payment',
            'ewallet': 'E-Wallet',
            'bank_transfer': 'Bank Transfer',
            'qris': 'QRIS',
            'over_the_counter': 'Over The Counter',
            'paylater': 'Pay Later',
            'other': 'Other'
        };

        for (const [category, methods] of Object.entries(methodsByCategory)) {
            const categoryName = categoryNames[category] || category;
            categoriesHtml += `
                <div>
                    <h5 class="font-semibold mb-2">${categoryName}</h5>
                    <div class="space-y-1">
                        ${methods.map(method => `
                            <div class="flex items-center gap-2 text-sm">
                                <span class="iconify ${method.available ? 'lucide--check-circle text-success' : 'lucide--x-circle text-error'} size-4"></span>
                                <span>${method.name}</span>
                                ${method.description ? `<span class="text-xs text-base-content/60">(${method.description})</span>` : ''}
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }

        const modalHtml = `
            <dialog id="syncResultModal" class="modal">
                <div class="modal-box max-w-4xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-lg">Payment Methods Sync Result</h3>
                        <form method="dialog">
                            <button class="btn btn-sm btn-ghost btn-circle">
                                <span class="iconify lucide--x size-4"></span>
                            </button>
                        </form>
                    </div>

                    <div class="space-y-4">
                        <!-- Status -->
                        <div class="alert alert-success">
                            <span class="iconify lucide--check-circle size-5"></span>
                            <div class="flex-1">
                                <span>${data.message}</span>
                                <p class="text-sm mt-1 opacity-70">Total: ${data.data?.total_methods || 0} payment methods</p>
                                ${data.data?.last_sync ? `<p class="text-xs mt-1 opacity-70">Last synced: ${data.data.last_sync}</p>` : ''}
                            </div>
                        </div>

                        ${data.note ? `
                        <div class="alert alert-info">
                            <span class="iconify lucide--info size-5"></span>
                            <span class="text-sm">${data.note}</span>
                        </div>
                        ` : ''}

                        <!-- Payment Methods by Category -->
                        <div class="bg-base-200 rounded-lg p-4">
                            <h4 class="font-semibold mb-3">Available Payment Methods</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                ${categoriesHtml}
                            </div>
                        </div>
                    </div>

                    <div class="modal-action">
                        <form method="dialog">
                            <button class="btn">Close</button>
                        </form>
                    </div>
                </div>
                <form method="dialog" class="modal-backdrop">
                    <button>close</button>
                </form>
            </dialog>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        document.getElementById('syncResultModal').showModal();
    }
</script>
@endsection