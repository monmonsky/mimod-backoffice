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

            <form class="space-y-6">
                <!-- Environment Mode -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Environment Mode <span class="text-error">*</span></span>
                    </label>
                    <select class="select select-bordered w-full">
                        <option disabled>Select Environment</option>
                        <option>Sandbox (Testing)</option>
                        <option selected>Production (Live)</option>
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
                            <input type="text" placeholder="G123456789" class="input input-bordered w-full" value="G123456789" />
                            <label class="label">
                                <span class="label-text-alt text-base-content/60">Your Midtrans Merchant ID</span>
                            </label>
                        </div>

                        <!-- Client Key -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Client Key <span class="text-error">*</span></span>
                            </label>
                            <input type="text" placeholder="SB-Mid-client-..." class="input input-bordered w-full" value="Mid-client-xxxxxxxxxxxxx" />
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
                                <input type="password" id="server-key-prod" placeholder="SB-Mid-server-..." class="input input-bordered join-item flex-1" value="Mid-server-xxxxxxxxxxxxx" />
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
                            <input type="text" placeholder="G987654321" class="input input-bordered w-full" value="G987654321" />
                        </div>

                        <!-- Sandbox Client Key -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Sandbox Client Key</span>
                            </label>
                            <input type="text" placeholder="SB-Mid-client-..." class="input input-bordered w-full" value="SB-Mid-client-xxxxxxxxxxxxx" />
                        </div>

                        <!-- Sandbox Server Key -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Sandbox Server Key</span>
                            </label>
                            <div class="join w-full">
                                <input type="password" id="server-key-sandbox" placeholder="SB-Mid-server-..." class="input input-bordered join-item flex-1" value="SB-Mid-server-xxxxxxxxxxxxx" />
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
                    <button type="button" class="btn btn-sm">
                        <span class="iconify lucide--play size-4"></span>
                        Test Connection
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

            <form class="space-y-6">
                <!-- Credit/Debit Card -->
                <div>
                    <h3 class="font-medium mb-3">Card Payment</h3>
                    <div class="space-y-3">
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="toggle toggle-primary" checked />
                                <div>
                                    <span class="label-text font-medium">Credit & Debit Card</span>
                                    <p class="text-xs text-base-content/60">Visa, Mastercard, JCB, Amex</p>
                                </div>
                            </label>
                        </div>

                        <div class="form-control ml-6">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="checkbox checkbox-primary" checked />
                                <span class="label-text">Enable 3D Secure</span>
                            </label>
                        </div>

                        <div class="form-control ml-6">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="checkbox checkbox-primary" checked />
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
                                <input type="checkbox" class="toggle toggle-primary" checked />
                                <div class="flex items-center gap-2">
                                    <span class="label-text font-medium">GoPay</span>
                                    <span class="badge badge-success badge-xs">Popular</span>
                                </div>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="toggle toggle-primary" checked />
                                <div class="flex items-center gap-2">
                                    <span class="label-text font-medium">ShopeePay</span>
                                    <span class="badge badge-success badge-xs">Popular</span>
                                </div>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="toggle toggle-primary" checked />
                                <span class="label-text font-medium">OVO</span>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="toggle toggle-primary" checked />
                                <span class="label-text font-medium">DANA</span>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="toggle toggle-primary" />
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
                                <input type="checkbox" class="toggle toggle-primary" checked />
                                <span class="label-text font-medium">BCA Virtual Account</span>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="toggle toggle-primary" checked />
                                <span class="label-text font-medium">Mandiri Virtual Account</span>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="toggle toggle-primary" checked />
                                <span class="label-text font-medium">BNI Virtual Account</span>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="toggle toggle-primary" checked />
                                <span class="label-text font-medium">BRI Virtual Account</span>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="toggle toggle-primary" />
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
                                <input type="checkbox" class="toggle toggle-primary" checked />
                                <div>
                                    <span class="label-text font-medium">QRIS</span>
                                    <p class="text-xs text-base-content/60">Quick Response Code Indonesian Standard</p>
                                </div>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="toggle toggle-primary" />
                                <div>
                                    <span class="label-text font-medium">Convenience Store</span>
                                    <p class="text-xs text-base-content/60">Alfamart, Indomaret</p>
                                </div>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="toggle toggle-primary" />
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

            <form class="space-y-6">
                <!-- Payment Expiry -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Payment Expiry Time (hours)</span>
                    </label>
                    <input type="number" placeholder="24" class="input input-bordered w-full" value="24" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Time limit for customer to complete payment</span>
                    </label>
                </div>

                <!-- Auto Capture -->
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" class="toggle toggle-primary" checked />
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
                        <input type="url" placeholder="https://yourstore.com/api/midtrans/notification" class="input input-bordered flex-1" value="https://minimoda.com/api/midtrans/notification" readonly />
                        <button type="button" class="btn btn-outline" onclick="copyToClipboard('https://minimoda.com/api/midtrans/notification')">
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
                    <input type="url" placeholder="https://yourstore.com/payment/finish" class="input input-bordered w-full" value="https://minimoda.com/payment/finish" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Redirect URL after payment is completed</span>
                    </label>
                </div>

                <!-- Error Redirect URL -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Error Redirect URL</span>
                    </label>
                    <input type="url" placeholder="https://yourstore.com/payment/error" class="input input-bordered w-full" value="https://minimoda.com/payment/error" />
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
            alert('Copied to clipboard!');
        });
    }
</script>
@endsection