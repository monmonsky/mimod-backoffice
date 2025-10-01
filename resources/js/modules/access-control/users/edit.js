import $ from 'jquery';
import Swal from 'sweetalert2';

$(document).ready(function () {
    const $form = $('#editUserForm');
    const $submitBtn = $('#submitBtn');
    const userId = $form.data('user-id');

    $form.on('submit', async function (e) {
        e.preventDefault();

        // Clear previous errors
        $('.label-text-alt.text-error').addClass('hidden').text('');
        $('input, select').removeClass('input-error select-error');

        // Get form data
        const formData = new FormData(this);

        // Disable submit button
        $submitBtn.prop('disabled', true).html('<span class="loading loading-spinner"></span> Updating...');

        try {
            const response = await fetch(`/user/${userId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json',
                    'X-HTTP-Method-Override': 'PUT'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok && data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message || 'User updated successfully',
                    timer: 2000,
                    showConfirmButton: false
                });

                window.location.href = '/user';
            } else {
                // Handle validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(key => {
                        const errorMessage = data.errors[key][0];
                        $(`#error-${key}`).removeClass('hidden').text(errorMessage);
                        $(`[name="${key}"]`).addClass('input-error');
                    });
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: data.message || 'Please check the form for errors',
                });
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An unexpected error occurred. Please try again.',
            });
        } finally {
            // Re-enable submit button
            $submitBtn.prop('disabled', false).html('<span class="iconify lucide--save size-4"></span> Update User');
        }
    });
});
