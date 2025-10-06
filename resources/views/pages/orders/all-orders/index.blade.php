@extends('layouts.app')

@section('title', 'All Orders')
@section('page_title', 'Orders')
@section('page_subtitle', 'All Orders')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">All Orders</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Orders</li>
            <li class="opacity-80">All Orders</li>
        </ul>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Total Orders</p>
                    <p class="text-2xl font-semibold mt-1">{{ $statistics->total_orders ?? 0 }}</p>
                    <p class="text-xs text-base-content/60 mt-1">All time</p>
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
                    <p class="text-sm text-base-content/70">Pending</p>
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
                    <p class="text-sm text-base-content/70">Processing</p>
                    <p class="text-2xl font-semibold mt-1 text-info">{{ $statistics->processing_count ?? 0 }}</p>
                    <p class="text-xs text-base-content/60 mt-1">Being prepared</p>
                </div>
                <div class="bg-info/10 p-3 rounded-lg">
                    <span class="iconify lucide--package size-5 text-info"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Completed</p>
                    <p class="text-2xl font-semibold mt-1 text-success">{{ $statistics->completed_count ?? 0 }}</p>
                    <p class="text-xs text-base-content/60 mt-1">Successfully delivered</p>
                </div>
                <div class="bg-success/10 p-3 rounded-lg">
                    <span class="iconify lucide--check-circle size-5 text-success"></span>
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
                <div class="inline-flex items-center gap-3 flex-wrap">
                    <label class="input input-sm">
                        <span class="iconify lucide--search text-base-content/80 size-3.5"></span>
                        <input
                            class="w-24 sm:w-36"
                            placeholder="Search orders"
                            type="search"
                            id="searchInput" />
                    </label>

                    <select class="select select-sm select-bordered" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="inline-flex items-center gap-2">
                    <button class="btn btn-sm btn-ghost">
                        <span class="iconify lucide--filter size-4"></span>
                        Filter
                    </button>
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
                                    $statusColors = [
                                        'pending' => 'badge-warning',
                                        'processing' => 'badge-info',
                                        'shipped' => 'badge-primary',
                                        'completed' => 'badge-success',
                                        'cancelled' => 'badge-error'
                                    ];
                                    $colorClass = $statusColors[$order->status] ?? 'badge-ghost';
                                @endphp
                                <span class="badge badge-sm {{ $colorClass }}">{{ ucfirst($order->status) }}</span>
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
                                            <a onclick="viewOrder({{ $order->id }})">
                                                <span class="iconify lucide--eye size-4"></span>
                                                View Details
                                            </a>
                                        </li>
                                        @if(hasPermission('orders.all-orders.update'))
                                            @if($order->status === 'pending')
                                            <li>
                                                <a onclick="updateStatus({{ $order->id }}, 'processing')">
                                                    <span class="iconify lucide--check size-4"></span>
                                                    Confirm Order
                                                </a>
                                            </li>
                                            @endif
                                        @endif
                                        @if(hasPermission('orders.all-orders.delete'))
                                            @if(in_array($order->status, ['pending', 'cancelled']))
                                            <li>
                                                <a onclick="deleteOrder({{ $order->id }})" class="text-error">
                                                    <span class="iconify lucide--trash-2 size-4"></span>
                                                    Delete
                                                </a>
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
        </div>
    </div>
</div>

<!-- Order Detail Modal -->
<dialog id="orderDetailModal" class="modal">
    <div class="modal-box max-w-4xl">
        <h3 class="font-bold text-lg mb-4">Order Details</h3>
        <div id="orderDetailContent">
            <!-- Order details will be loaded here -->
        </div>
        <div class="modal-action">
            <form method="dialog">
                <button class="btn">Close</button>
            </form>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

@endsection

@push('scripts')
<script>
    // Toast Notification Helper
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} fixed top-4 right-4 z-50 max-w-md shadow-lg`;
        toast.innerHTML = `
            <span class="iconify ${type === 'success' ? 'lucide--check-circle' : type === 'error' ? 'lucide--x-circle' : 'lucide--info'} size-5"></span>
            <span>${message}</span>
        `;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Loading Helper
    function showLoading(button) {
        button.disabled = true;
        button.dataset.originalHtml = button.innerHTML;
        button.innerHTML = '<span class="loading loading-spinner loading-xs"></span> Loading...';
    }

    function hideLoading(button) {
        button.disabled = false;
        button.innerHTML = button.dataset.originalHtml;
    }

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#ordersTable tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    // Status filter
    document.getElementById('statusFilter').addEventListener('change', function() {
        const status = this.value.toLowerCase();
        const rows = document.querySelectorAll('#ordersTable tr');

        rows.forEach(row => {
            if (!status) {
                row.style.display = '';
                return;
            }
            const statusBadge = row.querySelector('.badge');
            const rowStatus = statusBadge ? statusBadge.textContent.toLowerCase() : '';
            row.style.display = rowStatus.includes(status) ? '' : 'none';
        });
    });

    // View order details
    async function viewOrder(id) {
        const modal = document.getElementById('orderDetailModal');
        const content = document.getElementById('orderDetailContent');

        content.innerHTML = '<div class="flex justify-center py-8"><span class="loading loading-spinner loading-lg"></span></div>';
        modal.showModal();

        try {
            const response = await fetch(`/orders/all-orders/${id}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                const order = data.data;
                content.innerHTML = `
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm opacity-60">Order Number</p>
                                <p class="font-semibold">${order.order_number}</p>
                            </div>
                            <div>
                                <p class="text-sm opacity-60">Status</p>
                                <p><span class="badge badge-${order.status === 'completed' ? 'success' : order.status === 'cancelled' ? 'error' : 'warning'}">${order.status}</span></p>
                            </div>
                            <div>
                                <p class="text-sm opacity-60">Customer</p>
                                <p class="font-semibold">${order.customer_name}</p>
                                <p class="text-xs opacity-60">${order.customer_email}</p>
                            </div>
                            <div>
                                <p class="text-sm opacity-60">Total Amount</p>
                                <p class="font-semibold text-lg">Rp ${Number(order.total_amount).toLocaleString('id-ID')}</p>
                            </div>
                        </div>

                        <div class="divider">Order Items</div>

                        <div class="overflow-x-auto">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>SKU</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${order.items.map(item => `
                                        <tr>
                                            <td>
                                                <div>${item.product_name}</div>
                                                <div class="text-xs opacity-60">${item.size || ''} ${item.color || ''}</div>
                                            </td>
                                            <td>${item.sku}</td>
                                            <td>${item.quantity}</td>
                                            <td>Rp ${Number(item.price).toLocaleString('id-ID')}</td>
                                            <td>Rp ${Number(item.total).toLocaleString('id-ID')}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
            } else {
                content.innerHTML = `<div class="alert alert-error">${data.message}</div>`;
            }
        } catch (error) {
            content.innerHTML = `<div class="alert alert-error">Failed to load order details</div>`;
        }
    }

    // Update order status
    async function updateStatus(id, status) {
        if (!confirm(`Are you sure you want to update this order status to ${status}?`)) {
            return;
        }

        try {
            const response = await fetch(`/orders/all-orders/${id}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status })
            });

            const data = await response.json();

            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message, 'error');
            }
        } catch (error) {
            showToast('Failed to update order status', 'error');
        }
    }

    // Delete order
    async function deleteOrder(id) {
        if (!confirm('Are you sure you want to delete this order? This action cannot be undone.')) {
            return;
        }

        try {
            const response = await fetch(`/orders/all-orders/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
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
            showToast('Failed to delete order', 'error');
        }
    }
</script>
@endpush
