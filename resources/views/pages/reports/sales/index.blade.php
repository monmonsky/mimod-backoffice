@extends('layouts.app')

@section('title', 'Sales Report')
@section('page_title', 'Sales Report')
@section('page_subtitle', 'Sales Analytics & Insights')

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

<!-- Date Filter & Export -->
<div class="mt-6 flex flex-wrap gap-3 items-center justify-between">
    <div class="inline-flex items-center gap-3">
        <label class="input input-sm">
            <span class="iconify lucide--calendar text-base-content/80 size-3.5"></span>
            <input
                type="date"
                id="startDate"
                class="w-32"
                placeholder="Start Date" />
        </label>
        <span class="text-sm text-base-content/60">to</span>
        <label class="input input-sm">
            <span class="iconify lucide--calendar text-base-content/80 size-3.5"></span>
            <input
                type="date"
                id="endDate"
                class="w-32"
                placeholder="End Date" />
        </label>
        <button class="btn btn-primary btn-sm" id="filterBtn">
            <span class="iconify lucide--search"></span>
            Filter
        </button>
    </div>
    <div class="inline-flex items-center gap-3">
        @if(hasPermission('reports.sales.export'))
        <button class="btn btn-outline btn-sm" id="exportBtn">
            <span class="iconify lucide--download"></span>
            Export
        </button>
        @endif
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Total Sales</p>
                    <p class="text-2xl font-semibold mt-1" id="totalSales">-</p>
                    <p class="text-xs text-base-content/60 mt-1">All time sales</p>
                </div>
                <div class="bg-primary/10 p-3 rounded-lg">
                    <span class="iconify lucide--shopping-cart size-5 text-primary"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Total Orders</p>
                    <p class="text-2xl font-semibold mt-1 text-success" id="totalOrders">-</p>
                    <p class="text-xs text-base-content/60 mt-1">Completed orders</p>
                </div>
                <div class="bg-success/10 p-3 rounded-lg">
                    <span class="iconify lucide--package size-5 text-success"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Average Order</p>
                    <p class="text-2xl font-semibold mt-1 text-info" id="avgOrder">-</p>
                    <p class="text-xs text-base-content/60 mt-1">Per transaction</p>
                </div>
                <div class="bg-info/10 p-3 rounded-lg">
                    <span class="iconify lucide--arrow-up-from-line size-5 text-info"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Growth Rate</p>
                    <p class="text-2xl font-semibold mt-1 text-warning" id="growthRate">-</p>
                    <p class="text-xs text-base-content/60 mt-1">vs last period</p>
                </div>
                <div class="bg-warning/10 p-3 rounded-lg">
                    <span class="iconify lucide--bar-chart size-5 text-warning"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Section -->
<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h3 class="text-lg font-semibold mb-4">Sales Trend</h3>
            <div class="h-64 flex items-center justify-center text-base-content/50">
                <div class="text-center">
                    <span class="iconify lucide--line-chart size-12 mb-2"></span>
                    <p>Chart will be displayed here</p>
                    <p class="text-xs mt-1">(Chart.js or ApexCharts integration)</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body p-0">
            <div class="flex items-center justify-between px-5 pt-5">
                <h3 class="text-lg font-semibold">Sales Details</h3>
                <label class="input input-sm">
                    <span class="iconify lucide--search text-base-content/80 size-3.5"></span>
                    <input
                        class="w-24 sm:w-36"
                        placeholder="Search"
                        type="search"
                        id="searchInput" />
                </label>
            </div>

            <div class="mt-4 overflow-auto">
                <table class="table" id="salesTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" class="text-center py-8 text-base-content/50">
                                <span class="iconify lucide--badge-x size-8 mb-2"></span>
                                <p>No data available</p>
                                <p class="text-xs mt-1">Data will be populated from API</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customjs')
@vite(['resources/js/modules/reports/sales/index.js'])
@endsection
