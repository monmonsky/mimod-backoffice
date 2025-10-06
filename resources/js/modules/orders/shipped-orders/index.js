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

    // Complete Order
    $(document).on('click', '.complete-order-btn', async function(e) {
        e.preventDefault();
        const orderId = $(this).data('id');

        if (!confirm('Mark this order as completed?')) return;

        try {
            await Ajax.post(`/orders/shipped-orders/${orderId}/complete`, null, {
                loadingMessage: 'Completing order...',
                successMessage: 'Order completed successfully',
                onSuccess: () => {
                    setTimeout(() => window.location.reload(), 1000);
                }
            });
        } catch (error) {
            // Error already handled by Ajax helper
        }
    });
});
