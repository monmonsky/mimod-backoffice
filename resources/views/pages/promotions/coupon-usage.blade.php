@extends('layouts.app')

@section('title', 'Coupon Usage')
@section('page_title', 'Promotions')
@section('page_subtitle', 'Coupon Usage')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Coupon Usage Report</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Promotions</li>
            <li class="opacity-80">Coupon Usage</li>
        </ul>
    </div>
</div>

<!-- Statistics Cards -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Total Usage</p>
                    <h3 class="text-2xl font-bold mt-1">1,847</h3>
                    <p class="text-xs text-primary mt-2">All time</p>
                </div>
                <div class="bg-primary/10 rounded-full p-3">
                    <span class="iconify lucide--bar-chart size-6 text-primary"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">This Month</p>
                    <h3 class="text-2xl font-bold mt-1">342</h3>
                    <p class="text-xs text-success mt-2">+18% vs last month</p>
                </div>
                <div class="bg-success/10 rounded-full p-3">
                    <span class="iconify lucide--trending-up size-6 text-success"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Total Discount</p>
                    <h3 class="text-2xl font-bold mt-1">Rp 285M</h3>
                    <p class="text-xs text-warning mt-2">All time savings</p>
                </div>
                <div class="bg-warning/10 rounded-full p-3">
                    <span class="iconify lucide--wallet size-6 text-warning"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Avg Discount</p>
                    <h3 class="text-2xl font-bold mt-1">Rp 154K</h3>
                    <p class="text-xs text-info mt-2">Per usage</p>
                </div>
                <div class="bg-info/10 rounded-full p-3">
                    <span class="iconify lucide--calculator size-6 text-info"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Usage Trend Chart -->
<div class="mt-6 bg-base-100 card shadow">
    <div class="card-body">
        <div class="flex items-center justify-between mb-4">
            <h3 class="card-title text-base">Usage Trend</h3>
            <div class="flex gap-2">
                <button class="btn btn-sm btn-ghost btn-active">7 Days</button>
                <button class="btn btn-sm btn-ghost">30 Days</button>
                <button class="btn btn-sm btn-ghost">90 Days</button>
            </div>
        </div>
        <div class="h-64">
            <canvas id="usageChart"></canvas>
        </div>
    </div>
</div>

<!-- Top Performing Coupons & Recent Usage -->
<div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Top Performing -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h3 class="card-title text-base mb-4">Top Performing Coupons</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3 p-3 bg-primary/5 rounded-lg">
                    <div class="flex items-center justify-center w-10 h-10 bg-primary text-primary-content rounded-lg font-bold">
                        1
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">SAVE20</p>
                        <p class="text-sm text-base-content/60">145 uses</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-primary">Rp 42.5M</p>
                        <p class="text-xs text-base-content/60">Total discount</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-info/5 rounded-lg">
                    <div class="flex items-center justify-center w-10 h-10 bg-info text-info-content rounded-lg font-bold">
                        2
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">FREESHIP</p>
                        <p class="text-sm text-base-content/60">89 uses</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-info">Rp 8.9M</p>
                        <p class="text-xs text-base-content/60">Total discount</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-success/5 rounded-lg">
                    <div class="flex items-center justify-center w-10 h-10 bg-success text-success-content rounded-lg font-bold">
                        3
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">FIRST50K</p>
                        <p class="text-sm text-base-content/60">56 uses</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-success">Rp 2.8M</p>
                        <p class="text-xs text-base-content/60">Total discount</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-warning/5 rounded-lg">
                    <div class="flex items-center justify-center w-10 h-10 bg-warning text-warning-content rounded-lg font-bold">
                        4
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">FLASH15</p>
                        <p class="text-sm text-base-content/60">32 uses</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-warning">Rp 4.2M</p>
                        <p class="text-xs text-base-content/60">Total discount</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-error/5 rounded-lg">
                    <div class="flex items-center justify-center w-10 h-10 bg-error text-error-content rounded-lg font-bold">
                        5
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">MEMBER10</p>
                        <p class="text-sm text-base-content/60">28 uses</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-error">Rp 3.1M</p>
                        <p class="text-xs text-base-content/60">Total discount</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Usage -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h3 class="card-title text-base mb-4">Recent Usage</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3 p-3 border border-base-300 rounded-lg">
                    <div class="avatar placeholder">
                        <div class="bg-primary text-primary-content rounded-full w-10">
                            <span class="text-sm">JD</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">John Doe</p>
                        <p class="text-sm text-base-content/60">Used <span class="font-mono font-semibold">SAVE20</span></p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-primary">-Rp 120K</p>
                        <p class="text-xs text-base-content/60">5 mins ago</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 border border-base-300 rounded-lg">
                    <div class="avatar placeholder">
                        <div class="bg-info text-info-content rounded-full w-10">
                            <span class="text-sm">JS</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">Jane Smith</p>
                        <p class="text-sm text-base-content/60">Used <span class="font-mono font-semibold">FREESHIP</span></p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-info">-Rp 25K</p>
                        <p class="text-xs text-base-content/60">12 mins ago</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 border border-base-300 rounded-lg">
                    <div class="avatar placeholder">
                        <div class="bg-success text-success-content rounded-full w-10">
                            <span class="text-sm">BW</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">Bob Wilson</p>
                        <p class="text-sm text-base-content/60">Used <span class="font-mono font-semibold">FIRST50K</span></p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-success">-Rp 50K</p>
                        <p class="text-xs text-base-content/60">25 mins ago</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 border border-base-300 rounded-lg">
                    <div class="avatar placeholder">
                        <div class="bg-warning text-warning-content rounded-full w-10">
                            <span class="text-sm">AB</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">Alice Brown</p>
                        <p class="text-sm text-base-content/60">Used <span class="font-mono font-semibold">SAVE20</span></p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-warning">-Rp 85K</p>
                        <p class="text-xs text-base-content/60">1 hour ago</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 border border-base-300 rounded-lg">
                    <div class="avatar placeholder">
                        <div class="bg-error text-error-content rounded-full w-10">
                            <span class="text-sm">CD</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">Charlie Davis</p>
                        <p class="text-sm text-base-content/60">Used <span class="font-mono font-semibold">FLASH15</span></p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-error">-Rp 45K</p>
                        <p class="text-xs text-base-content/60">2 hours ago</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Usage Details Table -->
<div class="mt-6 bg-base-100 card shadow">
    <div class="card-body">
        <div class="flex items-center justify-between mb-4">
            <h3 class="card-title text-base">Usage Details</h3>
            <div class="flex gap-2">
                <select class="select select-bordered select-sm">
                    <option selected>All Coupons</option>
                    <option>SAVE20</option>
                    <option>FREESHIP</option>
                    <option>FIRST50K</option>
                </select>
                <button class="btn btn-ghost btn-sm">
                    <span class="iconify lucide--download size-4"></span>
                    Export
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Coupon Code</th>
                        <th>Customer</th>
                        <th>Order ID</th>
                        <th>Order Value</th>
                        <th>Discount</th>
                        <th>Final Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2024-01-15 14:35</td>
                        <td><span class="font-mono font-bold text-primary">SAVE20</span></td>
                        <td>John Doe</td>
                        <td>#ORD-2024-0542</td>
                        <td>Rp 600,000</td>
                        <td class="text-success font-semibold">-Rp 120,000</td>
                        <td class="font-bold">Rp 480,000</td>
                    </tr>
                    <tr>
                        <td>2024-01-15 14:22</td>
                        <td><span class="font-mono font-bold text-success">FREESHIP</span></td>
                        <td>Jane Smith</td>
                        <td>#ORD-2024-0541</td>
                        <td>Rp 350,000</td>
                        <td class="text-success font-semibold">-Rp 25,000</td>
                        <td class="font-bold">Rp 325,000</td>
                    </tr>
                    <tr>
                        <td>2024-01-15 14:10</td>
                        <td><span class="font-mono font-bold text-warning">FIRST50K</span></td>
                        <td>Bob Wilson</td>
                        <td>#ORD-2024-0540</td>
                        <td>Rp 280,000</td>
                        <td class="text-success font-semibold">-Rp 50,000</td>
                        <td class="font-bold">Rp 230,000</td>
                    </tr>
                    <tr>
                        <td>2024-01-15 13:40</td>
                        <td><span class="font-mono font-bold text-primary">SAVE20</span></td>
                        <td>Alice Brown</td>
                        <td>#ORD-2024-0539</td>
                        <td>Rp 425,000</td>
                        <td class="text-success font-semibold">-Rp 85,000</td>
                        <td class="font-bold">Rp 340,000</td>
                    </tr>
                    <tr>
                        <td>2024-01-15 13:15</td>
                        <td><span class="font-mono font-bold text-info">FLASH15</span></td>
                        <td>Charlie Davis</td>
                        <td>#ORD-2024-0538</td>
                        <td>Rp 300,000</td>
                        <td class="text-success font-semibold">-Rp 45,000</td>
                        <td class="font-bold">Rp 255,000</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-between items-center mt-4">
            <div class="text-sm text-base-content/60">
                Showing 1 to 5 of 1,847 usage records
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
    const ctx = document.getElementById('usageChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
            datasets: [{
                label: 'Coupon Usage',
                data: [45, 52, 38, 65, 48, 72, 55],
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
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection