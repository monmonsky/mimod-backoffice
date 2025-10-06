@extends('layouts.app')

@section('title', 'Pending Orders')
@section('page_title', 'Orders')
@section('page_subtitle', 'Pending Orders')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Pending Orders</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Orders</li>
            <li class="opacity-80">Pending Orders</li>
        </ul>
    </div>
</div>

<!-- Statistics Card -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-3">
    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Pending Orders</p>
                    <p class="text-2xl font-semibold mt-1 text-warning">{{ $statistics->pending_count ?? 0 }}</p>
                    <p class="text-xs text-base-content/60 mt-1">Awaiting confirmation</p>
                </div>
                <div class="bg-warning/10 p-3 rounded-lg">
                    <span class="iconify lucide--clock size-5 text-warning"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Total Value</p>
                    <p class="text-2xl font-semibold mt-1">Rp {{ number_format($orders->sum('total_amount') ?? 0, 0, ',', '.') }}</p>
                    <p class="text-xs text-base-content/60 mt-1">Pending orders value</p>
                </div>
                <div class="bg-primary/10 p-3 rounded-lg">
                    <span class="iconify lucide--dollar-sign size-5 text-primary"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Action Required</p>
                    <p class="text-2xl font-semibold mt-1 text-error">{{ $orders->count() }}</p>
                    <p class="text-xs text-base-content/60 mt-1">Need confirmation</p>
                </div>
                <div class="bg-error/10 p-3 rounded-lg">
                    <span class="iconify lucide--alert-circle size-5 text-error"></span>
                </div>
            </div>
        </div>
    </div>
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
                        placeholder="Search orders"
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
                            <th>Date</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTable">
                        @forelse($orders as $order)
                        <tr>
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
                                <div class="flex flex-col">
                                    <span class="text-xs">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</span>
                                    <span class="text-xs opacity-60">{{ \Carbon\Carbon::parse($order->created_at)->diffForHumans() }}</span>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="inline-flex gap-1">
                                    @if(hasPermission('orders.pending-orders.confirm'))
                                    <button onclick="confirmOrder({{ $order->id }})" class="btn btn-xs btn-success" title="Confirm Order">
                                        <span class="iconify lucide--check size-3"></span>
                                        Confirm
                                    </button>
                                    @endif
                                    @if(hasPermission('orders.pending-orders.cancel'))
                                    <button onclick="cancelOrder({{ $order->id }})" class="btn btn-xs btn-error" title="Cancel Order">
                                        <span class="iconify lucide--x size-3"></span>
                                        Cancel
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-8">
                                <div class="flex flex-col items-center gap-2 text-base-content/60">
                                    <span class="iconify lucide--inbox size-12"></span>
                                    <p>No pending orders</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toast Helper
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} fixed top-4 right-4 z-50 max-w-md shadow-lg`;
        toast.innerHTML = `
            <span class="iconify ${type === 'success' ? 'lucide--check-circle' : 'lucide--x-circle'} size-5"></span>
            <span>${message}</span>
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Search
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#ordersTable tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    // Confirm Order
    async function confirmOrder(id) {
        if (!confirm('Confirm this order and move to processing?')) return;

        try {
            const response = await fetch(`/orders/pending-orders/${id}/confirm`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message, 'error');
            }
        } catch (error) {
            showToast('Failed to confirm order', 'error');
        }
    }

    // Cancel Order
    async function cancelOrder(id) {
        const reason = prompt('Please provide a cancellation reason:');
        if (!reason) return;

        try {
            const response = await fetch(`/orders/pending-orders/${id}/cancel`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ cancellation_reason: reason })
            });

            const data = await response.json();

            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message, 'error');
            }
        } catch (error) {
            showToast('Failed to cancel order', 'error');
        }
    }
</script>
@endpush
