@extends('layouts.app')

@section('title', 'All Customers')
@section('page_title', 'Customers')
@section('page_subtitle', 'All Customers')

@section('content')
<x-page-header
    title="All Customers"
    :breadcrumbs="[
        ['label' => 'Nexus', 'url' => route('dashboard')],
        ['label' => 'Customers'],
        ['label' => 'All Customers']
    ]"
/>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <x-stat-card
        title="Total Customers"
        :value="number_format($statistics->total_customers ?? 0)"
        subtitle="All time"
        icon="users"
        icon-color="primary"
    />

    <x-stat-card
        title="Active Customers"
        :value="number_format($statistics->active_customers ?? 0)"
        subtitle="Currently active"
        icon="user-round-check"
        icon-color="success"
        value-color="text-success"
    />

    <x-stat-card
        title="VIP Customers"
        :value="number_format($statistics->vip_customers ?? 0)"
        subtitle="Premium members"
        icon="award"
        icon-color="warning"
        value-color="text-warning"
    />

    <x-stat-card
        title="Total Revenue"
        :value="'Rp ' . number_format($statistics->total_revenue ?? 0, 0, ',', '.')"
        subtitle="All time"
        icon="line-chart"
        icon-color="info"
        value-color="text-info"
    />
</div>

<!-- Filter Section -->
<div class="mt-6">
    <x-filter-section title="Filter Customers" :action="route('customers.all-customers.index')">
        <x-slot name="headerAction">
            @if(hasPermission('customers.all-customers.create'))
            <button type="button" class="btn btn-sm btn-primary" onclick="openCreateModal()">
                <span class="iconify lucide--plus size-4"></span>
                Add Customer
            </button>
            @endif
        </x-slot>

        <x-slot name="filters">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-form.input
                    name="name"
                    label="Name"
                    :value="request('name')"
                    placeholder="Search by name"
                />

                <x-form.input
                    name="email"
                    label="Email"
                    :value="request('email')"
                    placeholder="Search by email"
                />

                <x-form.input
                    name="phone"
                    label="Phone"
                    :value="request('phone')"
                    placeholder="Search by phone"
                />

                <x-form.select
                    name="segment"
                    label="Segment"
                    :value="request('segment')"
                    placeholder="All Segments"
                    :options="['regular' => 'Regular', 'premium' => 'Premium', 'vip' => 'VIP']"
                />

                <x-form.select
                    name="status"
                    label="Status"
                    :value="request('status')"
                    placeholder="All Status"
                    :options="['active' => 'Active', 'inactive' => 'Inactive', 'blocked' => 'Blocked']"
                />

                <x-form.select
                    name="sort_by"
                    label="Sort By"
                    :value="request('sort_by', 'created_at')"
                    :options="[
                        'created_at' => 'Date Created',
                        'name' => 'Name',
                        'email' => 'Email',
                        'total_orders' => 'Total Orders',
                        'total_spent' => 'Total Spent'
                    ]"
                />

                <x-form.select
                    name="sort_order"
                    label="Order"
                    :value="request('sort_order', 'desc')"
                    :options="['desc' => 'Descending', 'asc' => 'Ascending']"
                />
            </div>
        </x-slot>

        <x-slot name="actions">
            <button type="submit" class="btn btn-sm btn-primary">
                <span class="iconify lucide--search size-4"></span>
                Apply Filter
            </button>
            @if(request()->hasAny(['name', 'email', 'phone', 'segment', 'status', 'sort_by', 'sort_order']))
            <a href="{{ route('customers.all-customers.index') }}" class="btn btn-sm btn-ghost">
                <span class="iconify lucide--x size-4"></span>
                Clear All
            </a>
            @endif
        </x-slot>
    </x-filter-section>
</div>

<!-- Customers Table -->
<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body p-0">

            <div class="overflow-x-auto">
                <table class="table table-zebra" id="customersTable">
                    <thead>
                        <tr>
                            <th>Customer Code</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Segment</th>
                            <th>Total Orders</th>
                            <th>Total Spent</th>
                            <th>Status</th>
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
                                    @if($customer->is_vip)
                                    <span class="iconify lucide--award size-4 text-warning" title="VIP Customer"></span>
                                    @endif
                                    <span class="font-medium">{{ $customer->name }}</span>
                                </div>
                            </td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone ?? '-' }}</td>
                            <td>
                                <x-badge
                                    :type="$customer->segment === 'vip' ? 'warning' : ($customer->segment === 'premium' ? 'info' : 'ghost')"
                                    :label="ucfirst($customer->segment)"
                                />
                            </td>
                            <td>{{ number_format($customer->total_orders) }}</td>
                            <td>Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</td>
                            <td>
                                <x-badge
                                    :type="$customer->status === 'active' ? 'success' : ($customer->status === 'inactive' ? 'ghost' : 'error')"
                                    :label="ucfirst($customer->status)"
                                />
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    @if(hasPermission('customers.all-customers.view'))
                                    <a href="{{ route('customers.all-customers.detail', $customer->id) }}" class="btn btn-ghost btn-xs">
                                        <span class="iconify lucide--eye size-4"></span>
                                    </a>
                                    @endif

                                    @if(hasPermission('customers.all-customers.update'))
                                    <button type="button" class="btn btn-ghost btn-xs" onclick="editCustomer({{ $customer->id }})">
                                        <span class="iconify lucide--pencil size-4"></span>
                                    </button>
                                    @endif

                                    @if(hasPermission('customers.all-customers.delete'))
                                    <button type="button" class="btn btn-ghost btn-xs text-error" onclick="deleteCustomer({{ $customer->id }})">
                                        <span class="iconify lucide--trash-2 size-4"></span>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-base-content/60">
                                <span class="iconify lucide--users size-12 mx-auto block mb-2 opacity-20"></span>
                                No customers found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Info & Links -->
            <x-pagination-info :paginator="$customers" />
        </div>
    </div>
</div>

<!-- Edit Customer Modal -->
<x-modal id="editCustomerModal" title="Edit Customer">
    <form id="editCustomerForm" onsubmit="submitEditCustomer(event)">
        <input type="hidden" id="edit_customer_id">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-form.input
                name="edit_name"
                label="Name"
                required
            />

            <x-form.input
                type="email"
                name="edit_email"
                label="Email"
                required
            />

            <x-form.input
                name="edit_phone"
                label="Phone"
            />

            <x-form.select
                name="edit_segment"
                label="Segment"
                :options="['regular' => 'Regular', 'premium' => 'Premium', 'vip' => 'VIP']"
            />

            <x-form.select
                name="edit_status"
                label="Status"
                :options="['active' => 'Active', 'inactive' => 'Inactive', 'blocked' => 'Blocked']"
            />

            <div class="md:col-span-2">
                <x-form.textarea
                    name="edit_notes"
                    label="Notes"
                    :rows="3"
                />
            </div>
        </div>

        <div class="modal-action">
            <button type="button" class="btn btn-ghost" onclick="document.getElementById('editCustomerModal').close()">Cancel</button>
            <button type="submit" class="btn btn-primary">
                <span class="iconify lucide--save size-4"></span>
                Save Changes
            </button>
        </div>
    </form>
</x-modal>
@endsection

@vite(['resources/js/modules/customers/all-customers/index.js'])
