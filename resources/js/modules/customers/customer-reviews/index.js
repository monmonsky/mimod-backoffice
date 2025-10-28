import $ from 'jquery';
import Ajax from '../../../utils/ajax.js';

let currentReviewId = null;

$(document).ready(function() {
    // View Review
    $(document).on('click', '.view-review-btn', async function(e) {
        e.preventDefault();
        const reviewId = $(this).data('id');
        currentReviewId = reviewId;

        try {
            const response = await Ajax.get(`/api/product-reviews/${reviewId}`, {
                loadingMessage: 'Loading review...'
            });

            if (response.success) {
                displayReviewDetails(response.data);
                document.getElementById('viewReviewModal').showModal();
            }
        } catch (error) {
            console.error('Error loading review:', error);
        }
    });

    // Approve Review
    $(document).on('click', '.approve-review-btn', async function(e) {
        e.preventDefault();
        const reviewId = $(this).data('id');

        if (!confirm('Are you sure you want to approve this review?')) {
            return;
        }

        try {
            const response = await Ajax.post(`/api/product-reviews/${reviewId}/approve`, {}, {
                loadingMessage: 'Approving review...',
                successMessage: 'Review approved successfully'
            });

            if (response.success) {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } catch (error) {
            console.error('Error approving review:', error);
        }
    });

    // Delete Review
    $(document).on('click', '.delete-review-btn', async function(e) {
        e.preventDefault();
        const reviewId = $(this).data('id');

        if (!confirm('Are you sure you want to delete this review?')) {
            return;
        }

        try {
            const response = await Ajax.delete(`/api/product-reviews/${reviewId}`, {
                loadingMessage: 'Deleting review...',
                successMessage: 'Review deleted successfully'
            });

            if (response.success) {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } catch (error) {
            console.error('Error deleting review:', error);
        }
    });

    // Respond Button in View Modal
    $('#respondBtn').on('click', async function() {
        if (!currentReviewId) return;

        try {
            const response = await Ajax.get(`/api/product-reviews/${currentReviewId}`);

            if (response.success) {
                const review = response.data;

                // Fill review summary
                $('#reviewSummary').html(`
                    <div class="text-sm">
                        <div><strong>Product:</strong> ${review.product_name}</div>
                        <div><strong>Customer:</strong> ${review.customer_name}</div>
                        <div><strong>Rating:</strong> ${review.rating}/5</div>
                        <div class="mt-2"><strong>Review:</strong></div>
                        <div class="italic">${review.comment || 'No comment'}</div>
                    </div>
                `);

                $('#review_id').val(currentReviewId);
                $('textarea[name="admin_response"]').val(review.admin_response || '');

                document.getElementById('viewReviewModal').close();
                document.getElementById('respondModal').showModal();
            }
        } catch (error) {
            console.error('Error loading review for response:', error);
        }
    });

    // Submit Response
    $('#respondForm').on('submit', async function(e) {
        e.preventDefault();

        const reviewId = $('#review_id').val();
        const formData = {
            admin_response: $('textarea[name="admin_response"]').val(),
        };

        try {
            const response = await Ajax.post(`/api/product-reviews/${reviewId}/respond`, formData, {
                loadingMessage: 'Submitting response...',
                successMessage: 'Response submitted successfully'
            });

            if (response.success) {
                document.getElementById('respondModal').close();
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } catch (error) {
            console.error('Error submitting response:', error);
        }
    });

    function displayReviewDetails(review) {
        const stars = Array.from({length: 5}, (_, i) =>
            `<span class="iconify lucide--star size-4 ${i < review.rating ? 'text-warning' : 'text-base-300'}"></span>`
        ).join('');

        const html = `
            <div class="space-y-4">
                <!-- Product Info -->
                <div>
                    <h4 class="font-semibold mb-2">Product</h4>
                    <div class="p-3 bg-base-200 rounded">
                        <div class="font-medium">${review.product_name}</div>
                        <div class="text-sm text-base-content/60">SKU: ${review.product_sku}</div>
                    </div>
                </div>

                <!-- Customer Info -->
                <div>
                    <h4 class="font-semibold mb-2">Customer</h4>
                    <div class="p-3 bg-base-200 rounded">
                        <div class="font-medium">${review.customer_name}</div>
                        <div class="text-sm text-base-content/60">${review.customer_email}</div>
                        ${review.is_verified_purchase ? '<div class="badge badge-success badge-sm mt-1">Verified Purchase</div>' : ''}
                    </div>
                </div>

                <!-- Rating & Review -->
                <div>
                    <h4 class="font-semibold mb-2">Rating & Review</h4>
                    <div class="p-3 bg-base-200 rounded">
                        <div class="flex items-center gap-2 mb-2">
                            ${stars}
                            <span class="text-sm">(${review.rating}/5)</span>
                        </div>
                        ${review.title ? `<div class="font-medium mb-2">${review.title}</div>` : ''}
                        <div class="text-sm">${review.comment || 'No comment provided'}</div>
                        <div class="text-xs text-base-content/60 mt-2">
                            Posted on ${new Date(review.created_at).toLocaleDateString()}
                        </div>
                    </div>
                </div>

                <!-- Admin Response -->
                ${review.admin_response ? `
                <div>
                    <h4 class="font-semibold mb-2">Admin Response</h4>
                    <div class="p-3 bg-primary/10 rounded">
                        <div class="text-sm">${review.admin_response}</div>
                        <div class="text-xs text-base-content/60 mt-2">
                            Responded on ${new Date(review.responded_at).toLocaleDateString()}
                        </div>
                    </div>
                </div>
                ` : ''}

                <!-- Approval Status -->
                <div>
                    <h4 class="font-semibold mb-2">Status</h4>
                    <div class="flex gap-2">
                        <div class="badge ${review.is_approved ? 'badge-success' : 'badge-warning'}">
                            ${review.is_approved ? 'Approved' : 'Pending Approval'}
                        </div>
                        ${review.is_approved && review.approved_at ? `
                        <div class="text-xs text-base-content/60">
                            Approved on ${new Date(review.approved_at).toLocaleDateString()}
                            ${review.approved_by_name ? `by ${review.approved_by_name}` : ''}
                        </div>
                        ` : ''}
                    </div>
                </div>

                <!-- Helpful Count -->
                <div>
                    <h4 class="font-semibold mb-2">Feedback</h4>
                    <div class="flex gap-4 text-sm">
                        <div>
                            <span class="iconify lucide--thumbs-up size-4 inline"></span>
                            ${review.helpful_count || 0} helpful
                        </div>
                        <div>
                            <span class="iconify lucide--thumbs-down size-4 inline"></span>
                            ${review.not_helpful_count || 0} not helpful
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('#reviewDetails').html(html);
    }
});
