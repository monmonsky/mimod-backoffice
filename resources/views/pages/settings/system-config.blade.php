@extends('layouts.app')

@section('title', 'System Config')
@section('page_title', 'Settings')
@section('page_subtitle', 'System Configuration')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">System Configuration</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Settings</li>
            <li class="opacity-80">System Config</li>
        </ul>
    </div>
</div>

<div class="mt-6 space-y-6">
    <!-- General Settings -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">General Settings</h2>
            <p class="text-sm text-base-content/70 mb-4">Basic system configuration and behavior</p>

            <form class="space-y-6">
                <!-- Site Status -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Site Status</span>
                    </label>
                    <select class="select select-bordered w-full">
                        <option selected>Online</option>
                        <option>Maintenance Mode</option>
                        <option>Coming Soon</option>
                    </select>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Set maintenance mode to disable public access</span>
                    </label>
                </div>

                <!-- Timezone -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Timezone <span class="text-error">*</span></span>
                    </label>
                    <select class="select select-bordered w-full">
                        <option disabled>Select Timezone</option>
                        <option selected>Asia/Jakarta (GMT+7)</option>
                        <option>Asia/Singapore (GMT+8)</option>
                        <option>Asia/Tokyo (GMT+9)</option>
                        <option>UTC (GMT+0)</option>
                    </select>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Default timezone for all dates and times</span>
                    </label>
                </div>

                <!-- Language & Currency -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Default Language <span class="text-error">*</span></span>
                        </label>
                        <select class="select select-bordered w-full">
                            <option disabled>Select Language</option>
                            <option selected>Indonesian (ID)</option>
                            <option>English (EN)</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Default Currency <span class="text-error">*</span></span>
                        </label>
                        <select class="select select-bordered w-full">
                            <option disabled>Select Currency</option>
                            <option selected>IDR (Rp)</option>
                            <option>USD ($)</option>
                            <option>SGD (S$)</option>
                        </select>
                    </div>
                </div>

                <!-- Date & Time Format -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Date Format</span>
                        </label>
                        <select class="select select-bordered w-full">
                            <option>DD/MM/YYYY</option>
                            <option selected>YYYY-MM-DD</option>
                            <option>MM/DD/YYYY</option>
                            <option>DD-MM-YYYY</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Time Format</span>
                        </label>
                        <select class="select select-bordered w-full">
                            <option selected>24 Hour (HH:mm)</option>
                            <option>12 Hour (hh:mm AM/PM)</option>
                        </select>
                    </div>
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

    <!-- Security Settings -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Security Settings</h2>
            <p class="text-sm text-base-content/70 mb-4">Configure security and authentication settings</p>

            <form class="space-y-6">
                <!-- Two Factor Authentication -->
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" class="toggle toggle-primary" />
                        <div>
                            <span class="label-text font-medium">Enable Two-Factor Authentication</span>
                            <p class="text-xs text-base-content/60">Require 2FA for all admin users</p>
                        </div>
                    </label>
                </div>

                <!-- Password Requirements -->
                <div>
                    <label class="label">
                        <span class="label-text font-medium">Password Requirements</span>
                    </label>
                    <div class="space-y-3 ml-1">
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="checkbox checkbox-primary" checked />
                                <span class="label-text">Minimum 8 characters</span>
                            </label>
                        </div>
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="checkbox checkbox-primary" checked />
                                <span class="label-text">Require uppercase letter</span>
                            </label>
                        </div>
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="checkbox checkbox-primary" checked />
                                <span class="label-text">Require lowercase letter</span>
                            </label>
                        </div>
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="checkbox checkbox-primary" checked />
                                <span class="label-text">Require number</span>
                            </label>
                        </div>
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="checkbox checkbox-primary" />
                                <span class="label-text">Require special character</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Session Timeout -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Session Timeout (minutes)</span>
                    </label>
                    <input type="number" placeholder="120" class="input input-bordered w-full" value="120" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Auto-logout inactive users after specified time</span>
                    </label>
                </div>

                <!-- Max Login Attempts -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Max Login Attempts</span>
                    </label>
                    <input type="number" placeholder="5" class="input input-bordered w-full" value="5" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Lock account after failed login attempts</span>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-4"></span>
                        Save Security Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Order Settings -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Order Settings</h2>
            <p class="text-sm text-base-content/70 mb-4">Configure order processing and behavior</p>

            <form class="space-y-6">
                <!-- Order Number Format -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Order Number Format</span>
                    </label>
                    <input type="text" placeholder="ORD-{date}-{number}" class="input input-bordered w-full" value="ORD-{YYYYMMDD}-{000}" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">
                            Example: ORD-20241201-001 • Variables: {literal {YYYY}, {MM}, {DD}, {000} }
                        </span>
                    </label>
                </div>

                <!-- Auto Cancel Order -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Auto Cancel Unpaid Orders (hours)</span>
                    </label>
                    <input type="number" placeholder="24" class="input input-bordered w-full" value="24" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Automatically cancel orders if not paid within specified time</span>
                    </label>
                </div>

                <!-- Minimum Order Amount -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Minimum Order Amount (IDR)</span>
                    </label>
                    <input type="number" placeholder="50000" class="input input-bordered w-full" value="50000" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Minimum amount required to place an order</span>
                    </label>
                </div>

                <!-- Free Shipping Threshold -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Free Shipping Threshold (IDR)</span>
                    </label>
                    <input type="number" placeholder="250000" class="input input-bordered w-full" value="250000" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Order amount to qualify for free shipping (0 to disable)</span>
                    </label>
                </div>

                <!-- Low Stock Threshold -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Low Stock Alert Threshold</span>
                    </label>
                    <input type="number" placeholder="5" class="input input-bordered w-full" value="5" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Alert when product stock falls below this number</span>
                    </label>
                </div>

                <!-- Guest Checkout -->
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" class="toggle toggle-primary" checked />
                        <div>
                            <span class="label-text font-medium">Allow Guest Checkout</span>
                            <p class="text-xs text-base-content/60">Allow customers to checkout without registration</p>
                        </div>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-4"></span>
                        Save Order Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Inventory Settings -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Inventory Settings</h2>
            <p class="text-sm text-base-content/70 mb-4">Configure inventory and stock management</p>

            <form class="space-y-6">
                <!-- Stock Management -->
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" class="toggle toggle-primary" checked />
                        <div>
                            <span class="label-text font-medium">Enable Stock Management</span>
                            <p class="text-xs text-base-content/60">Track and manage product inventory</p>
                        </div>
                    </label>
                </div>

                <!-- Hold Stock for Cart -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Hold Stock in Cart (minutes)</span>
                    </label>
                    <input type="number" placeholder="60" class="input input-bordered w-full" value="60" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Reserve stock for items in cart for specified time</span>
                    </label>
                </div>

                <!-- Backorders -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Backorder Policy</span>
                    </label>
                    <select class="select select-bordered w-full">
                        <option selected>Do not allow backorders</option>
                        <option>Allow backorders (notify customer)</option>
                        <option>Allow backorders (no notification)</option>
                    </select>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">How to handle orders when product is out of stock</span>
                    </label>
                </div>

                <!-- Auto-hide Out of Stock -->
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" class="toggle toggle-primary" checked />
                        <div>
                            <span class="label-text font-medium">Hide Out of Stock Products</span>
                            <p class="text-xs text-base-content/60">Automatically hide products with zero stock from store</p>
                        </div>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-4"></span>
                        Save Inventory Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cache & Performance -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Cache & Performance</h2>
            <p class="text-sm text-base-content/70 mb-4">Optimize system performance with caching</p>

            <form class="space-y-6">
                <!-- Cache Settings -->
                <div class="space-y-3">
                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" class="toggle toggle-primary" checked />
                            <div>
                                <span class="label-text font-medium">Enable Application Cache</span>
                                <p class="text-xs text-base-content/60">Cache application data for faster performance</p>
                            </div>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" class="toggle toggle-primary" checked />
                            <div>
                                <span class="label-text font-medium">Enable Route Cache</span>
                                <p class="text-xs text-base-content/60">Cache routes for faster request handling</p>
                            </div>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" class="toggle toggle-primary" checked />
                            <div>
                                <span class="label-text font-medium">Enable View Cache</span>
                                <p class="text-xs text-base-content/60">Cache compiled views for faster rendering</p>
                            </div>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" class="toggle toggle-primary" />
                            <div>
                                <span class="label-text font-medium">Enable Query Cache</span>
                                <p class="text-xs text-base-content/60">Cache database queries for better performance</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Cache Actions -->
                <div class="alert alert-warning">
                    <span class="iconify lucide--alert-triangle size-5"></span>
                    <div class="flex-1">
                        <h4 class="font-medium">Clear System Cache</h4>
                        <p class="text-sm">Clear all cached data to reflect recent changes</p>
                    </div>
                    <button type="button" class="btn btn-sm btn-warning">
                        <span class="iconify lucide--trash-2 size-4"></span>
                        Clear All Cache
                    </button>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-4"></span>
                        Save Cache Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Maintenance Mode -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Maintenance Mode</h2>
            <p class="text-sm text-base-content/70 mb-4">Configure maintenance mode settings</p>

            <form class="space-y-6">
                <!-- Enable Maintenance Mode -->
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" class="toggle toggle-primary" />
                        <div>
                            <span class="label-text font-medium">Enable Maintenance Mode</span>
                            <p class="text-xs text-base-content/60">Put site in maintenance mode (admins can still access)</p>
                        </div>
                    </label>
                </div>

                <!-- Maintenance Message -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Maintenance Message</span>
                    </label>
                    <textarea id="description" class="textarea w-full h-24" placeholder="Enter maintenance message">We're currently performing scheduled maintenance. We'll be back soon!</textarea>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Message displayed to visitors during maintenance</span>
                    </label>
                </div>

                <!-- Allowed IPs -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Allowed IP Addresses</span>
                    </label>
                    <input type="text" placeholder="127.0.0.1, 192.168.1.1" class="input input-bordered w-full" value="127.0.0.1" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Comma-separated list of IPs that can access during maintenance</span>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-4"></span>
                        Save Maintenance Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('customjs')
<script>
    // Add any custom JavaScript for cache clearing, etc.
</script>
@endsection