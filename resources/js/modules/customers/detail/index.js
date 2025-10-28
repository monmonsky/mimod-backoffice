import $ from 'jquery';
import Ajax from '../../../utils/ajax.js';

$(document).ready(function() {
    // Add Address Modal
    $('#addAddressBtn').on('click', function() {
        // TODO: Open modal for adding new address
        console.log('Add address button clicked');
    });

    // Edit Address
    $(document).on('click', '.edit-address-btn', async function(e) {
        e.preventDefault();
        const addressId = $(this).data('id');

        try {
            const response = await Ajax.get(`/api/customer-addresses/${addressId}`, {
                loadingMessage: 'Loading address...'
            });

            if (response.success) {
                // TODO: Open modal with address data for editing
                console.log('Address data:', response.data);
            }
        } catch (error) {
            console.error('Error loading address:', error);
        }
    });

    // Set Default Address
    $(document).on('click', '.set-default-btn', async function(e) {
        e.preventDefault();
        const addressId = $(this).data('id');

        try {
            const response = await Ajax.post(`/api/customer-addresses/${addressId}/set-default`, {}, {
                loadingMessage: 'Setting as default...',
                successMessage: 'Address set as default successfully'
            });

            if (response.success) {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } catch (error) {
            console.error('Error setting default address:', error);
        }
    });

    // Delete Address
    $(document).on('click', '.delete-address-btn', async function(e) {
        e.preventDefault();
        const addressId = $(this).data('id');

        if (!confirm('Are you sure you want to delete this address?')) {
            return;
        }

        try {
            const response = await Ajax.delete(`/api/customer-addresses/${addressId}`, {
                loadingMessage: 'Deleting address...',
                successMessage: 'Address deleted successfully'
            });

            if (response.success) {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } catch (error) {
            console.error('Error deleting address:', error);
        }
    });
});
