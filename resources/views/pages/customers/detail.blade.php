@extends('layouts.app')

@section('title', 'Customer Detail')
@section('page_title', 'Customers')
@section('page_subtitle', 'Customer Detail')

@section('content')
<x-page-header
    title="Customer Detail"
    :breadcrumbs="[
        ['label' => 'Nexus', 'url' => route('dashboard')],
        ['label' => 'Customers', 'url' => route('customers.all-customers.index')],
        ['label' => $customer->name ?? 'Detail']
    ]"
/>

<!-- Customer Info & Statistics -->
<div class="grid grid-cols-1 gap-6 mt-6 lg:grid-cols-3">
    <!-- Customer Profile Card -->
    <div class="lg:col-span-1">
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="flex flex-col items-center gap-4">
                    <!-- Avatar -->
                    <div class="avatar placeholder">
                        <div class="bg-primary text-primary-content rounded-full w-24">
                            <span class="text-3xl">{{ strtoupper(substr($customer->name ?? 'C', 0, 2)) }}</span>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="text-center">
                        <h2 class="text-xl font-bold">{{ $customer->name }}</h2>
                        <p class="text-sm text-base-content/60">{{ $customer->customer_code }}</p>
                        @if($customer->is_vip)
                        <x-badge type="warning" label="VIP Customer" class="mt-2" />
                        @endif
                        @if($customer->segment)
                        <x-badge type="info" :label="ucfirst($customer->segment)" class="mt-2" />
                        @endif
                    </div>

                    <!-- Contact Info -->
                    <div class="w-full space-y-3 mt-4">
                        <div class="flex items-center gap-3">
                            <span class="iconify lucide--mail size-4 text-base-content/60"></span>
                            <span class="text-sm">{{ $customer->email }}</span>
                        </div>
                        @if($customer->phone)
                        <div class="flex items-center gap-3">
                            <span class="iconify lucide--phone size-4 text-base-content/60"></span>
                            <span class="text-sm">{{ $customer->phone }}</span>
                        </div>
                        @endif
                        @if($customer->date_of_birth)
                        <div class="flex items-center gap-3">
                            <span class="iconify lucide--calendar size-4 text-base-content/60"></span>
                            <span class="text-sm">{{ \Carbon\Carbon::parse($customer->date_of_birth)->format('d M Y') }}</span>
                        </div>
                        @endif
                        @if($customer->gender)
                        <div class="flex items-center gap-3">
                            <span class="iconify lucide--user size-4 text-base-content/60"></span>
                            <span class="text-sm">{{ ucfirst($customer->gender) }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Status Badge -->
                    <div class="w-full pt-4 border-t border-base-300">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-base-content/60">Status</span>
                            <x-badge :type="$customer->status === 'active' ? 'success' : 'error'" :label="ucfirst($customer->status)" />
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-sm text-base-content/60">Loyalty Points</span>
                            <span class="font-semibold">{{ number_format($customer->loyalty_points ?? 0) }} pts</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="lg:col-span-2">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-stat-card
                title="Total Orders"
                :value="$customer->total_orders ?? 0"
                subtitle="All time orders"
                icon="shopping-cart"
                icon-color="primary"
            />

            <x-stat-card
                title="Total Spent"
                :value="'Rp ' . number_format($customer->total_spent ?? 0, 0, ',', '.')"
                subtitle="Lifetime value"
                icon="badge-dollar-sign"
                icon-color="success"
            />

            <x-stat-card
                title="Average Order"
                :value="'Rp ' . number_format($customer->average_order_value ?? 0, 0, ',', '.')"
                subtitle="Per order value"
                icon="trending-up"
                icon-color="info"
            />

            <x-stat-card
                title="Last Order"
                :value="$customer->last_order_at ? \Carbon\Carbon::parse($customer->last_order_at)->diffForHumans() : 'Never'"
                subtitle="Latest purchase"
                icon="clock"
                icon-color="warning"
            />
        </div>
    </div>
</div>

<!-- Customer Addresses -->
<div class="mt-6">
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Shipping Addresses</h3>
                <button class="btn btn-primary btn-sm" id="addAddressBtn">
                    <span class="iconify lucide--plus size-4"></span>
                    Add Address
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($addresses as $address)
                <div class="border border-base-300 rounded-lg p-4 {{ $address->is_default ? 'border-primary' : '' }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="font-semibold">{{ $address->label }}</span>
                                @if($address->is_default)
                                <x-badge type="primary" label="Default" size="sm" />
                                @endif
                            </div>
                            <p class="text-sm font-medium">{{ $address->recipient_name }}</p>
                            <p class="text-sm text-base-content/60">{{ $address->phone }}</p>
                            <p class="text-sm text-base-content/60 mt-2">
                                {{ $address->address_line }}<br>
                                {{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}
                            </p>
                        </div>
                        <div class="dropdown dropdown-end">
                            <button tabindex="0" class="btn btn-ghost btn-xs">
                                <span class="iconify lucide--more-vertical size-4"></span>
                            </button>
                            <ul tabindex="0" class="dropdown-content menu p-2 shadow-lg bg-base-100 rounded-box w-52 z-10 border border-base-300">
                                <li><a class="edit-address-btn" data-id="{{ $address->id }}">
                                    <span class="iconify lucide--pencil size-4"></span>
                                    Edit
                                </a></li>
                                @if(!$address->is_default)
                                <li><a class="set-default-btn" data-id="{{ $address->id }}">
                                    <span class="iconify lucide--check size-4"></span>
                                    Set as Default
                                </a></li>
                                @endif
                                <li><a class="delete-address-btn text-error" data-id="{{ $address->id }}">
                                    <span class="iconify lucide--trash-2 size-4"></span>
                                    Delete
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-2 text-center py-8">
                    <span class="iconify lucide--map-pin size-12 text-base-content/40"></span>
                    <p class="text-base-content/60 mt-2">No addresses found</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Order History -->
<div class="mt-6">
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h3 class="text-lg font-semibold mb-4">Order History</h3>

            <div class="overflow-x-auto">
                <table class="table table-xs md:table-sm">
                    <thead>
                        <tr>
                            <th>Order Number</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>
                                <span class="font-medium">{{ $order->order_number }}</span>
                            </td>
                            <td>
                                <span class="text-sm">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</span>
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
                                <x-badge :type="$badgeType" :label="ucfirst($order->status)" size="sm" />
                            </td>
                            <td>
                                <a href="{{ route('orders.all-orders.show', $order->id) }}" class="btn btn-ghost btn-xs">
                                    <span class="iconify lucide--eye size-4"></span>
                                    View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-8">
                                <div class="flex flex-col items-center gap-2 text-base-content/60">
                                    <span class="iconify lucide--shopping-bag size-12"></span>
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

@endsection

@section('customjs')
@vite(['resources/js/modules/customers/detail/index.js'])
@endsection
