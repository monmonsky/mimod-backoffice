import $ from 'jquery';
import Ajax from '../../../utils/ajax.js';

$(document).ready(function() {
    // Add Group Button
    $('#addGroupBtn').on('click', function() {
        resetForm();
        document.getElementById('groupModal').showModal();
        $('#groupModal h3').text('Add Group');
    });

    // Edit Group
    $(document).on('click', '.edit-group-btn', async function(e) {
        e.preventDefault();
        const groupId = $(this).data('id');

        try {
            const response = await Ajax.get(`/api/customer-groups/${groupId}`, {
                loadingMessage: 'Loading group...'
            });

            if (response.success) {
                const group = response.data;

                // Fill form
                $('#group_id').val(group.id);
                $('input[name="name"]').val(group.name);
                $('input[name="code"]').val(group.code);
                $('input[name="color"]').val(group.color || '#000000');
                $('textarea[name="description"]').val(group.description);
                $('input[name="is_active"]').prop('checked', group.is_active);

                // Open modal
                document.getElementById('groupModal').showModal();
                $('#groupModal h3').text('Edit Group');
            }
        } catch (error) {
            console.error('Error loading group:', error);
        }
    });

    // Submit Form
    $('#groupForm').on('submit', async function(e) {
        e.preventDefault();

        const groupId = $('#group_id').val();
        const formData = {
            name: $('input[name="name"]').val(),
            code: $('input[name="code"]').val(),
            color: $('input[name="color"]').val(),
            description: $('textarea[name="description"]').val(),
            is_active: $('input[name="is_active"]').is(':checked'),
        };

        try {
            let response;
            if (groupId) {
                // Update
                response = await Ajax.put(`/api/customer-groups/${groupId}`, formData, {
                    loadingMessage: 'Updating group...',
                    successMessage: 'Group updated successfully'
                });
            } else {
                // Create
                response = await Ajax.post('/api/customer-groups', formData, {
                    loadingMessage: 'Creating group...',
                    successMessage: 'Group created successfully'
                });
            }

            if (response.success) {
                document.getElementById('groupModal').close();
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } catch (error) {
            console.error('Error saving group:', error);
        }
    });

    // Delete Group
    $(document).on('click', '.delete-group-btn', async function(e) {
        e.preventDefault();
        const groupId = $(this).data('id');

        if (!confirm('Are you sure you want to delete this group?')) {
            return;
        }

        try {
            const response = await Ajax.delete(`/api/customer-groups/${groupId}`, {
                loadingMessage: 'Deleting group...',
                successMessage: 'Group deleted successfully'
            });

            if (response.success) {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } catch (error) {
            console.error('Error deleting group:', error);
        }
    });

    function resetForm() {
        $('#groupForm')[0].reset();
        $('#group_id').val('');
        $('input[name="is_active"]').prop('checked', true);
    }
});
