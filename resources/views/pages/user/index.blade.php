@extends('layouts.app')

@section('title', 'Users')
@section('page_title', 'User')
@section('page_subtitle', 'User Management')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">User Management</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Access Control</li>
            <li class="opacity-80">Users</li>
        </ul>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Total Users</p>
                    <p class="text-2xl font-semibold mt-1">1,267</p>
                    <p class="text-xs text-base-content/60 mt-1">All registered users</p>
                </div>
                <div class="bg-primary/10 p-3 rounded-lg">
                    <span class="iconify lucide--users size-5 text-primary"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Active Users</p>
                    <p class="text-2xl font-semibold mt-1 text-success">1,245</p>
                    <p class="text-xs text-base-content/60 mt-1">98.3% of total</p>
                </div>
                <div class="bg-success/10 p-3 rounded-lg">
                    <span class="iconify lucide--user-check size-5 text-success"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Admin Users</p>
                    <p class="text-2xl font-semibold mt-1 text-info">22</p>
                    <p class="text-xs text-base-content/60 mt-1">Staff & managers</p>
                </div>
                <div class="bg-info/10 p-3 rounded-lg">
                    <span class="iconify lucide--shield size-5 text-info"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">New This Month</p>
                    <p class="text-2xl font-semibold mt-1 text-warning">87</p>
                    <p class="text-xs text-base-content/60 mt-1">↑ 23% from last month</p>
                </div>
                <div class="bg-warning/10 p-3 rounded-lg">
                    <span class="iconify lucide--user-plus size-5 text-warning"></span>
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
                        <span
                            class="iconify lucide--search text-base-content/80 size-3.5"></span>
                        <input
                            class="w-24 sm:w-36"
                            placeholder="Search along users"
                            aria-label="Search users"
                            type="search" />
                    </label>
                   
                </div>
                <div class="inline-flex items-center gap-3">
                    <a
                        aria-label="Create user link"
                        class="btn btn-primary btn-sm max-sm:btn-square"
                        href="{{ route('user.create') }}">
                        <span class="iconify lucide--plus size-4"></span>
                        <span class="hidden sm:inline">Add User</span>
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
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Last Login</th>
                            <th>Status</th>
                            <th>Two Factor</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            class="hover:bg-base-200/40 cursor-pointer *:text-nowrap">
                            <th>
                                <input
                                    aria-label="Single check"
                                    class="checkbox checkbox-sm"
                                    type="checkbox" />
                            </th>
                            <td>1</td>
                            <td>Monsky</td>
                            <td>monsky@gmail.com</td>
                            <td class="font-mono text-sm">Super Admin</td>
                            <td>
                                2025-09-29 00:00:00 <br>
                                <span class="badge badge-info badge-sm badge-soft">127.0.0.1</span>
                            </td>
                            <td>
                                <span class="badge badge-success badge-sm badge-soft">Active</span>
                            </td>
                            <td>
                                <span class="badge badge-error badge-sm badge-soft">Disabled</span>
                            </td>
                            <td class="text-sm font-medium">2025-09-29 00:00:00</td>
                            <td>
                                <div class="inline-flex">
                                    <a
                                        href="{{ route('user.edit', 1) }}"
                                        aria-label="Edit user"
                                        class="btn btn-square btn-ghost btn-sm">
                                        <span
                                            class="iconify lucide--pencil text-base-content/80 size-4"></span>
                                    </a>
                                    <button
                                        aria-label="View user detail"
                                        class="btn btn-square btn-ghost btn-sm"
                                        onclick="viewUserDrawer.showModal()">
                                        <span
                                            class="iconify lucide--eye text-base-content/80 size-4"></span>
                                    </button>
                                    <button
                                        aria-label="Dummy delete product"
                                        onclick="apps_product_delete.showModal()"
                                        class="btn btn-square btn-error btn-outline btn-sm border-transparent">
                                        <span
                                            class="iconify lucide--trash size-4"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between p-6">
                <div
                    class="text-base-content/80 hover:text-base-content flex gap-2 text-sm">
                    <span class="hidden sm:inline">Per page</span>
                    <select
                        class="select select-xs w-18"
                        aria-label="Per page">
                        <option value="10">10</option>
                        <option value="20" selected="">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <span class="text-base-content/80 hidden text-sm lg:inline">
                    Showing
                    <span class="text-base-content font-medium">
                        1 to 20
                    </span>
                    of 457 items
                </span>
                <div class="inline-flex items-center gap-1">
                    <button
                        class="btn btn-circle sm:btn-sm btn-xs btn-ghost"
                        aria-label="Prev">
                        <span class="iconify lucide--chevron-left"></span>
                    </button>
                    <button
                        class="btn btn-primary btn-circle sm:btn-sm btn-xs">
                        1
                    </button>
                    <button
                        class="btn btn-ghost btn-circle sm:btn-sm btn-xs">
                        2
                    </button>
                    <button
                        class="btn btn-ghost btn-circle sm:btn-sm btn-xs">
                        3
                    </button>
                    <button
                        class="btn btn-circle sm:btn-sm btn-xs btn-ghost"
                        aria-label="Next">
                        <span class="iconify lucide--chevron-right"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Delete Confirmation Modal -->
    <dialog id="apps_product_delete" class="modal">
        <div class="modal-box">
            <div
                class="flex items-center justify-between text-lg font-medium">
                Confirm Delete
                <form method="dialog">
                    <button
                        class="btn btn-sm btn-ghost btn-circle"
                        aria-label="Close modal">
                        <span class="iconify lucide--x size-4"></span>
                    </button>
                </form>
            </div>
            <p class="py-4">
                You are about to delete this user. Would you like to
                proceed further ?
            </p>
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn btn-ghost btn-sm">No</button>
                </form>
                <form method="dialog">
                    <button class="btn btn-sm btn-error">
                        Yes, delete it
                    </button>
                </form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

<!-- View User Drawer (Right Side) -->
<dialog id="viewUserDrawer" class="modal">
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
    <div class="modal-box max-w-sm sm:max-w-md h-screen rounded-none p-0 fixed right-0 top-0 bottom-0 translate-x-full transition-transform">
        <!-- Header -->
        <div class="bg-base-200/30 border-base-200 flex h-16 min-h-16 items-center justify-between border-b px-5">
            <div>
                <p class="text-lg font-medium">User Details</p>
                <p class="text-xs text-base-content/60">Complete user information</p>
            </div>
            <form method="dialog">
                <button class="btn btn-ghost btn-sm btn-circle">
                    <span class="iconify lucide--x size-5"></span>
                </button>
            </form>
        </div>

        <!-- Content -->
        <div class="overflow-y-auto h-[calc(100%-8rem)] p-5">
            <div class="space-y-4">
                <!-- User Profile -->
                <div class="flex items-start gap-4">
                    <div class="avatar">
                        <div class="w-20 rounded-full">
                            <img src="https://ui-avatars.com/api/?name=Monsky&size=128" alt="User Avatar" />
                        </div>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-xl font-semibold">Monsky</h4>
                        <p class="text-sm text-base-content/60">monsky@gmail.com</p>
                        <div class="flex gap-2 mt-2">
                            <span class="badge badge-success badge-sm">Active</span>
                            <span class="badge badge-primary badge-sm">Super Admin</span>
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Personal Information -->
                <div>
                    <h5 class="font-semibold mb-3">Personal Information</h5>
                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <span class="iconify lucide--user size-5 text-base-content/60 mt-0.5"></span>
                            <div class="flex-1">
                                <p class="text-xs text-base-content/60">Full Name</p>
                                <p class="font-medium">Monsky</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="iconify lucide--mail size-5 text-base-content/60 mt-0.5"></span>
                            <div class="flex-1">
                                <p class="text-xs text-base-content/60">Email Address</p>
                                <p class="font-medium">monsky@gmail.com</p>
                                <span class="badge badge-success badge-xs mt-1">Verified</span>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="iconify lucide--phone size-5 text-base-content/60 mt-0.5"></span>
                            <div class="flex-1">
                                <p class="text-xs text-base-content/60">Phone Number</p>
                                <p class="font-medium">+62 812 3456 7890</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Account Information -->
                <div>
                    <h5 class="font-semibold mb-3">Account Information</h5>
                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <span class="iconify lucide--shield size-5 text-base-content/60 mt-0.5"></span>
                            <div class="flex-1">
                                <p class="text-xs text-base-content/60">Roles</p>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    <span class="badge badge-primary badge-sm">Super Admin</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="iconify lucide--check-circle size-5 text-base-content/60 mt-0.5"></span>
                            <div class="flex-1">
                                <p class="text-xs text-base-content/60">Account Status</p>
                                <p class="font-medium">Active</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="iconify lucide--lock size-5 text-base-content/60 mt-0.5"></span>
                            <div class="flex-1">
                                <p class="text-xs text-base-content/60">Two-Factor Authentication</p>
                                <span class="badge badge-error badge-sm mt-1">Disabled</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Activity Information -->
                <div>
                    <h5 class="font-semibold mb-3">Activity Information</h5>
                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <span class="iconify lucide--log-in size-5 text-base-content/60 mt-0.5"></span>
                            <div class="flex-1">
                                <p class="text-xs text-base-content/60">Last Login</p>
                                <p class="font-medium">2025-09-29 00:00:00</p>
                                <span class="badge badge-info badge-xs mt-1">127.0.0.1</span>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="iconify lucide--calendar size-5 text-base-content/60 mt-0.5"></span>
                            <div class="flex-1">
                                <p class="text-xs text-base-content/60">Account Created</p>
                                <p class="font-medium">2025-09-29 00:00:00</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="iconify lucide--clock size-5 text-base-content/60 mt-0.5"></span>
                            <div class="flex-1">
                                <p class="text-xs text-base-content/60">Last Updated</p>
                                <p class="font-medium">2025-09-29 00:00:00</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Permissions -->
                <div>
                    <h5 class="font-semibold mb-3">Permissions</h5>
                    <div class="space-y-2">
                        <div class="collapse collapse-arrow bg-base-200">
                            <input type="checkbox" />
                            <div class="collapse-title text-sm font-medium">
                                Product Management
                            </div>
                            <div class="collapse-content">
                                <div class="space-y-1 text-sm">
                                    <p class="flex items-center gap-2">
                                        <span class="iconify lucide--check size-4 text-success"></span>
                                        View Products
                                    </p>
                                    <p class="flex items-center gap-2">
                                        <span class="iconify lucide--check size-4 text-success"></span>
                                        Create Products
                                    </p>
                                    <p class="flex items-center gap-2">
                                        <span class="iconify lucide--check size-4 text-success"></span>
                                        Update Products
                                    </p>
                                    <p class="flex items-center gap-2">
                                        <span class="iconify lucide--check size-4 text-success"></span>
                                        Delete Products
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="collapse collapse-arrow bg-base-200">
                            <input type="checkbox" />
                            <div class="collapse-title text-sm font-medium">
                                Order Management
                            </div>
                            <div class="collapse-content">
                                <div class="space-y-1 text-sm">
                                    <p class="flex items-center gap-2">
                                        <span class="iconify lucide--check size-4 text-success"></span>
                                        View Orders
                                    </p>
                                    <p class="flex items-center gap-2">
                                        <span class="iconify lucide--check size-4 text-success"></span>
                                        Update Orders
                                    </p>
                                    <p class="flex items-center gap-2">
                                        <span class="iconify lucide--check size-4 text-success"></span>
                                        Delete Orders
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="collapse collapse-arrow bg-base-200">
                            <input type="checkbox" />
                            <div class="collapse-title text-sm font-medium">
                                User Management
                            </div>
                            <div class="collapse-content">
                                <div class="space-y-1 text-sm">
                                    <p class="flex items-center gap-2">
                                        <span class="iconify lucide--check size-4 text-success"></span>
                                        View Users
                                    </p>
                                    <p class="flex items-center gap-2">
                                        <span class="iconify lucide--check size-4 text-success"></span>
                                        Create Users
                                    </p>
                                    <p class="flex items-center gap-2">
                                        <span class="iconify lucide--check size-4 text-success"></span>
                                        Update Users
                                    </p>
                                    <p class="flex items-center gap-2">
                                        <span class="iconify lucide--check size-4 text-success"></span>
                                        Delete Users
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Session Information -->
                <div>
                    <h5 class="font-semibold mb-3">Active Sessions</h5>
                    <div class="space-y-2">
                        <div class="border border-base-300 rounded-lg p-3">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start gap-3">
                                    <span class="iconify lucide--monitor size-5 text-base-content/60"></span>
                                    <div>
                                        <p class="text-sm font-medium">Chrome on Windows</p>
                                        <p class="text-xs text-base-content/60">127.0.0.1 • Last active: 2 hours ago</p>
                                        <span class="badge badge-success badge-xs mt-1">Current Session</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border border-base-300 rounded-lg p-3">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start gap-3">
                                    <span class="iconify lucide--smartphone size-5 text-base-content/60"></span>
                                    <div>
                                        <p class="text-sm font-medium">Mobile Safari on iOS</p>
                                        <p class="text-xs text-base-content/60">192.168.1.10 • Last active: 1 day ago</p>
                                    </div>
                                </div>
                                <button class="btn btn-ghost btn-xs text-error">
                                    Revoke
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="divider my-4"></div>
                <div class="flex gap-2">
                    <a href="{{ route('user.edit', 1) }}" class="btn btn-primary flex-1">
                        <span class="iconify lucide--pencil size-4"></span>
                        Edit
                    </a>
                    <button type="button" class="btn btn-error btn-outline flex-1">
                        <span class="iconify lucide--trash size-4"></span>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </dialog>

@section('customjs')
<style>
    /* Right drawer animation */
    #viewUserDrawer .modal-box {
        margin-left: auto;
    }
    #viewUserDrawer[open] .modal-box {
        transform: translateX(0);
        animation: slideInRight 0.3s ease-out;
    }
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
        }
        to {
            transform: translateX(0);
        }
    }
</style>
@endsection