/**
 * Loading Component
 * Reusable loading overlay for operations
 */

const Loading = {
    overlay: null,

    /**
     * Initialize loading overlay
     */
    init() {
        if (this.overlay) return;

        this.overlay = document.createElement('div');
        this.overlay.id = 'loading-overlay';
        this.overlay.className = 'fixed inset-0 bg-base-300/50 backdrop-blur-sm z-[9999] hidden items-center justify-center';
        this.overlay.innerHTML = `
            <div class="bg-base-100 p-8 rounded-lg shadow-xl flex flex-col items-center gap-4 min-w-64">
                <span class="loading loading-spinner loading-lg text-primary"></span>
                <p class="text-base-content font-medium" id="loading-message">Processing...</p>
            </div>
        `;
        document.body.appendChild(this.overlay);
    },

    /**
     * Show loading overlay
     * @param {string} message - Message to display
     */
    show(message = 'Processing...') {
        this.init();
        const messageEl = document.getElementById('loading-message');
        if (messageEl) {
            messageEl.textContent = message;
        }
        this.overlay.classList.remove('hidden');
        this.overlay.classList.add('flex');
    },

    /**
     * Hide loading overlay
     */
    hide() {
        if (this.overlay) {
            this.overlay.classList.remove('flex');
            this.overlay.classList.add('hidden');
        }
    },

    /**
     * Update loading message
     * @param {string} message - New message to display
     */
    updateMessage(message) {
        const messageEl = document.getElementById('loading-message');
        if (messageEl) {
            messageEl.textContent = message;
        }
    }
};

// Make available globally
window.Loading = Loading;

export default Loading;
