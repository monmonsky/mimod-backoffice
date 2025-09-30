@extends('layouts.app')

@section('title', 'RajaOngkir Config')
@section('page_title', 'Settings')
@section('page_subtitle', 'RajaOngkir Configuration')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">RajaOngkir API Configuration</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Settings</li>
            <li>Shipping</li>
            <li class="opacity-80">RajaOngkir Config</li>
        </ul>
    </div>
</div>

<div class="mt-6 space-y-6">
    <!-- Connection Status -->
    <div class="alert alert-info">
        <span class="iconify lucide--info size-5"></span>
        <div class="flex-1">
            <h4 class="font-medium">RajaOngkir Shipping API</h4>
            <p class="text-sm">Configure your RajaOngkir API credentials to enable automatic shipping cost calculation for multiple couriers</p>
        </div>
        <div class="flex gap-2">
            <a href="https://rajaongkir.com/" target="_blank" class="btn btn-sm">
                <span class="iconify lucide--external-link size-4"></span>
                RajaOngkir Dashboard
            </a>
        </div>
    </div>

    <!-- API Configuration -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">API Configuration</h2>
            <p class="text-sm text-base-content/70 mb-4">Configure RajaOngkir API credentials and settings</p>

            <form class="space-y-6">
                <!-- Account Type -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Account Type <span class="text-error">*</span></span>
                    </label>
                    <select class="select select-bordered w-full">
                        <option disabled>Select Account Type</option>
                        <option>Starter</option>
                        <option selected>Basic</option>
                        <option>Pro</option>
                    </select>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Your RajaOngkir subscription plan</span>
                    </label>
                </div>

                <!-- API Key -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">API Key <span class="text-error">*</span></span>
                    </label>
                    <div class="join w-full">
                        <input type="password" id="api-key" placeholder="Enter your RajaOngkir API key" class="input input-bordered join-item flex-1" value="xxxxxxxxxxxxxxxxxxxxxxxx" />
                        <button type="button" class="btn btn-outline join-item" onclick="togglePassword('api-key')">
                            <span class="iconify lucide--eye size-4"></span>
                        </button>
                    </div>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Get your API key from RajaOngkir dashboard</span>
                    </label>
                </div>

                <!-- Test Connection -->
                <div class="alert alert-warning">
                    <span class="iconify lucide--zap size-5"></span>
                    <div class="flex-1">
                        <h4 class="font-medium">Test API Connection</h4>
                        <p class="text-sm">Verify that your API key is valid and working</p>
                    </div>
                    <button type="button" class="btn btn-sm">
                        <span class="iconify lucide--play size-4"></span>
                        Test Connection
                    </button>
                </div>

                <!-- Connection Status Result -->
                <div class="alert alert-success hidden" id="connection-success">
                    <span class="iconify lucide--check-circle size-5"></span>
                    <div>
                        <h4 class="font-medium">Connection Successful</h4>
                        <p class="text-sm">API key is valid • Account: Basic • Available couriers: 12</p>
                    </div>
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

    <!-- Available Couriers -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Available Couriers</h2>
            <p class="text-sm text-base-content/70 mb-4">Couriers available based on your account type</p>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Courier</th>
                            <th>Code</th>
                            <th>Available Services</th>
                            <th>Account Required</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="avatar placeholder">
                                        <div class="bg-error text-error-content rounded w-8">
                                            <span class="text-xs font-bold">JNE</span>
                                        </div>
                                    </div>
                                    <span class="font-medium">JNE</span>
                                </div>
                            </td>
                            <td><code class="text-xs">jne</code></td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    <span class="badge badge-sm badge-outline">REG</span>
                                    <span class="badge badge-sm badge-outline">YES</span>
                                    <span class="badge badge-sm badge-outline">OKE</span>
                                </div>
                            </td>
                            <td><span class="badge badge-info badge-sm">Starter+</span></td>
                            <td><span class="badge badge-success badge-sm">Available</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="avatar placeholder">
                                        <div class="bg-error text-error-content rounded w-8">
                                            <span class="text-xs font-bold">J&T</span>
                                        </div>
                                    </div>
                                    <span class="font-medium">J&T Express</span>
                                </div>
                            </td>
                            <td><code class="text-xs">jnt</code></td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    <span class="badge badge-sm badge-outline">REG</span>
                                    <span class="badge badge-sm badge-outline">EZ</span>
                                </div>
                            </td>
                            <td><span class="badge badge-info badge-sm">Basic+</span></td>
                            <td><span class="badge badge-success badge-sm">Available</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="avatar placeholder">
                                        <div class="bg-warning text-warning-content rounded w-8">
                                            <span class="text-xs font-bold">SC</span>
                                        </div>
                                    </div>
                                    <span class="font-medium">SiCepat</span>
                                </div>
                            </td>
                            <td><code class="text-xs">sicepat</code></td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    <span class="badge badge-sm badge-outline">REG</span>
                                    <span class="badge badge-sm badge-outline">BEST</span>
                                    <span class="badge badge-sm badge-outline">CARGO</span>
                                </div>
                            </td>
                            <td><span class="badge badge-info badge-sm">Basic+</span></td>
                            <td><span class="badge badge-success badge-sm">Available</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="avatar placeholder">
                                        <div class="bg-info text-info-content rounded w-8">
                                            <span class="text-xs font-bold">TK</span>
                                        </div>
                                    </div>
                                    <span class="font-medium">TIKI</span>
                                </div>
                            </td>
                            <td><code class="text-xs">tiki</code></td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    <span class="badge badge-sm badge-outline">REG</span>
                                    <span class="badge badge-sm badge-outline">ECO</span>
                                    <span class="badge badge-sm badge-outline">ONS</span>
                                </div>
                            </td>
                            <td><span class="badge badge-info badge-sm">Starter+</span></td>
                            <td><span class="badge badge-success badge-sm">Available</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="avatar placeholder">
                                        <div class="bg-success text-success-content rounded w-8">
                                            <span class="text-xs font-bold">POS</span>
                                        </div>
                                    </div>
                                    <span class="font-medium">POS Indonesia</span>
                                </div>
                            </td>
                            <td><code class="text-xs">pos</code></td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    <span class="badge badge-sm badge-outline">Kilat</span>
                                    <span class="badge badge-sm badge-outline">Express</span>
                                </div>
                            </td>
                            <td><span class="badge badge-info badge-sm">Starter+</span></td>
                            <td><span class="badge badge-success badge-sm">Available</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="avatar placeholder">
                                        <div class="bg-primary text-primary-content rounded w-8">
                                            <span class="text-xs font-bold">AA</span>
                                        </div>
                                    </div>
                                    <span class="font-medium">AnterAja</span>
                                </div>
                            </td>
                            <td><code class="text-xs">anteraja</code></td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    <span class="badge badge-sm badge-outline">REG</span>
                                    <span class="badge badge-sm badge-outline">SAMEDAY</span>
                                </div>
                            </td>
                            <td><span class="badge badge-warning badge-sm">Pro</span></td>
                            <td><span class="badge badge-error badge-sm">Upgrade Required</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="alert alert-warning mt-4">
                <span class="iconify lucide--info size-5"></span>
                <div class="flex-1">
                    <p class="text-sm">Some couriers require Pro account. <a href="https://rajaongkir.com/akun/upgrade" target="_blank" class="link">Upgrade your account</a> to access more couriers.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Cache Settings -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Cache Settings</h2>
            <p class="text-sm text-base-content/70 mb-4">Configure shipping cost cache to reduce API calls</p>

            <form class="space-y-6">
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" class="toggle toggle-primary" checked />
                        <div>
                            <span class="label-text font-medium">Enable Shipping Cost Cache</span>
                            <p class="text-xs text-base-content/60">Cache shipping costs to reduce API usage and improve performance</p>
                        </div>
                    </label>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Cache Duration (minutes)</span>
                    </label>
                    <input type="number" placeholder="60" class="input input-bordered w-full" value="60" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">How long to cache shipping costs (recommended: 60 minutes)</span>
                    </label>
                </div>

                <div class="alert alert-info">
                    <span class="iconify lucide--info size-5"></span>
                    <div class="flex-1">
                        <h4 class="font-medium">API Usage</h4>
                        <p class="text-sm">Current month: 1,234 / 10,000 calls • Cached: 89%</p>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline">
                        <span class="iconify lucide--trash-2 size-4"></span>
                        Clear Cache
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

    <!-- Additional Settings -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Additional Settings</h2>
            <p class="text-sm text-base-content/70 mb-4">Configure additional shipping options</p>

            <form class="space-y-6">
                <!-- Insurance -->
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" class="toggle toggle-primary" />
                        <div>
                            <span class="label-text font-medium">Add Shipping Insurance</span>
                            <p class="text-xs text-base-content/60">Automatically add insurance cost to shipping (0.2% of product value)</p>
                        </div>
                    </label>
                </div>

                <!-- Markup -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Shipping Cost Markup</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <select class="select select-bordered w-full">
                                <option selected>Percentage (%)</option>
                                <option>Fixed Amount (Rp)</option>
                            </select>
                        </div>
                        <div class="form-control">
                            <input type="number" placeholder="0" class="input input-bordered w-full" value="0" />
                        </div>
                    </div>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Add markup to shipping costs (0 = no markup)</span>
                    </label>
                </div>

                <!-- Rounding -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Round Shipping Cost</span>
                    </label>
                    <select class="select select-bordered w-full">
                        <option selected>No Rounding</option>
                        <option>Round to nearest 100</option>
                        <option>Round to nearest 500</option>
                        <option>Round to nearest 1,000</option>
                        <option>Round up to nearest 1,000</option>
                    </select>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Round shipping costs for cleaner pricing</span>
                    </label>
                </div>

                <!-- Show ETD -->
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" class="checkbox checkbox-primary" checked />
                        <span class="label-text">Show Estimated Delivery Time (ETD) to customers</span>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-4"></span>
                        Save Additional Settings
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
</script>
@endsection