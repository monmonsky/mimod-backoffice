import Ajax from '../../../utils/ajax.js';
import Toast from '../../../components/toast.js';
import $ from 'jquery';

let currentOrderId = null;

$(document).ready(function() {
    loadData();
    initEventListeners();
});

function initEventListeners() {
    // Filter form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadData(1);
    });

    // Clear filters
    $('#clearFilters').on('click', function() {
        $('#filterForm')[0].reset();
        loadData(1);
    });

    // Quick search
    let searchTimeout;
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadData(1);
        }, 500);
    });

    // View order details
    $(document).on('click', '.view-order-btn', async function() {
        const orderId = $(this).data('id');
        await viewOrder(orderId);
    });

    // Update order status
    $(document).on('click', '.update-status-btn', async function() {
        const orderId = $(this).data('id');
        const status = $(this).data('status');
        await updateOrderStatus(orderId, status);
    });

    // Delete order
    $(document).on('click', '.delete-order-btn', async function(e) {
        e.preventDefault();
        const orderId = $(this).data('id');

        if (confirm('Are you sure you want to delete this order?')) {
            await deleteOrder(orderId);
        }
    });

    // Select all checkboxes
    $('#selectAll').on('change', function() {
        $('.checkbox-sm').prop('checked', $(this).prop('checked'));
    });
}

async function loadData(page = 1) {
    try {
        // Show loading state FIRST
        $('#ordersTableBody').html(`
            <tr id="loadingRow">
                <td colspan="7" class="text-center py-8">
                    <span class="loading loading-spinner loading-md"></span>
                    <p class="mt-2 text-base-content/60">Loading orders...</p>
                </td>
            </tr>
        `);

        // Get filter values
        const filters = {
            order_number: $('input[name="order_number"]').val(),
            customer: $('input[name="customer"]').val(),
            status: $('select[name="status"]').val(),
            date_from: $('input[name="date_from"]').val(),
            search: $('#searchInput').val(),
            page: page
        };

        // Remove empty values
        Object.keys(filters).forEach(key => {
            if (!filters[key]) delete filters[key];
        });

        const queryString = $.param(filters);

        const response = await Ajax.get(`/api/orders?${queryString}`, {
            showLoading: false,
            showToast: false
        });

        console.log('Orders API Response:', response);

        if (response.status && response.data) {
            renderTable(response.data.orders);
            renderPagination(response.data.orders);

            if (response.data.statistics) {
                updateStatistics(response.data.statistics);
            }
        } else {
            $('#ordersTableBody').html(`
                <tr>
                    <td colspan="7" class="text-center py-8">
                        <div class="flex flex-col items-center gap-2 text-base-content/60">
                            <span class="iconify lucide--badge-x size-12"></span>
                            <p>Failed to load orders</p>
                        </div>
                    </td>
                </tr>
            `);
        }
    } catch (error) {
        console.error('Error loading orders:', error);
        $('#ordersTableBody').html(`
            <tr>
                <td colspan="7" class="text-center py-8">
                    <div class="flex flex-col items-center gap-2 text-error">
                        <span class="iconify lucide--alert-circle size-12"></span>
                        <p>Error loading orders</p>
                    </div>
                </td>
            </tr>
        `);
    }
}

function renderTable(ordersData) {
    const orders = ordersData.data || [];

    if (!orders || orders.length === 0) {
        $('#ordersTableBody').html(`
            <tr>
                <td colspan="7" class="text-center py-8">
                    <div class="flex flex-col items-center gap-2 text-base-content/60">
                        <span class="iconify lucide--badge-x size-12"></span>
                        <p>No orders found</p>
                    </div>
                </td>
            </tr>
        `);
        return;
    }

    const statusMap = {
        'pending': 'warning',
        'processing': 'info',
        'shipped': 'info',
        'completed': 'success',
        'cancelled': 'error'
    };

    let html = '';
    orders.forEach(order => {
        const badgeType = statusMap[order.status] || 'ghost';
        const createdAt = new Date(order.created_at);
        const dateStr = createdAt.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        const timeStr = createdAt.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

        html += `
            <tr>
                <td>
                    <div class="font-medium">${order.order_number || '-'}</div>
                </td>
                <td>
                    <div class="flex flex-col">
                        <span class="font-medium">${order.customer_name || '-'}</span>
                        <span class="text-xs opacity-60">${order.customer_email || '-'}</span>
                    </div>
                </td>
                <td>
                    <span class="badge badge-sm badge-ghost">${order.items_count || 0} items</span>
                </td>
                <td>
                    <span class="font-medium">Rp ${parseFloat(order.total_amount || 0).toLocaleString('id-ID')}</span>
                </td>
                <td>
                    <span class="badge badge-${badgeType}">${order.status ? order.status.charAt(0).toUpperCase() + order.status.slice(1) : '-'}</span>
                </td>
                <td>
                    <div class="flex flex-col">
                        <span class="text-xs">${dateStr}</span>
                        <span class="text-xs opacity-60">${timeStr}</span>
                    </div>
                </td>
                <td class="text-right">
                    <div class="inline-flex gap-2">
                        ${window.hasPermission && window.hasPermission('orders.all-orders.view') ? `
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0 view-order-btn" data-id="${order.id}" title="View Details">
                            <span class="iconify lucide--eye size-5"></span>
                        </button>
                        ` : ''}
                        ${window.hasPermission && window.hasPermission('orders.all-orders.update-status') && order.status === 'pending' ? `
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0 update-status-btn" data-id="${order.id}" data-status="processing" title="Confirm Order">
                            <span class="iconify lucide--check size-5"></span>
                        </button>
                        ` : ''}
                        ${window.hasPermission && window.hasPermission('orders.all-orders.delete') && (order.status === 'pending' || order.status === 'cancelled') ? `
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0 delete-order-btn" data-id="${order.id}" title="Delete">
                            <span class="iconify lucide--trash-2 size-5"></span>
                        </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `;
    });

    $('#ordersTableBody').html(html);
}

function renderPagination(ordersData) {
    const pagination = ordersData;

    if (!pagination || pagination.last_page <= 1) {
        $('#paginationContainer').html('');
        return;
    }

    let html = `
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="text-sm text-base-content/60">
                Showing ${pagination.from || 0} to ${pagination.to || 0} of ${pagination.total || 0} entries
            </div>
            <div class="join">
    `;

    // Previous button
    html += `
        <button class="join-item btn btn-sm ${pagination.current_page === 1 ? 'btn-disabled' : ''}"
                onclick="loadData(${pagination.current_page - 1})"
                ${pagination.current_page === 1 ? 'disabled' : ''}>
            <span class="iconify lucide--chevron-left size-4"></span>
        </button>
    `;

    // Page numbers
    const maxPages = 5;
    let startPage = Math.max(1, pagination.current_page - Math.floor(maxPages / 2));
    let endPage = Math.min(pagination.last_page, startPage + maxPages - 1);

    if (endPage - startPage + 1 < maxPages) {
        startPage = Math.max(1, endPage - maxPages + 1);
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `
            <button class="join-item btn btn-sm ${i === pagination.current_page ? 'btn-active' : ''}"
                    onclick="loadData(${i})">
                ${i}
            </button>
        `;
    }

    // Next button
    html += `
        <button class="join-item btn btn-sm ${pagination.current_page === pagination.last_page ? 'btn-disabled' : ''}"
                onclick="loadData(${pagination.current_page + 1})"
                ${pagination.current_page === pagination.last_page ? 'disabled' : ''}>
            <span class="iconify lucide--chevron-right size-4"></span>
        </button>
    `;

    html += `
            </div>
        </div>
    `;

    $('#paginationContainer').html(html);
}

function updateStatistics(statistics) {
    $('#statTotalOrders').text(statistics.total_orders || 0);
    $('#statPendingOrders').text(statistics.pending_count || 0);
    $('#statProcessingOrders').text(statistics.processing_count || 0);
    $('#statCompletedOrders').text(statistics.completed_count || 0);
}

async function viewOrder(id) {
    try {
        const response = await Ajax.get(`/api/orders/${id}`, {
            showLoading: true,
            showToast: false
        });

        console.log('Order Detail Response:', response);

        if (response.status && response.data) {
            const order = response.data.order || response.data;

            const statusBadgeMap = {
                'pending': 'warning',
                'processing': 'info',
                'shipped': 'primary',
                'completed': 'success',
                'cancelled': 'error'
            };

            let html = `
                <div class="space-y-6">
                    <!-- Order Info -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-base-content/60">Order Number</label>
                            <p class="font-medium text-lg">${order.order_number || '-'}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-base-content/60">Status</label>
                            <p><span class="badge badge-${statusBadgeMap[order.status] || 'ghost'}">${order.status ? order.status.charAt(0).toUpperCase() + order.status.slice(1) : '-'}</span></p>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div>
                        <h4 class="font-semibold mb-2">Customer Information</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm text-base-content/60">Name</label>
                                <p class="font-medium">${order.customer_name || '-'}</p>
                            </div>
                            <div>
                                <label class="text-sm text-base-content/60">Email</label>
                                <p class="text-sm">${order.customer_email || '-'}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    ${order.items && order.items.length > 0 ? `
                    <div>
                        <h4 class="font-semibold mb-2">Order Items</h4>
                        <div class="overflow-x-auto">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${order.items.map(item => `
                                        <tr>
                                            <td>${item.product_name || item.name || '-'}</td>
                                            <td>${item.quantity || 0}</td>
                                            <td>Rp ${parseFloat(item.price || 0).toLocaleString('id-ID')}</td>
                                            <td>Rp ${parseFloat((item.quantity || 0) * (item.price || 0)).toLocaleString('id-ID')}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    ` : ''}

                    <!-- Shipping & Payment -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-base-content/60">Shipping Address</label>
                            <p class="text-sm">${order.shipping_address || '-'}</p>
                        </div>
                        <div>
                            <label class="text-sm text-base-content/60">Total Amount</label>
                            <p class="font-bold text-xl text-primary">Rp ${parseFloat(order.total_amount || 0).toLocaleString('id-ID')}</p>
                        </div>
                    </div>

                    ${order.notes ? `
                    <div>
                        <label class="text-sm font-medium text-base-content/60">Notes</label>
                        <p class="text-sm bg-base-200 p-3 rounded">${order.notes}</p>
                    </div>
                    ` : ''}

                    <!-- Action Buttons -->
                    ${order.status === 'pending' ? `
                    <div class="flex gap-2 pt-4 border-t">
                        <button class="btn btn-success btn-sm update-status-btn" data-id="${order.id}" data-status="processing" onclick="orderDetailModal.close()">
                            <span class="iconify lucide--check size-4"></span>
                            Accept Order
                        </button>
                        <button class="btn btn-error btn-sm cancel-order-btn" data-id="${order.id}" onclick="orderDetailModal.close()">
                            <span class="iconify lucide--x-circle size-4"></span>
                            Cancel Order
                        </button>
                    </div>
                    ` : order.status === 'processing' ? `
                    <div class="flex gap-2 pt-4 border-t">
                        <button class="btn btn-primary btn-sm update-status-btn" data-id="${order.id}" data-status="shipped" onclick="orderDetailModal.close()">
                            <span class="iconify lucide--truck size-4"></span>
                            Mark as Shipped
                        </button>
                    </div>
                    ` : order.status === 'shipped' ? `
                    <div class="flex gap-2 pt-4 border-t">
                        <button class="btn btn-success btn-sm update-status-btn" data-id="${order.id}" data-status="completed" onclick="orderDetailModal.close()">
                            <span class="iconify lucide--check-circle size-4"></span>
                            Mark as Completed
                        </button>
                    </div>
                    ` : ''}

                    <!-- Timestamps -->
                    <div class="grid grid-cols-2 gap-4 text-xs text-base-content/50 pt-4 border-t">
                        <div>
                            <span>Created: ${new Date(order.created_at).toLocaleString('id-ID')}</span>
                        </div>
                        <div class="text-right">
                            <span>Updated: ${new Date(order.updated_at).toLocaleString('id-ID')}</span>
                        </div>
                    </div>
                </div>
            `;

            $('#orderDetailContent').html(html);
            orderDetailModal.showModal();
        }
    } catch (error) {
        console.error('Error viewing order:', error);
        Toast.error('Failed to load order details');
    }
}

async function updateOrderStatus(id, status) {
    try {
        if (!confirm(`Are you sure you want to change this order status to ${status}?`)) {
            return;
        }

        const response = await Ajax.update(`/api/orders/${id}/status`, { status }, {
            successMessage: 'Order status updated successfully',
            showToast: true
        });

        if (response.status) {
            await loadData();
        }
    } catch (error) {
        console.error('Error updating order status:', error);
    }
}

async function deleteOrder(id) {
    try {
        const response = await Ajax.delete(`/api/orders/${id}`, {
            successMessage: 'Order deleted successfully',
            showToast: true
        });

        if (response.status) {
            await loadData();
        }
    } catch (error) {
        console.error('Error deleting order:', error);
    }
}

// Make loadData globally accessible for pagination
window.loadData = loadData;
