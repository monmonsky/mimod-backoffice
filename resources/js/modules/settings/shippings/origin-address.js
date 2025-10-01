import Toast from "../../../components/toast";

// jQuery is loaded from CDN in blade file

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
        const response = await fetch('/settings/shippings/api/wilayah/provinces');
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
        const response = await fetch(`/settings/shippings/api/wilayah/regencies/${provinceCode}`);
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
        const response = await fetch(`/settings/shippings/api/wilayah/districts/${regencyCode}`);
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
        const response = await fetch(`/settings/shippings/api/wilayah/villages/${districtCode}`);
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
            const response = await fetch('/settings/shippings/origin-address', {
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
    if (window.originData && window.originData.province_code) {
        setTimeout(() => {
            $('#origin-province').val(window.originData.province_code).trigger('change');

            if (window.originData.regency_code) {
                setTimeout(() => {
                    $('#origin-regency').val(window.originData.regency_code).trigger('change');

                    if (window.originData.district_code) {
                        setTimeout(() => {
                            $('#origin-district').val(window.originData.district_code).trigger('change');

                            if (window.originData.village_code) {
                                setTimeout(() => {
                                    $('#origin-village').val(window.originData.village_code).trigger('change');
                                }, 500);
                            }
                        }, 500);
                    }
                }, 500);
            }
        }, 500);
    }
}

function showMessage(type, message) {
    // Use Toast component
    Toast.showToast(message, type, 4000);
}
