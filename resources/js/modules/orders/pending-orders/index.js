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

    // Confirm Order
    $(document).on('click', '.confirm-order-btn', async function(e) {
        e.preventDefault();
        const orderId = $(this).data('id');

        if (!confirm('Confirm this order and move to processing?')) return;

        try {
            await Ajax.post(`/orders/pending-orders/${orderId}/confirm`, null, {
                loadingMessage: 'Confirming order...',
                successMessage: 'Order confirmed successfully',
                onSuccess: () => {
                    setTimeout(() => window.location.reload(), 1000);
                }
            });
        } catch (error) {
            // Error already handled by Ajax helper
        }
    });

    // Cancel Order
    $(document).on('click', '.cancel-order-btn', async function(e) {
        e.preventDefault();
        const orderId = $(this).data('id');

        const reason = prompt('Please provide a cancellation reason:');
        if (!reason) return;

        try {
            await Ajax.post(`/orders/pending-orders/${orderId}/cancel`, {
                cancellation_reason: reason
            }, {
                loadingMessage: 'Cancelling order...',
                successMessage: 'Order cancelled successfully',
                onSuccess: () => {
                    setTimeout(() => window.location.reload(), 1000);
                }
            });
        } catch (error) {
            // Error already handled by Ajax helper
        }
    });
});
