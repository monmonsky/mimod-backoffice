import $ from 'jquery';
import Ajax from '../../../utils/ajax.js';

$(document).ready(function() {
    // Initialize date inputs with current month
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

    $('#startDate').val(formatDate(firstDay));
    $('#endDate').val(formatDate(lastDay));

    // Load categories
    loadCategories();

    // Load initial data
    loadPerformanceData();

    // Filter button
    $('#filterBtn').on('click', function() {
        loadPerformanceData();
    });

    // Export button
    $('#exportBtn').on('click', async function() {
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();
        const category = $('#categoryFilter').val();

        if (!startDate || !endDate) {
            alert('Please select date range');
            return;
        }

        try {
            await Ajax.post('/reports/product-performance/export', {
                start_date: startDate,
                end_date: endDate,
                category_id: category
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
        $('#performanceTable tbody tr').filter(function() {
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

async function loadPerformanceData() {
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();
    const category = $('#categoryFilter').val();

    if (!startDate || !endDate) {
        alert('Please select date range');
        return;
    }

    try {
        // TODO: Replace with actual API endpoint
        // const response = await Ajax.get('/api/reports/product-performance', {
        //     params: {
        //         start_date: startDate,
        //         end_date: endDate,
        //         category_id: category
        //     },
        //     loadingMessage: 'Loading performance data...',
        //     showToast: false
        // });

        // Mock data for now
        updateStatistics({
            totalProducts: '245',
            bestSeller: 'Baby Milk Formula',
            totalViews: '45,230',
            conversionRate: '3.8%'
        });

        // TODO: Populate table with actual data
        // populateTable(response.data);

    } catch (error) {
        // Error handled by Ajax helper
    }
}

function updateStatistics(stats) {
    $('#totalProducts').text(stats.totalProducts);
    $('#bestSeller').text(stats.bestSeller);
    $('#totalViews').text(stats.totalViews);
    $('#conversionRate').text(stats.conversionRate);
}

function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}
