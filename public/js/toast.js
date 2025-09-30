/**
 * Toast Notification System
 * Simple, minimalist toast notifications for user feedback
 */

// Add CSS animations if not already present
if (!document.querySelector('#toast-animations')) {
    const style = document.createElement('style');
    style.id = 'toast-animations';
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(20px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);
}

/**
 * Show a toast notification
 * @param {string} message - The message to display
 * @param {string} type - Type of notification: 'success', 'error', 'info', 'warning'
 * @param {number} duration - Duration in milliseconds (default: 4000)
 */
function showToast(message, type = 'success', duration = 4000) {
    // Remove existing toast if any
    const existing = document.querySelector('.custom-toast-container');
    if (existing) existing.remove();

    // Create toast container
    const toastContainer = document.createElement('div');
    toastContainer.className = 'custom-toast-container';
    toastContainer.style.cssText = 'position: fixed; top: 1.5rem; right: 1.5rem; z-index: 99999;';

    // Determine colors and icons based on type
    const config = {
        success: {
            borderColor: 'border-success',
            textColor: 'text-success',
            iconClass: 'lucide--circle-check'
        },
        error: {
            borderColor: 'border-error',
            textColor: 'text-error',
            iconClass: 'lucide--circle-x'
        },
        info: {
            borderColor: 'border-info',
            textColor: 'text-info',
            iconClass: 'lucide--info'
        },
        warning: {
            borderColor: 'border-warning',
            textColor: 'text-warning',
            iconClass: 'lucide--alert-triangle'
        }
    };

    const typeConfig = config[type] || config.success;

    // Build toast HTML
    toastContainer.innerHTML = `
        <div class="bg-base-100 border-l-4 ${typeConfig.borderColor} rounded shadow-md flex items-center gap-3 px-4 py-3 min-w-[300px] max-w-md">
            <span class="iconify ${typeConfig.iconClass} size-5 ${typeConfig.textColor} flex-shrink-0"></span>
            <span class="text-sm text-base-content flex-1">${message}</span>
            <button type="button" class="btn btn-ghost btn-xs btn-square opacity-60 hover:opacity-100" onclick="this.closest('.custom-toast-container').remove()">
                <span class="iconify lucide--x size-4"></span>
            </button>
        </div>
    `;

    // Add to document
    document.body.appendChild(toastContainer);

    // Animate in
    requestAnimationFrame(() => {
        toastContainer.style.animation = 'slideIn 0.2s ease-out';
    });

    // Auto-remove after duration
    setTimeout(() => {
        if (toastContainer.parentElement) {
            toastContainer.style.opacity = '0';
            toastContainer.style.transition = 'opacity 0.2s ease-out';
            setTimeout(() => toastContainer.remove(), 200);
        }
    }, duration);
}

/**
 * Show success toast
 * @param {string} message
 * @param {number} duration
 */
function showSuccess(message, duration = 4000) {
    showToast(message, 'success', duration);
}

/**
 * Show error toast
 * @param {string} message
 * @param {number} duration
 */
function showError(message, duration = 4000) {
    showToast(message, 'error', duration);
}

/**
 * Show info toast
 * @param {string} message
 * @param {number} duration
 */
function showInfo(message, duration = 4000) {
    showToast(message, 'info', duration);
}

/**
 * Show warning toast
 * @param {string} message
 * @param {number} duration
 */
function showWarning(message, duration = 4000) {
    showToast(message, 'warning', duration);
}