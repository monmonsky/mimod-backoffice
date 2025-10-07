import $ from 'jquery';
import Ajax from '../../../utils/ajax.js';

$(document).ready(function() {
    // Add Program Button
    $('#addProgramBtn').on('click', function() {
        resetProgramForm();
        document.getElementById('programModal').showModal();
        $('#programModal h3').text('Add Program');
    });

    // Edit Program
    $(document).on('click', '.edit-program-btn', async function(e) {
        e.preventDefault();
        const programId = $(this).data('id');

        try {
            const response = await Ajax.get(`/api/loyalty-programs/${programId}`, {
                loadingMessage: 'Loading program...'
            });

            if (response.success) {
                const program = response.data;

                // Fill form
                $('#program_id').val(program.id);
                $('input[name="name"]').val(program.name);
                $('input[name="code"]').val(program.code);
                $('textarea[name="description"]').val(program.description);
                $('input[name="points_per_currency"]').val(program.points_per_currency);
                $('input[name="currency_per_point"]').val(program.currency_per_point);
                $('input[name="min_points_redeem"]').val(program.min_points_redeem);
                $('input[name="points_expiry_days"]').val(program.points_expiry_days);
                $('input[name="start_date"]').val(program.start_date ? program.start_date.split(' ')[0] : '');
                $('input[name="end_date"]').val(program.end_date ? program.end_date.split(' ')[0] : '');
                $('input[name="is_active"]').prop('checked', program.is_active);

                // Open modal
                document.getElementById('programModal').showModal();
                $('#programModal h3').text('Edit Program');
            }
        } catch (error) {
            console.error('Error loading program:', error);
        }
    });

    // Submit Program Form
    $('#programForm').on('submit', async function(e) {
        e.preventDefault();

        const programId = $('#program_id').val();
        const formData = {
            name: $('input[name="name"]').val(),
            code: $('input[name="code"]').val(),
            description: $('textarea[name="description"]').val(),
            points_per_currency: $('input[name="points_per_currency"]').val(),
            currency_per_point: $('input[name="currency_per_point"]').val(),
            min_points_redeem: $('input[name="min_points_redeem"]').val(),
            points_expiry_days: $('input[name="points_expiry_days"]').val() || null,
            start_date: $('input[name="start_date"]').val() || null,
            end_date: $('input[name="end_date"]').val() || null,
            is_active: $('input[name="is_active"]').is(':checked'),
        };

        try {
            let response;
            if (programId) {
                // Update
                response = await Ajax.put(`/api/loyalty-programs/${programId}`, formData, {
                    loadingMessage: 'Updating program...',
                    successMessage: 'Program updated successfully'
                });
            } else {
                // Create
                response = await Ajax.post('/api/loyalty-programs', formData, {
                    loadingMessage: 'Creating program...',
                    successMessage: 'Program created successfully'
                });
            }

            if (response.success) {
                document.getElementById('programModal').close();
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } catch (error) {
            console.error('Error saving program:', error);
        }
    });

    // Delete Program
    $(document).on('click', '.delete-program-btn', async function(e) {
        e.preventDefault();
        const programId = $(this).data('id');

        if (!confirm('Are you sure you want to delete this program?')) {
            return;
        }

        try {
            const response = await Ajax.delete(`/api/loyalty-programs/${programId}`, {
                loadingMessage: 'Deleting program...',
                successMessage: 'Program deleted successfully'
            });

            if (response.success) {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } catch (error) {
            console.error('Error deleting program:', error);
        }
    });

    // Add Transaction Button
    $('#addTransactionBtn').on('click', async function() {
        resetTransactionForm();

        // Load customers for select
        try {
            const customers = await loadCustomers();
            populateCustomerSelect(customers);
            document.getElementById('transactionModal').showModal();
        } catch (error) {
            console.error('Error loading customers:', error);
        }
    });

    // Submit Transaction Form
    $('#transactionForm').on('submit', async function(e) {
        e.preventDefault();

        const formData = {
            customer_id: $('select[name="customer_id"]').val(),
            transaction_type: $('select[name="transaction_type"]').val(),
            points: parseInt($('input[name="points"]').val()),
            description: $('textarea[name="description"]').val(),
        };

        try {
            const response = await Ajax.post('/api/loyalty-transactions', formData, {
                loadingMessage: 'Creating transaction...',
                successMessage: 'Transaction created successfully'
            });

            if (response.success) {
                document.getElementById('transactionModal').close();
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } catch (error) {
            console.error('Error creating transaction:', error);
        }
    });

    function resetProgramForm() {
        $('#programForm')[0].reset();
        $('#program_id').val('');
        $('input[name="is_active"]').prop('checked', true);
    }

    function resetTransactionForm() {
        $('#transactionForm')[0].reset();
        $('#customer_select').html('<option value="">Select Customer</option>');
    }

    async function loadCustomers() {
        // Simple fetch for customers - you might want to use your API endpoint
        const response = await fetch('/api/customers');
        const data = await response.json();
        return data.data || [];
    }

    function populateCustomerSelect(customers) {
        const select = $('#customer_select');
        select.html('<option value="">Select Customer</option>');

        customers.forEach(customer => {
            select.append(`
                <option value="${customer.id}">
                    ${customer.name} (${customer.email}) - ${customer.loyalty_points || 0} pts
                </option>
            `);
        });
    }
});
