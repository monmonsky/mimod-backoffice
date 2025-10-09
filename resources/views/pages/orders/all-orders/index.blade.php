@extends('layouts.app')

@section('title', 'All Orders')
@section('page_title', 'Orders')
@section('page_subtitle', 'All Orders')

@section('content')
<x-page-header
    title="All Orders"
    :breadcrumbs="[
        ['label' => 'Nexus', 'url' => route('dashboard')],
        ['label' => 'Orders'],
        ['label' => 'All Orders']
    ]"
/>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Total Orders</p>
                    <p class="text-2xl" id="statTotalOrders">...</p>
                    <p class="text-xs text-base-content/60">All time</p>
                </div>
                <span class="iconify lucide--shopping-cart size-8 text-base-content/20"></span>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-sm">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Pending</p>
                    <p class="text-2xl" id="statPendingOrders">...</p>
                    <p class="text-xs text-base-content/60">Awaiting confirmation</p>
                </div>
                <span class="iconify lucide--clock size-8"></span>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-sm">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Processing</p>
                    <p class="text-2xl" id="statProcessingOrders">...</p>
                    <p class="text-xs text-base-content/60">Being prepared</p>
                </div>
                <span class="iconify lucide--package size-8"></span>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-sm">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Completed</p>
                    <p class="text-2xl" id="statCompletedOrders">...</p>
                    <p class="text-xs text-base-content/60">Successfully delivered</p>
                </div>
                <span class="iconify lucide--check-circle-2 size-8"></span>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="mt-6">
    <x-filter-section
        id="filterForm"
        title="Filter Orders"
        :action="route('orders.all-orders.index')"
        method="GET"
    >
        <x-slot name="filters">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-form.input
                    name="order_number"
                    label="Order Number"
                    placeholder="Search by order number"
                    :value="request('order_number')"
                />

                <x-form.input
                    name="customer"
                    label="Customer"
                    placeholder="Search by customer name/email"
                    :value="request('customer')"
                />

                <x-form.select
                    name="status"
                    label="Status"
                    :options="[
                        '' => 'All Status',
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled'
                    ]"
                    :value="request('status')"
                />

                <x-form.input
                    name="date_from"
                    label="Date From"
                    type="date"
                    :value="request('date_from')"
                />
            </div>
        </x-slot>

        <x-slot name="actions">
            <button type="submit" class="btn btn-primary btn-sm">
                <span class="iconify lucide--search size-4"></span>
                Apply Filter
            </button>
            <button type="button" id="clearFilters" class="btn btn-ghost btn-sm">
                <span class="iconify lucide--x size-4"></span>
                Reset
            </button>
        </x-slot>
    </x-filter-section>
</div>

<!-- Orders Table -->
<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body p-0">
            <div class="flex flex-col gap-4 px-5 pt-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="inline-flex items-center gap-3 flex-wrap">
                    <label class="input input-sm">
                        <span class="iconify lucide--search text-base-content/80 size-3.5"></span>
                        <input
                            class="w-24 sm:w-36"
                            placeholder="Quick search..."
                            type="search"
                            id="searchInput" />
                    </label>
                </div>

                <div class="inline-flex items-center gap-2">
                    @if(hasPermission('orders.all-orders.export'))
                    <button class="btn btn-sm btn-ghost">
                        <span class="iconify lucide--download size-4"></span>
                        Export
                    </button>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="table table-xs md:table-sm">
                    <thead>
                        <tr>
                            <th>Order Number</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody">
                        <tr id="loadingRow">
                            <td colspan="7" class="text-center py-8">
                                <span class="loading loading-spinner loading-md"></span>
                                <p class="mt-2 text-base-content/60">Loading orders...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div id="paginationContainer"></div>
        </div>
    </div>
</div>

<!-- Order Detail Modal -->
<x-modal id="orderDetailModal" size="max-w-4xl">
    <x-slot name="title">
        <h3 class="font-bold text-lg">Order Details</h3>
    </x-slot>

    <div id="orderDetailContent">
        <!-- Order details will be loaded here -->
    </div>

    <x-slot name="footer">
        <button type="button" class="btn btn-ghost" onclick="orderDetailModal.close()">Close</button>
    </x-slot>
</x-modal>

@endsection

@section('customjs')
@vite(['resources/js/modules/orders/all-orders/index.js'])
@endsection
