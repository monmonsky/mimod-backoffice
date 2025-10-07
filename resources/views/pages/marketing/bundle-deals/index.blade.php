@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <x-page-header title="Bundle Deals" :breadcrumbs="[
        ['label' => 'Marketing', 'url' => '#'],
        ['label' => 'Bundle Deals', 'url' => route('marketing.bundle-deals.index')]
    ]" />

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
        <x-stat-card title="Total Bundles" :value="$statistics->total_bundles" icon="package" icon-color="primary" />
        <x-stat-card title="Active Bundles" :value="$statistics->active_bundles" icon="check-circle-2" icon-color="success" value-color="text-success" />
        <x-stat-card title="Total Sold" :value="$statistics->total_sold" icon="package" icon-color="info" value-color="text-info" />
        <x-stat-card title="Total Revenue" :value="'Rp ' . number_format($statistics->total_revenue, 0, ',', '.')" icon="package" icon-color="warning" value-color="text-warning" />
    </div>

    <!-- Filter Section -->
    <div class="mt-6">
        <x-filter-section title="Filter Bundle Deals" :action="route('marketing.bundle-deals.index')">
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
                @if(request()->hasAny(['search', 'status', 'sort_by']))
                <a href="{{ route('marketing.bundle-deals.index') }}" class="btn btn-sm btn-ghost">
                    <span class="iconify lucide--x size-4"></span>
                    Clear
                </a>
                @endif
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
                    <tbody>
                        @forelse($bundles as $bundle)
                        <tr class="hover">
                            <td>
                                <div>
                                    <div class="font-medium">{{ $bundle->name }}</div>
                                    @if($bundle->description)
                                    <div class="text-sm text-base-content/60">{{ Str::limit($bundle->description, 40) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="text-sm">
                                    <div class="font-bold text-success">Rp {{ number_format($bundle->bundle_price, 0, ',', '.') }}</div>
                                    <div class="line-through text-base-content/60">Rp {{ number_format($bundle->original_price, 0, ',', '.') }}</div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $savings = $bundle->original_price - $bundle->bundle_price;
                                    $savingsPercent = $bundle->original_price > 0 ? ($savings / $bundle->original_price * 100) : 0;
                                @endphp
                                <div class="text-sm">
                                    <div class="text-success font-medium">{{ number_format($savingsPercent, 0) }}%</div>
                                    <div class="text-base-content/60">Rp {{ number_format($savings, 0, ',', '.') }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="text-sm">
                                    <div>{{ $bundle->sold_count }} sold</div>
                                    @if($bundle->stock_limit)
                                    <div class="text-base-content/60">of {{ $bundle->stock_limit }}</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="text-sm">
                                    <div>{{ \Carbon\Carbon::parse($bundle->start_date)->format('d M Y') }}</div>
                                    <div class="text-base-content/60">{{ \Carbon\Carbon::parse($bundle->end_date)->format('d M Y') }}</div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $now = now();
                                    $isActive = $bundle->is_active &&
                                               $now >= $bundle->start_date &&
                                               $now <= $bundle->end_date;
                                    $isUpcoming = $bundle->is_active && $now < $bundle->start_date;
                                    $isExpired = $now > $bundle->end_date;
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
                                    @if(hasPermission('marketing.bundle-deals.view'))
                                    <button class="btn btn-sm btn-ghost" onclick="viewBundle({{ $bundle->id }})" title="View Items">
                                        <span class="iconify lucide--eye size-4"></span>
                                    </button>
                                    @endif
                                    @if(hasPermission('marketing.bundle-deals.update'))
                                    <button class="btn btn-sm btn-ghost" onclick="editBundle({{ $bundle->id }})" title="Edit">
                                        <span class="iconify lucide--pencil size-4"></span>
                                    </button>
                                    <button class="btn btn-sm btn-ghost" onclick="manageItems({{ $bundle->id }})" title="Manage Items">
                                        <span class="iconify lucide--package size-4"></span>
                                    </button>
                                    @endif
                                    @if(hasPermission('marketing.bundle-deals.delete'))
                                    <button class="btn btn-sm btn-ghost text-error" onclick="deleteBundle({{ $bundle->id }})" title="Delete">
                                        <span class="iconify lucide--trash-2 size-4"></span>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-base-content/60">
                                No bundle deals found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination-info :paginator="$bundles" />
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
