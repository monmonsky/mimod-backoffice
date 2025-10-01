import Toast from "../../../components/toast";

// jQuery is loaded from CDN in blade file
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
        const $submitBtn = $form.find('button[type="submit"]');
        const originalBtnText = $submitBtn.html();

        // Show loading state
        $submitBtn.prop('disabled', true).html('<span class="loading loading-spinner loading-sm"></span> Saving...');

        try {
            const response = await fetch($form.attr('action'), {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok && data.success) {
                Toast.showToast(data.message || 'Email settings saved successfully!', 'success');
            } else {
                if (data.errors) {
                    const errorMessages = Object.values(data.errors).flat().join(', ');
                    Toast.showToast(errorMessages, 'error');
                } else {
                    Toast.showToast(data.message || 'Failed to save settings', 'error');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            Toast.showToast('An error occurred while saving settings', 'error');
        } finally {
            $submitBtn.prop('disabled', false).html(originalBtnText);
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

        const originalBtnText = $testEmailBtn.html();

        // Show loading state
        $testEmailBtn.prop('disabled', true).html('<span class="loading loading-spinner loading-sm"></span> Sending...');

        try {
            const response = await fetch(window.testEmailUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ test_email: testEmail })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                Toast.showToast(data.message || 'Test email sent successfully!', 'success');
            } else {
                Toast.showToast(data.message || 'Failed to send test email', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            Toast.showToast('An error occurred while sending test email', 'error');
        } finally {
            $testEmailBtn.prop('disabled', false).html(originalBtnText);
        }
    });
}
