import Toast from "../../components/toast";

$(document).ready(function() {
    const $form = $('#editPermissionForm');
    const permissionId = $form.data('id');

    $form.on('submit', async function(e) {
        e.preventDefault();

        const $submitBtn = $form.find('button[type="submit"]');
        const originalBtnText = $submitBtn.html();

        const formData = new FormData(this);
        const requestData = {
            name: formData.get('name'),
            display_name: formData.get('display_name'),
            description: formData.get('description'),
            permission_group_id: formData.get('permission_group_id') || null,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $submitBtn.prop('disabled', true).html('<span class="loading loading-spinner loading-sm"></span> Updating...');

        try {
            const response = await fetch(`/access-control/permission/${permissionId}`, {
                method: 'PUT',
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
                    window.location.href = '/access-control/permission';
                }, 1000);
            } else {
                if (result.errors) {
                    const errorMessages = Object.values(result.errors).flat().join('<br>');
                    Toast.showToast(errorMessages, 'error');
                } else {
                    Toast.showToast(result.message || 'Failed to update permission', 'error');
                }
                $submitBtn.prop('disabled', false).html(originalBtnText);
            }
        } catch (error) {
            console.error('Error updating permission:', error);
            Toast.showToast('An error occurred while updating permission', 'error');
            $submitBtn.prop('disabled', false).html(originalBtnText);
        }
    });
});
