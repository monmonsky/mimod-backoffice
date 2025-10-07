@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <x-page-header title="Bundle Deals" :breadcrumbs="[
        ['label' => 'Marketing', 'url' => '#'],
        ['label' => 'Bundle Deals', 'url' => route('marketing.bundle-deals.index')]
    ]" />

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-4">
                <p class="text-sm text-base-content/60">Total Bundles</p>
                <p class="text-2xl" id="statTotalBundles">...</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-4">
                <p class="text-sm text-base-content/60">Active Bundles</p>
                <p class="text-2xl" id="statActiveBundles">...</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-4">
                <p class="text-sm text-base-content/60">Total Sold</p>
                <p class="text-2xl" id="statTotalSold">...</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-4">
                <p class="text-sm text-base-content/60">Total Revenue</p>
                <p class="text-2xl" id="statTotalRevenue">...</p>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="mt-6">
        <x-filter-section id="filterForm" title="Filter Bundle Deals" :action="route('marketing.bundle-deals.index')">
            <x-slot name="headerAction">
                @if(hasPermission('marketing.bundle-deals.create'))
                <button type="button" class="btn btn-sm btn-primary" onclick="openCreateModal()">
                    <span class="iconify lucide--plus size-4"></span>
                    Add Bundle Deal
                </button>
                @endif
            </x-slot>

            <x-slot name="filters">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <x-form.input
                        name="search"
                        label="Search"
                        :value="request('search')"
                        placeholder="Search by name"
                    />

                    <x-form.select
                        name="status"
                        label="Status"
                        :value="request('status')"
                        placeholder="All Status"
                        :options="[
                            'active' => 'Active',
                            'upcoming' => 'Upcoming',
                            'expired' => 'Expired'
                        ]"
                    />

                    <x-form.select
                        name="sort_by"
                        label="Sort By"
                        :value="request('sort_by', 'created_at')"
                        :options="[
                            'created_at' => 'Date Created',
                            'name' => 'Name',
                            'sold_count' => 'Total Sold',
                            'start_date' => 'Start Date',
                            'bundle_price' => 'Price'
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

    <!-- Bundle Deals Table -->
    <div class="mt-6">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Pricing</th>
                            <th>Savings</th>
                            <th>Sales</th>
                            <th>Valid Period</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="bundleDealsTableBody">
                        <tr id="loadingRow">
                            <td colspan="7" class="text-center py-8">
                                <span class="loading loading-spinner loading-md"></span>
                                <p class="mt-2 text-base-content/60">Loading bundle deals...</p>
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
<x-modal id="bundleModal" title="Create Bundle Deal" size="lg">
    <form id="bundleForm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control md:col-span-2">
                <label class="label">
                    <span class="label-text">Name <span class="text-error">*</span></span>
                </label>
                <input type="text" name="name" class="input input-bordered" required>
            </div>

            <div class="form-control md:col-span-2">
                <label class="label">
                    <span class="label-text">Slug</span>
                </label>
                <input type="text" name="slug" class="input input-bordered">
                <label class="label">
                    <span class="label-text-alt">Leave empty to auto-generate from name</span>
                </label>
            </div>

            <div class="form-control md:col-span-2">
                <label class="label">
                    <span class="label-text">Description</span>
                </label>
                <textarea name="description" class="textarea textarea-bordered" rows="3"></textarea>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Bundle Price <span class="text-error">*</span></span>
                </label>
                <input type="number" name="bundle_price" class="input input-bordered" step="0.01" min="0" required>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Original Price <span class="text-error">*</span></span>
                </label>
                <input type="number" name="original_price" class="input input-bordered" step="0.01" min="0" required>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Stock Limit</span>
                </label>
                <input type="number" name="stock_limit" class="input input-bordered" min="1">
                <label class="label">
                    <span class="label-text-alt">Leave empty for unlimited</span>
                </label>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Image URL</span>
                </label>
                <input type="text" name="image" class="input input-bordered">
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
            <button type="button" class="btn" onclick="bundleModal.close()">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</x-modal>

<!-- View Items Modal -->
<x-modal id="viewBundleModal" title="Bundle Items" size="lg">
    <div id="bundleDetails"></div>
</x-modal>

<!-- Manage Items Modal -->
<x-modal id="manageItemsModal" title="Manage Bundle Items" size="lg">
    <div id="itemsList"></div>
</x-modal>

@vite(['resources/js/modules/marketing/bundle-deals/index.js'])
@endsection
