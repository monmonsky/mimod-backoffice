@extends('layouts.app')

@section('title', 'Inventory Report')
@section('page_title', 'Reports')
@section('page_subtitle', 'Inventory Report')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Inventory Management Report</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Reports</li>
            <li class="opacity-80">Inventory Report</li>
        </ul>
    </div>
</div>

<!-- Inventory Metrics -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Total Stock Value</p>
                    <h3 class="text-2xl font-bold mt-1">Rp 125M</h3>
                    <p class="text-xs text-success mt-2">+8.5% this month</p>
                </div>
                <div class="bg-primary/10 rounded-full p-3">
                    <span class="iconify lucide--package size-6 text-primary"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Total Items</p>
                    <h3 class="text-2xl font-bold mt-1">12,847</h3>
                    <p class="text-xs text-info mt-2">Across 248 products</p>
                </div>
                <div class="bg-info/10 rounded-full p-3">
                    <span class="iconify lucide--boxes size-6 text-info"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Low Stock Items</p>
                    <h3 class="text-2xl font-bold mt-1">23</h3>
                    <p class="text-xs text-warning mt-2">Need restock soon</p>
                </div>
                <div class="bg-warning/10 rounded-full p-3">
                    <span class="iconify lucide--alert-triangle size-6 text-warning"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Out of Stock</p>
                    <h3 class="text-2xl font-bold mt-1">10</h3>
                    <p class="text-xs text-error mt-2">Immediate action needed</p>
                </div>
                <div class="bg-error/10 rounded-full p-3">
                    <span class="iconify lucide--x-circle size-6 text-error"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Status & Movement -->
<div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Stock Status -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h3 class="card-title text-base mb-4">Stock Status Overview</h3>
            <div class="h-64">
                <canvas id="stockStatusChart"></canvas>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-2">
                <div class="flex items-center justify-between p-2 bg-base-200 rounded">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-success"></div>
                        <span class="text-sm">In Stock</span>
                    </div>
                    <span class="text-sm font-bold">215 (86.7%)</span>
                </div>
                <div class="flex items-center justify-between p-2 bg-base-200 rounded">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-warning"></div>
                        <span class="text-sm">Low Stock</span>
                    </div>
                    <span class="text-sm font-bold">23 (9.3%)</span>
                </div>
                <div class="flex items-center justify-between p-2 bg-base-200 rounded">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-error"></div>
                        <span class="text-sm">Out of Stock</span>
                    </div>
                    <span class="text-sm font-bold">10 (4.0%)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Movement -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h3 class="card-title text-base mb-4">Stock Movement Trend</h3>
            <div class="h-64">
                <canvas id="stockMovementChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Critical Alerts -->
<div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Low Stock Alert -->
    <div class="bg-base-100 card shadow border-l-4 border-warning">
        <div class="card-body">
            <h3 class="card-title text-base mb-4 flex items-center gap-2">
                <span class="iconify lucide--alert-triangle text-warning"></span>
                Low Stock Alert
            </h3>
            <div class="space-y-2">
                <div class="flex items-center justify-between p-3 bg-warning/5 rounded-lg">
                    <div class="flex-1">
                        <p class="font-medium">Kids T-Shirt (Size M)</p>
                        <p class="text-sm text-base-content/60">SKU: KTS-001-M</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-warning">12 units</p>
                        <p class="text-xs">Min: 50</p>
                    </div>
                </div>
                <div class="flex items-center justify-between p-3 bg-warning/5 rounded-lg">
                    <div class="flex-1">
                        <p class="font-medium">Baby Romper Set</p>
                        <p class="text-sm text-base-content/60">SKU: BRS-003</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-warning">8 units</p>
                        <p class="text-xs">Min: 30</p>
                    </div>
                </div>
                <div class="flex items-center justify-between p-3 bg-warning/5 rounded-lg">
                    <div class="flex-1">
                        <p class="font-medium">School Bag</p>
                        <p class="text-sm text-base-content/60">SKU: SBG-005</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-warning">15 units</p>
                        <p class="text-xs">Min: 40</p>
                    </div>
                </div>
            </div>
            <button class="btn btn-warning btn-sm mt-4 w-full">
                <span class="iconify lucide--plus size-4"></span>
                Create Restock Orders
            </button>
        </div>
    </div>

    <!-- Out of Stock Alert -->
    <div class="bg-base-100 card shadow border-l-4 border-error">
        <div class="card-body">
            <h3 class="card-title text-base mb-4 flex items-center gap-2">
                <span class="iconify lucide--x-circle text-error"></span>
                Out of Stock Alert
            </h3>
            <div class="space-y-2">
                <div class="flex items-center justify-between p-3 bg-error/5 rounded-lg">
                    <div class="flex-1">
                        <p class="font-medium">Winter Coat</p>
                        <p class="text-sm text-base-content/60">SKU: WCT-008</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-error">0 units</p>
                        <p class="text-xs">Last sold: 2 days ago</p>
                    </div>
                </div>
                <div class="flex items-center justify-between p-3 bg-error/5 rounded-lg">
                    <div class="flex-1">
                        <p class="font-medium">Rain Boots (Size 30)</p>
                        <p class="text-sm text-base-content/60">SKU: RBT-009-30</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-error">0 units</p>
                        <p class="text-xs">Last sold: 1 day ago</p>
                    </div>
                </div>
                <div class="flex items-center justify-between p-3 bg-error/5 rounded-lg">
                    <div class="flex-1">
                        <p class="font-medium">Sun Hat</p>
                        <p class="text-sm text-base-content/60">SKU: SHT-010</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-error">0 units</p>
                        <p class="text-xs">Last sold: 5 days ago</p>
                    </div>
                </div>
            </div>
            <button class="btn btn-error btn-sm mt-4 w-full">
                <span class="iconify lucide--shopping-cart size-4"></span>
                Order Immediately
            </button>
        </div>
    </div>
</div>

<!-- Inventory Details Table -->
<div class="mt-6 bg-base-100 card shadow">
    <div class="card-body">
        <div class="flex items-center justify-between mb-4">
            <h3 class="card-title text-base">Inventory Details</h3>
            <div class="flex gap-2">
                <select class="select select-bordered select-sm">
                    <option>All Status</option>
                    <option>In Stock</option>
                    <option>Low Stock</option>
                    <option>Out of Stock</option>
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
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Current Stock</th>
                        <th>Min Stock</th>
                        <th>Value</th>
                        <th>Last Updated</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="avatar">
                                    <div class="w-10 rounded bg-primary/10 flex items-center justify-center">
                                        <span class="iconify lucide--shirt size-5 text-primary"></span>
                                    </div>
                                </div>
                                <span class="font-medium">Kids T-Shirt Premium</span>
                            </div>
                        </td>
                        <td class="font-mono text-sm">KTS-001</td>
                        <td>Clothing</td>
                        <td class="font-semibold">156</td>
                        <td>50</td>
                        <td>Rp 5.7M</td>
                        <td>2024-01-15</td>
                        <td><span class="badge badge-success badge-sm">In Stock</span></td>
                        <td>
                            <button class="btn btn-ghost btn-xs">
                                <span class="iconify lucide--pencil size-4"></span>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="avatar">
                                    <div class="w-10 rounded bg-info/10 flex items-center justify-center">
                                        <span class="iconify lucide--footprints size-5 text-info"></span>
                                    </div>
                                </div>
                                <span class="font-medium">Kids Sneakers</span>
                            </div>
                        </td>
                        <td class="font-mono text-sm">KSN-002</td>
                        <td>Footwear</td>
                        <td class="font-semibold">89</td>
                        <td>40</td>
                        <td>Rp 3.2M</td>
                        <td>2024-01-15</td>
                        <td><span class="badge badge-success badge-sm">In Stock</span></td>
                        <td>
                            <button class="btn btn-ghost btn-xs">
                                <span class="iconify lucide--pencil size-4"></span>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="avatar">
                                    <div class="w-10 rounded bg-warning/10 flex items-center justify-center">
                                        <span class="iconify lucide--package size-5 text-warning"></span>
                                    </div>
                                </div>
                                <span class="font-medium">Baby Romper Set</span>
                            </div>
                        </td>
                        <td class="font-mono text-sm">BRS-003</td>
                        <td>Clothing</td>
                        <td class="font-semibold text-warning">8</td>
                        <td>30</td>
                        <td>Rp 334K</td>
                        <td>2024-01-14</td>
                        <td><span class="badge badge-warning badge-sm">Low Stock</span></td>
                        <td>
                            <button class="btn btn-ghost btn-xs">
                                <span class="iconify lucide--pencil size-4"></span>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="avatar">
                                    <div class="w-10 rounded bg-success/10 flex items-center justify-center">
                                        <span class="iconify lucide--star size-5 text-success"></span>
                                    </div>
                                </div>
                                <span class="font-medium">Kids Jacket</span>
                            </div>
                        </td>
                        <td class="font-mono text-sm">KJK-004</td>
                        <td>Clothing</td>
                        <td class="font-semibold">67</td>
                        <td>30</td>
                        <td>Rp 2.6M</td>
                        <td>2024-01-14</td>
                        <td><span class="badge badge-success badge-sm">In Stock</span></td>
                        <td>
                            <button class="btn btn-ghost btn-xs">
                                <span class="iconify lucide--pencil size-4"></span>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="avatar">
                                    <div class="w-10 rounded bg-error/10 flex items-center justify-center">
                                        <span class="iconify lucide--cloud-snow size-5 text-error"></span>
                                    </div>
                                </div>
                                <span class="font-medium">Winter Coat</span>
                            </div>
                        </td>
                        <td class="font-mono text-sm">WCT-008</td>
                        <td>Clothing</td>
                        <td class="font-semibold text-error">0</td>
                        <td>20</td>
                        <td>Rp 0</td>
                        <td>2024-01-13</td>
                        <td><span class="badge badge-error badge-sm">Out of Stock</span></td>
                        <td>
                            <button class="btn btn-ghost btn-xs">
                                <span class="iconify lucide--pencil size-4"></span>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-between items-center mt-4">
            <div class="text-sm text-base-content/60">
                Showing 1 to 5 of 248 products
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
    // Stock Status Chart
    const statusCtx = document.getElementById('stockStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['In Stock', 'Low Stock', 'Out of Stock'],
            datasets: [{
                data: [215, 23, 10],
                backgroundColor: [
                    'rgb(34, 197, 94)',
                    'rgb(251, 191, 36)',
                    'rgb(239, 68, 68)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // Stock Movement Chart
    const movementCtx = document.getElementById('stockMovementChart').getContext('2d');
    new Chart(movementCtx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [
                {
                    label: 'Stock In',
                    data: [450, 380, 520, 480],
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Stock Out',
                    data: [380, 420, 390, 445],
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });
</script>
@endsection