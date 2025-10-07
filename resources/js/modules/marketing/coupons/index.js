import Ajax from '../../../utils/ajax.js';
import Toast from '../../../components/toast.js';
import $ from 'jquery';

let currentCouponId = null;

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
        $('#couponsTableBody').html(`
            <tr id="loadingRow">
                <td colspan="8" class="text-center py-8">
                    <span class="loading loading-spinner loading-md"></span>
                    <p class="mt-2 text-base-content/60">Loading coupons...</p>
                </td>
            </tr>
        `);

        // Get filter values from form
        const filters = {
            search: $('[name="search"]').val() || '',
            type: $('[name="type"]').val() || '',
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

        const response = await Ajax.get(`/api/marketing/coupons?${queryString}`, {
            showLoading: false,
            showToast: false
        });

        if (response.status && response.data) {
            renderTable(response.data.coupons);
            renderPagination(response.data.coupons);
            if (response.data.statistics) {
                updateStatistics(response.data.statistics);
            }
        } else {
            throw new Error('Invalid response structure');
        }
    } catch (error) {
        console.error('Error loading coupons:', error);
        $('#couponsTableBody').html(`
            <tr>
                <td colspan="8" class="text-center py-8 text-error">
                    <span class="iconify lucide--alert-circle size-8 mb-2"></span>
                    <p>Failed to load coupons. Please refresh the page.</p>
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
    const $tbody = $('#couponsTableBody');
    const items = data.data || data;

    if (!items || items.length === 0) {
        $tbody.html(`
            <tr>
                <td colspan="8" class="text-center py-8">
                    <span class="iconify lucide--inbox size-8 mb-2 text-base-content/40"></span>
                    <p class="text-base-content/60">No coupons found</p>
                </td>
            </tr>
        `);
        return;
    }

    let html = '';
    items.forEach(coupon => {
        // Type badge
        const typeBadges = {
            percentage: '<span class="badge badge-info badge-sm">Percentage</span>',
            fixed: '<span class="badge badge-success badge-sm">Fixed Amount</span>',
            free_shipping: '<span class="badge badge-warning badge-sm">Free Shipping</span>'
        };

        // Value display
        const valueDisplay = coupon.type === 'percentage'
            ? `${coupon.value}%`
            : coupon.type === 'fixed'
            ? `Rp ${parseFloat(coupon.value).toLocaleString('id-ID')}`
            : 'Free';

        // Status badge
        const now = new Date();
        const startDate = new Date(coupon.start_date);
        const endDate = new Date(coupon.end_date);
        const isActive = coupon.is_active && now >= startDate && now <= endDate;
        const isUpcoming = coupon.is_active && now < startDate;
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
                <td><code class="font-bold">${coupon.code}</code></td>
                <td>${coupon.name}</td>
                <td>${typeBadges[coupon.type] || ''}</td>
                <td>${valueDisplay}</td>
                <td><span class="text-sm">${coupon.usage_count} / ${coupon.usage_limit || '∞'}</span></td>
                <td>
                    <div class="text-sm">
                        <div>${new Date(coupon.start_date).toLocaleDateString('id-ID')}</div>
                        <div class="text-base-content/60">${new Date(coupon.end_date).toLocaleDateString('id-ID')}</div>
                    </div>
                </td>
                <td>${statusBadge}</td>
                <td class="text-right">
                    <div class="inline-flex gap-2">
                        ${window.hasPermission && window.hasPermission('marketing.coupons.view') ? `
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0" onclick="viewCoupon(${coupon.id})" title="View Details">
                            <span class="iconify lucide--eye size-5"></span>
                        </button>
                        ` : ''}
                        ${window.hasPermission && window.hasPermission('marketing.coupons.update') ? `
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0" onclick="editCoupon(${coupon.id})" title="Edit">
                            <span class="iconify lucide--pencil size-5"></span>
                        </button>
                        ` : ''}
                        ${window.hasPermission && window.hasPermission('marketing.coupons.delete') ? `
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0" onclick="deleteCoupon(${coupon.id})" title="Delete">
                            <span class="iconify lucide--trash-2 size-5"></span>
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

function updateStatistics(stats) {
    if (!stats) return;

    $('#statTotalCoupons').text(stats.total_coupons || 0);
    $('#statActiveCoupons').text(stats.active_coupons || 0);
    $('#statTotalUsage').text(stats.total_usage || 0);

    // Format total discount as currency
    const totalDiscount = stats.total_discount || 0;
    $('#statTotalDiscount').text('Rp ' + parseFloat(totalDiscount).toLocaleString('id-ID'));
}

// Make loadData accessible for pagination
window.loadDataPage = function(page) {
    loadData(page);
};

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
        $('[name="type"]').val('');
        $('[name="status"]').val('');
        $('[name="sort_by"]').val('created_at');
        loadData(1);
    });
}

// ============================================
// CRUD OPERATIONS
// ============================================

window.openCreateModal = function() {
    currentCouponId = null;
    document.getElementById('couponForm').reset();
    document.querySelector('#couponModal h3').textContent = 'Create Coupon';
    couponModal.showModal();
};

window.viewCoupon = async function(id) {
    try {
        const response = await Ajax.get(`/api/marketing/coupons/${id}`);

        if (response.status) {
            const { coupon, usage } = response.data;

            const typeLabels = {
                percentage: 'Percentage',
                fixed: 'Fixed Amount',
                free_shipping: 'Free Shipping'
            };

            const valueDisplay = coupon.type === 'percentage'
                ? `${coupon.value}%`
                : coupon.type === 'fixed'
                ? `Rp ${parseFloat(coupon.value).toLocaleString('id-ID')}`
                : 'Free';

            let html = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-base-content/60">Code</div>
                            <div class="font-bold text-lg text-primary">${coupon.code}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Name</div>
                            <div class="font-medium">${coupon.name}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Type</div>
                            <div>${typeLabels[coupon.type]}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Discount Value</div>
                            <div class="font-bold text-success">${valueDisplay}</div>
                        </div>
                        ${coupon.min_purchase ? `
                        <div>
                            <div class="text-sm text-base-content/60">Minimum Purchase</div>
                            <div>Rp ${parseFloat(coupon.min_purchase).toLocaleString('id-ID')}</div>
                        </div>
                        ` : ''}
                        ${coupon.max_discount ? `
                        <div>
                            <div class="text-sm text-base-content/60">Maximum Discount</div>
                            <div>Rp ${parseFloat(coupon.max_discount).toLocaleString('id-ID')}</div>
                        </div>
                        ` : ''}
                        <div>
                            <div class="text-sm text-base-content/60">Usage</div>
                            <div>${coupon.usage_count} / ${coupon.usage_limit || '∞'}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Per Customer Limit</div>
                            <div>${coupon.usage_limit_per_customer}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Start Date</div>
                            <div>${new Date(coupon.start_date).toLocaleString('id-ID')}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">End Date</div>
                            <div>${new Date(coupon.end_date).toLocaleString('id-ID')}</div>
                        </div>
                    </div>

                    ${coupon.description ? `
                    <div>
                        <div class="text-sm text-base-content/60">Description</div>
                        <div class="mt-1">${coupon.description}</div>
                    </div>
                    ` : ''}

                    <div class="divider">Usage History</div>

                    ${usage.length > 0 ? `
                        <div class="overflow-x-auto max-h-64">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Discount Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${usage.map(u => `
                                        <tr>
                                            <td>${new Date(u.used_at).toLocaleString('id-ID')}</td>
                                            <td>Customer #${u.customer_id}</td>
                                            <td>Rp ${parseFloat(u.discount_amount).toLocaleString('id-ID')}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    ` : `
                        <div class="text-center py-4 text-base-content/60">
                            No usage history yet
                        </div>
                    `}
                </div>
            `;

            document.getElementById('couponDetails').innerHTML = html;
            viewCouponModal.showModal();
        }
    } catch (error) {
        Toast.showToast('An error occurred', 'error')(error);
    }
};

window.editCoupon = async function(id) {
    try {
        const response = await Ajax.get(`/api/marketing/coupons/${id}`);

        if (response.status) {
            currentCouponId = id;
            const coupon = response.data.coupon;
            const form = document.getElementById('couponForm');

            form.querySelector('[name="code"]').value = coupon.code;
            form.querySelector('[name="name"]').value = coupon.name;
            form.querySelector('[name="description"]').value = coupon.description || '';
            form.querySelector('[name="type"]').value = coupon.type;
            form.querySelector('[name="value"]').value = coupon.value;
            form.querySelector('[name="min_purchase"]').value = coupon.min_purchase || '';
            form.querySelector('[name="max_discount"]').value = coupon.max_discount || '';
            form.querySelector('[name="usage_limit"]').value = coupon.usage_limit || '';
            form.querySelector('[name="usage_limit_per_customer"]').value = coupon.usage_limit_per_customer;

            // Format datetime-local
            form.querySelector('[name="start_date"]').value = new Date(coupon.start_date).toISOString().slice(0, 16);
            form.querySelector('[name="end_date"]').value = new Date(coupon.end_date).toISOString().slice(0, 16);
            form.querySelector('[name="is_active"]').checked = coupon.is_active;

            document.querySelector('#couponModal h3').textContent = 'Edit Coupon';
            couponModal.showModal();
        }
    } catch (error) {
        Toast.showToast('An error occurred', 'error')(error);
    }
};

window.deleteCoupon = async function(id) {
    if (!confirm('Are you sure you want to delete this coupon?')) return;

    try {
        await Ajax.destroy(`/api/marketing/coupons/${id}`, {
            successMessage: 'Coupon deleted successfully',
            showToast: true
        });

        await loadData();
    } catch (error) {
        // Error already handled by Ajax
    }
};

document.getElementById('couponForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = {
        code: formData.get('code'),
        name: formData.get('name'),
        description: formData.get('description'),
        type: formData.get('type'),
        value: parseFloat(formData.get('value')),
        min_purchase: formData.get('min_purchase') ? parseFloat(formData.get('min_purchase')) : null,
        max_discount: formData.get('max_discount') ? parseFloat(formData.get('max_discount')) : null,
        usage_limit: formData.get('usage_limit') ? parseInt(formData.get('usage_limit')) : null,
        usage_limit_per_customer: parseInt(formData.get('usage_limit_per_customer')),
        start_date: formData.get('start_date'),
        end_date: formData.get('end_date'),
        is_active: formData.get('is_active') ? true : false,
    };

    try {
        if (currentCouponId) {
            await Ajax.update(`/api/marketing/coupons/${currentCouponId}`, data, {
                successMessage: 'Coupon updated successfully',
                showToast: true
            });
        } else {
            await Ajax.create('/api/marketing/coupons', data, {
                successMessage: 'Coupon created successfully',
                showToast: true
            });
        }

        couponModal.close();
        this.reset();
        currentCouponId = null;
        await loadData();
    } catch (error) {
        // Error already handled by Ajax
    }
});
