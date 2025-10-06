import Toast from '../../../components/toast.js';
import Loading from '../../../components/loading.js';

$(document).ready(function() {
    const productId = $('input[name="product_id"]').val();
    const isEditMode = !!productId;

    // Auto-generate slug from name (always, field is readonly)
    $('#productName').on('input', function() {
        const name = $(this).val();
        const slug = name
            .toLowerCase()
            .replace(/'/g, '')  // Remove apostrophes
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        $('#productSlug').val(slug);
    });

    // Sync featured checkbox with hidden input
    $('#productFeatured').on('change', function() {
        $('#productFeaturedValue').val(this.checked ? '1' : '0');
    });

    // Initialize featured value on page load
    $('#productFeaturedValue').val($('#productFeatured').is(':checked') ? '1' : '0');

    // ===== PRODUCT FORM SUBMIT =====
    $('#productForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        // Get selected categories
        const selectedCategories = [];
        $('input[name="categories[]"]:checked').each(function() {
            selectedCategories.push($(this).val());
        });

        // Remove old category entries and add new ones
        formData.delete('categories[]');
        selectedCategories.forEach(catId => {
            formData.append('categories[]', catId);
        });

        const url = isEditMode
            ? `/catalog/products/${productId}`
            : '/catalog/products/store';

        // Add method spoofing for PUT
        if (isEditMode) {
            formData.append('_method', 'PUT');
        }

        const $submitBtn = $('#saveProductBtn');
        const originalBtnText = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<span class="loading loading-spinner loading-sm"></span> Saving...');

        Loading.show(isEditMode ? 'Updating product...' : 'Creating product...');

        $.ajax({
            url: url,
            type: 'POST',
            data: Object.fromEntries(formData),
            success: function(response) {
                if (response.success) {
                    Toast.showSuccess(response.message || 'Product saved successfully');
                    Loading.updateMessage('Redirecting...');

                    // Redirect to edit page for new product, reload for edit
                    setTimeout(() => {
                        if (!isEditMode && response.product_id) {
                            window.location.href = `/catalog/products/${response.product_id}/edit`;
                        } else {
                            location.reload();
                        }
                    }, 500);
                } else {
                    Loading.hide();
                    Toast.showError(response.message || 'Failed to save product');
                    $submitBtn.prop('disabled', false).html(originalBtnText);
                }
            },
            error: function(xhr) {
                Loading.hide();
                let message = 'Failed to save product';

                if (xhr.responseJSON?.errors) {
                    const errors = xhr.responseJSON.errors;
                    message = Object.values(errors).flat().join('<br>');
                } else if (xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
                }

                Loading.hide();
                Toast.showError(message);
                $submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });

    // ===== IMAGE UPLOAD =====
    if (isEditMode) {
        const $imageInput = $('#productImages');
        const $imagePreview = $('#imagePreviewContainer');
        const $uploadBtn = $('#uploadImagesBtn');
        const $selectedFilesInfo = $('#selectedFilesInfo');
        const $fileList = $('#fileList');
        const $fileCount = $('#fileCount');

        // Preview images before upload
        $imageInput.on('change', function(e) {
            const files = e.target.files;

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
            Array.from(files).forEach((file, index) => {
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
        });

        // Remove preview image (note: can't actually remove from FileList, so we'll just hide it)
        $imagePreview.on('click', '.remove-preview-btn', function() {
            const index = $(this).data('index');
            $(this).closest('.relative').remove();

            // Reset input if no previews left
            if ($imagePreview.children().length === 0) {
                $imageInput.val('');
                $imagePreview.addClass('hidden');
                $selectedFilesInfo.addClass('hidden');
                $uploadBtn.prop('disabled', true);
            }
        });

        // Upload images
        $uploadBtn.on('click', function() {
            const files = $imageInput[0].files;
            if (files.length === 0) {
                Toast.showWarning('Please select images to upload');
                return;
            }

            const formData = new FormData();
            Array.from(files).forEach((file) => {
                formData.append('images[]', file);
            });
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            const $btn = $(this);
            $btn.prop('disabled', true).html('<span class="loading loading-spinner loading-sm"></span> Uploading...');

            Loading.show('Uploading images...');

            $.ajax({
                url: `/catalog/products/${productId}/images/upload`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        Toast.showSuccess('Images uploaded successfully');
                        Loading.updateMessage('Refreshing...');
                        setTimeout(() => location.reload(), 500);
                    } else {
                        Loading.hide();
                        Toast.showError(response.message || 'Failed to upload images');
                        $btn.prop('disabled', false).html('<span class="iconify lucide--upload size-4"></span> Upload Images');
                    }
                },
                error: function(xhr) {
                    Loading.hide();
                    const message = xhr.responseJSON?.message || 'Failed to upload images';
                    Toast.showError(message);
                    $btn.prop('disabled', false).html('<span class="iconify lucide--upload size-4"></span> Upload Images');
                }
            });
        });

        // Delete image
        $('.delete-image-btn').on('click', function() {
            if (!confirm('Are you sure you want to delete this image?')) {
                return;
            }

            const imageId = $(this).data('image-id');
            const $imageCard = $(this).closest('.relative');

            Loading.show('Deleting image...');

            $.ajax({
                url: `/catalog/products/${productId}/images/${imageId}`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Toast.showSuccess('Image deleted successfully');
                        Loading.hide();
                        $imageCard.fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        Loading.hide();
                        Toast.showError(response.message || 'Failed to delete image');
                    }
                },
                error: function(xhr) {
                    Loading.hide();
                    const message = xhr.responseJSON?.message || 'Failed to delete image';
                    Toast.showError(message);
                }
            });
        });

        // Set primary image
        $('.set-primary-btn').on('click', function() {
            const imageId = $(this).data('image-id');

            Loading.show('Setting primary image...');

            $.ajax({
                url: `/catalog/products/${productId}/images/${imageId}/set-primary`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Toast.showSuccess('Primary image updated');
                        Loading.updateMessage('Refreshing...');
                        setTimeout(() => location.reload(), 500);
                    } else {
                        Loading.hide();
                        Toast.showError(response.message || 'Failed to set primary image');
                    }
                },
                error: function(xhr) {
                    Loading.hide();
                    const message = xhr.responseJSON?.message || 'Failed to set primary image';
                    Toast.showError(message);
                }
            });
        });
    }

    // ===== VARIANT MANAGEMENT =====
    const $variantModal = $('#variantModal')[0];
    const $variantForm = $('#variantForm');

    // Open modal for add variant
    $('#addVariantBtn').on('click', function() {
        // Reset form
        $variantForm[0].reset();
        $variantForm.find('input[name="variant_id"]').val('');
        $('#variantModalTitle').text('Add Variant');
        $('#variantSubmitBtn').html('<span class="iconify lucide--save size-4"></span> Add Variant');
        $variantModal.showModal();
    });

    // Open modal for edit variant
    $('.edit-variant-btn').on('click', function() {
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
    });

    // Submit variant form
    $variantForm.on('submit', function(e) {
        e.preventDefault();

        const variantId = $(this).find('input[name="variant_id"]').val();
        const isEdit = !!variantId;

        const formData = new FormData(this);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        const url = isEdit
            ? `/catalog/products/${productId}/variants/${variantId}`
            : `/catalog/products/${productId}/variants`;

        const method = isEdit ? 'PUT' : 'POST';

        const $submitBtn = $('#variantSubmitBtn');
        $submitBtn.prop('disabled', true).html('<span class="loading loading-spinner loading-sm"></span> Saving...');

        Loading.show(isEdit ? 'Updating variant...' : 'Adding variant...');

        $.ajax({
            url: url,
            type: method,
            data: Object.fromEntries(formData),
            success: function(response) {
                if (response.success) {
                    Toast.showSuccess(response.message || 'Variant saved successfully');
                    Loading.updateMessage('Refreshing...');
                    setTimeout(() => location.reload(), 500);
                } else {
                    Loading.hide();
                    Toast.showError(response.message || 'Failed to save variant');
                    $submitBtn.prop('disabled', false);
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Failed to save variant';
                Loading.hide();
                Toast.showError(message);
                $submitBtn.prop('disabled', false);
            }
        });
    });

    // Delete variant
    $('.delete-variant-btn').on('click', function() {
        if (!confirm('Are you sure you want to delete this variant?')) {
            return;
        }

        const variantId = $(this).data('id');

        Loading.show('Deleting variant...');

        $.ajax({
            url: `/catalog/products/${productId}/variants/${variantId}`,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Toast.showSuccess('Variant deleted successfully');
                    Loading.updateMessage('Refreshing...');
                    setTimeout(() => location.reload(), 500);
                } else {
                    Loading.hide();
                    Toast.showError(response.message || 'Failed to delete variant');
                }
            },
            error: function(xhr) {
                Loading.hide();
                const message = xhr.responseJSON?.message || 'Failed to delete variant';
                Toast.showError(message);
            }
        });
    });
});
