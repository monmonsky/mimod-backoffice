import $ from 'jquery';
import Ajax from '../../../utils/ajax.js';

$(document).ready(function() {
    // Search functionality
    $('#searchInput').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('#ordersTable tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(searchTerm) > -1);
        });
    });

    // Status filter
    $('#statusFilter').on('change', function() {
        const status = $(this).val().toLowerCase();
        $('#ordersTable tr').each(function() {
            const $row = $(this);
            if (!status) {
                $row.show();
                return;
            }
            const statusBadge = $row.find('.badge');
            const rowStatus = statusBadge.length ? statusBadge.text().toLowerCase() : '';
            $row.toggle(rowStatus.includes(status));
        });
    });

    // View order details
    $(document).on('click', '.view-order-btn', async function(e) {
        e.preventDefault();
        const orderId = $(this).data('id');
        const modal = document.getElementById('orderDetailModal');
        const content = $('#orderDetailContent');

        content.html('<div class="flex justify-center py-8"><span class="loading loading-spinner loading-lg"></span></div>');
        modal.showModal();

        try {
            const response = await fetch(`/orders/all-orders/${orderId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                const order = data.data;
                content.html(`
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm opacity-60">Order Number</p>
                                <p class="font-semibold">${order.order_number}</p>
                            </div>
                            <div>
                                <p class="text-sm opacity-60">Status</p>
                                <p><span class="badge badge-${order.status === 'completed' ? 'success' : order.status === 'cancelled' ? 'error' : 'warning'}">${order.status}</span></p>
                            </div>
                            <div>
                                <p class="text-sm opacity-60">Customer</p>
                                <p class="font-semibold">${order.customer_name}</p>
                                <p class="text-xs opacity-60">${order.customer_email}</p>
                            </div>
                            <div>
                                <p class="text-sm opacity-60">Total Amount</p>
                                <p class="font-semibold text-lg">Rp ${Number(order.total_amount).toLocaleString('id-ID')}</p>
                            </div>
                        </div>

                        <div class="divider">Order Items</div>

                        <div class="overflow-x-auto">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>SKU</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${order.items.map(item => `
                                        <tr>
                                            <td>
                                                <div>${item.product_name}</div>
                                                <div class="text-xs opacity-60">${item.size || ''} ${item.color || ''}</div>
                                            </td>
                                            <td>${item.sku}</td>
                                            <td>${item.quantity}</td>
                                            <td>Rp ${Number(item.price).toLocaleString('id-ID')}</td>
                                            <td>Rp ${Number(item.total).toLocaleString('id-ID')}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `);
            } else {
                content.html(`<div class="alert alert-error">${data.message}</div>`);
            }
        } catch (error) {
            content.html(`<div class="alert alert-error">Failed to load order details</div>`);
        }
    });

    // Update order status
    $(document).on('click', '.update-status-btn', async function(e) {
        e.preventDefault();
        const orderId = $(this).data('id');
        const status = $(this).data('status');

        if (!confirm(`Are you sure you want to update this order status to ${status}?`)) {
            return;
        }

        try {
            await Ajax.post(`/orders/all-orders/${orderId}/status`, { status }, {
                loadingMessage: 'Updating order status...',
                successMessage: 'Order status updated successfully',
                onSuccess: () => {
                    setTimeout(() => window.location.reload(), 1000);
                }
            });
        } catch (error) {
            // Error already handled by Ajax helper
        }
    });

    // Delete order
    $('.delete-form').on('submit', async function(e) {
        e.preventDefault();

        if (!confirm('Are you sure you want to delete this order? This action cannot be undone.')) {
            return;
        }

        const $form = $(this);
        const url = $form.attr('action');

        try {
            await Ajax.delete(url, {
                loadingMessage: 'Deleting order...',
                successMessage: 'Order deleted successfully',
                onSuccess: () => {
                    setTimeout(() => window.location.reload(), 1000);
                }
            });
        } catch (error) {
            // Error already handled by Ajax helper
        }
    });
});
