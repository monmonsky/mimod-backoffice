@extends('layouts.app')

@section('title', 'RajaOngkir Config')
@section('page_title', 'Settings')
@section('page_subtitle', 'RajaOngkir Configuration')

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Select2 DaisyUI Integration */
    .select2-container--default .select2-selection--single {
        height: 3rem !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        background-color: #ffffff !important;
        padding: 0.5rem 1rem !important;
        display: flex !important;
        align-items: center !important;
    }

    [data-theme="dark"] .select2-container--default .select2-selection--single {
        background-color: #374151 !important;
        border-color: #4b5563 !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 2rem !important;
        color: #1f2937 !important;
        padding-left: 0 !important;
        padding-right: 2rem !important;
    }

    [data-theme="dark"] .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #f3f4f6 !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 3rem !important;
        right: 0.5rem !important;
        top: 0 !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #6b7280 transparent transparent transparent !important;
    }

    [data-theme="dark"] .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #9ca3af transparent transparent transparent !important;
    }

    .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
        border-color: transparent transparent #6b7280 transparent !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #9ca3af !important;
    }

    [data-theme="dark"] .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #6b7280 !important;
    }

    /* Dropdown */
    .select2-dropdown {
        border: 1px solid hsl(var(--bc) / 0.2) !important;
        border-radius: 0.5rem !important;
        background-color: #ffffff !important;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -2px rgb(0 0 0 / 0.05) !important;
        z-index: 99999 !important;
    }

    [data-theme="dark"] .select2-dropdown {
        background-color: #1f2937 !important;
    }

    .select2-container {
        z-index: 99999 !important;
    }

    .select2-container--open {
        z-index: 99999 !important;
    }

    .select2-container--open .select2-dropdown {
        z-index: 99999 !important;
    }

    .select2-container--default .select2-results__option {
        padding: 0.75rem 1.5rem !important;
        background-color: #ffffff !important;
        color: #1f2937 !important;
        min-height: 2.5rem !important;
        display: flex !important;
        align-items: center !important;
        line-height: 1.5 !important;
    }

    [data-theme="dark"] .select2-container--default .select2-results__option {
        background-color: #1f2937 !important;
        color: #f3f4f6 !important;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: hsl(var(--p)) !important;
        color: hsl(var(--pc)) !important;
        padding: 0.75rem 1.5rem !important;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #f3f4f6 !important;
        color: #1f2937 !important;
        padding: 0.75rem 1.5rem !important;
        font-weight: 500 !important;
    }

    [data-theme="dark"] .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #374151 !important;
        color: #f3f4f6 !important;
    }

    .select2-container--default .select2-results__option--disabled {
        color: #9ca3af !important;
        background-color: #f9fafb !important;
        padding: 0.75rem 1.5rem !important;
    }

    [data-theme="dark"] .select2-container--default .select2-results__option--disabled {
        color: #6b7280 !important;
        background-color: #111827 !important;
    }

    /* Search box */
    .select2-container--default .select2-search--dropdown {
        padding: 0.5rem !important;
        background-color: #ffffff !important;
    }

    [data-theme="dark"] .select2-container--default .select2-search--dropdown {
        background-color: #1f2937 !important;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        padding: 0.5rem !important;
        background-color: #ffffff !important;
        color: #1f2937 !important;
        outline: none !important;
    }

    [data-theme="dark"] .select2-container--default .select2-search--dropdown .select2-search__field {
        background-color: #374151 !important;
        color: #f3f4f6 !important;
        border-color: #4b5563 !important;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border-color: hsl(var(--p)) !important;
        box-shadow: 0 0 0 3px hsl(var(--p) / 0.2) !important;
    }

    /* Results container */
    .select2-results {
        background-color: #ffffff !important;
        padding: 0.5rem 0 !important;
    }

    [data-theme="dark"] .select2-results {
        background-color: #1f2937 !important;
    }

    .select2-results__options {
        background-color: #ffffff !important;
        padding: 0 !important;
        margin: 0 !important;
        max-height: 300px !important;
        overflow-y: auto !important;
    }

    [data-theme="dark"] .select2-results__options {
        background-color: #1f2937 !important;
    }

    .select2-results__option {
        list-style: none !important;
        white-space: normal !important;
        word-wrap: break-word !important;
    }

    /* Scrollbar styling */
    .select2-results__options::-webkit-scrollbar {
        width: 8px;
    }

    .select2-results__options::-webkit-scrollbar-track {
        background: #f3f4f6;
        border-radius: 0 0.5rem 0.5rem 0;
    }

    [data-theme="dark"] .select2-results__options::-webkit-scrollbar-track {
        background: #374151;
    }

    .select2-results__options::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 4px;
    }

    [data-theme="dark"] .select2-results__options::-webkit-scrollbar-thumb {
        background: #6b7280;
    }

    .select2-results__options::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    [data-theme="dark"] .select2-results__options::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    /* Disabled state */
    .select2-container--default.select2-container--disabled .select2-selection--single {
        background-color: #f3f4f6 !important;
        cursor: not-allowed !important;
        opacity: 0.7 !important;
        border-color: #e5e7eb !important;
    }

    [data-theme="dark"] .select2-container--default.select2-container--disabled .select2-selection--single {
        background-color: #1f2937 !important;
        border-color: #374151 !important;
    }

    /* Focus state */
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: hsl(var(--p)) !important;
        box-shadow: 0 0 0 3px hsl(var(--p) / 0.2) !important;
    }

    /* Hover state */
    .select2-container--default .select2-selection--single:hover {
        border-color: #9ca3af !important;
    }

    [data-theme="dark"] .select2-container--default .select2-selection--single:hover {
        border-color: #6b7280 !important;
    }

    /* Loading state */
    .select2-container--default .select2-results__option--loading {
        background-color: #ffffff !important;
        color: #6b7280 !important;
    }

    [data-theme="dark"] .select2-container--default .select2-results__option--loading {
        background-color: #1f2937 !important;
        color: #9ca3af !important;
    }
</style>
@endpush

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
                <span class="iconify lucide--info size-4"></span>
                Legacy Dashboard
            </a>
        </div>
    </div>

    <!-- API Configuration -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">API Configuration</h2>
            <p class="text-sm text-base-content/70 mb-4">Configure RajaOngkir API credentials and settings</p>

            <form id="rajaongkirApiForm" action="{{ route('settings.shipping.rajaongkir-config.update') }}" method="POST" class="space-y-6">
                @csrf
                <!-- Account Type -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Account Type <span class="text-error">*</span></span>
                    </label>
                    <select name="account_type" class="select select-bordered w-full" required>
                        <option disabled>Select Account Type</option>
                        <option value="starter" {{ ($config['account_type'] ?? 'starter') == 'starter' ? 'selected' : '' }}>Starter</option>
                        <option value="basic" {{ ($config['account_type'] ?? 'starter') == 'basic' ? 'selected' : '' }}>Basic</option>
                        <option value="pro" {{ ($config['account_type'] ?? 'starter') == 'pro' ? 'selected' : '' }}>Pro</option>
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
                        <input type="password" name="api_key" id="api-key" placeholder="Enter your RajaOngkir API key" class="input input-bordered join-item flex-1" value="{{ $config['api_key'] ?? '' }}" required />
                        <button type="button" class="btn btn-outline join-item" onclick="togglePassword('api-key')">
                            <span class="iconify lucide--eye size-4"></span>
                        </button>
                    </div>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Get your new API key from <a href="https://collaborator.komerce.id" target="_blank" class="link">collaborator.komerce.id</a></span>
                    </label>
                </div>

                <!-- Base URL (Optional - for new platform) -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Base URL</span>
                    </label>
                    <input type="text" name="base_url" placeholder="https://rajaongkir.komerce.id/api/v1" class="input input-bordered w-full" value="{{ $config['base_url'] ?? '' }}" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">
                            For new Komerce API, use: <code class="bg-base-200 px-1 rounded">https://rajaongkir.komerce.id/api/v1</code>
                            <br>Leave empty to use old RajaOngkir endpoints (deprecated).
                        </span>
                    </label>
                </div>

                <!-- Test Connection -->
                <div class="alert alert-warning">
                    <span class="iconify lucide--zap size-5"></span>
                    <div class="flex-1">
                        <h4 class="font-medium">Test API Connection</h4>
                        <p class="text-sm">Verify that your API key is valid and working</p>
                    </div>
                    <button type="button" id="testRajaongkirBtn" class="btn btn-sm">
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

    <!-- Shipping Cost Calculator Tool -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Shipping Cost Calculator</h2>
            <p class="text-sm text-base-content/70 mb-4">Test shipping cost calculation with RajaOngkir API</p>

            <form id="shippingCalculatorForm" class="space-y-6">
                <!-- Origin Section -->
                <div>
                    <h3 class="font-semibold mb-3 flex items-center gap-2">
                        <span class="iconify lucide--map-pin size-4"></span>
                        Origin Address
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Origin Province -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Province <span class="text-error">*</span></span>
                            </label>
                            <select id="origin-province" class="select select-bordered w-full" required>
                                <option disabled selected>Loading provinces...</option>
                            </select>
                        </div>

                        <!-- Origin City -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">City <span class="text-error">*</span></span>
                            </label>
                            <select id="origin-city" class="select select-bordered w-full" required disabled>
                                <option disabled selected>Select province first</option>
                            </select>
                        </div>

                        <!-- Origin District -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">District <span class="text-error">*</span></span>
                            </label>
                            <select id="origin-district" class="select select-bordered w-full" required disabled>
                                <option disabled selected>Select city first</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Destination Section -->
                <div>
                    <h3 class="font-semibold mb-3 flex items-center gap-2">
                        <span class="iconify lucide--map-pin size-4"></span>
                        Destination Address
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Destination Province -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Province <span class="text-error">*</span></span>
                            </label>
                            <select id="dest-province" class="select select-bordered w-full" required>
                                <option disabled selected>Loading provinces...</option>
                            </select>
                        </div>

                        <!-- Destination City -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">City <span class="text-error">*</span></span>
                            </label>
                            <select id="dest-city" class="select select-bordered w-full" required disabled>
                                <option disabled selected>Select province first</option>
                            </select>
                        </div>

                        <!-- Destination District -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">District <span class="text-error">*</span></span>
                            </label>
                            <select id="dest-district" class="select select-bordered w-full" required disabled>
                                <option disabled selected>Select city first</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Package & Courier Section -->
                <div>
                    <h3 class="font-semibold mb-3 flex items-center gap-2">
                        <span class="iconify lucide--package size-4"></span>
                        Package Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Weight -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Weight (grams) <span class="text-error">*</span></span>
                            </label>
                            <input type="number" id="weight" placeholder="1000" class="input input-bordered w-full" value="1000" required />
                            <label class="label">
                                <span class="label-text-alt text-base-content/60">Enter weight in grams (e.g., 1000 = 1kg)</span>
                            </label>
                        </div>

                        <!-- Price Selection -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Price Option</span>
                            </label>
                            <select id="price" class="select select-bordered w-full">
                                <option value="lowest">Lowest Price</option>
                                <option value="highest">Highest Price</option>
                            </select>
                            <label class="label">
                                <span class="label-text-alt text-base-content/60">Filter by price preference</span>
                            </label>
                        </div>
                    </div>

                    <!-- Courier Selection -->
                    <div class="form-control mt-4">
                        <label class="label">
                            <span class="label-text font-medium">Select Couriers <span class="text-error">*</span></span>
                            <span class="label-text-alt">
                                <button type="button" id="selectAllCouriers" class="link link-primary text-xs">Select All</button>
                                |
                                <button type="button" id="clearAllCouriers" class="link link-primary text-xs">Clear All</button>
                            </span>
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 p-4 bg-base-200 rounded-lg">
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="jne" class="checkbox checkbox-sm checkbox-primary courier-checkbox" checked />
                                <span class="label-text">JNE</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="pos" class="checkbox checkbox-sm checkbox-primary courier-checkbox" />
                                <span class="label-text">POS Indonesia</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="tiki" class="checkbox checkbox-sm checkbox-primary courier-checkbox" />
                                <span class="label-text">TIKI</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="jnt" class="checkbox checkbox-sm checkbox-primary courier-checkbox" />
                                <span class="label-text">J&T Express</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="sicepat" class="checkbox checkbox-sm checkbox-primary courier-checkbox" />
                                <span class="label-text">SiCepat</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="anteraja" class="checkbox checkbox-sm checkbox-primary courier-checkbox" />
                                <span class="label-text">AnterAja</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="ninja" class="checkbox checkbox-sm checkbox-primary courier-checkbox" />
                                <span class="label-text">Ninja Express</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="lion" class="checkbox checkbox-sm checkbox-primary courier-checkbox" />
                                <span class="label-text">Lion Parcel</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="rpx" class="checkbox checkbox-sm checkbox-primary courier-checkbox" />
                                <span class="label-text">RPX</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="wahana" class="checkbox checkbox-sm checkbox-primary courier-checkbox" />
                                <span class="label-text">Wahana</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="ide" class="checkbox checkbox-sm checkbox-primary courier-checkbox" />
                                <span class="label-text">ID Express</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="sap" class="checkbox checkbox-sm checkbox-primary courier-checkbox" />
                                <span class="label-text">SAP</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="ncs" class="checkbox checkbox-sm checkbox-primary courier-checkbox" />
                                <span class="label-text">NCS</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="rex" class="checkbox checkbox-sm checkbox-primary courier-checkbox" />
                                <span class="label-text">REX</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="sentral" class="checkbox checkbox-sm checkbox-primary courier-checkbox" />
                                <span class="label-text">Sentral Cargo</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="star" class="checkbox checkbox-sm checkbox-primary courier-checkbox" />
                                <span class="label-text">Star Cargo</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="dse" class="checkbox checkbox-sm checkbox-primary courier-checkbox" />
                                <span class="label-text">DSE</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" id="calculateBtn" class="btn btn-primary">
                        <span class="iconify lucide--calculator size-4"></span>
                        Calculate Shipping Cost
                    </button>
                </div>

                <!-- Result -->
                <div id="calculatorResult" class="hidden">
                    <div class="divider"></div>
                    <h3 class="font-semibold mb-3">Shipping Options</h3>
                    <div id="shippingOptions" class="space-y-2">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Enabled Couriers -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Enabled Couriers</h2>
            <p class="text-sm text-base-content/70 mb-4">Select which couriers to enable for customers</p>

            <form id="couriersForm" action="{{ route('settings.shipping.rajaongkir-config.update') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @php
                        $couriers = $config['couriers'] ?? [];
                    @endphp

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="courier_jne" class="toggle toggle-primary" {{ ($couriers['jne']['enabled'] ?? false) ? 'checked' : '' }} />
                            <span class="label-text font-medium">JNE</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="courier_pos" class="toggle toggle-primary" {{ ($couriers['pos']['enabled'] ?? false) ? 'checked' : '' }} />
                            <span class="label-text font-medium">POS Indonesia</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="courier_tiki" class="toggle toggle-primary" {{ ($couriers['tiki']['enabled'] ?? false) ? 'checked' : '' }} />
                            <span class="label-text font-medium">TIKI</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="courier_rpx" class="toggle toggle-primary" {{ ($couriers['rpx']['enabled'] ?? false) ? 'checked' : '' }} />
                            <span class="label-text font-medium">RPX</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="courier_sicepat" class="toggle toggle-primary" {{ ($couriers['sicepat']['enabled'] ?? false) ? 'checked' : '' }} />
                            <span class="label-text font-medium">SiCepat</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="courier_jnt" class="toggle toggle-primary" {{ ($couriers['jnt']['enabled'] ?? false) ? 'checked' : '' }} />
                            <span class="label-text font-medium">J&T Express</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="courier_wahana" class="toggle toggle-primary" {{ ($couriers['wahana']['enabled'] ?? false) ? 'checked' : '' }} />
                            <span class="label-text font-medium">Wahana</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="courier_ninja" class="toggle toggle-primary" {{ ($couriers['ninja']['enabled'] ?? false) ? 'checked' : '' }} />
                            <span class="label-text font-medium">Ninja Express</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="courier_lion" class="toggle toggle-primary" {{ ($couriers['lion']['enabled'] ?? false) ? 'checked' : '' }} />
                            <span class="label-text font-medium">Lion Parcel</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="courier_anteraja" class="toggle toggle-primary" {{ ($couriers['anteraja']['enabled'] ?? false) ? 'checked' : '' }} />
                            <span class="label-text font-medium">AnterAja</span>
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-4"></span>
                        Save Couriers
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('customjs')
<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    console.log('=== RajaOngkir JavaScript Loaded ===');
    console.log('Document ready state:', document.readyState);

    function togglePassword(id) {
        const input = document.getElementById(id);
        if (input.type === 'password') {
            input.type = 'text';
        } else {
            input.type = 'password';
        }
    }


    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        console.log('RajaOngkir config page initialized');

        // API Configuration Form
        const apiForm = document.getElementById('rajaongkirApiForm');
        if (apiForm) {
            apiForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                console.log('API form submitted');

                const formData = new FormData(apiForm);
                const submitBtn = apiForm.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;

                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Saving...';

                try {
                    const response = await fetch(apiForm.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    const data = await response.json();
                    console.log('API form response:', data);

                    if (response.ok && data.success) {
                        showToast(data.message || 'RajaOngkir configuration saved successfully!', 'success');
                    } else {
                        if (data.errors) {
                            const errorMessages = Object.values(data.errors).flat().join(', ');
                            showToast(errorMessages, 'error');
                        } else {
                            showToast(data.message || 'Failed to save configuration', 'error');
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToast('An error occurred while saving configuration', 'error');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            });
            console.log('API form listener attached');
        }

        // Couriers Form
        const couriersForm = document.getElementById('couriersForm');
        if (couriersForm) {
            couriersForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                console.log('Couriers form submitted');

                const formData = new FormData(couriersForm);
                const submitBtn = couriersForm.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;

                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Saving...';

                try {
                    const response = await fetch(couriersForm.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    const data = await response.json();
                    console.log('Couriers form response:', data);

                    if (response.ok && data.success) {
                        showToast(data.message || 'Couriers configuration saved successfully!', 'success');
                    } else {
                        if (data.errors) {
                            const errorMessages = Object.values(data.errors).flat().join(', ');
                            showToast(errorMessages, 'error');
                        } else {
                            showToast(data.message || 'Failed to save couriers', 'error');
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToast('An error occurred while saving couriers', 'error');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            });
            console.log('Couriers form listener attached');
        }

        // Test RajaOngkir Connection
        const testRajaongkirBtn = document.getElementById('testRajaongkirBtn');
        console.log('Test button element:', testRajaongkirBtn);

        if (testRajaongkirBtn) {
            console.log('Attaching click listener to test button');
            testRajaongkirBtn.addEventListener('click', async function(e) {
                console.log('Test button clicked!');
                e.preventDefault();
                const originalBtnText = testRajaongkirBtn.innerHTML;

                // Show loading state
                testRajaongkirBtn.disabled = true;
                testRajaongkirBtn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Testing...';

                try {
                    const response = await fetch('{{ route("settings.shipping.rajaongkir-config.test") }}', {
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
                    testRajaongkirBtn.disabled = false;
                    testRajaongkirBtn.innerHTML = originalBtnText;
                }
            });
        }

        // Initialize Select2 for all address dropdowns
        initializeSelect2();

        // Load provinces for calculator
        loadProvinces();

        // Select All / Clear All couriers
        const selectAllBtn = document.getElementById('selectAllCouriers');
        const clearAllBtn = document.getElementById('clearAllCouriers');

        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function() {
                document.querySelectorAll('.courier-checkbox').forEach(cb => cb.checked = true);
            });
        }

        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', function() {
                document.querySelectorAll('.courier-checkbox').forEach(cb => cb.checked = false);
            });
        }

        // Origin Province Change - Using Select2 event
        $('#origin-province').on('change', function() {
            const provinceId = $(this).val();
            const citySelect = $('#origin-city');
            const districtSelect = $('#origin-district');

            // Clear and disable city select
            citySelect.empty().append('<option value="">Loading cities...</option>');
            citySelect.val('').trigger('change');
            citySelect.prop('disabled', true);

            // Clear and disable district select
            districtSelect.empty().append('<option value="">Select city first</option>');
            districtSelect.val('').trigger('change');
            districtSelect.prop('disabled', true);

            if (provinceId) {
                loadCitiesForProvince(provinceId, document.getElementById('origin-city'));
            }
        });

        // Origin City Change - Using Select2 event
        $('#origin-city').on('change', function() {
            const cityId = $(this).val();
            const districtSelect = $('#origin-district');

            // Clear and disable district
            districtSelect.empty().append('<option value="">Select district</option>');
            districtSelect.val('').trigger('change');
            districtSelect.prop('disabled', true);

            // Only load if cityId is valid (not empty, not "Loading cities...")
            if (cityId && cityId !== '' && !cityId.includes('Loading')) {
                loadDistrictsForCity(cityId, document.getElementById('origin-district'));
            }
        });

        // Destination Province Change - Using Select2 event
        $('#dest-province').on('change', function() {
            const provinceId = $(this).val();
            const citySelect = $('#dest-city');
            const districtSelect = $('#dest-district');

            // Clear and disable city select
            citySelect.empty().append('<option value="">Loading cities...</option>');
            citySelect.val('').trigger('change');
            citySelect.prop('disabled', true);

            // Clear and disable district select
            districtSelect.empty().append('<option value="">Select city first</option>');
            districtSelect.val('').trigger('change');
            districtSelect.prop('disabled', true);

            if (provinceId) {
                loadCitiesForProvince(provinceId, document.getElementById('dest-city'));
            }
        });

        // Destination City Change - Using Select2 event
        $('#dest-city').on('change', function() {
            const cityId = $(this).val();
            const districtSelect = $('#dest-district');

            // Clear and disable district
            districtSelect.empty().append('<option value="">Select district</option>');
            districtSelect.val('').trigger('change');
            districtSelect.prop('disabled', true);

            // Only load if cityId is valid (not empty, not "Loading cities...")
            if (cityId && cityId !== '' && !cityId.includes('Loading')) {
                loadDistrictsForCity(cityId, document.getElementById('dest-district'));
            }
        });

        // Shipping Calculator Form
        const calculatorForm = document.getElementById('shippingCalculatorForm');
        if (calculatorForm) {
            calculatorForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const calculateBtn = document.getElementById('calculateBtn');
                const originalBtnText = calculateBtn.innerHTML;

                // Collect selected couriers
                const selectedCouriers = Array.from(document.querySelectorAll('input[name="courier[]"]:checked'))
                    .map(cb => cb.value);

                // Validate at least one courier is selected
                if (selectedCouriers.length === 0) {
                    showToast('Please select at least one courier', 'error');
                    return;
                }

                // Join couriers with colon separator (Komerce API format)
                const courierString = selectedCouriers.join(':');

                // Collect form data
                const requestData = {
                    origin: document.getElementById('origin-district').value,
                    destination: document.getElementById('dest-district').value,
                    weight: document.getElementById('weight').value,
                    courier: courierString,
                    price: document.getElementById('price').value,
                };

                console.log('=== Calculate Shipping Request ===');
                console.log('Request data:', requestData);
                console.log('Selected couriers:', selectedCouriers);
                console.log('Courier string:', courierString);

                // Show loading state
                calculateBtn.disabled = true;
                calculateBtn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Calculating...';

                try {
                    const response = await fetch('{{ route("settings.shipping.api.calculate") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(requestData)
                    });

                    const data = await response.json();
                    console.log('=== Calculate Shipping Response ===');
                    console.log('HTTP Status:', response.status);
                    console.log('Response data:', data);
                    console.log('Response data type:', typeof data.data);
                    console.log('Response data is array:', Array.isArray(data.data));
                    if (data.data) {
                        console.log('Data keys:', Object.keys(data.data));
                        console.log('Data length:', data.data.length);
                    }
                    console.log('Debug info:', data.debug);

                    // Store full response for debugging
                    window.shippingFullResponse = data;

                    if (response.ok && data.success) {
                        // Check various data structures
                        let results = null;

                        if (Array.isArray(data.data)) {
                            results = data.data;
                        } else if (data.data && typeof data.data === 'object') {
                            // If data is object, try to get results from it
                            results = data.data.results || Object.values(data.data);
                        }

                        console.log('Extracted results:', results);
                        console.log('Results is array:', Array.isArray(results));
                        console.log('Results length:', results ? results.length : 0);

                        if (results && results.length > 0) {
                            displayShippingResults(results);
                            showToast('Shipping cost calculated successfully!', 'success');
                        } else {
                            console.warn('No shipping options returned from API');
                            console.warn('Full response:', JSON.stringify(data, null, 2));
                            showToast('No shipping options available for this route', 'warning');

                            // Store for debug modal
                            window.shippingRawData = data;

                            // Show debug info
                            const container = document.getElementById('shippingOptions');
                            const resultDiv = document.getElementById('calculatorResult');
                            container.innerHTML = `
                                <div class="alert alert-warning">
                                    <span class="iconify lucide--alert-triangle size-5"></span>
                                    <div class="flex-1">
                                        <p class="font-semibold">No shipping options found</p>
                                        <p class="text-sm">The API returned successfully but with no results.</p>
                                        <button onclick="showFullResponseModal()" class="btn btn-sm btn-outline mt-2">
                                            <span class="iconify lucide--search size-4"></span>
                                            View Response
                                        </button>
                                    </div>
                                </div>
                            `;
                            resultDiv.classList.remove('hidden');
                        }
                    } else {
                        console.error('Calculate failed:', data.message);
                        showToast(data.message || 'Failed to calculate shipping cost', 'error');
                    }
                } catch (error) {
                    console.error('Calculate error:', error);
                    showToast('An error occurred while calculating shipping cost', 'error');
                } finally {
                    calculateBtn.disabled = false;
                    calculateBtn.innerHTML = originalBtnText;
                }
            });
        }
    }

    // Initialize Select2 for address dropdowns
    function initializeSelect2() {
        // Initialize all address dropdowns with Select2
        $('#origin-province, #origin-city, #origin-district, #dest-province, #dest-city, #dest-district').select2({
            placeholder: 'Select an option',
            allowClear: false,
            width: '100%',
            minimumResultsForSearch: 5, // Show search box if more than 5 items
            theme: 'default',
            dropdownParent: $('#shippingCalculatorForm'), // Attach to form for better positioning
            dropdownAutoWidth: true
        });

        console.log('Select2 initialized for all address dropdowns');
    }

    // Load provinces from API
    async function loadProvinces() {
        try {
            const response = await fetch('{{ route("settings.shipping.api.provinces") }}');
            const data = await response.json();

            if (data.success && data.data) {
                const originProvinceSelect = document.getElementById('origin-province');
                const destProvinceSelect = document.getElementById('dest-province');

                // Clear and populate both dropdowns
                originProvinceSelect.innerHTML = '<option disabled selected>Select Province</option>';
                destProvinceSelect.innerHTML = '<option disabled selected>Select Province</option>';

                data.data.forEach(province => {
                    const provinceId = province.province_id || province.id;
                    const provinceName = province.province || province.name;

                    originProvinceSelect.add(new Option(provinceName, provinceId));
                    destProvinceSelect.add(new Option(provinceName, provinceId));
                });

                console.log(`Loaded ${data.data.length} provinces`);
            } else {
                showToast('Failed to load provinces', 'error');
            }
        } catch (error) {
            console.error('Error loading provinces:', error);
            showToast('Failed to load provinces', 'error');
        }
    }

    // Load cities for a specific province
    async function loadCitiesForProvince(provinceId, citySelectElement) {
        if (!provinceId || !citySelectElement) return;

        const $citySelect = $(citySelectElement);

        // Show loading - use empty value to prevent accidental API calls
        $citySelect.empty().append('<option value="">Loading cities...</option>').prop('disabled', true).trigger('change');

        try {
            const response = await fetch(`{{ route("settings.shipping.api.cities", ["provinceId" => ":provinceId"]) }}`.replace(':provinceId', provinceId));
            const data = await response.json();

            if (data.success && data.data) {
                // Clear and add default option
                $citySelect.empty().append('<option value="">Select City</option>');

                // Add cities
                data.data.forEach(city => {
                    const cityId = city.city_id || city.id;
                    const cityName = city.city_name || city.name;
                    const cityType = city.type || '';
                    const displayText = cityType ? `${cityType} ${cityName}` : cityName;

                    $citySelect.append(new Option(displayText, cityId));
                });

                $citySelect.prop('disabled', false);
                // Don't trigger change here - let user select
                console.log(`Loaded ${data.data.length} cities for province ${provinceId}`);
            } else {
                $citySelect.empty().append('<option value="">No cities found</option>');
                showToast('Failed to load cities', 'error');
            }
        } catch (error) {
            console.error('Error loading cities:', error);
            $citySelect.empty().append('<option value="">Error loading cities</option>');
            showToast('Failed to load cities', 'error');
        }
    }

    // Load districts for a specific city
    async function loadDistrictsForCity(cityId, districtSelectElement) {
        if (!cityId || !districtSelectElement) return;

        const $districtSelect = $(districtSelectElement);

        // Show loading - use empty value to prevent accidental API calls
        $districtSelect.empty().append('<option value="">Loading districts...</option>').prop('disabled', true).trigger('change');

        try {
            const response = await fetch(`{{ route("settings.shipping.api.districts", ["cityId" => ":cityId"]) }}`.replace(':cityId', cityId));
            const data = await response.json();

            if (data.success && data.data) {
                // Clear and add default option
                $districtSelect.empty().append('<option value="">Select District</option>');

                // Add districts
                data.data.forEach(district => {
                    const districtId = district.district_id || district.id;
                    const districtName = district.district_name || district.name;

                    $districtSelect.append(new Option(districtName, districtId));
                });

                $districtSelect.prop('disabled', false);
                // Don't trigger change here - let user select
                console.log(`Loaded ${data.data.length} districts for city ${cityId}`);
            } else {
                $districtSelect.empty().append('<option value="">No districts found</option>');
                showToast('Failed to load districts', 'error');
            }
        } catch (error) {
            console.error('Error loading districts:', error);
            $districtSelect.empty().append('<option value="">Error loading districts</option>');
            showToast('Failed to load districts', 'error');
        }
    }

    // Display shipping results
    function displayShippingResults(results) {
        const container = document.getElementById('shippingOptions');
        const resultDiv = document.getElementById('calculatorResult');

        console.log('=== displayShippingResults ===');
        console.log('Input results:', results);
        console.log('Results type:', typeof results);
        console.log('Results is array:', Array.isArray(results));

        // Convert object to array if needed
        if (!Array.isArray(results)) {
            if (results && typeof results === 'object') {
                console.log('Converting object to array');
                results = Object.values(results);
            } else {
                console.error('Results is not array or object:', results);
                container.innerHTML = '<p class="text-sm text-base-content/60">Invalid data format</p>';
                resultDiv.classList.remove('hidden');
                return;
            }
        }

        if (!results || results.length === 0) {
            console.warn('Results is empty');
            container.innerHTML = '<p class="text-sm text-base-content/60">No shipping options available</p>';
            resultDiv.classList.remove('hidden');
            return;
        }

        let html = '';
        let itemCount = 0;

        try {
            results.forEach((result, index) => {
                console.log(`\nProcessing result ${index}:`, result);
                console.log(`Result keys:`, Object.keys(result));

                // Handle different API response formats
                const courierName = result.name || result.code || result.courier || 'Unknown Courier';
                const costs = result.costs || result.services || result.options || [];

                console.log(`Courier: ${courierName}, Costs:`, costs);

                if (!costs || costs.length === 0) {
                    console.warn(`No costs found for courier ${courierName}, checking if result itself is the cost`);

                    // Maybe the result itself is a single cost item
                    if (result.service || result.price || result.value) {
                        console.log('Result itself appears to be a cost item, processing it');
                        const costItem = processCostItem(result, courierName);
                        if (costItem) {
                            html += costItem;
                            itemCount++;
                        }
                    }
                    return;
                }

                costs.forEach((cost, costIndex) => {
                    console.log(`  Processing cost ${costIndex}:`, cost);
                    const costItem = processCostItem(cost, courierName);
                    if (costItem) {
                        html += costItem;
                        itemCount++;
                    }
                });
            });

            console.log(`Total items rendered: ${itemCount}`);

            if (html === '' || itemCount === 0) {
                console.error('No HTML generated from results');
                console.error('Failed to parse results:', results);

                // Store results for modal
                window.shippingRawData = results;
                window.shippingFullResponse = window.shippingFullResponse || { data: results };

                container.innerHTML = `
                    <div class="alert alert-warning">
                        <span class="iconify lucide--alert-triangle size-5"></span>
                        <div class="flex-1">
                            <p class="font-semibold">Could not parse shipping data</p>
                            <p class="text-sm mt-1">The API returned data in an unexpected format.</p>
                            <button onclick="showRawDataModal()" class="btn btn-sm btn-outline mt-2">
                                <span class="iconify lucide--code size-4"></span>
                                View Raw Data
                            </button>
                        </div>
                    </div>
                `;

                // Automatically open the modal to help debugging
                setTimeout(() => showRawDataModal(), 500);
            } else {
                container.innerHTML = html;
            }
        } catch (error) {
            console.error('Error displaying results:', error);
            container.innerHTML = `
                <div class="alert alert-error">
                    <span class="iconify lucide--x-circle size-5"></span>
                    <p>Error displaying results: ${error.message}</p>
                </div>
            `;
        }

        resultDiv.classList.remove('hidden');
    }

    // Helper function to process individual cost item
    function processCostItem(cost, courierName) {
        console.log('    processCostItem - cost:', cost, 'courier:', courierName);

        // Handle different cost formats
        const service = cost.service || cost.name || cost.type || 'Standard';
        const description = cost.description || cost.desc || cost.note || '';

        // Get price and ETD - handle nested array or direct object
        let price = 0;
        let etd = 'N/A';

        if (cost.cost && Array.isArray(cost.cost) && cost.cost.length > 0) {
            // Old API format: cost is array
            price = cost.cost[0].value || 0;
            etd = cost.cost[0].etd || 'N/A';
            console.log('    Using old API format (nested cost array)');
        } else if (cost.price || cost.value || cost.amount) {
            // New API format: price is direct property
            price = cost.price || cost.value || cost.amount || 0;
            etd = cost.etd || cost.estimate || cost.estimation || 'N/A';
            console.log('    Using new API format (direct price)');
        } else {
            console.warn('    Could not find price in cost item:', cost);
            return null;
        }

        // Clean ETD string
        if (typeof etd === 'string') {
            etd = etd.replace(/[^\d-]/g, ''); // Remove non-numeric chars except dash
        }

        console.log(`    Parsed - Service: ${service}, Price: ${price}, ETD: ${etd}`);

        if (!price || price <= 0) {
            console.warn('    Invalid price, skipping');
            return null;
        }

        return `
            <div class="bg-base-200 rounded-lg p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-semibold">${courierName.toUpperCase()} - ${service}</p>
                        ${description ? `<p class="text-sm text-base-content/60">${description}</p>` : ''}
                        <p class="text-xs text-base-content/60 mt-1">
                            <span class="iconify lucide--clock size-3 inline"></span>
                            ETD: ${etd} days
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-lg text-primary">Rp ${parseInt(price).toLocaleString('id-ID')}</p>
                    </div>
                </div>
            </div>
        `;
    }

    // Show raw data modal
    function showRawDataModal() {
        const data = window.shippingRawData || {};

        // Remove existing modal if any
        const existingModal = document.getElementById('rawDataModal');
        if (existingModal) existingModal.remove();

        // Build simple table view
        let tableHTML = '';

        try {
            if (Array.isArray(data) && data.length > 0) {
                // Get all unique keys from all items
                const allKeys = new Set();
                data.forEach(item => {
                    Object.keys(item).forEach(key => allKeys.add(key));
                });

                // Create table header
                tableHTML = `
                    <div class="overflow-x-auto">
                        <table class="table table-xs table-zebra">
                            <thead>
                                <tr>
                                    <th class="bg-primary text-primary-content sticky left-0 z-10">#</th>
                                    ${Array.from(allKeys).map(key => `
                                        <th class="bg-primary text-primary-content text-xs">${key}</th>
                                    `).join('')}
                                </tr>
                            </thead>
                            <tbody>
                `;

                // Create table rows
                data.forEach((item, index) => {
                    tableHTML += `<tr class="hover">
                        <td class="font-semibold sticky left-0 bg-base-100 z-10">${index + 1}</td>`;

                    allKeys.forEach(key => {
                        const value = item[key];
                        let displayValue = '';

                        if (value === null || value === undefined) {
                            displayValue = '<span class="text-base-content/40 text-xs">-</span>';
                        } else if (typeof value === 'object') {
                            if (Array.isArray(value)) {
                                displayValue = `<span class="badge badge-info badge-xs">Array (${value.length})</span>`;
                            } else {
                                displayValue = '<span class="badge badge-warning badge-xs">Object</span>';
                            }
                        } else if (typeof value === 'boolean') {
                            displayValue = value
                                ? '<span class="badge badge-success badge-xs">✓</span>'
                                : '<span class="badge badge-ghost badge-xs">✗</span>';
                        } else if (typeof value === 'number') {
                            displayValue = `<span class="font-mono text-xs">${value.toLocaleString()}</span>`;
                        } else {
                            const strValue = String(value);
                            displayValue = `<span class="text-xs">${strValue.length > 50 ? strValue.substring(0, 50) + '...' : strValue}</span>`;
                        }

                        tableHTML += `<td class="text-xs">${displayValue}</td>`;
                    });

                    tableHTML += '</tr>';
                });

                tableHTML += `
                            </tbody>
                        </table>
                    </div>
                `;
            } else if (typeof data === 'object' && data !== null) {
                // Single object - show as key-value table
                tableHTML = `
                    <div class="overflow-x-auto">
                        <table class="table table-xs">
                            <thead>
                                <tr>
                                    <th class="bg-primary text-primary-content w-1/4 text-xs">Field</th>
                                    <th class="bg-primary text-primary-content text-xs">Value</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                Object.entries(data).forEach(([key, value]) => {
                    let displayValue = '';

                    if (value === null || value === undefined) {
                        displayValue = '<span class="text-base-content/40 text-xs">null</span>';
                    } else if (typeof value === 'object') {
                        if (Array.isArray(value)) {
                            displayValue = `<span class="badge badge-info badge-xs">Array (${value.length})</span>`;
                        } else {
                            displayValue = '<span class="badge badge-warning badge-xs">Object</span>';
                        }
                    } else if (typeof value === 'boolean') {
                        displayValue = value
                            ? '<span class="badge badge-success badge-xs">true</span>'
                            : '<span class="badge badge-ghost badge-xs">false</span>';
                    } else if (typeof value === 'number') {
                        displayValue = `<span class="font-mono text-xs">${value.toLocaleString()}</span>`;
                    } else {
                        displayValue = `<span class="text-xs">${String(value)}</span>`;
                    }

                    tableHTML += `
                        <tr class="hover">
                            <td class="font-semibold text-xs">${key}</td>
                            <td class="text-xs">${displayValue}</td>
                        </tr>
                    `;
                });

                tableHTML += `
                            </tbody>
                        </table>
                    </div>
                `;
            } else {
                tableHTML = '<p class="text-center text-base-content/60">No data available</p>';
            }
        } catch (e) {
            console.error('Error formatting table:', e);
            tableHTML = `<div class="alert alert-error"><p>Error formatting data: ${e.message}</p></div>`;
        }

        const modalHtml = `
            <dialog id="rawDataModal" class="modal">
                <div class="modal-box w-[98vw] max-w-none h-[95vh]">
                    <div class="flex items-center justify-between mb-3 sticky top-0 bg-base-100 z-10 pb-2 border-b">
                        <h3 class="font-bold text-lg flex items-center gap-2">
                            <span class="iconify lucide--table size-5"></span>
                            API Response Data
                            <span class="badge badge-sm badge-ghost">
                                ${Array.isArray(data) ? `${data.length} items` : typeof data === 'object' ? 'Object' : typeof data}
                            </span>
                        </h3>
                        <form method="dialog">
                            <button class="btn btn-sm btn-ghost btn-circle">
                                <span class="iconify lucide--x size-4"></span>
                            </button>
                        </form>
                    </div>

                    <div class="space-y-3 overflow-y-auto h-[calc(95vh-140px)]">
                        ${tableHTML}

                        <div class="alert alert-sm alert-warning">
                            <span class="iconify lucide--info size-4"></span>
                            <p class="text-xs">Arrays and Objects are shown as badges. Click "Copy JSON" to see full data structure.</p>
                        </div>
                    </div>

                    <div class="modal-action sticky bottom-0 bg-base-100 pt-4 border-t mt-4">
                        <button onclick="copyRawDataToClipboard()" class="btn btn-ghost btn-sm">
                            <span class="iconify lucide--copy size-4"></span>
                            Copy JSON
                        </button>
                        <form method="dialog">
                            <button class="btn btn-primary">Close</button>
                        </form>
                    </div>
                </div>
                <form method="dialog" class="modal-backdrop">
                    <button>close</button>
                </form>
            </dialog>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        document.getElementById('rawDataModal').showModal();
    }

    // Copy raw data to clipboard
    function copyRawDataToClipboard() {
        const data = window.shippingRawData || {};
        navigator.clipboard.writeText(JSON.stringify(data, null, 2)).then(() => {
            showToast('JSON copied to clipboard!', 'success');
        }).catch(err => {
            showToast('Failed to copy', 'error');
        });
    }

    // Show full response modal (for successful API call but no results)
    function showFullResponseModal() {
        const fullData = window.shippingFullResponse || {};

        // Remove existing modal if any
        const existingModal = document.getElementById('fullResponseModal');
        if (existingModal) existingModal.remove();

        const modalHtml = `
            <dialog id="fullResponseModal" class="modal">
                <div class="modal-box w-[98vw] max-w-none h-[95vh]">
                    <div class="flex items-center justify-between mb-3 sticky top-0 bg-base-100 z-10 pb-2 border-b">
                        <h3 class="font-bold text-lg flex items-center gap-2">
                            <span class="iconify lucide--file-json size-5"></span>
                            API Response Details
                        </h3>
                        <form method="dialog">
                            <button class="btn btn-sm btn-ghost btn-circle">
                                <span class="iconify lucide--x size-4"></span>
                            </button>
                        </form>
                    </div>

                    <div class="space-y-3 overflow-y-auto h-[calc(95vh-140px)]">
                        <!-- Success Status -->
                        <div class="alert ${fullData.success ? 'alert-success' : 'alert-error'}">
                            <span class="iconify ${fullData.success ? 'lucide--check-circle' : 'lucide--x-circle'} size-5"></span>
                            <div>
                                <p class="font-semibold">${fullData.success ? 'API Call Successful' : 'API Call Failed'}</p>
                                ${fullData.message ? `<p class="text-sm">${fullData.message}</p>` : ''}
                            </div>
                        </div>

                        <!-- Response Data -->
                        <div class="collapse collapse-arrow bg-base-200">
                            <input type="checkbox" checked />
                            <div class="collapse-title font-semibold flex items-center gap-2">
                                <span class="iconify lucide--database size-4"></span>
                                Response Data
                                ${fullData.data ? `<span class="badge badge-sm">${Array.isArray(fullData.data) ? fullData.data.length + ' items' : typeof fullData.data}</span>` : ''}
                            </div>
                            <div class="collapse-content">
                                <div class="bg-base-300 rounded-lg p-4 overflow-x-auto">
                                    <pre class="text-xs"><code>${JSON.stringify(fullData.data, null, 2)}</code></pre>
                                </div>
                            </div>
                        </div>

                        <!-- Debug Info -->
                        ${fullData.debug ? `
                            <div class="collapse collapse-arrow bg-base-200">
                                <input type="checkbox" />
                                <div class="collapse-title font-semibold flex items-center gap-2">
                                    <span class="iconify lucide--bug size-4"></span>
                                    Debug Information
                                </div>
                                <div class="collapse-content">
                                    <div class="bg-base-300 rounded-lg p-4 overflow-x-auto">
                                        <pre class="text-xs"><code>${JSON.stringify(fullData.debug, null, 2)}</code></pre>
                                    </div>
                                </div>
                            </div>
                        ` : ''}

                        <!-- Full Response -->
                        <div class="collapse collapse-arrow bg-base-200">
                            <input type="checkbox" />
                            <div class="collapse-title font-semibold flex items-center gap-2">
                                <span class="iconify lucide--braces size-4"></span>
                                Complete Raw Response
                                <button onclick="copyFullResponse()" class="btn btn-xs btn-ghost ml-auto" type="button">
                                    <span class="iconify lucide--copy size-3"></span>
                                    Copy
                                </button>
                            </div>
                            <div class="collapse-content">
                                <div class="bg-base-300 rounded-lg p-4 overflow-x-auto">
                                    <pre id="fullResponseContent" class="text-xs"><code>${JSON.stringify(fullData, null, 2)}</code></pre>
                                </div>
                            </div>
                        </div>

                        <!-- Helpful Info -->
                        <div class="alert alert-info">
                            <span class="iconify lucide--lightbulb size-5"></span>
                            <div>
                                <p class="font-semibold text-sm">Troubleshooting Tips</p>
                                <ul class="text-xs list-disc list-inside mt-1 space-y-1">
                                    <li>Check if the courier codes are correct and supported</li>
                                    <li>Verify origin and destination district IDs are valid</li>
                                    <li>Ensure weight is within acceptable range (min: 1 gram)</li>
                                    <li>Some couriers may not service certain routes</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="modal-action sticky bottom-0 bg-base-100 pt-4 border-t mt-4">
                        <form method="dialog">
                            <button class="btn btn-primary">Close</button>
                        </form>
                    </div>
                </div>
                <form method="dialog" class="modal-backdrop">
                    <button>close</button>
                </form>
            </dialog>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        document.getElementById('fullResponseModal').showModal();
    }

    // Copy full response to clipboard
    function copyFullResponse() {
        const jsonContent = document.getElementById('fullResponseContent').textContent;
        navigator.clipboard.writeText(jsonContent).then(() => {
            showToast('Full response copied to clipboard!', 'success');
        }).catch(err => {
            showToast('Failed to copy', 'error');
        });
    }

    // Copy JSON to clipboard
    function copyToClipboard() {
        const jsonContent = document.getElementById('rawJsonContent').textContent;
        navigator.clipboard.writeText(jsonContent).then(() => {
            showToast('Copied to clipboard!', 'success');
        }).catch(err => {
            showToast('Failed to copy', 'error');
        });
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

                        ${data.account_type ? `
                        <div class="flex gap-2 items-center text-sm">
                            <span class="badge badge-primary">${data.account_type}</span>
                            ${data.http_code ? `<span class="badge badge-outline">HTTP ${data.http_code}</span>` : ''}
                            ${data.province_count ? `<span class="badge badge-outline">${data.province_count} provinces</span>` : ''}
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

                        <!-- Note/Warning -->
                        ${data.note ? `
                        <div class="alert alert-info">
                            <span class="iconify lucide--info size-5"></span>
                            <span class="text-sm">${data.note}</span>
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

</script>
@endsection