@extends('layouts.app')

@section('title', 'Shipped Orders')
@section('page_title', 'Orders')
@section('page_subtitle', 'Shipped Orders')

@section('content')
<x-page-header
    title="Shipped Orders"
    :breadcrumbs="[
        ['label' => 'Nexus', 'url' => route('dashboard')],
        ['label' => 'Orders'],
        ['label' => 'Shipped Orders']
    ]"
/>

<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2">
    <x-stat-card
        title="Shipped Orders"
        :value="$statistics->shipped_count ?? 0"
        subtitle="On delivery"
        icon="truck"
        icon-color="primary"
    />

    <x-stat-card
        title="Total Value"
        :value="'Rp ' . number_format($orders->sum('total_amount') ?? 0, 0, ',', '.')"
        subtitle="Shipped orders value"
        icon="badge-dollar-sign"
        icon-color="success"
    />
</div>

<!-- Filter Section -->
<div class="mt-6">
    <x-filter-section
        title="Filter Shipped Orders"
        :action="route('orders.shipped-orders.index')"
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

                <x-form.input
                    name="tracking"
                    label="Tracking Number"
                    placeholder="Search by tracking"
                    :value="request('tracking')"
                />

                <x-form.input
                    name="date_from"
                    label="Shipped From"
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
            <a href="{{ route('orders.shipped-orders.index') }}" class="btn btn-ghost btn-sm">
                <span class="iconify lucide--x size-4"></span>
                Reset
            </a>
        </x-slot>
    </x-filter-section>
</div>

<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body p-0">
            <div class="flex flex-col gap-4 px-5 pt-5 sm:flex-row sm:items-center sm:justify-between">
                <label class="input input-sm">
                    <span class="iconify lucide--search text-base-content/80 size-3.5"></span>
                    <input class="w-24 sm:w-36" placeholder="Quick search..." type="search" id="searchInput" />
                </label>
            </div>

            <div class="overflow-x-auto">
                <table class="table table-xs md:table-sm">
                    <thead>
                        <tr>
                            <th>Order Number</th>
                            <th>Customer</th>
                            <th>Tracking</th>
                            <th>Total Amount</th>
                            <th>Shipped Date</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTable">
                        @forelse($orders as $order)
                        <tr>
                            <td><div class="font-medium">{{ $order->order_number }}</div></td>
                            <td>
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ $order->customer_name }}</span>
                                    <span class="text-xs opacity-60">{{ $order->customer_email }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-col">
                                    <span class="text-xs font-medium">{{ $order->tracking_number ?? '-' }}</span>
                                    <span class="text-xs opacity-60">{{ $order->courier ?? '-' }}</span>
                                </div>
                            </td>
                            <td><span class="font-medium">Rp {{ number_format($order->total_amount ?? 0, 0, ',', '.') }}</span></td>
                            <td>
                                <div class="flex flex-col">
                                    <span class="text-xs">{{ $order->shipped_at ? \Carbon\Carbon::parse($order->shipped_at)->format('d M Y') : '-' }}</span>
                                    <span class="text-xs opacity-60">{{ $order->shipped_at ? \Carbon\Carbon::parse($order->shipped_at)->diffForHumans() : '-' }}</span>
                                </div>
                            </td>
                            <td class="text-right">
                                @if(hasPermission('orders.shipped-orders.complete'))
                                <button class="btn btn-xs btn-success complete-order-btn" data-id="{{ $order->id }}">
                                    <span class="iconify lucide--check-circle-2 size-3"></span>
                                    Complete
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-8">
                                <div class="flex flex-col items-center gap-2 text-base-content/60">
                                    <span class="iconify lucide--inbox size-12"></span>
                                    <p>No shipped orders</p>
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
@endsection

@section('customjs')
@vite(['resources/js/modules/orders/shipped-orders/index.js'])
@endsection
