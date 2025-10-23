# Standardize Module to API-Driven Architecture

**Complete Implementation Guide**

> 💡 **Quick Reference**: For experienced developers, see [QUICK_API_DRIVEN.md](./QUICK_API_DRIVEN.md)

## Table of Contents

1. [Task Description](#task-description)
2. [Context & Prerequisites](#context--prerequisites)
3. [Implementation Steps](#implementation-steps)
   - [Blade Views](#1-blade-views-update)
   - [Controller](#2-controller-update)
   - [JavaScript](#3-javascript-file-update)
   - [API Controller](#5-api-controller-verification)
   - [Routes](#6-routes-verification)
4. [Ajax.js Usage](#4-ajaxjs-usage-rules)
5. [Testing](#7-testing-checklist)
6. [Troubleshooting](#troubleshooting)
7. [Common Gotchas](#8-common-gotchas)
8. [Success Criteria](#10-success-criteria)
9. [Example](#example-marketing-module)

---

## Template Prompt untuk Standardisasi Module

Gunakan prompt ini untuk mengubah module apapun menjadi fully API-driven dengan ajax.js utility.

---

## Task Description

**Standardize [MODULE_NAME] module to be fully API-driven using ajax.js utility**

Replace `[MODULE_NAME]`, `[module_path]`, `[ControllerPath]`, dan placeholders lainnya dengan nilai yang sesuai.

---

## Context

- **Module location:** `resources/views/pages/[module_path]/`
- **JavaScript location:** `resources/js/modules/[module_path]/`
- **Controller location:** `app/Http/Controllers/[ControllerPath]/`
- **API Controller location:** `app/Http/Controllers/Api/[ControllerPath]/`

---

## Prerequisites (Already Complete)

✅ ajax.js utility is complete with all required methods (get, post, put, delete, create, update, destroy)
✅ API controllers already created with standardized ResultBuilder response
✅ API routes configured in api.php with proper permissions
✅ Web routes configured in web.php for rendering views only
✅ **Global permissions configured in layout** - `window.userPermissions` and `window.hasPermission()` available globally

---

## Requirements

### 1. Blade Views Update
**File:** `resources/views/pages/[module_path]/*.blade.php`

#### Current State (REMOVE):
```blade
{{-- Server-side data rendering --}}
@foreach($items as $item)
    <tr>
        <td>{{ $item->name }}</td>
        <td>{{ $item->status }}</td>
    </tr>
@endforeach

{{-- Pagination --}}
{{ $items->links() }}
```

#### Target State (REPLACE WITH):
```blade
{{-- Empty table structure with loading state --}}
<tbody id="dataTableBody">
    <tr id="loadingRow">
        <td colspan="8" class="text-center py-8">
            <span class="loading loading-spinner loading-md"></span>
            <p class="mt-2 text-base-content/60">Loading data...</p>
        </td>
    </tr>
</tbody>

{{-- Pagination container (will be filled by JavaScript) --}}
<div id="paginationContainer" class="p-4"></div>
```

#### Statistics Cards Pattern:
```blade
{{-- Change FROM server-side: --}}
<x-stat-card title="Total Items" :value="$statistics->total_items" />

{{-- TO client-side with ID: --}}
<div class="card bg-base-100 shadow-sm">
    <div class="card-body p-4">
        <p class="text-sm text-base-content/60">Total Items</p>
        <p class="text-2xl font-bold" id="statTotalItems">...</p>
    </div>
</div>
```

#### What to Keep:
- Statistics cards structure (but replace with client-side pattern above)
- Filter form structure
- Modal structures
- Page header, breadcrumbs
- Permission checks for buttons (`@if(hasPermission(...))`) - keep in blade or remove for JS

#### What to Remove:
- `@foreach` loops for main data
- `{{ $variable }}` for data display in table rows
- Server-side pagination `{{ $items->links() }}`
- Any data processing logic in blade
- Server-side rendered statistics values

---

### 2. Controller Update
**File:** `app/Http/Controllers/[Controller].php`

#### Keep ONLY this:
```php
<?php

namespace App\Http\Controllers\[Namespace];

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\[RepositoryInterface];
use Illuminate\Http\Request;

class [Controller] extends Controller
{
    protected $repo;

    public function __construct([RepositoryInterface] $repository)
    {
        $this->repo = $repository;
    }

    public function index(Request $request)
    {
        // Pure view only - NO data processing
        return view('pages.[module].index');
    }
}
```

#### Remove These Methods:
- `show()` - Moved to API controller
- `store()` - Moved to API controller
- `update()` - Moved to API controller
- `destroy()` - Moved to API controller
- All query building logic
- All filtering logic
- All pagination logic

---

### 3. JavaScript File Update
**File:** `resources/js/modules/[module]/index.js`

#### Complete Pattern:
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
// LOAD DATA FROM API
// ============================================

async function loadData(page = 1) {
    try {
        // Show loading state
        $('#dataTableBody').html(`
            <tr id="loadingRow">
                <td colspan="8" class="text-center py-8">
                    <span class="loading loading-spinner loading-md"></span>
                    <p class="mt-2 text-base-content/60">Loading data...</p>
                </td>
            </tr>
        `);

        // Get filter values from form
        const filters = {
            search: $('[name="search"]').val() || '',
            status: $('[name="status"]').val() || '',
            type: $('[name="type"]').val() || '',
            sort_by: $('[name="sort_by"]').val() || 'created_at',
            page: page,
            per_page: 20
        };

        // Remove empty values
        Object.keys(filters).forEach(key => {
            if (!filters[key] || filters[key] === '') delete filters[key];
        });

        // Build query string
        const queryString = new URLSearchParams(filters).toString();

        const response = await Ajax.get(`/api/[module-path]?${queryString}`, {
            showLoading: false,  // We handle loading manually
            showToast: false     // Don't show toast for data load
        });

        // IMPORTANT: Check response.status (not response.success)
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
        $('#dataTableBody').html(`
            <tr>
                <td colspan="8" class="text-center py-8 text-error">
                    <span class="iconify lucide--alert-circle size-8 mb-2"></span>
                    <p>Failed to load data. Please refresh the page.</p>
                    <p class="text-xs mt-2">${error.message || 'Unknown error'}</p>
                </td>
            </tr>
        `);
    }
}

// ============================================
// RENDER FUNCTIONS
// ============================================

function renderTable(data) {
    const $tbody = $('#dataTableBody');

    // Check if paginated response
    const items = data.data || data;

    if (!items || items.length === 0) {
        $tbody.html(`
            <tr>
                <td colspan="8" class="text-center py-8">
                    <span class="iconify lucide--inbox size-8 mb-2 text-base-content/40"></span>
                    <p class="text-base-content/60">No data found</p>
                </td>
            </tr>
        `);
        return;
    }

    let html = '';
    items.forEach(item => {
        // Determine status badge
        const statusBadge = item.is_active
            ? '<span class="badge badge-success badge-sm">Active</span>'
            : '<span class="badge badge-ghost badge-sm">Inactive</span>';

        html += `
            <tr class="hover">
                <td>${item.name || '-'}</td>
                <td>${item.code || '-'}</td>
                <td>${statusBadge}</td>
                <td>${new Date(item.created_at).toLocaleDateString('id-ID')}</td>
                <td>
                    <div class="flex gap-2">
                        ${window.hasPermission && window.hasPermission('[module].[view]') ? `
                        <button class="btn btn-sm btn-ghost" onclick="viewItem(${item.id})" title="View">
                            <span class="iconify lucide--eye size-4"></span>
                        </button>
                        ` : ''}
                        ${window.hasPermission && window.hasPermission('[module].[update]') ? `
                        <button class="btn btn-sm btn-ghost" onclick="editItem(${item.id})" title="Edit">
                            <span class="iconify lucide--pencil size-4"></span>
                        </button>
                        ` : ''}
                        ${window.hasPermission && window.hasPermission('[module].[delete]') ? `
                        <button class="btn btn-sm btn-ghost text-error" onclick="deleteItem(${item.id})" title="Delete">
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

    // Pagination info
    html += `<p class="text-sm text-base-content/60">Showing ${data.from} to ${data.to} of ${data.total} results</p>`;

    // Pagination buttons
    html += '<div class="join">';

    // Previous button
    html += `
        <button class="join-item btn btn-sm" ${data.current_page === 1 ? 'disabled' : ''} onclick="loadDataPage(${data.current_page - 1})">
            «
        </button>
    `;

    // Page numbers (show max 5 pages)
    const startPage = Math.max(1, data.current_page - 2);
    const endPage = Math.min(data.last_page, data.current_page + 2);

    for (let i = startPage; i <= endPage; i++) {
        html += `
            <button class="join-item btn btn-sm ${i === data.current_page ? 'btn-active' : ''}" onclick="loadDataPage(${i})">
                ${i}
            </button>
        `;
    }

    // Next button
    html += `
        <button class="join-item btn btn-sm" ${data.current_page === data.last_page ? 'disabled' : ''} onclick="loadDataPage(${data.current_page + 1})">
            »
        </button>
    `;

    html += '</div></div>';

    $container.html(html);
}

// Make loadData accessible for pagination
window.loadDataPage = function(page) {
    loadData(page);
};

function updateStatistics(stats) {
    if (!stats) return;

    // Update stat cards if present
    $('#totalCount').text(stats.total || 0);
    $('#activeCount').text(stats.active || 0);
    // ... update other statistics
}

// ============================================
// EVENT LISTENERS
// ============================================

function initEventListeners() {
    // Filter form submit
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadData(1); // Reset to page 1
    });

    // Clear filters
    $('#clearFilters, .clear-filter').on('click', function(e) {
        e.preventDefault();
        $('#filterForm')[0].reset();
        loadData(1);
    });

}

// ============================================
// FORM SUBMIT (Create/Update)
// ============================================

document.getElementById('itemForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = {
        name: formData.get('name'),
        code: formData.get('code'),
        description: formData.get('description'),
        is_active: formData.get('is_active') ? true : false,
    };

    try {
        if (currentItemId) {
            await Ajax.update(`/api/[module-path]/${currentItemId}`, data, {
                successMessage: 'Item updated successfully',
                showToast: true
            });
        } else {
            await Ajax.create('/api/[module-path]', data, {
                successMessage: 'Item created successfully',
                showToast: true
            });
        }

        itemModal.close();
        this.reset();
        currentItemId = null;
        await loadData();
    } catch (error) {
        // Error already handled by Ajax
    }
});
}

// ============================================
// CRUD OPERATIONS
// ============================================

// View item details
window.viewItem = async function(id) {
    try {
        const response = await Ajax.get(`/api/[module-path]/${id}`);

        if (response.status) {
            const item = response.data.item;

            $('#detailContent').html(`
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-base-content/60">Name</div>
                            <div class="font-medium">${item.name}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Code</div>
                            <div class="font-medium">${item.code}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Status</div>
                            <div>
                                ${item.is_active
                                    ? '<span class="badge badge-success">Active</span>'
                                    : '<span class="badge badge-ghost">Inactive</span>'}
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Created At</div>
                            <div>${new Date(item.created_at).toLocaleString('id-ID')}</div>
                        </div>
                    </div>

                    ${item.description ? `
                    <div>
                        <div class="text-sm text-base-content/60">Description</div>
                        <div class="mt-1">${item.description}</div>
                    </div>
                    ` : ''}
                </div>
            `);

            detailModal.showModal();
        }
    } catch (error) {
        Toast.showToast('An error occurred', 'error');
    }
};

// Edit item
window.editItem = async function(id) {
    try {
        const response = await Ajax.get(`/api/[module-path]/${id}`);

        if (response.status) {
            currentItemId = id;
            const item = response.data.item;

            // Populate form fields
            $('#name').val(item.name);
            $('#code').val(item.code);
            $('#description').val(item.description);
            $('#is_active').prop('checked', item.is_active);

            // Update modal title
            $('#itemModal h3').text('Edit Item');
            $('#btnSave').text('Update');

            itemModal.showModal();
        }
    } catch (error) {
        Toast.showToast('An error occurred', 'error');
    }
};

// Delete item
window.deleteItem = async function(id) {
    if (!confirm('Are you sure you want to delete this item?')) return;

    try {
        await Ajax.destroy(`/api/[module-path]/${id}`, {
            successMessage: 'Item deleted successfully',
            showToast: true
        });

        await loadData();
    } catch (error) {
        // Error already handled by Ajax
    }
};

// Open create modal
window.openCreateModal = function() {
    currentItemId = null;
    document.getElementById('itemForm').reset();
    document.querySelector('#itemModal h3').textContent = 'Create New Item';
    itemModal.showModal();
};
```

---

### 4. Ajax.js Usage Rules

#### DO ✅
- Always use `Ajax.get()`, `Ajax.post()`, `Ajax.put()`, `Ajax.delete()`
- Or use convenience methods: `Ajax.create()`, `Ajax.update()`, `Ajax.destroy()`
- Use jQuery for DOM manipulation (`$()` syntax)
- Use `async/await` with try-catch
- Let Ajax handle errors (it shows toast automatically)
- Use `import $ from 'jquery'`

#### DON'T ❌
- ❌ Use native `fetch()`
- ❌ Use `axios` directly
- ❌ Use `$.ajax()` directly
- ❌ Use vanilla JavaScript for DOM (use jQuery)
- ❌ Show custom error toasts in catch block (Ajax already handles it)
- ❌ Use `document.getElementById()` (use `$('#id')` instead)

#### Ajax Options Reference:
```javascript
{
    showLoading: true,              // Show loading indicator
    showToast: true,                // Show success/error toast
    loadingTarget: '#btnSave',      // Specific element to show loading
    useGlobalLoading: true,         // Use full-screen overlay (default)
    loadingMessage: 'Saving...',    // Custom loading message
    successMessage: 'Saved!',       // Custom success message
    errorMessage: 'Failed!',        // Custom error message (rarely needed)
    timeout: 30000,                 // Request timeout (default 30s)
    onSuccess: (response) => {},    // Callback on success
    onError: (xhr, msg) => {},      // Callback on error
    onComplete: () => {}            // Callback always runs
}
```

#### Examples:

**GET data list (no toast):**
```javascript
const response = await Ajax.get('/api/items', {
    showLoading: false,  // Manual loading in table
    showToast: false     // Don't show toast for list load
});
```

**CREATE with toast:**
```javascript
await Ajax.create('/api/items', data, {
    successMessage: 'Item created successfully',
    showToast: true
});
```

**UPDATE with toast:**
```javascript
await Ajax.update(`/api/items/${id}`, data, {
    successMessage: 'Item updated successfully',
    showToast: true
});
```

**DELETE with confirmation:**
```javascript
if (confirm('Delete?')) {
    await Ajax.destroy(`/api/items/${id}`, {
        successMessage: 'Item deleted successfully',
        showToast: true
    });
    await loadData(); // Reload table
}
```

---

### 5. API Controller Verification
**File:** `app/Http/Controllers/Api/[ApiController].php`

#### Required Methods (Should Already Exist):

```php
// Get all with filters
public function index(Request $request)
{
    try {
        $query = $this->repo->query();

        // Apply filters
        if ($request->filled('search')) {
            $query->where('name', 'ILIKE', "%{$request->search}%");
        }

        $perPage = $request->get('per_page', 20);
        $items = $query->paginate($perPage);
        $statistics = $this->repo->getStatistics();

        $this->responseBuilder->setMessage("Items retrieved successfully.");
        $this->responseBuilder->setData([
            'items' => $items,
            'statistics' => $statistics
        ]);
        return $this->response->generateResponse($this->responseBuilder);
    } catch (\Exception $e) {
        $result = (new ResultBuilder())
            ->setStatus(false)
            ->setStatusCode('500')
            ->setMessage($e->getMessage())
            ->setData([]);
        return response()->json($this->response->generateResponse($result), 500);
    }
}

// Get single item
public function show($id) { }

// Create new item
public function store(Request $request) { }

// Update item
public function update(Request $request, $id) { }

// Delete item
public function destroy($id) { }
```

#### All responses MUST use ResultBuilder:
```php
$this->responseBuilder->setMessage("Success message");
$this->responseBuilder->setData(['key' => $value]);
return $this->response->generateResponse($this->responseBuilder);
```

#### IMPORTANT: Response Structure
The API returns this structure:
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Success message",
    "data": {
        "items": {...},
        "statistics": {...}
    }
}
```

**In JavaScript, always check `response.status` (NOT `response.success`)**

---

### 6. Routes Verification

#### web.php - Should ONLY have:
```php
Route::prefix('[module-prefix]')->group(function () {
    Route::controller([Controller]::class)->group(function () {
        Route::get('/', 'index')->name('[module].index')->middleware('permission:[module].view');
    });
});
```

#### api.php - Should have:
```php
Route::prefix('[module-path]')->group(function () {
    Route::get('/', [[ApiController]::class, 'index'])
        ->middleware('permission:[module].view');
    Route::get('/{id}', [[ApiController]::class, 'show'])
        ->middleware('permission:[module].view');
    Route::post('/', [[ApiController]::class, 'store'])
        ->middleware('permission:[module].create');
    Route::put('/{id}', [[ApiController]::class, 'update'])
        ->middleware('permission:[module].update');
    Route::delete('/{id}', [[ApiController]::class, 'destroy'])
        ->middleware('permission:[module].delete');
});
```

---

### 7. Testing Checklist

After implementation, verify:

- [ ] Page loads without error, shows loading state initially
- [ ] Data loads automatically via AJAX after page ready
- [ ] Statistics update from API response
- [ ] Table renders correctly with actual data
- [ ] Action buttons (view, edit, delete) are visible
- [ ] Pagination appears and works (if data > 20 items)
- [ ] Filter form submits via AJAX (page doesn't refresh)
- [ ] Loading indicator shows when filtering
- [ ] Clear filter button resets form and reloads data
- [ ] Create button opens modal with empty form
- [ ] Create form submits via AJAX and shows success toast
- [ ] Modal closes after successful create
- [ ] New item appears in table after create (no page reload)
- [ ] Edit button loads item data into modal
- [ ] Edit form submits via AJAX and shows success toast
- [ ] Modal closes after successful update
- [ ] Updated item reflects in table (no page reload)
- [ ] Delete button shows confirmation dialog
- [ ] Delete shows success toast and removes item from table
- [ ] View button shows item details in modal
- [ ] Success toasts appear after create/update/delete
- [ ] Error toasts appear for invalid data or failures
- [ ] All operations work without page refresh
- [ ] Console shows no JavaScript errors

---

## Troubleshooting

### Problem: Data tidak tampil setelah page load

**Symptoms:**
- Table shows loading state forever
- Console shows "Invalid response structure"
- API returns 200 but table empty

**Solutions:**
1. Check response structure in console: `console.log('API Response:', response);`
2. Verify you're checking `response.status` NOT `response.success`:
   ```javascript
   // ❌ WRONG
   if (response.success && response.data) { }

   // ✅ CORRECT
   if (response.status && response.data) { }
   ```
3. Check API endpoint is correct: `/api/[module-path]`
4. Check API controller returns ResultBuilder format

---

### Problem: Action buttons (view, edit, delete) tidak tampil

**Symptoms:**
- Table renders but no action buttons
- Empty action column

**Solutions:**
1. Remove permission checks di `renderTable()`:
   ```javascript
   // ❌ WRONG (if hasPermission not defined)
   ${window.hasPermission('module.view') ? `<button>...` : ''}

   // ✅ CORRECT (show all buttons)
   <button onclick="viewItem(${item.id})">...</button>
   ```
2. Check button HTML syntax - pastikan tidak ada missing quotes atau tags
3. Check console untuk JavaScript errors

---

### Problem: Toast notification tidak muncul setelah create/update/delete

**Symptoms:**
- Operation berhasil tapi tidak ada toast
- Data update tapi silent

**Solutions:**
1. Add `showToast: true` explicitly:
   ```javascript
   await Ajax.create('/api/items', data, {
       successMessage: 'Created successfully',
       showToast: true  // ← Add this
   });
   ```
2. Check Toast component imported: `import Toast from '../../../components/toast.js';`
3. Check `<meta name="csrf-token">` exists in layout head

---

### Problem: Data tidak reload setelah create/update/delete

**Symptoms:**
- Operation berhasil
- Toast muncul
- Tapi table tidak update

**Solutions:**
1. Call `await loadData()` setelah operation:
   ```javascript
   await Ajax.create('/api/items', data, { showToast: true });
   await loadData();  // ← Add this
   ```
2. Check modal closed before reload:
   ```javascript
   itemModal.close();
   this.reset();
   currentItemId = null;
   await loadData();
   ```

---

### Problem: Loading indicator tidak muncul saat filter

**Symptoms:**
- Filter works tapi tidak ada loading state
- User tidak tahu data sedang di-fetch

**Solutions:**
1. Add manual loading state di awal `loadData()`:
   ```javascript
   async function loadData(page = 1) {
       // Show loading FIRST
       $('#tableBody').html(`
           <tr><td colspan="8" class="text-center py-8">
               <span class="loading loading-spinner loading-md"></span>
           </td></tr>
       `);

       // Then fetch data
       const response = await Ajax.get(...);
   }
   ```

---

### Problem: Edit modal shows old data after update

**Symptoms:**
- Update berhasil
- Open edit modal lagi, shows old data

**Solutions:**
1. Reset `currentItemId` after operation:
   ```javascript
   await Ajax.update(`/api/items/${currentItemId}`, data, { showToast: true });

   itemModal.close();
   this.reset();
   currentItemId = null;  // ← Add this
   await loadData();
   ```

---

### Problem: Page refreshes after form submit

**Symptoms:**
- Form submit causes full page reload
- Loses API-driven behavior

**Solutions:**
1. Check `e.preventDefault()` is called:
   ```javascript
   document.getElementById('itemForm').addEventListener('submit', async function(e) {
       e.preventDefault();  // ← Must have this
       // ... rest of code
   });
   ```

---

### Problem: Statistics tidak update dari API

**Symptoms:**
- Table data updates
- Statistics cards still show "..."

**Solutions:**
1. Check `updateStatistics()` is called in `loadData()`:
   ```javascript
   if (response.status && response.data) {
       renderTable(response.data.items);
       renderPagination(response.data.items);
       if (response.data.statistics) {
           updateStatistics(response.data.statistics);  // ← Add this
       }
   }
   ```
2. Check element IDs match:
   ```javascript
   // HTML: <p id="statTotal">...</p>
   // JS: $('#statTotal').text(stats.total);
   ```

---

### Problem: Console shows JavaScript errors

**Common Errors:**

**Error: `itemModal is not defined`**
- Solution: Check modal element exists with correct ID in blade
- DaisyUI modals: `const itemModal = document.getElementById('itemModal');`

**Error: `$ is not a function`**
- Solution: Add `import $ from 'jquery';` at top of file

**Error: `Ajax is not defined`**
- Solution: Add `import Ajax from '../../../utils/ajax.js';` at top of file

**Error: `Cannot read property 'data' of undefined`**
- Solution: Response structure issue, check API returns correct format

---

### Problem: Pagination tidak berfungsi

**Symptoms:**
- Pagination buttons appear
- Click tidak load page berikutnya

**Solutions:**
1. Check `window.loadDataPage` function exists:
   ```javascript
   window.loadDataPage = function(page) {
       loadData(page);
   };
   ```
2. Check onclick calls correct function: `onclick="loadDataPage(2)"`
3. Check `page` parameter passed to API: `/api/items?page=2`

---

### 8. Common Gotchas

#### Response Structure Check:
```javascript
// WRONG ❌
if (response.success && response.data) {
    // ...
}

// CORRECT ✅
if (response.status && response.data) {
    // ...
}
```

#### Pagination Structure:
```javascript
// Paginated response structure:
{
    status: true,        // ← Use 'status', not 'success'
    statusCode: "200",
    message: "...",
    data: {
        items: {
            current_page: 1,
            data: [...],      // ← Actual items array
            first_page_url: "...",
            from: 1,
            last_page: 5,
            last_page_url: "...",
            next_page_url: "...",
            path: "...",
            per_page: 20,
            prev_page_url: null,
            to: 20,
            total: 100
        }
    }
}

// Access items: response.data.items.data or response.data.items
```

#### FormData vs JSON:
```javascript
// For file uploads - use FormData:
const formData = new FormData(form);
await Ajax.post('/api/items', formData);

// For regular data - use JSON:
const data = {
    name: $('#name').val(),
    status: $('#status').val()
};
await Ajax.post('/api/items', data);
```

#### CSRF Token:
```html
<!-- Ensure this is in your layout head -->
<meta name="csrf-token" content="{{ csrf_token() }}">
```

#### Modal References:
```javascript
// DaisyUI modal (no # prefix):
itemModal.showModal();  // ✅ Correct
itemModal.close();      // ✅ Correct

$('#itemModal').show(); // ❌ Wrong
```

#### jQuery Import:
```javascript
import $ from 'jquery';  // ✅ Correct
import jQuery from 'jquery'; // ❌ Wrong
```

#### Global Functions for onclick:
```javascript
// Make function accessible to HTML onclick
window.editItem = async function(id) {
    // ...
};

// HTML can call it:
<button onclick="editItem(123)">Edit</button>
```

---

### 9. Files to Modify

Checklist for [MODULE_NAME]:

1. ✏️ **Blade View** - `resources/views/pages/[module]/index.blade.php`
   - Remove server-side data rendering
   - Add empty table structure with loading state
   - Add pagination container
   - Keep statistics cards (if any)
   - Keep filter form structure
   - Keep modal structures

2. ✏️ **Controller** - `app/Http/Controllers/[Controller].php`
   - Keep only `index()` method
   - Remove show, store, update, destroy methods
   - Remove query building logic
   - Only return view with statistics (optional)

3. ✏️ **JavaScript** - `resources/js/modules/[module]/index.js`
   - Implement loadData() function
   - Implement renderTable() function
   - Implement renderPagination() function
   - Implement all CRUD functions (view, create, edit, delete)
   - Add event listeners for form submit, filters, etc.
   - Use Ajax utility for all requests
   - Use jQuery for DOM manipulation

4. ✅ **API Controller** - `app/Http/Controllers/Api/[ApiController].php`
   - Should already exist
   - Verify index, show, store, update, destroy methods
   - Verify all use ResultBuilder pattern

5. ✅ **API Routes** - `routes/api.php`
   - Should already be configured
   - Verify all CRUD routes exist
   - Verify middleware permissions

6. ✅ **Web Routes** - `routes/web.php`
   - Should already be configured
   - Verify only index route exists

---

### 10. Success Criteria

Module is successfully standardized when:

✅ No server-side data rendering in blade
✅ Statistics load from API (not from controller)
✅ All data loads via AJAX from API
✅ All CRUD operations work through API endpoints
✅ No native fetch/axios/$.ajax() - only Ajax utility used
✅ jQuery used for all DOM manipulation
✅ Loading indicator shows when filtering/loading data
✅ Toast notifications work on create/update/delete
✅ Response structure uses `response.status` (not `response.success`)
✅ Error handling works (try invalid data)
✅ Pagination works (if applicable)
✅ Filters work without page refresh
✅ Action buttons visible and working
✅ Page never refreshes during operations
✅ Modal closes after successful create/update
✅ `currentItemId` resets after operations
✅ Data auto-reloads after create/update/delete
✅ All tests in checklist pass

---

## Example: Marketing Module

### Replace These Values:
- `[MODULE_NAME]` → `Marketing - Coupons`
- `[module_path]` → `marketing/coupons`
- `[module-path]` → `marketing/coupons`
- `[ControllerPath]` → `Marketing`
- `[Controller]` → `CouponsController`
- `[ApiController]` → `CouponApiController`
- `[RepositoryInterface]` → `CouponRepositoryInterface`
- `[module]` → `marketing.coupons`
- `[module-prefix]` → `marketing`

### File Paths:
- View: `resources/views/pages/marketing/coupons/index.blade.php`
- JS: `resources/js/modules/marketing/coupons/index.js`
- Controller: `app/Http/Controllers/Marketing/CouponsController.php`
- API Controller: `app/Http/Controllers/Api/Marketing/CouponApiController.php`

---

## Notes

- This is a one-way migration - once converted to API-driven, don't go back to server-side rendering
- Keep this document updated if new patterns emerge
- All new modules should follow this architecture from the start
- For complex modules with relationships, additional API endpoints may be needed

---

## Reference Files

**Good Examples (Already Implemented):**
- `resources/js/utils/ajax.js` - Complete Ajax utility
- `resources/js/modules/marketing/coupons/index.js` - API-driven pattern (⭐ PRIMARY REFERENCE)
- `app/Http/Controllers/Api/Marketing/CouponApiController.php` - Standardized API controller
- `resources/views/pages/marketing/coupons/index.blade.php` - API-driven blade view

**Copy these patterns for new modules!**

---

## Quick Reference

For faster implementation without full explanations, see:

**[QUICK_API_DRIVEN.md](./QUICK_API_DRIVEN.md)** - Quick reference with copy-paste templates

**When to use which document:**
- **First time implementing?** → Use this document (STANDARDIZE_MODULE_TO_API_DRIVEN.md)
- **Already familiar with pattern?** → Use Quick Reference (QUICK_API_DRIVEN.md)
- **Debugging issues?** → See [Troubleshooting](#troubleshooting) section in this document
