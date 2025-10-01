import Toast from "../../components/toast.js";

$(document).ready(function() {
    // Toggle active status
    $('.toggle-form').on('submit', async function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const url = $form.attr('action');
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });
            
            const result = await response.json();
            
            if (response.ok && result.success) {
                Toast.showToast(result.message, 'success', 2000);
                setTimeout(() => window.location.reload(), 800);
            } else {
                Toast.showToast(result.message || 'Failed to toggle status', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            Toast.showToast('An error occurred', 'error');
        }
    });
    
    // Delete form handler
    $('.delete-form').on('submit', async function(e) {
        e.preventDefault();
        
        const confirmed = confirm('Are you sure you want to delete this role? This action cannot be undone.');
        if (!confirmed) return;
        
        const $form = $(this);
        const url = $form.attr('action');
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        
        try {
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });
            
            const result = await response.json();
            
            if (response.ok && result.success) {
                Toast.showToast(result.message, 'success', 2000);
                setTimeout(() => window.location.reload(), 800);
            } else {
                Toast.showToast(result.message || 'Failed to delete role', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            Toast.showToast('An error occurred while deleting role', 'error');
        }
    });
});
