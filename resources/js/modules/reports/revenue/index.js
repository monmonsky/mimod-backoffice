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
    loadRevenueData();

    // Filter button
    $('#filterBtn').on('click', function() {
        loadRevenueData();
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
            await Ajax.post('/reports/revenue/export', {
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
        $('#revenueTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});

async function loadRevenueData() {
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();

    if (!startDate || !endDate) {
        alert('Please select date range');
        return;
    }

    try {
        // TODO: Replace with actual API endpoint
        // const response = await Ajax.get('/api/reports/revenue', {
        //     params: { start_date: startDate, end_date: endDate },
        //     loadingMessage: 'Loading revenue data...',
        //     showToast: false
        // });

        // Mock data for now
        updateStatistics({
            totalRevenue: 'Rp 125,500,000',
            netRevenue: 'Rp 118,750,000',
            totalDiscounts: 'Rp 6,750,000',
            profitMargin: '28.5%'
        });

        // TODO: Populate table with actual data
        // populateTable(response.data);

    } catch (error) {
        // Error handled by Ajax helper
    }
}

function updateStatistics(stats) {
    $('#totalRevenue').text(stats.totalRevenue);
    $('#netRevenue').text(stats.netRevenue);
    $('#totalDiscounts').text(stats.totalDiscounts);
    $('#profitMargin').text(stats.profitMargin);
}

function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}
