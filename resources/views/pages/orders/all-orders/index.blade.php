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
    <x-stat-card
        title="Total Orders"
        :value="$statistics->total_orders ?? 0"
        subtitle="All time"
        icon="shopping-cart"
        icon-color="primary"
    />

    <x-stat-card
        title="Pending"
        :value="$statistics->pending_count ?? 0"
        subtitle="Awaiting confirmation"
        icon="clock"
        icon-color="warning"
    />

    <x-stat-card
        title="Processing"
        :value="$statistics->processing_count ?? 0"
        subtitle="Being prepared"
        icon="package"
        icon-color="info"
    />

    <x-stat-card
        title="Completed"
        :value="$statistics->completed_count ?? 0"
        subtitle="Successfully delivered"
        icon="check-circle-2"
        icon-color="success"
    />
</div>

<!-- Filter Section -->
<div class="mt-6">
    <x-filter-section
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
                <span class="iconify lucide--filter size-4"></span>
                Apply Filter
            </button>
            <a href="{{ route('orders.all-orders.index') }}" class="btn btn-ghost btn-sm">
                <span class="iconify lucide--x size-4"></span>
                Reset
            </a>
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
                            <th class="w-8">
                                <input type="checkbox" class="checkbox checkbox-sm" id="selectAll" />
                            </th>
                            <th>Order Number</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTable">
                        @forelse($orders as $order)
                        <tr>
                            <td>
                                <input type="checkbox" class="checkbox checkbox-sm" />
                            </td>
                            <td>
                                <div class="font-medium">{{ $order->order_number }}</div>
                            </td>
                            <td>
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ $order->customer_name }}</span>
                                    <span class="text-xs opacity-60">{{ $order->customer_email }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-sm badge-ghost">{{ $order->items_count ?? 0 }} items</span>
                            </td>
                            <td>
                                <span class="font-medium">Rp {{ number_format($order->total_amount ?? 0, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                @php
                                    $statusMap = [
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'shipped' => 'primary',
                                        'completed' => 'success',
                                        'cancelled' => 'error'
                                    ];
                                    $badgeType = $statusMap[$order->status] ?? 'ghost';
                                @endphp
                                <x-badge :type="$badgeType" :label="ucfirst($order->status)" />
                            </td>
                            <td>
                                <div class="flex flex-col">
                                    <span class="text-xs">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</span>
                                    <span class="text-xs opacity-60">{{ \Carbon\Carbon::parse($order->created_at)->format('H:i') }}</span>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="dropdown dropdown-left">
                                    <button tabindex="0" class="btn btn-ghost btn-xs">
                                        <span class="iconify lucide--more-vertical size-4"></span>
                                    </button>
                                    <ul tabindex="0" class="dropdown-content menu p-2 shadow-lg bg-base-100 rounded-box w-52 z-10 border border-base-300">
                                        <li>
                                            <a class="view-order-btn" data-id="{{ $order->id }}">
                                                <span class="iconify lucide--eye size-4"></span>
                                                View Details
                                            </a>
                                        </li>
                                        @if(hasPermission('orders.all-orders.update'))
                                            @if($order->status === 'pending')
                                            <li>
                                                <a class="update-status-btn" data-id="{{ $order->id }}" data-status="processing">
                                                    <span class="iconify lucide--check size-4"></span>
                                                    Confirm Order
                                                </a>
                                            </li>
                                            @endif
                                        @endif
                                        @if(hasPermission('orders.all-orders.delete'))
                                            @if(in_array($order->status, ['pending', 'cancelled']))
                                            <li>
                                                <form action="{{ route('orders.all-orders.destroy', $order->id) }}" method="POST" class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-error w-full text-left">
                                                        <span class="iconify lucide--trash-2 size-4"></span>
                                                        Delete
                                                    </button>
                                                </form>
                                            </li>
                                            @endif
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-8">
                                <div class="flex flex-col items-center gap-2 text-base-content/60">
                                    <span class="iconify lucide--inbox size-12"></span>
                                    <p>No orders found</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <x-pagination-info :paginator="$orders" />
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
