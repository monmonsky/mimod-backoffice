import $ from 'jquery';
import Ajax from '../../../utils/ajax.js';

$(document).ready(function() {
    // Load categories
    loadCategories();

    // Load initial data
    loadInventoryData();

    // Filter button
    $('#filterBtn').on('click', function() {
        loadInventoryData();
    });

    // Export button
    $('#exportBtn').on('click', async function() {
        const category = $('#categoryFilter').val();
        const stockLevel = $('#stockFilter').val();

        try {
            await Ajax.post('/reports/inventory/export', {
                category_id: category,
                stock_level: stockLevel
            }, {
                loadingMessage: 'Preparing export...',
                successMessage: 'Report exported successfully'
            });
        } catch (error) {
            // Error handled by Ajax helper
        }
    });

    // Search functionality
    $('#searchInput').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('#inventoryTable tbody tr, #lowStockTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});

async function loadCategories() {
    try {
        // TODO: Replace with actual API endpoint
        // const response = await Ajax.get('/api/categories', {
        //     showToast: false
        // });

        // Mock categories
        const categories = [
            { id: 1, name: 'Baby Food' },
            { id: 2, name: 'Diapers' },
            { id: 3, name: 'Toys' },
            { id: 4, name: 'Clothing' }
        ];

        let options = '<option value="">All Categories</option>';
        categories.forEach(cat => {
            options += `<option value="${cat.id}">${cat.name}</option>`;
        });
        $('#categoryFilter').html(options);

    } catch (error) {
        // Error handled by Ajax helper
    }
}

async function loadInventoryData() {
    const category = $('#categoryFilter').val();
    const stockLevel = $('#stockFilter').val();

    try {
        // TODO: Replace with actual API endpoint
        // const response = await Ajax.get('/api/reports/inventory', {
        //     params: {
        //         category_id: category,
        //         stock_level: stockLevel
        //     },
        //     loadingMessage: 'Loading inventory data...',
        //     showToast: false
        // });

        // Mock data for now
        updateStatistics({
            totalSkus: '1,245',
            inStock: '1,089',
            lowStock: '87',
            outOfStock: '69'
        });

        // TODO: Populate tables with actual data
        // populateLowStockTable(response.low_stock);
        // populateInventoryTable(response.inventory);

    } catch (error) {
        // Error handled by Ajax helper
    }
}

function updateStatistics(stats) {
    $('#totalSkus').text(stats.totalSkus);
    $('#inStock').text(stats.inStock);
    $('#lowStock').text(stats.lowStock);
    $('#outOfStock').text(stats.outOfStock);
}
