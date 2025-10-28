# AJAX Helper Utility

Global AJAX helper untuk membuat request HTTP dengan consistent loading, toast notifications, dan error handling.

## Features

- ✅ Auto CSRF token injection
- ✅ Built-in loading state management (target-specific & global overlay)
- ✅ Auto toast notifications
- ✅ Consistent error handling
- ✅ Promise-based (async/await)
- ✅ Request timeout
- ✅ Support FormData & JSON

## Usage

### Import

```javascript
import Ajax from '../utils/ajax.js';
```

### Basic Methods

```javascript
// GET request
const data = await Ajax.get('/api/users');

// POST request (Create)
const user = await Ajax.post('/user/store', formData);

// PUT request (Update)
const updated = await Ajax.put('/user/1', data);

// DELETE request
await Ajax.delete('/user/1');
```

### Convenience Methods

```javascript
// Create with auto success message
await Ajax.create('/user/store', data);

// Update with auto success message
await Ajax.update('/user/1', data);

// Delete with auto success message
await Ajax.destroy('/user/1');
```

### With Options

```javascript
await Ajax.create('/user/store', formData, {
    loadingMessage: 'Creating user...',   // Message for global loading (default: 'Processing...')
    successMessage: 'User created!',      // Custom success message
    errorMessage: 'Failed to create',     // Custom error message
    showLoading: true,                    // Show loading state (default: true)
    useGlobalLoading: true,               // Use full-screen loading overlay (default: true)
    loadingTarget: null,                  // Show loading on specific element (default: null)
    showToast: true,                      // Show toast notification (default: true)
    timeout: 30000,                       // Request timeout in ms (default: 30000)
    onSuccess: (response) => {            // Success callback
        console.log(response);
    },
    onError: (xhr, errorMessage) => {     // Error callback
        console.error(errorMessage);
    },
    onComplete: () => {                   // Always runs after request
        console.log('Done');
    }
});
```

### Examples

#### Toggle Status

```javascript
window.toggleStatus = async function(checkbox) {
    const userId = $(checkbox).data('id');
    const isActive = $(checkbox).is(':checked');

    try {
        await Ajax.post(`/user/${userId}/toggle-active`, null, {
            loadingTarget: checkbox,
            successMessage: 'Status updated',
            onSuccess: () => {
                // Update UI
            },
            onError: () => {
                // Revert checkbox
                $(checkbox).prop('checked', !isActive);
            }
        });
    } catch (error) {
        // Error already handled
    }
};
```

#### Create Form

```javascript
$form.on('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    try {
        await Ajax.create('/user/store', formData, {
            loadingMessage: 'Creating user...',
            successMessage: 'User created successfully',
            onSuccess: () => {
                window.location.href = '/user';
            },
            onError: (xhr) => {
                // Handle validation errors
                if (xhr.responseJSON?.errors) {
                    Object.keys(xhr.responseJSON.errors).forEach(key => {
                        $(`#error-${key}`).text(xhr.responseJSON.errors[key][0]);
                    });
                }
            }
        });
    } catch (error) {
        // Error handled by Ajax helper
    }
});
```

#### Delete with Confirmation

```javascript
window.deleteUser = async function(userId, userName) {
    if (!confirm(`Delete ${userName}?`)) return;

    try {
        await Ajax.destroy(`/user/${userId}`, {
            loadingMessage: 'Deleting user...',
            successMessage: 'User deleted successfully',
            onSuccess: () => {
                window.location.reload();
            }
        });
    } catch (error) {
        // Error handled
    }
};
```

## Loading State

The helper supports 2 types of loading indicators:

### 1. Global Loading Overlay (default)
Full-screen loading overlay with backdrop blur and custom message - **enabled by default**

```javascript
await Ajax.post('/api/endpoint', data, {
    loadingMessage: 'Processing your request...'
});
```

### 2. Target-Specific Loading (optional)
- **Button**: Disabled + loading spinner
- **Checkbox**: Disabled
- **Row opacity**: 60% during request

```javascript
await Ajax.post('/api/endpoint', data, {
    useGlobalLoading: false,        // Disable global loading
    loadingTarget: '#myButton'      // Use target-specific loading
});
```

**When to use:**
- **Global overlay** (default): Form submissions, delete operations, most AJAX requests
- **Target-specific**: Small inline actions where full-screen overlay is too intrusive

## Error Handling

Automatic error handling with fallbacks:

1. Try to get message from `response.message`
2. Try to get validation errors from `response.errors`
3. Use custom `errorMessage` if provided
4. Fallback to HTTP status messages
5. Show error toast (if `showToast: true`)
6. Call `onError` callback

## Default Options

```javascript
{
    showLoading: true,
    showToast: true,
    loadingTarget: null,             // Element to show loading on (optional)
    loadingMessage: 'Processing...', // Message for global loading
    useGlobalLoading: true,          // Use full-screen overlay (default: true)
    successMessage: null,
    errorMessage: null,
    timeout: 30000,
    onSuccess: null,
    onError: null,
    onComplete: null
}
```
