import Toast from "../../components/toast";

$(document).ready(function() {
    const $form = $('#createModuleForm');

    $form.on('submit', async function(e) {
        e.preventDefault();

        const $submitBtn = $form.find('button[type="submit"]');
        const originalBtnText = $submitBtn.html();

        const formData = new FormData(this);
        const requestData = {
            name: formData.get('name'),
            display_name: formData.get('display_name'),
            description: formData.get('description'),
            icon: formData.get('icon'),
            parent_id: formData.get('parent_id') || null,
            route: formData.get('route'),
            component: formData.get('component'),
            sort_order: formData.get('sort_order') || 0,
            is_active: $('input[name="is_active"]').is(':checked') ? 1 : 0,
            is_visible: $('input[name="is_visible"]').is(':checked') ? 1 : 0,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $submitBtn.prop('disabled', true).html('<span class="loading loading-spinner loading-sm"></span> Creating...');

        try {
            const response = await fetch('/access-control/modules/store', {
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
                    window.location.href = '/access-control/module';
                }, 1000);
            } else {
                if (result.errors) {
                    const errorMessages = Object.values(result.errors).flat().join('<br>');
                    Toast.showToast(errorMessages, 'error');
                } else {
                    Toast.showToast(result.message || 'Failed to create module', 'error');
                }
                $submitBtn.prop('disabled', false).html(originalBtnText);
            }
        } catch (error) {
            console.error('Error creating module:', error);
            Toast.showToast('An error occurred while creating module', 'error');
            $submitBtn.prop('disabled', false).html(originalBtnText);
        }
    });
});
