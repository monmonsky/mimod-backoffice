@extends('layouts.app')

@section('title', 'Sales Report')
@section('page_title', 'Reports')
@section('page_subtitle', 'Sales Report')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Sales Report</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Reports</li>
            <li class="opacity-80">Sales Report</li>
        </ul>
    </div>
</div>

<!-- Filters -->
<div class="mt-6 bg-base-100 card shadow">
    <div class="card-body">
        <h3 class="card-title text-base mb-4">Filters</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Date Range</span>
                </label>
                <select class="select select-bordered select-sm">
                    <option>Today</option>
                    <option>Yesterday</option>
                    <option>Last 7 Days</option>
                    <option selected>Last 30 Days</option>
                    <option>This Month</option>
                    <option>Last Month</option>
                    <option>Custom Range</option>
                </select>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Category</span>
                </label>
                <select class="select select-bordered select-sm">
                    <option selected>All Categories</option>
                    <option>Clothing</option>
                    <option>Footwear</option>
                    <option>Accessories</option>
                </select>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Status</span>
                </label>
                <select class="select select-bordered select-sm">
                    <option selected>All Status</option>
                    <option>Completed</option>
                    <option>Pending</option>
                    <option>Cancelled</option>
                </select>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">&nbsp;</span>
                </label>
                <div class="flex gap-2">
                    <button class="btn btn-primary btn-sm flex-1">
                        <span class="iconify lucide--filter size-4"></span>
                        Apply
                    </button>
                    <button class="btn btn-ghost btn-sm">
                        <span class="iconify lucide--x size-4"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center gap-3">
                <div class="bg-primary/10 rounded-lg p-3">
                    <span class="iconify lucide--dollar-sign size-6 text-primary"></span>
                </div>
                <div>
                    <p class="text-sm text-base-content/60">Total Sales</p>
                    <h3 class="text-xl font-bold">Rp 45.2M</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center gap-3">
                <div class="bg-info/10 rounded-lg p-3">
                    <span class="iconify lucide--shopping-bag size-6 text-info"></span>
                </div>
                <div>
                    <p class="text-sm text-base-content/60">Total Orders</p>
                    <h3 class="text-xl font-bold">1,247</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center gap-3">
                <div class="bg-success/10 rounded-lg p-3">
                    <span class="iconify lucide--trending-up size-6 text-success"></span>
                </div>
                <div>
                    <p class="text-sm text-base-content/60">Avg Order Value</p>
                    <h3 class="text-xl font-bold">Rp 362K</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center gap-3">
                <div class="bg-warning/10 rounded-lg p-3">
                    <span class="iconify lucide--percent size-6 text-warning"></span>
                </div>
                <div>
                    <p class="text-sm text-base-content/60">Growth Rate</p>
                    <h3 class="text-xl font-bold">+12.5%</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sales Chart -->
<div class="mt-6 bg-base-100 card shadow">
    <div class="card-body">
        <div class="flex items-center justify-between mb-4">
            <h3 class="card-title text-base">Sales Trend</h3>
            <div class="flex gap-2">
                <button class="btn btn-ghost btn-xs">Daily</button>
                <button class="btn btn-primary btn-xs">Weekly</button>
                <button class="btn btn-ghost btn-xs">Monthly</button>
            </div>
        </div>
        <div class="h-80">
            <canvas id="salesChart"></canvas>
        </div>
    </div>
</div>

<!-- Sales by Category -->
<div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h3 class="card-title text-base mb-4">Sales by Category</h3>
            <div class="h-64">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h3 class="card-title text-base mb-4">Top Performing Products</h3>
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Sales</th>
                            <th>Revenue</th>
                            <th>Growth</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Kids T-Shirt Premium</td>
                            <td>342</td>
                            <td class="font-semibold">Rp 12.5M</td>
                            <td><span class="text-success">+15%</span></td>
                        </tr>
                        <tr>
                            <td>Kids Sneakers</td>
                            <td>287</td>
                            <td class="font-semibold">Rp 10.2M</td>
                            <td><span class="text-success">+8%</span></td>
                        </tr>
                        <tr>
                            <td>Baby Romper Set</td>
                            <td>213</td>
                            <td class="font-semibold">Rp 8.9M</td>
                            <td><span class="text-error">-3%</span></td>
                        </tr>
                        <tr>
                            <td>Kids Jacket</td>
                            <td>198</td>
                            <td class="font-semibold">Rp 7.8M</td>
                            <td><span class="text-success">+12%</span></td>
                        </tr>
                        <tr>
                            <td>School Bag</td>
                            <td>156</td>
                            <td class="font-semibold">Rp 5.4M</td>
                            <td><span class="text-success">+5%</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Sales Table -->
<div class="mt-6 bg-base-100 card shadow">
    <div class="card-body">
        <div class="flex items-center justify-between mb-4">
            <h3 class="card-title text-base">Sales Transactions</h3>
            <button class="btn btn-ghost btn-sm">
                <span class="iconify lucide--download size-4"></span>
                Export
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Products</th>
                        <th>Quantity</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Payment</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2024-01-15</td>
                        <td class="font-mono text-sm">#ORD-001</td>
                        <td>John Doe</td>
                        <td>Kids T-Shirt</td>
                        <td>3</td>
                        <td class="font-semibold">Rp 850,000</td>
                        <td><span class="badge badge-success badge-sm">Completed</span></td>
                        <td>Credit Card</td>
                    </tr>
                    <tr>
                        <td>2024-01-15</td>
                        <td class="font-mono text-sm">#ORD-002</td>
                        <td>Jane Smith</td>
                        <td>Sneakers</td>
                        <td>2</td>
                        <td class="font-semibold">Rp 620,000</td>
                        <td><span class="badge badge-warning badge-sm">Processing</span></td>
                        <td>Bank Transfer</td>
                    </tr>
                    <tr>
                        <td>2024-01-14</td>
                        <td class="font-mono text-sm">#ORD-003</td>
                        <td>Bob Wilson</td>
                        <td>Baby Romper</td>
                        <td>5</td>
                        <td class="font-semibold">Rp 1,250,000</td>
                        <td><span class="badge badge-success badge-sm">Completed</span></td>
                        <td>E-Wallet</td>
                    </tr>
                    <tr>
                        <td>2024-01-14</td>
                        <td class="font-mono text-sm">#ORD-004</td>
                        <td>Alice Brown</td>
                        <td>School Bag</td>
                        <td>1</td>
                        <td class="font-semibold">Rp 350,000</td>
                        <td><span class="badge badge-success badge-sm">Completed</span></td>
                        <td>COD</td>
                    </tr>
                    <tr>
                        <td>2024-01-13</td>
                        <td class="font-mono text-sm">#ORD-005</td>
                        <td>Charlie Davis</td>
                        <td>Kids Jacket</td>
                        <td>4</td>
                        <td class="font-semibold">Rp 980,000</td>
                        <td><span class="badge badge-error badge-sm">Cancelled</span></td>
                        <td>-</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-between items-center mt-4">
            <div class="text-sm text-base-content/60">
                Showing 1 to 5 of 1,247 entries
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
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'Sales (Rp)',
                data: [8500000, 12300000, 9800000, 14600000],
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
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                        }
                    }
                }
            }
        }
    });

    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: ['Clothing', 'Footwear', 'Accessories', 'Others'],
            datasets: [{
                data: [45, 28, 18, 9],
                backgroundColor: [
                    'rgb(59, 130, 246)',
                    'rgb(59, 130, 246, 0.7)',
                    'rgb(251, 191, 36)',
                    'rgb(34, 197, 94)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
@endsection