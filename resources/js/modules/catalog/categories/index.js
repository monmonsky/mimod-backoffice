import $ from 'jquery';
import Sortable from 'sortablejs';
import Toast from '../../../components/toast.js';
import Ajax from '../../../utils/ajax.js';

let sortableInstance = null;
let hasChanges = false;
let currentEditId = null;

$(document).ready(function() {
    initSortable();
    initSaveOrderButton();
    initAddButton();
    initEditButtons();
    initToggleButtons();
    initDeleteButtons();
    initSearchFilter();
    initFormHandlers();
});

// Initialize sortable drag & drop
function initSortable() {
    const tbody = document.getElementById('sortableCategories');
    if (!tbody) return;

    sortableInstance = new Sortable(tbody, {
        animation: 150,
        handle: '.drag-handle',
        draggable: '.sortable-category',
        ghostClass: 'bg-base-300',
        chosenClass: 'bg-primary/10',
        onEnd: function() {
            hasChanges = true;
            showSaveButton();
        }
    });
}

function showSaveButton() {
    $('#saveOrderContainer').removeClass('hidden');
}

function hideSaveButton() {
    $('#saveOrderContainer').addClass('hidden');
}

// Save order button
function initSaveOrderButton() {
    $('#saveOrderBtn').on('click', async function() {
        if (!hasChanges) return;

        const categories = $('.sortable-category');
        const order = [];

        categories.each(function(index) {
            order.push({
                id: $(this).data('category-id'),
                sort_order: (index + 1) * 10
            });
        });

        const originalText = $(this).html();
        $(this).prop('disabled', true).html('<span class="loading loading-spinner loading-sm"></span> Saving...');

        try {
            await Ajax.post('/catalog/products/categories/update-order', { order }, {
                loadingMessage: 'Updating category order...',
                successMessage: 'Category order updated successfully',
                onSuccess: () => {
                    hasChanges = false;
                    hideSaveButton();
                    setTimeout(() => window.location.reload(), 1000);
                }
            });
        } catch (error) {
            $(this).prop('disabled', false).html(originalText);
        }
    });
}

// Add category button
function initAddButton() {
    $('#addCategoryBtn').on('click', function() {
        resetForm();
        $('#modalTitle').text('Add Category');
        $('#formMethod').val('POST');
        currentEditId = null;
        categoryModal.showModal();
    });
}

// Edit buttons
function initEditButtons() {
    $(document).on('click', '.edit-category-btn', async function() {
        const categoryId = $(this).data('id');
        currentEditId = categoryId;

        // Get category data from table row
        const row = $(this).closest('tr');
        const name = row.find('td:nth-child(2) .font-medium').text().trim();
        const slug = row.find('code').text().trim();
        const isActive = row.find('.badge-success').length > 0;

        // Populate form
        $('#categoryId').val(categoryId);
        $('#categoryName').val(name);
        $('#categorySlug').val(slug);
        $('#categoryActive').prop('checked', isActive);
        $('#formMethod').val('PUT');
        $('#modalTitle').text('Edit Category');

        // TODO: Load full category data via AJAX if needed (description, image, parent)
        // For now, using data from table

        categoryModal.showModal();
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
                successMessage: 'Category status updated successfully',
                onSuccess: () => {
                    setTimeout(() => window.location.reload(), 1000);
                }
            });
        } catch (error) {
            // Error handled by Ajax helper
        }
    });
}

// Delete category
function initDeleteButtons() {
    $(document).on('submit', '.delete-form', async function(e) {
        e.preventDefault();

        const row = $(this).closest('tr');
        const categoryName = row.find('td:nth-child(2) .font-medium').text().trim();
        const productCount = parseInt(row.find('.badge-ghost').text().match(/\d+/)[0]);

        let confirmMessage = `Are you sure you want to delete "${categoryName}"?`;

        if (productCount > 0) {
            confirmMessage += `\n\nThis category has ${productCount} product(s). You must remove all products before deleting.`;
            alert(confirmMessage);
            return;
        }

        if (!confirm(confirmMessage + '\n\nThis action cannot be undone.')) {
            return;
        }

        const url = $(this).attr('action');

        try {
            await Ajax.delete(url, {
                loadingMessage: 'Deleting category...',
                successMessage: 'Category deleted successfully',
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
        $('#sortableCategories tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
}

// Form handlers
function initFormHandlers() {
    // Auto-generate slug from name
    $('#categoryName').on('input', function() {
        if (!currentEditId) { // Only auto-generate for new categories
            const slug = generateSlug($(this).val());
            $('#categorySlug').val(slug);
        }
    });

    // Image preview
    $('#categoryImage').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview img').attr('src', e.target.result);
                $('#imagePreview').removeClass('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            $('#imagePreview').addClass('hidden');
        }
    });

    // Form submission
    $('#categoryForm').on('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const method = $('#formMethod').val();

        // Convert checkbox to boolean
        formData.set('is_active', $('#categoryActive').is(':checked') ? 1 : 0);

        // Remove category_id from FormData (it's in URL)
        formData.delete('category_id');

        let url = '/catalog/products/categories/store';

        if (method === 'PUT' && currentEditId) {
            url = `/catalog/products/categories/${currentEditId}`;
            formData.append('_method', 'PUT');
        }

        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="loading loading-spinner loading-sm"></span> Saving...');

        try {
            await Ajax.post(url, formData, {
                loadingMessage: method === 'PUT' ? 'Updating category...' : 'Creating category...',
                successMessage: method === 'PUT' ? 'Category updated successfully' : 'Category created successfully',
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                onSuccess: () => {
                    categoryModal.close();
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
    $('#categoryForm')[0].reset();
    $('#categoryId').val('');
    $('#categoryActive').prop('checked', true);
    $('#imagePreview').addClass('hidden');
    $('#imagePreview img').attr('src', '');
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
