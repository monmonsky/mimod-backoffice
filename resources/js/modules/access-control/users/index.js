import $ from 'jquery';
import Ajax from '../../../utils/ajax.js';

$(document).ready(function () {
    // Search functionality
    $('#searchInput').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('#usersTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Toggle user status
    window.toggleStatus = async function(checkbox) {
        const userId = $(checkbox).data('id');
        const isActive = $(checkbox).is(':checked');

        try {
            await Ajax.post(`/user/${userId}/toggle-active`, null, {
                loadingMessage: 'Updating status...',
                successMessage: 'User status updated successfully',
                onSuccess: () => {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                },
                onError: () => {
                    // Revert checkbox state
                    $(checkbox).prop('checked', !isActive);
                }
            });
        } catch (error) {
            // Error already handled by Ajax helper
        }
    };

    // Delete user
    window.deleteUser = async function(userId, userName) {
        if (!confirm(`Are you sure you want to delete "${userName}"? This action cannot be undone.`)) {
            return;
        }

        try {
            await Ajax.destroy(`/user/${userId}`, {
                loadingMessage: 'Deleting user...',
                successMessage: 'User deleted successfully',
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
});
