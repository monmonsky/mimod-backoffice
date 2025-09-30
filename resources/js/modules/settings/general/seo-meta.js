import Toast from "../../../components/toast";

// jQuery is loaded from CDN in blade file
// Initialize when DOM is ready
$(document).ready(function() {
    setupFormHandler();
});

/**
 * Setup form submission handler
 */
function setupFormHandler() {
    const $form = $('#seoMetaForm');

    if ($form.length === 0) {
        console.error('Form #seoMetaForm not found!');
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
                Toast.showToast(data.message || 'SEO settings saved successfully!', 'success');
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
