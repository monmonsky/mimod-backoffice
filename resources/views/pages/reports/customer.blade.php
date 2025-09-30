@extends('layouts.app')

@section('title', 'Customer Report')
@section('page_title', 'Reports')
@section('page_subtitle', 'Customer Report')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Customer Analytics Report</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Reports</li>
            <li class="opacity-80">Customer Report</li>
        </ul>
    </div>
</div>

<!-- Customer Metrics -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Total Customers</p>
                    <h3 class="text-2xl font-bold mt-1">8,542</h3>
                    <p class="text-xs text-success mt-2">+245 this month</p>
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
                    <p class="text-sm text-base-content/60">New Customers</p>
                    <h3 class="text-2xl font-bold mt-1">1,287</h3>
                    <p class="text-xs text-info mt-2">Last 30 days</p>
                </div>
                <div class="bg-info/10 rounded-full p-3">
                    <span class="iconify lucide--user-plus size-6 text-info"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Repeat Rate</p>
                    <h3 class="text-2xl font-bold mt-1">62%</h3>
                    <p class="text-xs text-success mt-2">+5% vs last month</p>
                </div>
                <div class="bg-success/10 rounded-full p-3">
                    <span class="iconify lucide--repeat size-6 text-success"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Avg Lifetime Value</p>
                    <h3 class="text-2xl font-bold mt-1">Rp 2.4M</h3>
                    <p class="text-xs text-warning mt-2">Per customer</p>
                </div>
                <div class="bg-warning/10 rounded-full p-3">
                    <span class="iconify lucide--wallet size-6 text-warning"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Customer Growth Chart -->
<div class="mt-6 bg-base-100 card shadow">
    <div class="card-body">
        <h3 class="card-title text-base mb-4">Customer Growth</h3>
        <div class="h-64">
            <canvas id="customerGrowthChart"></canvas>
        </div>
    </div>
</div>

<!-- Customer Segments & Top Customers -->
<div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Customer Segments -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h3 class="card-title text-base mb-4">Customer Segments</h3>

            <div class="space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium">VIP Customers (10+)</span>
                        <span class="text-sm font-bold">892</span>
                    </div>
                    <progress class="progress progress-primary" value="892" max="8542"></progress>
                    <p class="text-xs text-base-content/60 mt-1">10.4% of total • Avg spend: Rp 8.5M</p>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium">Frequent (5-9 orders)</span>
                        <span class="text-sm font-bold">1,854</span>
                    </div>
                    <progress class="progress progress-info" value="1854" max="8542"></progress>
                    <p class="text-xs text-base-content/60 mt-1">21.7% of total • Avg spend: Rp 4.2M</p>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium">Regular (2-4 orders)</span>
                        <span class="text-sm font-bold">3,421</span>
                    </div>
                    <progress class="progress progress-success" value="3421" max="8542"></progress>
                    <p class="text-xs text-base-content/60 mt-1">40.0% of total • Avg spend: Rp 1.8M</p>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium">New (1 order)</span>
                        <span class="text-sm font-bold">2,375</span>
                    </div>
                    <progress class="progress progress-warning" value="2375" max="8542"></progress>
                    <p class="text-xs text-base-content/60 mt-1">27.9% of total • Avg spend: Rp 450K</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Customers -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h3 class="card-title text-base mb-4">Top Customers</h3>

            <div class="space-y-3">
                <div class="flex items-center gap-3 p-3 bg-primary/5 rounded-lg">
                    <div class="avatar placeholder">
                        <div class="bg-primary text-primary-content rounded-full w-10">
                            <span class="text-sm">JD</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">John Doe</p>
                        <p class="text-sm text-base-content/60">32 orders</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold">Rp 12.5M</p>
                        <p class="text-xs text-base-content/60">Lifetime</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-info/5 rounded-lg">
                    <div class="avatar placeholder">
                        <div class="bg-info text-info-content rounded-full w-10">
                            <span class="text-sm">JS</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">Jane Smith</p>
                        <p class="text-sm text-base-content/60">28 orders</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold">Rp 10.8M</p>
                        <p class="text-xs text-base-content/60">Lifetime</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-success/5 rounded-lg">
                    <div class="avatar placeholder">
                        <div class="bg-success text-success-content rounded-full w-10">
                            <span class="text-sm">BW</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">Bob Wilson</p>
                        <p class="text-sm text-base-content/60">24 orders</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold">Rp 9.2M</p>
                        <p class="text-xs text-base-content/60">Lifetime</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-warning/5 rounded-lg">
                    <div class="avatar placeholder">
                        <div class="bg-warning text-warning-content rounded-full w-10">
                            <span class="text-sm">AB</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">Alice Brown</p>
                        <p class="text-sm text-base-content/60">21 orders</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold">Rp 8.5M</p>
                        <p class="text-xs text-base-content/60">Lifetime</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-error/5 rounded-lg">
                    <div class="avatar placeholder">
                        <div class="bg-error text-error-content rounded-full w-10">
                            <span class="text-sm">CD</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">Charlie Davis</p>
                        <p class="text-sm text-base-content/60">19 orders</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold">Rp 7.8M</p>
                        <p class="text-xs text-base-content/60">Lifetime</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Customer Table -->
<div class="mt-6 bg-base-100 card shadow">
    <div class="card-body">
        <div class="flex items-center justify-between mb-4">
            <h3 class="card-title text-base">Customer Details</h3>
            <button class="btn btn-ghost btn-sm">
                <span class="iconify lucide--download size-4"></span>
                Export
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Location</th>
                        <th>Orders</th>
                        <th>Total Spent</th>
                        <th>Avg Order</th>
                        <th>Last Order</th>
                        <th>Segment</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="avatar placeholder">
                                    <div class="bg-primary text-primary-content rounded-full w-8">
                                        <span class="text-xs">JD</span>
                                    </div>
                                </div>
                                <span class="font-medium">John Doe</span>
                            </div>
                        </td>
                        <td>john@example.com</td>
                        <td>Jakarta</td>
                        <td>32</td>
                        <td class="font-semibold">Rp 12.5M</td>
                        <td>Rp 390K</td>
                        <td>2024-01-15</td>
                        <td><span class="badge badge-primary badge-sm">VIP</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="avatar placeholder">
                                    <div class="bg-info text-info-content rounded-full w-8">
                                        <span class="text-xs">JS</span>
                                    </div>
                                </div>
                                <span class="font-medium">Jane Smith</span>
                            </div>
                        </td>
                        <td>jane@example.com</td>
                        <td>Bandung</td>
                        <td>28</td>
                        <td class="font-semibold">Rp 10.8M</td>
                        <td>Rp 385K</td>
                        <td>2024-01-14</td>
                        <td><span class="badge badge-primary badge-sm">VIP</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="avatar placeholder">
                                    <div class="bg-success text-success-content rounded-full w-8">
                                        <span class="text-xs">BW</span>
                                    </div>
                                </div>
                                <span class="font-medium">Bob Wilson</span>
                            </div>
                        </td>
                        <td>bob@example.com</td>
                        <td>Surabaya</td>
                        <td>7</td>
                        <td class="font-semibold">Rp 3.2M</td>
                        <td>Rp 457K</td>
                        <td>2024-01-13</td>
                        <td><span class="badge badge-info badge-sm">Frequent</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="avatar placeholder">
                                    <div class="bg-warning text-warning-content rounded-full w-8">
                                        <span class="text-xs">AB</span>
                                    </div>
                                </div>
                                <span class="font-medium">Alice Brown</span>
                            </div>
                        </td>
                        <td>alice@example.com</td>
                        <td>Medan</td>
                        <td>3</td>
                        <td class="font-semibold">Rp 1.4M</td>
                        <td>Rp 466K</td>
                        <td>2024-01-12</td>
                        <td><span class="badge badge-success badge-sm">Regular</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="avatar placeholder">
                                    <div class="bg-error text-error-content rounded-full w-8">
                                        <span class="text-xs">CD</span>
                                    </div>
                                </div>
                                <span class="font-medium">Charlie Davis</span>
                            </div>
                        </td>
                        <td>charlie@example.com</td>
                        <td>Semarang</td>
                        <td>1</td>
                        <td class="font-semibold">Rp 425K</td>
                        <td>Rp 425K</td>
                        <td>2024-01-10</td>
                        <td><span class="badge badge-warning badge-sm">New</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-between items-center mt-4">
            <div class="text-sm text-base-content/60">
                Showing 1 to 5 of 8,542 customers
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

@section('customjs')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('customerGrowthChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'New Customers',
                data: [320, 450, 380, 520, 480, 590, 640, 710, 680, 750, 820, 890],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endsection