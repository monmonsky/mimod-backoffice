import $ from 'jquery';
import Ajax from '../../../utils/ajax.js';

$(document).ready(function () {
    const $form = $('#editUserForm');
    const userId = $form.data('user-id');

    $form.on('submit', async function (e) {
        e.preventDefault();

        // Clear previous errors
        $('.label-text-alt.text-error').addClass('hidden').text('');
        $('input, select').removeClass('input-error select-error');

        // Get form data
        const formData = new FormData(this);

        // Convert checkbox to boolean value if exists
        const isActiveCheckbox = $('input[name="is_active"]');
        if (isActiveCheckbox.length) {
            formData.delete('is_active');
            formData.append('is_active', isActiveCheckbox.is(':checked') ? '1' : '0');
        }

        // Add PUT method override for Laravel
        formData.append('_method', 'PUT');

        try {
            await Ajax.post(`/user/${userId}`, formData, {
                loadingMessage: 'Updating user...',
                successMessage: 'User updated successfully',
                onSuccess: () => {
                    // Redirect after short delay
                    setTimeout(() => {
                        window.location.href = '/user';
                    }, 1000);
                },
                onError: (xhr) => {
                    // Handle validation errors
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        Object.keys(xhr.responseJSON.errors).forEach(key => {
                            const errorMessage = xhr.responseJSON.errors[key][0];

                            // Show error below field
                            $(`#error-${key}`).removeClass('hidden').text(errorMessage);
                            $(`[name="${key}"]`).addClass('input-error');
                        });
                    }
                }
            });
        } catch (error) {
            // Error already handled by Ajax helper
        }
    });
});
