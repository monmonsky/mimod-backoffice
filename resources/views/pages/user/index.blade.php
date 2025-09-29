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
                        aria-label="Create product link"
                        class="btn btn-primary btn-sm max-sm:btn-square"
                        href="./apps-ecommerce-products-create.html">
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
                                        aria-label="Edit product link"
                                        class="btn btn-square btn-ghost btn-sm"
                                        href="./apps-ecommerce-products-edit.html">
                                        <span
                                            class="iconify lucide--pencil text-base-content/80 size-4"></span>
                                    </a>
                                    <button
                                        aria-label="Dummy show product"
                                        class="btn btn-square btn-ghost btn-sm">
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
                You are about to delete this product. Would you like to
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
    </div>
@endsection

@section('customjs')
<script>

</script>
@endsection