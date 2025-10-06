// Save button handler - FIXED VERSION
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

            console.log('[Save Order] Processing', groupHeaders.length, 'groups');

            groupHeaders.forEach((groupHeader) => {
                const groupName = groupHeader.dataset.groupName;

                // Get all parent modules in this group (not child rows, they will be handled separately)
                const modulesInGroup = document.querySelectorAll(`.module-row[data-group-name="${groupName}"]`);

                console.log(`[Save Order] Group: ${groupName}, found ${modulesInGroup.length} modules`);

                modulesInGroup.forEach((moduleRow) => {
                    const moduleId = moduleRow.dataset.moduleId;

                    moduleOrders.push({
                        id: moduleId,
                        sort_order: currentSortOrder
                    });

                    console.log(`[Save Order] - Module ID ${moduleId}: sort_order = ${currentSortOrder}`);

                    currentSortOrder += 10; // Increment by 10 for gap

                    // Also add children of this module with incremental sort order
                    const childRows = document.querySelectorAll(`.child-row[data-parent-id="${moduleId}"]`);
                    childRows.forEach((childRow) => {
                        moduleOrders.push({
                            id: childRow.dataset.moduleId,
                            sort_order: currentSortOrder
                        });
                        console.log(`[Save Order]   - Child ID ${childRow.dataset.moduleId}: sort_order = ${currentSortOrder}`);
                        currentSortOrder += 10;
                    });
                });
            });

            console.log('[Save Order] Total modules to update:', moduleOrders.length);

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

                console.log('[Save Order] Success:', moduleResult.updated_count, 'modules updated');
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
