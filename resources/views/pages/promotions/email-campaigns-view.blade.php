@extends('layouts.app')

@section('title', 'Campaign Details')
@section('page_title', 'Promotions')
@section('page_subtitle', 'Campaign Details')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Weekend Flash Sale Campaign</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li><a href="{{ route('promotions.email-campaigns') }}">Email Campaigns</a></li>
            <li class="opacity-80">Details</li>
        </ul>
    </div>
</div>

<!-- Campaign Status & Actions -->
<div class="mt-6 bg-base-100 card shadow">
    <div class="card-body">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="bg-success/10 rounded-full p-4">
                    <span class="iconify lucide--mail size-8 text-success"></span>
                </div>
                <div>
                    <h2 class="text-xl font-bold">Weekend Flash Sale</h2>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="badge badge-success badge-sm">Sending</span>
                        <span class="badge badge-primary badge-sm">Promotional</span>
                        <span class="text-sm text-base-content/60">Created: Jan 15, 2024</span>
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <button class="btn btn-outline btn-sm">
                    <span class="iconify lucide--pause size-4"></span>
                    Pause
                </button>
                <button class="btn btn-outline btn-sm">
                    <span class="iconify lucide--copy size-4"></span>
                    Duplicate
                </button>
                <div class="dropdown dropdown-end">
                    <button tabindex="0" class="btn btn-outline btn-sm">
                        <span class="iconify lucide--more-vertical size-4"></span>
                    </button>
                    <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-48 p-2 shadow">
                        <li><a><span class="iconify lucide--edit size-4"></span> Edit Campaign</a></li>
                        <li><a><span class="iconify lucide--send size-4"></span> Send Test Email</a></li>
                        <li><a><span class="iconify lucide--download size-4"></span> Export Report</a></li>
                        <li><a class="text-error"><span class="iconify lucide--trash-2 size-4"></span> Delete</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performance Metrics -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Recipients</p>
                    <h3 class="text-2xl font-bold mt-1">8,542</h3>
                </div>
                <div class="bg-info/10 rounded-full p-3">
                    <span class="iconify lucide--users size-5 text-info"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Sent</p>
                    <h3 class="text-2xl font-bold mt-1">6,234</h3>
                    <p class="text-xs text-success mt-1">73% delivered</p>
                </div>
                <div class="bg-success/10 rounded-full p-3">
                    <span class="iconify lucide--send size-5 text-success"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Opened</p>
                    <h3 class="text-2xl font-bold mt-1">2,847</h3>
                    <p class="text-xs text-primary mt-1">45.7% open rate</p>
                </div>
                <div class="bg-primary/10 rounded-full p-3">
                    <span class="iconify lucide--mail-open size-5 text-primary"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Clicked</p>
                    <h3 class="text-2xl font-bold mt-1">892</h3>
                    <p class="text-xs text-warning mt-1">14.3% CTR</p>
                </div>
                <div class="bg-warning/10 rounded-full p-3">
                    <span class="iconify lucide--mouse-pointer-click size-5 text-warning"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Bounced</p>
                    <h3 class="text-2xl font-bold mt-1">124</h3>
                    <p class="text-xs text-error mt-1">1.5% bounce rate</p>
                </div>
                <div class="bg-error/10 rounded-full p-3">
                    <span class="iconify lucide--alert-circle size-5 text-error"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts & Analytics -->
<div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Performance Over Time -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h3 class="card-title text-base mb-4">Performance Over Time</h3>
            <div class="h-64">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Link Clicks -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h3 class="card-title text-base mb-4">Top Clicked Links</h3>
            <div class="space-y-3">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <a href="#" class="text-sm link link-primary truncate max-w-[70%]">https://nexusstore.com/flash-sale</a>
                        <span class="text-sm font-bold">425 clicks</span>
                    </div>
                    <progress class="progress progress-primary" value="425" max="892"></progress>
                    <p class="text-xs text-base-content/60 mt-1">47.6% of total clicks</p>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <a href="#" class="text-sm link link-primary truncate max-w-[70%]">https://nexusstore.com/products</a>
                        <span class="text-sm font-bold">289 clicks</span>
                    </div>
                    <progress class="progress progress-info" value="289" max="892"></progress>
                    <p class="text-xs text-base-content/60 mt-1">32.4% of total clicks</p>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <a href="#" class="text-sm link link-primary truncate max-w-[70%]">https://nexusstore.com/categories</a>
                        <span class="text-sm font-bold">178 clicks</span>
                    </div>
                    <progress class="progress progress-success" value="178" max="892"></progress>
                    <p class="text-xs text-base-content/60 mt-1">20.0% of total clicks</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Campaign Details & Email Preview -->
<div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Campaign Details -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-base-100 card shadow">
            <div class="card-body">
                <h3 class="card-title text-base mb-4">Campaign Details</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-base-content/60 mb-1">Subject</p>
                        <p class="font-medium">Don't miss our weekend flash sale!</p>
                    </div>
                    <div>
                        <p class="text-base-content/60 mb-1">Preview Text</p>
                        <p class="font-medium">Get up to 50% off on selected items...</p>
                    </div>
                    <div>
                        <p class="text-base-content/60 mb-1">From</p>
                        <p class="font-medium">Nexus Store &lt;noreply@nexusstore.com&gt;</p>
                    </div>
                    <div>
                        <p class="text-base-content/60 mb-1">Reply To</p>
                        <p class="font-medium">support@nexusstore.com</p>
                    </div>
                    <div>
                        <p class="text-base-content/60 mb-1">Template</p>
                        <p class="font-medium">Flash Sale Promo</p>
                    </div>
                    <div>
                        <p class="text-base-content/60 mb-1">Recipients</p>
                        <div class="flex flex-wrap gap-1 mt-1">
                            <span class="badge badge-sm badge-outline">VIP Customers</span>
                            <span class="badge badge-sm badge-outline">Frequent Buyers</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-base-content/60 mb-1">Send Time</p>
                        <p class="font-medium">Jan 15, 2024 08:00 AM</p>
                    </div>
                    <div>
                        <p class="text-base-content/60 mb-1">Created By</p>
                        <p class="font-medium">Admin User</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-base-100 card shadow">
            <div class="card-body">
                <h3 class="card-title text-base mb-4">Device Stats</h3>
                <div class="space-y-3">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="iconify lucide--smartphone size-4"></span>
                                <span class="text-sm">Mobile</span>
                            </div>
                            <span class="text-sm font-bold">1,698 (59.6%)</span>
                        </div>
                        <progress class="progress progress-primary" value="59.6" max="100"></progress>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="iconify lucide--monitor size-4"></span>
                                <span class="text-sm">Desktop</span>
                            </div>
                            <span class="text-sm font-bold">912 (32.0%)</span>
                        </div>
                        <progress class="progress progress-info" value="32" max="100"></progress>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="iconify lucide--tablet size-4"></span>
                                <span class="text-sm">Tablet</span>
                            </div>
                            <span class="text-sm font-bold">237 (8.4%)</span>
                        </div>
                        <progress class="progress progress-success" value="8.4" max="100"></progress>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Preview -->
    <div class="lg:col-span-2">
        <div class="bg-base-100 card shadow">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="card-title text-base">Email Preview</h3>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-sm btn-active">Desktop</button>
                        <button class="btn btn-sm">Mobile</button>
                    </div>
                </div>

                <div class="border border-base-300 rounded-lg overflow-hidden">
                    <!-- Email Header -->
                    <div class="bg-base-200 p-4 border-b border-base-300">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="avatar placeholder">
                                <div class="bg-primary text-primary-content rounded-full w-10">
                                    <span class="text-xs">NS</span>
                                </div>
                            </div>
                            <div>
                                <p class="font-medium text-sm">Nexus Store</p>
                                <p class="text-xs text-base-content/60">noreply@nexusstore.com</p>
                            </div>
                        </div>
                        <p class="font-bold">Don't miss our weekend flash sale!</p>
                        <p class="text-sm text-base-content/60">Get up to 50% off on selected items...</p>
                    </div>

                    <!-- Email Body Preview -->
                    <div class="bg-white p-6" style="max-height: 600px; overflow-y: auto;">
                        <div class="bg-gradient-to-r from-primary to-secondary rounded-lg p-8 text-center text-white mb-6">
                            <h1 class="text-3xl font-bold mb-2">Weekend Flash Sale!</h1>
                            <p class="text-xl">Up to 50% OFF</p>
                        </div>

                        <div class="prose max-w-none">
                            <p class="text-base-content">Hi Customer,</p>
                            <p class="text-base-content">This weekend only, enjoy massive discounts on our most popular products! Don't miss out on these incredible deals.</p>

                            <div class="grid grid-cols-2 gap-4 my-6">
                                <div class="border border-base-300 rounded-lg p-4 text-center">
                                    <div class="bg-base-200 h-32 rounded mb-2"></div>
                                    <p class="font-semibold">Product Name</p>
                                    <p class="text-sm text-error line-through">Rp 500,000</p>
                                    <p class="text-lg font-bold text-primary">Rp 250,000</p>
                                </div>
                                <div class="border border-base-300 rounded-lg p-4 text-center">
                                    <div class="bg-base-200 h-32 rounded mb-2"></div>
                                    <p class="font-semibold">Product Name</p>
                                    <p class="text-sm text-error line-through">Rp 400,000</p>
                                    <p class="text-lg font-bold text-primary">Rp 200,000</p>
                                </div>
                            </div>

                            <div class="text-center my-6">
                                <button class="btn btn-primary btn-lg">Shop Now</button>
                            </div>

                            <p class="text-sm text-base-content/60">Hurry! Sale ends Sunday at midnight.</p>
                        </div>

                        <div class="border-t border-base-300 mt-6 pt-6 text-center text-sm text-base-content/60">
                            <p>© 2024 Nexus Store. All rights reserved.</p>
                            <p class="mt-2">
                                <a href="#" class="link">Unsubscribe</a> |
                                <a href="#" class="link">View in browser</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recipients List -->
<div class="mt-6 bg-base-100 card shadow">
    <div class="card-body">
        <div class="flex items-center justify-between mb-4">
            <h3 class="card-title text-base">Recipients Activity</h3>
            <div class="flex gap-2">
                <select class="select select-bordered select-sm">
                    <option>All Recipients</option>
                    <option>Opened</option>
                    <option>Clicked</option>
                    <option>Bounced</option>
                    <option>Not Opened</option>
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
                        <th>Recipient</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Opened</th>
                        <th>Clicked</th>
                        <th>Sent At</th>
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
                        <td><span class="badge badge-success badge-sm">Delivered</span></td>
                        <td>
                            <div class="flex items-center gap-1">
                                <span class="iconify lucide--check text-success size-4"></span>
                                <span class="text-sm">Yes (2x)</span>
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center gap-1">
                                <span class="iconify lucide--check text-success size-4"></span>
                                <span class="text-sm">3 links</span>
                            </div>
                        </td>
                        <td>Jan 15, 08:02</td>
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
                        <td><span class="badge badge-success badge-sm">Delivered</span></td>
                        <td>
                            <div class="flex items-center gap-1">
                                <span class="iconify lucide--check text-success size-4"></span>
                                <span class="text-sm">Yes (1x)</span>
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center gap-1">
                                <span class="iconify lucide--x text-base-content/40 size-4"></span>
                                <span class="text-sm text-base-content/60">No</span>
                            </div>
                        </td>
                        <td>Jan 15, 08:03</td>
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
                        <td><span class="badge badge-success badge-sm">Delivered</span></td>
                        <td>
                            <div class="flex items-center gap-1">
                                <span class="iconify lucide--x text-base-content/40 size-4"></span>
                                <span class="text-sm text-base-content/60">Not yet</span>
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center gap-1">
                                <span class="iconify lucide--x text-base-content/40 size-4"></span>
                                <span class="text-sm text-base-content/60">No</span>
                            </div>
                        </td>
                        <td>Jan 15, 08:05</td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="avatar placeholder">
                                    <div class="bg-error text-error-content rounded-full w-8">
                                        <span class="text-xs">AB</span>
                                    </div>
                                </div>
                                <span class="font-medium">Alice Brown</span>
                            </div>
                        </td>
                        <td>alice.invalid@example.com</td>
                        <td><span class="badge badge-error badge-sm">Bounced</span></td>
                        <td>
                            <div class="flex items-center gap-1">
                                <span class="iconify lucide--minus text-base-content/40 size-4"></span>
                                <span class="text-sm text-base-content/60">-</span>
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center gap-1">
                                <span class="iconify lucide--minus text-base-content/40 size-4"></span>
                                <span class="text-sm text-base-content/60">-</span>
                            </div>
                        </td>
                        <td>Jan 15, 08:02</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-between items-center mt-4">
            <div class="text-sm text-base-content/60">
                Showing 1 to 4 of 6,234 recipients
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
    const ctx = document.getElementById('performanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00'],
            datasets: [
                {
                    label: 'Sent',
                    data: [850, 1450, 2150, 3200, 4300, 5200, 5800, 6234],
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Opened',
                    data: [120, 380, 650, 1120, 1650, 2150, 2520, 2847],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Clicked',
                    data: [15, 68, 145, 298, 485, 672, 784, 892],
                    borderColor: 'rgb(251, 191, 36)',
                    backgroundColor: 'rgba(251, 191, 36, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
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