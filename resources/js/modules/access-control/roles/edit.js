import $ from 'jquery';
import Ajax from '../../../utils/ajax.js';

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
            permissions: permissions
        };

        try {
            await Ajax.put(`/access-control/role/${roleId}`, requestData, {
                loadingMessage: 'Updating role...',
                successMessage: 'Role updated successfully',
                onSuccess: () => {
                    setTimeout(() => {
                        window.location.href = '/access-control/role';
                    }, 1000);
                },
                onError: (xhr) => {
                    // Error already handled by Ajax helper with toast
                }
            });
        } catch (error) {
            // Error already handled by Ajax helper
        }
    });
});
