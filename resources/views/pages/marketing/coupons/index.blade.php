@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <x-page-header title="Coupons" :breadcrumbs="[
        ['label' => 'Marketing', 'url' => '#'],
        ['label' => 'Coupons', 'url' => route('marketing.coupons.index')]
    ]" />

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
        <div class="stat-card">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-base-content/60">Total Coupons</p>
                            <p class="text-2xl" id="statTotalCoupons">...</p>
                        </div>
                        <span class="iconify lucide--package size-8"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-base-content/60">Active Coupons</p>
                            <p class="text-2xl" id="statActiveCoupons">...</p>
                        </div>
                        <span class="iconify lucide--check-circle-2 size-8"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-base-content/60">Total Usage</p>
                            <p class="text-2xl" id="statTotalUsage">...</p>
                        </div>
                        <span class="iconify lucide--package size-8"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-base-content/60">Total Discount</p>
                            <p class="text-2xl" id="statTotalDiscount">...</p>
                        </div>
                        <span class="iconify lucide--package size-8"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="mt-6">
        <x-filter-section title="Filter Coupons" action="#" id="filterForm">
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
                <button type="button" id="clearFilters" class="btn btn-sm btn-ghost">
                    <span class="iconify lucide--x size-4"></span>
                    Clear
                </button>
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
                    <tbody id="couponsTableBody">
                        <tr id="loadingRow">
                            <td colspan="8" class="text-center py-8">
                                <span class="loading loading-spinner loading-md"></span>
                                <p class="mt-2 text-base-content/60">Loading coupons...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="paginationContainer" class="p-4"></div>
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
