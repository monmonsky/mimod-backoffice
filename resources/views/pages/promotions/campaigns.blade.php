@extends('layouts.app')

@section('title', 'Marketing Campaigns')
@section('page_title', 'Promotions')
@section('page_subtitle', 'Marketing Campaigns')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Marketing Campaigns</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Promotions</li>
            <li class="opacity-80">Campaigns</li>
        </ul>
    </div>
</div>

<!-- Statistics Cards -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Total Campaigns</p>
                    <h3 class="text-2xl font-bold mt-1">15</h3>
                    <p class="text-xs text-info mt-2">All campaigns</p>
                </div>
                <div class="bg-info/10 rounded-full p-3">
                    <span class="iconify lucide--megaphone size-6 text-info"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Active Campaigns</p>
                    <h3 class="text-2xl font-bold mt-1">6</h3>
                    <p class="text-xs text-success mt-2">Currently running</p>
                </div>
                <div class="bg-success/10 rounded-full p-3">
                    <span class="iconify lucide--play-circle size-6 text-success"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Total Reach</p>
                    <h3 class="text-2xl font-bold mt-1">28.5K</h3>
                    <p class="text-xs text-primary mt-2">Customers reached</p>
                </div>
                <div class="bg-primary/10 rounded-full p-3">
                    <span class="iconify lucide--users size-6 text-primary"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Conversion Rate</p>
                    <h3 class="text-2xl font-bold mt-1">12.8%</h3>
                    <p class="text-xs text-warning mt-2">Average</p>
                </div>
                <div class="bg-warning/10 rounded-full p-3">
                    <span class="iconify lucide--target size-6 text-warning"></span>
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
                    <input type="text" placeholder="Search campaigns..." class="input input-bordered w-full sm:w-64" />
                </div>
                <select class="select select-bordered w-full sm:w-auto">
                    <option selected>All Status</option>
                    <option>Active</option>
                    <option>Scheduled</option>
                    <option>Completed</option>
                    <option>Paused</option>
                </select>
                <select class="select select-bordered w-full sm:w-auto">
                    <option selected>All Types</option>
                    <option>Email</option>
                    <option>SMS</option>
                    <option>Push Notification</option>
                    <option>Multi-Channel</option>
                </select>
            </div>
            <button class="btn btn-primary w-full lg:w-auto">
                <span class="iconify lucide--plus size-4"></span>
                Create Campaign
            </button>
        </div>
    </div>
</div>

<!-- Active Campaigns -->
<div class="mt-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-medium">Active Campaigns</h3>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Campaign Card 1 -->
        <div class="bg-base-100 card shadow">
            <div class="card-body">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="bg-primary/10 rounded-lg p-3">
                            <span class="iconify lucide--mail size-6 text-primary"></span>
                        </div>
                        <div>
                            <h4 class="font-bold">Flash Sale Weekend</h4>
                            <span class="badge badge-primary badge-sm mt-1">Email</span>
                        </div>
                    </div>
                    <div class="dropdown dropdown-end">
                        <button tabindex="0" class="btn btn-ghost btn-sm btn-square">
                            <span class="iconify lucide--more-vertical size-4"></span>
                        </button>
                        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-40 p-2 shadow">
                            <li><a><span class="iconify lucide--eye size-4"></span> View</a></li>
                            <li><a><span class="iconify lucide--pencil size-4"></span> Edit</a></li>
                            <li><a><span class="iconify lucide--pause size-4"></span> Pause</a></li>
                            <li><a class="text-error"><span class="iconify lucide--trash-2 size-4"></span> Delete</a></li>
                        </ul>
                    </div>
                </div>

                <p class="text-sm text-base-content/60 mb-4">Get up to 50% off on selected items this weekend only! Don't miss out on amazing deals.</p>

                <div class="grid grid-cols-3 gap-2 mb-4">
                    <div class="text-center p-2 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/60">Sent</p>
                        <p class="font-bold">8,542</p>
                    </div>
                    <div class="text-center p-2 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/60">Opened</p>
                        <p class="font-bold">5,234</p>
                    </div>
                    <div class="text-center p-2 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/60">Clicked</p>
                        <p class="font-bold">1,847</p>
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="iconify lucide--calendar size-4 text-base-content/60"></span>
                        <span>Started: Jan 15, 2024</span>
                    </div>
                    <span class="badge badge-success badge-sm">Active</span>
                </div>

                <div class="mt-3">
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span>Performance</span>
                        <span class="font-bold">61.2%</span>
                    </div>
                    <progress class="progress progress-success" value="61" max="100"></progress>
                </div>
            </div>
        </div>

        <!-- Campaign Card 2 -->
        <div class="bg-base-100 card shadow">
            <div class="card-body">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="bg-success/10 rounded-lg p-3">
                            <span class="iconify lucide--smartphone size-6 text-success"></span>
                        </div>
                        <div>
                            <h4 class="font-bold">New Arrivals Alert</h4>
                            <span class="badge badge-success badge-sm mt-1">Push</span>
                        </div>
                    </div>
                    <div class="dropdown dropdown-end">
                        <button tabindex="0" class="btn btn-ghost btn-sm btn-square">
                            <span class="iconify lucide--more-vertical size-4"></span>
                        </button>
                        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-40 p-2 shadow">
                            <li><a><span class="iconify lucide--eye size-4"></span> View</a></li>
                            <li><a><span class="iconify lucide--pencil size-4"></span> Edit</a></li>
                            <li><a><span class="iconify lucide--pause size-4"></span> Pause</a></li>
                            <li><a class="text-error"><span class="iconify lucide--trash-2 size-4"></span> Delete</a></li>
                        </ul>
                    </div>
                </div>

                <p class="text-sm text-base-content/60 mb-4">Check out our latest collection of products. Be the first to shop the newest arrivals!</p>

                <div class="grid grid-cols-3 gap-2 mb-4">
                    <div class="text-center p-2 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/60">Sent</p>
                        <p class="font-bold">12,340</p>
                    </div>
                    <div class="text-center p-2 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/60">Delivered</p>
                        <p class="font-bold">11,892</p>
                    </div>
                    <div class="text-center p-2 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/60">Clicked</p>
                        <p class="font-bold">3,248</p>
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="iconify lucide--calendar size-4 text-base-content/60"></span>
                        <span>Started: Jan 10, 2024</span>
                    </div>
                    <span class="badge badge-success badge-sm">Active</span>
                </div>

                <div class="mt-3">
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span>Performance</span>
                        <span class="font-bold">27.3%</span>
                    </div>
                    <progress class="progress progress-success" value="27" max="100"></progress>
                </div>
            </div>
        </div>

        <!-- Campaign Card 3 -->
        <div class="bg-base-100 card shadow">
            <div class="card-body">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="bg-warning/10 rounded-lg p-3">
                            <span class="iconify lucide--message-square size-6 text-warning"></span>
                        </div>
                        <div>
                            <h4 class="font-bold">Loyalty Rewards</h4>
                            <span class="badge badge-warning badge-sm mt-1">SMS</span>
                        </div>
                    </div>
                    <div class="dropdown dropdown-end">
                        <button tabindex="0" class="btn btn-ghost btn-sm btn-square">
                            <span class="iconify lucide--more-vertical size-4"></span>
                        </button>
                        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-40 p-2 shadow">
                            <li><a><span class="iconify lucide--eye size-4"></span> View</a></li>
                            <li><a><span class="iconify lucide--pencil size-4"></span> Edit</a></li>
                            <li><a><span class="iconify lucide--pause size-4"></span> Pause</a></li>
                            <li><a class="text-error"><span class="iconify lucide--trash-2 size-4"></span> Delete</a></li>
                        </ul>
                    </div>
                </div>

                <p class="text-sm text-base-content/60 mb-4">Thank you for being a loyal customer! Enjoy exclusive rewards and special discounts.</p>

                <div class="grid grid-cols-3 gap-2 mb-4">
                    <div class="text-center p-2 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/60">Sent</p>
                        <p class="font-bold">3,254</p>
                    </div>
                    <div class="text-center p-2 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/60">Delivered</p>
                        <p class="font-bold">3,187</p>
                    </div>
                    <div class="text-center p-2 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/60">Responded</p>
                        <p class="font-bold">892</p>
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="iconify lucide--calendar size-4 text-base-content/60"></span>
                        <span>Started: Jan 12, 2024</span>
                    </div>
                    <span class="badge badge-success badge-sm">Active</span>
                </div>

                <div class="mt-3">
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span>Performance</span>
                        <span class="font-bold">28.0%</span>
                    </div>
                    <progress class="progress progress-warning" value="28" max="100"></progress>
                </div>
            </div>
        </div>

        <!-- Campaign Card 4 -->
        <div class="bg-base-100 card shadow">
            <div class="card-body">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="bg-info/10 rounded-lg p-3">
                            <span class="iconify lucide--layers size-6 text-info"></span>
                        </div>
                        <div>
                            <h4 class="font-bold">Multi-Channel Promo</h4>
                            <span class="badge badge-info badge-sm mt-1">Multi</span>
                        </div>
                    </div>
                    <div class="dropdown dropdown-end">
                        <button tabindex="0" class="btn btn-ghost btn-sm btn-square">
                            <span class="iconify lucide--more-vertical size-4"></span>
                        </button>
                        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-40 p-2 shadow">
                            <li><a><span class="iconify lucide--eye size-4"></span> View</a></li>
                            <li><a><span class="iconify lucide--pencil size-4"></span> Edit</a></li>
                            <li><a><span class="iconify lucide--pause size-4"></span> Pause</a></li>
                            <li><a class="text-error"><span class="iconify lucide--trash-2 size-4"></span> Delete</a></li>
                        </ul>
                    </div>
                </div>

                <p class="text-sm text-base-content/60 mb-4">Comprehensive campaign across email, SMS, and push notifications for maximum reach.</p>

                <div class="grid grid-cols-3 gap-2 mb-4">
                    <div class="text-center p-2 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/60">Total Sent</p>
                        <p class="font-bold">18,542</p>
                    </div>
                    <div class="text-center p-2 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/60">Reached</p>
                        <p class="font-bold">16,892</p>
                    </div>
                    <div class="text-center p-2 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/60">Engaged</p>
                        <p class="font-bold">4,523</p>
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="iconify lucide--calendar size-4 text-base-content/60"></span>
                        <span>Started: Jan 08, 2024</span>
                    </div>
                    <span class="badge badge-success badge-sm">Active</span>
                </div>

                <div class="mt-3">
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span>Performance</span>
                        <span class="font-bold">26.8%</span>
                    </div>
                    <progress class="progress progress-info" value="27" max="100"></progress>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scheduled & Past Campaigns -->
<div class="mt-6 bg-base-100 card shadow">
    <div class="card-body">
        <div class="flex items-center justify-between mb-4">
            <h3 class="card-title text-base">All Campaigns</h3>
            <button class="btn btn-ghost btn-sm">
                <span class="iconify lucide--download size-4"></span>
                Export
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Campaign Name</th>
                        <th>Type</th>
                        <th>Recipients</th>
                        <th>Engagement</th>
                        <th>Conversion</th>
                        <th>Date Range</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="iconify lucide--mail text-primary size-4"></span>
                                <span class="font-medium">Flash Sale Weekend</span>
                            </div>
                        </td>
                        <td><span class="badge badge-primary badge-sm">Email</span></td>
                        <td>8,542</td>
                        <td>
                            <div>
                                <p class="text-sm">1,847</p>
                                <p class="text-xs text-base-content/60">21.6%</p>
                            </div>
                        </td>
                        <td class="font-semibold text-success">12.5%</td>
                        <td>
                            <div class="text-sm">
                                <p>Jan 15 - Jan 17</p>
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
                                    <li><a><span class="iconify lucide--pencil size-4"></span> Edit</a></li>
                                    <li><a><span class="iconify lucide--pause size-4"></span> Pause</a></li>
                                    <li><a class="text-error"><span class="iconify lucide--trash-2 size-4"></span> Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="iconify lucide--smartphone text-success size-4"></span>
                                <span class="font-medium">New Arrivals Alert</span>
                            </div>
                        </td>
                        <td><span class="badge badge-success badge-sm">Push</span></td>
                        <td>12,340</td>
                        <td>
                            <div>
                                <p class="text-sm">3,248</p>
                                <p class="text-xs text-base-content/60">26.3%</p>
                            </div>
                        </td>
                        <td class="font-semibold text-success">8.2%</td>
                        <td>
                            <div class="text-sm">
                                <p>Jan 10 - Jan 20</p>
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
                                    <li><a><span class="iconify lucide--pencil size-4"></span> Edit</a></li>
                                    <li><a><span class="iconify lucide--pause size-4"></span> Pause</a></li>
                                    <li><a class="text-error"><span class="iconify lucide--trash-2 size-4"></span> Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="iconify lucide--calendar text-warning size-4"></span>
                                <span class="font-medium">Valentine's Day Special</span>
                            </div>
                        </td>
                        <td><span class="badge badge-warning badge-sm">Multi</span></td>
                        <td>15,000</td>
                        <td>
                            <div>
                                <p class="text-sm">-</p>
                                <p class="text-xs text-base-content/60">-</p>
                            </div>
                        </td>
                        <td class="font-semibold">-</td>
                        <td>
                            <div class="text-sm">
                                <p>Feb 10 - Feb 15</p>
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
                                    <li><a><span class="iconify lucide--pencil size-4"></span> Edit</a></li>
                                    <li><a class="text-error"><span class="iconify lucide--trash-2 size-4"></span> Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="iconify lucide--mail text-base-content/40 size-4"></span>
                                <span class="font-medium text-base-content/60">Year End Sale 2023</span>
                            </div>
                        </td>
                        <td><span class="badge badge-ghost badge-sm">Email</span></td>
                        <td>10,542</td>
                        <td>
                            <div>
                                <p class="text-sm">4,234</p>
                                <p class="text-xs text-base-content/60">40.2%</p>
                            </div>
                        </td>
                        <td class="font-semibold text-success">18.5%</td>
                        <td>
                            <div class="text-sm text-base-content/60">
                                <p>Dec 20 - Dec 31</p>
                            </div>
                        </td>
                        <td><span class="badge badge-ghost badge-sm">Completed</span></td>
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
                Showing 1 to 4 of 15 campaigns
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