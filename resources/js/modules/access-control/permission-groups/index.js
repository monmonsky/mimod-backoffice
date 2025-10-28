import $ from 'jquery';
import Ajax from '../../../utils/ajax.js';

$(document).ready(function() {
    // Delete form handler
    $('.delete-form').on('submit', async function(e) {
        e.preventDefault();

        const confirmed = confirm('Are you sure you want to delete this permission group? This action cannot be undone.');
        if (!confirmed) return;

        const $form = $(this);
        const url = $form.attr('action');

        try {
            await Ajax.delete(url, {
                loadingMessage: 'Deleting permission group...',
                successMessage: 'Permission group deleted successfully',
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
