@extends('layouts.app')

@section('title', 'Completed Orders')
@section('page_title', 'Orders')
@section('page_subtitle', 'Completed Orders')

@section('content')
<x-page-header
    title="Completed Orders"
    :breadcrumbs="[
        ['label' => 'Nexus', 'url' => route('dashboard')],
        ['label' => 'Orders'],
        ['label' => 'Completed Orders']
    ]"
/>

<!-- Statistics Card -->
<div class="grid grid-cols-1 gap-4 mt-6">
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body p-6">
            <div class="flex items-center gap-4">
                <div class="bg-success/10 p-3 rounded-lg">
                    <span class="iconify lucide--check-circle text-success size-8"></span>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-base-content/60">Completed Orders</p>
                    <p class="text-3xl font-bold" id="statCompletedCount">...</p>
                    <p class="text-xs text-base-content/50 mt-1">Successfully delivered</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="mt-6">
    <x-filter-section
        id="filterForm"
        title="Filter Completed Orders"
        :action="route('orders.completed-orders.index')"
        method="GET"
    >
        <x-slot name="filters">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
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
                <span class="iconify lucide--filter size-4"></span>
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
                <label class="input input-sm">
                    <span class="iconify lucide--search text-base-content/80 size-3.5"></span>
                    <input
                        class="w-24 sm:w-36"
                        placeholder="Quick search..."
                        type="search"
                        id="searchInput" />
                </label>
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
                            <td colspan="6" class="text-center py-8">
                                <span class="loading loading-spinner loading-md"></span>
                                <p class="mt-2 text-base-content/60">Loading pending orders...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div id="paginationContainer" class="p-4"></div>
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
@vite(['resources/js/modules/orders/completed-orders/index.js'])
@endsection
