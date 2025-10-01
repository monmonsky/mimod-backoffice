@extends('layouts.app')

@section('title', 'Tax Settings')
@section('page_title', 'Settings')
@section('page_subtitle', 'Tax Configuration')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Tax Settings</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Settings</li>
            <li>Payment</li>
            <li class="opacity-80">Tax Settings</li>
        </ul>
    </div>
</div>

<div class="mt-6 space-y-6">
    <!-- Tax Configuration -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">General Tax Configuration</h2>
            <p class="text-sm text-base-content/70 mb-4">Configure tax calculation and display settings</p>

            <form id="taxConfigForm" action="{{ route('settings.payments.tax-settings.update') }}" method="POST" class="space-y-6">
                @csrf
                <!-- Enable Tax -->
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" name="enabled" class="toggle toggle-primary" {{ ($taxConfig['enabled'] ?? true) ? 'checked' : '' }} />
                        <div>
                            <span class="label-text font-medium">Enable Tax Calculation</span>
                            <p class="text-xs text-base-content/60">Calculate and add tax to orders</p>
                        </div>
                    </label>
                </div>

                <!-- Tax Display Mode -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Price Display Mode</span>
                    </label>
                    <select name="price_display_mode" class="select select-bordered w-full" required>
                        <option disabled>Select display mode</option>
                        <option value="including" {{ ($taxConfig['price_display_mode'] ?? 'including') == 'including' ? 'selected' : '' }}>Including Tax</option>
                        <option value="excluding" {{ ($taxConfig['price_display_mode'] ?? '') == 'excluding' ? 'selected' : '' }}>Excluding Tax</option>
                        <option value="both" {{ ($taxConfig['price_display_mode'] ?? '') == 'both' ? 'selected' : '' }}>Show Both</option>
                    </select>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">How to display prices to customers</span>
                    </label>
                </div>

                <!-- Tax Calculation Based On -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Calculate Tax Based On</span>
                    </label>
                    <select name="calculation_based_on" class="select select-bordered w-full" required>
                        <option disabled>Select calculation basis</option>
                        <option value="shipping_address" {{ ($taxConfig['calculation_based_on'] ?? 'shipping_address') == 'shipping_address' ? 'selected' : '' }}>Shipping Address</option>
                        <option value="billing_address" {{ ($taxConfig['calculation_based_on'] ?? '') == 'billing_address' ? 'selected' : '' }}>Billing Address</option>
                        <option value="store_address" {{ ($taxConfig['calculation_based_on'] ?? '') == 'store_address' ? 'selected' : '' }}>Store Address</option>
                    </select>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Which address to use for tax calculation</span>
                    </label>
                </div>

                <!-- Tax Rounding -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Tax Rounding</span>
                    </label>
                    <select name="tax_rounding" class="select select-bordered w-full" required>
                        <option disabled>Select rounding method</option>
                        <option value="round_up" {{ ($taxConfig['tax_rounding'] ?? 'round_down') == 'round_up' ? 'selected' : '' }}>Round Up</option>
                        <option value="round_down" {{ ($taxConfig['tax_rounding'] ?? 'round_down') == 'round_down' ? 'selected' : '' }}>Round Down</option>
                        <option value="standard" {{ ($taxConfig['tax_rounding'] ?? '') == 'standard' ? 'selected' : '' }}>Standard Rounding</option>
                        <option value="none" {{ ($taxConfig['tax_rounding'] ?? '') == 'none' ? 'selected' : '' }}>No Rounding</option>
                    </select>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">How to round tax amounts</span>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-4"></span>
                        Save General Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tax Rates -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="card-title text-lg">Tax Rates</h2>
                    <p class="text-sm text-base-content/70">Manage tax rates for different regions and product categories</p>
                </div>
                <button type="button" class="btn btn-primary btn-sm" onclick="add_tax_rate_modal.showModal()">
                    <span class="iconify lucide--plus size-4"></span>
                    Add Tax Rate
                </button>
            </div>

            <!-- Tax Rates Table -->
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tax Name</th>
                            <th>Rate (%)</th>
                            <th>Region/Province</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div>
                                    <p class="font-medium">PPN (Pajak Pertambahan Nilai)</p>
                                    <p class="text-xs text-base-content/60">Value Added Tax</p>
                                </div>
                            </td>
                            <td>
                                <span class="font-medium">11%</span>
                            </td>
                            <td>
                                <span class="badge badge-outline">All Indonesia</span>
                            </td>
                            <td>
                                <span class="badge badge-info badge-sm">Standard</span>
                            </td>
                            <td>
                                <span class="badge badge-success badge-sm">Active</span>
                            </td>
                            <td>
                                <div class="inline-flex gap-1">
                                    <button class="btn btn-square btn-ghost btn-sm" onclick="edit_tax_rate_modal.showModal()">
                                        <span class="iconify lucide--pencil size-4"></span>
                                    </button>
                                    <button class="btn btn-square btn-error btn-outline btn-sm border-transparent">
                                        <span class="iconify lucide--trash size-4"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div>
                                    <p class="font-medium">PPh 23 Services</p>
                                    <p class="text-xs text-base-content/60">Income Tax for Services</p>
                                </div>
                            </td>
                            <td>
                                <span class="font-medium">2%</span>
                            </td>
                            <td>
                                <span class="badge badge-outline">All Indonesia</span>
                            </td>
                            <td>
                                <span class="badge badge-warning badge-sm">Services</span>
                            </td>
                            <td>
                                <span class="badge badge-error badge-sm">Inactive</span>
                            </td>
                            <td>
                                <div class="inline-flex gap-1">
                                    <button class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--pencil size-4"></span>
                                    </button>
                                    <button class="btn btn-square btn-error btn-outline btn-sm border-transparent">
                                        <span class="iconify lucide--trash size-4"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tax Classes -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="card-title text-lg">Tax Classes</h2>
                    <p class="text-sm text-base-content/70">Define tax classes for different product types</p>
                </div>
                <button type="button" class="btn btn-primary btn-sm" onclick="add_tax_class_modal.showModal()">
                    <span class="iconify lucide--plus size-4"></span>
                    Add Tax Class
                </button>
            </div>

            <!-- Tax Classes List -->
            <div class="space-y-3">
                <!-- Standard Rate -->
                <div class="border border-base-300 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium">Standard Rate</h3>
                            <p class="text-sm text-base-content/60">Default tax class for most products</p>
                            <div class="flex gap-2 mt-2">
                                <span class="badge badge-outline badge-sm">PPN 11%</span>
                                <span class="badge badge-success badge-xs mt-0.5">Default</span>
                            </div>
                        </div>
                        <div class="inline-flex gap-1">
                            <button class="btn btn-square btn-ghost btn-sm">
                                <span class="iconify lucide--pencil size-4"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Reduced Rate -->
                <div class="border border-base-300 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium">Reduced Rate</h3>
                            <p class="text-sm text-base-content/60">For essential goods and services</p>
                            <div class="flex gap-2 mt-2">
                                <span class="badge badge-outline badge-sm">PPN 0%</span>
                            </div>
                        </div>
                        <div class="inline-flex gap-1">
                            <button class="btn btn-square btn-ghost btn-sm">
                                <span class="iconify lucide--pencil size-4"></span>
                            </button>
                            <button class="btn btn-square btn-error btn-outline btn-sm border-transparent">
                                <span class="iconify lucide--trash size-4"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Zero Rate -->
                <div class="border border-base-300 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium">Zero Rate</h3>
                            <p class="text-sm text-base-content/60">Tax-exempt products</p>
                            <div class="flex gap-2 mt-2">
                                <span class="badge badge-outline badge-sm">No Tax</span>
                            </div>
                        </div>
                        <div class="inline-flex gap-1">
                            <button class="btn btn-square btn-ghost btn-sm">
                                <span class="iconify lucide--pencil size-4"></span>
                            </button>
                            <button class="btn btn-square btn-error btn-outline btn-sm border-transparent">
                                <span class="iconify lucide--trash size-4"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tax Reports -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Tax Reports</h2>
            <p class="text-sm text-base-content/70 mb-4">Generate tax reports for accounting and compliance</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Total Tax Collected -->
                <div class="stat bg-base-200 rounded-lg">
                    <div class="stat-figure text-primary">
                        <span class="iconify lucide--trending-up size-8"></span>
                    </div>
                    <div class="stat-title">Tax Collected (This Month)</div>
                    <div class="stat-value text-primary text-2xl">Rp 45.6M</div>
                    <div class="stat-desc">↗︎ 12% from last month</div>
                </div>

                <!-- Tax Transactions -->
                <div class="stat bg-base-200 rounded-lg">
                    <div class="stat-figure text-secondary">
                        <span class="iconify lucide--receipt size-8"></span>
                    </div>
                    <div class="stat-title">Taxable Transactions</div>
                    <div class="stat-value text-secondary text-2xl">3,456</div>
                    <div class="stat-desc">This month</div>
                </div>

                <!-- Avg Tax Rate -->
                <div class="stat bg-base-200 rounded-lg">
                    <div class="stat-figure text-accent">
                        <span class="iconify lucide--percent size-8"></span>
                    </div>
                    <div class="stat-title">Average Tax Rate</div>
                    <div class="stat-value text-accent text-2xl">11%</div>
                    <div class="stat-desc">Weighted average</div>
                </div>
            </div>

            <!-- Export Options -->
            <div class="flex gap-2 mt-6">
                <button type="button" class="btn btn-outline btn-sm">
                    <span class="iconify lucide--download size-4"></span>
                    Export Monthly Report
                </button>
                <button type="button" class="btn btn-outline btn-sm">
                    <span class="iconify lucide--download size-4"></span>
                    Export Yearly Report
                </button>
                <button type="button" class="btn btn-outline btn-sm">
                    <span class="iconify lucide--calendar size-4"></span>
                    Custom Date Range
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Tax Rate -->
<dialog id="add_tax_rate_modal" class="modal">
    <div class="modal-box max-w-lg">
        <div class="flex items-center justify-between text-lg font-medium mb-4">
            Add Tax Rate
            <form method="dialog">
                <button class="btn btn-sm btn-ghost btn-circle" aria-label="Close modal">
                    <span class="iconify lucide--x size-4"></span>
                </button>
            </form>
        </div>

        <form class="space-y-4">
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Tax Name <span class="text-error">*</span></span>
                </label>
                <input type="text" placeholder="e.g., PPN" class="input input-bordered w-full" />
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Description</span>
                </label>
                <input type="text" placeholder="e.g., Value Added Tax" class="input input-bordered w-full" />
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Tax Rate (%) <span class="text-error">*</span></span>
                </label>
                <input type="number" step="0.01" placeholder="11.00" class="input input-bordered w-full" />
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Region/Province</span>
                </label>
                <select class="select select-bordered w-full">
                    <option selected>All Indonesia</option>
                    <option>DKI Jakarta</option>
                    <option>Jawa Barat</option>
                    <option>Jawa Tengah</option>
                    <option>Jawa Timur</option>
                </select>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Tax Type</span>
                </label>
                <select class="select select-bordered w-full">
                    <option selected>Standard</option>
                    <option>Reduced</option>
                    <option>Zero</option>
                    <option>Services</option>
                </select>
            </div>

            <div class="form-control">
                <label class="label cursor-pointer justify-start gap-3">
                    <input type="checkbox" class="toggle toggle-primary" checked />
                    <span class="label-text">Active</span>
                </label>
            </div>

            <div class="modal-action">
                <form method="dialog">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                </form>
                <button type="submit" class="btn btn-primary">
                    <span class="iconify lucide--plus size-4"></span>
                    Add Tax Rate
                </button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<!-- Modal: Add Tax Class -->
<dialog id="add_tax_class_modal" class="modal">
    <div class="modal-box max-w-lg">
        <div class="flex items-center justify-between text-lg font-medium mb-4">
            Add Tax Class
            <form method="dialog">
                <button class="btn btn-sm btn-ghost btn-circle" aria-label="Close modal">
                    <span class="iconify lucide--x size-4"></span>
                </button>
            </form>
        </div>

        <form class="space-y-4">
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Class Name <span class="text-error">*</span></span>
                </label>
                <input type="text" placeholder="e.g., Luxury Goods" class="input input-bordered w-full" />
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Description</span>
                </label>
                <textarea class="textarea textarea-bordered" placeholder="Description of this tax class"></textarea>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Applicable Tax Rates</span>
                </label>
                <div class="space-y-2">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" class="checkbox checkbox-primary" checked />
                        <span class="label-text">PPN 11%</span>
                    </label>
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" class="checkbox checkbox-primary" />
                        <span class="label-text">PPh 23 Services 2%</span>
                    </label>
                </div>
            </div>

            <div class="form-control">
                <label class="label cursor-pointer justify-start gap-3">
                    <input type="checkbox" class="toggle toggle-primary" />
                    <span class="label-text">Set as default tax class</span>
                </label>
            </div>

            <div class="modal-action">
                <form method="dialog">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                </form>
                <button type="submit" class="btn btn-primary">
                    <span class="iconify lucide--plus size-4"></span>
                    Add Tax Class
                </button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

@endsection

@section('customjs')
<!-- jQuery from CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

@vite(['resources/js/modules/settings/payments/tax-settings.js'])
@endsection
