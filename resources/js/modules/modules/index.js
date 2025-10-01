import Sortable from 'sortablejs';

let sortableInstance = null;
let hasChanges = false;

document.addEventListener('DOMContentLoaded', function() {
    initSortable();
    initSaveButton();
});

function initSortable() {
    const tbody = document.getElementById('sortableTable');

    if (!tbody) return;

    sortableInstance = new Sortable(tbody, {
        animation: 150,
        handle: '.cursor-move',
        filter: '.child-row',
        draggable: '.sortable-row',
        ghostClass: 'bg-base-300',
        chosenClass: 'bg-primary/10',
        onEnd: function(evt) {
            hasChanges = true;
            showSaveButton();
            updateOrderNumbers();
        }
    });
}

function updateOrderNumbers() {
    const rows = document.querySelectorAll('#sortableTable tr.sortable-row');
    rows.forEach((row, index) => {
        const badge = row.querySelector('td:nth-child(7) .badge');
        if (badge) {
            badge.textContent = index + 1;
        }
    });
}

function showSaveButton() {
    const container = document.getElementById('saveOrderContainer');
    if (container) {
        container.classList.remove('hidden');
    }
}

function hideSaveButton() {
    const container = document.getElementById('saveOrderContainer');
    if (container) {
        container.classList.add('hidden');
    }
}

function initSaveButton() {
    const saveBtn = document.getElementById('saveOrderBtn');

    if (!saveBtn) return;

    saveBtn.addEventListener('click', async function() {
        if (!hasChanges) return;

        const originalText = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Saving...';

        try {
            // Get new order
            const rows = document.querySelectorAll('#sortableTable tr.sortable-row');
            const order = [];

            rows.forEach((row, index) => {
                order.push({
                    id: row.dataset.id,
                    sort_order: index + 1
                });
            });

            const response = await fetch('/access-control/modules/update-order', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ order })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                hasChanges = false;
                hideSaveButton();

                // Show success message temporarily
                const alertDiv = document.querySelector('#saveOrderContainer .alert');
                if (alertDiv) {
                    alertDiv.classList.remove('alert-warning');
                    alertDiv.classList.add('alert-success');
                    alertDiv.querySelector('span:nth-child(2)').textContent = result.message || 'Order saved successfully!';
                    alertDiv.querySelector('button').classList.add('hidden');
                }

                // Reload page after 1 second
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                alert(result.message || 'Failed to save order');
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            }
        } catch (error) {
            console.error('Error saving order:', error);
            alert('An error occurred while saving order');
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
    });
}
