import Toast from "../../components/toast.js";

$(document).ready(function() {
    const $form = $('#editRoleForm');
    const roleId = $form.data('id');

    // Select All button for permission groups
    $('.select-all-group').on('click', function() {
        const groupId = $(this).data('group-id');
        const $checkboxes = $(`.permission-checkbox[data-group-id="${groupId}"]`);
        const allChecked = $checkboxes.filter(':checked').length === $checkboxes.length;
        $checkboxes.prop('checked', !allChecked);
        $(this).text(allChecked ? 'Select All' : 'Deselect All');
    });

    // Select All button for specific module
    $('.select-all-module').on('click', function() {
        const module = $(this).data('module');
        const groupId = $(this).data('group-id');
        const $checkboxes = $(`.permission-checkbox[data-module="${module}"][data-group-id="${groupId}"]`);
        const allChecked = $checkboxes.filter(':checked').length === $checkboxes.length;
        $checkboxes.prop('checked', !allChecked);
        $(this).text(allChecked ? 'Select All' : 'Deselect All');
    });

    // Select All button for child section (e.g., Settings → Generals)
    $('.select-all-child').on('click', function() {
        const parent = $(this).data('parent');
        const child = $(this).data('child');
        const groupId = $(this).data('group-id');
        const $checkboxes = $(`.permission-checkbox[data-parent="${parent}"][data-child="${child}"][data-group-id="${groupId}"]`);
        const allChecked = $checkboxes.filter(':checked').length === $checkboxes.length;
        $checkboxes.prop('checked', !allChecked);
        $(this).text(allChecked ? 'Select All' : 'Deselect All');
    });

    $form.on('submit', async function(e) {
        e.preventDefault();

        const $submitBtn = $form.find('button[type="submit"]');
        const originalBtnText = $submitBtn.html();

        const formData = new FormData(this);

        // Collect permissions
        const permissions = [];
        $('input[name="permissions[]"]:checked').each(function() {
            permissions.push(parseInt($(this).val()));
        });

        const requestData = {
            name: formData.get('name'),
            display_name: formData.get('display_name'),
            description: formData.get('description'),
            priority: formData.get('priority'),
            is_active: $('input[name="is_active"]').is(':checked'),
            is_system: $('input[name="is_system"]').is(':checked'),
            permissions: permissions,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $submitBtn.prop('disabled', true).html('<span class="loading loading-spinner loading-sm"></span> Updating...');

        try {
            const response = await fetch(`/access-control/role/${roleId}`, {
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
                    window.location.href = '/access-control/role';
                }, 1000);
            } else {
                if (result.errors) {
                    const errorMessages = Object.values(result.errors).flat().join('<br>');
                    Toast.showToast(errorMessages, 'error');
                } else {
                    Toast.showToast(result.message || 'Failed to update role', 'error');
                }
                $submitBtn.prop('disabled', false).html(originalBtnText);
            }
        } catch (error) {
            console.error('Error updating role:', error);
            Toast.showToast('An error occurred while updating role', 'error');
            $submitBtn.prop('disabled', false).html(originalBtnText);
        }
    });
});
