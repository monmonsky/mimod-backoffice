import Ajax from '../../../utils/ajax.js';
import Toast from '../../../components/toast.js';

/* global $ */

document.addEventListener('DOMContentLoaded', function() {
    initFilters();
    initClearLogs();
    initExport();
});

/**
 * Initialize filter form
 */
function initFilters() {
    const $filterForm = $('#filter-form');
    if ($filterForm.length === 0) return;

    $filterForm.on('submit', function(e) {
        e.preventDefault();

        // Show loading overlay
        showLoadingOverlay('Applying filters...');

        // Submit form
        this.submit();
    });

    // Reset filters
    $('#reset-filters').on('click', function() {
        showLoadingOverlay('Resetting filters...');
        setTimeout(() => {
            window.location.href = window.location.pathname;
        }, 100);
    });
}

/**
 * Show loading overlay
 */
function showLoadingOverlay(message = 'Loading...') {
    // Remove existing overlay if any
    const existing = document.getElementById('loading-overlay');
    if (existing) existing.remove();

    // Create overlay
    const overlay = document.createElement('div');
    overlay.id = 'loading-overlay';
    overlay.className = 'fixed inset-0 bg-base-300/50 backdrop-blur-sm flex items-center justify-center z-[9999]';
    overlay.innerHTML = `
        <div class="bg-base-100 rounded-lg p-6 shadow-xl flex flex-col items-center gap-4">
            <span class="loading loading-spinner loading-lg text-primary"></span>
            <p class="text-base-content font-medium">${message}</p>
        </div>
    `;
    document.body.appendChild(overlay);
}

/**
 * Show activity detail modal
 */
window.viewActivityDetail = async function(activityId) {
    try {
        const response = await Ajax.get(`/access-control/user-activities/${activityId}`, {
            loadingMessage: 'Loading activity details...'
        });

        if (response.success) {
            showDetailModal(response.data);
        }
    } catch (error) {
        // Error handled by Ajax helper
    }
};

/**
 * Display activity detail in modal
 */
function showDetailModal(activity) {
    const modal = document.getElementById('activity_detail_modal');
    if (!modal) return;

    // Update user info
    document.getElementById('detail-user').textContent = activity.user_name || '-';
    document.getElementById('detail-email').textContent = activity.user_email || '-';

    // Update action badge with color
    const actionBadge = document.getElementById('detail-action-badge');
    const actionColors = {
        'login': 'badge-success',
        'logout': 'badge-info',
        'create': 'badge-primary',
        'update': 'badge-warning',
        'delete': 'badge-error',
        'view': 'badge-ghost',
        'export': 'badge-secondary',
    };
    const badgeClass = actionColors[activity.action] || 'badge-ghost';
    actionBadge.className = `badge ${badgeClass}`;
    actionBadge.textContent = activity.action || '-';

    // Update description
    document.getElementById('detail-description').textContent = activity.description || '-';

    // Update subject
    const subjectText = activity.subject_type
        ? `${activity.subject_type}${activity.subject_id ? ' #' + activity.subject_id : ''}`
        : '-';
    document.getElementById('detail-subject').textContent = subjectText;

    // Update date
    const date = new Date(activity.created_at);
    const formattedDate = date.toLocaleString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    document.getElementById('detail-date').textContent = formattedDate;

    // Update IP and User Agent
    document.getElementById('detail-ip').textContent = activity.ip_address || '-';
    document.getElementById('detail-user-agent').textContent = activity.user_agent || '-';

    // Properties
    const propertiesContainer = document.getElementById('detail-properties');
    if (activity.properties) {
        try {
            const props = typeof activity.properties === 'string'
                ? JSON.parse(activity.properties)
                : activity.properties;

            propertiesContainer.innerHTML = `<pre class="text-xs font-mono overflow-auto max-h-32">${JSON.stringify(props, null, 2)}</pre>`;
        } catch (e) {
            propertiesContainer.innerHTML = '<p class="text-base-content/60">No additional data</p>';
        }
    } else {
        propertiesContainer.innerHTML = '<p class="text-base-content/60">No additional data</p>';
    }

    // Show modal
    modal.showModal();
}

/**
 * Initialize clear logs functionality
 */
function initClearLogs() {
    const $clearBtn = $('#clear-logs-btn');
    if ($clearBtn.length === 0) return;

    $clearBtn.on('click', async function() {
        if (!confirm('Are you sure you want to clear ALL activity logs? This action cannot be undone!')) {
            return;
        }

        if (!confirm('This will permanently delete all activity history. Are you absolutely sure?')) {
            return;
        }

        try {
            await Ajax.delete('/access-control/user-activities/clear', {
                loadingMessage: 'Clearing all logs...',
                successMessage: 'All activity logs have been cleared',
                onSuccess: () => {
                    setTimeout(() => window.location.reload(), 1000);
                }
            });
        } catch (error) {
            // Error handled by Ajax helper
        }
    });
}

/**
 * Initialize export functionality
 */
function initExport() {
    const $exportBtn = $('#export-logs-btn');
    if ($exportBtn.length === 0) return;

    $exportBtn.on('click', function() {
        // Get current filter parameters
        const params = new URLSearchParams(window.location.search);
        const exportUrl = `/access-control/user-activities/export?${params.toString()}`;

        Toast.showToast('Preparing export...', 'info');

        // Trigger download
        window.location.href = exportUrl;

        setTimeout(() => {
            Toast.showToast('Export started. Download should begin shortly.', 'success');
        }, 500);
    });
}
