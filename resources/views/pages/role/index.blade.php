@extends('layouts.app')

@section('title', 'Roles')
@section('page_title', 'Role')
@section('page_subtitle', 'Role Management')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Role Management</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Access Control</li>
            <li class="opacity-80">Roles</li>
        </ul>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Total Roles</p>
                    <p class="text-2xl font-semibold mt-1">9</p>
                    <p class="text-xs text-base-content/60 mt-1">All defined roles</p>
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
                    <p class="text-sm text-base-content/70">Active Roles</p>
                    <p class="text-2xl font-semibold mt-1 text-success">9</p>
                    <p class="text-xs text-base-content/60 mt-1">Currently in use</p>
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
                    <p class="text-sm text-base-content/70">System Roles</p>
                    <p class="text-2xl font-semibold mt-1 text-warning">4</p>
                    <p class="text-xs text-base-content/60 mt-1">Protected roles</p>
                </div>
                <div class="bg-warning/10 p-3 rounded-lg">
                    <span class="iconify lucide--lock size-5 text-warning"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Users Assigned</p>
                    <p class="text-2xl font-semibold mt-1 text-info">1,267</p>
                    <p class="text-xs text-base-content/60 mt-1">Total assignments</p>
                </div>
                <div class="bg-info/10 p-3 rounded-lg">
                    <span class="iconify lucide--users size-5 text-info"></span>
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
                            placeholder="Search roles"
                            aria-label="Search roles"
                            type="search" />
                    </label>
                    <select class="select select-sm w-32" aria-label="Filter by status">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="inline-flex items-center gap-3">
                    <a
                        aria-label="Create role link"
                        class="btn btn-primary btn-sm max-sm:btn-square"
                        href="{{ route('role.create') }}">
                        <span class="iconify lucide--plus size-4"></span>
                        <span class="hidden sm:inline">Add Role</span>
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
                            <th>Role Name</th>
                            <th>Display Name</th>
                            <th>Description</th>
                            <th>Priority</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Total Users</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Super Admin -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap">
                            <th>
                                <input
                                    aria-label="Single check"
                                    class="checkbox checkbox-sm"
                                    type="checkbox" disabled />
                            </th>
                            <td class="font-mono text-sm">super_admin</td>
                            <td>Super Administrator</td>
                            <td class="max-w-xs truncate">Full system access with all permissions</td>
                            <td>
                                <span class="badge badge-primary badge-sm">100</span>
                            </td>
                            <td>
                                <span class="badge badge-warning badge-sm badge-soft">System</span>
                            </td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">Active</span>
                            </td>
                            <td class="text-center">1</td>
                            <td class="text-sm">2024-01-01</td>
                            <td>
                                <div class="inline-flex">
                                    <a
                                        aria-label="View role permissions"
                                        class="btn btn-square btn-ghost btn-sm"
                                        href="#">
                                        <span class="iconify lucide--shield text-base-content/80 size-4"></span>
                                    </a>
                                    <a
                                        aria-label="Edit role"
                                        class="btn btn-square btn-ghost btn-sm"
                                        href="#">
                                        <span class="iconify lucide--pencil text-base-content/80 size-4"></span>
                                    </a>
                                    <button
                                        aria-label="View users"
                                        onclick="showRoleUsers('super_admin')"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--users text-base-content/80 size-4"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Administrator -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap">
                            <th>
                                <input
                                    aria-label="Single check"
                                    class="checkbox checkbox-sm"
                                    type="checkbox" disabled />
                            </th>
                            <td class="font-mono text-sm">admin</td>
                            <td>Administrator</td>
                            <td class="max-w-xs truncate">Admin panel access with limited permissions</td>
                            <td>
                                <span class="badge badge-primary badge-sm">90</span>
                            </td>
                            <td>
                                <span class="badge badge-warning badge-sm badge-soft">System</span>
                            </td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">Active</span>
                            </td>
                            <td class="text-center">3</td>
                            <td class="text-sm">2024-01-01</td>
                            <td>
                                <div class="inline-flex">
                                    <a
                                        aria-label="View role permissions"
                                        class="btn btn-square btn-ghost btn-sm"
                                        href="#">
                                        <span class="iconify lucide--shield text-base-content/80 size-4"></span>
                                    </a>
                                    <a
                                        aria-label="Edit role"
                                        class="btn btn-square btn-ghost btn-sm"
                                        href="#">
                                        <span class="iconify lucide--pencil text-base-content/80 size-4"></span>
                                    </a>
                                    <button
                                        aria-label="View users"
                                        onclick="showRoleUsers('admin')"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--users text-base-content/80 size-4"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Store Manager -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap">
                            <th>
                                <input
                                    aria-label="Single check"
                                    class="checkbox checkbox-sm"
                                    type="checkbox" />
                            </th>
                            <td class="font-mono text-sm">store_manager</td>
                            <td>Store Manager</td>
                            <td class="max-w-xs truncate">Manage products and orders</td>
                            <td>
                                <span class="badge badge-info badge-sm">80</span>
                            </td>
                            <td>
                                <span class="badge badge-ghost badge-sm">Custom</span>
                            </td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">Active</span>
                            </td>
                            <td class="text-center">2</td>
                            <td class="text-sm">2024-02-15</td>
                            <td>
                                <div class="inline-flex">
                                    <a
                                        aria-label="View role permissions"
                                        class="btn btn-square btn-ghost btn-sm"
                                        href="#">
                                        <span class="iconify lucide--shield text-base-content/80 size-4"></span>
                                    </a>
                                    <a
                                        aria-label="Edit role"
                                        class="btn btn-square btn-ghost btn-sm"
                                        href="#">
                                        <span class="iconify lucide--pencil text-base-content/80 size-4"></span>
                                    </a>
                                    <button
                                        aria-label="View users"
                                        onclick="showRoleUsers('store_manager')"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--users text-base-content/80 size-4"></span>
                                    </button>
                                    <button
                                        aria-label="Delete role"
                                        onclick="confirmDeleteRole(3)"
                                        class="btn btn-square btn-error btn-outline btn-sm border-transparent">
                                        <span class="iconify lucide--trash size-4"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Content Editor -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap">
                            <th>
                                <input
                                    aria-label="Single check"
                                    class="checkbox checkbox-sm"
                                    type="checkbox" />
                            </th>
                            <td class="font-mono text-sm">content_editor</td>
                            <td>Content Editor</td>
                            <td class="max-w-xs truncate">Manage product content & descriptions</td>
                            <td>
                                <span class="badge badge-info badge-sm">60</span>
                            </td>
                            <td>
                                <span class="badge badge-ghost badge-sm">Custom</span>
                            </td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">Active</span>
                            </td>
                            <td class="text-center">4</td>
                            <td class="text-sm">2024-03-10</td>
                            <td>
                                <div class="inline-flex">
                                    <a
                                        aria-label="View role permissions"
                                        class="btn btn-square btn-ghost btn-sm"
                                        href="#">
                                        <span class="iconify lucide--shield text-base-content/80 size-4"></span>
                                    </a>
                                    <a
                                        aria-label="Edit role"
                                        class="btn btn-square btn-ghost btn-sm"
                                        href="#">
                                        <span class="iconify lucide--pencil text-base-content/80 size-4"></span>
                                    </a>
                                    <button
                                        aria-label="View users"
                                        onclick="showRoleUsers('content_editor')"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--users text-base-content/80 size-4"></span>
                                    </button>
                                    <button
                                        aria-label="Delete role"
                                        onclick="confirmDeleteRole(4)"
                                        class="btn btn-square btn-error btn-outline btn-sm border-transparent">
                                        <span class="iconify lucide--trash size-4"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Customer Service -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap">
                            <th>
                                <input
                                    aria-label="Single check"
                                    class="checkbox checkbox-sm"
                                    type="checkbox" />
                            </th>
                            <td class="font-mono text-sm">cs_staff</td>
                            <td>Customer Service</td>
                            <td class="max-w-xs truncate">Handle customer inquiries & orders</td>
                            <td>
                                <span class="badge badge-info badge-sm">50</span>
                            </td>
                            <td>
                                <span class="badge badge-ghost badge-sm">Custom</span>
                            </td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">Active</span>
                            </td>
                            <td class="text-center">5</td>
                            <td class="text-sm">2024-03-20</td>
                            <td>
                                <div class="inline-flex">
                                    <a
                                        aria-label="View role permissions"
                                        class="btn btn-square btn-ghost btn-sm"
                                        href="#">
                                        <span class="iconify lucide--shield text-base-content/80 size-4"></span>
                                    </a>
                                    <a
                                        aria-label="Edit role"
                                        class="btn btn-square btn-ghost btn-sm"
                                        href="#">
                                        <span class="iconify lucide--pencil text-base-content/80 size-4"></span>
                                    </a>
                                    <button
                                        aria-label="View users"
                                        onclick="showRoleUsers('cs_staff')"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--users text-base-content/80 size-4"></span>
                                    </button>
                                    <button
                                        aria-label="Delete role"
                                        onclick="confirmDeleteRole(5)"
                                        class="btn btn-square btn-error btn-outline btn-sm border-transparent">
                                        <span class="iconify lucide--trash size-4"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Customer -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap">
                            <th>
                                <input
                                    aria-label="Single check"
                                    class="checkbox checkbox-sm"
                                    type="checkbox" disabled />
                            </th>
                            <td class="font-mono text-sm">customer</td>
                            <td>Customer</td>
                            <td class="max-w-xs truncate">Customer account access</td>
                            <td>
                                <span class="badge badge-ghost badge-sm">10</span>
                            </td>
                            <td>
                                <span class="badge badge-warning badge-sm badge-soft">System</span>
                            </td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">Active</span>
                            </td>
                            <td class="text-center">1,245</td>
                            <td class="text-sm">2024-01-01</td>
                            <td>
                                <div class="inline-flex">
                                    <a
                                        aria-label="View role permissions"
                                        class="btn btn-square btn-ghost btn-sm"
                                        href="#">
                                        <span class="iconify lucide--shield text-base-content/80 size-4"></span>
                                    </a>
                                    <a
                                        aria-label="Edit role"
                                        class="btn btn-square btn-ghost btn-sm"
                                        href="#">
                                        <span class="iconify lucide--pencil text-base-content/80 size-4"></span>
                                    </a>
                                    <button
                                        aria-label="View users"
                                        onclick="showRoleUsers('customer')"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span class="iconify lucide--users text-base-content/80 size-4"></span>
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
                    of 6 roles
                </span>
                <div class="inline-flex items-center gap-1">
                    <button class="btn btn-circle sm:btn-sm btn-xs btn-ghost" aria-label="Prev">
                        <span class="iconify lucide--chevron-left"></span>
                    </button>
                    <button class="btn btn-primary btn-circle sm:btn-sm btn-xs">1</button>
                    <button class="btn btn-circle sm:btn-sm btn-xs btn-ghost" aria-label="Next">
                        <span class="iconify lucide--chevron-right"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Role Modal -->
    <dialog id="delete_role_modal" class="modal">
        <div class="modal-box">
            <div class="flex items-center justify-between text-lg font-medium">
                Confirm Delete Role
                <form method="dialog">
                    <button class="btn btn-sm btn-ghost btn-circle" aria-label="Close modal">
                        <span class="iconify lucide--x size-4"></span>
                    </button>
                </form>
            </div>
            <p class="py-4">
                You are about to delete this role. All users with this role will lose their permissions.
                Would you like to proceed?
            </p>
            <div class="alert alert-warning">
                <span class="iconify lucide--alert-triangle size-5"></span>
                <span>This action cannot be undone.</span>
            </div>
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn btn-ghost btn-sm">Cancel</button>
                </form>
                <form method="dialog">
                    <button class="btn btn-sm btn-error">Yes, delete it</button>
                </form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <!-- View Users Modal -->
    <dialog id="view_users_modal" class="modal">
        <div class="modal-box max-w-3xl">
            <div class="flex items-center justify-between text-lg font-medium">
                <span>Users with <span id="role_name_display" class="badge badge-primary"></span> role</span>
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
                            <th>Name</th>
                            <th>Email</th>
                            <th>Assigned At</th>
                            <th>Assigned By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="role_users_list">
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
function confirmDeleteRole(roleId) {
    delete_role_modal.showModal();
}

function showRoleUsers(roleName) {
    document.getElementById('role_name_display').textContent = roleName;

    // Sample data - replace with actual API call
    const sampleUsers = {
        'super_admin': [
            { name: 'Admin User', email: 'admin@minimoda.com', assigned_at: '2024-01-01', assigned_by: 'System' }
        ],
        'admin': [
            { name: 'John Doe', email: 'john@minimoda.com', assigned_at: '2024-02-15', assigned_by: 'Admin User' },
            { name: 'Jane Smith', email: 'jane@minimoda.com', assigned_at: '2024-03-01', assigned_by: 'Admin User' },
            { name: 'Bob Wilson', email: 'bob@minimoda.com', assigned_at: '2024-03-20', assigned_by: 'Admin User' }
        ],
        'store_manager': [
            { name: 'Alice Manager', email: 'alice@minimoda.com', assigned_at: '2024-04-01', assigned_by: 'Admin User' },
            { name: 'Charlie Brown', email: 'charlie@minimoda.com', assigned_at: '2024-04-15', assigned_by: 'Admin User' }
        ],
        'content_editor': [
            { name: 'David Editor', email: 'david@minimoda.com', assigned_at: '2024-03-10', assigned_by: 'Manager User' },
            { name: 'Eva Content', email: 'eva@minimoda.com', assigned_at: '2024-03-25', assigned_by: 'Manager User' },
            { name: 'Frank Writer', email: 'frank@minimoda.com', assigned_at: '2024-04-05', assigned_by: 'Manager User' },
            { name: 'Grace Author', email: 'grace@minimoda.com', assigned_at: '2024-04-20', assigned_by: 'Manager User' }
        ],
        'cs_staff': [
            { name: 'Helen Support', email: 'helen@minimoda.com', assigned_at: '2024-03-20', assigned_by: 'Manager User' },
            { name: 'Ivan Helper', email: 'ivan@minimoda.com', assigned_at: '2024-04-01', assigned_by: 'Manager User' },
            { name: 'Julia Service', email: 'julia@minimoda.com', assigned_at: '2024-04-10', assigned_by: 'Manager User' },
            { name: 'Kevin Care', email: 'kevin@minimoda.com', assigned_at: '2024-04-15', assigned_by: 'Manager User' },
            { name: 'Laura Assist', email: 'laura@minimoda.com', assigned_at: '2024-04-25', assigned_by: 'Manager User' }
        ],
        'customer': [
            { name: 'Customer 1', email: 'customer1@gmail.com', assigned_at: '2024-01-15', assigned_by: 'System' },
            { name: 'Customer 2', email: 'customer2@gmail.com', assigned_at: '2024-01-20', assigned_by: 'System' },
            { name: 'Customer 3', email: 'customer3@gmail.com', assigned_at: '2024-01-25', assigned_by: 'System' }
        ]
    };

    const users = sampleUsers[roleName] || [];
    let html = '';

    users.forEach(user => {
        html += `
            <tr>
                <td>${user.name}</td>
                <td>${user.email}</td>
                <td>${user.assigned_at}</td>
                <td>${user.assigned_by}</td>
                <td>
                    <button class="btn btn-xs btn-ghost">Remove</button>
                </td>
            </tr>
        `;
    });

    document.getElementById('role_users_list').innerHTML = html;
    view_users_modal.showModal();
}
</script>
@endsection