import Ajax from '../../../utils/ajax.js';
import Toast from '../../../components/toast.js';
import $ from 'jquery';

let currentBundleId = null;

// ============================================
// INITIALIZATION
// ============================================

$(document).ready(function() {
    loadData();
    initEventListeners();
});

// ============================================
// LOAD DATA FROM API
// ============================================

async function loadData(page = 1) {
    try {
        // Show loading state
        $('#bundleDealsTableBody').html(`
            <tr id="loadingRow">
                <td colspan="7" class="text-center py-8">
                    <span class="loading loading-spinner loading-md"></span>
                    <p class="mt-2 text-base-content/60">Loading bundle deals...</p>
                </td>
            </tr>
        `);

        // Get filter values from form
        const filters = {
            search: $('[name="search"]').val() || '',
            status: $('[name="status"]').val() || '',
            sort_by: $('[name="sort_by"]').val() || 'created_at',
            page: page,
            per_page: 20
        };

        // Remove empty values
        Object.keys(filters).forEach(key => {
            if (!filters[key] || filters[key] === '') delete filters[key];
        });

        // Build query string
        const queryString = new URLSearchParams(filters).toString();

        const response = await Ajax.get(`/api/marketing/bundle-deals?${queryString}`, {
            showLoading: false,
            showToast: false
        });

        console.log('Bundle Deals API Response:', response);

        // IMPORTANT: Check response.status NOT response.success
        if (response.status && response.data) {
            // Handle both possible response structures
            const bundlesData = response.data.bundles || response.data.bundle_deals || response.data;
            renderTable(bundlesData);
            renderPagination(bundlesData);
            if (response.data.statistics) {
                updateStatistics(response.data.statistics);
            }
        } else {
            throw new Error('Invalid response structure');
        }
    } catch (error) {
        console.error('Error loading bundle deals:', error);
        $('#bundleDealsTableBody').html(`
            <tr>
                <td colspan="7" class="text-center py-8 text-error">
                    <span class="iconify lucide--alert-circle size-8 mb-2"></span>
                    <p>Failed to load bundle deals. Please refresh the page.</p>
                    <p class="text-xs mt-2">${error.message || 'Unknown error'}</p>
                </td>
            </tr>
        `);
    }
}

// ============================================
// RENDER FUNCTIONS
// ============================================

function renderTable(data) {
    const $tbody = $('#bundleDealsTableBody');
    const items = data.data || data;

    if (!items || items.length === 0) {
        $tbody.html(`
            <tr>
                <td colspan="7" class="text-center py-8">
                    <span class="iconify lucide--inbox size-8 mb-2 text-base-content/40"></span>
                    <p class="text-base-content/60">No bundle deals found</p>
                </td>
            </tr>
        `);
        return;
    }

    let html = '';
    items.forEach(bundle => {
        // Calculate savings
        const savings = bundle.original_price - bundle.bundle_price;
        const savingsPercent = bundle.original_price > 0 ? (savings / bundle.original_price * 100) : 0;

        // Determine status badge
        const now = new Date();
        const startDate = new Date(bundle.start_date);
        const endDate = new Date(bundle.end_date);
        const isActive = bundle.is_active && now >= startDate && now <= endDate;
        const isUpcoming = bundle.is_active && now < startDate;
        const isExpired = now > endDate;

        let statusBadge = '';
        if (isActive) {
            statusBadge = '<span class="badge badge-success badge-sm">Active</span>';
        } else if (isUpcoming) {
            statusBadge = '<span class="badge badge-info badge-sm">Upcoming</span>';
        } else if (isExpired) {
            statusBadge = '<span class="badge badge-error badge-sm">Expired</span>';
        } else {
            statusBadge = '<span class="badge badge-ghost badge-sm">Inactive</span>';
        }

        html += `
            <tr class="hover">
                <td>
                    <div>
                        <div class="font-medium">${bundle.name}</div>
                        ${bundle.description ? `<div class="text-sm text-base-content/60">${bundle.description.substring(0, 40)}${bundle.description.length > 40 ? '...' : ''}</div>` : ''}
                    </div>
                </td>
                <td>
                    <div class="text-sm">
                        <div class="font-bold text-success">Rp ${parseFloat(bundle.bundle_price).toLocaleString('id-ID')}</div>
                        <div class="line-through text-base-content/60">Rp ${parseFloat(bundle.original_price).toLocaleString('id-ID')}</div>
                    </div>
                </td>
                <td>
                    <div class="text-sm">
                        <div class="text-success font-medium">${savingsPercent.toFixed(0)}%</div>
                        <div class="text-base-content/60">Rp ${savings.toLocaleString('id-ID')}</div>
                    </div>
                </td>
                <td>
                    <div class="text-sm">
                        <div>${bundle.sold_count || 0} sold</div>
                        ${bundle.stock_limit ? `<div class="text-base-content/60">of ${bundle.stock_limit}</div>` : ''}
                    </div>
                </td>
                <td>
                    <div class="text-sm">
                        <div>${new Date(bundle.start_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}</div>
                        <div class="text-base-content/60">${new Date(bundle.end_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}</div>
                    </div>
                </td>
                <td>${statusBadge}</td>
                <td class="text-right">
                    <div class="inline-flex gap-2">
                        ${window.hasPermission && window.hasPermission('marketing.bundle-deals.view') ? `
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0" onclick="viewBundle(${bundle.id})" title="View Details">
                            <span class="iconify lucide--eye size-5 text-info"></span>
                        </button>
                        ` : ''}
                        ${window.hasPermission && window.hasPermission('marketing.bundle-deals.update') ? `
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0" onclick="editBundle(${bundle.id})" title="Edit">
                            <span class="iconify lucide--pencil size-5 text-warning"></span>
                        </button>
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0" onclick="manageItems(${bundle.id})" title="Manage Items">
                            <span class="iconify lucide--package size-5 text-success"></span>
                        </button>
                        ` : ''}
                        ${window.hasPermission && window.hasPermission('marketing.bundle-deals.delete') ? `
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0" onclick="deleteBundle(${bundle.id})" title="Delete">
                            <span class="iconify lucide--trash-2 size-5 text-error"></span>
                        </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `;
    });

    $tbody.html(html);
}

function renderPagination(data) {
    const $container = $('#paginationContainer');

    if (!data.last_page || data.last_page <= 1) {
        $container.html('');
        return;
    }

    let html = '<div class="flex justify-between items-center">';

    // Pagination info
    html += `<p class="text-sm text-base-content/60">Showing ${data.from} to ${data.to} of ${data.total} results</p>`;

    // Pagination buttons
    html += '<div class="join">';

    // Previous button
    html += `
        <button class="join-item btn btn-sm" ${data.current_page === 1 ? 'disabled' : ''} onclick="loadDataPage(${data.current_page - 1})">
            «
        </button>
    `;

    // Page numbers (show max 5 pages)
    const startPage = Math.max(1, data.current_page - 2);
    const endPage = Math.min(data.last_page, data.current_page + 2);

    for (let i = startPage; i <= endPage; i++) {
        html += `
            <button class="join-item btn btn-sm ${i === data.current_page ? 'btn-active' : ''}" onclick="loadDataPage(${i})">
                ${i}
            </button>
        `;
    }

    // Next button
    html += `
        <button class="join-item btn btn-sm" ${data.current_page === data.last_page ? 'disabled' : ''} onclick="loadDataPage(${data.current_page + 1})">
            »
        </button>
    `;

    html += '</div></div>';

    $container.html(html);
}

// Make loadData accessible for pagination
window.loadDataPage = function(page) {
    loadData(page);
};

function updateStatistics(stats) {
    if (!stats) return;

    $('#statTotalBundles').text(stats.total_bundles || 0);
    $('#statActiveBundles').text(stats.active_bundles || 0);
    $('#statTotalSold').text(stats.total_sold || 0);
    $('#statTotalRevenue').text('Rp ' + (parseFloat(stats.total_revenue || 0).toLocaleString('id-ID')));
}

// ============================================
// EVENT LISTENERS
// ============================================

function initEventListeners() {
    // Filter form submit
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadData(1);
    });

    // Clear filters
    $('#clearFilters').on('click', function() {
        $('[name="search"]').val('');
        $('[name="status"]').val('');
        $('[name="sort_by"]').val('created_at');
        loadData(1);
    });
}

// ============================================
// FORM SUBMIT (Create/Update)
// ============================================

document.getElementById('bundleForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = {
        name: formData.get('name'),
        description: formData.get('description'),
        bundle_price: parseFloat(formData.get('bundle_price')) || 0,
        original_price: parseFloat(formData.get('original_price')) || 0,
        start_date: formData.get('start_date'),
        end_date: formData.get('end_date'),
        stock_limit: formData.get('stock_limit') ? parseInt(formData.get('stock_limit')) : null,
        is_active: formData.get('is_active') ? true : false,
    };

    try {
        if (currentBundleId) {
            await Ajax.update(`/api/marketing/bundle-deals/${currentBundleId}`, data, {
                successMessage: 'Bundle deal updated successfully',
                showToast: true
            });
        } else {
            await Ajax.create('/api/marketing/bundle-deals', data, {
                successMessage: 'Bundle deal created successfully',
                showToast: true
            });
        }

        bundleModal.close();
        this.reset();
        currentBundleId = null;
        await loadData();
    } catch (error) {
        // Error already handled by Ajax
    }
});

// ============================================
// CRUD OPERATIONS
// ============================================

window.viewBundle = async function(id) {
    try {
        const response = await Ajax.get(`/api/marketing/bundle-deals/${id}`);

        if (response.status) {
            const { bundle, items } = response.data;

            const savings = bundle.original_price - bundle.bundle_price;
            const savingsPercent = bundle.original_price > 0 ? (savings / bundle.original_price * 100) : 0;

            let html = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-base-content/60">Name</div>
                            <div class="font-medium">${bundle.name}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Status</div>
                            <div>${bundle.is_active ? '<span class="badge badge-success badge-sm">Active</span>' : '<span class="badge badge-ghost badge-sm">Inactive</span>'}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Bundle Price</div>
                            <div class="font-bold text-success">Rp ${parseFloat(bundle.bundle_price).toLocaleString('id-ID')}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Original Price</div>
                            <div class="line-through">Rp ${parseFloat(bundle.original_price).toLocaleString('id-ID')}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Savings</div>
                            <div class="text-success">${savingsPercent.toFixed(0)}% (Rp ${savings.toLocaleString('id-ID')})</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Sold Count</div>
                            <div>${bundle.sold_count || 0}${bundle.stock_limit ? ` / ${bundle.stock_limit}` : ''}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Valid Period</div>
                            <div>${new Date(bundle.start_date).toLocaleDateString('id-ID')} - ${new Date(bundle.end_date).toLocaleDateString('id-ID')}</div>
                        </div>
                    </div>

                    ${bundle.description ? `
                    <div>
                        <div class="text-sm text-base-content/60">Description</div>
                        <div class="mt-1">${bundle.description}</div>
                    </div>
                    ` : ''}

                    <div class="divider">Items in Bundle</div>

                    ${items && items.length > 0 ? `
                        <div class="overflow-x-auto max-h-64">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${items.map(item => `
                                        <tr>
                                            <td>${item.product_name || item.name}</td>
                                            <td>${item.quantity}</td>
                                            <td>Rp ${parseFloat(item.price || 0).toLocaleString('id-ID')}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    ` : `
                        <div class="text-center py-4 text-base-content/60">
                            No items added yet
                        </div>
                    `}
                </div>
            `;

            document.getElementById('bundleDetails').innerHTML = html;
            viewBundleModal.showModal();
        }
    } catch (error) {
        Toast.showToast('An error occurred', 'error');
    }
};

window.editBundle = async function(id) {
    try {
        const response = await Ajax.get(`/api/marketing/bundle-deals/${id}`);

        if (response.status) {
            currentBundleId = id;
            const bundle = response.data.bundle;
            const form = document.getElementById('bundleForm');

            form.querySelector('[name="name"]').value = bundle.name;
            form.querySelector('[name="description"]').value = bundle.description || '';
            form.querySelector('[name="bundle_price"]').value = bundle.bundle_price;
            form.querySelector('[name="original_price"]').value = bundle.original_price;
            form.querySelector('[name="stock_limit"]').value = bundle.stock_limit || '';

            // Format date
            form.querySelector('[name="start_date"]').value = bundle.start_date.split(' ')[0];
            form.querySelector('[name="end_date"]').value = bundle.end_date.split(' ')[0];
            form.querySelector('[name="is_active"]').checked = bundle.is_active;

            document.querySelector('#bundleModal h3').textContent = 'Edit Bundle Deal';
            bundleModal.showModal();
        }
    } catch (error) {
        Toast.showToast('An error occurred', 'error');
    }
};

window.deleteBundle = async function(id) {
    if (!confirm('Are you sure you want to delete this bundle deal?')) return;

    try {
        await Ajax.destroy(`/api/marketing/bundle-deals/${id}`, {
            successMessage: 'Bundle deal deleted successfully',
            showToast: true
        });

        await loadData();
    } catch (error) {
        // Error already handled by Ajax
    }
};

window.openCreateModal = function() {
    currentBundleId = null;
    document.getElementById('bundleForm').reset();
    document.querySelector('#bundleModal h3').textContent = 'Create Bundle Deal';
    bundleModal.showModal();
};

window.manageItems = function(id) {
    // TODO: Implement manage items functionality
    Toast.showToast('Manage items feature coming soon', 'info');
};
