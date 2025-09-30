import Toast from "../../../components/toast";

// jQuery is loaded from CDN in blade file

console.log('=== RajaOngkir JavaScript Loaded ===');
console.log('Document ready state:', document.readyState);

// Global utility functions
window.togglePassword = function(id) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
    } else {
        input.type = 'password';
    }
};

window.showRawDataModal = showRawDataModal;
window.copyRawDataToClipboard = copyRawDataToClipboard;
window.showFullResponseModal = showFullResponseModal;
window.copyFullResponse = copyFullResponse;
window.copyToClipboard = copyToClipboard;

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
                    Toast.showToast(data.message || 'RajaOngkir configuration saved successfully!', 'success');
                } else {
                    if (data.errors) {
                        const errorMessages = Object.values(data.errors).flat().join(', ');
                        Toast.showToast(errorMessages, 'error');
                    } else {
                        Toast.showToast(data.message || 'Failed to save configuration', 'error');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                Toast.showToast('An error occurred while saving configuration', 'error');
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
                    Toast.showToast(data.message || 'Couriers configuration saved successfully!', 'success');
                } else {
                    if (data.errors) {
                        const errorMessages = Object.values(data.errors).flat().join(', ');
                        Toast.showToast(errorMessages, 'error');
                    } else {
                        Toast.showToast(data.message || 'Failed to save couriers', 'error');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                Toast.showToast('An error occurred while saving couriers', 'error');
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
                const response = await fetch(window.testApiRoute, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    Toast.showToast(data.message || 'Connection test successful!', 'success');
                } else {
                    Toast.showToast(data.message || 'Connection test failed', 'error');
                }

                // Show request and response details in modal
                if (data.request || data.response) {
                    showTestResultModal(data);
                }
            } catch (error) {
                console.error('Error:', error);
                Toast.showToast('An error occurred while testing connection', 'error');
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
                Toast.showToast('Please select at least one courier', 'error');
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
                const response = await fetch(window.calculateApiRoute, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.csrfToken,
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
                        Toast.showToast('Shipping cost calculated successfully!', 'success');
                    } else {
                        console.warn('No shipping options returned from API');
                        console.warn('Full response:', JSON.stringify(data, null, 2));
                        Toast.showToast('No shipping options available for this route', 'warning');

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
                    Toast.showToast(data.message || 'Failed to calculate shipping cost', 'error');
                }
            } catch (error) {
                console.error('Calculate error:', error);
                Toast.showToast('An error occurred while calculating shipping cost', 'error');
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
        const response = await fetch(window.provincesApiRoute);
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
            Toast.showToast('Failed to load provinces', 'error');
        }
    } catch (error) {
        console.error('Error loading provinces:', error);
        Toast.showToast('Failed to load provinces', 'error');
    }
}

// Load cities for a specific province
async function loadCitiesForProvince(provinceId, citySelectElement) {
    if (!provinceId || !citySelectElement) return;

    const $citySelect = $(citySelectElement);

    // Show loading - use empty value to prevent accidental API calls
    $citySelect.empty().append('<option value="">Loading cities...</option>').prop('disabled', true).trigger('change');

    try {
        const response = await fetch(window.citiesApiRoute.replace(':provinceId', provinceId));
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
            Toast.showToast('Failed to load cities', 'error');
        }
    } catch (error) {
        console.error('Error loading cities:', error);
        $citySelect.empty().append('<option value="">Error loading cities</option>');
        Toast.showToast('Failed to load cities', 'error');
    }
}

// Load districts for a specific city
async function loadDistrictsForCity(cityId, districtSelectElement) {
    if (!cityId || !districtSelectElement) return;

    const $districtSelect = $(districtSelectElement);

    // Show loading - use empty value to prevent accidental API calls
    $districtSelect.empty().append('<option value="">Loading districts...</option>').prop('disabled', true).trigger('change');

    try {
        const response = await fetch(window.districtsApiRoute.replace(':cityId', cityId));
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
            Toast.showToast('Failed to load districts', 'error');
        }
    } catch (error) {
        console.error('Error loading districts:', error);
        $districtSelect.empty().append('<option value="">Error loading districts</option>');
        Toast.showToast('Failed to load districts', 'error');
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
        Toast.showToast('JSON copied to clipboard!', 'success');
    }).catch(err => {
        Toast.showToast('Failed to copy', 'error');
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
        Toast.showToast('Full response copied to clipboard!', 'success');
    }).catch(err => {
        Toast.showToast('Failed to copy', 'error');
    });
}

// Copy JSON to clipboard
function copyToClipboard() {
    const jsonContent = document.getElementById('rawJsonContent').textContent;
    navigator.clipboard.writeText(jsonContent).then(() => {
        Toast.showToast('Copied to clipboard!', 'success');
    }).catch(err => {
        Toast.showToast('Failed to copy', 'error');
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
