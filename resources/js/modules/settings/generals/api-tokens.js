import Ajax from '../../../utils/ajax.js';
import Toast from '../../../components/toast.js';

// jQuery is loaded from CDN
/* global $ */

$(document).ready(function() {
    setupFormHandler();
});

/**
 * Show generate token modal
 */
window.showGenerateModal = function() {
    document.getElementById('generateTokenModal').showModal();
};

/**
 * Setup form submission handler
 */
function setupFormHandler() {
    const $form = $('#generateTokenForm');

    if ($form.length === 0) {
        console.error('Form #generateTokenForm not found!');
        return;
    }

    $form.on('submit', async function(e) {
        e.preventDefault();
        await handleGenerateToken($form);
    });
}

/**
 * Handle generate token
 */
async function handleGenerateToken($form) {
    const formData = new FormData($form[0]);

    // Get ability type
    const abilityType = $('input[name="ability_type"]:checked').val();
    const abilities = abilityType === 'readonly' ? ['settings:read'] : ['*'];

    // Build request data
    const requestData = {
        token_name: formData.get('token_name'),
        abilities: abilities,
    };

    try {
        const result = await Ajax.post('/settings/generals/api-tokens/generate', requestData, {
            loadingMessage: 'Generating token...',
            successMessage: 'Token generated successfully',
            useGlobalLoading: false // Use custom flow for modal
        });

        // Close generate modal
        document.getElementById('generateTokenModal').close();

        // Show success modal with token
        showTokenSuccessModal(result.data.token);

        // Reset form
        $form[0].reset();

        // Reload page after 5 seconds
        setTimeout(() => {
            window.location.reload();
        }, 5000);
    } catch (error) {
        // Error already handled by Ajax helper
    }
}

/**
 * Show token success modal with generated token
 */
function showTokenSuccessModal(token) {
    $('#generatedToken').val(token);
    $('#tokenExample').text(token);
    document.getElementById('tokenSuccessModal').showModal();
}

/**
 * Copy token to clipboard
 */
window.copyToken = function() {
    const tokenInput = document.getElementById('generatedToken');
    tokenInput.select();
    tokenInput.setSelectionRange(0, 99999); // For mobile

    navigator.clipboard.writeText(tokenInput.value).then(() => {
        Toast.showToast('Token copied to clipboard!', 'success');
    }).catch(() => {
        // Fallback
        document.execCommand('copy');
        Toast.showToast('Token copied to clipboard!', 'success');
    });
};

/**
 * Close success modal
 */
window.closeSuccessModal = function() {
    document.getElementById('tokenSuccessModal').close();
    window.location.reload();
};

/**
 * Revoke specific token
 */
window.revokeToken = async function(tokenId, tokenName) {
    if (!confirm(`Are you sure you want to revoke token "${tokenName}"?\n\nThis action cannot be undone.`)) {
        return;
    }

    try {
        await Ajax.delete(`/settings/generals/api-tokens/${tokenId}`, {
            loadingMessage: 'Revoking token...',
            successMessage: 'Token revoked successfully',
            onSuccess: () => {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        });
    } catch (error) {
        // Error already handled by Ajax helper
    }
};

/**
 * Revoke all tokens
 */
window.revokeAllTokens = async function() {
    if (!confirm('Are you sure you want to revoke ALL tokens?\n\nThis will invalidate all existing API tokens and cannot be undone.')) {
        return;
    }

    try {
        await Ajax.delete('/settings/generals/api-tokens', {
            loadingMessage: 'Revoking all tokens...',
            successMessage: 'All tokens revoked successfully',
            onSuccess: () => {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        });
    } catch (error) {
        // Error already handled by Ajax helper
    }
};

/**
 * Show token details modal
 */
window.showTokenDetails = async function(tokenId) {
    try {
        const result = await Ajax.get(`/settings/generals/api-tokens/${tokenId}/show`, {
            loadingMessage: 'Loading token details...',
            useGlobalLoading: false
        });

        displayTokenDetails(result.data);
        document.getElementById('tokenDetailsModal').showModal();
    } catch (error) {
        // Error already handled by Ajax helper
    }
};

/**
 * Display token details in modal
 */
function displayTokenDetails(token) {
    const abilities = token.abilities && token.abilities.length > 0 ? token.abilities : ['*'];
    const isFullAccess = abilities.includes('*');

    const detailsHtml = `
        <div class="space-y-3">
            <div>
                <p class="text-sm text-base-content/60">Token Name</p>
                <p class="font-medium">${token.name}</p>
            </div>
            <div>
                <p class="text-sm text-base-content/60">Abilities</p>
                <div class="flex flex-wrap gap-2 mt-1">
                    ${isFullAccess
                        ? '<span class="badge badge-success">Full Access</span>'
                        : abilities.map(a => `<span class="badge">${a}</span>`).join('')
                    }
                </div>
            </div>
            <div>
                <p class="text-sm text-base-content/60">Last Used</p>
                <p class="font-medium">${token.last_used_at || 'Never'}</p>
            </div>
            <div>
                <p class="text-sm text-base-content/60">Expires At</p>
                <p class="font-medium">${token.expires_at || 'Never'}</p>
            </div>
            <div>
                <p class="text-sm text-base-content/60">Created At</p>
                <p class="font-medium">${token.created_at}</p>
            </div>
        </div>
    `;

    $('#tokenDetailsContent').html(detailsHtml);
}
