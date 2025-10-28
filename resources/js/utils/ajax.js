import $ from 'jquery';
import Toast from '../components/toast.js';

/**
 * Global AJAX Helper
 * Provides consistent AJAX operations with built-in loading, toast, and error handling
 */
class AjaxHelper {
    constructor() {
        this.loadingOverlay = null;
        this.defaultOptions = {
            showLoading: true,
            showToast: true,
            loadingTarget: null, // jQuery selector or element
            loadingMessage: 'Processing...', // Message for global loading overlay
            useGlobalLoading: true, // Use full-screen loading overlay by default
            successMessage: null,
            errorMessage: null,
            timeout: 30000, // 30 seconds
            onSuccess: null,
            onError: null,
            onComplete: null
        };
        this.initLoadingOverlay();
    }

    /**
     * Initialize global loading overlay
     */
    initLoadingOverlay() {
        this.loadingOverlay = $(`
            <div id="global-loading" class="fixed inset-0 bg-base-300/50 backdrop-blur-sm z-[9999] hidden items-center justify-center">
                <div class="bg-base-100 p-8 rounded-lg shadow-xl flex flex-col items-center gap-4">
                    <span class="loading loading-spinner loading-lg text-primary"></span>
                    <p class="text-base-content font-medium" id="loading-text">Processing...</p>
                </div>
            </div>
        `);
        $('body').append(this.loadingOverlay);
    }

    /**
     * Show global loading overlay
     */
    showGlobalLoading(message = 'Processing...') {
        $('#loading-text').text(message);
        this.loadingOverlay.removeClass('hidden').addClass('flex');
    }

    /**
     * Hide global loading overlay
     */
    hideGlobalLoading() {
        this.loadingOverlay.removeClass('flex').addClass('hidden');
    }

    /**
     * Get CSRF token from meta tag
     */
    getCsrfToken() {
        return $('meta[name="csrf-token"]').attr('content');
    }

    /**
     * Show loading state on target element
     */
    showLoading(target, originalContent = null) {
        if (!target) return null;

        const $target = $(target);
        const original = originalContent || $target.html();

        if ($target.is('button')) {
            $target.prop('disabled', true)
                .data('original-html', original)
                .html('<span class="loading loading-spinner loading-xs"></span> Loading...');
        } else if ($target.is('input[type="checkbox"]')) {
            $target.prop('disabled', true);
        }

        // Add opacity to closest row if exists
        const $row = $target.closest('tr');
        if ($row.length) {
            $row.css('opacity', '0.6').data('original-opacity', $row.css('opacity'));
        }

        return original;
    }

    /**
     * Hide loading state on target element
     */
    hideLoading(target, originalContent = null) {
        if (!target) return;

        const $target = $(target);
        const original = originalContent || $target.data('original-html');

        if ($target.is('button') && original) {
            $target.prop('disabled', false).html(original);
        } else if ($target.is('input[type="checkbox"]')) {
            $target.prop('disabled', false);
        }

        // Restore row opacity
        const $row = $target.closest('tr');
        if ($row.length) {
            const originalOpacity = $row.data('original-opacity') || '1';
            $row.css('opacity', originalOpacity);
        }
    }

    /**
     * Handle AJAX error
     */
    handleError(xhr, options) {
        let errorMessage = options.errorMessage;

        // Try to get error message from response
        if (xhr.responseJSON) {
            if (xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseJSON.errors) {
                // Validation errors
                const errors = Object.values(xhr.responseJSON.errors).flat();
                errorMessage = errors.join(', ');
            }
        }

        // Fallback error message
        if (!errorMessage) {
            if (xhr.status === 0) {
                errorMessage = 'Network error. Please check your connection.';
            } else if (xhr.status === 404) {
                errorMessage = 'Resource not found.';
            } else if (xhr.status === 500) {
                errorMessage = 'Server error. Please try again later.';
            } else {
                errorMessage = 'An error occurred. Please try again.';
            }
        }

        if (options.showToast) {
            Toast.showToast(errorMessage, 'error', 5000);
        }

        if (options.onError) {
            options.onError(xhr, errorMessage);
        }

        return errorMessage;
    }

    /**
     * Core request method
     */
    async request(method, url, data = null, userOptions = {}) {
        const options = { ...this.defaultOptions, ...userOptions };
        let originalContent = null;

        try {
            // Show loading (global or target-specific)
            if (options.showLoading) {
                if (options.useGlobalLoading) {
                    this.showGlobalLoading(options.loadingMessage);
                } else if (options.loadingTarget) {
                    originalContent = this.showLoading(options.loadingTarget);
                }
            }

            // Prepare AJAX settings
            const ajaxSettings = {
                url,
                method,
                headers: {
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                timeout: options.timeout
            };

            // Add data based on method
            if (data) {
                if (data instanceof FormData) {
                    ajaxSettings.data = data;
                    ajaxSettings.processData = false;
                    ajaxSettings.contentType = false;
                } else {
                    ajaxSettings.data = data;
                    ajaxSettings.contentType = 'application/json';
                    ajaxSettings.dataType = 'json';
                    if (typeof data === 'object') {
                        ajaxSettings.data = JSON.stringify(data);
                    }
                }
            }

            // Make request
            const response = await $.ajax(ajaxSettings);

            // Handle success
            if (options.showToast && (options.successMessage || response.message)) {
                Toast.showToast(options.successMessage || response.message, 'success');
            }

            if (options.onSuccess) {
                options.onSuccess(response);
            }

            return response;

        } catch (xhr) {
            // Handle error
            this.handleError(xhr, options);
            throw xhr;

        } finally {
            // Hide loading (global or target-specific)
            if (options.showLoading) {
                if (options.useGlobalLoading) {
                    this.hideGlobalLoading();
                } else if (options.loadingTarget) {
                    this.hideLoading(options.loadingTarget, originalContent);
                }
            }

            // Call complete callback
            if (options.onComplete) {
                options.onComplete();
            }
        }
    }

    /**
     * GET request
     */
    async get(url, options = {}) {
        return this.request('GET', url, null, options);
    }

    /**
     * POST request (Create)
     */
    async post(url, data, options = {}) {
        return this.request('POST', url, data, options);
    }

    /**
     * PUT request (Update)
     */
    async put(url, data, options = {}) {
        return this.request('PUT', url, data, options);
    }

    /**
     * PATCH request (Partial Update)
     */
    async patch(url, data, options = {}) {
        return this.request('PATCH', url, data, options);
    }

    /**
     * DELETE request
     */
    async delete(url, options = {}) {
        return this.request('DELETE', url, null, options);
    }

    /**
     * Convenience method for create operations
     */
    async create(url, data, options = {}) {
        return this.post(url, data, {
            successMessage: 'Created successfully',
            errorMessage: 'Failed to create',
            ...options
        });
    }

    /**
     * Convenience method for update operations
     */
    async update(url, data, options = {}) {
        return this.put(url, data, {
            successMessage: 'Updated successfully',
            errorMessage: 'Failed to update',
            ...options
        });
    }

    /**
     * Convenience method for destroy operations
     */
    async destroy(url, options = {}) {
        return this.delete(url, {
            successMessage: 'Deleted successfully',
            errorMessage: 'Failed to delete',
            ...options
        });
    }
}

// Export singleton instance
const Ajax = new AjaxHelper();
export default Ajax;
