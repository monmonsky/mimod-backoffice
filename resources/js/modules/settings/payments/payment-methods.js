import Ajax from '../../../utils/ajax.js';
import Toast from '../../../components/toast.js';

// jQuery is loaded from CDN
/* global $ */

$(document).ready(function() {
    initTogglePaymentMethod();
    initAddBankForm();
    initBankSettings();
});

/**
 * Initialize toggle payment method (activate/deactivate)
 */
function initTogglePaymentMethod() {
    $('.toggle-payment-method').on('change', async function() {
        const $toggle = $(this);
        const method = $toggle.data('method');
        const isActive = $toggle.is(':checked');

        try {
            await Ajax.post(`/settings/payments/methods/${method}/toggle`, {
                is_active: isActive ? 1 : 0
            }, {
                loadingMessage: isActive ? 'Activating payment method...' : 'Deactivating payment method...',
                successMessage: `Payment method ${isActive ? 'activated' : 'deactivated'} successfully`,
                onSuccess: () => {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            });
        } catch (error) {
            // Revert toggle state on error
            $toggle.prop('checked', !isActive);
        }
    });
}

/**
 * Initialize add bank account form
 */
function initAddBankForm() {
    const $form = $('#addBankForm');

    if ($form.length === 0) return;

    $form.on('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const requestData = {
            bank_name: formData.get('bank_name'),
            account_number: formData.get('account_number'),
            account_holder: formData.get('account_holder'),
            branch: formData.get('branch')
        };

        try {
            await Ajax.post('/settings/payments/methods/banks/store', requestData, {
                loadingMessage: 'Adding bank account...',
                successMessage: 'Bank account added successfully',
                onSuccess: () => {
                    // Close modal
                    document.getElementById('add_bank_account_modal').close();

                    // Reset form
                    $form[0].reset();

                    // Reload page
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            });
        } catch (error) {
            // Error already handled by Ajax helper
        }
    });
}

/**
 * Initialize bank settings
 */
function initBankSettings() {
    // Bank settings modal functionality will be added here
    $('.btn-bank-settings').on('click', function() {
        // Load bank settings
        Toast.showToast('Bank settings feature coming soon', 'info');
    });
}

/**
 * Edit bank account
 */
window.editBankAccount = async function(bankId) {
    try {
        const result = await Ajax.get(`/settings/payments/methods/banks/${bankId}`, {
            loadingMessage: 'Loading bank details...',
            useGlobalLoading: false
        });

        // Populate edit modal with data
        $('#edit_bank_id').val(result.data.id);
        $('#edit_bank_name').val(result.data.bank_name);
        $('#edit_account_number').val(result.data.account_number);
        $('#edit_account_holder').val(result.data.account_holder);
        $('#edit_branch').val(result.data.branch);

        // Show edit modal
        document.getElementById('edit_bank_account_modal').showModal();
    } catch (error) {
        // Error already handled by Ajax helper
    }
};

/**
 * Delete bank account
 */
window.deleteBankAccount = async function(bankId, bankName) {
    if (!confirm(`Are you sure you want to delete bank account "${bankName}"?\n\nThis action cannot be undone.`)) {
        return;
    }

    try {
        await Ajax.delete(`/settings/payments/methods/banks/${bankId}`, {
            loadingMessage: 'Deleting bank account...',
            successMessage: 'Bank account deleted successfully',
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

/**
 * Toggle bank account active status
 */
window.toggleBankActive = async function(bankId) {
    try {
        await Ajax.post(`/settings/payments/methods/banks/${bankId}/toggle`, null, {
            loadingMessage: 'Updating bank status...',
            successMessage: 'Bank status updated successfully',
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
