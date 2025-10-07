@extends('layouts.app')

@section('title', 'Sessions')
@section('page_title', 'Sessions')
@section('page_subtitle', 'Active User Sessions')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Session Management</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Access Control</li>
            <li class="opacity-80">Sessions</li>
        </ul>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Active Sessions</p>
                    <p class="text-2xl font-semibold mt-1 text-success">47</p>
                    <p class="text-xs text-base-content/60 mt-1">Currently online</p>
                </div>
                <div class="bg-success/10 p-3 rounded-lg">
                    <span class="iconify lucide--users size-5 text-success"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Today's Sessions</p>
                    <p class="text-2xl font-semibold mt-1 text-info">234</p>
                    <p class="text-xs text-base-content/60 mt-1">Total today</p>
                </div>
                <div class="bg-info/10 p-3 rounded-lg">
                    <span class="iconify lucide--calendar size-5 text-info"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Unique Users</p>
                    <p class="text-2xl font-semibold mt-1">38</p>
                    <p class="text-xs text-base-content/60 mt-1">Different users</p>
                </div>
                <div class="bg-primary/10 p-3 rounded-lg">
                    <span class="iconify lucide--user-check size-5 text-primary"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Avg. Duration</p>
                    <p class="text-2xl font-semibold mt-1">28m</p>
                    <p class="text-xs text-base-content/60 mt-1">Per session</p>
                </div>
                <div class="bg-warning/10 p-3 rounded-lg">
                    <span class="iconify lucide--clock size-5 text-warning"></span>
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
                            placeholder="Search user/IP"
                            aria-label="Search sessions"
                            type="search" />
                    </label>
                    <select class="select select-sm w-32" aria-label="Filter by status">
                        <option value="">All Sessions</option>
                        <option value="active">Active</option>
                        <option value="idle">Idle</option>
                        <option value="expired">Expired</option>
                    </select>
                    <select class="select select-sm w-32" aria-label="Filter by device">
                        <option value="">All Devices</option>
                        <option value="desktop">Desktop</option>
                        <option value="mobile">Mobile</option>
                        <option value="tablet">Tablet</option>
                    </select>
                </div>
                <div class="inline-flex items-center gap-3">
                    <button class="btn btn-sm btn-ghost" onclick="refreshSessions()">
                        <span class="iconify lucide--refresh-cw size-4"></span>
                        Refresh
                    </button>
                    <button onclick="terminateAll()" class="btn btn-error btn-sm">
                        <span class="iconify lucide--log-out size-4"></span>
                        Terminate All
                    </button>
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
                            <th>User</th>
                            <th>IP Address</th>
                            <th>Device/Browser</th>
                            <th>Location</th>
                            <th>Started At</th>
                            <th>Last Activity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Current User Session -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap bg-success/5">
                            <th>
                                <input
                                    aria-label="Single check"
                                    class="checkbox checkbox-sm"
                                    type="checkbox" />
                            </th>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="avatar online">
                                        <div class="w-8 rounded-full">
                                            <img src="./images/avatars/1.png" alt="Avatar" />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-medium">Admin User</div>
                                        <div class="text-xs text-base-content/60">admin@minimoda.com</div>
                                        <span class="badge badge-primary badge-xs">You</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="font-mono text-sm">192.168.1.100</span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--monitor size-4"></span>
                                    <div>
                                        <div class="text-sm">Chrome 120.0</div>
                                        <div class="text-xs text-base-content/60">Windows 10</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-sm">Jakarta, ID</div>
                            </td>
                            <td>
                                <div>
                                    <div class="text-sm">10:15:30</div>
                                    <div class="text-xs text-base-content/60">32 mins ago</div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="text-sm">10:47:23</div>
                                    <div class="text-xs text-base-content/60">Just now</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">
                                    <span class="loading loading-ring loading-xs"></span>
                                    Active
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-xs btn-ghost" disabled>
                                    Current
                                </button>
                            </td>
                        </tr>

                        <!-- Active Session 1 -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap">
                            <th>
                                <input
                                    aria-label="Single check"
                                    class="checkbox checkbox-sm"
                                    type="checkbox" />
                            </th>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="avatar online">
                                        <div class="w-8 rounded-full">
                                            <img src="./images/avatars/2.png" alt="Avatar" />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-medium">John Doe</div>
                                        <div class="text-xs text-base-content/60">john.doe@minimoda.com</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="font-mono text-sm">192.168.1.50</span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--smartphone size-4"></span>
                                    <div>
                                        <div class="text-sm">Safari iOS</div>
                                        <div class="text-xs text-base-content/60">iPhone 14</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-sm">Surabaya, ID</div>
                            </td>
                            <td>
                                <div>
                                    <div class="text-sm">09:45:12</div>
                                    <div class="text-xs text-base-content/60">1 hour ago</div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="text-sm">10:42:15</div>
                                    <div class="text-xs text-base-content/60">5 mins ago</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">Active</span>
                            </td>
                            <td>
                                <div class="inline-flex">
                                    <button onclick="viewDetails('session1')" class="btn btn-xs btn-ghost">
                                        <span class="iconify lucide--eye size-3"></span>
                                    </button>
                                    <button onclick="terminateSession('session1')" class="btn btn-xs btn-ghost text-error">
                                        <span class="iconify lucide--x size-3"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Idle Session -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap">
                            <th>
                                <input
                                    aria-label="Single check"
                                    class="checkbox checkbox-sm"
                                    type="checkbox" />
                            </th>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="avatar away">
                                        <div class="w-8 rounded-full">
                                            <img src="./images/avatars/3.png" alt="Avatar" />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-medium">Sarah CS</div>
                                        <div class="text-xs text-base-content/60">sarah.cs@minimoda.com</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="font-mono text-sm">192.168.1.75</span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--monitor size-4"></span>
                                    <div>
                                        <div class="text-sm">Firefox 121.0</div>
                                        <div class="text-xs text-base-content/60">Windows 11</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-sm">Bandung, ID</div>
                            </td>
                            <td>
                                <div>
                                    <div class="text-sm">08:30:45</div>
                                    <div class="text-xs text-base-content/60">2 hours ago</div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="text-sm">10:15:20</div>
                                    <div class="text-xs text-base-content/60">32 mins ago</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-warning badge-sm badge-soft">Idle</span>
                            </td>
                            <td>
                                <div class="inline-flex">
                                    <button onclick="viewDetails('session2')" class="btn btn-xs btn-ghost">
                                        <span class="iconify lucide--eye size-3"></span>
                                    </button>
                                    <button onclick="terminateSession('session2')" class="btn btn-xs btn-ghost text-error">
                                        <span class="iconify lucide--x size-3"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Mobile Session -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap">
                            <th>
                                <input
                                    aria-label="Single check"
                                    class="checkbox checkbox-sm"
                                    type="checkbox" />
                            </th>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="avatar online">
                                        <div class="w-8 rounded-full">
                                            <img src="./images/avatars/4.png" alt="Avatar" />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-medium">Mike Manager</div>
                                        <div class="text-xs text-base-content/60">mike@minimoda.com</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="font-mono text-sm">203.0.113.45</span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--smartphone size-4"></span>
                                    <div>
                                        <div class="text-sm">Chrome Android</div>
                                        <div class="text-xs text-base-content/60">Samsung S23</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-sm">Medan, ID</div>
                            </td>
                            <td>
                                <div>
                                    <div class="text-sm">10:20:15</div>
                                    <div class="text-xs text-base-content/60">27 mins ago</div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="text-sm">10:45:50</div>
                                    <div class="text-xs text-base-content/60">2 mins ago</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">Active</span>
                            </td>
                            <td>
                                <div class="inline-flex">
                                    <button onclick="viewDetails('session3')" class="btn btn-xs btn-ghost">
                                        <span class="iconify lucide--eye size-3"></span>
                                    </button>
                                    <button onclick="terminateSession('session3')" class="btn btn-xs btn-ghost text-error">
                                        <span class="iconify lucide--x size-3"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Tablet Session -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap">
                            <th>
                                <input
                                    aria-label="Single check"
                                    class="checkbox checkbox-sm"
                                    type="checkbox" />
                            </th>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="avatar online">
                                        <div class="w-8 rounded-full">
                                            <img src="./images/avatars/5.png" alt="Avatar" />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-medium">Lisa Content</div>
                                        <div class="text-xs text-base-content/60">lisa@minimoda.com</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="font-mono text-sm">192.168.1.88</span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--tablet size-4"></span>
                                    <div>
                                        <div class="text-sm">Safari iPadOS</div>
                                        <div class="text-xs text-base-content/60">iPad Pro</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-sm">Yogyakarta, ID</div>
                            </td>
                            <td>
                                <div>
                                    <div class="text-sm">09:00:30</div>
                                    <div class="text-xs text-base-content/60">1.5 hours ago</div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="text-sm">10:38:45</div>
                                    <div class="text-xs text-base-content/60">9 mins ago</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">Active</span>
                            </td>
                            <td>
                                <div class="inline-flex">
                                    <button onclick="viewDetails('session4')" class="btn btn-xs btn-ghost">
                                        <span class="iconify lucide--eye size-3"></span>
                                    </button>
                                    <button onclick="terminateSession('session4')" class="btn btn-xs btn-ghost text-error">
                                        <span class="iconify lucide--x size-3"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Expired Session -->
                        <tr class="hover:bg-base-200/40 cursor-pointer *:text-nowrap opacity-60">
                            <th>
                                <input
                                    aria-label="Single check"
                                    class="checkbox checkbox-sm"
                                    type="checkbox" />
                            </th>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="avatar offline">
                                        <div class="w-8 rounded-full">
                                            <img src="./images/avatars/6.png" alt="Avatar" />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-medium">David Warehouse</div>
                                        <div class="text-xs text-base-content/60">david@minimoda.com</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="font-mono text-sm">192.168.1.95</span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--monitor size-4"></span>
                                    <div>
                                        <div class="text-sm">Edge 120.0</div>
                                        <div class="text-xs text-base-content/60">Windows 10</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-sm">Semarang, ID</div>
                            </td>
                            <td>
                                <div>
                                    <div class="text-sm">07:15:20</div>
                                    <div class="text-xs text-base-content/60">3.5 hours ago</div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="text-sm">08:45:30</div>
                                    <div class="text-xs text-base-content/60">2 hours ago</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-error badge-sm badge-soft">Expired</span>
                            </td>
                            <td>
                                <div class="inline-flex">
                                    <button onclick="viewDetails('session5')" class="btn btn-xs btn-ghost">
                                        <span class="iconify lucide--eye size-3"></span>
                                    </button>
                                    <button onclick="removeSession('session5')" class="btn btn-xs btn-ghost text-error">
                                        <span class="iconify lucide--trash-2 size-3"></span>
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
                    of 47 sessions
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

    <!-- Session Details Modal -->
    <dialog id="session_details_modal" class="modal">
        <div class="modal-box max-w-2xl">
            <div class="flex items-center justify-between text-lg font-medium">
                Session Details
                <form method="dialog">
                    <button class="btn btn-sm btn-ghost btn-circle" aria-label="Close modal">
                        <span class="iconify lucide--x size-4"></span>
                    </button>
                </form>
            </div>
            <div class="mt-4" id="session_details_content">
                <!-- Dynamic content -->
            </div>
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn btn-sm">Close</button>
                </form>
                <button class="btn btn-error btn-sm">Terminate Session</button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <!-- Terminate Confirmation Modal -->
    <dialog id="terminate_modal" class="modal">
        <div class="modal-box">
            <div class="flex items-center justify-between text-lg font-medium">
                Terminate Session
                <form method="dialog">
                    <button class="btn btn-sm btn-ghost btn-circle" aria-label="Close modal">
                        <span class="iconify lucide--x size-4"></span>
                    </button>
                </form>
            </div>
            <p class="py-4">
                Are you sure you want to terminate this session? The user will be logged out immediately.
            </p>
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn btn-ghost btn-sm">Cancel</button>
                </form>
                <button class="btn btn-error btn-sm">Terminate</button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <!-- Terminate All Modal -->
    <dialog id="terminate_all_modal" class="modal">
        <div class="modal-box">
            <div class="flex items-center justify-between text-lg font-medium">
                Terminate All Sessions
                <form method="dialog">
                    <button class="btn btn-sm btn-ghost btn-circle" aria-label="Close modal">
                        <span class="iconify lucide--x size-4"></span>
                    </button>
                </form>
            </div>
            <p class="py-4">
                This will terminate all active sessions except yours. All users will be logged out immediately.
            </p>
            <div class="alert alert-warning">
                <span class="iconify lucide--triangle-alert size-5"></span>
                <span>46 sessions will be terminated.</span>
            </div>
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn btn-ghost btn-sm">Cancel</button>
                </form>
                <button class="btn btn-error btn-sm">Terminate All</button>
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
function viewDetails(sessionId) {
    const details = `
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-sm text-base-content/60">Session ID:</span>
                    <p class="font-mono text-sm">sess_2024122910452301</p>
                </div>
                <div>
                    <span class="text-sm text-base-content/60">User ID:</span>
                    <p class="font-mono text-sm">550e8400-e29b-41d4</p>
                </div>
                <div>
                    <span class="text-sm text-base-content/60">IP Address:</span>
                    <p class="font-mono">192.168.1.50</p>
                </div>
                <div>
                    <span class="text-sm text-base-content/60">Location:</span>
                    <p>Surabaya, Indonesia</p>
                </div>
                <div>
                    <span class="text-sm text-base-content/60">Browser:</span>
                    <p>Safari iOS 17.2</p>
                </div>
                <div>
                    <span class="text-sm text-base-content/60">Device:</span>
                    <p>iPhone 14 Pro</p>
                </div>
                <div>
                    <span class="text-sm text-base-content/60">Started:</span>
                    <p>09:45:12 (1 hour ago)</p>
                </div>
                <div>
                    <span class="text-sm text-base-content/60">Last Activity:</span>
                    <p>10:42:15 (5 mins ago)</p>
                </div>
            </div>

            <div class="divider">Activity Summary</div>

            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-2xl font-semibold">127</p>
                    <p class="text-xs text-base-content/60">Page Views</p>
                </div>
                <div>
                    <p class="text-2xl font-semibold">23</p>
                    <p class="text-xs text-base-content/60">Actions</p>
                </div>
                <div>
                    <p class="text-2xl font-semibold">57m</p>
                    <p class="text-xs text-base-content/60">Duration</p>
                </div>
            </div>

            <div class="divider">Recent Actions</div>

            <div class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span>Viewed Product List</span>
                    <span class="text-base-content/60">10:42:15</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span>Updated Order #ORD-001</span>
                    <span class="text-base-content/60">10:38:30</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span>Created New Product</span>
                    <span class="text-base-content/60">10:35:20</span>
                </div>
            </div>
        </div>
    `;

    document.getElementById('session_details_content').innerHTML = details;
    session_details_modal.showModal();
}

function terminateSession(sessionId) {
    terminate_modal.showModal();
}

function terminateAll() {
    terminate_all_modal.showModal();
}

function removeSession(sessionId) {
    // Remove expired session
    console.log('Removing session:', sessionId);
}

function refreshSessions() {
    // Refresh session list
    location.reload();
}

// Auto refresh every 30 seconds
setInterval(() => {
    // In production, this would be an AJAX call
    console.log('Refreshing session data...');
}, 30000);
</script>
@endsection