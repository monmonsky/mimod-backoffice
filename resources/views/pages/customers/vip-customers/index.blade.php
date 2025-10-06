@extends('layouts.app')

@section('title', 'VIP Customers')
@section('page_title', 'Customers')
@section('page_subtitle', 'VIP Customers')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">VIP Customers</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Customers</li>
            <li class="opacity-80">VIP Customers</li>
        </ul>
    </div>
</div>

<!-- Statistics Card -->
<div class="mt-6">
    <div class="card bg-gradient-to-r from-warning/10 to-warning/5 shadow-lg border border-warning/20">
        <div class="card-body">
            <div class="flex items-center gap-4">
                <div class="bg-warning/20 p-4 rounded-full">
                    <span class="iconify lucide--crown size-8 text-warning"></span>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold">{{ number_format($statistics->vip_customers ?? 0) }}</h3>
                    <p class="text-base-content/70">Total VIP Customers</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-base-content/70">Total Revenue from VIP</p>
                    <p class="text-xl font-semibold text-warning">Rp {{ number_format($customers->sum('total_spent'), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- VIP Customers Table -->
<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body p-0">
            <div class="flex flex-col gap-4 px-5 pt-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="inline-flex items-center gap-3">
                    <label class="input input-sm">
                        <span class="iconify lucide--search text-base-content/80 size-3.5"></span>
                        <input
                            class="w-24 sm:w-36"
                            placeholder="Search VIP customers"
                            type="search"
                            id="searchInput" />
                    </label>
                </div>

                <div class="text-sm text-base-content/70">
                    Showing {{ $customers->count() }} VIP customers
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="table table-zebra" id="vipTable">
                    <thead>
                        <tr>
                            <th>Customer Code</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Total Orders</th>
                            <th>Total Spent</th>
                            <th>Loyalty Points</th>
                            <th>Last Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr>
                            <td>
                                <span class="font-mono text-sm">{{ $customer->customer_code }}</span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--crown size-4 text-warning"></span>
                                    <span class="font-medium">{{ $customer->name }}</span>
                                </div>
                            </td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone ?? '-' }}</td>
                            <td>
                                <span class="font-semibold">{{ number_format($customer->total_orders) }}</span>
                            </td>
                            <td>
                                <span class="font-semibold text-success">Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <span class="badge badge-warning">{{ number_format($customer->loyalty_points) }} pts</span>
                            </td>
                            <td>
                                @if($customer->last_order_at)
                                <span class="text-sm">{{ \Carbon\Carbon::parse($customer->last_order_at)->diffForHumans() }}</span>
                                @else
                                <span class="text-sm text-base-content/50">Never</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex gap-1">
                                    <a href="{{ route('customers.all-customers.index') }}" class="btn btn-xs btn-ghost" title="View Details">
                                        <span class="iconify lucide--eye size-4"></span>
                                    </a>

                                    @if(hasPermission('customers.vip-customers.manage'))
                                    <button class="btn btn-xs btn-ghost text-warning" onclick="removeVip({{ $customer->id }})" title="Remove VIP">
                                        <span class="iconify lucide--crown-off size-4"></span>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-base-content/60">
                                <span class="iconify lucide--crown size-12 mx-auto block mb-2 opacity-20"></span>
                                No VIP customers found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const table = document.getElementById('vipTable');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    }
});

// Remove VIP status
async function removeVip(id) {
    if (!confirm('Are you sure you want to remove VIP status from this customer?')) return;

    try {
        const response = await fetch(`/customers/vip-customers/${id}/toggle-vip`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('Failed to update VIP status');
    }
}
</script>
@endsection
