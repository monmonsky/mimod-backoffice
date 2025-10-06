import $ from 'jquery';
import Ajax from '../../../utils/ajax.js';

$(document).ready(function() {
    // Add Segment Button
    $('#addSegmentBtn').on('click', function() {
        resetForm();
        $('#segmentModal dialog').prop('open', true);
        $('#segmentModal h3').text('Add Segment');
    });

    // Edit Segment
    $(document).on('click', '.edit-segment-btn', async function(e) {
        e.preventDefault();
        const segmentId = $(this).data('id');

        try {
            const response = await Ajax.get(`/api/customer-segments/${segmentId}`, {
                loadingMessage: 'Loading segment...'
            });

            if (response.success) {
                const segment = response.data;

                // Fill form
                $('#segment_id').val(segment.id);
                $('input[name="name"]').val(segment.name);
                $('input[name="code"]').val(segment.code);
                $('input[name="color"]').val(segment.color || '#000000');
                $('textarea[name="description"]').val(segment.description);
                $('input[name="min_orders"]').val(segment.min_orders);
                $('input[name="max_orders"]').val(segment.max_orders);
                $('input[name="min_spent"]').val(segment.min_spent);
                $('input[name="max_spent"]').val(segment.max_spent);
                $('input[name="min_loyalty_points"]').val(segment.min_loyalty_points);
                $('input[name="days_since_last_order"]').val(segment.days_since_last_order);
                $('input[name="is_auto_assign"]').prop('checked', segment.is_auto_assign);
                $('input[name="is_active"]').prop('checked', segment.is_active);

                // Open modal
                document.getElementById('segmentModal').showModal();
                $('#segmentModal h3').text('Edit Segment');
            }
        } catch (error) {
            console.error('Error loading segment:', error);
        }
    });

    // Submit Form
    $('#segmentForm').on('submit', async function(e) {
        e.preventDefault();

        const segmentId = $('#segment_id').val();
        const formData = {
            name: $('input[name="name"]').val(),
            code: $('input[name="code"]').val(),
            color: $('input[name="color"]').val(),
            description: $('textarea[name="description"]').val(),
            min_orders: $('input[name="min_orders"]').val() || null,
            max_orders: $('input[name="max_orders"]').val() || null,
            min_spent: $('input[name="min_spent"]').val() || null,
            max_spent: $('input[name="max_spent"]').val() || null,
            min_loyalty_points: $('input[name="min_loyalty_points"]').val() || null,
            days_since_last_order: $('input[name="days_since_last_order"]').val() || null,
            is_auto_assign: $('input[name="is_auto_assign"]').is(':checked'),
            is_active: $('input[name="is_active"]').is(':checked'),
        };

        try {
            let response;
            if (segmentId) {
                // Update
                response = await Ajax.put(`/api/customer-segments/${segmentId}`, formData, {
                    loadingMessage: 'Updating segment...',
                    successMessage: 'Segment updated successfully'
                });
            } else {
                // Create
                response = await Ajax.post('/api/customer-segments', formData, {
                    loadingMessage: 'Creating segment...',
                    successMessage: 'Segment created successfully'
                });
            }

            if (response.success) {
                document.getElementById('segmentModal').close();
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } catch (error) {
            console.error('Error saving segment:', error);
        }
    });

    // Delete Segment
    $(document).on('click', '.delete-segment-btn', async function(e) {
        e.preventDefault();
        const segmentId = $(this).data('id');

        if (!confirm('Are you sure you want to delete this segment?')) {
            return;
        }

        try {
            const response = await Ajax.delete(`/api/customer-segments/${segmentId}`, {
                loadingMessage: 'Deleting segment...',
                successMessage: 'Segment deleted successfully'
            });

            if (response.success) {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } catch (error) {
            console.error('Error deleting segment:', error);
        }
    });

    function resetForm() {
        $('#segmentForm')[0].reset();
        $('#segment_id').val('');
        $('input[name="is_active"]').prop('checked', true);
    }
});
