import Ajax from '../../../utils/ajax.js';
import Toast from '../../../components/toast.js';
import $ from 'jquery';

let currentPage = 1;
let perPage = 15;
let currentFilters = {};
let isLoading = false;
let currentBrandId = null;

// ============================================
// INITIALIZATION
// ============================================

$(document).ready(function() {
    loadData();
    initEventListeners();
});

// ============================================
// LOAD DATA FROM API
// ============================================

async function loadData(page = 1) {
    if (isLoading) return;

    // isLoading = true;

    try {
        // Show loading in table
        $('#brandsTableBody').html(`
            <tr>
                <td colspan="5" class="text-center py-8">
                    <span class="loading loading-spinner loading-md"></span>
                    <p class="mt-2 text-base-content/60">Loading brands...</p>
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

        const response = await Ajax.get(`/api/catalog/brands?${queryString}`, {
            showLoading: false,
            showToast: false
        });

        if (response.status && response.data) {
            renderTable(response.data.brands);
            renderPagination(response.data.brands);
            updateStatistics(response.data.statistics);
            currentPage = page;
        } else {
            throw new Error('Invalid response structure');
        }
    } catch (error) {
        console.error('Error loading brands:', error);
        $('#brandsTableBody').html(`
            <tr>
                <td colspan="5" class="text-center py-8 text-error">
                    <span class="iconify lucide--alert-circle size-8 mb-2"></span>
                    <p>Failed to load brands. Please refresh the page.</p>
                    <p class="text-xs mt-2">${error.message || 'Unknown error'}</p>
                </td>
            </tr>
        `);
    } finally {
        isLoading = false;
    }
}

// Make loadData accessible for pagination
window.brandsLoadPage = function(page) {
    loadData(page);
};

// ============================================
// RENDER FUNCTIONS
// ============================================

function renderTable(brands) {
    const tbody = $('#brandsTableBody');
    const items = brands.data || brands;

    if (!items || items.length === 0) {
        tbody.html(`
            <tr>
                <td colspan="5" class="text-center py-8">
                    <span class="iconify lucide--inbox size-8 mb-2 text-base-content/40"></span>
                    <p class="text-base-content/60">No brands found</p>
                </td>
            </tr>
        `);
        return;
    }

    let html = '';
    items.forEach(brand => {
        const logoHtml = brand.logo
            ? `<img src="/storage/${brand.logo}" alt="${brand.name}" class="w-10 h-10 rounded object-cover">`
            : `<div class="w-10 h-10 rounded bg-base-200 flex items-center justify-center">
                <span class="iconify lucide--image size-5 text-base-content/40"></span>
               </div>`;

        const statusBadge = brand.is_active
            ? '<span class="badge badge-success badge-sm">Active</span>'
            : '<span class="badge badge-error badge-sm">Inactive</span>';

        html += `
            <tr class="hover">
                <td>
                    <div class="flex items-center gap-2">
                        ${logoHtml}
                        <div>
                            <p class="font-medium">${brand.name}</p>
                            ${brand.description ? `<p class="text-xs text-base-content/60 line-clamp-1">${brand.description}</p>` : ''}
                        </div>
                    </div>
                </td>
                <td>
                    <code class="text-xs bg-base-200 px-2 py-1 rounded">${brand.slug}</code>
                </td>
                <td>
                    <span class="badge badge-sm badge-ghost">${brand.product_count || 0} products</span>
                </td>
                <td>${statusBadge}</td>
                <td class="text-right">
                    <div class="inline-flex gap-2">
                        ${window.hasPermission && window.hasPermission('catalog.products.brands.view') ? `
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0" onclick="viewBrand(${brand.id})" title="View Details">
                            <span class="iconify lucide--eye size-5 text-info"></span>
                        </button>
                        ` : ''}
                        ${window.hasPermission && window.hasPermission('catalog.products.brands.update') ? `
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0" onclick="editBrand(${brand.id})" title="Edit">
                            <span class="iconify lucide--pencil size-5 text-warning"></span>
                        </button>
                        ` : ''}
                        ${window.hasPermission && window.hasPermission('catalog.products.brands.delete') ? `
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0" onclick="deleteBrand(${brand.id}, '${brand.name}', ${brand.product_count || 0})" title="Delete">
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

function renderPagination(brands) {
    const container = $('#paginationContainer');

    if (!brands.last_page || brands.last_page <= 1) {
        container.html('');
        return;
    }

    let html = '<div class="flex items-center justify-between">';

    // Info
    html += `<div class="text-sm text-base-content/60">
        Showing ${brands.from || 0} to ${brands.to || 0} of ${brands.total || 0} brands
    </div>`;

    // Pagination buttons
    html += '<div class="join">';

    // Previous
    html += `<button class="join-item btn btn-sm ${brands.current_page === 1 ? 'btn-disabled' : ''}"
             onclick="window.brandsLoadPage(${brands.current_page - 1})"
             ${brands.current_page === 1 ? 'disabled' : ''}>
        <span class="iconify lucide--chevron-left"></span>
    </button>`;

    // Page numbers
    const maxPages = 5;
    let startPage = Math.max(1, brands.current_page - Math.floor(maxPages / 2));
    let endPage = Math.min(brands.last_page, startPage + maxPages - 1);

    if (endPage - startPage < maxPages - 1) {
        startPage = Math.max(1, endPage - maxPages + 1);
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `<button class="join-item btn btn-sm ${i === brands.current_page ? 'btn-active' : ''}"
                 onclick="window.brandsLoadPage(${i})">${i}</button>`;
    }

    // Next
    html += `<button class="join-item btn btn-sm ${brands.current_page === brands.last_page ? 'btn-disabled' : ''}"
             onclick="window.brandsLoadPage(${brands.current_page + 1})"
             ${brands.current_page === brands.last_page ? 'disabled' : ''}>
        <span class="iconify lucide--chevron-right"></span>
    </button>`;

    html += '</div></div>';
    container.html(html);
}

function updateStatistics(stats) {
    if (!stats) return;

    $('#statTotalBrands').text(stats.total || 0);
    $('#statActiveBrands').text(stats.active || 0);
    $('#statInactiveBrands').text(stats.inactive || 0);
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

    // Add brand button
    $('#addBrandBtn').on('click', function() {
        openCreateModal();
    });

    // Generate slug from name
    $(document).on('input', '#brandName', function() {
        const name = $(this).val();
        const slug = name.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
        $('#brandSlug').val(slug);
    });

    // Logo preview
    $(document).on('change', '#brandLogo', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Show file name
            $('#fileName').text(`Selected: ${file.name}`).removeClass('hidden');

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#logoPreview img').attr('src', e.target.result);
                $('#logoPreview').removeClass('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            $('#logoPreview').addClass('hidden');
            $('#fileName').addClass('hidden');
        }
    });

    // Submit brand form
    $(document).on('submit', '#brandForm', async function(e) {
        e.preventDefault();
        await saveBrand(this);
    });
}

function initTableActions() {
    // Toggle status
    $(document).off('click', '.toggle-status-btn').on('click', '.toggle-status-btn', async function() {
        const brandId = $(this).data('id');
        const currentStatus = $(this).data('active');
        const newStatus = !currentStatus;

        try {
            const response = await Ajax.patch(`/api/catalog/brands/${brandId}/status`, {
                is_active: newStatus
            }, {
                showToast: true
            });

            if (response.status) {
                loadData(currentPage);
            }
        } catch (error) {
            console.error('Failed to toggle brand status:', error);
        }
    });
}

// ============================================
// CRUD OPERATIONS
// ============================================

window.openCreateModal = function() {
    currentBrandId = null;
    resetBrandForm();
    document.querySelector('#brandModal h3').textContent = 'Create Brand';
    $('#formMethod').val('POST');
    $('#brandId').val('');
    document.getElementById('brandModal').showModal();
};

window.viewBrand = async function(id) {
    try {
        const response = await Ajax.get(`/api/catalog/brands/${id}`, {
            showLoading: true,
            showToast: false
        });

        if (response.status && response.data) {
            showBrandDetailModal(response.data);
        }
    } catch (error) {
        console.error('Failed to load brand details:', error);
        Toast.showToast('Failed to load brand details', 'error');
    }
};

window.editBrand = async function(id) {
    try {
        const response = await Ajax.get(`/api/catalog/brands/${id}`, {
            showLoading: true,
            showToast: false
        });

        if (response.status && response.data) {
            currentBrandId = id;
            const brand = response.data;

            $('#brandId').val(brand.id);
            $('#brandName').val(brand.name);
            $('#brandSlug').val(brand.slug);
            $('#brandDescription').val(brand.description || '');
            $('#brandActive').prop('checked', brand.is_active);

            if (brand.logo) {
                $('#logoPreview img').attr('src', `/storage/${brand.logo}`);
                $('#logoPreview').removeClass('hidden');
            }

            document.querySelector('#brandModal h3').textContent = 'Edit Brand';
            $('#formMethod').val('PUT');
            document.getElementById('brandModal').showModal();
        }
    } catch (error) {
        console.error('Failed to load brand:', error);
        Toast.showToast('Failed to load brand', 'error');
    }
};

window.deleteBrand = async function(id, name, productCount) {
    if (productCount > 0) {
        alert(`Cannot delete "${name}"\n\nThis brand has ${productCount} product(s). You must remove all products before deleting.`);
        return;
    }

    if (!confirm(`Are you sure you want to delete "${name}"?\n\nThis action cannot be undone.`)) {
        return;
    }

    try {
        const response = await Ajax.delete(`/api/catalog/brands/${id}`, {
            showToast: true
        });

        if (response.status) {
            loadData(currentPage);
        }
    } catch (error) {
        console.error('Failed to delete brand:', error);
    }
};

async function saveBrand(form) {
    const formMethod = $('#formMethod').val();
    const brandId = $('#brandId').val();
    const formData = new FormData(form);

    // Convert checkbox to boolean
    formData.set('is_active', $('#brandActive').is(':checked') ? '1' : '0');

    // Remove unnecessary fields
    formData.delete('brand_id');
    formData.delete('_token');

    const submitBtn = $('#submitBtn');
    const originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<span class="loading loading-spinner loading-xs"></span> Saving...');

    try {
        let response;
        if (formMethod === 'POST' && !brandId) {
            // Create new brand
            response = await Ajax.post('/api/catalog/brands', formData, {
                showToast: true
            });
        } else {
            // Update existing brand
            response = await Ajax.post(`/api/catalog/brands/${brandId}`, formData, {
                showToast: true
            });
        }

        if (response.status) {
            document.getElementById('brandModal').close();
            loadData(currentPage);
            resetBrandForm();
            currentBrandId = null;
        }
    } catch (error) {
        console.error('Failed to save brand:', error);
    } finally {
        submitBtn.prop('disabled', false).html(originalText);
    }
}

// ============================================
// HELPER FUNCTIONS
// ============================================

function resetBrandForm() {
    $('#brandForm')[0].reset();
    $('#brandSlug').val('');
    $('#logoPreview').addClass('hidden');
    $('#fileName').addClass('hidden');
    $('#brandActive').prop('checked', true);
}

function showBrandDetailModal(brand) {
    const logoHtml = brand.logo
        ? `<img src="/storage/${brand.logo}" alt="${brand.name}" class="w-32 h-32 rounded object-cover">`
        : `<div class="w-32 h-32 rounded bg-base-200 flex items-center justify-center">
            <span class="iconify lucide--image size-16 text-base-content/40"></span>
           </div>`;

    const statusBadge = brand.is_active
        ? '<span class="badge badge-success">Active</span>'
        : '<span class="badge badge-error">Inactive</span>';

    const modalContent = `
        <div class="space-y-4">
            <div class="flex justify-center mb-4">
                ${logoHtml}
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="text-sm text-base-content/60">Brand Name</div>
                    <div class="font-semibold text-lg">${brand.name}</div>
                </div>
                <div>
                    <div class="text-sm text-base-content/60">Status</div>
                    <div class="mt-1">${statusBadge}</div>
                </div>
                <div>
                    <div class="text-sm text-base-content/60">Slug</div>
                    <code class="text-sm bg-base-200 px-2 py-1 rounded">${brand.slug}</code>
                </div>
                <div>
                    <div class="text-sm text-base-content/60">Total Products</div>
                    <div class="font-bold text-2xl">${brand.product_count || 0}</div>
                </div>
            </div>

            ${brand.description ? `
            <div>
                <div class="text-sm text-base-content/60">Description</div>
                <div class="mt-1">${brand.description}</div>
            </div>
            ` : ''}

            <div>
                <div class="text-sm text-base-content/60">Created At</div>
                <div class="text-sm">${brand.created_at ? new Date(brand.created_at).toLocaleDateString('id-ID') : '-'}</div>
            </div>
        </div>
    `;

    document.getElementById('brandDetails').innerHTML = modalContent;
    viewBrandModal.showModal();
}
