import Ajax from '../../../utils/ajax.js';
import Toast from '../../../components/toast.js';

// jQuery is loaded from CDN in blade file
/* global $ */

// Initialize when DOM is ready
$(document).ready(function() {
    setupFormHandler();
    setupTestEmailHandler();
});

/**
 * Setup form submission handler
 */
function setupFormHandler() {
    const $form = $('#emailSettingsForm');

    if ($form.length === 0) {
        console.error('Form #emailSettingsForm not found!');
        return;
    }

    $form.on('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        try {
            await Ajax.post($form.attr('action'), formData, {
                loadingMessage: 'Saving email settings...',
                successMessage: 'Email settings saved successfully',
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

/**
 * Setup test email button handler
 */
function setupTestEmailHandler() {
    const $testEmailBtn = $('#testEmailBtn');

    if ($testEmailBtn.length === 0) {
        return;
    }

    $testEmailBtn.on('click', async function() {
        // Get email address for test
        const fromEmail = $('input[name="from_email"]').val();
        const testEmail = prompt('Enter email address to send test email:', fromEmail);

        if (!testEmail) {
            return; // User cancelled
        }

        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(testEmail)) {
            Toast.showToast('Please enter a valid email address', 'error');
            return;
        }

        try {
            await Ajax.post(window.testEmailUrl, { test_email: testEmail }, {
                loadingMessage: 'Sending test email...',
                successMessage: 'Test email sent successfully'
            });
        } catch (error) {
            // Error already handled by Ajax helper
        }
    });
}
