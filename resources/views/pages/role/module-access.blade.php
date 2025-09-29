@extends('layouts.app')

@section('title', 'Module Access')
@section('page_title', 'Role')
@section('page_subtitle', 'Module Access Management')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Module Access for <span class="badge badge-primary badge-lg">Store Manager</span></p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Management</li>
            <li><a href="{{ route('role.index') }}">Roles</a></li>
            <li class="opacity-80">Module Access</li>
        </ul>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-6 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Total Modules</p>
                    <p class="text-2xl font-semibold mt-1">15</p>
                </div>
                <div class="bg-primary/10 p-3 rounded-lg">
                    <span class="iconify lucide--layout-grid text-primary size-6"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Accessible</p>
                    <p class="text-2xl font-semibold mt-1 text-success">12</p>
                </div>
                <div class="bg-success/10 p-3 rounded-lg">
                    <span class="iconify lucide--check-circle text-success size-6"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Restricted</p>
                    <p class="text-2xl font-semibold mt-1 text-error">3</p>
                </div>
                <div class="bg-error/10 p-3 rounded-lg">
                    <span class="iconify lucide--x-circle text-error size-6"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Permissions</p>
                    <p class="text-2xl font-semibold mt-1">45</p>
                </div>
                <div class="bg-info/10 p-3 rounded-lg">
                    <span class="iconify lucide--shield text-info size-6"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Module Access Table -->
<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body p-0">
            <div class="flex items-center justify-between px-5 pt-5">
                <div class="inline-flex items-center gap-3">
                    <label class="input input-sm">
                        <span class="iconify lucide--search text-base-content/80 size-3.5"></span>
                        <input
                            class="w-24 sm:w-36"
                            placeholder="Search modules"
                            aria-label="Search modules"
                            type="search" />
                    </label>
                    <select class="select select-sm w-32" aria-label="Filter modules">
                        <option value="">All Modules</option>
                        <option value="accessible">Accessible</option>
                        <option value="restricted">Restricted</option>
                    </select>
                </div>
                <div class="inline-flex items-center gap-3">
                    <button class="btn btn-sm btn-ghost">
                        <span class="iconify lucide--refresh-cw size-4"></span>
                        Reset to Default
                    </button>
                    <button class="btn btn-primary btn-sm">
                        <span class="iconify lucide--save size-4"></span>
                        Save Changes
                    </button>
                </div>
            </div>

            <div class="mt-4 overflow-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Module</th>
                            <th class="text-center">
                                <div class="flex items-center gap-1">
                                    <input type="checkbox" class="checkbox checkbox-sm" id="check-all-view" />
                                    <label for="check-all-view">View</label>
                                </div>
                            </th>
                            <th class="text-center">
                                <div class="flex items-center gap-1">
                                    <input type="checkbox" class="checkbox checkbox-sm" id="check-all-create" />
                                    <label for="check-all-create">Create</label>
                                </div>
                            </th>
                            <th class="text-center">
                                <div class="flex items-center gap-1">
                                    <input type="checkbox" class="checkbox checkbox-sm" id="check-all-update" />
                                    <label for="check-all-update">Update</label>
                                </div>
                            </th>
                            <th class="text-center">
                                <div class="flex items-center gap-1">
                                    <input type="checkbox" class="checkbox checkbox-sm" id="check-all-delete" />
                                    <label for="check-all-delete">Delete</label>
                                </div>
                            </th>
                            <th class="text-center">
                                <div class="flex items-center gap-1">
                                    <input type="checkbox" class="checkbox checkbox-sm" id="check-all-export" />
                                    <label for="check-all-export">Export</label>
                                </div>
                            </th>
                            <th>Custom Permissions</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dashboard -->
                        <tr class="hover:bg-base-200/40">
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--layout-dashboard text-base-content/70"></span>
                                    <div>
                                        <div class="font-medium">Dashboard</div>
                                        <div class="text-xs text-base-content/60">/admin/dashboard</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm" disabled />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm" disabled />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm" disabled />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td>
                                <button class="btn btn-xs btn-ghost">Configure</button>
                            </td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">Accessible</span>
                            </td>
                        </tr>

                        <!-- Product Management (Parent) -->
                        <tr class="hover:bg-base-200/40 font-medium bg-base-200/20">
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--package text-base-content/70"></span>
                                    <div>
                                        <div class="font-medium">Product Management</div>
                                        <div class="text-xs text-base-content/60">/admin/products</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-error" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td>
                                <button class="btn btn-xs btn-ghost">Configure</button>
                            </td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">Accessible</span>
                            </td>
                        </tr>

                        <!-- Products (Child) -->
                        <tr class="hover:bg-base-200/40">
                            <td class="pl-10">
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--chevron-right text-base-content/50 size-3"></span>
                                    <div>
                                        <div>Products</div>
                                        <div class="text-xs text-base-content/60">/admin/products/list</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-error" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td>
                                <span class="text-xs text-base-content/60">Inherited</span>
                            </td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">Accessible</span>
                            </td>
                        </tr>

                        <!-- Categories (Child) -->
                        <tr class="hover:bg-base-200/40">
                            <td class="pl-10">
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--chevron-right text-base-content/50 size-3"></span>
                                    <div>
                                        <div>Categories</div>
                                        <div class="text-xs text-base-content/60">/admin/categories</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-error" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-error" />
                            </td>
                            <td>
                                <span class="text-xs text-base-content/60">Inherited</span>
                            </td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">Accessible</span>
                            </td>
                        </tr>

                        <!-- Brands (Child) -->
                        <tr class="hover:bg-base-200/40">
                            <td class="pl-10">
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--chevron-right text-base-content/50 size-3"></span>
                                    <div>
                                        <div>Brands</div>
                                        <div class="text-xs text-base-content/60">/admin/brands</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-error" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-error" />
                            </td>
                            <td>
                                <span class="text-xs text-base-content/60">Inherited</span>
                            </td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">Accessible</span>
                            </td>
                        </tr>

                        <!-- Order Management -->
                        <tr class="hover:bg-base-200/40 font-medium bg-base-200/20">
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--shopping-cart text-base-content/70"></span>
                                    <div>
                                        <div class="font-medium">Order Management</div>
                                        <div class="text-xs text-base-content/60">/admin/orders</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-error" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-error" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td>
                                <button class="btn btn-xs btn-ghost">Configure</button>
                            </td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">Accessible</span>
                            </td>
                        </tr>

                        <!-- Customer Management -->
                        <tr class="hover:bg-base-200/40">
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--users text-base-content/70"></span>
                                    <div>
                                        <div class="font-medium">Customer Management</div>
                                        <div class="text-xs text-base-content/60">/admin/customers</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-error" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-error" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-error" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td>
                                <button class="btn btn-xs btn-ghost">Configure</button>
                            </td>
                            <td>
                                <span class="badge badge-warning badge-sm badge-soft">Limited</span>
                            </td>
                        </tr>

                        <!-- Marketing -->
                        <tr class="hover:bg-base-200/40 font-medium bg-base-200/20">
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--megaphone text-base-content/70"></span>
                                    <div>
                                        <div class="font-medium">Marketing</div>
                                        <div class="text-xs text-base-content/60">/admin/marketing</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-error" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-error" />
                            </td>
                            <td>
                                <button class="btn btn-xs btn-ghost">Configure</button>
                            </td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">Accessible</span>
                            </td>
                        </tr>

                        <!-- Reports -->
                        <tr class="hover:bg-base-200/40">
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--bar-chart text-base-content/70"></span>
                                    <div>
                                        <div class="font-medium">Reports</div>
                                        <div class="text-xs text-base-content/60">/admin/reports</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm" disabled />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm" disabled />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm" disabled />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-success" checked />
                            </td>
                            <td>
                                <button class="btn btn-xs btn-ghost">Configure</button>
                            </td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">Accessible</span>
                            </td>
                        </tr>

                        <!-- Settings (Restricted) -->
                        <tr class="hover:bg-base-200/40 opacity-60">
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--settings text-base-content/70"></span>
                                    <div>
                                        <div class="font-medium">Settings</div>
                                        <div class="text-xs text-base-content/60">/admin/settings</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-error" disabled />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-error" disabled />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-error" disabled />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-error" disabled />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-error" disabled />
                            </td>
                            <td>
                                <span class="text-xs text-base-content/60">No Access</span>
                            </td>
                            <td>
                                <span class="badge badge-error badge-sm badge-soft">Restricted</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Custom Permissions Modal -->
    <dialog id="custom_permissions_modal" class="modal">
        <div class="modal-box max-w-2xl">
            <div class="flex items-center justify-between text-lg font-medium">
                Custom Permissions - Product Management
                <form method="dialog">
                    <button class="btn btn-sm btn-ghost btn-circle" aria-label="Close modal">
                        <span class="iconify lucide--x size-4"></span>
                    </button>
                </form>
            </div>

            <div class="mt-4">
                <p class="text-sm text-base-content/70 mb-4">Configure additional module-specific permissions:</p>

                <div class="space-y-3">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" class="checkbox checkbox-sm" checked />
                        <span>Can manage product variants</span>
                    </label>
                    <label class="flex items-center gap-3">
                        <input type="checkbox" class="checkbox checkbox-sm" checked />
                        <span>Can set featured products</span>
                    </label>
                    <label class="flex items-center gap-3">
                        <input type="checkbox" class="checkbox checkbox-sm" />
                        <span>Can manage product pricing</span>
                    </label>
                    <label class="flex items-center gap-3">
                        <input type="checkbox" class="checkbox checkbox-sm" checked />
                        <span>Can upload product images</span>
                    </label>
                    <label class="flex items-center gap-3">
                        <input type="checkbox" class="checkbox checkbox-sm" />
                        <span>Can delete product reviews</span>
                    </label>
                    <label class="flex items-center gap-3">
                        <input type="checkbox" class="checkbox checkbox-sm" checked />
                        <span>Can manage inventory</span>
                    </label>
                    <label class="flex items-center gap-3">
                        <input type="checkbox" class="checkbox checkbox-sm" />
                        <span>Can approve product changes</span>
                    </label>
                </div>

                <div class="mt-6">
                    <label class="form-control">
                        <div class="label">
                            <span class="label-text">Additional JSON permissions (advanced)</span>
                        </div>
                        <textarea class="textarea textarea-bordered h-24 font-mono text-xs"
                                  placeholder='{"can_bulk_import": false, "max_products_per_day": 50}'>{"can_bulk_import": false, "max_products_per_day": 50, "can_manage_seo": true}</textarea>
                    </label>
                </div>
            </div>

            <div class="modal-action">
                <form method="dialog">
                    <button class="btn btn-ghost btn-sm">Cancel</button>
                </form>
                <button class="btn btn-primary btn-sm">Apply Permissions</button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <!-- Info Alert -->
    <div class="mt-6">
        <div class="alert">
            <span class="iconify lucide--info size-5"></span>
            <div>
                <h3 class="font-bold">Module Access Information</h3>
                <div class="text-sm">
                    <ul class="list-disc list-inside mt-1">
                        <li>Changes to module access will take effect immediately after saving</li>
                        <li>Child modules inherit permissions from parent modules by default</li>
                        <li>Custom permissions allow fine-grained control for specific module features</li>
                        <li>Users with this role will need to re-login for changes to take effect</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customjs')
<script>
// Handle check all functionality
document.querySelectorAll('[id^="check-all-"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const permission = this.id.replace('check-all-', '');
        const columnIndex = ['view', 'create', 'update', 'delete', 'export'].indexOf(permission) + 1;

        document.querySelectorAll(`tbody tr td:nth-child(${columnIndex + 1}) input[type="checkbox"]:not(:disabled)`).forEach(cb => {
            cb.checked = this.checked;
        });
    });
});

// Handle configure button clicks
document.querySelectorAll('button:contains("Configure")').forEach(btn => {
    btn.addEventListener('click', function() {
        custom_permissions_modal.showModal();
    });
});

// Update row status based on permissions
function updateRowStatus(row) {
    const checkboxes = row.querySelectorAll('input[type="checkbox"]:not(:disabled)');
    const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
    const statusBadge = row.querySelector('.badge');

    if (checkedCount === 0) {
        statusBadge.className = 'badge badge-error badge-sm badge-soft';
        statusBadge.textContent = 'Restricted';
    } else if (checkedCount < checkboxes.length) {
        statusBadge.className = 'badge badge-warning badge-sm badge-soft';
        statusBadge.textContent = 'Limited';
    } else {
        statusBadge.className = 'badge badge-success badge-sm badge-soft';
        statusBadge.textContent = 'Accessible';
    }
}

// Add change listeners to all permission checkboxes
document.querySelectorAll('tbody input[type="checkbox"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        updateRowStatus(this.closest('tr'));
    });
});
</script>
@endsection