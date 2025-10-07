@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <x-page-header title="Flash Sales" :breadcrumbs="[
        ['label' => 'Marketing', 'url' => '#'],
        ['label' => 'Flash Sales', 'url' => route('marketing.flash-sales.index')]
    ]" />

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-4">
                <p class="text-sm text-base-content/60">Total Flash Sales</p>
                <p class="text-2xl font-bold" id="statTotalFlashSales">...</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-4">
                <p class="text-sm text-base-content/60">Active</p>
                <p class="text-2xl font-bold text-success" id="statActiveFlashSales">...</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-4">
                <p class="text-sm text-base-content/60">Upcoming</p>
                <p class="text-2xl font-bold text-info" id="statUpcomingFlashSales">...</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-4">
                <p class="text-sm text-base-content/60">Total Products</p>
                <p class="text-2xl font-bold text-warning" id="statTotalProducts">...</p>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="mt-6">
        <x-filter-section id="filterForm" title="Filter Flash Sales" :action="route('marketing.flash-sales.index')">
            <x-slot name="headerAction">
                @if(hasPermission('marketing.flash-sales.create'))
                <button type="button" class="btn btn-sm btn-primary" onclick="openCreateModal()">
                    <span class="iconify lucide--plus size-4"></span>
                    Add Flash Sale
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
                        :value="request('sort_by', 'priority')"
                        :options="[
                            'priority' => 'Priority',
                            'created_at' => 'Date Created',
                            'name' => 'Name',
                            'start_time' => 'Start Time',
                            'end_time' => 'End Time'
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

    <!-- Flash Sales Table -->
    <div class="mt-6">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Period</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="flashSalesTableBody">
                        <tr id="loadingRow">
                            <td colspan="5" class="text-center py-8">
                                <span class="loading loading-spinner loading-md"></span>
                                <p class="mt-2 text-base-content/60">Loading flash sales...</p>
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
<x-modal id="flashSaleModal" title="Create Flash Sale" size="lg">
    <form id="flashSaleForm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control md:col-span-2">
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
                    <span class="label-text">Start Time <span class="text-error">*</span></span>
                </label>
                <input type="datetime-local" name="start_time" class="input input-bordered" required>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">End Time <span class="text-error">*</span></span>
                </label>
                <input type="datetime-local" name="end_time" class="input input-bordered" required>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Priority</span>
                </label>
                <input type="number" name="priority" class="input input-bordered" min="0" value="0">
                <label class="label">
                    <span class="label-text-alt">Higher priority shows first</span>
                </label>
            </div>

            <div class="form-control md:col-span-2">
                <label class="label cursor-pointer justify-start gap-2">
                    <input type="checkbox" name="is_active" class="checkbox" checked>
                    <span class="label-text">Active</span>
                </label>
            </div>
        </div>

        <div class="modal-action">
            <button type="button" class="btn" onclick="flashSaleModal.close()">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</x-modal>

<!-- View Products Modal -->
<x-modal id="viewFlashSaleModal" title="Flash Sale Products" size="lg">
    <div id="flashSaleDetails"></div>
</x-modal>

<!-- Manage Products Modal -->
<x-modal id="manageProductsModal" title="Manage Products" size="lg">
    <div id="productsList"></div>
</x-modal>

@vite(['resources/js/modules/marketing/flash-sales/index.js'])
@endsection
