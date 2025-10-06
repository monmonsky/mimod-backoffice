import $ from 'jquery';
import Toast from '../../../components/toast.js';
import Ajax from '../../../utils/ajax.js';

let currentEditId = null;

$(document).ready(function() {
    initAddButton();
    initEditButtons();
    initToggleButtons();
    initDeleteButtons();
    initSearchFilter();
    initFormHandlers();
});

// Add brand button
function initAddButton() {
    $('#addBrandBtn').on('click', function() {
        resetForm();
        $('#modalTitle').text('Add Brand');
        $('#formMethod').val('POST');
        currentEditId = null;
        brandModal.showModal();
    });
}

// Edit buttons
function initEditButtons() {
    $(document).on('click', '.edit-brand-btn', async function() {
        const brandId = $(this).data('id');
        currentEditId = brandId;

        // Get brand data from table row
        const row = $(this).closest('tr');
        const name = row.find('td:nth-child(1) .font-medium').text().trim();
        const slug = row.find('code').text().trim();
        const description = row.find('td:nth-child(1) .text-xs').text().trim();
        const isActive = row.find('.badge-success').length > 0;

        // Populate form
        $('#brandId').val(brandId);
        $('#brandName').val(name);
        $('#brandSlug').val(slug);
        $('#brandDescription').val(description);
        $('#brandActive').prop('checked', isActive);
        $('#formMethod').val('PUT');
        $('#modalTitle').text('Edit Brand');

        // TODO: Load full brand data via AJAX if needed (logo)
        // For now, using data from table

        brandModal.showModal();
    });
}

// Toggle active status
function initToggleButtons() {
    $(document).on('submit', '.toggle-form', async function(e) {
        e.preventDefault();

        const url = $(this).attr('action');

        try {
            await Ajax.post(url, null, {
                loadingMessage: 'Updating status...',
                successMessage: 'Brand status updated successfully',
                onSuccess: () => {
                    setTimeout(() => window.location.reload(), 1000);
                }
            });
        } catch (error) {
            // Error handled by Ajax helper
        }
    });
}

// Delete brand
function initDeleteButtons() {
    $(document).on('submit', '.delete-form', async function(e) {
        e.preventDefault();

        const row = $(this).closest('tr');
        const brandName = row.find('td:nth-child(1) .font-medium').text().trim();
        const productCount = parseInt(row.find('.badge-ghost').text().match(/\d+/)[0]);

        let confirmMessage = `Are you sure you want to delete "${brandName}"?`;

        if (productCount > 0) {
            confirmMessage += `\n\nThis brand has ${productCount} product(s). You must remove all products before deleting.`;
            alert(confirmMessage);
            return;
        }

        if (!confirm(confirmMessage + '\n\nThis action cannot be undone.')) {
            return;
        }

        const url = $(this).attr('action');

        try {
            await Ajax.delete(url, {
                loadingMessage: 'Deleting brand...',
                successMessage: 'Brand deleted successfully',
                onSuccess: () => {
                    setTimeout(() => window.location.reload(), 1000);
                }
            });
        } catch (error) {
            // Error handled by Ajax helper
        }
    });
}

// Search filter
function initSearchFilter() {
    $('#searchInput').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('#brandsTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
}

// Form handlers
function initFormHandlers() {
    // Auto-generate slug from name
    $('#brandName').on('input', function() {
        if (!currentEditId) { // Only auto-generate for new brands
            const slug = generateSlug($(this).val());
            $('#brandSlug').val(slug);
        }
    });

    // Logo preview
    $('#brandLogo').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#logoPreview img').attr('src', e.target.result);
                $('#logoPreview').removeClass('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            $('#logoPreview').addClass('hidden');
        }
    });

    // Form submission
    $('#brandForm').on('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const method = $('#formMethod').val();

        // Convert checkbox to boolean
        formData.set('is_active', $('#brandActive').is(':checked') ? 1 : 0);

        // Remove brand_id from FormData (it's in URL)
        formData.delete('brand_id');

        let url = '/catalog/products/brands/store';

        if (method === 'PUT' && currentEditId) {
            url = `/catalog/products/brands/${currentEditId}`;
            formData.append('_method', 'PUT');
        }

        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="loading loading-spinner loading-sm"></span> Saving...');

        try {
            await Ajax.post(url, formData, {
                loadingMessage: method === 'PUT' ? 'Updating brand...' : 'Creating brand...',
                successMessage: method === 'PUT' ? 'Brand updated successfully' : 'Brand created successfully',
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                onSuccess: () => {
                    brandModal.close();
                    setTimeout(() => window.location.reload(), 1000);
                }
            });
        } catch (error) {
            submitBtn.prop('disabled', false).html(originalText);
        }
    });
}

// Reset form
function resetForm() {
    $('#brandForm')[0].reset();
    $('#brandId').val('');
    $('#brandActive').prop('checked', true);
    $('#logoPreview').addClass('hidden');
    $('#logoPreview img').attr('src', '');
}

// Generate slug from text
function generateSlug(text) {
    return text
        .toString()
        .toLowerCase()
        .trim()
        .replace(/\s+/g, '-')        // Replace spaces with -
        .replace(/[^\w\-]+/g, '')    // Remove all non-word chars
        .replace(/\-\-+/g, '-')      // Replace multiple - with single -
        .replace(/^-+/, '')          // Trim - from start of text
        .replace(/-+$/, '');         // Trim - from end of text
}
