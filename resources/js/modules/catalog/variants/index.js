$(document).ready(function() {
    // Search functionality
    $('#searchInput').on('keyup', function() {
        filterTable();
    });

    // Stock filter
    $('#stockFilter').on('change', function() {
        filterTable();
    });

    function filterTable() {
        const searchValue = $('#searchInput').val().toLowerCase();
        const stockFilter = $('#stockFilter').val();

        $('#variantsTable tbody tr').each(function() {
            const $row = $(this);

            // Skip empty state row
            if ($row.find('td').length === 1) {
                return;
            }

            const productName = $row.find('td:eq(0)').text().toLowerCase();
            const sku = $row.find('td:eq(1)').text().toLowerCase();
            const size = $row.find('td:eq(2)').text().toLowerCase();
            const color = $row.find('td:eq(3)').text().toLowerCase();
            const stockBadge = $row.find('td:eq(5) .badge');

            // Search filter
            const matchesSearch = productName.includes(searchValue) ||
                                sku.includes(searchValue) ||
                                size.includes(searchValue) ||
                                color.includes(searchValue);

            // Stock filter
            let matchesStock = true;
            if (stockFilter === 'out') {
                matchesStock = stockBadge.hasClass('badge-error');
            } else if (stockFilter === 'low') {
                matchesStock = stockBadge.hasClass('badge-warning');
            } else if (stockFilter === 'available') {
                matchesStock = stockBadge.hasClass('badge-success');
            }

            // Show/hide row
            if (matchesSearch && matchesStock) {
                $row.show();
            } else {
                $row.hide();
            }
        });

        // Show "no results" message if all rows are hidden
        updateEmptyState();
    }

    function updateEmptyState() {
        const $tbody = $('#variantsTable tbody');
        const visibleRows = $tbody.find('tr:visible').not(':has(td[colspan])').length;

        if (visibleRows === 0) {
            // Show empty state if not already shown
            if ($tbody.find('.empty-state-row').length === 0) {
                $tbody.append(`
                    <tr class="empty-state-row">
                        <td colspan="7" class="text-center py-8">
                            <div class="flex flex-col items-center gap-2 text-base-content/50">
                                <span class="iconify lucide--search-x size-12"></span>
                                <p>No variants match your filters</p>
                            </div>
                        </td>
                    </tr>
                `);
            }
        } else {
            // Remove empty state
            $tbody.find('.empty-state-row').remove();
        }
    }
});
