import Ajax from '../../../../utils/ajax.js';
import Toast from '../../../../components/toast.js';
import $ from 'jquery';

// ============================================
// CATEGORY OPERATIONS
// ============================================

export async function handleSaveCategories(productId) {
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
        }
    } catch (error) {
        console.error('Error saving categories:', error);
    }
}

export function initCategoryEventListeners(productId) {
    $('#saveCategoriesBtn').on('click', () => handleSaveCategories(productId));
}
