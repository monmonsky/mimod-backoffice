import $ from 'jquery';
import Ajax from '../../../utils/ajax.js';

$(document).ready(function() {
    // Delete customer handler
    $('.delete-form').on('submit', async function(e) {
        e.preventDefault();

        const confirmed = confirm('Are you sure you want to delete this customer? This action cannot be undone.');
        if (!confirmed) return;

        const $form = $(this);
        const url = $form.attr('action');

        try {
            await Ajax.delete(url, {
                loadingMessage: 'Deleting customer...',
                successMessage: 'Customer deleted successfully',
                onSuccess: () => {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            });
        } catch (error) {
            // Error already handled by Ajax helper
        }
    });
});

// View customer detail
window.viewCustomer = async function(customerId) {
    const modal = document.getElementById('viewCustomerModal');
    const $content = $('#customerDetails');

    // Show modal
    modal.showModal();

    try {
        const response = await Ajax.get(`/customers/all-customers/${customerId}`, {
            loadingMessage: 'Loading customer details...',
            showToast: false
        });

        const customer = response.data;

        // Build customer details HTML
        let html = `
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-base-content/70">Customer Code</p>
                    <p class="font-medium">${customer.customer_code}</p>
                </div>
                <div>
                    <p class="text-sm text-base-content/70">Name</p>
                    <p class="font-medium">${customer.name}</p>
                </div>
                <div>
                    <p class="text-sm text-base-content/70">Email</p>
                    <p class="font-medium">${customer.email}</p>
                </div>
                <div>
                    <p class="text-sm text-base-content/70">Phone</p>
                    <p class="font-medium">${customer.phone || '-'}</p>
                </div>
                <div>
                    <p class="text-sm text-base-content/70">Segment</p>
                    <p class="font-medium capitalize">${customer.segment}</p>
                </div>
                <div>
                    <p class="text-sm text-base-content/70">VIP Status</p>
                    <p class="font-medium">${customer.is_vip ? 'Yes' : 'No'}</p>
                </div>
                <div>
                    <p class="text-sm text-base-content/70">Total Orders</p>
                    <p class="font-medium">${customer.total_orders}</p>
                </div>
                <div>
                    <p class="text-sm text-base-content/70">Total Spent</p>
                    <p class="font-medium">Rp ${Number(customer.total_spent).toLocaleString('id-ID')}</p>
                </div>
                <div>
                    <p class="text-sm text-base-content/70">Loyalty Points</p>
                    <p class="font-medium">${customer.loyalty_points}</p>
                </div>
                <div>
                    <p class="text-sm text-base-content/70">Status</p>
                    <p class="font-medium capitalize">${customer.status}</p>
                </div>
            </div>
        `;

        // Add addresses if available
        if (customer.addresses && customer.addresses.length > 0) {
            html += `
                <div class="mt-6">
                    <p class="font-medium mb-2">Addresses</p>
                    <div class="space-y-2">
            `;

            customer.addresses.forEach(addr => {
                html += `
                    <div class="p-3 bg-base-200 rounded">
                        <p class="font-medium">
                            ${addr.label}
                            ${addr.is_default ? '<span class="badge badge-primary badge-sm">Default</span>' : ''}
                        </p>
                        <p class="text-sm">${addr.address_line}, ${addr.city}, ${addr.province} ${addr.postal_code}</p>
                        <p class="text-sm text-base-content/70">${addr.phone}</p>
                    </div>
                `;
            });

            html += '</div></div>';
        }

        $content.html(html);

    } catch (error) {
        modal.close();
        // Error already handled by Ajax helper
    }
};

// Edit customer
window.editCustomer = async function(customerId) {
    const modal = document.getElementById('editCustomerModal');

    try {
        const response = await Ajax.get(`/customers/all-customers/${customerId}`, {
            loadingMessage: 'Loading customer data...',
            showToast: false
        });

        const customer = response.data;

        // Populate form
        $('#edit_customer_id').val(customer.id);
        $('#edit_name').val(customer.name);
        $('#edit_email').val(customer.email);
        $('#edit_phone').val(customer.phone || '');
        $('#edit_segment').val(customer.segment);
        $('#edit_status').val(customer.status);
        $('#edit_notes').val(customer.notes || '');

        modal.showModal();

    } catch (error) {
        // Error already handled by Ajax helper
    }
};

// Submit edit customer form
window.submitEditCustomer = async function(event) {
    event.preventDefault();

    const $form = $(event.target);
    const $submitBtn = $form.find('button[type="submit"]');
    const customerId = $('#edit_customer_id').val();

    const formData = {
        name: $('#edit_name').val(),
        email: $('#edit_email').val(),
        phone: $('#edit_phone').val(),
        segment: $('#edit_segment').val(),
        status: $('#edit_status').val(),
        notes: $('#edit_notes').val(),
    };

    try {
        await Ajax.put(`/customers/all-customers/${customerId}`, formData, {
            loadingMessage: 'Updating customer...',
            successMessage: 'Customer updated successfully',
            loadingTarget: $submitBtn,
            useGlobalLoading: false,
            onSuccess: () => {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        });
    } catch (error) {
        // Error already handled by Ajax helper
    }
};

// Delete customer (for action button, not form)
window.deleteCustomer = async function(customerId, event) {
    const confirmed = confirm('Are you sure you want to delete this customer?');
    if (!confirmed) return;

    const $btn = $(event.target).closest('button');

    try {
        await Ajax.delete(`/customers/all-customers/${customerId}`, {
            loadingMessage: 'Deleting customer...',
            successMessage: 'Customer deleted successfully',
            loadingTarget: $btn,
            useGlobalLoading: false,
            onSuccess: () => {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        });
    } catch (error) {
        // Error already handled by Ajax helper
    }
};
