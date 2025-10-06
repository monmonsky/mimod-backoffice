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

    // Ship Order
    $(document).on('click', '.ship-order-btn', async function(e) {
        e.preventDefault();
        const orderId = $(this).data('id');

        const tracking = prompt('Enter tracking number:');
        if (!tracking) return;

        const courier = prompt('Enter courier name (e.g., JNE, TIKI, SiCepat):');
        if (!courier) return;

        try {
            await Ajax.post(`/orders/processing-orders/${orderId}/ship`, {
                tracking_number: tracking,
                courier: courier,
                shipping_notes: ''
            }, {
                loadingMessage: 'Shipping order...',
                successMessage: 'Order shipped successfully',
                onSuccess: () => {
                    setTimeout(() => window.location.reload(), 1000);
                }
            });
        } catch (error) {
            // Error already handled by Ajax helper
        }
    });
});
