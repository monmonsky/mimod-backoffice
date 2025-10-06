import $ from 'jquery';
import Sortable from 'sortablejs';
import Toast from '../../../components/toast.js';
import Ajax from '../../../utils/ajax.js';

let sortableInstance = null;
let moduleSortableInstances = {};
let hasChanges = false;

document.addEventListener('DOMContentLoaded', function() {
    initSortable();
    initSaveButton();
    initToggleButtons();
    initDeleteButtons();
    initGroupCollapse();
    initModuleCollapse();
    initCollapseAllButton();
});

function initSortable() {
    const tbody = document.getElementById('sortableTable');

    if (!tbody) return;

    sortableInstance = new Sortable(tbody, {
        animation: 150,
        handle: '.group-drag-handle', // Only drag groups by their handle
        draggable: '.group-header',
        filter: '.module-row, .child-row', // Don't allow dragging modules when group sortable is active
        ghostClass: 'bg-base-300',
        chosenClass: 'bg-primary/10',
        onEnd: function(evt) {
            const draggedRow = evt.item;
            const groupName = draggedRow.dataset.groupName;

            // Move all modules in this group along with the group header
            const allModuleRows = document.querySelectorAll(`.module-row[data-group-name="${groupName}"], .child-row[data-group-name="${groupName}"]`);

            // Insert all module rows right after the group header
            let insertAfter = draggedRow;
            allModuleRows.forEach(moduleRow => {
                moduleRow.remove();
                insertAfter.after(moduleRow);
                insertAfter = moduleRow;
            });

            hasChanges = true;
            showSaveButton();
        }
    });
}

function updateOrderNumbers() {
    const rows = document.querySelectorAll('#sortableTable tr.sortable-row');
    rows.forEach((row, index) => {
        const badge = row.querySelector('td:nth-child(5) .badge');
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
            // Get order based on GROUP HEADERS position, then modules within each group
            const groupHeaders = document.querySelectorAll('.group-header');
            const moduleOrders = [];
            let currentSortOrder = 10;

            groupHeaders.forEach((groupHeader) => {
                const groupName = groupHeader.dataset.groupName;

                // Get all modules in this group (regardless of visibility)
                const modulesInGroup = document.querySelectorAll(`.sortable-module[data-group-name="${groupName}"]`);

                modulesInGroup.forEach((moduleRow) => {
                    moduleOrders.push({
                        id: moduleRow.dataset.moduleId,
                        sort_order: currentSortOrder
                    });

                    currentSortOrder += 10; // Increment by 10 for gap
                });
            });

            // Update module order
            if (moduleOrders.length > 0) {
                const moduleResponse = await fetch('/access-control/modules/update-order', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ order: moduleOrders })
                });

                const moduleResult = await moduleResponse.json();

                if (!moduleResponse.ok || !moduleResult.success) {
                    throw new Error(moduleResult.message || 'Failed to save module order');
                }
            }

            hasChanges = false;
            hideSaveButton();

            // Show success toast
            Toast.showToast('Module order updated successfully!', 'success');

            // Reload page after 1.5 seconds
            setTimeout(() => {
                window.location.reload();
            }, 1500);

        } catch (error) {
            console.error('Error saving order:', error);
            Toast.showToast(error.message || 'An error occurred while saving order', 'error');
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

function initGroupCollapse() {
    // Handle group header clicks to expand/collapse modules
    document.querySelectorAll('.group-header').forEach(groupRow => {
        groupRow.addEventListener('click', function(e) {
            // Don't trigger if clicking on drag handle or action buttons
            if (e.target.closest('.group-drag-handle') || e.target.closest('button')) {
                return;
            }

            const groupName = this.dataset.groupName;
            const moduleRows = document.querySelectorAll(`.module-row[data-group-name="${groupName}"]`);
            const chevron = this.querySelector('.group-chevron');

            // Check if currently expanded
            const isExpanded = !moduleRows[0]?.classList.contains('hidden');

            if (isExpanded) {
                // Collapse: hide all modules and their children
                moduleRows.forEach(row => {
                    row.classList.add('hidden');
                    const moduleId = row.dataset.moduleId;
                    const childRows = document.querySelectorAll(`.child-row[data-parent-id="${moduleId}"]`);
                    childRows.forEach(child => child.classList.add('hidden'));

                    // Reset module chevron
                    const moduleChevron = row.querySelector('.module-chevron');
                    if (moduleChevron) {
                        moduleChevron.style.transform = 'rotate(0deg)';
                    }
                });
                chevron.style.transform = 'rotate(0deg)';

                // Destroy sortable instance for this group
                if (moduleSortableInstances[groupName]) {
                    moduleSortableInstances[groupName].destroy();
                    delete moduleSortableInstances[groupName];
                }

                // Re-enable group sortable if all groups are collapsed
                const hasExpandedGroups = document.querySelectorAll('.module-row:not(.hidden)').length > 0;
                if (!hasExpandedGroups && sortableInstance) {
                    sortableInstance.option('disabled', false);
                }
            } else {
                // Expand: show all parent modules (children remain hidden until parent is clicked)
                moduleRows.forEach(row => row.classList.remove('hidden'));
                chevron.style.transform = 'rotate(90deg)';

                // Disable group sortable and enable module sortable
                if (sortableInstance) {
                    sortableInstance.option('disabled', true);
                }

                // Destroy all other module sortable instances and initialize only for this group
                // This prevents conflicts when multiple groups are open
                initModuleSortable(groupName);
            }
        });
    });
}

function initModuleSortable(groupName) {
    // Get all module rows for this group
    const moduleRows = document.querySelectorAll(`.module-row[data-group-name="${groupName}"]`);

    console.log(`[Module Sortable] Init for group: ${groupName}, found ${moduleRows.length} modules`);

    if (moduleRows.length <= 1) {
        console.log(`[Module Sortable] Skipped - only ${moduleRows.length} module(s)`);
        return; // No need to sort if only 1 module
    }

    const tbody = document.getElementById('sortableTable');

    // Destroy ALL existing sortable instances first to avoid conflicts
    Object.keys(moduleSortableInstances).forEach(key => {
        if (moduleSortableInstances[key] && key !== groupName) {
            console.log(`[Module Sortable] Destroying instance: ${key}`);
            moduleSortableInstances[key].destroy();
            delete moduleSortableInstances[key];
        }
    });

    // Destroy existing instance for this group if any
    if (moduleSortableInstances[groupName]) {
        moduleSortableInstances[groupName].destroy();
        delete moduleSortableInstances[groupName];
    }

    console.log(`[Module Sortable] Creating sortable for: ${groupName}`);

    // Debug: Check if draggable elements exist and have handles
    const draggableElements = document.querySelectorAll(`.sortable-module[data-group-name="${groupName}"]`);
    console.log(`[Module Sortable] Found ${draggableElements.length} draggable elements`);
    draggableElements.forEach((el, idx) => {
        const handle = el.querySelector('.module-drag-handle');
        console.log(`[Module Sortable] Element ${idx}: has handle = ${!!handle}`, el);
    });

    // Create new sortable instance ONLY for this group
    moduleSortableInstances[groupName] = new Sortable(tbody, {
        animation: 150,
        handle: '.module-drag-handle',
        draggable: `.sortable-module[data-group-name="${groupName}"]`,
        filter: '.child-row, .group-header',
        ghostClass: 'bg-base-300',
        chosenClass: 'bg-primary/10',
        onStart: function(evt) {
            console.log('[Module Sortable] Drag started', evt.item);
        },
        onMove: function(evt) {
            // Only allow moving within same group
            const draggedGroup = evt.dragged.dataset.groupName;
            const relatedGroup = evt.related.dataset.groupName;

            // Don't allow moving to different groups or group headers
            if (evt.related.classList.contains('group-header')) {
                return false;
            }

            if (evt.related.classList.contains('child-row')) {
                return false;
            }

            // Must be same group
            if (relatedGroup && draggedGroup !== relatedGroup) {
                return false;
            }

            return true;
        },
        onEnd: function(evt) {
            const draggedRow = evt.item;
            const draggedModuleId = draggedRow.dataset.moduleId;

            // Move children with parent to maintain hierarchy
            const childRows = document.querySelectorAll(`.child-row[data-parent-id="${draggedModuleId}"]`);
            if (childRows.length > 0) {
                // Insert children right after the parent
                let insertAfter = draggedRow;
                childRows.forEach(child => {
                    // Remove child from current position
                    const currentParent = child.parentNode;
                    if (currentParent) {
                        child.remove();
                    }
                    // Insert after last inserted child or parent
                    insertAfter.after(child);
                    insertAfter = child;
                });
            }

            hasChanges = true;
            showSaveButton();
            updateModuleOrderNumbers(groupName);
        }
    });
}

function updateModuleOrderNumbers(groupName) {
    const moduleRows = document.querySelectorAll(`.sortable-module[data-group-name="${groupName}"]`);
    moduleRows.forEach((row, index) => {
        const badge = row.querySelector('td:nth-child(6) .badge');
        if (badge) {
            // Update display only, actual order will be saved on save button click
            badge.textContent = (index + 1) * 10;
        }
    });
}

function initModuleCollapse() {
    // Handle parent module row clicks to expand/collapse children
    document.querySelectorAll('.module-row[data-is-parent="true"]').forEach(moduleRow => {
        moduleRow.addEventListener('click', function(e) {
            // Don't trigger if clicking on action buttons, forms, or drag handle
            if (e.target.closest('button') ||
                e.target.closest('form') ||
                e.target.closest('a') ||
                e.target.closest('.module-drag-handle')) {
                return;
            }

            const moduleId = this.dataset.moduleId;
            const childRows = document.querySelectorAll(`.child-row[data-parent-id="${moduleId}"]`);
            const chevron = this.querySelector('.module-chevron');

            if (childRows.length === 0) return;

            // Check if currently expanded
            const isExpanded = !childRows[0].classList.contains('hidden');

            if (isExpanded) {
                // Collapse
                childRows.forEach(child => child.classList.add('hidden'));
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            } else {
                // Expand
                childRows.forEach(child => child.classList.remove('hidden'));
                if (chevron) chevron.style.transform = 'rotate(90deg)';
            }
        });
    });
}

function initCollapseAllButton() {
    const collapseAllBtn = document.getElementById('collapseAllBtn');

    if (!collapseAllBtn) {
        console.warn('Collapse All button not found');
        return;
    }

    collapseAllBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        // Get all rows and chevrons
        const allModuleRows = document.querySelectorAll('.module-row');
        const allChildRows = document.querySelectorAll('.child-row');
        const allGroupChevrons = document.querySelectorAll('.group-chevron');
        const allModuleChevrons = document.querySelectorAll('.module-chevron');

        // Check if any module row is visible (expanded)
        const hasExpandedGroups = Array.from(allModuleRows).some(row => !row.classList.contains('hidden'));

        if (hasExpandedGroups) {
            // COLLAPSE ALL
            // Hide all module and child rows
            allModuleRows.forEach(row => row.classList.add('hidden'));
            allChildRows.forEach(row => row.classList.add('hidden'));

            // Reset all chevrons to collapsed state (0deg)
            allGroupChevrons.forEach(chevron => {
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            });
            allModuleChevrons.forEach(chevron => {
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            });

            // Destroy all module sortable instances
            Object.keys(moduleSortableInstances).forEach(groupName => {
                if (moduleSortableInstances[groupName]) {
                    moduleSortableInstances[groupName].destroy();
                    delete moduleSortableInstances[groupName];
                }
            });

            // Update button text and icon
            const btnIcon = collapseAllBtn.querySelector('.iconify');
            const btnText = collapseAllBtn.childNodes[collapseAllBtn.childNodes.length - 1];
            if (btnIcon) btnIcon.classList.replace('lucide--chevrons-up', 'lucide--chevrons-down');
            if (btnText) btnText.textContent = ' Expand All';

            Toast.showToast('All groups collapsed', 'info');
        } else {
            // EXPAND ALL
            // Show all module rows AND their children
            allModuleRows.forEach(row => {
                row.classList.remove('hidden');
            });

            // Show all children rows too
            allChildRows.forEach(row => {
                row.classList.remove('hidden');
            });

            // Rotate all group chevrons to expanded state (90deg)
            allGroupChevrons.forEach(chevron => {
                if (chevron) chevron.style.transform = 'rotate(90deg)';
            });

            // Rotate all module chevrons to expanded state (90deg)
            allModuleChevrons.forEach(chevron => {
                if (chevron) chevron.style.transform = 'rotate(90deg)';
            });

            // Destroy all existing sortable instances first
            Object.keys(moduleSortableInstances).forEach(key => {
                if (moduleSortableInstances[key]) {
                    moduleSortableInstances[key].destroy();
                    delete moduleSortableInstances[key];
                }
            });

            // Note: When expand all, we DON'T create sortable instances
            // User must click on individual group to enable drag & drop for that group
            // This prevents conflicts from multiple active sortable instances

            // Update button text and icon
            const btnIcon = collapseAllBtn.querySelector('.iconify');
            const btnText = collapseAllBtn.childNodes[collapseAllBtn.childNodes.length - 1];
            if (btnIcon) btnIcon.classList.replace('lucide--chevrons-down', 'lucide--chevrons-up');
            if (btnText) btnText.textContent = ' Collapse All';

            Toast.showToast('All groups expanded (click group to enable drag & drop)', 'info');
        }
    });
}
