@extends('layouts.app')

@section('title', 'Permissions')
@section('page_title', 'Permission')
@section('page_subtitle', 'Permission Management')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Permission List</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Access Control</li>
            <li class="opacity-80">Permissions</li>
        </ul>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Total Permissions</p>
                    <p class="text-2xl font-semibold mt-1">156</p>
                </div>
                <div class="bg-primary/10 p-3 rounded-lg">
                    <span class="iconify lucide--shield size-5 text-primary"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Active</p>
                    <p class="text-2xl font-semibold mt-1 text-success">142</p>
                </div>
                <div class="bg-success/10 p-3 rounded-lg">
                    <span class="iconify lucide--check-circle size-5 text-success"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Modules</p>
                    <p class="text-2xl font-semibold mt-1">12</p>
                </div>
                <div class="bg-info/10 p-3 rounded-lg">
                    <span class="iconify lucide--layout-grid size-5 text-info"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Permission Groups</p>
                    <p class="text-2xl font-semibold mt-1">8</p>
                </div>
                <div class="bg-warning/10 p-3 rounded-lg">
                    <span class="iconify lucide--layers size-5 text-warning"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body p-0">
            <div class="flex items-center justify-between px-5 pt-5">
                <div class="inline-flex items-center gap-3">
                    <label class="input input-sm">
                        <span class="iconify lucide--search text-base-content/80 size-3.5"></span>
                        <input
                            class="w-24 sm:w-36"
                            placeholder="Search permissions"
                            aria-label="Search permissions"
                            type="search" />
                    </label>
                    <select class="select select-sm w-32" aria-label="Filter by module">
                        <option value="">All Modules</option>
                        <option value="product">Product</option>
                        <option value="order">Order</option>
                        <option value="customer">Customer</option>
                        <option value="marketing">Marketing</option>
                        <option value="report">Report</option>
                        <option value="settings">Settings</option>
                    </select>
                    <select class="select select-sm w-32" aria-label="Filter by action">
                        <option value="">All Actions</option>
                        <option value="view">View</option>
                        <option value="create">Create</option>
                        <option value="update">Update</option>
                        <option value="delete">Delete</option>
                        <option value="export">Export</option>
                    </select>
                </div>
                <div class="inline-flex items-center gap-3">
                    <button class="btn btn-sm btn-ghost">
                        <span class="iconify lucide--refresh-cw size-4"></span>
                        Sync Permissions
                    </button>
                    <a
                        aria-label="Create permission"
                        class="btn btn-primary btn-sm max-sm:btn-square"
                        href="#">
                        <span class="iconify lucide--plus size-4"></span>
                        <span class="hidden sm:inline">Add Permission</span>
                    </a>
                </div>
            </div>

            <div class="mt-4 overflow-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>
                                <input
                                    aria-label="Check all"
                                    class="checkbox checkbox-sm"
                                    type="checkbox" />
                            </th>
                            <th>Permission Name</th>
                            <th>Display Name</th>
                            <th>Module</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Roles Using</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Product View -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap">
                            <th>
                                <input
                                    aria-label="Single check"
                                    class="checkbox checkbox-sm"
                                    type="checkbox" />
                            </th>
                            <td class="font-mono text-sm">product.view</td>
                            <td>View Products</td>
                            <td>
                                <span class="badge badge-sm badge-ghost">Product</span>
                            </td>
                            <td>
                                <span class="badge badge-info badge-sm">View</span>
                            </td>
                            <td class="max-w-xs truncate">Allow viewing product listings and details</td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">Active</span>
                            </td>
                            <td class="text-center">5</td>
                            <td class="text-sm">2024-01-01</td>
                            <td>
                                <div class="inline-flex">
                                    <button
                                        aria-label="Edit permission"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--pencil text-base-content/80 size-4"></span>
                                    </button>
                                    <button
                                        aria-label="View roles"
                                        onclick="showPermissionRoles('product.view')"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--users text-base-content/80 size-4"></span>
                                    </button>
                                    <button
                                        aria-label="Toggle status"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--toggle-right text-base-content/80 size-4"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Product Create -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap">
                            <th>
                                <input
                                    aria-label="Single check"
                                    class="checkbox checkbox-sm"
                                    type="checkbox" />
                            </th>
                            <td class="font-mono text-sm">product.create</td>
                            <td>Create Products</td>
                            <td>
                                <span class="badge badge-sm badge-ghost">Product</span>
                            </td>
                            <td>
                                <span class="badge badge-success badge-sm">Create</span>
                            </td>
                            <td class="max-w-xs truncate">Allow creating new products</td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">Active</span>
                            </td>
                            <td class="text-center">3</td>
                            <td class="text-sm">2024-01-01</td>
                            <td>
                                <div class="inline-flex">
                                    <button
                                        aria-label="Edit permission"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--pencil text-base-content/80 size-4"></span>
                                    </button>
                                    <button
                                        aria-label="View roles"
                                        onclick="showPermissionRoles('product.create')"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--users text-base-content/80 size-4"></span>
                                    </button>
                                    <button
                                        aria-label="Toggle status"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--toggle-right text-base-content/80 size-4"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Product Update -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap">
                            <th>
                                <input
                                    aria-label="Single check"
                                    class="checkbox checkbox-sm"
                                    type="checkbox" />
                            </th>
                            <td class="font-mono text-sm">product.update</td>
                            <td>Update Products</td>
                            <td>
                                <span class="badge badge-sm badge-ghost">Product</span>
                            </td>
                            <td>
                                <span class="badge badge-warning badge-sm">Update</span>
                            </td>
                            <td class="max-w-xs truncate">Allow updating existing products</td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">Active</span>
                            </td>
                            <td class="text-center">4</td>
                            <td class="text-sm">2024-01-01</td>
                            <td>
                                <div class="inline-flex">
                                    <button
                                        aria-label="Edit permission"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--pencil text-base-content/80 size-4"></span>
                                    </button>
                                    <button
                                        aria-label="View roles"
                                        onclick="showPermissionRoles('product.update')"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--users text-base-content/80 size-4"></span>
                                    </button>
                                    <button
                                        aria-label="Toggle status"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--toggle-right text-base-content/80 size-4"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Product Delete -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap">
                            <th>
                                <input
                                    aria-label="Single check"
                                    class="checkbox checkbox-sm"
                                    type="checkbox" />
                            </th>
                            <td class="font-mono text-sm">product.delete</td>
                            <td>Delete Products</td>
                            <td>
                                <span class="badge badge-sm badge-ghost">Product</span>
                            </td>
                            <td>
                                <span class="badge badge-error badge-sm">Delete</span>
                            </td>
                            <td class="max-w-xs truncate">Allow deleting products</td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">Active</span>
                            </td>
                            <td class="text-center">2</td>
                            <td class="text-sm">2024-01-01</td>
                            <td>
                                <div class="inline-flex">
                                    <button
                                        aria-label="Edit permission"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--pencil text-base-content/80 size-4"></span>
                                    </button>
                                    <button
                                        aria-label="View roles"
                                        onclick="showPermissionRoles('product.delete')"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--users text-base-content/80 size-4"></span>
                                    </button>
                                    <button
                                        aria-label="Toggle status"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--toggle-right text-base-content/80 size-4"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Order View -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap">
                            <th>
                                <input
                                    aria-label="Single check"
                                    class="checkbox checkbox-sm"
                                    type="checkbox" />
                            </th>
                            <td class="font-mono text-sm">order.view</td>
                            <td>View Orders</td>
                            <td>
                                <span class="badge badge-sm badge-ghost">Order</span>
                            </td>
                            <td>
                                <span class="badge badge-info badge-sm">View</span>
                            </td>
                            <td class="max-w-xs truncate">Allow viewing order listings and details</td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">Active</span>
                            </td>
                            <td class="text-center">5</td>
                            <td class="text-sm">2024-01-01</td>
                            <td>
                                <div class="inline-flex">
                                    <button
                                        aria-label="Edit permission"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--pencil text-base-content/80 size-4"></span>
                                    </button>
                                    <button
                                        aria-label="View roles"
                                        onclick="showPermissionRoles('order.view')"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--users text-base-content/80 size-4"></span>
                                    </button>
                                    <button
                                        aria-label="Toggle status"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--toggle-right text-base-content/80 size-4"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Settings Update (Inactive Example) -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap opacity-60">
                            <th>
                                <input
                                    aria-label="Single check"
                                    class="checkbox checkbox-sm"
                                    type="checkbox" />
                            </th>
                            <td class="font-mono text-sm">settings.update</td>
                            <td>Update Settings</td>
                            <td>
                                <span class="badge badge-sm badge-ghost">Settings</span>
                            </td>
                            <td>
                                <span class="badge badge-warning badge-sm">Update</span>
                            </td>
                            <td class="max-w-xs truncate">Allow updating system settings</td>
                            <td>
                                <span class="badge badge-error badge-sm badge-soft">Inactive</span>
                            </td>
                            <td class="text-center">1</td>
                            <td class="text-sm">2024-01-01</td>
                            <td>
                                <div class="inline-flex">
                                    <button
                                        aria-label="Edit permission"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--pencil text-base-content/80 size-4"></span>
                                    </button>
                                    <button
                                        aria-label="View roles"
                                        onclick="showPermissionRoles('settings.update')"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--users text-base-content/80 size-4"></span>
                                    </button>
                                    <button
                                        aria-label="Toggle status"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--toggle-left text-base-content/80 size-4"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between p-6">
                <div class="text-base-content/80 hover:text-base-content flex gap-2 text-sm">
                    <span class="hidden sm:inline">Per page</span>
                    <select class="select select-xs w-18" aria-label="Per page">
                        <option value="10">10</option>
                        <option value="20" selected="">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <span class="text-base-content/80 hidden text-sm lg:inline">
                    Showing
                    <span class="text-base-content font-medium">1 to 6</span>
                    of 156 permissions
                </span>
                <div class="inline-flex items-center gap-1">
                    <button class="btn btn-circle sm:btn-sm btn-xs btn-ghost" aria-label="Prev">
                        <span class="iconify lucide--chevron-left"></span>
                    </button>
                    <button class="btn btn-primary btn-circle sm:btn-sm btn-xs">1</button>
                    <button class="btn btn-ghost btn-circle sm:btn-sm btn-xs">2</button>
                    <button class="btn btn-ghost btn-circle sm:btn-sm btn-xs">3</button>
                    <button class="btn btn-circle sm:btn-sm btn-xs btn-ghost" aria-label="Next">
                        <span class="iconify lucide--chevron-right"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Roles Modal -->
    <dialog id="view_roles_modal" class="modal">
        <div class="modal-box max-w-2xl">
            <div class="flex items-center justify-between text-lg font-medium">
                <span>Roles using <span id="permission_name_display" class="badge badge-primary"></span> permission</span>
                <form method="dialog">
                    <button class="btn btn-sm btn-ghost btn-circle" aria-label="Close modal">
                        <span class="iconify lucide--x size-4"></span>
                    </button>
                </form>
            </div>
            <div class="mt-4 max-h-96 overflow-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Role Name</th>
                            <th>Display Name</th>
                            <th>Priority</th>
                            <th>Users Count</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="permission_roles_list">
                        <!-- Dynamic content -->
                    </tbody>
                </table>
            </div>
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn btn-sm">Close</button>
                </form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</div>
@endsection

@section('customjs')
<script>
function showPermissionRoles(permissionName) {
    document.getElementById('permission_name_display').textContent = permissionName;

    // Sample data - replace with actual API call
    const sampleRoles = {
        'product.view': [
            { name: 'super_admin', display: 'Super Administrator', priority: 100, users: 1 },
            { name: 'admin', display: 'Administrator', priority: 90, users: 3 },
            { name: 'store_manager', display: 'Store Manager', priority: 80, users: 2 },
            { name: 'content_editor', display: 'Content Editor', priority: 60, users: 4 },
            { name: 'cs_staff', display: 'Customer Service', priority: 50, users: 5 }
        ],
        'product.create': [
            { name: 'super_admin', display: 'Super Administrator', priority: 100, users: 1 },
            { name: 'admin', display: 'Administrator', priority: 90, users: 3 },
            { name: 'store_manager', display: 'Store Manager', priority: 80, users: 2 }
        ],
        'product.delete': [
            { name: 'super_admin', display: 'Super Administrator', priority: 100, users: 1 },
            { name: 'admin', display: 'Administrator', priority: 90, users: 3 }
        ]
    };

    const roles = sampleRoles[permissionName] || [];
    let html = '';

    roles.forEach(role => {
        html += `
            <tr>
                <td class="font-mono text-sm">${role.name}</td>
                <td>${role.display}</td>
                <td><span class="badge badge-sm">${role.priority}</span></td>
                <td class="text-center">${role.users}</td>
                <td>
                    <button class="btn btn-xs btn-ghost">Remove</button>
                </td>
            </tr>
        `;
    });

    document.getElementById('permission_roles_list').innerHTML = html;
    view_roles_modal.showModal();
}
</script>
@endsection