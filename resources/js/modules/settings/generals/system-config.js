import Ajax from '../../../utils/ajax.js';

// jQuery is loaded from CDN in blade file
/* global $ */

// Initialize when DOM is ready
$(document).ready(function() {
    setupFormHandler();
});

/**
 * Setup form submission handler
 */
function setupFormHandler() {
    const $form = $('#systemConfigForm');

    if ($form.length === 0) {
        console.error('Form #systemConfigForm not found!');
        return;
    }

    $form.on('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        try {
            await Ajax.post($form.attr('action'), formData, {
                loadingMessage: 'Saving system settings...',
                successMessage: 'System settings saved successfully',
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
}
