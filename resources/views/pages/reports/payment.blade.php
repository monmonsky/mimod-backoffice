@extends('layouts.app')

@section('title', 'Payment Report')
@section('page_title', 'Reports')
@section('page_subtitle', 'Payment Report')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Payment Analytics Report</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Reports</li>
            <li class="opacity-80">Payment Report</li>
        </ul>
    </div>
</div>

<!-- Payment Metrics -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Total Transactions</p>
                    <h3 class="text-2xl font-bold mt-1">1,247</h3>
                    <p class="text-xs text-success mt-2">+8.3% this month</p>
                </div>
                <div class="bg-primary/10 rounded-full p-3">
                    <span class="iconify lucide--credit-card size-6 text-primary"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Success Rate</p>
                    <h3 class="text-2xl font-bold mt-1">96.8%</h3>
                    <p class="text-xs text-success mt-2">+1.2% improvement</p>
                </div>
                <div class="bg-success/10 rounded-full p-3">
                    <span class="iconify lucide--check-circle-2 size-6 text-success"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Pending Payments</p>
                    <h3 class="text-2xl font-bold mt-1">23</h3>
                    <p class="text-xs text-warning mt-2">Awaiting confirmation</p>
                </div>
                <div class="bg-warning/10 rounded-full p-3">
                    <span class="iconify lucide--clock size-6 text-warning"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Failed Payments</p>
                    <h3 class="text-2xl font-bold mt-1">40</h3>
                    <p class="text-xs text-error mt-2">3.2% failure rate</p>
                </div>
                <div class="bg-error/10 rounded-full p-3">
                    <span class="iconify lucide--x-circle size-6 text-error"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Payment Methods -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h3 class="card-title text-base mb-4">Payment Methods Distribution</h3>
            <div class="h-64">
                <canvas id="paymentMethodsChart"></canvas>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-2">
                <div class="flex items-center justify-between p-2 bg-base-200 rounded">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-primary"></div>
                        <span class="text-sm">Credit Card</span>
                    </div>
                    <span class="text-sm font-bold">42%</span>
                </div>
                <div class="flex items-center justify-between p-2 bg-base-200 rounded">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-info"></div>
                        <span class="text-sm">Bank Transfer</span>
                    </div>
                    <span class="text-sm font-bold">28%</span>
                </div>
                <div class="flex items-center justify-between p-2 bg-base-200 rounded">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-success"></div>
                        <span class="text-sm">E-Wallet</span>
                    </div>
                    <span class="text-sm font-bold">22%</span>
                </div>
                <div class="flex items-center justify-between p-2 bg-base-200 rounded">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-warning"></div>
                        <span class="text-sm">COD</span>
                    </div>
                    <span class="text-sm font-bold">8%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Status Trend -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h3 class="card-title text-base mb-4">Payment Status Trend</h3>
            <div class="h-64">
                <canvas id="paymentStatusChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Payment Method Performance -->
<div class="mt-6 bg-base-100 card shadow">
    <div class="card-body">
        <h3 class="card-title text-base mb-4">Payment Method Performance</h3>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Payment Method</th>
                        <th>Transactions</th>
                        <th>Success Rate</th>
                        <th>Total Amount</th>
                        <th>Avg Transaction</th>
                        <th>Failed</th>
                        <th>Trend</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="bg-primary/10 rounded p-2">
                                    <span class="iconify lucide--credit-card size-5 text-primary"></span>
                                </div>
                                <span class="font-medium">Credit Card</span>
                            </div>
                        </td>
                        <td>524</td>
                        <td><span class="badge badge-success">98.5%</span></td>
                        <td class="font-semibold">Rp 19.2M</td>
                        <td>Rp 366K</td>
                        <td>8</td>
                        <td><span class="text-success">+12%</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="bg-info/10 rounded p-2">
                                    <span class="iconify lucide--building size-5 text-info"></span>
                                </div>
                                <span class="font-medium">Bank Transfer</span>
                            </div>
                        </td>
                        <td>349</td>
                        <td><span class="badge badge-success">97.2%</span></td>
                        <td class="font-semibold">Rp 12.8M</td>
                        <td>Rp 367K</td>
                        <td>10</td>
                        <td><span class="text-success">+5%</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="bg-success/10 rounded p-2">
                                    <span class="iconify lucide--smartphone size-5 text-success"></span>
                                </div>
                                <span class="font-medium">E-Wallet (GoPay, OVO)</span>
                            </div>
                        </td>
                        <td>274</td>
                        <td><span class="badge badge-success">96.8%</span></td>
                        <td class="font-semibold">Rp 10.1M</td>
                        <td>Rp 368K</td>
                        <td>9</td>
                        <td><span class="text-success">+18%</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="bg-warning/10 rounded p-2">
                                    <span class="iconify lucide--banknote size-5 text-warning"></span>
                                </div>
                                <span class="font-medium">Cash on Delivery</span>
                            </div>
                        </td>
                        <td>100</td>
                        <td><span class="badge badge-warning">92.0%</span></td>
                        <td class="font-semibold">Rp 3.1M</td>
                        <td>Rp 310K</td>
                        <td>13</td>
                        <td><span class="text-error">-8%</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="mt-6 bg-base-100 card shadow">
    <div class="card-body">
        <div class="flex items-center justify-between mb-4">
            <h3 class="card-title text-base">Recent Payment Transactions</h3>
            <button class="btn btn-ghost btn-sm">
                <span class="iconify lucide--download size-4"></span>
                Export
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Date & Time</th>
                        <th>Customer</th>
                        <th>Payment Method</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="font-mono text-sm">#PAY-2024-001</td>
                        <td>
                            <div>
                                <p class="text-sm">2024-01-15</p>
                                <p class="text-xs text-base-content/60">14:35:22</p>
                            </div>
                        </td>
                        <td>John Doe</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="iconify lucide--credit-card size-4"></span>
                                <span>Credit Card</span>
                            </div>
                        </td>
                        <td class="font-semibold">Rp 850,000</td>
                        <td><span class="badge badge-success badge-sm">Success</span></td>
                        <td>
                            <button class="btn btn-ghost btn-xs">
                                <span class="iconify lucide--eye size-4"></span>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-mono text-sm">#PAY-2024-002</td>
                        <td>
                            <div>
                                <p class="text-sm">2024-01-15</p>
                                <p class="text-xs text-base-content/60">13:22:15</p>
                            </div>
                        </td>
                        <td>Jane Smith</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="iconify lucide--building size-4"></span>
                                <span>Bank Transfer</span>
                            </div>
                        </td>
                        <td class="font-semibold">Rp 620,000</td>
                        <td><span class="badge badge-warning badge-sm">Pending</span></td>
                        <td>
                            <button class="btn btn-ghost btn-xs">
                                <span class="iconify lucide--eye size-4"></span>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-mono text-sm">#PAY-2024-003</td>
                        <td>
                            <div>
                                <p class="text-sm">2024-01-14</p>
                                <p class="text-xs text-base-content/60">16:45:33</p>
                            </div>
                        </td>
                        <td>Bob Wilson</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="iconify lucide--smartphone size-4"></span>
                                <span>GoPay</span>
                            </div>
                        </td>
                        <td class="font-semibold">Rp 1,250,000</td>
                        <td><span class="badge badge-success badge-sm">Success</span></td>
                        <td>
                            <button class="btn btn-ghost btn-xs">
                                <span class="iconify lucide--eye size-4"></span>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-mono text-sm">#PAY-2024-004</td>
                        <td>
                            <div>
                                <p class="text-sm">2024-01-14</p>
                                <p class="text-xs text-base-content/60">11:20:45</p>
                            </div>
                        </td>
                        <td>Alice Brown</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="iconify lucide--banknote size-4"></span>
                                <span>COD</span>
                            </div>
                        </td>
                        <td class="font-semibold">Rp 350,000</td>
                        <td><span class="badge badge-success badge-sm">Success</span></td>
                        <td>
                            <button class="btn btn-ghost btn-xs">
                                <span class="iconify lucide--eye size-4"></span>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-mono text-sm">#PAY-2024-005</td>
                        <td>
                            <div>
                                <p class="text-sm">2024-01-13</p>
                                <p class="text-xs text-base-content/60">09:15:12</p>
                            </div>
                        </td>
                        <td>Charlie Davis</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="iconify lucide--credit-card size-4"></span>
                                <span>Credit Card</span>
                            </div>
                        </td>
                        <td class="font-semibold">Rp 980,000</td>
                        <td><span class="badge badge-error badge-sm">Failed</span></td>
                        <td>
                            <button class="btn btn-ghost btn-xs">
                                <span class="iconify lucide--eye size-4"></span>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-between items-center mt-4">
            <div class="text-sm text-base-content/60">
                Showing 1 to 5 of 1,247 transactions
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
    // Payment Methods Chart
    const methodsCtx = document.getElementById('paymentMethodsChart').getContext('2d');
    new Chart(methodsCtx, {
        type: 'doughnut',
        data: {
            labels: ['Credit Card', 'Bank Transfer', 'E-Wallet', 'COD'],
            datasets: [{
                data: [42, 28, 22, 8],
                backgroundColor: [
                    'rgb(59, 130, 246)',
                    'rgb(59, 130, 246, 0.7)',
                    'rgb(34, 197, 94)',
                    'rgb(251, 191, 36)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // Payment Status Chart
    const statusCtx = document.getElementById('paymentStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'bar',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [
                {
                    label: 'Success',
                    data: [295, 310, 287, 335],
                    backgroundColor: 'rgba(34, 197, 94, 0.8)'
                },
                {
                    label: 'Failed',
                    data: [8, 12, 9, 11],
                    backgroundColor: 'rgba(239, 68, 68, 0.8)'
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