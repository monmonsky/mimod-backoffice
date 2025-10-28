import $ from 'jquery';
import Ajax from '../../../utils/ajax.js';

$(document).ready(function() {
    // Initialize date inputs with current month
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

    $('#startDate').val(formatDate(firstDay));
    $('#endDate').val(formatDate(lastDay));

    // Load initial data
    loadSalesData();

    // Filter button
    $('#filterBtn').on('click', function() {
        loadSalesData();
    });

    // Export button
    $('#exportBtn').on('click', async function() {
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();

        if (!startDate || !endDate) {
            alert('Please select date range');
            return;
        }

        try {
            await Ajax.post('/reports/sales/export', {
                start_date: startDate,
                end_date: endDate
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
        $('#salesTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});

async function loadSalesData() {
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();

    if (!startDate || !endDate) {
        alert('Please select date range');
        return;
    }

    try {
        // TODO: Replace with actual API endpoint
        // const response = await Ajax.get('/api/reports/sales', {
        //     params: { start_date: startDate, end_date: endDate },
        //     loadingMessage: 'Loading sales data...',
        //     showToast: false
        // });

        // Mock data for now
        updateStatistics({
            totalSales: 'Rp 125,500,000',
            totalOrders: '1,234',
            avgOrder: 'Rp 101,700',
            growthRate: '+12.5%'
        });

        // TODO: Populate table with actual data
        // populateTable(response.data);

    } catch (error) {
        // Error handled by Ajax helper
    }
}

function updateStatistics(stats) {
    $('#totalSales').text(stats.totalSales);
    $('#totalOrders').text(stats.totalOrders);
    $('#avgOrder').text(stats.avgOrder);
    $('#growthRate').text(stats.growthRate);
}

function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}
