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
                    <select class="select select-bordered w-full" id="origin-province" name="province_code" required>
                        <option value="">Select Province</option>
                    </select>
                    <input type="hidden" name="province_name" id="province-name">
                </div>

                <!-- Regency / City -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">City / Regency <span class="text-error">*</span></span>
                    </label>
                    <select class="select select-bordered w-full" id="origin-regency" name="regency_code" required disabled>
                        <option value="">Select province first</option>
                    </select>
                    <input type="hidden" name="regency_name" id="regency-name">
                </div>

                <!-- District -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">District / Kecamatan <span class="text-error">*</span></span>
                    </label>
                    <select class="select select-bordered w-full" id="origin-district" name="district_code" required disabled>
                        <option value="">Select regency first</option>
                    </select>
                    <input type="hidden" name="district_name" id="district-name">
                </div>

                <!-- Village -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Village / Kelurahan</span>
                    </label>
                    <select class="select select-bordered w-full" id="origin-village" name="village_code" disabled>
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
    $(document).ready(function() {
        // Initialize Select2
        initializeSelect2();

        // Load provinces on page load
        loadProvinces();

        // Cascade event handlers
        setupCascadeHandlers();

        // Form submission
        setupFormSubmission();

        // Load saved values if exists
        loadSavedValues();
    });

    function initializeSelect2() {
        $('#origin-province, #origin-regency, #origin-district, #origin-village').select2({
            placeholder: 'Select an option',
            allowClear: false,
            width: '100%',
            minimumResultsForSearch: 5,
            theme: 'default',
            dropdownAutoWidth: true
        });
    }

    async function loadProvinces() {
        const $provinceSelect = $('#origin-province');
        $provinceSelect.empty().append('<option value="">Loading provinces...</option>').prop('disabled', true);

        try {
            const response = await fetch('/settings/shipping/api/wilayah/provinces');
            const data = await response.json();

            if (data.success && data.data) {
                $provinceSelect.empty().append('<option value="">Select Province</option>');

                data.data.forEach(province => {
                    $provinceSelect.append(new Option(province.name, province.code));
                });

                $provinceSelect.prop('disabled', false);
                console.log(`Loaded ${data.data.length} provinces`);
            } else {
                showMessage('error', 'Failed to load provinces: ' + (data.message || 'Unknown error'));
                $provinceSelect.empty().append('<option value="">Failed to load provinces</option>');
            }
        } catch (error) {
            console.error('Error loading provinces:', error);
            showMessage('error', 'Error loading provinces: ' + error.message);
            $provinceSelect.empty().append('<option value="">Error loading provinces</option>');
        }
    }

    async function loadRegencies(provinceCode) {
        const $regencySelect = $('#origin-regency');
        $regencySelect.empty().append('<option value="">Loading regencies...</option>').prop('disabled', true);

        // Clear dependent dropdowns
        $('#origin-district').empty().append('<option value="">Select regency first</option>').prop('disabled', true);
        $('#origin-village').empty().append('<option value="">Select district first</option>').prop('disabled', true);

        try {
            const response = await fetch(`/settings/shipping/api/wilayah/regencies/${provinceCode}`);
            const data = await response.json();

            if (data.success && data.data) {
                $regencySelect.empty().append('<option value="">Select Regency</option>');

                data.data.forEach(regency => {
                    $regencySelect.append(new Option(regency.name, regency.code));
                });

                $regencySelect.prop('disabled', false);
                console.log(`Loaded ${data.data.length} regencies for province ${provinceCode}`);
            } else {
                showMessage('error', 'Failed to load regencies: ' + (data.message || 'Unknown error'));
                $regencySelect.empty().append('<option value="">Failed to load regencies</option>');
            }
        } catch (error) {
            console.error('Error loading regencies:', error);
            showMessage('error', 'Error loading regencies: ' + error.message);
            $regencySelect.empty().append('<option value="">Error loading regencies</option>');
        }
    }

    async function loadDistricts(regencyCode) {
        const $districtSelect = $('#origin-district');
        $districtSelect.empty().append('<option value="">Loading districts...</option>').prop('disabled', true);

        // Clear dependent dropdown
        $('#origin-village').empty().append('<option value="">Select district first</option>').prop('disabled', true);

        try {
            const response = await fetch(`/settings/shipping/api/wilayah/districts/${regencyCode}`);
            const data = await response.json();

            if (data.success && data.data) {
                $districtSelect.empty().append('<option value="">Select District</option>');

                data.data.forEach(district => {
                    $districtSelect.append(new Option(district.name, district.code));
                });

                $districtSelect.prop('disabled', false);
                console.log(`Loaded ${data.data.length} districts for regency ${regencyCode}`);
            } else {
                showMessage('error', 'Failed to load districts: ' + (data.message || 'Unknown error'));
                $districtSelect.empty().append('<option value="">Failed to load districts</option>');
            }
        } catch (error) {
            console.error('Error loading districts:', error);
            showMessage('error', 'Error loading districts: ' + error.message);
            $districtSelect.empty().append('<option value="">Error loading districts</option>');
        }
    }

    async function loadVillages(districtCode) {
        const $villageSelect = $('#origin-village');
        $villageSelect.empty().append('<option value="">Loading villages...</option>').prop('disabled', true);

        try {
            const response = await fetch(`/settings/shipping/api/wilayah/villages/${districtCode}`);
            const data = await response.json();

            if (data.success && data.data) {
                $villageSelect.empty().append('<option value="">Select Village (Optional)</option>');

                data.data.forEach(village => {
                    $villageSelect.append(new Option(village.name, village.code));
                });

                $villageSelect.prop('disabled', false);
                console.log(`Loaded ${data.data.length} villages for district ${districtCode}`);
            } else {
                showMessage('error', 'Failed to load villages: ' + (data.message || 'Unknown error'));
                $villageSelect.empty().append('<option value="">Failed to load villages</option>');
            }
        } catch (error) {
            console.error('Error loading villages:', error);
            showMessage('error', 'Error loading villages: ' + error.message);
            $villageSelect.empty().append('<option value="">Error loading villages</option>');
        }
    }

    function setupCascadeHandlers() {
        // Province change
        $('#origin-province').on('change', function() {
            const provinceCode = $(this).val();
            const provinceName = $(this).find('option:selected').text();

            $('#province-name').val(provinceName);

            if (provinceCode && provinceCode !== '') {
                loadRegencies(provinceCode);
            } else {
                $('#origin-regency').empty().append('<option value="">Select province first</option>').prop('disabled', true);
                $('#origin-district').empty().append('<option value="">Select regency first</option>').prop('disabled', true);
                $('#origin-village').empty().append('<option value="">Select district first</option>').prop('disabled', true);
            }
        });

        // Regency change
        $('#origin-regency').on('change', function() {
            const regencyCode = $(this).val();
            const regencyName = $(this).find('option:selected').text();

            $('#regency-name').val(regencyName);

            if (regencyCode && regencyCode !== '' && !regencyCode.includes('Loading')) {
                loadDistricts(regencyCode);
            } else {
                $('#origin-district').empty().append('<option value="">Select regency first</option>').prop('disabled', true);
                $('#origin-village').empty().append('<option value="">Select district first</option>').prop('disabled', true);
            }
        });

        // District change
        $('#origin-district').on('change', function() {
            const districtCode = $(this).val();
            const districtName = $(this).find('option:selected').text();

            $('#district-name').val(districtName);

            if (districtCode && districtCode !== '' && !districtCode.includes('Loading')) {
                loadVillages(districtCode);
            } else {
                $('#origin-village').empty().append('<option value="">Select district first</option>').prop('disabled', true);
            }
        });

        // Village change
        $('#origin-village').on('change', function() {
            const villageName = $(this).find('option:selected').text();
            $('#village-name').val(villageName);
        });
    }

    function setupFormSubmission() {
        $('#originAddressForm').on('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitButton = $(this).find('button[type="submit"]');

            submitButton.prop('disabled', true).html('<span class="loading loading-spinner loading-sm"></span> Saving...');

            try {
                const response = await fetch('/settings/shipping/origin-address', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showMessage('success', data.message || 'Origin address saved successfully');
                } else {
                    showMessage('error', data.message || 'Failed to save origin address');
                }
            } catch (error) {
                console.error('Error saving origin address:', error);
                showMessage('error', 'Error saving origin address: ' + error.message);
            } finally {
                submitButton.prop('disabled', false).html('<span class="iconify lucide--save size-5"></span> Save Origin Address');
            }
        });
    }

    function loadSavedValues() {
        // Load saved values from backend if exists
        @if(isset($origin['province_code']) && $origin['province_code'])
            setTimeout(() => {
                $('#origin-province').val('{{ $origin["province_code"] }}').trigger('change');

                @if(isset($origin['regency_code']) && $origin['regency_code'])
                    setTimeout(() => {
                        $('#origin-regency').val('{{ $origin["regency_code"] }}').trigger('change');

                        @if(isset($origin['district_code']) && $origin['district_code'])
                            setTimeout(() => {
                                $('#origin-district').val('{{ $origin["district_code"] }}').trigger('change');

                                @if(isset($origin['village_code']) && $origin['village_code'])
                                    setTimeout(() => {
                                        $('#origin-village').val('{{ $origin["village_code"] }}').trigger('change');
                                    }, 500);
                                @endif
                            }, 500);
                        @endif
                    }, 500);
                @endif
            }, 500);
        @endif
    }

    function showMessage(type, message) {
        // toast.js format: showToast(message, type, duration)
        if (typeof window.showToast === 'function') {
            window.showToast(message, type, 4000);
        } else {
            console.warn('Toast function not loaded, using fallback');
            console.log(`Toast [${type}]: ${message}`);
        }
    }
</script>
@endsection