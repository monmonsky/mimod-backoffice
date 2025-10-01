import Toast from "../../../components/toast";

// jQuery is loaded from CDN
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
    const $submitBtn = $form.find('button[type="submit"]');
    const originalBtnText = $submitBtn.html();

    // Get ability type
    const abilityType = $('input[name="ability_type"]:checked').val();
    const abilities = abilityType === 'readonly' ? ['settings:read'] : ['*'];

    // Build request data
    const requestData = {
        token_name: formData.get('token_name'),
        abilities: abilities,
        _token: $('meta[name="csrf-token"]').attr('content')
    };

    // Show loading state
    $submitBtn.prop('disabled', true).html('<span class="loading loading-spinner loading-sm"></span> Generating...');

    try {
        const response = await fetch('/settings/general/api-tokens/generate', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': requestData._token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(requestData)
        });

        const data = await response.json();

        if (response.ok && data.success) {
            // Close generate modal
            document.getElementById('generateTokenModal').close();

            // Show success modal with token
            showTokenSuccessModal(data.data.token);

            // Reset form
            $form[0].reset();

            Toast.showToast(data.message || 'Token generated successfully!', 'success');

            // Reload page after 5 seconds
            setTimeout(() => {
                window.location.reload();
            }, 5000);
        } else {
            // Handle validation errors
            if (data.errors) {
                const errorMessages = Object.values(data.errors).flat().join(', ');
                Toast.showToast(errorMessages, 'error');
            } else {
                Toast.showToast(data.message || 'Failed to generate token', 'error');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        Toast.showToast('An error occurred while generating token', 'error');
    } finally {
        $submitBtn.prop('disabled', false).html(originalBtnText);
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

    // Show loading toast
    Toast.showToast('Revoking token...', 'info', 1000);

    try {
        const response = await fetch(`/settings/general/api-tokens/${tokenId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });

        const data = await response.json();

        if (response.ok && data.success) {
            Toast.showToast(data.message || 'Token revoked successfully', 'success');

            // Reload page after short delay
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            Toast.showToast(data.message || 'Failed to revoke token', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Toast.showToast('An error occurred while revoking token', 'error');
    }
};

/**
 * Revoke all tokens
 */
window.revokeAllTokens = async function() {
    if (!confirm('Are you sure you want to revoke ALL tokens?\n\nThis will invalidate all existing API tokens and cannot be undone.')) {
        return;
    }

    // Show loading toast
    Toast.showToast('Revoking all tokens...', 'info', 1000);

    try {
        const response = await fetch('/settings/general/api-tokens', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });

        const data = await response.json();

        if (response.ok && data.success) {
            Toast.showToast(data.message || 'All tokens revoked successfully', 'success');

            // Reload page after short delay
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            Toast.showToast(data.message || 'Failed to revoke tokens', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Toast.showToast('An error occurred while revoking tokens', 'error');
    }
};

/**
 * Show token details modal
 */
window.showTokenDetails = async function(tokenId) {
    // Show loading toast
    Toast.showToast('Loading token details...', 'info', 1000);

    try {
        const response = await fetch(`/settings/general/api-tokens/${tokenId}/show`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });

        const data = await response.json();

        if (response.ok && data.success) {
            displayTokenDetails(data.data);
            document.getElementById('tokenDetailsModal').showModal();
        } else {
            Toast.showToast(data.message || 'Failed to get token details', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Toast.showToast('An error occurred while fetching token details', 'error');
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
