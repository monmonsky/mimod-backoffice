@extends('layouts.app')

@section('title', 'Origin Address')
@section('page_title', 'Settings')
@section('page_subtitle', 'Origin Address')

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
        color: #1f2937 !important;
    }

    .select2-container--default .select2-selection--single:hover {
        border-color: #9ca3af !important;
    }

    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #3b82f6 !important;
        outline: 2px solid #3b82f630 !important;
        outline-offset: 2px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #1f2937 !important;
        line-height: 1.5 !important;
        padding: 0 !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #9ca3af !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 3rem !important;
        right: 0.75rem !important;
    }

    .select2-dropdown {
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        background-color: #ffffff !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        z-index: 9999 !important;
    }

    .select2-search--dropdown {
        padding: 0.75rem !important;
        background-color: #ffffff !important;
    }

    .select2-search--dropdown .select2-search__field {
        border: 1px solid #d1d5db !important;
        border-radius: 0.375rem !important;
        padding: 0.5rem 0.75rem !important;
        background-color: #ffffff !important;
        color: #1f2937 !important;
    }

    .select2-results {
        background-color: #ffffff !important;
    }

    .select2-results__options {
        max-height: 300px !important;
    }

    .select2-container--default .select2-results__option {
        padding: 0.75rem 1.5rem !important;
        background-color: #ffffff !important;
        color: #1f2937 !important;
        line-height: 1.5 !important;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6 !important;
        color: #ffffff !important;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #dbeafe !important;
        color: #1e40af !important;
    }

    .select2-container--default .select2-results__option--disabled {
        color: #9ca3af !important;
        background-color: #f9fafb !important;
    }

    /* Dark mode support */
    [data-theme="dark"] .select2-container--default .select2-selection--single {
        background-color: #1f2937 !important;
        border-color: #374151 !important;
        color: #f9fafb !important;
    }

    [data-theme="dark"] .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #f9fafb !important;
    }

    [data-theme="dark"] .select2-dropdown {
        background-color: #1f2937 !important;
        border-color: #374151 !important;
    }

    [data-theme="dark"] .select2-container--default .select2-results__option {
        background-color: #1f2937 !important;
        color: #f9fafb !important;
    }

    [data-theme="dark"] .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6 !important;
        color: #ffffff !important;
    }
</style>
@endpush

@section('content')
@php
    $canUpdate = hasPermission('settings.shippings.origin.update');
    $disabled = $canUpdate ? '' : 'disabled';
@endphp

<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Origin Address Configuration</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Settings</li>
            <li>Shipping</li>
            <li class="opacity-80">Origin Address</li>
        </ul>
    </div>
</div>

<div class="mt-6 space-y-6">
    <!-- Information Alert -->
    <div class="alert alert-info">
        <span class="iconify lucide--info size-5"></span>
        <div class="flex-1">
            <h4 class="font-medium">Origin Address for Shipping Calculation</h4>
            <p class="text-sm">Configure the warehouse or store address used as the origin point for shipping cost calculations. Data will be retrieved from Wilayah.id API.</p>
        </div>
    </div>

    <!-- Primary Origin Address -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Primary Origin Address</h2>
            <p class="text-sm text-base-content/70 mb-4">Main warehouse or store address for shipping</p>

@php
    $canUpdate = hasPermission('settings.shippings.origin.update');
    $disabled = $canUpdate ? '' : 'disabled';
@endphp

            <form id="originAddressForm" class="space-y-6">
                @csrf

                <!-- Location Name -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Location Name</span>
                    </label>
                    <input type="text" name="location_name" placeholder="e.g., Main Warehouse"
                           class="input input-bordered w-full"
                           value="{{ $origin['location_name'] ?? 'Main Warehouse' }}" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Internal name for this location</span>
                    </label>
                </div>

                <div class="divider"></div>

                <!-- Address Information -->
                <h3 class="font-medium flex items-center gap-2">
                    <span class="iconify lucide--map-pin size-5"></span>
                    Address Details
                </h3>

                <!-- Province -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Province <span class="text-error">*</span></span>
                    </label>
                    <select class="select select-bordered w-full {{ $disabled }}" id="origin-province" name="province_code" required>
                        <option value="">Select Province</option>
                    </select>
                    <input type="hidden" name="province_name" id="province-name">
                </div>

                <!-- Regency / City -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">City / Regency <span class="text-error">*</span></span>
                    </label>
                    <select class="select select-bordered w-full {{ $disabled }}" id="origin-regency" name="regency_code" required disabled>
                        <option value="">Select province first</option>
                    </select>
                    <input type="hidden" name="regency_name" id="regency-name">
                </div>

                <!-- District -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">District / Kecamatan <span class="text-error">*</span></span>
                    </label>
                    <select class="select select-bordered w-full {{ $disabled }}" id="origin-district" name="district_code" required disabled>
                        <option value="">Select regency first</option>
                    </select>
                    <input type="hidden" name="district_name" id="district-name">
                </div>

                <!-- Village -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Village / Kelurahan</span>
                    </label>
                    <select class="select select-bordered w-full {{ $disabled }}" id="origin-village" name="village_code" disabled>
                        <option value="">Select district first</option>
                    </select>
                    <input type="hidden" name="village_name" id="village-name">
                </div>

                <!-- Postal Code -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Postal Code</span>
                    </label>
                    <input type="text" name="postal_code" placeholder="e.g., 12345"
                           class="input input-bordered w-full"
                           value="{{ $origin['postal_code'] ?? '' }}" />
                </div>

                <!-- Full Address -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Full Address <span class="text-error">*</span></span>
                    </label>
                    <textarea name="address" placeholder="Street address, building number, etc."
                              class="textarea textarea-bordered w-full h-24" required>{{ $origin['address'] ?? '' }}</textarea>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Detailed street address</span>
                    </label>
                </div>

                <div class="divider"></div>

                <!-- Contact Information -->
                <h3 class="font-medium flex items-center gap-2">
                    <span class="iconify lucide--user size-5"></span>
                    Contact Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Contact Person</span>
                        </label>
                        <input type="text" name="contact_person" placeholder="Enter contact name"
                               class="input input-bordered w-full"
                               value="{{ $origin['contact_person'] ?? '' }}" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Phone Number</span>
                        </label>
                        <input type="tel" name="phone" placeholder="+62 812 3456 7890"
                               class="input input-bordered w-full"
                               value="{{ $origin['phone'] ?? '' }}" />
                    </div>
                </div>

                @if(hasPermission('settings.shippings.origin.update'))
                <!-- Action Buttons -->
                <div class="card-actions justify-end pt-4">
                    <button type="button" class="btn btn-ghost" onclick="window.location.reload()">
                        <span class="iconify lucide--rotate-ccw size-5"></span>
                        Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-5"></span>
                        Save Origin Address
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
    window.originData = {
        @if(isset($origin['province_code']) && $origin['province_code'])
            province_code: '{{ $origin["province_code"] }}',
            @if(isset($origin['regency_code']) && $origin['regency_code'])
                regency_code: '{{ $origin["regency_code"] }}',
                @if(isset($origin['district_code']) && $origin['district_code'])
                    district_code: '{{ $origin["district_code"] }}',
                    @if(isset($origin['village_code']) && $origin['village_code'])
                        village_code: '{{ $origin["village_code"] }}',
                    @endif
                @endif
            @endif
        @endif
    };
</script>

@vite(['resources/js/modules/settings/shippings/origin-address.js'])
@endsection
