import Ajax from '../../../utils/ajax.js';
import Toast from '../../../components/toast.js';
import $ from 'jquery';

let currentProductId = null;

$(document).ready(function() {
    loadFilterOptions();
    loadData();
    initEventListeners();
});

async function loadFilterOptions() {
    try {
        // Load brands
        const brandsResponse = await Ajax.get('/api/catalog/brands', {
            showLoading: false,
            showToast: false
        });

        console.log('Brands response:', brandsResponse);

        if (brandsResponse.status && brandsResponse.data) {
            // Handle array or collection
            let brands = brandsResponse.data;
            if (!Array.isArray(brands)) {
                brands = brands.data || [];
            }

            let brandOptions = '<option value="">All Brands</option>';
            if (Array.isArray(brands) && brands.length > 0) {
                brands.forEach(brand => {
                    brandOptions += `<option value="${brand.id}">${brand.name}</option>`;
                });
            }
            $('select[name="brand_id"]').html(brandOptions);
            $('#brandFilter').html(brandOptions);
        }

        // Load categories
        const categoriesResponse = await Ajax.get('/api/catalog/categories', {
            showLoading: false,
            showToast: false
        });

        console.log('Categories response:', categoriesResponse);

        if (categoriesResponse.status && categoriesResponse.data) {
            // Handle array or collection
            let categories = categoriesResponse.data;
            if (!Array.isArray(categories)) {
                categories = categories.data || [];
            }

            let categoryOptions = '<option value="">All Categories</option>';
            if (Array.isArray(categories) && categories.length > 0) {
                categories.forEach(category => {
                    categoryOptions += `<option value="${category.id}">${category.name}</option>`;
                });
            }
            $('select[name="category_id"]').html(categoryOptions);
            $('#categoryFilter').html(categoryOptions);
        }
    } catch (error) {
        console.error('Error loading filter options:', error);
    }
}

function initEventListeners() {
    // Filter form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadData(1);
    });

    // Clear filters
    $('#clearFilters').on('click', function() {
        $('#filterForm')[0].reset();
        loadData(1);
    });

    // Quick search
    let searchTimeout;
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadData(1);
        }, 500);
    });

    // View product details
    $(document).on('click', '.view-product-btn', async function() {
        const productId = $(this).data('id');
        await viewProduct(productId);
    });

    // Update product status
    $(document).on('click', '.update-status-btn', async function() {
        const productId = $(this).data('id');
        const status = $(this).data('status');
        await updateProductStatus(productId, status);
    });

    // Delete product
    $(document).on('click', '.delete-product-btn', async function(e) {
        e.preventDefault();
        const productId = $(this).data('id');

        if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
            await deleteProduct(productId);
        }
    });
}

async function loadData(page = 1) {
    try {
        // Show loading state FIRST
        $('#productsTableBody').html(`
            <tr id="loadingRow">
                <td colspan="8" class="text-center py-8">
                    <span class="loading loading-spinner loading-md"></span>
                    <p class="mt-2 text-base-content/60">Loading products...</p>
                </td>
            </tr>
        `);

        // Get filter values
        const filters = {
            search: $('input[name="search"]').val() || $('#searchInput').val(),
            status: $('select[name="status"]').val(),
            brand_id: $('select[name="brand_id"]').val(),
            category_id: $('select[name="category_id"]').val(),
            stock_status: $('select[name="stock_status"]').val(),
            min_price: $('input[name="min_price"]').val(),
            max_price: $('input[name="max_price"]').val(),
            page: page
        };

        // Remove empty values
        Object.keys(filters).forEach(key => {
            if (!filters[key]) delete filters[key];
        });

        const queryString = $.param(filters);

        const response = await Ajax.get(`/api/catalog/products?${queryString}`, {
            showLoading: false,
            showToast: false
        });

        console.log('Products API Response:', response);

        if (response.status && response.data) {
            renderTable(response.data.products);
            renderPagination(response.data.products);

            if (response.data.statistics) {
                updateStatistics(response.data.statistics);
            }
        } else {
            $('#productsTableBody').html(`
                <tr>
                    <td colspan="8" class="text-center py-8">
                        <div class="flex flex-col items-center gap-2 text-base-content/60">
                            <span class="iconify lucide--badge-x size-12"></span>
                            <p>Failed to load products</p>
                        </div>
                    </td>
                </tr>
            `);
        }
    } catch (error) {
        console.error('Error loading products:', error);
        $('#productsTableBody').html(`
            <tr>
                <td colspan="8" class="text-center py-8">
                    <div class="flex flex-col items-center gap-2 text-error">
                        <span class="iconify lucide--alert-circle size-12"></span>
                        <p>Error loading products</p>
                    </div>
                </td>
            </tr>
        `);
    }
}

function renderTable(productsData) {
    const products = productsData.data || [];

    if (!products || products.length === 0) {
        $('#productsTableBody').html(`
            <tr>
                <td colspan="8" class="text-center py-8">
                    <div class="flex flex-col items-center gap-2 text-base-content/60">
                        <span class="iconify lucide--badge-x size-12"></span>
                        <p>No products found</p>
                    </div>
                </td>
            </tr>
        `);
        return;
    }

    const statusMap = {
        'active': 'success',
        'inactive': 'warning',
        'draft': 'ghost'
    };

    let html = '';
    products.forEach(product => {
        const badgeType = statusMap[product.status] || 'ghost';
        const brandName = product.brand ? product.brand.name : '-';

        // Calculate total stock from variants
        let totalStock = 0;
        if (product.variants && product.variants.length > 0) {
            totalStock = product.variants.reduce((sum, variant) => sum + (variant.stock_quantity || 0), 0);
        }

        // Get min price from variants
        let minPrice = 0;
        if (product.variants && product.variants.length > 0) {
            const prices = product.variants.map(v => parseFloat(v.price || 0)).filter(p => p > 0);
            minPrice = prices.length > 0 ? Math.min(...prices) : 0;
        }

        // Get SKU from first variant
        const sku = (product.variants && product.variants.length > 0 && product.variants[0].sku)
            ? product.variants[0].sku
            : '-';

        // Get primary image
        let imageUrl = '/images/placeholder-product.png';
        if (product.images && product.images.length > 0) {
            const primaryImage = product.images.find(img => img.is_primary) || product.images[0];
            imageUrl = `/storage/${primaryImage.url}`;
        }

        html += `
            <tr>
                <td>
                    <div class="avatar">
                        <div class="mask mask-squircle w-12 h-12">
                            <img src="${imageUrl}" alt="${product.name}" onerror="this.src='/images/placeholder-product.png'" />
                        </div>
                    </div>
                </td>
                <td>
                    <div class="font-medium max-w-xs truncate">${product.name || '-'}</div>
                </td>
                <td>
                    <span class="text-sm opacity-60">${sku}</span>
                </td>
                <td>
                    <span class="text-sm">${brandName}</span>
                </td>
                <td>
                    ${totalStock <= 0
                        ? '<span class="badge badge-error badge-sm">Out of Stock</span>'
                        : totalStock <= 10
                            ? `<span class="badge badge-warning badge-sm">${totalStock}</span>`
                            : `<span class="badge badge-success badge-sm">${totalStock}</span>`
                    }
                </td>
                <td>
                    <span class="font-medium">Rp ${parseFloat(minPrice).toLocaleString('id-ID')}</span>
                </td>
                <td>
                    <span class="badge badge-${badgeType}">${product.status ? product.status.charAt(0).toUpperCase() + product.status.slice(1) : '-'}</span>
                </td>
                <td class="text-right">
                    <div class="inline-flex gap-2">
                        ${window.hasPermission && window.hasPermission('catalog.products.all-products.view') ? `
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0 view-product-btn" data-id="${product.id}" title="View Details">
                            <span class="iconify lucide--eye size-5 text-info"></span>
                        </button>
                        ` : ''}
                        ${window.hasPermission && window.hasPermission('catalog.products.all-products.update') ? `
                        <a href="/catalog/products/${product.id}/edit" class="btn btn-ghost btn-xs p-0 h-auto min-h-0" title="Edit">
                            <span class="iconify lucide--pencil size-5 text-warning"></span>
                        </a>
                        ` : ''}
                        ${window.hasPermission && window.hasPermission('catalog.products.all-products.update-status') && product.status === 'inactive' ? `
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0 update-status-btn" data-id="${product.id}" data-status="active" title="Activate">
                            <span class="iconify lucide--check size-5 text-success"></span>
                        </button>
                        ` : ''}
                        ${window.hasPermission && window.hasPermission('catalog.products.all-products.update-status') && product.status === 'active' ? `
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0 update-status-btn" data-id="${product.id}" data-status="inactive" title="Deactivate">
                            <span class="iconify lucide--x size-5 text-error"></span>
                        </button>
                        ` : ''}
                        ${window.hasPermission && window.hasPermission('catalog.products.all-products.delete') ? `
                        <button class="btn btn-ghost btn-xs p-0 h-auto min-h-0 delete-product-btn" data-id="${product.id}" title="Delete">
                            <span class="iconify lucide--trash-2 size-5 text-error"></span>
                        </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `;
    });

    $('#productsTableBody').html(html);
}

function renderPagination(productsData) {
    const currentPage = productsData.current_page;
    const lastPage = productsData.last_page;
    const from = productsData.from || 0;
    const to = productsData.to || 0;
    const total = productsData.total || 0;

    if (lastPage <= 1) {
        $('#paginationContainer').html('');
        return;
    }

    let html = '<div class="flex items-center justify-between">';

    // Info text
    html += `<div class="text-sm text-base-content/60">Showing ${from} to ${to} of ${total} products</div>`;

    // Pagination buttons
    html += '<div class="join">';

    // Previous button
    if (currentPage > 1) {
        html += `<button class="join-item btn btn-sm" onclick="window.loadDataPage(${currentPage - 1})">«</button>`;
    }

    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(lastPage, currentPage + 2);

    for (let i = startPage; i <= endPage; i++) {
        const activeClass = i === currentPage ? 'btn-active' : '';
        html += `<button class="join-item btn btn-sm ${activeClass}" onclick="window.loadDataPage(${i})">${i}</button>`;
    }

    // Next button
    if (currentPage < lastPage) {
        html += `<button class="join-item btn btn-sm" onclick="window.loadDataPage(${currentPage + 1})">»</button>`;
    }

    html += '</div></div>';

    $('#paginationContainer').html(html);
}

// Make loadDataPage available globally for onclick handlers
window.loadDataPage = function(page) {
    loadData(page);
};

function updateStatistics(stats) {
    $('#statTotalProducts').text(stats.total || 0);
    $('#statActiveProducts').text(stats.active || 0);
    $('#statInactiveProducts').text(stats.inactive || 0);
    $('#statOutOfStockProducts').text(stats.out_of_stock || 0);
}

async function viewProduct(productId) {
    try {
        currentProductId = productId;

        const response = await Ajax.get(`/api/catalog/products/${productId}`, {
            showLoading: true,
            showToast: false
        });

        if (response.status && response.data) {
            const product = response.data;
            renderProductDetail(product);
            document.getElementById('productDetailModal').showModal();
        }
    } catch (error) {
        console.error('Error loading product details:', error);
        Toast.error('Failed to load product details');
    }
}

function renderProductDetail(product) {
    const brandName = product.brand ? product.brand.name : '-';
    const categories = product.categories && product.categories.length > 0
        ? product.categories.map(cat => `<span class="badge badge-sm">${cat.name}</span>`).join(' ')
        : '<span class="text-sm opacity-60">No categories</span>';

    // Calculate total stock from variants
    let totalStock = 0;
    if (product.variants && product.variants.length > 0) {
        totalStock = product.variants.reduce((sum, variant) => sum + (variant.stock_quantity || 0), 0);
    }

    // Get min price from variants
    let minPrice = 0;
    if (product.variants && product.variants.length > 0) {
        const prices = product.variants.map(v => parseFloat(v.price || 0)).filter(p => p > 0);
        minPrice = prices.length > 0 ? Math.min(...prices) : 0;
    }

    // Get SKU from first variant
    const sku = (product.variants && product.variants.length > 0 && product.variants[0].sku)
        ? product.variants[0].sku
        : '-';

    const createdAt = new Date(product.created_at);
    const dateStr = createdAt.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

    let imagesHtml = '';
    if (product.images && product.images.length > 0) {
        imagesHtml = product.images.map(img => `
            <div class="avatar">
                <div class="w-24 rounded">
                    <img src="/storage/${img.url}" alt="Product image" />
                </div>
            </div>
        `).join('');
    } else {
        imagesHtml = '<span class="text-sm opacity-60">No images</span>';
    }

    const html = `
        <div class="grid grid-cols-1 gap-4">
            <div class="flex flex-col gap-2">
                <h4 class="font-semibold text-lg">${product.name}</h4>
                <p class="text-sm opacity-60">SKU: ${sku}</p>
            </div>

            <div class="divider my-0"></div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-base-content/60">Brand</p>
                    <p class="font-medium">${brandName}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-base-content/60">Status</p>
                    <span class="badge badge-${product.status === 'active' ? 'success' : 'warning'}">${product.status ? product.status.charAt(0).toUpperCase() + product.status.slice(1) : '-'}</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-base-content/60">Stock</p>
                    <p class="font-medium">${totalStock}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-base-content/60">Min Price</p>
                    <p class="font-medium">Rp ${parseFloat(minPrice).toLocaleString('id-ID')}</p>
                </div>
            </div>

            <div class="divider my-0"></div>

            <div>
                <p class="text-sm font-medium text-base-content/60 mb-2">Categories</p>
                <div class="flex flex-wrap gap-2">${categories}</div>
            </div>

            <div>
                <p class="text-sm font-medium text-base-content/60 mb-2">Description</p>
                <p class="text-sm">${product.description || 'No description'}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-base-content/60 mb-2">Images</p>
                <div class="flex flex-wrap gap-2">${imagesHtml}</div>
            </div>

            ${product.variants && product.variants.length > 0 ? `
            <div class="divider my-0"></div>

            <div>
                <p class="text-sm font-medium text-base-content/60 mb-2">Variants (${product.variants.length})</p>
                <div class="overflow-x-auto">
                    <table class="table table-xs">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>SKU</th>
                                <th>Size</th>
                                <th>Color</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Weight</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${product.variants.map(variant => {
                                const primaryImage = variant.images && variant.images.length > 0
                                    ? variant.images.find(img => img.is_primary) || variant.images[0]
                                    : null;

                                return `
                                <tr>
                                    <td>
                                        ${primaryImage ? `
                                            <div class="avatar">
                                                <div class="w-12 rounded">
                                                    <img src="/storage/${primaryImage.url}" alt="${primaryImage.alt_text || variant.color}" />
                                                </div>
                                            </div>
                                        ` : '<span class="text-xs opacity-60">No image</span>'}
                                    </td>
                                    <td class="font-mono text-xs">${variant.sku}</td>
                                    <td>${variant.size || '-'}</td>
                                    <td>${variant.color || '-'}</td>
                                    <td>Rp ${parseFloat(variant.price || 0).toLocaleString('id-ID')}</td>
                                    <td><span class="badge badge-sm ${variant.stock_quantity > 10 ? 'badge-success' : variant.stock_quantity > 0 ? 'badge-warning' : 'badge-error'}">${variant.stock_quantity || 0}</span></td>
                                    <td>${variant.weight_gram || 0}g</td>
                                </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
            ` : ''}

            <div class="divider my-0"></div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-base-content/60">Created At</p>
                    <p>${dateStr}</p>
                </div>
                <div>
                    <p class="text-base-content/60">Last Updated</p>
                    <p>${new Date(product.updated_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}</p>
                </div>
            </div>
        </div>
    `;

    $('#productDetailContent').html(html);
}

async function updateProductStatus(productId, newStatus) {
    try {
        const response = await Ajax.patch(`/api/catalog/products/${productId}/status`,
            { status: newStatus },
            {
                showLoading: true,
                showToast: true,
                loadingMessage: 'Updating product status...',
                successMessage: 'Product status updated successfully'
            }
        );

        if (response.status) {
            await loadData();
            currentProductId = null;
        }
    } catch (error) {
        console.error('Error updating product status:', error);
    }
}

async function deleteProduct(productId) {
    try {
        const response = await Ajax.delete(`/api/catalog/products/${productId}`, {
            showLoading: true,
            showToast: true,
            loadingMessage: 'Deleting product...',
            successMessage: 'Product deleted successfully'
        });

        if (response.status) {
            await loadData();
            currentProductId = null;
        }
    } catch (error) {
        console.error('Error deleting product:', error);
    }
}
