import $ from 'jquery';
import Ajax from '../../../utils/ajax.js';

$(document).ready(function() {
    const $form = $('#editModuleForm');
    const moduleId = $form.data('module-id');

    $form.on('submit', async function(e) {
        e.preventDefault();

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
        };

        try {
            await Ajax.put(`/access-control/modules/${moduleId}`, requestData, {
                loadingMessage: 'Updating module...',
                successMessage: 'Module updated successfully',
                onSuccess: () => {
                    setTimeout(() => {
                        window.location.href = '/access-control/modules';
                    }, 1000);
                }
            });
        } catch (error) {
            // Error already handled by Ajax helper
        }
    });
});
