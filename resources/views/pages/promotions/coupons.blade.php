@extends('layouts.app')

@section('title', 'Coupons')
@section('page_title', 'Promotions')
@section('page_subtitle', 'Coupons')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Manage Coupons</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Promotions</li>
            <li class="opacity-80">Coupons</li>
        </ul>
    </div>
</div>

<!-- Statistics Cards -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Total Coupons</p>
                    <h3 class="text-2xl font-bold mt-1">24</h3>
                    <p class="text-xs text-info mt-2">All coupons</p>
                </div>
                <div class="bg-info/10 rounded-full p-3">
                    <span class="iconify lucide--ticket size-6 text-info"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Active Coupons</p>
                    <h3 class="text-2xl font-bold mt-1">18</h3>
                    <p class="text-xs text-success mt-2">Currently active</p>
                </div>
                <div class="bg-success/10 rounded-full p-3">
                    <span class="iconify lucide--check-circle size-6 text-success"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Used This Month</p>
                    <h3 class="text-2xl font-bold mt-1">342</h3>
                    <p class="text-xs text-primary mt-2">+18% vs last month</p>
                </div>
                <div class="bg-primary/10 rounded-full p-3">
                    <span class="iconify lucide--trending-up size-6 text-primary"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Total Discount</p>
                    <h3 class="text-2xl font-bold mt-1">Rp 45.8M</h3>
                    <p class="text-xs text-warning mt-2">This month</p>
                </div>
                <div class="bg-warning/10 rounded-full p-3">
                    <span class="iconify lucide--wallet size-6 text-warning"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters & Actions -->
<div class="mt-6 bg-base-100 card shadow">
    <div class="card-body">
        <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                <div class="form-control">
                    <input type="text" placeholder="Search coupons..." class="input input-bordered w-full sm:w-64" />
                </div>
                <select class="select select-bordered w-full sm:w-auto">
                    <option selected>All Status</option>
                    <option>Active</option>
                    <option>Scheduled</option>
                    <option>Expired</option>
                    <option>Inactive</option>
                </select>
                <select class="select select-bordered w-full sm:w-auto">
                    <option selected>All Types</option>
                    <option>Percentage</option>
                    <option>Fixed Amount</option>
                    <option>Free Shipping</option>
                </select>
            </div>
            <button class="btn btn-primary w-full lg:w-auto">
                <span class="iconify lucide--plus size-4"></span>
                Create Coupon
            </button>
        </div>
    </div>
</div>

<!-- Coupons Table -->
<div class="mt-6 bg-base-100 card shadow">
    <div class="card-body">
        <div class="flex items-center justify-between mb-4">
            <h3 class="card-title text-base">All Coupons</h3>
            <button class="btn btn-ghost btn-sm">
                <span class="iconify lucide--download size-4"></span>
                Export
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Usage</th>
                        <th>Valid Period</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="iconify lucide--ticket text-primary size-4"></span>
                                <span class="font-mono font-bold">SAVE20</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <p class="font-medium">20% Off All Products</p>
                                <p class="text-xs text-base-content/60">Min. purchase: Rp 500K</p>
                            </div>
                        </td>
                        <td><span class="badge badge-primary badge-sm">Percentage</span></td>
                        <td class="font-semibold">20%</td>
                        <td>
                            <div>
                                <p class="text-sm">145 / 500</p>
                                <progress class="progress progress-primary w-20" value="145" max="500"></progress>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm">
                                <p>2024-01-01</p>
                                <p class="text-base-content/60">to 2024-12-31</p>
                            </div>
                        </td>
                        <td><span class="badge badge-success badge-sm">Active</span></td>
                        <td>
                            <div class="dropdown dropdown-end">
                                <button tabindex="0" class="btn btn-ghost btn-sm btn-square">
                                    <span class="iconify lucide--more-vertical size-4"></span>
                                </button>
                                <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-40 p-2 shadow">
                                    <li><a><span class="iconify lucide--eye size-4"></span> View</a></li>
                                    <li><a><span class="iconify lucide--edit size-4"></span> Edit</a></li>
                                    <li><a><span class="iconify lucide--copy size-4"></span> Duplicate</a></li>
                                    <li><a class="text-error"><span class="iconify lucide--trash-2 size-4"></span> Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="iconify lucide--ticket text-success size-4"></span>
                                <span class="font-mono font-bold">FREESHIP</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <p class="font-medium">Free Shipping</p>
                                <p class="text-xs text-base-content/60">No minimum purchase</p>
                            </div>
                        </td>
                        <td><span class="badge badge-success badge-sm">Free Shipping</span></td>
                        <td class="font-semibold">100%</td>
                        <td>
                            <div>
                                <p class="text-sm">89 / 200</p>
                                <progress class="progress progress-success w-20" value="89" max="200"></progress>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm">
                                <p>2024-01-15</p>
                                <p class="text-base-content/60">to 2024-02-15</p>
                            </div>
                        </td>
                        <td><span class="badge badge-success badge-sm">Active</span></td>
                        <td>
                            <div class="dropdown dropdown-end">
                                <button tabindex="0" class="btn btn-ghost btn-sm btn-square">
                                    <span class="iconify lucide--more-vertical size-4"></span>
                                </button>
                                <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-40 p-2 shadow">
                                    <li><a><span class="iconify lucide--eye size-4"></span> View</a></li>
                                    <li><a><span class="iconify lucide--edit size-4"></span> Edit</a></li>
                                    <li><a><span class="iconify lucide--copy size-4"></span> Duplicate</a></li>
                                    <li><a class="text-error"><span class="iconify lucide--trash-2 size-4"></span> Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="iconify lucide--ticket text-warning size-4"></span>
                                <span class="font-mono font-bold">FIRST50K</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <p class="font-medium">First Order Discount</p>
                                <p class="text-xs text-base-content/60">First-time customers only</p>
                            </div>
                        </td>
                        <td><span class="badge badge-warning badge-sm">Fixed Amount</span></td>
                        <td class="font-semibold">Rp 50K</td>
                        <td>
                            <div>
                                <p class="text-sm">56 / 100</p>
                                <progress class="progress progress-warning w-20" value="56" max="100"></progress>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm">
                                <p>2024-01-01</p>
                                <p class="text-base-content/60">to 2024-03-31</p>
                            </div>
                        </td>
                        <td><span class="badge badge-success badge-sm">Active</span></td>
                        <td>
                            <div class="dropdown dropdown-end">
                                <button tabindex="0" class="btn btn-ghost btn-sm btn-square">
                                    <span class="iconify lucide--more-vertical size-4"></span>
                                </button>
                                <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-40 p-2 shadow">
                                    <li><a><span class="iconify lucide--eye size-4"></span> View</a></li>
                                    <li><a><span class="iconify lucide--edit size-4"></span> Edit</a></li>
                                    <li><a><span class="iconify lucide--copy size-4"></span> Duplicate</a></li>
                                    <li><a class="text-error"><span class="iconify lucide--trash-2 size-4"></span> Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="iconify lucide--ticket text-info size-4"></span>
                                <span class="font-mono font-bold">FLASH15</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <p class="font-medium">Flash Sale 15%</p>
                                <p class="text-xs text-base-content/60">Selected items only</p>
                            </div>
                        </td>
                        <td><span class="badge badge-info badge-sm">Percentage</span></td>
                        <td class="font-semibold">15%</td>
                        <td>
                            <div>
                                <p class="text-sm">32 / 50</p>
                                <progress class="progress progress-info w-20" value="32" max="50"></progress>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm">
                                <p>2024-02-01</p>
                                <p class="text-base-content/60">to 2024-02-03</p>
                            </div>
                        </td>
                        <td><span class="badge badge-warning badge-sm">Scheduled</span></td>
                        <td>
                            <div class="dropdown dropdown-end">
                                <button tabindex="0" class="btn btn-ghost btn-sm btn-square">
                                    <span class="iconify lucide--more-vertical size-4"></span>
                                </button>
                                <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-40 p-2 shadow">
                                    <li><a><span class="iconify lucide--eye size-4"></span> View</a></li>
                                    <li><a><span class="iconify lucide--edit size-4"></span> Edit</a></li>
                                    <li><a><span class="iconify lucide--copy size-4"></span> Duplicate</a></li>
                                    <li><a class="text-error"><span class="iconify lucide--trash-2 size-4"></span> Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="iconify lucide--ticket text-base-content/40 size-4"></span>
                                <span class="font-mono font-bold text-base-content/60">NEWYEAR2023</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <p class="font-medium text-base-content/60">New Year Sale</p>
                                <p class="text-xs text-base-content/60">All products</p>
                            </div>
                        </td>
                        <td><span class="badge badge-ghost badge-sm">Percentage</span></td>
                        <td class="font-semibold text-base-content/60">25%</td>
                        <td>
                            <div>
                                <p class="text-sm">200 / 200</p>
                                <progress class="progress progress-error w-20" value="200" max="200"></progress>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-base-content/60">
                                <p>2023-12-31</p>
                                <p class="text-base-content/60">to 2024-01-07</p>
                            </div>
                        </td>
                        <td><span class="badge badge-error badge-sm">Expired</span></td>
                        <td>
                            <div class="dropdown dropdown-end">
                                <button tabindex="0" class="btn btn-ghost btn-sm btn-square">
                                    <span class="iconify lucide--more-vertical size-4"></span>
                                </button>
                                <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-40 p-2 shadow">
                                    <li><a><span class="iconify lucide--eye size-4"></span> View</a></li>
                                    <li><a><span class="iconify lucide--copy size-4"></span> Duplicate</a></li>
                                    <li><a class="text-error"><span class="iconify lucide--trash-2 size-4"></span> Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-between items-center mt-4">
            <div class="text-sm text-base-content/60">
                Showing 1 to 5 of 24 coupons
            </div>
            <div class="join">
                <button class="join-item btn btn-sm">«</button>
                <button class="join-item btn btn-sm btn-active">1</button>
                <button class="join-item btn btn-sm">2</button>
                <button class="join-item btn btn-sm">3</button>
                <button class="join-item btn btn-sm">»</button>
            </div>
        </div>
    </div>
</div>
@endsection