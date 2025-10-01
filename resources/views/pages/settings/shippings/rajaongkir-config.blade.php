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
@php
    $canUpdate = hasPermission('settings.shippings.rajaongkir.update');
    $disabled = $canUpdate ? '' : 'disabled';
@endphp

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

@php
    $canUpdate = hasPermission('settings.shippings.rajaongkir.update');
    $disabled = $canUpdate ? '' : 'disabled';
@endphp

            <form id="rajaongkirApiForm" action="{{ route('settings.shippings.rajaongkir-config.update') }}" method="POST" class="space-y-6">
                @csrf
                <!-- Account Type -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Account Type <span class="text-error">*</span></span>
                    </label>
                    <select name="account_type" class="select select-bordered w-full {{ $disabled }}" required>
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
                        <input type="password" name="api_key" id="api-key" placeholder="Enter your RajaOngkir API key" class="input input-bordered join-item flex-1 {{ $disabled }}" value="{{ $config['api_key'] ?? '' }}" required />
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
                    <input type="text" name="base_url" placeholder="https://rajaongkir.komerce.id/api/v1" class="input input-bordered w-full {{ $disabled }}" value="{{ $config['base_url'] ?? '' }}" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">
                            For new Komerce API, use: <code class="bg-base-200 px-1 rounded">https://rajaongkir.komerce.id/api/v1</code>
                            <br>Leave empty to use old RajaOngkir endpoints (deprecated).
                        </span>
                    </label>
                </div>

                <!-- Test Connection -->
                @if($canUpdate)
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
                @endif

                <!-- Action Buttons -->
                @if(hasPermission('settings.shippings.rajaongkir.update'))
                <div class="flex justify-end gap-2 pt-4">
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-4"></span>
                        Save API Configuration
                    </button>
                </div>
                @endif
        </div>
            </form>
    </div>

    <!-- Shipping Cost Calculator Tool -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Shipping Cost Calculator</h2>
            <p class="text-sm text-base-content/70 mb-4">Test shipping cost calculation with RajaOngkir API</p>

@php
    $canUpdate = hasPermission('settings.shippings.rajaongkir.update');
    $disabled = $canUpdate ? '' : 'disabled';
@endphp

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
                            <select id="origin-province" class="select select-bordered w-full {{ $disabled }}" required>
                                <option disabled selected>Loading provinces...</option>
                            </select>
                        </div>

                        <!-- Origin City -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">City <span class="text-error">*</span></span>
                            </label>
                            <select id="origin-city" class="select select-bordered w-full {{ $disabled }}" required disabled>
                                <option disabled selected>Select province first</option>
                            </select>
                        </div>

                        <!-- Origin District -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">District <span class="text-error">*</span></span>
                            </label>
                            <select id="origin-district" class="select select-bordered w-full {{ $disabled }}" required disabled>
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
                            <select id="dest-province" class="select select-bordered w-full {{ $disabled }}" required>
                                <option disabled selected>Loading provinces...</option>
                            </select>
                        </div>

                        <!-- Destination City -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">City <span class="text-error">*</span></span>
                            </label>
                            <select id="dest-city" class="select select-bordered w-full {{ $disabled }}" required disabled>
                                <option disabled selected>Select province first</option>
                            </select>
                        </div>

                        <!-- Destination District -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">District <span class="text-error">*</span></span>
                            </label>
                            <select id="dest-district" class="select select-bordered w-full {{ $disabled }}" required disabled>
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
                            <input type="number" id="weight" placeholder="1000" class="input input-bordered w-full {{ $disabled }}" value="1000" required />
                            <label class="label">
                                <span class="label-text-alt text-base-content/60">Enter weight in grams (e.g., 1000 = 1kg)</span>
                            </label>
                        </div>

                        <!-- Price Selection -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Price Option</span>
                            </label>
                            <select id="price" class="select select-bordered w-full {{ $disabled }}">
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
                                <input type="checkbox" name="courier[]" value="jne" class="checkbox checkbox-sm checkbox-primary courier-checkbox {{ $disabled }}" checked />
                                <span class="label-text">JNE</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="pos" class="checkbox checkbox-sm checkbox-primary courier-checkbox {{ $disabled }}" />
                                <span class="label-text">POS Indonesia</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="tiki" class="checkbox checkbox-sm checkbox-primary courier-checkbox {{ $disabled }}" />
                                <span class="label-text">TIKI</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="jnt" class="checkbox checkbox-sm checkbox-primary courier-checkbox {{ $disabled }}" />
                                <span class="label-text">J&T Express</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="sicepat" class="checkbox checkbox-sm checkbox-primary courier-checkbox {{ $disabled }}" />
                                <span class="label-text">SiCepat</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="anteraja" class="checkbox checkbox-sm checkbox-primary courier-checkbox {{ $disabled }}" />
                                <span class="label-text">AnterAja</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="ninja" class="checkbox checkbox-sm checkbox-primary courier-checkbox {{ $disabled }}" />
                                <span class="label-text">Ninja Express</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="lion" class="checkbox checkbox-sm checkbox-primary courier-checkbox {{ $disabled }}" />
                                <span class="label-text">Lion Parcel</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="rpx" class="checkbox checkbox-sm checkbox-primary courier-checkbox {{ $disabled }}" />
                                <span class="label-text">RPX</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="wahana" class="checkbox checkbox-sm checkbox-primary courier-checkbox {{ $disabled }}" />
                                <span class="label-text">Wahana</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="ide" class="checkbox checkbox-sm checkbox-primary courier-checkbox {{ $disabled }}" />
                                <span class="label-text">ID Express</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="sap" class="checkbox checkbox-sm checkbox-primary courier-checkbox {{ $disabled }}" />
                                <span class="label-text">SAP</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="ncs" class="checkbox checkbox-sm checkbox-primary courier-checkbox {{ $disabled }}" />
                                <span class="label-text">NCS</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="rex" class="checkbox checkbox-sm checkbox-primary courier-checkbox {{ $disabled }}" />
                                <span class="label-text">REX</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="sentral" class="checkbox checkbox-sm checkbox-primary courier-checkbox {{ $disabled }}" />
                                <span class="label-text">Sentral Cargo</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="star" class="checkbox checkbox-sm checkbox-primary courier-checkbox {{ $disabled }}" />
                                <span class="label-text">Star Cargo</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="courier[]" value="dse" class="checkbox checkbox-sm checkbox-primary courier-checkbox {{ $disabled }}" />
                                <span class="label-text">DSE</span>
                            </label>
                        </div>
                    </div>
                </div>

                @if(hasPermission('settings.shippings.rajaongkir.update'))
                <div class="flex justify-end">
                    <button type="submit" id="calculateBtn" class="btn btn-primary">
                        <span class="iconify lucide--calculator size-4"></span>
                        Calculate Shipping Cost
                    </button>
                </div>
                @endif

                <!-- Result -->
                <div id="calculatorResult" class="hidden">
                    <div class="divider"></div>
                    <h3 class="font-semibold mb-3">Shipping Options</h3>
                    <div id="shippingOptions" class="space-y-2">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
        </div>
            </form>
    </div>

    <!-- Enabled Couriers -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Enabled Couriers</h2>
            <p class="text-sm text-base-content/70 mb-4">Select which couriers to enable for customers</p>

@php
    $canUpdate = hasPermission('settings.shippings.rajaongkir.update');
    $disabled = $canUpdate ? '' : 'disabled';
@endphp

            <form id="couriersForm" action="{{ route('settings.shippings.rajaongkir-config.update') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @php
                        $couriers = $config['couriers'] ?? [];
                    @endphp

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="courier_jne" class="toggle toggle-primary {{ $disabled }}" {{ ($couriers['jne']['enabled'] ?? false) ? 'checked' : '' }} />
                            <span class="label-text font-medium">JNE</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="courier_pos" class="toggle toggle-primary {{ $disabled }}" {{ ($couriers['pos']['enabled'] ?? false) ? 'checked' : '' }} />
                            <span class="label-text font-medium">POS Indonesia</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="courier_tiki" class="toggle toggle-primary {{ $disabled }}" {{ ($couriers['tiki']['enabled'] ?? false) ? 'checked' : '' }} />
                            <span class="label-text font-medium">TIKI</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="courier_rpx" class="toggle toggle-primary {{ $disabled }}" {{ ($couriers['rpx']['enabled'] ?? false) ? 'checked' : '' }} />
                            <span class="label-text font-medium">RPX</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="courier_sicepat" class="toggle toggle-primary {{ $disabled }}" {{ ($couriers['sicepat']['enabled'] ?? false) ? 'checked' : '' }} />
                            <span class="label-text font-medium">SiCepat</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="courier_jnt" class="toggle toggle-primary {{ $disabled }}" {{ ($couriers['jnt']['enabled'] ?? false) ? 'checked' : '' }} />
                            <span class="label-text font-medium">J&T Express</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="courier_wahana" class="toggle toggle-primary {{ $disabled }}" {{ ($couriers['wahana']['enabled'] ?? false) ? 'checked' : '' }} />
                            <span class="label-text font-medium">Wahana</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="courier_ninja" class="toggle toggle-primary {{ $disabled }}" {{ ($couriers['ninja']['enabled'] ?? false) ? 'checked' : '' }} />
                            <span class="label-text font-medium">Ninja Express</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="courier_lion" class="toggle toggle-primary {{ $disabled }}" {{ ($couriers['lion']['enabled'] ?? false) ? 'checked' : '' }} />
                            <span class="label-text font-medium">Lion Parcel</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="courier_anteraja" class="toggle toggle-primary {{ $disabled }}" {{ ($couriers['anteraja']['enabled'] ?? false) ? 'checked' : '' }} />
                            <span class="label-text font-medium">AnterAja</span>
                        </label>
                    </div>
                </div>

                @if(hasPermission('settings.shippings.rajaongkir.update'))
                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-4"></span>
                        Save Couriers
                    </button>
                </div>
                @endif
        </div>
            </form>
    </div>
</div>
@endsection

@section('customjs')
<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Pass Laravel data to JavaScript -->
<script>
    window.csrfToken = '{{ csrf_token() }}';
    window.testApiRoute = '{{ route("settings.shippings.rajaongkir-config.test") }}';
    window.calculateApiRoute = '{{ route("settings.shippings.api.calculate") }}';
    window.provincesApiRoute = '{{ route("settings.shippings.api.provinces") }}';
    window.citiesApiRoute = '{{ route("settings.shippings.api.cities", ["provinceId" => ":provinceId"]) }}';
    window.districtsApiRoute = '{{ route("settings.shippings.api.districts", ["cityId" => ":cityId"]) }}';
</script>

@vite(['resources/js/modules/settings/shippings/rajaongkir-config.js'])
@endsection
