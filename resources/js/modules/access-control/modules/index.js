import $ from 'jquery';
import Sortable from 'sortablejs';
import Toast from '../../../components/toast.js';
import Ajax from '../../../utils/ajax.js';

let sortableInstance = null;
let hasChanges = false;

document.addEventListener('DOMContentLoaded', function() {
    initSortable();
    initSaveButton();
    initToggleButtons();
    initDeleteButtons();
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
            // Move child rows with their parent
            moveChildrenWithParent();

            hasChanges = true;
            showSaveButton();
            updateOrderNumbers();
        }
    });
}

function moveChildrenWithParent() {
    const tbody = document.getElementById('sortableTable');
    const parentRows = tbody.querySelectorAll('.sortable-row');

    parentRows.forEach(parentRow => {
        const parentId = parentRow.dataset.id;
        const childRows = Array.from(tbody.querySelectorAll(`.child-row[data-parent-id="${parentId}"]`));

        // Move all children right after their parent
        childRows.forEach(childRow => {
            // Remove from current position
            childRow.remove();
            // Insert after the last moved child or parent
            const lastChild = childRows[childRows.indexOf(childRow) - 1] || parentRow;
            lastChild.after(childRow);
        });
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

                // Show success toast
                Toast.showToast(result.message || 'Module order updated successfully!', 'success');

                // Reload page after 1.5 seconds
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                Toast.showToast(result.message || 'Failed to save order', 'error');
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            }
        } catch (error) {
            console.error('Error saving order:', error);
            Toast.showToast('An error occurred while saving order', 'error');
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
    });
}

function initToggleButtons() {
    // Handle toggle active/visible buttons
    document.querySelectorAll('form[action*="toggle-active"], form[action*="toggle-visible"]').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const url = this.action;

            try {
                await Ajax.post(url, null, {
                    loadingMessage: 'Updating module...',
                    successMessage: 'Module updated successfully',
                    onSuccess: () => {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                });
            } catch (error) {
                // Error already handled by Ajax helper
            }
        });
    });
}

function initDeleteButtons() {
    // Handle delete buttons
    document.querySelectorAll('form[action*="/modules/"]').forEach(form => {
        // Only handle forms with DELETE method
        const methodInput = form.querySelector('input[name="_method"][value="DELETE"]');
        if (!methodInput) return;

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Get module name from the row
            const row = this.closest('tr');
            const moduleName = row ? row.querySelector('td:nth-child(4)')?.textContent.trim() : 'this module';

            // Show confirmation dialog
            const confirmed = confirm(`Are you sure you want to delete ${moduleName}?\n\nThis will also delete all child modules.`);

            if (!confirmed) return;

            const url = this.action;

            try {
                await Ajax.delete(url, {
                    loadingMessage: 'Deleting module...',
                    successMessage: 'Module deleted successfully',
                    onSuccess: () => {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                });
            } catch (error) {
                // Error already handled by Ajax helper
            }
        });
    });
}
