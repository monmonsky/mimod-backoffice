import Ajax from '../../../utils/ajax.js';
import Toast from '../../../components/toast.js';
import $ from 'jquery';

let productId = null;
let isEditMode = false;

// ============================================
// INITIALIZATION
// ============================================

$(document).ready(function() {
    productId = $('input[name="product_id"]').val();
    isEditMode = !!productId;

    initEventListeners();
    initSlugGeneration();
    initFeaturedCheckbox();

    // Load data if in edit mode
    if (isEditMode) {
        loadProductData();
    }
});

// ============================================
// EVENT LISTENERS
// ============================================

function initEventListeners() {
    // Product form submit
    $('#productForm').on('submit', handleProductSubmit);

    // Category operations (only in edit mode)
    if (isEditMode) {
        $('#saveCategoriesBtn').on('click', handleSaveCategories);
    }

    // Image operations (only in edit mode)
    if (isEditMode) {
        $('#productImages').on('change', handleImagePreview);
        $('#uploadImagesBtn').on('click', handleImageUpload);
        $(document).on('click', '.delete-image-btn', handleImageDelete);
        $(document).on('click', '.set-primary-btn', handleSetPrimaryImage);
        $(document).on('click', '.remove-preview-btn', handleRemovePreview);
        $(document).on('click', '.preview-product-image-btn', handlePreviewProductImage);
        $(document).on('click', '.product-image-item', function(e) {
            if (!$(e.target).closest('button').length) {
                const imageUrl = $(this).data('image-url');
                showImagePreview(imageUrl);
            }
        });
    }

    // Variant operations (only in edit mode)
    if (isEditMode) {
        $('#addVariantBtn').on('click', openAddVariantModal);
        $(document).on('click', '.edit-variant-btn', openEditVariantModal);
        $('#variantForm').on('submit', handleVariantSubmit);
        $(document).on('click', '.delete-variant-btn', handleVariantDelete);

        // Variant image operations
        $(document).on('change', '.variant-images-input', handleVariantImagePreview);
        $(document).on('click', '.upload-variant-images-btn', handleVariantImageUpload);
        $(document).on('click', '.delete-variant-image-btn', handleVariantImageDelete);
        $(document).on('click', '.set-primary-variant-btn', handleSetPrimaryVariantImage);
        $(document).on('click', '.preview-variant-image-btn', handlePreviewVariantImage);
        $(document).on('click', '.variant-image-item', function(e) {
            if (!$(e.target).closest('button').length) {
                const imageUrl = $(this).data('image-url');
                showImagePreview(imageUrl);
            }
        });
    }
}

// ============================================
// DATA LOADING & RENDERING
// ============================================

async function loadProductData() {
    try {
        const response = await Ajax.get(`/api/catalog/products/${productId}`, {
            showLoading: true,
            loadingMessage: 'Loading product data...'
        });

        if (response.status && response.data) {
            renderProductImages(response.data.images || []);
            renderProductVariants(response.data.variants || []);
        }
    } catch (error) {
        console.error('Error loading product data:', error);
        Toast.showError('Failed to load product data');
    }
}

function renderProductImages(images) {
    const $container = $('#productImagesContainer');

    if (images.length === 0) {
        $container.html(`
            <div class="text-center py-12 bg-base-200/30 rounded-lg">
                <span class="iconify lucide--image-off size-12 mx-auto mb-3 text-base-content/40"></span>
                <p class="text-sm text-base-content/50">No images uploaded yet</p>
            </div>
        `);
        return;
    }

    const imagesHtml = images.map(image => `
        <div class="relative group border-2 ${image.is_primary ? 'border-primary' : 'border-base-300'} rounded-lg overflow-hidden cursor-pointer product-image-item" data-image-url="${window.location.origin}/storage/${image.url}">
            <img src="${window.location.origin}/storage/${image.url}" alt="${image.alt_text || ''}" class="w-full h-32 object-cover">
            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-1">
                <button type="button" class="btn btn-xs btn-ghost text-white preview-product-image-btn" data-image-url="${window.location.origin}/storage/${image.url}" title="Preview">
                    <span class="iconify lucide--eye size-3"></span>
                </button>
                ${!image.is_primary ? `
                    <button type="button" class="btn btn-xs btn-success set-primary-btn" data-image-id="${image.id}" title="Set as primary">
                        <span class="iconify lucide--star size-3"></span>
                    </button>
                ` : ''}
                <button type="button" class="btn btn-xs btn-error delete-image-btn" data-image-id="${image.id}">
                    <span class="iconify lucide--trash size-3"></span>
                </button>
            </div>
            ${image.is_primary ? '<div class="absolute top-1 left-1"><span class="badge badge-xs badge-primary">Primary</span></div>' : ''}
            <div class="absolute bottom-1 right-1">
                <span class="badge badge-xs badge-ghost bg-black/70 text-white border-0">#${image.sort_order}</span>
            </div>
        </div>
    `).join('');

    $container.html(`<div class="grid grid-cols-4 gap-3">${imagesHtml}</div>`);
}

function renderProductVariants(variants) {
    const $container = $('#productVariantsContainer');

    if (variants.length === 0) {
        $container.html(`
            <div class="text-center py-12 border border-base-300 rounded-lg">
                <div class="flex flex-col items-center gap-3 text-base-content/50">
                    <span class="iconify lucide--package size-16"></span>
                    <div>
                        <p class="font-medium">No variants added yet</p>
                        <p class="text-sm mt-1">Click "Add Variant" to create product variations</p>
                    </div>
                </div>
            </div>
        `);
        return;
    }

    const variantsHtml = variants.map(variant => {
        const variantImages = variant.images || [];
        const imagesHtml = variantImages.length > 0 ? `
            <div class="grid grid-cols-6 gap-2">
                ${variantImages.map(image => `
                    <div class="relative group border-2 border-base-300 rounded-lg overflow-hidden cursor-pointer variant-image-item" data-image-url="${window.location.origin}/storage/${image.url}">
                        <img src="${window.location.origin}/storage/${image.url}" alt="${image.alt_text || ''}" class="w-full h-24 object-cover">
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-1">
                            <button type="button" class="btn btn-xs btn-ghost text-white preview-variant-image-btn" data-image-url="${window.location.origin}/storage/${image.url}" title="Preview">
                                <span class="iconify lucide--eye size-3"></span>
                            </button>
                            ${!image.is_primary ? `
                                <button type="button" class="btn btn-xs btn-success set-primary-variant-btn" data-variant-id="${variant.id}" data-image-id="${image.id}" title="Set as primary">
                                    <span class="iconify lucide--star size-3"></span>
                                </button>
                            ` : ''}
                            <button type="button" class="btn btn-xs btn-error delete-variant-image-btn" data-variant-id="${variant.id}" data-image-id="${image.id}">
                                <span class="iconify lucide--trash size-3"></span>
                            </button>
                        </div>
                        ${image.is_primary ? '<div class="absolute top-1 left-1"><span class="badge badge-xs badge-success">Primary</span></div>' : ''}
                        <div class="absolute bottom-1 right-1">
                            <span class="badge badge-xs badge-ghost bg-black/70 text-white border-0">#${image.sort_order}</span>
                        </div>
                    </div>
                `).join('')}
            </div>
        ` : `
            <div class="text-center py-6 bg-base-200/30 rounded-lg">
                <span class="iconify lucide--image-off size-8 mx-auto mb-2 text-base-content/40"></span>
                <p class="text-sm text-base-content/50">No images uploaded for this variant</p>
            </div>
        `;

        const stockBadge = variant.stock_quantity > 10 ? 'badge-success' : (variant.stock_quantity > 0 ? 'badge-warning' : 'badge-error');

        return `
            <div class="card bg-base-100 border border-base-300 shadow-sm">
                <div class="card-body p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-6 gap-4">
                            <div>
                                <div class="text-xs text-base-content/60 mb-1">SKU</div>
                                <code class="text-xs bg-base-200 px-2 py-1 rounded">${variant.sku}</code>
                            </div>
                            <div>
                                <div class="text-xs text-base-content/60 mb-1">Size</div>
                                <span class="badge badge-outline badge-sm">${variant.size}</span>
                            </div>
                            <div>
                                <div class="text-xs text-base-content/60 mb-1">Color</div>
                                <span class="text-sm font-medium">${variant.color || '-'}</span>
                            </div>
                            <div>
                                <div class="text-xs text-base-content/60 mb-1">Price</div>
                                <div class="flex flex-col">
                                    <span class="font-semibold text-sm">Rp ${parseFloat(variant.price || 0).toLocaleString('id-ID')}</span>
                                    ${variant.compare_at_price ? `<span class="text-xs text-base-content/60 line-through">Rp ${parseFloat(variant.compare_at_price).toLocaleString('id-ID')}</span>` : ''}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-base-content/60 mb-1">Stock</div>
                                <span class="badge ${stockBadge} badge-sm">${variant.stock_quantity} units</span>
                            </div>
                            <div>
                                <div class="text-xs text-base-content/60 mb-1">Weight</div>
                                <span class="text-sm">${variant.weight_gram}g</span>
                            </div>
                        </div>
                        <div class="flex gap-1">
                            <button type="button" class="btn btn-sm btn-ghost edit-variant-btn"
                                data-id="${variant.id}"
                                data-sku="${variant.sku}"
                                data-size="${variant.size}"
                                data-color="${variant.color || ''}"
                                data-weight="${variant.weight_gram}"
                                data-price="${variant.price}"
                                data-compare-price="${variant.compare_at_price || ''}"
                                data-stock="${variant.stock_quantity}"
                                data-barcode="${variant.barcode || ''}">
                                <span class="iconify lucide--pencil size-4"></span>
                            </button>
                            <button type="button" class="btn btn-sm btn-ghost text-error delete-variant-btn" data-id="${variant.id}">
                                <span class="iconify lucide--trash size-4"></span>
                            </button>
                        </div>
                    </div>
                    <div class="divider my-2"></div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="font-semibold text-sm flex items-center gap-2">
                                <span class="iconify lucide--image size-4"></span>
                                Variant Images
                            </h4>
                            <div class="flex items-center gap-2">
                                <input type="file" id="variantImages-${variant.id}" class="variant-images-input hidden" data-variant-id="${variant.id}" accept="image/*" multiple>
                                <label for="variantImages-${variant.id}" class="btn btn-sm btn-ghost">
                                    <span class="iconify lucide--upload size-4"></span>
                                    Select Images
                                </label>
                                <button type="button" class="btn btn-sm btn-primary upload-variant-images-btn" data-variant-id="${variant.id}" disabled>
                                    <span class="iconify lucide--save size-4"></span>
                                    Upload
                                </button>
                            </div>
                        </div>
                        <div id="variantImagePreview-${variant.id}" class="grid grid-cols-6 gap-2 hidden"></div>
                        ${imagesHtml}
                    </div>
                </div>
            </div>
        `;
    }).join('');

    const totalStock = variants.reduce((sum, v) => sum + (v.stock_quantity || 0), 0);
    const lowStock = variants.filter(v => v.stock_quantity < 10).length;

    $container.html(`
        <div class="grid grid-cols-1 gap-4">
            ${variantsHtml}
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="border border-base-300 rounded-lg p-3">
                <div class="text-xs text-base-content/60">Total Variants</div>
                <div class="text-2xl font-semibold mt-1">${variants.length}</div>
            </div>
            <div class="border border-base-300 rounded-lg p-3">
                <div class="text-xs text-base-content/60">Total Stock</div>
                <div class="text-2xl font-semibold mt-1">${totalStock}</div>
            </div>
            <div class="border border-base-300 rounded-lg p-3">
                <div class="text-xs text-base-content/60">Low Stock Items</div>
                <div class="text-2xl font-semibold text-warning mt-1">${lowStock}</div>
            </div>
        </div>
    `);
}

// ============================================
// PRODUCT OPERATIONS
// ============================================

async function handleProductSubmit(e) {
    e.preventDefault();

    const formData = {};
    const form = $(this).serializeArray();

    form.forEach(field => {
        if (field.name === 'categories[]') {
            if (!formData.categories) formData.categories = [];
            formData.categories.push(field.value);
        } else {
            formData[field.name] = field.value;
        }
    });

    // Convert is_featured to boolean
    formData.is_featured = $('#productFeatured').is(':checked');

    const url = isEditMode ? `/api/catalog/products/${productId}` : '/api/catalog/products';

    try {
        const response = await Ajax.post(url, formData, {
            showLoading: true,
            loadingMessage: isEditMode ? 'Updating product...' : 'Creating product...'
        });

        if (response.status) {
            Toast.showSuccess(response.message || 'Product saved successfully');

            // Redirect to edit page for new product, reload for edit
            setTimeout(() => {
                if (!isEditMode && response.data?.product_id) {
                    window.location.href = `/catalog/products/${response.data.product_id}/edit`;
                } else {
                    location.reload();
                }
            }, 500);
        }
    } catch (error) {
        console.error('Error saving product:', error);
    }
}

async function handleSaveCategories() {
    const selectedCategories = [];
    $('input[name="categories[]"]:checked').each(function() {
        selectedCategories.push($(this).val());
    });

    const data = {
        categories: selectedCategories
    };

    try {
        const response = await Ajax.post(`/api/catalog/products/${productId}/categories`, data, {
            showLoading: true,
            loadingMessage: 'Saving categories...'
        });

        if (response.status) {
            Toast.showSuccess(response.message || 'Categories saved successfully');
            loadProductData();
        }
    } catch (error) {
        console.error('Error saving categories:', error);
    }
}

// ============================================
// IMAGE OPERATIONS
// ============================================

function handleImagePreview(e) {
    const files = e.target.files;
    const $imagePreview = $('#imagePreviewContainer');
    const $selectedFilesInfo = $('#selectedFilesInfo');
    const $uploadBtn = $('#uploadImagesBtn');
    const $fileList = $('#fileList');
    const $fileCount = $('#fileCount');

    if (files.length === 0) {
        $imagePreview.addClass('hidden').empty();
        $selectedFilesInfo.addClass('hidden');
        $uploadBtn.prop('disabled', true);
        return;
    }

    // Enable upload button
    $uploadBtn.prop('disabled', false);

    // Show file count
    $fileCount.text(`${files.length} file${files.length > 1 ? 's' : ''}`);
    $selectedFilesInfo.removeClass('hidden');

    // Show file list
    $fileList.empty();
    Array.from(files).forEach((file) => {
        const fileSize = (file.size / 1024).toFixed(1) + ' KB';
        const $fileItem = $(`
            <div class="flex items-center justify-between text-xs py-1 px-2 bg-base-200 rounded">
                <div class="flex items-center gap-2 flex-1 min-w-0">
                    <span class="iconify lucide--image size-3 text-primary flex-shrink-0"></span>
                    <span class="truncate">${file.name}</span>
                </div>
                <span class="text-base-content/60 ml-2 flex-shrink-0">${fileSize}</span>
            </div>
        `);
        $fileList.append($fileItem);
    });

    // Show image previews
    $imagePreview.empty().removeClass('hidden');

    Array.from(files).forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const $preview = $(`
                    <div class="relative group border-2 border-base-300 rounded-lg overflow-hidden">
                        <img src="${e.target.result}" alt="Preview" class="w-full h-32 object-cover">
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                            <button type="button" class="btn btn-sm btn-circle btn-error remove-preview-btn" data-index="${index}">
                                <span class="iconify lucide--x size-4"></span>
                            </button>
                        </div>
                        <div class="absolute bottom-1 right-1">
                            <span class="badge badge-xs badge-ghost bg-black/70 text-white border-0">
                                #${index + 1}
                            </span>
                        </div>
                    </div>
                `);
                $imagePreview.append($preview);
            };
            reader.readAsDataURL(file);
        }
    });
}

function handleRemovePreview(e) {
    const $imagePreview = $('#imagePreviewContainer');
    const $imageInput = $('#productImages');
    const $selectedFilesInfo = $('#selectedFilesInfo');
    const $uploadBtn = $('#uploadImagesBtn');

    $(this).closest('.relative').remove();

    // Reset input if no previews left
    if ($imagePreview.children().length === 0) {
        $imageInput.val('');
        $imagePreview.addClass('hidden');
        $selectedFilesInfo.addClass('hidden');
        $uploadBtn.prop('disabled', true);
    }
}

async function handleImageUpload() {
    const files = $('#productImages')[0].files;
    if (files.length === 0) {
        Toast.showWarning('Please select images to upload');
        return;
    }

    const formData = new FormData();
    Array.from(files).forEach((file) => {
        formData.append('images[]', file);
    });

    try {
        const response = await Ajax.post(`/api/catalog/products/${productId}/images`, formData, {
            showLoading: true,
            loadingMessage: 'Uploading images...'
        });

        if (response.status) {
            Toast.showSuccess(response.message || 'Images uploaded successfully');
            $('#productImages').val('');
            $('#imagePreviewContainer').addClass('hidden').empty();
            $('#uploadImagesBtn').prop('disabled', true);
            loadProductData();
        }
    } catch (error) {
        console.error('Error uploading images:', error);
    }
}

async function handleImageDelete(e) {
    if (!confirm('Are you sure you want to delete this image?')) {
        return;
    }

    const imageId = $(this).data('image-id');

    try {
        const response = await Ajax.delete(`/api/catalog/products/${productId}/images/${imageId}`, {
            showLoading: true,
            loadingMessage: 'Deleting image...'
        });

        if (response.status) {
            Toast.showSuccess(response.message || 'Image deleted successfully');
            loadProductData();
        }
    } catch (error) {
        console.error('Error deleting image:', error);
    }
}

async function handleSetPrimaryImage(e) {
    const imageId = $(this).data('image-id');

    try {
        const response = await Ajax.post(`/api/catalog/products/${productId}/images/${imageId}/primary`, {}, {
            showLoading: true,
            loadingMessage: 'Setting primary image...'
        });

        if (response.status) {
            Toast.showSuccess(response.message || 'Primary image updated');
            loadProductData();
        }
    } catch (error) {
        console.error('Error setting primary image:', error);
    }
}

// ============================================
// VARIANT OPERATIONS
// ============================================

function openAddVariantModal() {
    const $variantModal = $('#variantModal')[0];
    const $variantForm = $('#variantForm');

    // Reset form
    $variantForm[0].reset();
    $variantForm.find('input[name="variant_id"]').val('');
    $('#variantModalTitle').text('Add Variant');
    $('#variantSubmitBtn').html('<span class="iconify lucide--save size-4"></span> Add Variant');
    $variantModal.showModal();
}

function openEditVariantModal(e) {
    const $variantModal = $('#variantModal')[0];
    const $variantForm = $('#variantForm');

    const variantId = $(this).data('id');
    const sku = $(this).data('sku');
    const size = $(this).data('size');
    const color = $(this).data('color');
    const weight = $(this).data('weight');
    const price = $(this).data('price');
    const comparePrice = $(this).data('compare-price');
    const stock = $(this).data('stock');
    const barcode = $(this).data('barcode');

    // Fill form
    $variantForm.find('input[name="variant_id"]').val(variantId);
    $variantForm.find('input[name="sku"]').val(sku);
    $variantForm.find('input[name="size"]').val(size);
    $variantForm.find('input[name="color"]').val(color);
    $variantForm.find('input[name="weight_gram"]').val(weight);
    $variantForm.find('input[name="price"]').val(price);
    $variantForm.find('input[name="compare_at_price"]').val(comparePrice);
    $variantForm.find('input[name="stock_quantity"]').val(stock);
    $variantForm.find('input[name="barcode"]').val(barcode);

    $('#variantModalTitle').text('Edit Variant');
    $('#variantSubmitBtn').html('<span class="iconify lucide--save size-4"></span> Update Variant');
    $variantModal.showModal();
}

async function handleVariantSubmit(e) {
    e.preventDefault();

    const variantId = $(this).find('input[name="variant_id"]').val();
    const isEdit = !!variantId;

    const formData = {};
    $(this).serializeArray().forEach(field => {
        if (field.name !== 'variant_id') {
            formData[field.name] = field.value;
        }
    });

    const url = isEdit
        ? `/api/catalog/products/${productId}/variants/${variantId}`
        : `/api/catalog/products/${productId}/variants`;

    try {
        const response = await Ajax.post(url, formData, {
            showLoading: true,
            loadingMessage: isEdit ? 'Updating variant...' : 'Adding variant...'
        });

        if (response.status) {
            Toast.showSuccess(response.message || 'Variant saved successfully');
            loadProductData();
        }
    } catch (error) {
        console.error('Error saving variant:', error);
    }
}

async function handleVariantDelete(e) {
    if (!confirm('Are you sure you want to delete this variant?')) {
        return;
    }

    const variantId = $(this).data('id');

    try {
        const response = await Ajax.delete(`/api/catalog/products/${productId}/variants/${variantId}`, {
            showLoading: true,
            loadingMessage: 'Deleting variant...'
        });

        if (response.status) {
            Toast.showSuccess(response.message || 'Variant deleted successfully');
            loadProductData();
        }
    } catch (error) {
        console.error('Error deleting variant:', error);
    }
}

// ============================================
// VARIANT IMAGE OPERATIONS
// ============================================

function handleVariantImagePreview(e) {
    const $input = $(this);
    const variantId = $input.data('variant-id');
    const files = e.target.files;
    const $previewContainer = $(`#variantImagePreview-${variantId}`);
    const $uploadBtn = $(`.upload-variant-images-btn[data-variant-id="${variantId}"]`);

    if (files.length === 0) {
        $previewContainer.addClass('hidden').empty();
        $uploadBtn.prop('disabled', true);
        return;
    }

    $uploadBtn.prop('disabled', false);
    $previewContainer.empty().removeClass('hidden');

    Array.from(files).forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const $preview = $(`
                    <div class="relative group border-2 border-base-300 rounded-lg overflow-hidden">
                        <img src="${e.target.result}" alt="Preview" class="w-full h-24 object-cover">
                        <div class="absolute bottom-1 right-1">
                            <span class="badge badge-xs badge-ghost bg-black/70 text-white border-0">#${index + 1}</span>
                        </div>
                    </div>
                `);
                $previewContainer.append($preview);
            };
            reader.readAsDataURL(file);
        }
    });
}

async function handleVariantImageUpload(e) {
    const $btn = $(this);
    const variantId = $btn.data('variant-id');
    const $input = $(`.variant-images-input[data-variant-id="${variantId}"]`);
    const files = $input[0].files;

    if (files.length === 0) {
        Toast.showWarning('Please select images to upload');
        return;
    }

    const formData = new FormData();
    Array.from(files).forEach((file) => {
        formData.append('images[]', file);
    });

    try {
        const response = await Ajax.post(`/api/catalog/products/${productId}/variants/${variantId}/images`, formData, {
            showLoading: true,
            loadingMessage: 'Uploading variant images...'
        });

        if (response.status) {
            Toast.showSuccess(response.message || 'Variant images uploaded successfully');
            loadProductData();
        }
    } catch (error) {
        console.error('Error uploading variant images:', error);
    }
}

async function handleVariantImageDelete(e) {
    if (!confirm('Are you sure you want to delete this variant image?')) {
        return;
    }

    const $btn = $(this);
    const variantId = $btn.data('variant-id');
    const imageId = $btn.data('image-id');

    try {
        const response = await Ajax.delete(`/api/catalog/products/${productId}/variants/${variantId}/images/${imageId}`, {
            showLoading: true,
            loadingMessage: 'Deleting variant image...'
        });

        if (response.status) {
            Toast.showSuccess(response.message || 'Variant image deleted successfully');
            loadProductData();
        }
    } catch (error) {
        console.error('Error deleting variant image:', error);
    }
}

async function handleSetPrimaryVariantImage(e) {
    const $btn = $(this);
    const variantId = $btn.data('variant-id');
    const imageId = $btn.data('image-id');

    try {
        const response = await Ajax.post(`/api/catalog/products/${productId}/variants/${variantId}/images/${imageId}/primary`, {}, {
            showLoading: true,
            loadingMessage: 'Setting primary variant image...'
        });

        if (response.status) {
            Toast.showSuccess(response.message || 'Primary variant image updated');
            loadProductData();
        }
    } catch (error) {
        console.error('Error setting primary variant image:', error);
    }
}

function handlePreviewProductImage(e) {
    e.stopPropagation();
    const imageUrl = $(this).data('image-url');
    showImagePreview(imageUrl);
}

function handlePreviewVariantImage(e) {
    e.stopPropagation();
    const imageUrl = $(this).data('image-url');
    showImagePreview(imageUrl);
}

function showImagePreview(imageUrl) {
    $('#previewImage').attr('src', imageUrl);
    const modal = document.getElementById('imagePreviewModal');
    if (modal) {
        modal.showModal();
    }
}

// ============================================
// HELPER FUNCTIONS
// ============================================

function initSlugGeneration() {
    $('#productName').on('input', function() {
        const name = $(this).val();
        const slug = name
            .toLowerCase()
            .replace(/'/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        $('#productSlug').val(slug);
    });
}

function initFeaturedCheckbox() {
    $('#productFeatured').on('change', function() {
        $('#productFeaturedValue').val(this.checked ? '1' : '0');
    });

    // Initialize value on page load
    $('#productFeaturedValue').val($('#productFeatured').is(':checked') ? '1' : '0');
}
