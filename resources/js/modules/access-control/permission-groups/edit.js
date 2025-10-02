import $ from 'jquery';
import Ajax from '../../../utils/ajax.js';

$(document).ready(function() {
    const $form = $('#editPermissionGroupForm');
    const groupId = $form.data('id');

    $form.on('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const requestData = {
            name: formData.get('name'),
            description: formData.get('description')
        };

        try {
            await Ajax.put(`/access-control/permission-group/${groupId}`, requestData, {
                loadingMessage: 'Updating permission group...',
                successMessage: 'Permission group updated successfully',
                onSuccess: () => {
                    setTimeout(() => {
                        window.location.href = '/access-control/permission-group';
                    }, 1000);
                }
            });
        } catch (error) {
            // Error already handled by Ajax helper
        }
    });
});
