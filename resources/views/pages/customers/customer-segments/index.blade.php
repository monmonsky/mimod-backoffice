@extends('layouts.app')

@section('title', 'Customer Segments')
@section('page_title', 'Customers')
@section('page_subtitle', 'Customer Segments')

@section('content')
<x-page-header
    title="Customer Segments"
    :breadcrumbs="[
        ['label' => 'Nexus', 'url' => route('dashboard')],
        ['label' => 'Customers'],
        ['label' => 'Customer Segments']
    ]"
/>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <x-stat-card
        title="Total Segments"
        :value="number_format($statistics->total_segments ?? 0)"
        subtitle="All segments"
        icon="folder-open"
        icon-color="primary"
    />

    <x-stat-card
        title="Active Segments"
        :value="number_format($statistics->active_segments ?? 0)"
        subtitle="Currently active"
        icon="folder-check"
        icon-color="success"
        value-color="text-success"
    />

    <x-stat-card
        title="Auto-Assign Segments"
        :value="number_format($statistics->auto_assign_segments ?? 0)"
        subtitle="Automatic assignment"
        icon="sparkles"
        icon-color="warning"
        value-color="text-warning"
    />

    <x-stat-card
        title="Total Customers"
        :value="number_format($statistics->total_customers_in_segments ?? 0)"
        subtitle="In all segments"
        icon="users"
        icon-color="info"
        value-color="text-info"
    />
</div>

<!-- Filter Section -->
<div class="mt-6">
    <x-filter-section title="Filter Segments" :action="route('customers.customer-segments.index')">
        <x-slot name="headerAction">
            @if(hasPermission('customers.customer-segments.create'))
            <button type="button" class="btn btn-sm btn-primary" id="addSegmentBtn">
                <span class="iconify lucide--plus size-4"></span>
                Add Segment
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
                    name="code"
                    label="Code"
                    :value="request('code')"
                    placeholder="Search by code"
                />

                <x-form.select
                    name="is_active"
                    label="Status"
                    :value="request('is_active')"
                    placeholder="All Status"
                    :options="['1' => 'Active', '0' => 'Inactive']"
                />

                <x-form.select
                    name="is_auto_assign"
                    label="Auto Assign"
                    :value="request('is_auto_assign')"
                    placeholder="All"
                    :options="['1' => 'Yes', '0' => 'No']"
                />
            </div>
        </x-slot>

        <x-slot name="actions">
            <button type="submit" class="btn btn-sm btn-primary">
                <span class="iconify lucide--search size-4"></span>
                Apply Filter
            </button>
            @if(request()->hasAny(['name', 'code', 'is_active', 'is_auto_assign']))
            <a href="{{ route('customers.customer-segments.index') }}" class="btn btn-sm btn-ghost">
                <span class="iconify lucide--x size-4"></span>
                Clear All
            </a>
            @endif
        </x-slot>
    </x-filter-section>
</div>

<!-- Segments Table -->
<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra" id="segmentsTable">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Criteria</th>
                            <th>Customers</th>
                            <th>Auto Assign</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($segments as $segment)
                        <tr>
                            <td>
                                <span class="font-mono text-sm">{{ $segment->code }}</span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    @if($segment->color)
                                    <span class="badge badge-sm" style="background-color: {{ $segment->color }}; color: white;">
                                        {{ $segment->name }}
                                    </span>
                                    @else
                                    <span class="font-medium">{{ $segment->name }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="text-xs space-y-1">
                                    @if($segment->min_orders || $segment->max_orders)
                                    <div>
                                        <span class="text-base-content/60">Orders:</span>
                                        {{ $segment->min_orders ?? 0 }} - {{ $segment->max_orders ?? '∞' }}
                                    </div>
                                    @endif
                                    @if($segment->min_spent || $segment->max_spent)
                                    <div>
                                        <span class="text-base-content/60">Spent:</span>
                                        Rp {{ number_format($segment->min_spent ?? 0) }} - Rp {{ number_format($segment->max_spent ?? 999999999) }}
                                    </div>
                                    @endif
                                    @if($segment->min_loyalty_points)
                                    <div>
                                        <span class="text-base-content/60">Points:</span>
                                        ≥ {{ number_format($segment->min_loyalty_points) }}
                                    </div>
                                    @endif
                                    @if($segment->days_since_last_order)
                                    <div>
                                        <span class="text-base-content/60">Inactive:</span>
                                        ≥ {{ $segment->days_since_last_order }} days
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="font-medium">{{ number_format($segment->customer_count) }}</span>
                            </td>
                            <td>
                                <x-badge
                                    :type="$segment->is_auto_assign ? 'success' : 'ghost'"
                                    :label="$segment->is_auto_assign ? 'Yes' : 'No'"
                                />
                            </td>
                            <td>
                                <x-badge
                                    :type="$segment->is_active ? 'success' : 'error'"
                                    :label="$segment->is_active ? 'Active' : 'Inactive'"
                                />
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    @if(hasPermission('customers.customer-segments.update'))
                                    <button type="button" class="btn btn-ghost btn-xs edit-segment-btn" data-id="{{ $segment->id }}">
                                        <span class="iconify lucide--pencil size-4"></span>
                                    </button>
                                    @endif

                                    @if(hasPermission('customers.customer-segments.delete'))
                                    <button type="button" class="btn btn-ghost btn-xs text-error delete-segment-btn" data-id="{{ $segment->id }}">
                                        <span class="iconify lucide--trash-2 size-4"></span>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-base-content/60">
                                <span class="iconify lucide--tags size-12 mx-auto block mb-2 opacity-20"></span>
                                No segments found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Info & Links -->
            <x-pagination-info :paginator="$segments" />
        </div>
    </div>
</div>

<!-- Add/Edit Segment Modal -->
<x-modal id="segmentModal" title="Add Segment">
    <form id="segmentForm">
        <input type="hidden" id="segment_id">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-form.input
                name="name"
                label="Segment Name"
                required
                placeholder="e.g., VIP Customers"
            />

            <x-form.input
                name="code"
                label="Code"
                required
                placeholder="e.g., VIP"
            />

            <x-form.input
                name="color"
                label="Color"
                type="color"
                placeholder="#000000"
            />

            <div class="md:col-span-2">
                <x-form.textarea
                    name="description"
                    label="Description"
                    :rows="2"
                    placeholder="Describe this segment"
                />
            </div>

            <div class="md:col-span-2">
                <h3 class="font-medium mb-3">Segment Criteria</h3>
            </div>

            <x-form.input
                name="min_orders"
                label="Min Orders"
                type="number"
                min="0"
                placeholder="0"
            />

            <x-form.input
                name="max_orders"
                label="Max Orders"
                type="number"
                min="0"
                placeholder="Unlimited"
            />

            <x-form.input
                name="min_spent"
                label="Min Spent (Rp)"
                type="number"
                min="0"
                step="0.01"
                placeholder="0"
            />

            <x-form.input
                name="max_spent"
                label="Max Spent (Rp)"
                type="number"
                min="0"
                step="0.01"
                placeholder="Unlimited"
            />

            <x-form.input
                name="min_loyalty_points"
                label="Min Loyalty Points"
                type="number"
                min="0"
                placeholder="0"
            />

            <x-form.input
                name="days_since_last_order"
                label="Days Since Last Order"
                type="number"
                min="0"
                placeholder="For inactive customers"
            />

            <div class="md:col-span-2">
                <h3 class="font-medium mb-3">Settings</h3>
            </div>

            <div class="form-control">
                <label class="label cursor-pointer justify-start gap-2">
                    <input type="checkbox" name="is_auto_assign" class="checkbox checkbox-primary" />
                    <span class="label-text">Auto Assign Customers</span>
                </label>
                <p class="text-xs text-base-content/60 mt-1">Automatically assign customers based on criteria</p>
            </div>

            <div class="form-control">
                <label class="label cursor-pointer justify-start gap-2">
                    <input type="checkbox" name="is_active" class="checkbox checkbox-primary" checked />
                    <span class="label-text">Active</span>
                </label>
                <p class="text-xs text-base-content/60 mt-1">Segment is active and visible</p>
            </div>
        </div>

        <div class="modal-action">
            <button type="button" class="btn btn-ghost" onclick="document.getElementById('segmentModal').close()">Cancel</button>
            <button type="submit" class="btn btn-primary">
                <span class="iconify lucide--save size-4"></span>
                Save Segment
            </button>
        </div>
    </form>
</x-modal>
@endsection

@vite(['resources/js/modules/customers/customer-segments/index.js'])
