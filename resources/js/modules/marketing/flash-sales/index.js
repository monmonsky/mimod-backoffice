import Ajax from '../../../utils/ajax.js';
import Toast from '../../../components/toast.js';
import $ from 'jquery';

let currentFlashSaleId = null;

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
        $('#flashSalesTableBody').html(`
            <tr id="loadingRow">
                <td colspan="5" class="text-center py-8">
                    <span class="loading loading-spinner loading-md"></span>
                    <p class="mt-2 text-base-content/60">Loading flash sales...</p>
                </td>
            </tr>
        `);

        // Get filter values from form
        const filters = {
            search: $('[name="search"]').val() || '',
            status: $('[name="status"]').val() || '',
            sort_by: $('[name="sort_by"]').val() || 'priority',
            page: page,
            per_page: 20
        };

        // Remove empty values
        Object.keys(filters).forEach(key => {
            if (!filters[key] || filters[key] === '') delete filters[key];
        });

        // Build query string
        const queryString = new URLSearchParams(filters).toString();

        const response = await Ajax.get(`/api/marketing/flash-sales?${queryString}`, {
            showLoading: false,
            showToast: false
        });

        // IMPORTANT: Check response.status NOT response.success
        if (response.status && response.data) {
            renderTable(response.data.flash_sales);
            renderPagination(response.data.flash_sales);
            if (response.data.statistics) {
                updateStatistics(response.data.statistics);
            }
        } else {
            throw new Error('Invalid response structure');
        }
    } catch (error) {
        console.error('Error loading flash sales:', error);
        $('#flashSalesTableBody').html(`
            <tr>
                <td colspan="5" class="text-center py-8 text-error">
                    <span class="iconify lucide--alert-circle size-8 mb-2"></span>
                    <p>Failed to load flash sales. Please refresh the page.</p>
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
    const $tbody = $('#flashSalesTableBody');
    const items = data.data || data;

    if (!items || items.length === 0) {
        $tbody.html(`
            <tr>
                <td colspan="5" class="text-center py-8">
                    <span class="iconify lucide--inbox size-8 mb-2 text-base-content/40"></span>
                    <p class="text-base-content/60">No flash sales found</p>
                </td>
            </tr>
        `);
        return;
    }

    let html = '';
    items.forEach(flashSale => {
        // Determine status badge
        const now = new Date();
        const startTime = new Date(flashSale.start_time);
        const endTime = new Date(flashSale.end_time);
        const isActive = flashSale.is_active && now >= startTime && now <= endTime;
        const isUpcoming = flashSale.is_active && now < startTime;
        const isExpired = now > endTime;

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
                        <div class="font-medium">${flashSale.name}</div>
                        ${flashSale.description ? `<div class="text-sm text-base-content/60">${flashSale.description.substring(0, 50)}${flashSale.description.length > 50 ? '...' : ''}</div>` : ''}
                    </div>
                </td>
                <td>
                    <div class="text-sm">
                        <div>${new Date(flashSale.start_time).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</div>
                        <div class="text-base-content/60">${new Date(flashSale.end_time).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</div>
                    </div>
                </td>
                <td>
                    <span class="badge badge-info badge-sm">${flashSale.priority}</span>
                </td>
                <td>${statusBadge}</td>
                <td class="text-right">
                    <div class="inline-flex gap-2">
                        ${window.hasPermission && window.hasPermission('marketing.flash-sales.view') ? `
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0" onclick="viewFlashSale(${flashSale.id})" title="View Details">
                            <span class="iconify lucide--eye size-5 text-info"></span>
                        </button>
                        ` : ''}
                        ${window.hasPermission && window.hasPermission('marketing.flash-sales.update') ? `
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0" onclick="editFlashSale(${flashSale.id})" title="Edit">
                            <span class="iconify lucide--pencil size-5 text-warning"></span>
                        </button>
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0" onclick="manageProducts(${flashSale.id})" title="Manage Products">
                            <span class="iconify lucide--package size-5 text-primary"></span>
                        </button>
                        ` : ''}
                        ${window.hasPermission && window.hasPermission('marketing.flash-sales.delete') ? `
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0" onclick="deleteFlashSale(${flashSale.id})" title="Delete">
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

    $('#statTotalFlashSales').text(stats.total_flash_sales || 0);
    $('#statActiveFlashSales').text(stats.active_flash_sales || 0);
    $('#statUpcomingFlashSales').text(stats.upcoming_flash_sales || 0);
    $('#statTotalProducts').text(stats.total_products || 0);
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
        $('[name="sort_by"]').val('priority');
        loadData(1);
    });
}

// ============================================
// FORM SUBMIT (Create/Update)
// ============================================

document.getElementById('flashSaleForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = {
        name: formData.get('name'),
        description: formData.get('description'),
        start_time: formData.get('start_time'),
        end_time: formData.get('end_time'),
        priority: parseInt(formData.get('priority')) || 0,
        is_active: formData.get('is_active') ? true : false,
    };

    try {
        if (currentFlashSaleId) {
            await Ajax.update(`/api/marketing/flash-sales/${currentFlashSaleId}`, data, {
                successMessage: 'Flash sale updated successfully',
                showToast: true
            });
        } else {
            await Ajax.create('/api/marketing/flash-sales', data, {
                successMessage: 'Flash sale created successfully',
                showToast: true
            });
        }

        flashSaleModal.close();
        this.reset();
        currentFlashSaleId = null;
        await loadData();
    } catch (error) {
        // Error already handled by Ajax
    }
});

// ============================================
// CRUD OPERATIONS
// ============================================

window.viewFlashSale = async function(id) {
    try {
        const response = await Ajax.get(`/api/marketing/flash-sales/${id}`);

        if (response.status) {
            const { flash_sale, products } = response.data;

            let html = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-base-content/60">Name</div>
                            <div class="font-medium">${flash_sale.name}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Priority</div>
                            <div>${flash_sale.priority}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Start Time</div>
                            <div>${new Date(flash_sale.start_time).toLocaleString('id-ID')}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">End Time</div>
                            <div>${new Date(flash_sale.end_time).toLocaleString('id-ID')}</div>
                        </div>
                    </div>

                    ${flash_sale.description ? `
                    <div>
                        <div class="text-sm text-base-content/60">Description</div>
                        <div class="mt-1">${flash_sale.description}</div>
                    </div>
                    ` : ''}

                    <div class="divider">Products in Flash Sale</div>

                    ${products && products.length > 0 ? `
                        <div class="overflow-x-auto max-h-64">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Original Price</th>
                                        <th>Flash Price</th>
                                        <th>Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${products.map(p => `
                                        <tr>
                                            <td>${p.product_name || p.name}</td>
                                            <td>Rp ${parseFloat(p.original_price || p.price || 0).toLocaleString('id-ID')}</td>
                                            <td class="text-success font-bold">Rp ${parseFloat(p.flash_price || 0).toLocaleString('id-ID')}</td>
                                            <td>${p.flash_stock || 0}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    ` : `
                        <div class="text-center py-4 text-base-content/60">
                            No products added yet
                        </div>
                    `}
                </div>
            `;

            document.getElementById('flashSaleDetails').innerHTML = html;
            viewFlashSaleModal.showModal();
        }
    } catch (error) {
        Toast.showToast('An error occurred', 'error');
    }
};

window.editFlashSale = async function(id) {
    try {
        const response = await Ajax.get(`/api/marketing/flash-sales/${id}`);

        if (response.status) {
            currentFlashSaleId = id;
            const flashSale = response.data.flash_sale;
            const form = document.getElementById('flashSaleForm');

            form.querySelector('[name="name"]').value = flashSale.name;
            form.querySelector('[name="description"]').value = flashSale.description || '';
            form.querySelector('[name="priority"]').value = flashSale.priority;

            // Format datetime-local
            form.querySelector('[name="start_time"]').value = new Date(flashSale.start_time).toISOString().slice(0, 16);
            form.querySelector('[name="end_time"]').value = new Date(flashSale.end_time).toISOString().slice(0, 16);
            form.querySelector('[name="is_active"]').checked = flashSale.is_active;

            document.querySelector('#flashSaleModal h3').textContent = 'Edit Flash Sale';
            flashSaleModal.showModal();
        }
    } catch (error) {
        Toast.showToast('An error occurred', 'error');
    }
};

window.deleteFlashSale = async function(id) {
    if (!confirm('Are you sure you want to delete this flash sale?')) return;

    try {
        await Ajax.destroy(`/api/marketing/flash-sales/${id}`, {
            successMessage: 'Flash sale deleted successfully',
            showToast: true
        });

        await loadData();
    } catch (error) {
        // Error already handled by Ajax
    }
};

window.openCreateModal = function() {
    currentFlashSaleId = null;
    document.getElementById('flashSaleForm').reset();
    document.querySelector('#flashSaleModal h3').textContent = 'Create Flash Sale';
    flashSaleModal.showModal();
};

window.manageProducts = function(id) {
    // TODO: Implement manage products functionality
    Toast.showToast('Manage products feature coming soon', 'info');
};
