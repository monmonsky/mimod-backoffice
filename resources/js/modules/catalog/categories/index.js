import Ajax from '../../../utils/ajax.js';
import Toast from '../../../components/toast.js';
import $ from 'jquery';

let currentPage = 1;
let perPage = 15;
let currentFilters = {};
let isLoading = false;
let currentCategoryId = null;

// ============================================
// INITIALIZATION
// ============================================

$(document).ready(function() {
    loadFilterOptions();
    loadData();
    initEventListeners();
});

// ============================================
// LOAD DATA FROM API
// ============================================

async function loadFilterOptions() {
    try {
        const response = await Ajax.get('/api/catalog/categories/parents', {
            showLoading: false,
            showToast: false
        });

        if (response.status && response.data) {
            let parents = response.data;
            if (!Array.isArray(parents)) {
                parents = parents.data || [];
            }

            let parentOptions = '<option value="">All Categories</option><option value="0">Top Level Only</option>';
            parents.forEach(parent => {
                parentOptions += `<option value="${parent.id}">${parent.name}</option>`;
            });
            $('#parentFilter').html(parentOptions);
        }
    } catch (error) {
        console.error('Failed to load filter options:', error);
    }
}

async function loadData(page = 1) {
    if (isLoading) return;

    isLoading = true;

    try {
        // Show loading in table
        $('#categoriesTableBody').html(`
            <tr>
                <td colspan="5" class="text-center py-8">
                    <span class="loading loading-spinner loading-md"></span>
                    <p class="mt-2 text-base-content/60">Loading categories...</p>
                </td>
            </tr>
        `);

        const params = {
            page: page,
            per_page: perPage,
            ...currentFilters
        };

        // Remove empty values
        Object.keys(params).forEach(key => {
            if (!params[key] || params[key] === '') delete params[key];
        });

        // Build query string
        const queryString = new URLSearchParams(params).toString();

        const response = await Ajax.get(`/api/catalog/categories?${queryString}`, {
            showLoading: false,
            showToast: false
        });

        if (response.status && response.data) {
            renderTable(response.data.categories);
            renderPagination(response.data.categories);
            updateStatistics(response.data.statistics);
            currentPage = page;
        } else {
            throw new Error('Invalid response structure');
        }
    } catch (error) {
        console.error('Error loading categories:', error);
        $('#categoriesTableBody').html(`
            <tr>
                <td colspan="5" class="text-center py-8 text-error">
                    <span class="iconify lucide--alert-circle size-8 mb-2"></span>
                    <p>Failed to load categories. Please refresh the page.</p>
                    <p class="text-xs mt-2">${error.message || 'Unknown error'}</p>
                </td>
            </tr>
        `);
    } finally {
        isLoading = false;
    }
}

// Make loadData accessible for pagination
window.categoriesLoadPage = function(page) {
    loadData(page);
};

// ============================================
// RENDER FUNCTIONS
// ============================================

function renderTable(categories) {
    const tbody = $('#categoriesTableBody');
    const items = categories.data || categories;

    if (!items || items.length === 0) {
        tbody.html(`
            <tr>
                <td colspan="5" class="text-center py-8">
                    <span class="iconify lucide--inbox size-8 mb-2 text-base-content/40"></span>
                    <p class="text-base-content/60">No categories found</p>
                </td>
            </tr>
        `);
        return;
    }

    let html = '';
    items.forEach(category => {
        const imageHtml = category.image
            ? `<img src="/storage/${category.image}" alt="${category.name}" class="w-10 h-10 rounded object-cover">`
            : `<div class="w-10 h-10 rounded bg-base-200 flex items-center justify-center">
                <span class="iconify lucide--image size-5 text-base-content/40"></span>
               </div>`;

        const statusBadge = category.is_active
            ? '<span class="badge badge-success badge-sm">Active</span>'
            : '<span class="badge badge-error badge-sm">Inactive</span>';

        const parentName = category.parent ? category.parent.name : '<span class="text-base-content/40">Top Level</span>';

        html += `
            <tr class="hover">
                <td>
                    <div class="flex items-center gap-2">
                        ${imageHtml}
                        <div>
                            <p class="font-medium">${category.name}</p>
                            ${category.description ? `<p class="text-xs text-base-content/60 line-clamp-1">${category.description}</p>` : ''}
                        </div>
                    </div>
                </td>
                <td>${parentName}</td>
                <td>
                    <span class="badge badge-sm badge-ghost">${category.product_count || 0} products</span>
                </td>
                <td>${statusBadge}</td>
                <td class="text-right">
                    <div class="inline-flex gap-2">
                        ${window.hasPermission && window.hasPermission('catalog.products.categories.view') ? `
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0" onclick="viewCategory(${category.id})" title="View Details">
                            <span class="iconify lucide--eye size-5 text-info"></span>
                        </button>
                        ` : ''}
                        ${window.hasPermission && window.hasPermission('catalog.products.categories.update') ? `
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0" onclick="editCategory(${category.id})" title="Edit">
                            <span class="iconify lucide--pencil size-5 text-warning"></span>
                        </button>
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0 toggle-status-btn" data-id="${category.id}" data-active="${category.is_active ? 1 : 0}" title="${category.is_active ? 'Deactivate' : 'Activate'}">
                            <span class="iconify lucide--${category.is_active ? 'eye-off' : 'eye'} size-5 text-warning"></span>
                        </button>
                        ` : ''}
                        ${window.hasPermission && window.hasPermission('catalog.products.categories.delete') ? `
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0" onclick="deleteCategory(${category.id}, '${category.name}', ${category.product_count || 0})" title="Delete">
                            <span class="iconify lucide--trash-2 size-5 text-error"></span>
                        </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `;
    });

    tbody.html(html);
    initTableActions();
}

function renderPagination(categories) {
    const container = $('#paginationContainer');

    if (!categories.last_page || categories.last_page <= 1) {
        container.html('');
        return;
    }

    let html = '<div class="flex items-center justify-between">';

    // Info
    html += `<div class="text-sm text-base-content/60">
        Showing ${categories.from || 0} to ${categories.to || 0} of ${categories.total || 0} categories
    </div>`;

    // Pagination buttons
    html += '<div class="join">';

    // Previous
    html += `<button class="join-item btn btn-sm ${categories.current_page === 1 ? 'btn-disabled' : ''}"
             onclick="window.categoriesLoadPage(${categories.current_page - 1})"
             ${categories.current_page === 1 ? 'disabled' : ''}>
        <span class="iconify lucide--chevron-left"></span>
    </button>`;

    // Page numbers
    const maxPages = 5;
    let startPage = Math.max(1, categories.current_page - Math.floor(maxPages / 2));
    let endPage = Math.min(categories.last_page, startPage + maxPages - 1);

    if (endPage - startPage < maxPages - 1) {
        startPage = Math.max(1, endPage - maxPages + 1);
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `<button class="join-item btn btn-sm ${i === categories.current_page ? 'btn-active' : ''}"
                 onclick="window.categoriesLoadPage(${i})">${i}</button>`;
    }

    // Next
    html += `<button class="join-item btn btn-sm ${categories.current_page === categories.last_page ? 'btn-disabled' : ''}"
             onclick="window.categoriesLoadPage(${categories.current_page + 1})"
             ${categories.current_page === categories.last_page ? 'disabled' : ''}>
        <span class="iconify lucide--chevron-right"></span>
    </button>`;

    html += '</div></div>';
    container.html(html);
}

function updateStatistics(stats) {
    if (!stats) return;

    $('#statTotalCategories').text(stats.total || 0);
    $('#statActiveCategories').text(stats.active || 0);
    $('#statInactiveCategories').text(stats.inactive || 0);
    $('#statParentCategories').text(stats.parent_categories || 0);
    $('#statTotalProducts').text(stats.total_products || 0);
}

// ============================================
// EVENT LISTENERS
// ============================================

function initEventListeners() {
    // Filter form submit
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();

        currentFilters = {
            search: $('input[name="search"]').val(),
            parent_id: $('select[name="parent_id"]').val(),
            status: $('select[name="status"]').val()
        };

        // Remove empty filters
        Object.keys(currentFilters).forEach(key => {
            if (!currentFilters[key]) delete currentFilters[key];
        });

        loadData(1);
    });

    // Clear filters
    $('#clearFilters').on('click', function() {
        $('#filterForm')[0].reset();
        currentFilters = {};
        loadData(1);
    });

    // Add category button
    $('#addCategoryBtn').on('click', function() {
        openCreateModal();
    });

    // Generate slug from name
    $(document).on('input', '#categoryName', function() {
        const name = $(this).val();
        const slug = name.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
        $('#categorySlug').val(slug);
    });

    // Image preview
    $(document).on('change', '#categoryImage', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Show file name
            $('#fileName').text(`Selected: ${file.name}`).removeClass('hidden');

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview img').attr('src', e.target.result);
                $('#imagePreview').removeClass('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            $('#imagePreview').addClass('hidden');
            $('#fileName').addClass('hidden');
        }
    });

    // Submit category form
    $(document).on('submit', '#categoryForm', async function(e) {
        e.preventDefault();
        await saveCategory(this);
    });
}

function initTableActions() {
    // Toggle status
    $(document).off('click', '.toggle-status-btn').on('click', '.toggle-status-btn', async function() {
        const categoryId = $(this).data('id');
        const currentStatus = $(this).data('active');
        const newStatus = !currentStatus;

        try {
            const response = await Ajax.patch(`/api/catalog/categories/${categoryId}/status`, {
                is_active: newStatus
            }, {
                showToast: true
            });

            if (response.status) {
                loadData(currentPage);
            }
        } catch (error) {
            console.error('Failed to toggle category status:', error);
        }
    });
}

// ============================================
// CRUD OPERATIONS
// ============================================

window.openCreateModal = function() {
    currentCategoryId = null;
    resetCategoryForm();
    loadParentOptions();
    document.querySelector('#categoryModal h3').textContent = 'Create Category';
    $('#formMethod').val('POST');
    $('#categoryId').val('');
    document.getElementById('categoryModal').showModal();
};

window.viewCategory = async function(id) {
    try {
        const response = await Ajax.get(`/api/catalog/categories/${id}`, {
            showLoading: true,
            showToast: false
        });

        if (response.status && response.data) {
            showCategoryDetailModal(response.data);
        }
    } catch (error) {
        console.error('Failed to load category details:', error);
        Toast.showToast('Failed to load category details', 'error');
    }
};

window.editCategory = async function(id) {
    try {
        const response = await Ajax.get(`/api/catalog/categories/${id}`, {
            showLoading: true,
            showToast: false
        });

        if (response.status && response.data) {
            currentCategoryId = id;
            const category = response.data;

            await loadParentOptions(category.parent_id);

            $('#categoryId').val(category.id);
            $('#categoryName').val(category.name);
            $('#categorySlug').val(category.slug);
            $('#categoryDescription').val(category.description || '');
            $('#categoryParent').val(category.parent_id || '');
            $('#categoryActive').prop('checked', category.is_active);

            if (category.image) {
                $('#imagePreview img').attr('src', `/storage/${category.image}`);
                $('#imagePreview').removeClass('hidden');
            }

            document.querySelector('#categoryModal h3').textContent = 'Edit Category';
            $('#formMethod').val('PUT');
            document.getElementById('categoryModal').showModal();
        }
    } catch (error) {
        console.error('Failed to load category:', error);
        Toast.showToast('Failed to load category', 'error');
    }
};

window.deleteCategory = async function(id, name, productCount) {
    if (productCount > 0) {
        alert(`Cannot delete "${name}"\n\nThis category has ${productCount} product(s). You must remove all products before deleting.`);
        return;
    }

    if (!confirm(`Are you sure you want to delete "${name}"?\n\nThis action cannot be undone.`)) {
        return;
    }

    try {
        const response = await Ajax.delete(`/api/catalog/categories/${id}`, {
            showToast: true
        });

        if (response.status) {
            loadData(currentPage);
            loadFilterOptions();
        }
    } catch (error) {
        console.error('Failed to delete category:', error);
    }
};

async function loadParentOptions(currentParentId = null) {
    try {
        const response = await Ajax.get('/api/catalog/categories/parents', {
            showLoading: false,
            showToast: false
        });

        if (response.status && response.data) {
            let parents = response.data;
            if (!Array.isArray(parents)) {
                parents = parents.data || [];
            }

            let parentOptions = '<option value="">No Parent (Top Level)</option>';
            parents.forEach(parent => {
                // Don't show current category as parent option when editing
                if (currentCategoryId && parent.id === currentCategoryId) return;

                const selected = currentParentId && parent.id === currentParentId ? 'selected' : '';
                parentOptions += `<option value="${parent.id}" ${selected}>${parent.name}</option>`;
            });
            $('#categoryParent').html(parentOptions);
        }
    } catch (error) {
        console.error('Failed to load parent options:', error);
    }
}

async function saveCategory(form) {
    const formMethod = $('#formMethod').val();
    const categoryId = $('#categoryId').val();
    const formData = new FormData(form);

    // Convert checkbox to boolean
    formData.set('is_active', $('#categoryActive').is(':checked') ? '1' : '0');

    // Remove unnecessary fields
    formData.delete('category_id');
    formData.delete('_token');

    const submitBtn = $('#submitBtn');
    const originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<span class="loading loading-spinner loading-xs"></span> Saving...');

    try {
        let response;
        if (formMethod === 'POST' && !categoryId) {
            // Create new category
            response = await Ajax.post('/api/catalog/categories', formData, {
                showToast: true
            });
        } else {
            // Update existing category
            response = await Ajax.post(`/api/catalog/categories/${categoryId}`, formData, {
                showToast: true
            });
        }

        if (response.status) {
            document.getElementById('categoryModal').close();
            loadData(currentPage);
            loadFilterOptions();
            resetCategoryForm();
            currentCategoryId = null;
        }
    } catch (error) {
        console.error('Failed to save category:', error);
    } finally {
        submitBtn.prop('disabled', false).html(originalText);
    }
}

// ============================================
// HELPER FUNCTIONS
// ============================================

function resetCategoryForm() {
    $('#categoryForm')[0].reset();
    $('#categorySlug').val('');
    $('#imagePreview').addClass('hidden');
    $('#fileName').addClass('hidden');
    $('#categoryActive').prop('checked', true);
}

function showCategoryDetailModal(category) {
    const imageHtml = category.image
        ? `<img src="/storage/${category.image}" alt="${category.name}" class="w-32 h-32 rounded object-cover">`
        : `<div class="w-32 h-32 rounded bg-base-200 flex items-center justify-center">
            <span class="iconify lucide--image size-16 text-base-content/40"></span>
           </div>`;

    const statusBadge = category.is_active
        ? '<span class="badge badge-success">Active</span>'
        : '<span class="badge badge-error">Inactive</span>';

    const parentName = category.parent ? category.parent.name : '<span class="text-base-content/40">Top Level</span>';

    const modalContent = `
        <div class="space-y-4">
            <div class="flex justify-center mb-4">
                ${imageHtml}
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="text-sm text-base-content/60">Category Name</div>
                    <div class="font-semibold text-lg">${category.name}</div>
                </div>
                <div>
                    <div class="text-sm text-base-content/60">Status</div>
                    <div class="mt-1">${statusBadge}</div>
                </div>
                <div>
                    <div class="text-sm text-base-content/60">Slug</div>
                    <code class="text-sm bg-base-200 px-2 py-1 rounded">${category.slug}</code>
                </div>
                <div>
                    <div class="text-sm text-base-content/60">Parent Category</div>
                    <div>${parentName}</div>
                </div>
                <div>
                    <div class="text-sm text-base-content/60">Total Products</div>
                    <div class="font-bold text-2xl">${category.product_count || 0}</div>
                </div>
                <div>
                    <div class="text-sm text-base-content/60">Created At</div>
                    <div class="text-sm">${category.created_at ? new Date(category.created_at).toLocaleDateString('id-ID') : '-'}</div>
                </div>
            </div>

            ${category.description ? `
            <div>
                <div class="text-sm text-base-content/60">Description</div>
                <div class="mt-1">${category.description}</div>
            </div>
            ` : ''}
        </div>
    `;

    document.getElementById('categoryDetails').innerHTML = modalContent;
    viewCategoryModal.showModal();
}
