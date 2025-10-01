@extends('layouts.app')

@section('title', 'Shipping Methods')
@section('page_title', 'Settings')
@section('page_subtitle', 'Shipping Methods')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Shipping Methods</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Settings</li>
            <li>Shipping</li>
            <li class="opacity-80">Shipping Methods</li>
        </ul>
    </div>
</div>

<div class="mt-6 space-y-6">
    <!-- RajaOngkir Integration -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="card-title text-lg">RajaOngkir - Automated Shipping</h2>
                    <p class="text-sm text-base-content/70">Automatic shipping cost calculation with multiple couriers</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="badge badge-success badge-sm">Active</span>
                    <div class="form-control">
                        <input type="checkbox" class="toggle toggle-primary" checked />
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <!-- Available Couriers -->
                <div>
                    <h3 class="font-medium mb-3">Available Couriers</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <!-- JNE -->
                        <div class="border border-base-300 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-error text-error-content rounded w-10">
                                            <span class="text-xs font-bold">JNE</span>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-medium">JNE</p>
                                        <p class="text-xs text-base-content/60">Jalur Nugraha Ekakurir</p>
                                    </div>
                                </div>
                                <input type="checkbox" class="toggle toggle-sm toggle-primary" checked />
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 text-xs">
                                    <input type="checkbox" class="checkbox checkbox-xs checkbox-primary" checked />
                                    <span>REG (Regular)</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs">
                                    <input type="checkbox" class="checkbox checkbox-xs checkbox-primary" checked />
                                    <span>YES (Yakin Esok Sampai)</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs">
                                    <input type="checkbox" class="checkbox checkbox-xs checkbox-primary" />
                                    <span>OKE (Ongkos Kirim Ekonomis)</span>
                                </div>
                            </div>
                        </div>

                        <!-- JNT -->
                        <div class="border border-base-300 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-error text-error-content rounded w-10">
                                            <span class="text-xs font-bold">J&T</span>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-medium">J&T Express</p>
                                        <p class="text-xs text-base-content/60">J&T Express</p>
                                    </div>
                                </div>
                                <input type="checkbox" class="toggle toggle-sm toggle-primary" checked />
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 text-xs">
                                    <input type="checkbox" class="checkbox checkbox-xs checkbox-primary" checked />
                                    <span>REG (Regular)</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs">
                                    <input type="checkbox" class="checkbox checkbox-xs checkbox-primary" />
                                    <span>EZ (Ekonomis)</span>
                                </div>
                            </div>
                        </div>

                        <!-- SiCepat -->
                        <div class="border border-base-300 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-warning text-warning-content rounded w-10">
                                            <span class="text-xs font-bold">SC</span>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-medium">SiCepat</p>
                                        <p class="text-xs text-base-content/60">SiCepat Express</p>
                                    </div>
                                </div>
                                <input type="checkbox" class="toggle toggle-sm toggle-primary" checked />
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 text-xs">
                                    <input type="checkbox" class="checkbox checkbox-xs checkbox-primary" checked />
                                    <span>REG (Regular)</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs">
                                    <input type="checkbox" class="checkbox checkbox-xs checkbox-primary" checked />
                                    <span>BEST (Best Sameday)</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs">
                                    <input type="checkbox" class="checkbox checkbox-xs checkbox-primary" />
                                    <span>CARGO</span>
                                </div>
                            </div>
                        </div>

                        <!-- TIKI -->
                        <div class="border border-base-300 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-info text-info-content rounded w-10">
                                            <span class="text-xs font-bold">TK</span>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-medium">TIKI</p>
                                        <p class="text-xs text-base-content/60">Citra Van Titipan Kilat</p>
                                    </div>
                                </div>
                                <input type="checkbox" class="toggle toggle-sm toggle-primary" />
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 text-xs">
                                    <input type="checkbox" class="checkbox checkbox-xs checkbox-primary" />
                                    <span>REG (Regular)</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs">
                                    <input type="checkbox" class="checkbox checkbox-xs checkbox-primary" />
                                    <span>ECO (Economy)</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs">
                                    <input type="checkbox" class="checkbox checkbox-xs checkbox-primary" />
                                    <span>ONS (Over Night Service)</span>
                                </div>
                            </div>
                        </div>

                        <!-- POS Indonesia -->
                        <div class="border border-base-300 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-success text-success-content rounded w-10">
                                            <span class="text-xs font-bold">POS</span>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-medium">POS Indonesia</p>
                                        <p class="text-xs text-base-content/60">Pos Indonesia</p>
                                    </div>
                                </div>
                                <input type="checkbox" class="toggle toggle-sm toggle-primary" />
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 text-xs">
                                    <input type="checkbox" class="checkbox checkbox-xs checkbox-primary" />
                                    <span>Paket Kilat Khusus</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs">
                                    <input type="checkbox" class="checkbox checkbox-xs checkbox-primary" />
                                    <span>Express Next Day</span>
                                </div>
                            </div>
                        </div>

                        <!-- AnterAja -->
                        <div class="border border-base-300 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-primary text-primary-content rounded w-10">
                                            <span class="text-xs font-bold">AA</span>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-medium">AnterAja</p>
                                        <p class="text-xs text-base-content/60">AnterAja</p>
                                    </div>
                                </div>
                                <input type="checkbox" class="toggle toggle-sm toggle-primary" />
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 text-xs">
                                    <input type="checkbox" class="checkbox checkbox-xs checkbox-primary" />
                                    <span>REG (Regular)</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs">
                                    <input type="checkbox" class="checkbox checkbox-xs checkbox-primary" />
                                    <span>SAMEDAY</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuration Button -->
                <div class="flex justify-between items-center pt-4 border-t border-base-300">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="iconify lucide--check-circle size-4 text-success"></span>
                            <span class="text-sm">RajaOngkir API connected</span>
                        </div>
                        <p class="text-xs text-base-content/60 mt-1">Last synced: 2 hours ago</p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" class="btn btn-outline btn-sm">
                            <span class="iconify lucide--refresh-cw size-4"></span>
                            Sync Couriers
                        </button>
                        <a href="{{ route('settings.shippings.rajaongkir-config') }}" class="btn btn-primary btn-sm">
                            <span class="iconify lucide--settings size-4"></span>
                            Configure API
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flat Rate Shipping -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="card-title text-lg">Flat Rate Shipping</h2>
                    <p class="text-sm text-base-content/70">Fixed shipping cost for all orders</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="badge badge-error badge-sm">Inactive</span>
                    <div class="form-control">
                        <input type="checkbox" class="toggle toggle-primary" />
                    </div>
                </div>
            </div>

            <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Flat Rate Name</span>
                        </label>
                        <input type="text" placeholder="e.g., Standard Shipping" class="input input-bordered w-full" value="Standard Shipping" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Shipping Cost</span>
                        </label>
                        <label class="input input-bordered flex items-center gap-2">
                            <span class="text-base-content/60">Rp</span>
                            <input type="number" placeholder="10000" class="grow" value="10000" />
                        </label>
                    </div>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Estimated Delivery Time</span>
                    </label>
                    <input type="text" placeholder="e.g., 3-5 business days" class="input input-bordered w-full" value="3-5 business days" />
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" class="btn btn-ghost btn-sm">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <span class="iconify lucide--save size-4"></span>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Free Shipping -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="card-title text-lg">Free Shipping</h2>
                    <p class="text-sm text-base-content/70">Offer free shipping based on conditions</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="badge badge-success badge-sm">Active</span>
                    <div class="form-control">
                        <input type="checkbox" class="toggle toggle-primary" checked />
                    </div>
                </div>
            </div>

            <form class="space-y-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Free Shipping Condition</span>
                    </label>
                    <select class="select select-bordered w-full">
                        <option disabled>Select condition</option>
                        <option selected>Minimum Order Amount</option>
                        <option>Specific Products</option>
                        <option>Specific Categories</option>
                        <option>Coupon Code</option>
                        <option>Always Free</option>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Minimum Order Amount</span>
                    </label>
                    <label class="input input-bordered flex items-center gap-2">
                        <span class="text-base-content/60">Rp</span>
                        <input type="number" placeholder="250000" class="grow" value="250000" />
                    </label>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Orders above this amount get free shipping</span>
                    </label>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Applies To</span>
                    </label>
                    <select class="select select-bordered w-full">
                        <option selected>All Regions</option>
                        <option>Specific Provinces</option>
                        <option>Specific Cities</option>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" class="checkbox checkbox-primary" checked />
                        <div>
                            <span class="label-text">Show promotional message</span>
                            <p class="text-xs text-base-content/60">Display "Free shipping for orders over Rp 250,000"</p>
                        </div>
                    </label>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" class="btn btn-ghost btn-sm">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <span class="iconify lucide--save size-4"></span>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Local Pickup -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="card-title text-lg">Local Pickup / Store Pickup</h2>
                    <p class="text-sm text-base-content/70">Allow customers to pick up orders at your store</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="badge badge-error badge-sm">Inactive</span>
                    <div class="form-control">
                        <input type="checkbox" class="toggle toggle-primary" />
                    </div>
                </div>
            </div>

            <form class="space-y-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Pickup Location Name</span>
                    </label>
                    <input type="text" placeholder="e.g., Main Store - Jakarta" class="input input-bordered w-full" value="Main Store - Jakarta" />
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Pickup Address</span>
                    </label>
                    <textarea class="textarea textarea-bordered" placeholder="Enter pickup address">Jl. Sudirman No. 123, Jakarta Selatan, DKI Jakarta 12180</textarea>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Pickup Instructions</span>
                    </label>
                    <textarea class="textarea textarea-bordered" placeholder="Pickup instructions for customers">Please bring your order confirmation and valid ID. Pickup available Mon-Fri 9AM-5PM.</textarea>
                </div>

                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" class="checkbox checkbox-primary" />
                        <span class="label-text">Charge pickup fee</span>
                    </label>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" class="btn btn-ghost btn-sm">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <span class="iconify lucide--save size-4"></span>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Shipping Statistics -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Shipping Statistics (Last 30 Days)</h2>
            <p class="text-sm text-base-content/70 mb-4">Overview of shipping methods usage</p>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Courier / Method</th>
                            <th>Orders</th>
                            <th>Total Cost</th>
                            <th>Avg. Cost</th>
                            <th>Avg. Delivery Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-error badge-sm">JNE</span>
                                    <span>REG</span>
                                </div>
                            </td>
                            <td><span class="font-medium">1,456</span></td>
                            <td><span class="font-medium">Rp 145,600,000</span></td>
                            <td><span class="text-sm">Rp 100,000</span></td>
                            <td><span class="text-sm">3-4 days</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-error badge-sm">J&T</span>
                                    <span>REG</span>
                                </div>
                            </td>
                            <td><span class="font-medium">2,345</span></td>
                            <td><span class="font-medium">Rp 187,600,000</span></td>
                            <td><span class="text-sm">Rp 80,000</span></td>
                            <td><span class="text-sm">2-3 days</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-warning badge-sm">SC</span>
                                    <span>REG</span>
                                </div>
                            </td>
                            <td><span class="font-medium">987</span></td>
                            <td><span class="font-medium">Rp 74,025,000</span></td>
                            <td><span class="text-sm">Rp 75,000</span></td>
                            <td><span class="text-sm">2-3 days</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-success badge-sm">FREE</span>
                                    <span>Free Shipping</span>
                                </div>
                            </td>
                            <td><span class="font-medium">543</span></td>
                            <td><span class="font-medium text-success">Rp 0</span></td>
                            <td><span class="text-sm">-</span></td>
                            <td><span class="text-sm">3-5 days</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customjs')
<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

@vite(['resources/js/modules/settings/shippings/shipping-methods.js'])
@endsection