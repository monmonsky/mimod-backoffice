import Toast from "../../components/toast";

$(document).ready(function() {
    const $form = $('#createPermissionGroupForm');

    $form.on('submit', async function(e) {
        e.preventDefault();

        const $submitBtn = $form.find('button[type="submit"]');
        const originalBtnText = $submitBtn.html();

        const formData = new FormData(this);
        const requestData = {
            name: formData.get('name'),
            description: formData.get('description'),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $submitBtn.prop('disabled', true).html('<span class="loading loading-spinner loading-sm"></span> Creating...');

        try {
            const response = await fetch('/access-control/permission-group/store', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': requestData._token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(requestData)
            });

            const result = await response.json();

            if (response.ok && result.success) {
                Toast.showToast(result.message, 'success');
                setTimeout(() => {
                    window.location.href = '/access-control/permission-group';
                }, 1000);
            } else {
                if (result.errors) {
                    const errorMessages = Object.values(result.errors).flat().join('<br>');
                    Toast.showToast(errorMessages, 'error');
                } else {
                    Toast.showToast(result.message || 'Failed to create permission group', 'error');
                }
                $submitBtn.prop('disabled', false).html(originalBtnText);
            }
        } catch (error) {
            console.error('Error creating permission group:', error);
            Toast.showToast('An error occurred while creating permission group', 'error');
            $submitBtn.prop('disabled', false).html(originalBtnText);
        }
    });
});
