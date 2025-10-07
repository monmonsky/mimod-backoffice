@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <x-page-header title="Coupons" :breadcrumbs="[
        ['label' => 'Marketing', 'url' => '#'],
        ['label' => 'Coupons', 'url' => route('marketing.coupons.index')]
    ]" />

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
        <x-stat-card title="Total Coupons" :value="$statistics->total_coupons" icon="package" icon-color="primary" />
        <x-stat-card title="Active Coupons" :value="$statistics->active_coupons" icon="check-circle-2" icon-color="success" value-color="text-success" />
        <x-stat-card title="Total Usage" :value="$statistics->total_usage" icon="package" icon-color="info" value-color="text-info" />
        <x-stat-card title="Total Discount" :value="'Rp ' . number_format($statistics->total_discount, 0, ',', '.')" icon="package" icon-color="warning" value-color="text-warning" />
    </div>

    <!-- Filter Section -->
    <div class="mt-6">
        <x-filter-section title="Filter Coupons" :action="route('marketing.coupons.index')">
            <x-slot name="headerAction">
                @if(hasPermission('marketing.coupons.create'))
                <button type="button" class="btn btn-sm btn-primary" onclick="openCreateModal()">
                    <span class="iconify lucide--plus size-4"></span>
                    Add Coupon
                </button>
                @endif
            </x-slot>

            <x-slot name="filters">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <x-form.input
                        name="search"
                        label="Search"
                        :value="request('search')"
                        placeholder="Search by code or name"
                    />

                    <x-form.select
                        name="type"
                        label="Type"
                        :value="request('type')"
                        placeholder="All Types"
                        :options="[
                            'percentage' => 'Percentage',
                            'fixed' => 'Fixed Amount',
                            'free_shipping' => 'Free Shipping'
                        ]"
                    />

                    <x-form.select
                        name="status"
                        label="Status"
                        :value="request('status')"
                        placeholder="All Status"
                        :options="[
                            'active' => 'Active',
                            'upcoming' => 'Upcoming',
                            'expired' => 'Expired',
                            'inactive' => 'Inactive'
                        ]"
                    />

                    <x-form.select
                        name="sort_by"
                        label="Sort By"
                        :value="request('sort_by', 'created_at')"
                        :options="[
                            'created_at' => 'Date Created',
                            'code' => 'Code',
                            'name' => 'Name',
                            'usage_count' => 'Usage Count',
                            'start_date' => 'Start Date'
                        ]"
                    />
                </div>
            </x-slot>

            <x-slot name="actions">
                <button type="submit" class="btn btn-sm btn-primary">
                    <span class="iconify lucide--search size-4"></span>
                    Apply Filter
                </button>
                @if(request()->hasAny(['search', 'type', 'status', 'sort_by']))
                <a href="{{ route('marketing.coupons.index') }}" class="btn btn-sm btn-ghost">
                    <span class="iconify lucide--x size-4"></span>
                    Clear
                </a>
                @endif
            </x-slot>
        </x-filter-section>
    </div>

    <!-- Coupons Table -->
    <div class="mt-6">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Discount</th>
                            <th>Usage</th>
                            <th>Valid Period</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coupons as $coupon)
                        <tr class="hover">
                            <td>
                                <code class="font-bold text-primary">{{ $coupon->code }}</code>
                            </td>
                            <td>{{ $coupon->name }}</td>
                            <td>
                                @if($coupon->type === 'percentage')
                                <x-badge type="info" label="Percentage" />
                                @elseif($coupon->type === 'fixed')
                                <x-badge type="success" label="Fixed Amount" />
                                @else
                                <x-badge type="warning" label="Free Shipping" />
                                @endif
                            </td>
                            <td>
                                @if($coupon->type === 'percentage')
                                {{ $coupon->value }}%
                                @elseif($coupon->type === 'fixed')
                                Rp {{ number_format($coupon->value, 0, ',', '.') }}
                                @else
                                Free
                                @endif
                            </td>
                            <td>
                                <span class="text-sm">{{ $coupon->usage_count }} / {{ $coupon->usage_limit ?? '∞' }}</span>
                            </td>
                            <td>
                                <div class="text-sm">
                                    <div>{{ \Carbon\Carbon::parse($coupon->start_date)->format('d M Y') }}</div>
                                    <div class="text-base-content/60">{{ \Carbon\Carbon::parse($coupon->end_date)->format('d M Y') }}</div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $now = now();
                                    $isActive = $coupon->is_active &&
                                               $now >= $coupon->start_date &&
                                               $now <= $coupon->end_date;
                                    $isUpcoming = $coupon->is_active && $now < $coupon->start_date;
                                    $isExpired = $now > $coupon->end_date;
                                @endphp

                                @if($isActive)
                                <x-badge type="success" label="Active" />
                                @elseif($isUpcoming)
                                <x-badge type="info" label="Upcoming" />
                                @elseif($isExpired)
                                <x-badge type="error" label="Expired" />
                                @else
                                <x-badge type="ghost" label="Inactive" />
                                @endif
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    @if(hasPermission('marketing.coupons.view'))
                                    <button class="btn btn-sm btn-ghost" onclick="viewCoupon({{ $coupon->id }})" title="View">
                                        <span class="iconify lucide--eye size-4"></span>
                                    </button>
                                    @endif
                                    @if(hasPermission('marketing.coupons.update'))
                                    <button class="btn btn-sm btn-ghost" onclick="editCoupon({{ $coupon->id }})" title="Edit">
                                        <span class="iconify lucide--pencil size-4"></span>
                                    </button>
                                    @endif
                                    @if(hasPermission('marketing.coupons.delete'))
                                    <button class="btn btn-sm btn-ghost text-error" onclick="deleteCoupon({{ $coupon->id }})" title="Delete">
                                        <span class="iconify lucide--trash-2 size-4"></span>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-base-content/60">
                                No coupons found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination-info :paginator="$coupons" />
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<x-modal id="couponModal" title="Create Coupon" size="lg">
    <form id="couponForm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Code <span class="text-error">*</span></span>
                </label>
                <input type="text" name="code" class="input input-bordered" required>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Name <span class="text-error">*</span></span>
                </label>
                <input type="text" name="name" class="input input-bordered" required>
            </div>

            <div class="form-control md:col-span-2">
                <label class="label">
                    <span class="label-text">Description</span>
                </label>
                <textarea name="description" class="textarea textarea-bordered" rows="3"></textarea>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Type <span class="text-error">*</span></span>
                </label>
                <select name="type" class="select select-bordered" required>
                    <option value="percentage">Percentage</option>
                    <option value="fixed">Fixed Amount</option>
                    <option value="free_shipping">Free Shipping</option>
                </select>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Value <span class="text-error">*</span></span>
                </label>
                <input type="number" name="value" class="input input-bordered" step="0.01" min="0" required>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Minimum Purchase</span>
                </label>
                <input type="number" name="min_purchase" class="input input-bordered" step="0.01" min="0">
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Maximum Discount</span>
                </label>
                <input type="number" name="max_discount" class="input input-bordered" step="0.01" min="0">
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Total Usage Limit</span>
                </label>
                <input type="number" name="usage_limit" class="input input-bordered" min="1">
                <label class="label">
                    <span class="label-text-alt">Leave empty for unlimited</span>
                </label>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Usage Limit Per Customer <span class="text-error">*</span></span>
                </label>
                <input type="number" name="usage_limit_per_customer" class="input input-bordered" min="1" value="1" required>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Start Date <span class="text-error">*</span></span>
                </label>
                <input type="datetime-local" name="start_date" class="input input-bordered" required>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">End Date <span class="text-error">*</span></span>
                </label>
                <input type="datetime-local" name="end_date" class="input input-bordered" required>
            </div>

            <div class="form-control md:col-span-2">
                <label class="label cursor-pointer justify-start gap-2">
                    <input type="checkbox" name="is_active" class="checkbox" checked>
                    <span class="label-text">Active</span>
                </label>
            </div>
        </div>

        <div class="modal-action">
            <button type="button" class="btn" onclick="couponModal.close()">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</x-modal>

<!-- View Modal -->
<x-modal id="viewCouponModal" title="Coupon Details" size="lg">
    <div id="couponDetails"></div>
</x-modal>

@vite(['resources/js/modules/marketing/coupons/index.js'])
@endsection
