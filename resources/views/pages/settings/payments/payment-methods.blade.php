@extends('layouts.app')

@section('title', 'Payment Methods')
@section('page_title', 'Settings')
@section('page_subtitle', 'Payment Methods')

@section('content')
@php
    $canUpdate = hasPermission('settings.payments.methods.update');
    $disabled = $canUpdate ? '' : 'disabled';
@endphp

<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Payment Methods</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Settings</li>
            <li>Payment</li>
            <li class="opacity-80">Payment Methods</li>
        </ul>
    </div>
</div>

<div class="mt-6 space-y-6">
    <!-- Digital Payment Methods -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="card-title text-lg">Payment Methods</h2>
                    <p class="text-sm text-base-content/70">Configure payment methods for your store</p>
                </div>
            </div>

            <!-- Midtrans -->
            <div class="border border-base-300 rounded-lg p-4 space-y-4">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="avatar placeholder">
                            <div class="bg-primary text-primary-content rounded-lg w-12">
                                <span class="iconify lucide--credit-card size-6"></span>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-medium text-base">Midtrans Payment Gateway</h3>
                            <p class="text-sm text-base-content/60">Credit Card, Debit Card, E-wallet, Bank Transfer</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="badge badge-success badge-sm">Active</span>
                        <div class="form-control">
                            <input type="checkbox" class="toggle toggle-primary {{ $disabled }}" checked />
                        </div>
                    </div>
                </div>

                <div class="pl-15 space-y-3">
                    <!-- Supported Methods -->
                    <div>
                        <p class="text-sm font-medium mb-2">Supported Payment Methods:</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="badge badge-outline">Credit Card</span>
                            <span class="badge badge-outline">Debit Card</span>
                            <span class="badge badge-outline">GoPay</span>
                            <span class="badge badge-outline">OVO</span>
                            <span class="badge badge-outline">DANA</span>
                            <span class="badge badge-outline">ShopeePay</span>
                            <span class="badge badge-outline">Bank Transfer</span>
                            <span class="badge badge-outline">QRIS</span>
                        </div>
                    </div>

                    <!-- Configuration Status -->
                    <div class="flex items-center gap-2">
                        <span class="iconify lucide--check-circle-2 size-4 text-success"></span>
                        <span class="text-sm">Configured and ready to use</span>
                    </div>

                    <!-- Actions -->
                    @if($canUpdate)
                    <div class="flex gap-2">
                        <a href="{{ route('settings.payments.midtrans-config') }}" class="btn btn-outline btn-sm">
                            <span class="iconify lucide--settings size-4"></span>
                            Configure
                        </a>
                        <button type="button" class="btn btn-outline btn-sm">
                            <span class="iconify lucide--zap size-4"></span>
                            Test Connection
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Manual Bank Transfer -->
            <div class="border border-base-300 rounded-lg p-4 space-y-4">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="avatar placeholder">
                            <div class="bg-info text-info-content rounded-lg w-12">
                                <span class="iconify lucide--layers-3 size-6"></span>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-medium text-base">Manual Bank Transfer</h3>
                            <p class="text-sm text-base-content/60">Direct bank transfer to store account</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="badge badge-success badge-sm">Active</span>
                        @if($canUpdate)
                        <div class="form-control">
                            <input type="checkbox" class="toggle toggle-primary toggle-payment-method" data-method="manual_bank" checked />
                        </div>
                        @endif
                    </div>
                </div>

                <div class="pl-15 space-y-3">
                    <!-- Bank Accounts -->
                    <div>
                        <p class="text-sm font-medium mb-2">Configured Bank Accounts:</p>
                        @if(count($banks) > 0)
                        <div class="space-y-2">
                            @foreach($banks as $bank)
                            <div class="flex items-center justify-between p-3 bg-base-200 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-2">
                                        <span class="iconify lucide--layers-3 size-5 text-primary"></span>
                                        <div>
                                            <p class="font-medium text-sm">{{ $bank['bank_name'] }}</p>
                                            <p class="text-xs text-base-content/60">{{ $bank['account_number'] }} - a/n {{ $bank['account_holder'] }}</p>
                                            @if(isset($bank['branch']) && $bank['branch'])
                                            <p class="text-xs text-base-content/50">Cabang: {{ $bank['branch'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if(isset($bank['is_active']) && $bank['is_active'])
                                        <span class="badge badge-success badge-sm">Active</span>
                                    @else
                                        <span class="badge badge-error badge-sm">Inactive</span>
                                    @endif
                                    @if($canUpdate)
                                    <div class="dropdown dropdown-end">
                                        <button tabindex="0" class="btn btn-ghost btn-xs">
                                            <span class="iconify lucide--more-vertical size-4"></span>
                                        </button>
                                        <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52 z-10">
                                            <li><a onclick="toggleBankActive('{{ $bank['id'] }}')">
                                                <span class="iconify lucide--power size-4"></span>
                                                {{ isset($bank['is_active']) && $bank['is_active'] ? 'Deactivate' : 'Activate' }}
                                            </a></li>
                                            <li><a onclick="deleteBankAccount('{{ $bank['id'] }}', '{{ $bank['bank_name'] }}')">
                                                <span class="iconify lucide--trash-2 size-4 text-error"></span>
                                                Delete
                                            </a></li>
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="alert alert-info">
                            <span class="iconify lucide--info size-5"></span>
                            <span>No bank accounts configured yet. Add your first bank account to accept manual bank transfers.</span>
                        </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    @if($canUpdate)
                        <div class="flex gap-2">
                            <button type="button" class="btn btn-outline btn-sm" onclick="add_bank_account_modal.showModal()">
                                <span class="iconify lucide--plus size-4"></span>
                                Add Bank Account
                            </button>
                        </div>
                    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Gateway Statistics -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Payment Method Usage (Last 30 Days)</h2>
            <p class="text-sm text-base-content/70 mb-4">Transaction statistics by payment method</p>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Payment Method</th>
                            <th>Transactions</th>
                            <th>Success Rate</th>
                            <th>Total Amount</th>
                            <th>Avg. Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--credit-card size-4"></span>
                                    <span>Credit/Debit Card</span>
                                </div>
                            </td>
                            <td>
                                <span class="font-medium">1,234</span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <progress class="progress progress-success w-20" value="98" max="100"></progress>
                                    <span class="text-sm">98%</span>
                                </div>
                            </td>
                            <td>
                                <span class="font-medium">Rp 456,789,000</span>
                            </td>
                            <td>
                                <span class="text-sm">Rp 370,215</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--wallet size-4"></span>
                                    <span>E-Wallet</span>
                                </div>
                            </td>
                            <td>
                                <span class="font-medium">2,456</span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <progress class="progress progress-success w-20" value="99" max="100"></progress>
                                    <span class="text-sm">99%</span>
                                </div>
                            </td>
                            <td>
                                <span class="font-medium">Rp 345,678,000</span>
                            </td>
                            <td>
                                <span class="text-sm">Rp 140,764</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--layers-3 size-4"></span>
                                    <span>Bank Transfer</span>
                                </div>
                            </td>
                            <td>
                                <span class="font-medium">876</span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <progress class="progress progress-success w-20" value="95" max="100"></progress>
                                    <span class="text-sm">95%</span>
                                </div>
                            </td>
                            <td>
                                <span class="font-medium">Rp 234,567,000</span>
                            </td>
                            <td>
                                <span class="text-sm">Rp 267,770</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--hash size-4"></span>
                                    <span>QRIS</span>
                                </div>
                            </td>
                            <td>
                                <span class="font-medium">543</span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <progress class="progress progress-success w-20" value="97" max="100"></progress>
                                    <span class="text-sm">97%</span>
                                </div>
                            </td>
                            <td>
                                <span class="font-medium">Rp 123,456,000</span>
                            </td>
                            <td>
                                <span class="text-sm">Rp 227,369</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Bank Account -->
<dialog id="add_bank_account_modal" class="modal">
    <div class="modal-box max-w-md">
        <div class="flex items-center justify-between text-lg font-medium mb-4">
            <span>Add Bank Account</span>
            <form method="dialog">
                <button class="btn btn-sm btn-ghost btn-circle" aria-label="Close modal">
                    <span class="iconify lucide--x size-4"></span>
                </button>
            </form>
        </div>

        <form id="addBankForm">
            <div class="space-y-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Bank Name <span class="text-error">*</span></span>
                    </label>
                    <select name="bank_name" class="select select-bordered w-full" required>
                        <option disabled selected>Select Bank</option>
                        <option>BCA - Bank Central Asia</option>
                        <option>Mandiri</option>
                        <option>BNI - Bank Negara Indonesia</option>
                        <option>BRI - Bank Rakyat Indonesia</option>
                        <option>CIMB Niaga</option>
                        <option>Permata Bank</option>
                        <option>BTN - Bank Tabungan Negara</option>
                        <option>Danamon</option>
                        <option>OCBC NISP</option>
                        <option>Maybank</option>
                        <option>Other</option>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Account Number <span class="text-error">*</span></span>
                    </label>
                    <input type="text" name="account_number" placeholder="Enter account number" class="input input-bordered w-full" required />
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Account Holder Name <span class="text-error">*</span></span>
                    </label>
                    <input type="text" name="account_holder" placeholder="Enter account holder name" class="input input-bordered w-full" required />
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Branch (Optional)</span>
                    </label>
                    <input type="text" name="branch" placeholder="Enter branch name" class="input input-bordered w-full" />
                </div>
            </div>

            @if($canUpdate)
            <div class="modal-action">
                <button type="submit" class="btn btn-primary">
                    <span class="iconify lucide--plus size-4"></span>
                    Add Account
                </button>
            </div>
            @endif
        </form>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

</div>
@endsection

@section('customjs')
<!-- jQuery from CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@vite(['resources/js/modules/settings/payments/payment-methods.js'])
@endsection
