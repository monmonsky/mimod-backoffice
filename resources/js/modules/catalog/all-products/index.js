import $ from 'jquery';
import Toast from '../../../components/toast.js';
import Ajax from '../../../utils/ajax.js';

$(document).ready(function() {
    initSearchFilter();
    initStatusFilter();
    initToggleFeatured();
    initChangeStatus();
    initDeleteButtons();
});

// Search filter
function initSearchFilter() {
    $('#searchInput').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('#productsTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
}

// Status filter
function initStatusFilter() {
    $('#statusFilter').on('change', function() {
        const value = $(this).val().toLowerCase();

        if (value === '') {
            $('#productsTable tbody tr').show();
        } else {
            $('#productsTable tbody tr').filter(function() {
                const status = $(this).data('status');
                $(this).toggle(status === value);
            });
        }
    });
}

// Toggle featured
function initToggleFeatured() {
    $(document).on('click', '.toggle-featured-btn', async function(e) {
        e.preventDefault();

        const productId = $(this).data('id');
        const url = `/catalog/products/${productId}/toggle-featured`;
        const starIcon = $(this).find('.iconify');

        try {
            await Ajax.post(url, null, {
                loadingMessage: 'Updating featured status...',
                successMessage: 'Featured status updated successfully',
                onSuccess: () => {
                    // Toggle star icon color
                    starIcon.toggleClass('text-warning');

                    // Update title
                    const isFeatured = starIcon.hasClass('text-warning');
                    $(this).attr('title', isFeatured ? 'Remove from featured' : 'Add to featured');
                }
            });
        } catch (error) {
            // Error handled by Ajax helper
        }
    });
}

// Change status
function initChangeStatus() {
    $(document).on('click', '.change-status-btn', async function(e) {
        e.preventDefault();

        const productId = $(this).data('id');
        const newStatus = $(this).data('status');
        const url = `/catalog/products/${productId}/toggle-status`;
        const row = $(`tr[data-status]`).filter(function() {
            return $(this).find(`[data-id="${productId}"]`).length > 0;
        });

        try {
            await Ajax.post(url, { status: newStatus }, {
                loadingMessage: 'Updating status...',
                successMessage: 'Product status updated successfully',
                onSuccess: () => {
                    setTimeout(() => window.location.reload(), 1000);
                }
            });
        } catch (error) {
            // Error handled by Ajax helper
        }
    });
}

// Delete product
function initDeleteButtons() {
    $(document).on('submit', '.delete-form', async function(e) {
        e.preventDefault();

        const row = $(this).closest('tr');
        const productName = row.find('td:first .font-medium').text().trim();
        const variantsCount = parseInt(row.find('.badge-info').text().match(/\d+/)[0] || 0);

        let confirmMessage = `Are you sure you want to delete "${productName}"?`;

        if (variantsCount > 0) {
            confirmMessage += `\n\nThis product has ${variantsCount} variant(s). You must delete all variants before deleting the product.`;
            alert(confirmMessage);
            return;
        }

        if (!confirm(confirmMessage + '\n\nThis action cannot be undone.')) {
            return;
        }

        const url = $(this).attr('action');

        try {
            await Ajax.delete(url, {
                loadingMessage: 'Deleting product...',
                successMessage: 'Product deleted successfully',
                onSuccess: () => {
                    setTimeout(() => window.location.reload(), 1000);
                }
            });
        } catch (error) {
            // Error handled by Ajax helper
        }
    });
}
