import Toast from "../../../components/toast";
import * as FilePond from 'filepond';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';

// Import CSS
import 'filepond/dist/filepond.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';
import 'select2/dist/css/select2.min.css';

// jQuery and Select2 are loaded from CDN in blade file
// Initialize when DOM is ready
$(document).ready(function() {
    initializeSelect2();
    loadProvinces();
    setupCascadeHandlers();
    loadSavedValues();
    initializeFilePond();
    setupFormHandler();
});

/**
 * Initialize Select2 for dropdowns
 */
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

/**
 * Load provinces from API
 */
async function loadProvinces() {
    const $provinceSelect = $('#store-province');
    $provinceSelect.empty().append('<option value="">Loading provinces...</option>').prop('disabled', true);

    try {
        const response = await fetch('/settings/shippings/api/wilayah/provinces');
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

/**
 * Load regencies based on province code
 */
async function loadRegencies(provinceCode) {
    const $regencySelect = $('#store-regency');
    $regencySelect.empty().append('<option value="">Loading regencies...</option>').prop('disabled', true);
    $('#store-district').empty().append('<option value="">Select regency first</option>').prop('disabled', true);
    $('#store-village').empty().append('<option value="">Select district first</option>').prop('disabled', true);

    try {
        const response = await fetch(`/settings/shippings/api/wilayah/regencies/${provinceCode}`);
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

/**
 * Load districts based on regency code
 */
async function loadDistricts(regencyCode) {
    const $districtSelect = $('#store-district');
    $districtSelect.empty().append('<option value="">Loading districts...</option>').prop('disabled', true);
    $('#store-village').empty().append('<option value="">Select district first</option>').prop('disabled', true);

    try {
        const response = await fetch(`/settings/shippings/api/wilayah/districts/${regencyCode}`);
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

/**
 * Load villages based on district code
 */
async function loadVillages(districtCode) {
    const $villageSelect = $('#store-village');
    $villageSelect.empty().append('<option value="">Loading villages...</option>').prop('disabled', true);

    try {
        const response = await fetch(`/settings/shippings/api/wilayah/villages/${districtCode}`);
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

/**
 * Setup cascade change handlers for location dropdowns
 */
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

/**
 * Load saved location values from backend
 */
function loadSavedValues() {
    // This function will be populated with server data in blade template
    // Check if window has savedLocationData
    if (window.savedLocationData) {
        const data = window.savedLocationData;

        if (data.province_code) {
            setTimeout(() => {
                $('#store-province').val(data.province_code).trigger('change');

                if (data.regency_code) {
                    setTimeout(() => {
                        $('#store-regency').val(data.regency_code).trigger('change');

                        if (data.district_code) {
                            setTimeout(() => {
                                $('#store-district').val(data.district_code).trigger('change');

                                if (data.village_code) {
                                    setTimeout(() => {
                                        $('#store-village').val(data.village_code).trigger('change');
                                    }, 500);
                                }
                            }, 500);
                        }
                    }, 500);
                }
            }, 500);
        }
    }
}

/**
 * Initialize FilePond for logo upload
 */
function initializeFilePond() {
    // Register FilePond plugins
    FilePond.registerPlugin(FilePondPluginImagePreview);

    // Get logo input element
    const logoInput = document.querySelector('#store-logo-upload');
    if (!logoInput) return;

    // FilePond configuration
    const filepondConfig = {
        credits: false,
        allowImagePreview: true,
        imagePreviewHeight: 150,
        stylePanelLayout: 'compact',
        acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml'],
        maxFileSize: '2MB',
        server: {
            process: {
                url: window.uploadLogoUrl || '',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                onload: (response) => {
                    const data = JSON.parse(response);
                    Toast.showToast('Logo uploaded successfully!', 'success');
                    return data.path;
                },
                onerror: (response) => {
                    try {
                        const data = JSON.parse(response);
                        Toast.showToast(data.message || 'Failed to upload logo', 'error');
                    } catch (e) {
                        Toast.showToast('Failed to upload logo', 'error');
                    }
                    return response;
                }
            },
            revert: {
                url: window.deleteLogoUrl || '',
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                onload: () => {
                    Toast.showToast('Logo deleted successfully!', 'success');
                },
                onerror: () => {
                    Toast.showToast('Failed to delete logo', 'error');
                }
            },
            load: (source, load, error) => {
                console.log('FilePond loading file from:', '/storage/' + source);
                const abortController = new AbortController();

                fetch('/storage/' + source, {
                    signal: abortController.signal
                })
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
                        console.error('Error loading file:', err);
                        error('Could not load file');
                    });

                return {
                    abort: () => {
                        abortController.abort();
                    }
                };
            }
        }
    };

    // Add existing logo if available
    if (window.existingLogo) {
        filepondConfig.files = [{
            source: window.existingLogo,
            options: {
                type: 'local'
            }
        }];
    }

    // Create FilePond instance
    const logoPond = FilePond.create(logoInput, filepondConfig);

    // Debug: Log when file is loaded
    logoPond.on('addfile', (error, file) => {
        if (error) {
            console.error('FilePond addfile error:', error);
        } else {
            console.log('FilePond file added:', file);
        }
    });
}

/**
 * Setup form submission handler
 */
function setupFormHandler() {
    const $form = $('#storeInfoForm');

    if ($form.length === 0) {
        console.error('Form #storeInfoForm not found!');
        return;
    }

    $form.on('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const $submitBtn = $form.find('button[type="submit"]');
        const originalBtnText = $submitBtn.html();

        // Show loading state
        $submitBtn.prop('disabled', true).html('<span class="loading loading-spinner loading-sm"></span> Saving...');

        try {
            const response = await fetch($form.attr('action'), {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok && data.success) {
                Toast.showToast(data.message || 'Settings saved successfully!', 'success');
            } else {
                if (data.errors) {
                    const errorMessages = Object.values(data.errors).flat().join(', ');
                    Toast.showToast(errorMessages, 'error');
                } else {
                    Toast.showToast(data.message || 'Failed to save settings', 'error');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            Toast.showToast('An error occurred while saving settings', 'error');
        } finally {
            $submitBtn.prop('disabled', false).html(originalBtnText);
        }
    });
}
