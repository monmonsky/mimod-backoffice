@extends('layouts.app')

@section('title', 'Store Info')
@section('page_title', 'Settings')
@section('page_subtitle', 'Store Information')

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
    <p class="text-lg font-medium">Store Information</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Settings</li>
            <li class="opacity-80">Store Info</li>
        </ul>
    </div>
</div>

<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Basic Information</h2>
            <p class="text-sm text-base-content/70 mb-4">Configure your store's basic details and contact information</p>

            <form id="storeInfoForm" class="space-y-6 mt-3" action="{{ route('settings.store-info.update') }}" method="POST">
                @csrf
                <!-- Store Name -->
                <div class="form-control">
                    <label class="fieldset-label" for="name">
                        <span class="label-text">Store Name <span class="text-error">*</span></span>
                    </label>
                    <input type="text" name="store_name" placeholder="Enter store name" class="input input-bordered w-full" value="{{ $storeInfo['name'] ?? 'Minimoda' }}" required />
                </div>

                <!-- Store Description -->
                <div class="form-control">
                    <label class="fieldset-label" for="name">
                        <span class="label-text">Store Description</span>
                    </label>
                    <textarea name="description" placeholder="Description" id="description" class="textarea w-full">{{ $storeInfo['description'] ?? 'Premium children\'s clothing store offering quality fashion for kids aged 0-12 years.' }}</textarea>
                </div>

                <!-- Store Tagline -->
                <div class="form-control">
                    <label class="fieldset-label">
                        <span class="label-text">Tagline</span>
                    </label>
                    <input type="text" name="tagline" placeholder="Enter tagline" class="input input-bordered w-full" value="{{ $storeInfo['tagline'] ?? 'Fashion for Little Stars' }}" />
                </div>

                <!-- Store Logo -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Store Logo</span>
                    </label>
                    <div class="flex items-start gap-4">
                        <div class="flex-1">
                            <input type="file" id="store-logo-upload" name="logo" accept="image/png,image/jpeg,image/jpg,image/svg+xml" />
                            <label class="label">
                                <span class="label-text-alt text-base-content/60">Recommended size: 200x200px (PNG, JPG, SVG - Max: 2MB)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Contact Information -->
                <h3 class="text-lg font-medium">Contact Information</h3>

                <!-- Email -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Email Address <span class="text-error">*</span></span>
                    </label>
                    <input type="email" name="email" placeholder="store@example.com" class="input input-bordered w-full" value="{{ $storeContact['email'] ?? 'contact@minimoda.com' }}" required />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Primary email for customer communications</span>
                    </label>
                </div>

                <!-- Phone -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Phone Number <span class="text-error">*</span></span>
                    </label>
                    <input type="tel" name="phone" placeholder="+62 812 3456 7890" class="input input-bordered w-full" value="{{ $storeContact['phone'] ?? '+62 812 3456 7890' }}" required />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Customer service contact number</span>
                    </label>
                </div>

                <!-- WhatsApp -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">WhatsApp Number</span>
                    </label>
                    <input type="tel" name="whatsapp" placeholder="+62 812 3456 7890" class="input input-bordered w-full" value="{{ $storeContact['whatsapp'] ?? '+62 812 3456 7890' }}" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">WhatsApp contact for customer support</span>
                    </label>
                </div>

                <div class="divider"></div>

                <!-- Address Information -->
                <h3 class="text-lg font-medium flex items-center gap-2">
                    <span class="iconify lucide--map-pin size-5"></span>
                    Address Information
                </h3>

                <!-- Province -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Province <span class="text-error">*</span></span>
                    </label>
                    <select class="select select-bordered w-full" id="store-province" name="province_code" required>
                        <option value="">Select Province</option>
                    </select>
                    <input type="hidden" name="province_name" id="province-name">
                </div>

                <!-- Regency / City -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">City / Regency <span class="text-error">*</span></span>
                    </label>
                    <select class="select select-bordered w-full" id="store-regency" name="regency_code" required disabled>
                        <option value="">Select province first</option>
                    </select>
                    <input type="hidden" name="regency_name" id="regency-name">
                </div>

                <!-- District -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">District / Kecamatan <span class="text-error">*</span></span>
                    </label>
                    <select class="select select-bordered w-full" id="store-district" name="district_code" required disabled>
                        <option value="">Select regency first</option>
                    </select>
                    <input type="hidden" name="district_name" id="district-name">
                </div>

                <!-- Village -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Village / Kelurahan</span>
                    </label>
                    <select class="select select-bordered w-full" id="store-village" name="village_code" disabled>
                        <option value="">Select district first</option>
                    </select>
                    <input type="hidden" name="village_name" id="village-name">
                </div>

                <!-- Street Address -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Street Address <span class="text-error">*</span></span>
                    </label>
                    <textarea name="street" placeholder="Street address, building number, etc." class="textarea textarea-bordered w-full h-24" required>{{ $storeAddress['street'] ?? 'Jl. Sudirman No. 123' }}</textarea>
                </div>

                <!-- Postal Code & Country -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Postal Code</span>
                        </label>
                        <input type="text" name="postal_code" placeholder="12345" class="input input-bordered w-full" value="{{ $storeAddress['postal_code'] ?? '12180' }}" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Country</span>
                        </label>
                        <input type="text" name="country" placeholder="Country" class="input input-bordered w-full" value="{{ $storeAddress['country'] ?? 'Indonesia' }}" readonly />
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Social Media -->
                <h3 class="text-lg font-medium">Social Media</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Instagram -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Instagram</span>
                        </label>
                        <label class="input input-bordered flex items-center gap-2">
                            <span class="iconify lucide--instagram size-4 text-base-content/60"></span>
                            <input type="text" name="instagram" placeholder="@minimoda_official" class="grow" value="{{ $storeSocial['instagram'] ?? '@minimoda_official' }}" />
                        </label>
                    </div>

                    <!-- Facebook -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Facebook</span>
                        </label>
                        <label class="input input-bordered flex items-center gap-2">
                            <span class="iconify lucide--facebook size-4 text-base-content/60"></span>
                            <input type="text" name="facebook" placeholder="minimoda.official" class="grow" value="{{ $storeSocial['facebook'] ?? 'minimoda.official' }}" />
                        </label>
                    </div>

                    <!-- Twitter -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Twitter/X</span>
                        </label>
                        <label class="input input-bordered flex items-center gap-2">
                            <span class="iconify lucide--twitter size-4 text-base-content/60"></span>
                            <input type="text" name="twitter" placeholder="@minimoda" class="grow" value="{{ $storeSocial['twitter'] ?? '@minimoda' }}" />
                        </label>
                    </div>

                    <!-- TikTok -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">TikTok</span>
                        </label>
                        <label class="input input-bordered flex items-center gap-2">
                            <span class="iconify lucide--video size-4 text-base-content/60"></span>
                            <input type="text" name="tiktok" placeholder="@minimoda_id" class="grow" value="{{ $storeSocial['tiktok'] ?? '@minimoda_id' }}" />
                        </label>
                    </div>

                    <!-- YouTube -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">YouTube</span>
                        </label>
                        <label class="input input-bordered flex items-center gap-2">
                            <span class="iconify lucide--youtube size-4 text-base-content/60"></span>
                            <input type="text" name="youtube" placeholder="Minimoda Official" class="grow" value="{{ $storeSocial['youtube'] ?? '' }}" />
                        </label>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Operating Hours -->
                <h3 class="text-lg font-medium">Operating Hours</h3>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Business Hours</span>
                    </label>
                    <textarea name="operating_hours" placeholder="Enter operating hours" class="textarea w-full">{{ $operatingHours['hours'] ?? "Monday - Friday: 09:00 - 18:00\nSaturday: 09:00 - 15:00\nSunday: Closed" }}</textarea>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Store operating hours for customer reference</span>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-4"></span>
                        Save Changes
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

<!-- CDN for FilePond -->
<link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />
<script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script>
    // Initialize Select2 first
    $(document).ready(function() {
        initializeSelect2();
        loadProvinces();
        setupCascadeHandlers();
        loadSavedValues();
    });

    function initializeSelect2() {
        $('#store-province, #store-regency, #store-district, #store-village').select2({
            placeholder: 'Select an option',
            allowClear: false,
            width: '100%',
            minimumResultsForSearch: 5,
            theme: 'default',
            dropdownAutoWidth: true
        });
    }

    async function loadProvinces() {
        const $provinceSelect = $('#store-province');
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
            }
        } catch (error) {
            console.error('Error loading provinces:', error);
            $provinceSelect.empty().append('<option value="">Error loading provinces</option>');
        }
    }

    async function loadRegencies(provinceCode) {
        const $regencySelect = $('#store-regency');
        $regencySelect.empty().append('<option value="">Loading regencies...</option>').prop('disabled', true);
        $('#store-district').empty().append('<option value="">Select regency first</option>').prop('disabled', true);
        $('#store-village').empty().append('<option value="">Select district first</option>').prop('disabled', true);

        try {
            const response = await fetch(`/settings/shipping/api/wilayah/regencies/${provinceCode}`);
            const data = await response.json();

            if (data.success && data.data) {
                $regencySelect.empty().append('<option value="">Select Regency</option>');
                data.data.forEach(regency => {
                    $regencySelect.append(new Option(regency.name, regency.code));
                });
                $regencySelect.prop('disabled', false);
            }
        } catch (error) {
            console.error('Error loading regencies:', error);
            $regencySelect.empty().append('<option value="">Error loading regencies</option>');
        }
    }

    async function loadDistricts(regencyCode) {
        const $districtSelect = $('#store-district');
        $districtSelect.empty().append('<option value="">Loading districts...</option>').prop('disabled', true);
        $('#store-village').empty().append('<option value="">Select district first</option>').prop('disabled', true);

        try {
            const response = await fetch(`/settings/shipping/api/wilayah/districts/${regencyCode}`);
            const data = await response.json();

            if (data.success && data.data) {
                $districtSelect.empty().append('<option value="">Select District</option>');
                data.data.forEach(district => {
                    $districtSelect.append(new Option(district.name, district.code));
                });
                $districtSelect.prop('disabled', false);
            }
        } catch (error) {
            console.error('Error loading districts:', error);
            $districtSelect.empty().append('<option value="">Error loading districts</option>');
        }
    }

    async function loadVillages(districtCode) {
        const $villageSelect = $('#store-village');
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
            }
        } catch (error) {
            console.error('Error loading villages:', error);
            $villageSelect.empty().append('<option value="">Error loading villages</option>');
        }
    }

    function setupCascadeHandlers() {
        $('#store-province').on('change', function() {
            const provinceCode = $(this).val();
            const provinceName = $(this).find('option:selected').text();
            $('#province-name').val(provinceName);

            if (provinceCode && provinceCode !== '') {
                loadRegencies(provinceCode);
            } else {
                $('#store-regency').empty().append('<option value="">Select province first</option>').prop('disabled', true);
                $('#store-district').empty().append('<option value="">Select regency first</option>').prop('disabled', true);
                $('#store-village').empty().append('<option value="">Select district first</option>').prop('disabled', true);
            }
        });

        $('#store-regency').on('change', function() {
            const regencyCode = $(this).val();
            const regencyName = $(this).find('option:selected').text();
            $('#regency-name').val(regencyName);

            if (regencyCode && regencyCode !== '' && !regencyCode.includes('Loading')) {
                loadDistricts(regencyCode);
            } else {
                $('#store-district').empty().append('<option value="">Select regency first</option>').prop('disabled', true);
                $('#store-village').empty().append('<option value="">Select district first</option>').prop('disabled', true);
            }
        });

        $('#store-district').on('change', function() {
            const districtCode = $(this).val();
            const districtName = $(this).find('option:selected').text();
            $('#district-name').val(districtName);

            if (districtCode && districtCode !== '' && !districtCode.includes('Loading')) {
                loadVillages(districtCode);
            } else {
                $('#store-village').empty().append('<option value="">Select district first</option>').prop('disabled', true);
            }
        });

        $('#store-village').on('change', function() {
            const villageName = $(this).find('option:selected').text();
            $('#village-name').val(villageName);
        });
    }

    function loadSavedValues() {
        @if(isset($storeAddress['province_code']) && $storeAddress['province_code'])
            setTimeout(() => {
                $('#store-province').val('{{ $storeAddress["province_code"] }}').trigger('change');

                @if(isset($storeAddress['regency_code']) && $storeAddress['regency_code'])
                    setTimeout(() => {
                        $('#store-regency').val('{{ $storeAddress["regency_code"] }}').trigger('change');

                        @if(isset($storeAddress['district_code']) && $storeAddress['district_code'])
                            setTimeout(() => {
                                $('#store-district').val('{{ $storeAddress["district_code"] }}').trigger('change');

                                @if(isset($storeAddress['village_code']) && $storeAddress['village_code'])
                                    setTimeout(() => {
                                        $('#store-village').val('{{ $storeAddress["village_code"] }}').trigger('change');
                                    }, 500);
                                @endif
                            }, 500);
                        @endif
                    }, 500);
                @endif
            }, 500);
        @endif
    }

    // Register FilePond plugins
    FilePond.registerPlugin(FilePondPluginImagePreview);

    // Initialize FilePond for logo upload
    const logoInput = document.querySelector('#store-logo-upload');

    const filepondConfig = {
        credits: false,
        allowImagePreview: true,
        imagePreviewHeight: 150,
        stylePanelLayout: 'compact',
        acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml'],
        maxFileSize: '2MB',
        server: {
            process: {
                url: '{{ route("settings.store-info.upload-logo") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                onload: (response) => {
                    const data = JSON.parse(response);
                    showToast('Logo uploaded successfully!', 'success');
                    return data.path;
                },
                onerror: (response) => {
                    const data = JSON.parse(response);
                    showToast(data.message || 'Failed to upload logo', 'error');
                    return response;
                }
            },
            revert: {
                url: '{{ route("settings.store-info.delete-logo") }}',
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                onload: (response) => {
                    const data = JSON.parse(response);
                    showToast('Logo deleted successfully!', 'success');
                },
                onerror: (response) => {
                    showToast('Failed to delete logo', 'error');
                }
            },
            load: (source, load, error, progress, abort, headers) => {
                console.log('FilePond loading file from:', '/storage/' + source);
                fetch('/storage/' + source)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to load image');
                        }
                        return response.blob();
                    })
                    .then(blob => {
                        console.log('File loaded successfully:', blob);
                        load(blob);
                    })
                    .catch(err => {
                        console.error('FilePond load error:', err);
                        error('Failed to load image');
                    });

                return {
                    abort: () => {
                        abort();
                    }
                };
            }
        }
    };

    @if(isset($storeInfo['logo']) && $storeInfo['logo'])
    // Load existing logo
    console.log('Loading existing logo:', '{{ $storeInfo["logo"] }}');
    filepondConfig.files = [{
        source: '{{ $storeInfo["logo"] }}',
        options: {
            type: 'local'
        }
    }];
    @endif

    const logoPond = FilePond.create(logoInput, filepondConfig);

    // Debug: Log when file is loaded
    logoPond.on('addfile', (error, file) => {
        if (error) {
            console.error('FilePond addfile error:', error);
        } else {
            console.log('FilePond file added:', file);
        }
    });

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        const form = document.getElementById('storeInfoForm');

        if (!form) {
            console.error('Form #storeInfoForm not found!');
            return;
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Saving...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showToast(data.message || 'Settings saved successfully!', 'success');
                } else {
                    if (data.errors) {
                        const errorMessages = Object.values(data.errors).flat().join(', ');
                        showToast(errorMessages, 'error');
                    } else {
                        showToast(data.message || 'Failed to save settings', 'error');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('An error occurred while saving settings', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    }
</script>
@endsection