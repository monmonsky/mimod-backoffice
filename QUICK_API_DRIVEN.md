# Quick Reference: API-Driven Module

⚡ **Fast implementation guide for experienced developers**

For complete explanations, see [STANDARDIZE_MODULE_TO_API_DRIVEN.md](./STANDARDIZE_MODULE_TO_API_DRIVEN.md)

---

## TL;DR Checklist

- [ ] **Permissions**: Global `window.userPermissions` and `window.hasPermission()` already set in layout
- [ ] Controller: Pure `return view()` only - NO data processing
- [ ] Blade: Empty `<tbody id="tableBody">` + statistics with IDs
- [ ] JavaScript: `loadData()` on page ready
- [ ] Response check: `response.status` NOT `response.success`
- [ ] Action buttons: Check `window.hasPermission('[module].[action]')`
- [ ] CRUD: `showToast: true` + `await loadData()` after operations
- [ ] Reference: `resources/js/modules/marketing/coupons/index.js`

---

## Implementation Time

- **Simple CRUD module**: 2-3 hours
- **Complex with relations**: 4-6 hours
- **Testing**: 1 hour

---

## Files to Modify

```
✏️ resources/views/pages/[module]/index.blade.php
✏️ app/Http/Controllers/[Module]Controller.php
✏️ resources/js/modules/[module]/index.js
✅ app/Http/Controllers/Api/[Module]ApiController.php (already exists)
✅ routes/api.php (already configured)
```

---

## 1. Controller (1 minute)

```php
public function index(Request $request)
{
    return view('pages.[module].index');
}
```

**Remove**: show, store, update, destroy, query building, filters, pagination

---

## 2. Blade View (5 minutes)

### Statistics (Change from server to client)

```blade
<!-- FROM -->
<x-stat-card title="Total" :value="$statistics->total" />

<!-- TO -->
<div class="card bg-base-100 shadow-sm">
    <div class="card-body p-4">
        <p class="text-sm text-base-content/60">Total</p>
        <p class="text-2xl font-bold" id="statTotal">...</p>
    </div>
</div>
```

### Table Body

```blade
<!-- FROM -->
@foreach($items as $item)
    <tr>...</tr>
@endforeach

<!-- TO -->
<tbody id="tableBody">
    <tr id="loadingRow">
        <td colspan="8" class="text-center py-8">
            <span class="loading loading-spinner loading-md"></span>
            <p class="mt-2 text-base-content/60">Loading...</p>
        </td>
    </tr>
</tbody>
```

### Pagination

```blade
<!-- FROM -->
{{ $items->links() }}

<!-- TO -->
<div id="paginationContainer" class="p-4"></div>
```

---

## 3. Permissions (Already Configured)

**Global permissions are already set in layout** (`resources/views/layouts/app.blade.php`):

```javascript
// Available globally:
window.userPermissions = ['permission.name.1', 'permission.name.2', ...]
window.hasPermission(permission) // Returns true/false
```

**Usage in your module:**
```javascript
// Check permission before showing button
${window.hasPermission && window.hasPermission('marketing.coupons.view') ? `
    <button onclick="viewItem(${item.id})">View</button>
` : ''}
```

**Permission naming pattern:**
- View: `[module].[submodule].view`
- Create: `[module].[submodule].create`
- Update: `[module].[submodule].update`
- Delete: `[module].[submodule].delete`

**Example:**
- `marketing.coupons.view`
- `marketing.coupons.create`
- `marketing.coupons.update`
- `marketing.coupons.delete`

---

## 4. JavaScript Structure (Copy-Paste Template)

```javascript
import Ajax from '../../../utils/ajax.js';
import Toast from '../../../components/toast.js';
import $ from 'jquery';

let currentItemId = null;

// ============================================
// INITIALIZATION
// ============================================

$(document).ready(function() {
    loadData();
    initEventListeners();
});

// ============================================
// LOAD DATA
// ============================================

async function loadData(page = 1) {
    try {
        // Show loading
        $('#tableBody').html(`
            <tr><td colspan="8" class="text-center py-8">
                <span class="loading loading-spinner loading-md"></span>
                <p class="mt-2 text-base-content/60">Loading...</p>
            </td></tr>
        `);

        // Get filters
        const filters = {
            search: $('[name="search"]').val() || '',
            status: $('[name="status"]').val() || '',
            page: page,
            per_page: 20
        };

        // Remove empty
        Object.keys(filters).forEach(key => {
            if (!filters[key] || filters[key] === '') delete filters[key];
        });

        // API call
        const response = await Ajax.get(`/api/[module-path]?${new URLSearchParams(filters)}`, {
            showLoading: false,
            showToast: false
        });

        // ⚠️ IMPORTANT: Check response.status NOT response.success
        if (response.status && response.data) {
            renderTable(response.data.items);
            renderPagination(response.data.items);
            if (response.data.statistics) {
                updateStatistics(response.data.statistics);
            }
        } else {
            throw new Error('Invalid response structure');
        }
    } catch (error) {
        console.error('Error loading data:', error);
        $('#tableBody').html(`
            <tr><td colspan="8" class="text-center py-8 text-error">
                <p>Failed to load data</p>
            </td></tr>
        `);
    }
}

// ============================================
// RENDER FUNCTIONS
// ============================================

function renderTable(data) {
    const $tbody = $('#tableBody');
    const items = data.data || data;

    if (!items || items.length === 0) {
        $tbody.html(`<tr><td colspan="8" class="text-center py-8">No data found</td></tr>`);
        return;
    }

    let html = '';
    items.forEach(item => {
        html += `
            <tr class="hover">
                <td>${item.name}</td>
                <td>${item.code}</td>
                <td>
                    <div class="flex gap-2">
                        ${window.hasPermission && window.hasPermission('[module].[view]') ? `
                        <button class="btn btn-sm btn-ghost" onclick="viewItem(${item.id})">
                            <span class="iconify lucide--eye size-4"></span>
                        </button>
                        ` : ''}
                        ${window.hasPermission && window.hasPermission('[module].[update]') ? `
                        <button class="btn btn-sm btn-ghost" onclick="editItem(${item.id})">
                            <span class="iconify lucide--pencil size-4"></span>
                        </button>
                        ` : ''}
                        ${window.hasPermission && window.hasPermission('[module].[delete]') ? `
                        <button class="btn btn-sm btn-ghost text-error" onclick="deleteItem(${item.id})">
                            <span class="iconify lucide--trash-2 size-4"></span>
                        </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `;
    });

    $tbody.html(html);
}

function renderPagination(data) {
    const $container = $('#paginationContainer');

    if (!data.last_page || data.last_page <= 1) {
        $container.html('');
        return;
    }

    let html = '<div class="flex justify-between items-center">';
    html += `<p class="text-sm text-base-content/60">Showing ${data.from} to ${data.to} of ${data.total}</p>`;
    html += '<div class="join">';

    // Previous
    html += `<button class="join-item btn btn-sm" ${data.current_page === 1 ? 'disabled' : ''} onclick="loadDataPage(${data.current_page - 1})">«</button>`;

    // Pages
    const startPage = Math.max(1, data.current_page - 2);
    const endPage = Math.min(data.last_page, data.current_page + 2);
    for (let i = startPage; i <= endPage; i++) {
        html += `<button class="join-item btn btn-sm ${i === data.current_page ? 'btn-active' : ''}" onclick="loadDataPage(${i})">${i}</button>`;
    }

    // Next
    html += `<button class="join-item btn btn-sm" ${data.current_page === data.last_page ? 'disabled' : ''} onclick="loadDataPage(${data.current_page + 1})">»</button>`;

    html += '</div></div>';
    $container.html(html);
}

window.loadDataPage = function(page) {
    loadData(page);
};

function updateStatistics(stats) {
    if (!stats) return;
    $('#statTotal').text(stats.total || 0);
    $('#statActive').text(stats.active || 0);
}

// ============================================
// EVENT LISTENERS
// ============================================

function initEventListeners() {
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadData(1);
    });

    $('#clearFilters').on('click', function() {
        $('[name="search"]').val('');
        $('[name="status"]').val('');
        loadData(1);
    });
}

// ============================================
// FORM SUBMIT
// ============================================

document.getElementById('itemForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = {
        name: formData.get('name'),
        code: formData.get('code'),
        is_active: formData.get('is_active') ? true : false,
    };

    try {
        if (currentItemId) {
            await Ajax.update(`/api/[module-path]/${currentItemId}`, data, {
                successMessage: 'Updated successfully',
                showToast: true  // ⚠️ IMPORTANT
            });
        } else {
            await Ajax.create('/api/[module-path]', data, {
                successMessage: 'Created successfully',
                showToast: true  // ⚠️ IMPORTANT
            });
        }

        itemModal.close();
        this.reset();
        currentItemId = null;
        await loadData();  // ⚠️ IMPORTANT: Reload data
    } catch (error) {
        // Error handled by Ajax
    }
});

// ============================================
// CRUD OPERATIONS
// ============================================

window.viewItem = async function(id) {
    try {
        const response = await Ajax.get(`/api/[module-path]/${id}`);
        if (response.status) {  // ⚠️ IMPORTANT: Use response.status
            const item = response.data.item;
            // Populate modal with item data
            viewModal.showModal();
        }
    } catch (error) {
        Toast.showToast('An error occurred', 'error');
    }
};

window.editItem = async function(id) {
    try {
        const response = await Ajax.get(`/api/[module-path]/${id}`);
        if (response.status) {  // ⚠️ IMPORTANT: Use response.status
            currentItemId = id;
            const item = response.data.item;

            // Populate form
            document.getElementById('itemForm').querySelector('[name="name"]').value = item.name;
            document.getElementById('itemForm').querySelector('[name="code"]').value = item.code;

            itemModal.showModal();
        }
    } catch (error) {
        Toast.showToast('An error occurred', 'error');
    }
};

window.deleteItem = async function(id) {
    if (!confirm('Are you sure?')) return;

    try {
        await Ajax.destroy(`/api/[module-path]/${id}`, {
            successMessage: 'Deleted successfully',
            showToast: true  // ⚠️ IMPORTANT
        });

        await loadData();  // ⚠️ IMPORTANT: Reload data
    } catch (error) {
        // Error handled by Ajax
    }
};

window.openCreateModal = function() {
    currentItemId = null;
    document.getElementById('itemForm').reset();
    document.querySelector('#itemModal h3').textContent = 'Create New';
    itemModal.showModal();
};
```

---

## ⚠️ Common Mistakes

### 1. Wrong Response Check
```javascript
// ❌ WRONG
if (response.success && response.data) { }

// ✅ CORRECT
if (response.status && response.data) { }
```

### 2. No Toast Notification
```javascript
// ❌ WRONG
await Ajax.create(url, data);

// ✅ CORRECT
await Ajax.create(url, data, { showToast: true });
```

### 3. Forgot to Reload
```javascript
// ❌ WRONG
await Ajax.destroy(url);

// ✅ CORRECT
await Ajax.destroy(url, { showToast: true });
await loadData();
```

### 4. Not Resetting currentItemId
```javascript
// ❌ WRONG
itemModal.close();
this.reset();
await loadData();

// ✅ CORRECT
itemModal.close();
this.reset();
currentItemId = null;  // ← Add this!
await loadData();
```

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Data tidak tampil | Check `response.status` not `response.success` |
| Action buttons hilang | Remove permission checks atau pastikan syntax benar |
| Toast tidak muncul | Add `showToast: true` di Ajax options |
| Tidak reload setelah CRUD | Add `await loadData()` setelah operasi |
| Loading tidak muncul saat filter | Manual loading state di awal `loadData()` |
| Modal tidak close | Check `itemModal.close()` dipanggil |

---

## API Response Structure

```json
{
    "status": true,          // ← Use this, NOT "success"
    "statusCode": "200",
    "message": "Success",
    "data": {
        "items": {
            "current_page": 1,
            "data": [...],   // ← Actual items
            "last_page": 5,
            "from": 1,
            "to": 20,
            "total": 100
        },
        "statistics": {
            "total": 100,
            "active": 50
        }
    }
}
```

---

## Testing Checklist

Quick test after implementation:

- [ ] Page loads without errors
- [ ] Data loads automatically
- [ ] Statistics update from API
- [ ] Action buttons visible
- [ ] Filter shows loading + works
- [ ] Create shows toast + reloads data
- [ ] Edit shows toast + reloads data
- [ ] Delete shows toast + reloads data
- [ ] No page refresh on any operation
- [ ] Console has no errors

---

## Reference Files

**Copy these patterns:**
- JavaScript: `resources/js/modules/marketing/coupons/index.js`
- API Controller: `app/Http/Controllers/Api/Marketing/CouponApiController.php`
- Ajax Utility: `resources/js/utils/ajax.js`
- Blade View: `resources/views/pages/marketing/coupons/index.blade.php`

---

## Need Help?

See complete guide: [STANDARDIZE_MODULE_TO_API_DRIVEN.md](./STANDARDIZE_MODULE_TO_API_DRIVEN.md)
