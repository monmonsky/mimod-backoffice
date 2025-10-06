@extends('layouts.app')

@section('title', 'Shipped Orders')
@section('page_title', 'Orders')
@section('page_subtitle', 'Shipped Orders')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Shipped Orders</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Orders</li>
            <li class="opacity-80">Shipped Orders</li>
        </ul>
    </div>
</div>

<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2">
    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Shipped Orders</p>
                    <p class="text-2xl font-semibold mt-1 text-primary">{{ $statistics->shipped_count ?? 0 }}</p>
                    <p class="text-xs text-base-content/60 mt-1">On delivery</p>
                </div>
                <div class="bg-primary/10 p-3 rounded-lg">
                    <span class="iconify lucide--truck size-5 text-primary"></span>
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
                    <p class="text-xs text-base-content/60 mt-1">Shipped orders value</p>
                </div>
                <div class="bg-success/10 p-3 rounded-lg">
                    <span class="iconify lucide--dollar-sign size-5 text-success"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body p-0">
            <div class="flex flex-col gap-4 px-5 pt-5 sm:flex-row sm:items-center sm:justify-between">
                <label class="input input-sm">
                    <span class="iconify lucide--search text-base-content/80 size-3.5"></span>
                    <input class="w-24 sm:w-36" placeholder="Search orders" type="search" id="searchInput" />
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
                                <button onclick="completeOrder({{ $order->id }})" class="btn btn-xs btn-success">
                                    <span class="iconify lucide--check-circle size-3"></span>
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
        document.querySelectorAll('#ordersTable tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(searchTerm) ? '' : 'none';
        });
    });

    // Complete Order
    async function completeOrder(id) {
        if (!confirm('Mark this order as completed?')) return;

        try {
            const response = await fetch(`/orders/shipped-orders/${id}/complete`, {
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
            showToast('Failed to complete order', 'error');
        }
    }
</script>
@endpush
