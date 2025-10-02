import $ from 'jquery';
import Ajax from '../../../utils/ajax.js';

$(document).ready(function() {
    // Toggle active status
    $('.toggle-form').on('submit', async function(e) {
        e.preventDefault();

        const $form = $(this);
        const url = $form.attr('action');

        try {
            await Ajax.post(url, null, {
                loadingMessage: 'Updating status...',
                successMessage: 'Role status updated successfully',
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

    // Delete form handler
    $('.delete-form').on('submit', async function(e) {
        e.preventDefault();

        const confirmed = confirm('Are you sure you want to delete this role? This action cannot be undone.');
        if (!confirmed) return;

        const $form = $(this);
        const url = $form.attr('action');

        try {
            await Ajax.delete(url, {
                loadingMessage: 'Deleting role...',
                successMessage: 'Role deleted successfully',
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

// Show role detail modal
window.showRoleDetail = async function(roleId) {
    const modal = document.getElementById('roleDetailModal');
    const $modalLoading = $('#modalLoading');
    const $modalContent = $('#modalContent');

    // Show modal and loading
    modal.showModal();
    $modalLoading.removeClass('hidden');
    $modalContent.addClass('hidden');

    try {
        const response = await Ajax.get(`/access-control/role/${roleId}/detail`, {
            loadingMessage: 'Loading role details...',
            showToast: false
        });

        const role = response.data;

        // Populate role info
        $('#roleName').text(role.name);
        $('#roleDisplayName').text(role.display_name);
        $('#rolePriority').text(role.priority);
        $('#roleDescription').text(role.description || '-');

        // Status badge
        const statusBadge = role.is_active
            ? '<span class="badge badge-sm badge-success">Active</span>'
            : '<span class="badge badge-sm badge-error">Inactive</span>';
        $('#roleStatus').html(statusBadge);

        // System badge
        if (role.is_system) {
            $('#roleStatus').append(' <span class="badge badge-sm badge-warning ml-2">System</span>');
        }

        // Populate permissions
        $('#permissionCount').text(role.permissions.length);

        if (role.permissions.length > 0) {
            // Group permissions by module and action
            const permissionTree = {};

            role.permissions.forEach(permission => {
                // Parse permission name: "module.resource.action" or "module.action"
                const parts = permission.name.split('.');
                const module = permission.module || parts[0] || 'Other';

                if (!permissionTree[module]) {
                    permissionTree[module] = {};
                }

                // Group by resource if exists (e.g., "access-control.users.view" -> users)
                if (parts.length >= 3) {
                    const resource = parts.slice(1, -1).join('.');
                    const action = parts[parts.length - 1];

                    if (!permissionTree[module][resource]) {
                        permissionTree[module][resource] = [];
                    }
                    permissionTree[module][resource].push({
                        action: action,
                        display: permission.display_name || permission.name
                    });
                } else {
                    // Simple permission without resource
                    if (!permissionTree[module]['_direct']) {
                        permissionTree[module]['_direct'] = [];
                    }
                    permissionTree[module]['_direct'].push({
                        action: parts[parts.length - 1] || permission.name,
                        display: permission.display_name || permission.name
                    });
                }
            });

            // Build tree HTML
            let permissionsHtml = '<div class="space-y-4">';

            Object.keys(permissionTree).sort().forEach(module => {
                permissionsHtml += `
                    <div class="border border-base-300 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="iconify lucide--folder text-primary size-5"></span>
                            <h5 class="font-semibold text-base text-primary capitalize">${module.replace(/-/g, ' ')}</h5>
                        </div>
                        <div class="ml-7 space-y-2">
                `;

                const resources = permissionTree[module];
                Object.keys(resources).sort().forEach(resource => {
                    if (resource === '_direct') {
                        // Direct permissions (no resource grouping)
                        resources[resource].forEach(perm => {
                            permissionsHtml += `
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="iconify lucide--check-circle text-success size-4"></span>
                                    <span class="text-base-content/80">${perm.display}</span>
                                </div>
                            `;
                        });
                    } else {
                        // Grouped by resource
                        permissionsHtml += `
                            <div class="mb-2">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="iconify lucide--file-text text-info size-4"></span>
                                    <span class="font-medium text-sm capitalize">${resource.replace(/-/g, ' ')}</span>
                                </div>
                                <div class="ml-6 space-y-1">
                        `;

                        resources[resource].forEach(perm => {
                            permissionsHtml += `
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="iconify lucide--dot size-3 text-base-content/40"></span>
                                    <span class="text-base-content/70 capitalize">${perm.action.replace(/-/g, ' ')}</span>
                                </div>
                            `;
                        });

                        permissionsHtml += '</div></div>';
                    }
                });

                permissionsHtml += '</div></div>';
            });

            permissionsHtml += '</div>';
            $('#permissionsList').html(permissionsHtml);
        } else {
            $('#permissionsList').html('<p class="text-base-content/60 text-sm">No permissions assigned</p>');
        }

        // Hide loading, show content
        $modalLoading.addClass('hidden');
        $modalContent.removeClass('hidden');

    } catch (error) {
        modal.close();
        // Error already handled by Ajax helper
    }
};
