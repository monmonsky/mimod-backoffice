@extends('layouts.app')

@section('title', 'Activity Logs')
@section('page_title', 'Activity Logs')
@section('page_subtitle', 'System Activity Monitoring')

@section('content')
<x-page-header
    title="Activity Logs"
    :breadcrumbs="[
        ['label' => 'Nexus', 'url' => route('dashboard')],
        ['label' => 'Access Control'],
        ['label' => 'Activity Logs']
    ]"
/>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Total Activities</p>
                    <p class="text-2xl font-semibold mt-1">12,543</p>
                    <p class="text-xs text-base-content/60 mt-1">Last 30 days</p>
                </div>
                <div class="bg-primary/10 p-3 rounded-lg">
                    <span class="iconify lucide--activity size-5 text-primary"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">User Actions</p>
                    <p class="text-2xl font-semibold mt-1 text-info">8,321</p>
                    <p class="text-xs text-base-content/60 mt-1">↑ 12% from yesterday</p>
                </div>
                <div class="bg-info/10 p-3 rounded-lg">
                    <span class="iconify lucide--user-check size-5 text-info"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">System Events</p>
                    <p class="text-2xl font-semibold mt-1 text-success">3,215</p>
                    <p class="text-xs text-base-content/60 mt-1">Automated</p>
                </div>
                <div class="bg-success/10 p-3 rounded-lg">
                    <span class="iconify lucide--server size-5 text-success"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Warnings</p>
                    <p class="text-2xl font-semibold mt-1 text-warning">7</p>
                    <p class="text-xs text-base-content/60 mt-1">Needs attention</p>
                </div>
                <div class="bg-warning/10 p-3 rounded-lg">
                    <span class="iconify lucide--alert-triangle size-5 text-warning"></span>
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
                            placeholder="Search logs"
                            aria-label="Search logs"
                            type="search" />
                    </label>
                    <select class="select select-sm w-32" aria-label="Filter by actor">
                        <option value="">All Actors</option>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                        <option value="system">System</option>
                    </select>
                    <select class="select select-sm w-32" aria-label="Filter by action">
                        <option value="">All Actions</option>
                        <option value="created">Created</option>
                        <option value="updated">Updated</option>
                        <option value="deleted">Deleted</option>
                        <option value="viewed">Viewed</option>
                        <option value="login">Login</option>
                        <option value="logout">Logout</option>
                    </select>
                    <input type="date" class="input input-sm w-32" aria-label="Date filter" />
                </div>
                <div class="inline-flex items-center gap-3">
                    @if(hasPermission('access-control.activity-logs.view'))
                    <button class="btn btn-sm btn-ghost">
                        <span class="iconify lucide--download size-4"></span>
                        Export
                    </button>
                    @endif

                    @if(hasPermission('access-control.activity-logs.delete'))
                    <button onclick="clearLogs()" class="btn btn-error btn-sm">
                        <span class="iconify lucide--trash size-4"></span>
                        Clear Old Logs
                    </button>
                    @endif
                </div>
            </div>

            <div class="mt-4 overflow-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Actor</th>
                            <th>Action</th>
                            <th>Object</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Login Activity -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap">
                            <td>
                                <div>
                                    <div class="text-sm">2024-12-29 10:45:23</div>
                                    <div class="text-xs text-base-content/60">2 mins ago</div>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-info badge-sm">User</span>
                                    <span class="text-sm">john.doe@minimoda.com</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">user.login</span>
                            </td>
                            <td>
                                <span class="text-sm">Authentication</span>
                            </td>
                            <td>
                                <span class="font-mono text-xs">192.168.1.100</span>
                            </td>
                            <td class="max-w-xs truncate text-xs">
                                Mozilla/5.0 (Windows NT 10.0; Win64)
                            </td>
                            <td>
                                <button onclick="showDetails('login1')" class="btn btn-xs btn-ghost">
                                    <span class="iconify lucide--eye size-3"></span>
                                    View
                                </button>
                            </td>
                        </tr>

                        <!-- Product Created -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap">
                            <td>
                                <div>
                                    <div class="text-sm">2024-12-29 10:42:15</div>
                                    <div class="text-xs text-base-content/60">5 mins ago</div>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-warning badge-sm">Admin</span>
                                    <span class="text-sm">admin@minimoda.com</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">product.created</span>
                            </td>
                            <td>
                                <div>
                                    <div class="text-sm">Product #P001234</div>
                                    <div class="text-xs text-base-content/60">Kids T-Shirt Blue</div>
                                </div>
                            </td>
                            <td>
                                <span class="font-mono text-xs">192.168.1.50</span>
                            </td>
                            <td class="max-w-xs truncate text-xs">
                                Chrome/120.0.0.0 Safari/537.36
                            </td>
                            <td>
                                <button onclick="showDetails('product1')" class="btn btn-xs btn-ghost">
                                    <span class="iconify lucide--eye size-3"></span>
                                    View
                                </button>
                            </td>
                        </tr>

                        <!-- Order Updated -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap">
                            <td>
                                <div>
                                    <div class="text-sm">2024-12-29 10:38:45</div>
                                    <div class="text-xs text-base-content/60">9 mins ago</div>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-info badge-sm">User</span>
                                    <span class="text-sm">sarah.cs@minimoda.com</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-warning badge-sm badge-soft">order.updated</span>
                            </td>
                            <td>
                                <div>
                                    <div class="text-sm">Order #ORD-20241229-001</div>
                                    <div class="text-xs text-base-content/60">Status: Processing → Shipped</div>
                                </div>
                            </td>
                            <td>
                                <span class="font-mono text-xs">192.168.1.75</span>
                            </td>
                            <td class="max-w-xs truncate text-xs">
                                Firefox/121.0 (Windows)
                            </td>
                            <td>
                                <button onclick="showDetails('order1')" class="btn btn-xs btn-ghost">
                                    <span class="iconify lucide--eye size-3"></span>
                                    View
                                </button>
                            </td>
                        </tr>

                        <!-- System Event -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap">
                            <td>
                                <div>
                                    <div class="text-sm">2024-12-29 10:30:00</div>
                                    <div class="text-xs text-base-content/60">17 mins ago</div>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-ghost badge-sm">System</span>
                                    <span class="text-sm">Cron Job</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-info badge-sm badge-soft">cart.cleanup</span>
                            </td>
                            <td>
                                <div>
                                    <div class="text-sm">Expired Carts</div>
                                    <div class="text-xs text-base-content/60">15 carts removed</div>
                                </div>
                            </td>
                            <td>
                                <span class="font-mono text-xs">127.0.0.1</span>
                            </td>
                            <td class="max-w-xs truncate text-xs">
                                System Process
                            </td>
                            <td>
                                <button onclick="showDetails('system1')" class="btn btn-xs btn-ghost">
                                    <span class="iconify lucide--eye size-3"></span>
                                    View
                                </button>
                            </td>
                        </tr>

                        <!-- Failed Login (Warning) -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap bg-warning/5">
                            <td>
                                <div>
                                    <div class="text-sm">2024-12-29 10:25:12</div>
                                    <div class="text-xs text-base-content/60">22 mins ago</div>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-error badge-sm">Unknown</span>
                                    <span class="text-sm">test@example.com</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-error badge-sm badge-soft">user.login.failed</span>
                            </td>
                            <td>
                                <div>
                                    <div class="text-sm">Authentication</div>
                                    <div class="text-xs text-error">Invalid credentials</div>
                                </div>
                            </td>
                            <td>
                                <span class="font-mono text-xs">203.0.113.45</span>
                            </td>
                            <td class="max-w-xs truncate text-xs">
                                Unknown Browser
                            </td>
                            <td>
                                <button onclick="showDetails('failed1')" class="btn btn-xs btn-ghost">
                                    <span class="iconify lucide--eye size-3"></span>
                                    View
                                </button>
                            </td>
                        </tr>

                        <!-- Role Permission Changed -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap">
                            <td>
                                <div>
                                    <div class="text-sm">2024-12-29 10:15:30</div>
                                    <div class="text-xs text-base-content/60">32 mins ago</div>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-error badge-sm">Super Admin</span>
                                    <span class="text-sm">admin@minimoda.com</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-warning badge-sm badge-soft">role.permission.updated</span>
                            </td>
                            <td>
                                <div>
                                    <div class="text-sm">Store Manager Role</div>
                                    <div class="text-xs text-base-content/60">Added: product.delete</div>
                                </div>
                            </td>
                            <td>
                                <span class="font-mono text-xs">192.168.1.1</span>
                            </td>
                            <td class="max-w-xs truncate text-xs">
                                Chrome/120.0.0.0 Safari/537.36
                            </td>
                            <td>
                                <button onclick="showDetails('role1')" class="btn btn-xs btn-ghost">
                                    <span class="iconify lucide--eye size-3"></span>
                                    View
                                </button>
                            </td>
                        </tr>

                        <!-- Customer Deleted -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap">
                            <td>
                                <div>
                                    <div class="text-sm">2024-12-29 10:10:45</div>
                                    <div class="text-xs text-base-content/60">37 mins ago</div>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-warning badge-sm">Admin</span>
                                    <span class="text-sm">admin@minimoda.com</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-error badge-sm badge-soft">customer.deleted</span>
                            </td>
                            <td>
                                <div>
                                    <div class="text-sm">Customer #C005432</div>
                                    <div class="text-xs text-base-content/60">inactive.user@email.com</div>
                                </div>
                            </td>
                            <td>
                                <span class="font-mono text-xs">192.168.1.1</span>
                            </td>
                            <td class="max-w-xs truncate text-xs">
                                Chrome/120.0.0.0 Safari/537.36
                            </td>
                            <td>
                                <button onclick="showDetails('customer1')" class="btn btn-xs btn-ghost">
                                    <span class="iconify lucide--eye size-3"></span>
                                    View
                                </button>
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
                    <span class="text-base-content font-medium">1 to 7</span>
                    of 12,543 logs
                </span>
                <div class="inline-flex items-center gap-1">
                    <button class="btn btn-circle sm:btn-sm btn-xs btn-ghost" aria-label="Prev">
                        <span class="iconify lucide--chevron-left"></span>
                    </button>
                    <button class="btn btn-primary btn-circle sm:btn-sm btn-xs">1</button>
                    <button class="btn btn-ghost btn-circle sm:btn-sm btn-xs">2</button>
                    <button class="btn btn-ghost btn-circle sm:btn-sm btn-xs">3</button>
                    <button class="btn btn-ghost btn-circle sm:btn-sm btn-xs">...</button>
                    <button class="btn btn-ghost btn-circle sm:btn-sm btn-xs">628</button>
                    <button class="btn btn-circle sm:btn-sm btn-xs btn-ghost" aria-label="Next">
                        <span class="iconify lucide--chevron-right"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <dialog id="details_modal" class="modal">
        <div class="modal-box max-w-3xl">
            <div class="flex items-center justify-between text-lg font-medium">
                Activity Details
                <form method="dialog">
                    <button class="btn btn-sm btn-ghost btn-circle" aria-label="Close modal">
                        <span class="iconify lucide--x size-4"></span>
                    </button>
                </form>
            </div>
            <div class="mt-4" id="activity_details">
                <!-- Dynamic content -->
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

    <!-- Clear Logs Confirmation -->
    <dialog id="clear_logs_modal" class="modal">
        <div class="modal-box">
            <div class="flex items-center justify-between text-lg font-medium">
                Clear Old Logs
                <form method="dialog">
                    <button class="btn btn-sm btn-ghost btn-circle" aria-label="Close modal">
                        <span class="iconify lucide--x size-4"></span>
                    </button>
                </form>
            </div>
            <p class="py-4">
                This will permanently delete all activity logs older than 90 days.
                This action cannot be undone.
            </p>
            <div class="alert alert-warning">
                <span class="iconify lucide--alert-triangle size-5"></span>
                <span>Approximately 8,234 logs will be deleted.</span>
            </div>
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn btn-ghost btn-sm">Cancel</button>
                </form>
                <button class="btn btn-error btn-sm">Clear Logs</button>
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
function showDetails(logId) {
    // Sample detailed data
    const details = {
        'login1': `
            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-base-content/60">Actor Type:</span>
                        <p class="font-medium">User</p>
                    </div>
                    <div>
                        <span class="text-sm text-base-content/60">Actor ID:</span>
                        <p class="font-mono text-sm">550e8400-e29b-41d4-a716-446655440000</p>
                    </div>
                    <div>
                        <span class="text-sm text-base-content/60">IP Address:</span>
                        <p class="font-mono">192.168.1.100</p>
                    </div>
                    <div>
                        <span class="text-sm text-base-content/60">Location:</span>
                        <p>Jakarta, Indonesia</p>
                    </div>
                </div>
                <div class="divider">Meta Data</div>
                <pre class="bg-base-200 p-3 rounded text-xs overflow-auto">{
    "session_id": "sess_2024122910452301",
    "login_method": "email",
    "two_factor": false,
    "remember_me": true,
    "browser": "Chrome 120.0.0.0",
    "os": "Windows 10",
    "device": "Desktop"
}</pre>
            </div>
        `,
        'product1': `
            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-base-content/60">Product ID:</span>
                        <p class="font-mono text-sm">P001234</p>
                    </div>
                    <div>
                        <span class="text-sm text-base-content/60">Product Name:</span>
                        <p class="font-medium">Kids T-Shirt Blue</p>
                    </div>
                </div>
                <div class="divider">Changes Made</div>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Field</th>
                            <th>Old Value</th>
                            <th>New Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Name</td>
                            <td>-</td>
                            <td>Kids T-Shirt Blue</td>
                        </tr>
                        <tr>
                            <td>Price</td>
                            <td>-</td>
                            <td>Rp 75,000</td>
                        </tr>
                        <tr>
                            <td>Stock</td>
                            <td>-</td>
                            <td>100</td>
                        </tr>
                        <tr>
                            <td>Category</td>
                            <td>-</td>
                            <td>Kids Clothing</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        `
    };

    document.getElementById('activity_details').innerHTML = details[logId] || '<p>No details available</p>';
    details_modal.showModal();
}

function clearLogs() {
    clear_logs_modal.showModal();
}
</script>
@endsection