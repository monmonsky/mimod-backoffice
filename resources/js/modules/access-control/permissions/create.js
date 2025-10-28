import $ from 'jquery';
import Ajax from '../../../utils/ajax.js';

$(document).ready(function() {
    const $form = $('#createPermissionForm');

    $form.on('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const requestData = {
            name: formData.get('name'),
            display_name: formData.get('display_name'),
            description: formData.get('description'),
            permission_group_id: formData.get('permission_group_id') || null
        };

        try {
            await Ajax.create('/access-control/permission/store', requestData, {
                loadingMessage: 'Creating permission...',
                successMessage: 'Permission created successfully',
                onSuccess: () => {
                    setTimeout(() => {
                        window.location.href = '/access-control/permission';
                    }, 1000);
                }
            });
        } catch (error) {
            // Error already handled by Ajax helper
        }
    });
});
