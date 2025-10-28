import Ajax from '../../../../utils/ajax.js';
import Toast from '../../../../components/toast.js';
import $ from 'jquery';

// ============================================
// IMAGE RENDERING
// ============================================

export function renderProductImages(images) {
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

// ============================================
// IMAGE OPERATIONS
// ============================================

export function handleImagePreview() {
    const files = $('#productImages')[0].files;
    const $previewContainer = $('#imagePreviewContainer');
    const $uploadBtn = $('#uploadImagesBtn');

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
                        <img src="${e.target.result}" alt="Preview" class="w-full h-32 object-cover">
                        <div class="absolute top-1 right-1">
                            <button type="button" class="btn btn-xs btn-circle btn-error remove-preview-btn" data-index="${index}">✕</button>
                        </div>
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

export async function handleImageUpload(productId, reloadCallback) {
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
            if (reloadCallback) reloadCallback();
        }
    } catch (error) {
        console.error('Error uploading images:', error);
    }
}

export async function handleImageDelete(productId, imageId, reloadCallback) {
    if (!confirm('Are you sure you want to delete this image?')) {
        return;
    }

    try {
        const response = await Ajax.delete(`/api/catalog/products/${productId}/images/${imageId}`, {
            showLoading: true,
            loadingMessage: 'Deleting image...'
        });

        if (response.status) {
            Toast.showSuccess(response.message || 'Image deleted successfully');
            if (reloadCallback) reloadCallback();
        }
    } catch (error) {
        console.error('Error deleting image:', error);
    }
}

export async function handleSetPrimaryImage(productId, imageId, reloadCallback) {
    try {
        const response = await Ajax.post(`/api/catalog/products/${productId}/images/${imageId}/primary`, {}, {
            showLoading: true,
            loadingMessage: 'Setting primary image...'
        });

        if (response.status) {
            Toast.showSuccess(response.message || 'Primary image updated');
            if (reloadCallback) reloadCallback();
        }
    } catch (error) {
        console.error('Error setting primary image:', error);
    }
}

export function handleRemovePreview() {
    const index = $(this).data('index');
    const $input = $('#productImages')[0];
    const dt = new DataTransfer();

    Array.from($input.files).forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });

    $input.files = dt.files;
    handleImagePreview();
}

export function handlePreviewProductImage(e) {
    e.stopPropagation();
    const imageUrl = $(this).data('image-url');
    showImagePreview(imageUrl);
}

export function showImagePreview(imageUrl) {
    $('#previewImage').attr('src', imageUrl);
    const modal = document.getElementById('imagePreviewModal');
    if (modal) {
        modal.showModal();
    }
}

// ============================================
// EVENT LISTENERS
// ============================================

export function initImageEventListeners(productId, reloadCallback) {
    $('#productImages').on('change', handleImagePreview);
    $('#uploadImagesBtn').on('click', () => handleImageUpload(productId, reloadCallback));
    $(document).on('click', '.delete-image-btn', function() {
        handleImageDelete(productId, $(this).data('image-id'), reloadCallback);
    });
    $(document).on('click', '.set-primary-btn', function() {
        handleSetPrimaryImage(productId, $(this).data('image-id'), reloadCallback);
    });
    $(document).on('click', '.remove-preview-btn', handleRemovePreview);
    $(document).on('click', '.preview-product-image-btn', handlePreviewProductImage);
    $(document).on('click', '.product-image-item', function(e) {
        if (!$(e.target).closest('button').length) {
            const imageUrl = $(this).data('image-url');
            showImagePreview(imageUrl);
        }
    });
}
