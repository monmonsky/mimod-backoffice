@extends('layouts.app')

@section('title', 'Email Campaigns')
@section('page_title', 'Promotions')
@section('page_subtitle', 'Email Campaigns')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Email Marketing Campaigns</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Promotions</li>
            <li class="opacity-80">Email Campaigns</li>
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
                    <h3 class="text-2xl font-bold mt-1">32</h3>
                    <p class="text-xs text-info mt-2">All email campaigns</p>
                </div>
                <div class="bg-info/10 rounded-full p-3">
                    <span class="iconify lucide--mail size-6 text-info"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Emails Sent</p>
                    <h3 class="text-2xl font-bold mt-1">125.4K</h3>
                    <p class="text-xs text-success mt-2">This month</p>
                </div>
                <div class="bg-success/10 rounded-full p-3">
                    <span class="iconify lucide--send size-6 text-success"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Open Rate</p>
                    <h3 class="text-2xl font-bold mt-1">42.5%</h3>
                    <p class="text-xs text-primary mt-2">+3.2% vs last month</p>
                </div>
                <div class="bg-primary/10 rounded-full p-3">
                    <span class="iconify lucide--mail-open size-6 text-primary"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Click Rate</p>
                    <h3 class="text-2xl font-bold mt-1">18.7%</h3>
                    <p class="text-xs text-warning mt-2">Average CTR</p>
                </div>
                <div class="bg-warning/10 rounded-full p-3">
                    <span class="iconify lucide--mouse-pointer-click size-6 text-warning"></span>
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
                    <option>Draft</option>
                    <option>Scheduled</option>
                    <option>Sending</option>
                    <option>Sent</option>
                    <option>Paused</option>
                </select>
                <select class="select select-bordered w-full sm:w-auto">
                    <option selected>All Types</option>
                    <option>Promotional</option>
                    <option>Newsletter</option>
                    <option>Transactional</option>
                    <option>Abandoned Cart</option>
                </select>
            </div>
            <a href="{{ route('promotions.email-campaigns.create') }}" class="btn btn-primary w-full lg:w-auto">
                <span class="iconify lucide--plus size-4"></span>
                Create Campaign
            </a>
        </div>
    </div>
</div>

<!-- Active/Scheduled Campaigns -->
<div class="mt-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-medium">Active & Scheduled</h3>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Campaign Card 1 -->
        <div class="bg-base-100 card shadow border-l-4 border-success">
            <div class="card-body">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <h4 class="font-bold">Weekend Flash Sale</h4>
                            <span class="badge badge-success badge-sm">Sending</span>
                        </div>
                        <p class="text-sm text-base-content/60">Promotional • Flash Sale Weekend template</p>
                    </div>
                    <div class="dropdown dropdown-end">
                        <button tabindex="0" class="btn btn-ghost btn-sm btn-square">
                            <span class="iconify lucide--more-vertical size-4"></span>
                        </button>
                        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-40 p-2 shadow">
                            <li><a><span class="iconify lucide--eye size-4"></span> View</a></li>
                            <li><a><span class="iconify lucide--pencil size-4"></span> Edit</a></li>
                            <li><a><span class="iconify lucide--pause size-4"></span> Pause</a></li>
                            <li><a><span class="iconify lucide--copy size-4"></span> Duplicate</a></li>
                            <li><a class="text-error"><span class="iconify lucide--trash-2 size-4"></span> Delete</a></li>
                        </ul>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-2 mb-3">
                    <div class="text-center p-2 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/60">Recipients</p>
                        <p class="font-bold text-sm">8,542</p>
                    </div>
                    <div class="text-center p-2 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/60">Sent</p>
                        <p class="font-bold text-sm">6,234</p>
                    </div>
                    <div class="text-center p-2 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/60">Opened</p>
                        <p class="font-bold text-sm">2,847</p>
                    </div>
                    <div class="text-center p-2 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/60">Clicked</p>
                        <p class="font-bold text-sm">892</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-base-content/60">Progress</span>
                        <span class="font-semibold">73%</span>
                    </div>
                    <progress class="progress progress-success" value="73" max="100"></progress>
                </div>

                <div class="flex items-center justify-between text-sm mt-3 pt-3 border-t border-base-300">
                    <div class="flex items-center gap-2">
                        <span class="iconify lucide--calendar size-4 text-base-content/60"></span>
                        <span>Started: Jan 15, 2024 08:00</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="badge badge-primary badge-sm">Open: 45.7%</span>
                        <span class="badge badge-warning badge-sm">CTR: 14.3%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campaign Card 2 -->
        <div class="bg-base-100 card shadow border-l-4 border-warning">
            <div class="card-body">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <h4 class="font-bold">New Collection Launch</h4>
                            <span class="badge badge-warning badge-sm">Scheduled</span>
                        </div>
                        <p class="text-sm text-base-content/60">Newsletter • Product Launch template</p>
                    </div>
                    <div class="dropdown dropdown-end">
                        <button tabindex="0" class="btn btn-ghost btn-sm btn-square">
                            <span class="iconify lucide--more-vertical size-4"></span>
                        </button>
                        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-40 p-2 shadow">
                            <li><a><span class="iconify lucide--eye size-4"></span> View</a></li>
                            <li><a><span class="iconify lucide--pencil size-4"></span> Edit</a></li>
                            <li><a><span class="iconify lucide--send size-4"></span> Send Now</a></li>
                            <li><a><span class="iconify lucide--copy size-4"></span> Duplicate</a></li>
                            <li><a class="text-error"><span class="iconify lucide--trash-2 size-4"></span> Delete</a></li>
                        </ul>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-2 mb-3">
                    <div class="text-center p-2 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/60">Recipients</p>
                        <p class="font-bold text-sm">12,340</p>
                    </div>
                    <div class="text-center p-2 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/60">Segments</p>
                        <p class="font-bold text-sm">3</p>
                    </div>
                    <div class="text-center p-2 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/60">-</p>
                        <p class="font-bold text-sm">-</p>
                    </div>
                    <div class="text-center p-2 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/60">-</p>
                        <p class="font-bold text-sm">-</p>
                    </div>
                </div>

                <div class="alert alert-warning">
                    <span class="iconify lucide--clock size-4"></span>
                    <span class="text-sm">Scheduled to send on Jan 18, 2024 at 10:00 AM</span>
                </div>

                <div class="flex items-center justify-between text-sm mt-3 pt-3 border-t border-base-300">
                    <div class="flex items-center gap-2">
                        <span class="iconify lucide--user size-4 text-base-content/60"></span>
                        <span>Created by: Admin</span>
                    </div>
                    <button class="btn btn-sm btn-primary">
                        <span class="iconify lucide--play size-4"></span>
                        Send Now
                    </button>
                </div>
            </div>
        </div>

        <!-- Campaign Card 3 -->
        <div class="bg-base-100 card shadow border-l-4 border-info">
            <div class="card-body">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <h4 class="font-bold">Abandoned Cart Recovery</h4>
                            <span class="badge badge-info badge-sm">Automated</span>
                        </div>
                        <p class="text-sm text-base-content/60">Transactional • Abandoned Cart template</p>
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

                <div class="grid grid-cols-4 gap-2 mb-3">
                    <div class="text-center p-2 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/60">Sent (7d)</p>
                        <p class="font-bold text-sm">342</p>
                    </div>
                    <div class="text-center p-2 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/60">Opened</p>
                        <p class="font-bold text-sm">234</p>
                    </div>
                    <div class="text-center p-2 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/60">Clicked</p>
                        <p class="font-bold text-sm">128</p>
                    </div>
                    <div class="text-center p-2 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/60">Recovered</p>
                        <p class="font-bold text-sm">45</p>
                    </div>
                </div>

                <div class="alert alert-info">
                    <span class="iconify lucide--zap size-4"></span>
                    <span class="text-sm">Auto-triggered 24h after cart abandonment</span>
                </div>

                <div class="flex items-center justify-between text-sm mt-3 pt-3 border-t border-base-300">
                    <div class="flex gap-2">
                        <span class="badge badge-primary badge-sm">Open: 68.4%</span>
                        <span class="badge badge-warning badge-sm">CTR: 37.4%</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-success font-semibold">Rp 22.5M recovered</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campaign Card 4 (Draft) -->
        <div class="bg-base-100 card shadow border-l-4 border-base-300">
            <div class="card-body">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <h4 class="font-bold text-base-content/60">Valentine's Day Special</h4>
                            <span class="badge badge-ghost badge-sm">Draft</span>
                        </div>
                        <p class="text-sm text-base-content/60">Promotional • No template selected</p>
                    </div>
                    <div class="dropdown dropdown-end">
                        <button tabindex="0" class="btn btn-ghost btn-sm btn-square">
                            <span class="iconify lucide--more-vertical size-4"></span>
                        </button>
                        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-40 p-2 shadow">
                            <li><a><span class="iconify lucide--pencil size-4"></span> Edit</a></li>
                            <li><a><span class="iconify lucide--copy size-4"></span> Duplicate</a></li>
                            <li><a class="text-error"><span class="iconify lucide--trash-2 size-4"></span> Delete</a></li>
                        </ul>
                    </div>
                </div>

                <div class="alert">
                    <span class="iconify lucide--alert-circle size-4"></span>
                    <div>
                        <p class="font-semibold text-sm">Campaign incomplete</p>
                        <p class="text-xs">Please complete setup before sending</p>
                    </div>
                </div>

                <div class="mt-3">
                    <p class="text-sm font-medium mb-2">Setup Checklist:</p>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-sm">
                            <span class="iconify lucide--check-circle text-success size-4"></span>
                            <span>Campaign name added</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-base-content/60">
                            <span class="iconify lucide--circle size-4"></span>
                            <span>Email template not selected</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-base-content/60">
                            <span class="iconify lucide--circle size-4"></span>
                            <span>Recipients not defined</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm mt-3 pt-3 border-t border-base-300">
                    <div class="flex items-center gap-2 text-base-content/60">
                        <span class="iconify lucide--calendar size-4"></span>
                        <span>Created: Jan 10, 2024</span>
                    </div>
                    <button class="btn btn-sm btn-primary">
                        <span class="iconify lucide--pencil size-4"></span>
                        Continue Setup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Campaign History Table -->
<div class="mt-6 bg-base-100 card shadow">
    <div class="card-body">
        <div class="flex items-center justify-between mb-4">
            <h3 class="card-title text-base">Campaign History</h3>
            <button class="btn btn-ghost btn-sm">
                <span class="iconify lucide--download size-4"></span>
                Export Report
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Campaign Name</th>
                        <th>Type</th>
                        <th>Recipients</th>
                        <th>Sent</th>
                        <th>Opened</th>
                        <th>Clicked</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="iconify lucide--mail text-success size-4"></span>
                                <span class="font-medium">Weekend Flash Sale</span>
                            </div>
                        </td>
                        <td><span class="badge badge-primary badge-sm">Promotional</span></td>
                        <td>8,542</td>
                        <td>
                            <div>
                                <p class="font-semibold">6,234</p>
                                <p class="text-xs text-base-content/60">73%</p>
                            </div>
                        </td>
                        <td>
                            <div>
                                <p class="font-semibold">2,847</p>
                                <p class="text-xs text-success">45.7%</p>
                            </div>
                        </td>
                        <td>
                            <div>
                                <p class="font-semibold">892</p>
                                <p class="text-xs text-warning">14.3%</p>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm">
                                <p>Jan 15, 2024</p>
                                <p class="text-base-content/60">08:00 AM</p>
                            </div>
                        </td>
                        <td><span class="badge badge-success badge-sm">Sending</span></td>
                        <td>
                            <div class="dropdown dropdown-end">
                                <button tabindex="0" class="btn btn-ghost btn-sm btn-square">
                                    <span class="iconify lucide--more-vertical size-4"></span>
                                </button>
                                <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-40 p-2 shadow">
                                    <li><a><span class="iconify lucide--bar-chart size-4"></span> Analytics</a></li>
                                    <li><a><span class="iconify lucide--pause size-4"></span> Pause</a></li>
                                    <li><a><span class="iconify lucide--copy size-4"></span> Duplicate</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="iconify lucide--mail text-info size-4"></span>
                                <span class="font-medium">Monthly Newsletter - Jan</span>
                            </div>
                        </td>
                        <td><span class="badge badge-info badge-sm">Newsletter</span></td>
                        <td>15,240</td>
                        <td>
                            <div>
                                <p class="font-semibold">15,240</p>
                                <p class="text-xs text-base-content/60">100%</p>
                            </div>
                        </td>
                        <td>
                            <div>
                                <p class="font-semibold">6,482</p>
                                <p class="text-xs text-success">42.5%</p>
                            </div>
                        </td>
                        <td>
                            <div>
                                <p class="font-semibold">2,134</p>
                                <p class="text-xs text-warning">14.0%</p>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm">
                                <p>Jan 01, 2024</p>
                                <p class="text-base-content/60">09:00 AM</p>
                            </div>
                        </td>
                        <td><span class="badge badge-ghost badge-sm">Completed</span></td>
                        <td>
                            <div class="dropdown dropdown-end">
                                <button tabindex="0" class="btn btn-ghost btn-sm btn-square">
                                    <span class="iconify lucide--more-vertical size-4"></span>
                                </button>
                                <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-40 p-2 shadow">
                                    <li><a><span class="iconify lucide--bar-chart size-4"></span> Analytics</a></li>
                                    <li><a><span class="iconify lucide--copy size-4"></span> Duplicate</a></li>
                                    <li><a class="text-error"><span class="iconify lucide--trash-2 size-4"></span> Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="iconify lucide--mail text-warning size-4"></span>
                                <span class="font-medium">Year End Sale 2023</span>
                            </div>
                        </td>
                        <td><span class="badge badge-warning badge-sm">Promotional</span></td>
                        <td>20,542</td>
                        <td>
                            <div>
                                <p class="font-semibold">20,542</p>
                                <p class="text-xs text-base-content/60">100%</p>
                            </div>
                        </td>
                        <td>
                            <div>
                                <p class="font-semibold">9,847</p>
                                <p class="text-xs text-success">47.9%</p>
                            </div>
                        </td>
                        <td>
                            <div>
                                <p class="font-semibold">4,234</p>
                                <p class="text-xs text-warning">20.6%</p>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm">
                                <p>Dec 26, 2023</p>
                                <p class="text-base-content/60">08:00 AM</p>
                            </div>
                        </td>
                        <td><span class="badge badge-ghost badge-sm">Completed</span></td>
                        <td>
                            <div class="dropdown dropdown-end">
                                <button tabindex="0" class="btn btn-ghost btn-sm btn-square">
                                    <span class="iconify lucide--more-vertical size-4"></span>
                                </button>
                                <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-40 p-2 shadow">
                                    <li><a><span class="iconify lucide--bar-chart size-4"></span> Analytics</a></li>
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
                Showing 1 to 3 of 32 campaigns
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