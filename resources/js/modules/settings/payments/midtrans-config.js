import Toast from "../../../components/toast";
import Ajax from "../../../utils/ajax";

// jQuery is loaded from CDN in blade file
// Initialize when DOM is ready
$(document).ready(function() {
    setupFormHandlers();
    setupTestConnection();
    setupSyncPaymentMethods();

    // Make utility functions available globally for onclick handlers
    window.togglePassword = togglePassword;
    window.copyToClipboard = copyToClipboard;
});

/**
 * Toggle password visibility
 */
function togglePassword(id) {
    const $input = $('#' + id);
    if ($input.attr('type') === 'password') {
        $input.attr('type', 'text');
    } else {
        $input.attr('type', 'password');
    }
}

/**
 * Copy text to clipboard
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        Toast.showToast('Copied to clipboard!', 'success');
    });
}

/**
 * Setup form submission handlers for all forms
 */
function setupFormHandlers() {
    const formIds = ['midtransApiForm', 'midtransMethodsForm', 'midtransTransactionForm'];

    formIds.forEach(formId => {
        const $form = $('#' + formId);
        if ($form.length > 0) {
            $form.on('submit', async function(e) {
                e.preventDefault();
                await handleFormSubmit(this);
            });
        }
    });
}

/**
 * Generic form submission handler
 */
async function handleFormSubmit(form) {
    const $form = $(form);
    const formData = new FormData(form);

    try {
        await Ajax.post($form.attr('action'), formData, {
            loadingMessage: 'Saving settings...',
            successMessage: 'Settings saved successfully!'
        });
    } catch (error) {
        console.error('Error:', error);
    }
}

/**
 * Setup test Midtrans connection button
 */
function setupTestConnection() {
    const $testBtn = $('#testMidtransBtn');

    if ($testBtn.length === 0) return;

    $testBtn.on('click', async function() {
        try {
            const data = await Ajax.post(window.testMidtransUrl, {}, {
                loadingMessage: 'Testing connection...',
                successMessage: 'Connection test successful!',
                errorMessage: 'Connection test failed'
            });

            // Show request and response details in modal
            if (data.request || data.response) {
                showTestResultModal(data);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });
}

/**
 * Setup sync payment methods button
 */
function setupSyncPaymentMethods() {
    const $syncBtn = $('#syncPaymentMethodsBtn');

    if ($syncBtn.length === 0) return;

    $syncBtn.on('click', async function() {
        try {
            const data = await Ajax.post(window.syncMidtransUrl, {}, {
                loadingMessage: 'Syncing payment methods...',
                showToast: false, // We'll show custom toast with count
                errorMessage: 'Sync failed'
            });

            // Show custom success toast with count
            const methodsCount = data.data?.total_methods || 0;
            Toast.showToast(data.message + ` (${methodsCount} methods)`, 'success');

            // Show sync details in modal
            if (data.data) {
                showSyncResultModal(data);
            }

            // Reload page after 2 seconds to show updated timestamp
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } catch (error) {
            console.error('Error:', error);
        }
    });
}

/**
 * Show test result modal with request and response details
 */
function showTestResultModal(data) {
    // Remove existing modal if any
    $('#testResultModal').remove();

    const modalHtml = `
        <dialog id="testResultModal" class="modal">
            <div class="modal-box max-w-4xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-lg">API Test Result</h3>
                    <form method="dialog">
                        <button class="btn btn-sm btn-ghost btn-circle">
                            <span class="iconify lucide--x size-4"></span>
                        </button>
                    </form>
                </div>

                <div class="space-y-4">
                    <!-- Status -->
                    <div class="alert ${data.success ? 'alert-success' : 'alert-error'}">
                        <span class="iconify ${data.success ? 'lucide--check-circle' : 'lucide--x-circle'} size-5"></span>
                        <span>${data.message}</span>
                    </div>

                    ${data.environment ? `
                    <div class="flex gap-2 items-center text-sm">
                        <span class="badge badge-primary">${data.environment}</span>
                        ${data.http_code ? `<span class="badge badge-outline">HTTP ${data.http_code}</span>` : ''}
                    </div>
                    ` : ''}

                    <!-- Request Details -->
                    ${data.request ? `
                    <div>
                        <h4 class="font-semibold mb-2 flex items-center gap-2">
                            <span class="iconify lucide--arrow-up-right size-4"></span>
                            Request
                        </h4>
                        <div class="bg-base-200 rounded-lg p-4 overflow-x-auto">
                            <pre class="text-xs"><code>${JSON.stringify(data.request, null, 2)}</code></pre>
                        </div>
                    </div>
                    ` : ''}

                    <!-- Response Details -->
                    ${data.response ? `
                    <div>
                        <h4 class="font-semibold mb-2 flex items-center gap-2">
                            <span class="iconify lucide--arrow-down-left size-4"></span>
                            Response
                        </h4>
                        <div class="bg-base-200 rounded-lg p-4 overflow-x-auto">
                            <pre class="text-xs"><code>${JSON.stringify(data.response, null, 2)}</code></pre>
                        </div>
                    </div>
                    ` : ''}
                </div>

                <div class="modal-action">
                    <form method="dialog">
                        <button class="btn">Close</button>
                    </form>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>
    `;

    $('body').append(modalHtml);
    $('#testResultModal')[0].showModal();
}

/**
 * Show sync result modal with payment methods details
 */
function showSyncResultModal(data) {
    // Remove existing modal if any
    $('#syncResultModal').remove();

    // Group methods by category
    const methodsByCategory = {};
    if (data.data?.methods) {
        Object.values(data.data.methods).forEach(method => {
            const category = method.category || 'other';
            if (!methodsByCategory[category]) {
                methodsByCategory[category] = [];
            }
            methodsByCategory[category].push(method);
        });
    }

    // Create category display
    let categoriesHtml = '';
    const categoryNames = {
        'card': 'Card Payment',
        'ewallet': 'E-Wallet',
        'bank_transfer': 'Bank Transfer',
        'qris': 'QRIS',
        'over_the_counter': 'Over The Counter',
        'paylater': 'Pay Later',
        'other': 'Other'
    };

    for (const [category, methods] of Object.entries(methodsByCategory)) {
        const categoryName = categoryNames[category] || category;
        categoriesHtml += `
            <div>
                <h5 class="font-semibold mb-2">${categoryName}</h5>
                <div class="space-y-1">
                    ${methods.map(method => `
                        <div class="flex items-center gap-2 text-sm">
                            <span class="iconify ${method.available ? 'lucide--check-circle text-success' : 'lucide--x-circle text-error'} size-4"></span>
                            <span>${method.name}</span>
                            ${method.description ? `<span class="text-xs text-base-content/60">(${method.description})</span>` : ''}
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    const modalHtml = `
        <dialog id="syncResultModal" class="modal">
            <div class="modal-box max-w-4xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-lg">Sync Result</h3>
                    <form method="dialog">
                        <button class="btn btn-sm btn-ghost btn-circle">
                            <span class="iconify lucide--x size-4"></span>
                        </button>
                    </form>
                </div>

                <div class="space-y-4">
                    <div class="alert alert-success">
                        <span class="iconify lucide--check-circle size-5"></span>
                        <span>${data.message}</span>
                    </div>

                    <div class="stats shadow w-full">
                        <div class="stat">
                            <div class="stat-title">Total Methods</div>
                            <div class="stat-value">${data.data?.total_methods || 0}</div>
                            <div class="stat-desc">Synced from Midtrans</div>
                        </div>
                        <div class="stat">
                            <div class="stat-title">Last Sync</div>
                            <div class="stat-value text-sm">${data.data?.last_sync || '-'}</div>
                        </div>
                    </div>

                    ${data.note ? `
                    <div class="alert alert-info">
                        <span class="iconify lucide--info size-5"></span>
                        <span>${data.note}</span>
                    </div>
                    ` : ''}

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        ${categoriesHtml}
                    </div>
                </div>

                <div class="modal-action">
                    <form method="dialog">
                        <button class="btn">Close</button>
                    </form>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>
    `;

    $('body').append(modalHtml);
    $('#syncResultModal')[0].showModal();
}
