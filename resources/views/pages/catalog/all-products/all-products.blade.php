@extends('layouts.app')

@section('title', 'All Products')
@section('page_title', 'Catalog')
@section('page_subtitle', 'All Products')

@section('content')
<x-page-header
    title="All Products"
    :breadcrumbs="[
        ['label' => 'Nexus', 'url' => route('dashboard')],
        ['label' => 'Catalog'],
        ['label' => 'All Products']
    ]"
/>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Total Products</p>
                    <p class="text-2xl" id="statTotalProducts">...</p>
                    <p class="text-xs text-base-content/60">All products</p>
                </div>
                <span class="iconify lucide--package size-8 text-base-content/20"></span>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-sm">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Active</p>
                    <p class="text-2xl" id="statActiveProducts">...</p>
                    <p class="text-xs text-base-content/60">Published products</p>
                </div>
                <span class="iconify lucide--check-circle-2 size-8"></span>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-sm">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Inactive</p>
                    <p class="text-2xl" id="statInactiveProducts">...</p>
                    <p class="text-xs text-base-content/60">Draft products</p>
                </div>
                <span class="iconify lucide--archive size-8"></span>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-sm">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Out of Stock</p>
                    <p class="text-2xl" id="statOutOfStockProducts">...</p>
                    <p class="text-xs text-base-content/60">Need restock</p>
                </div>
                <span class="iconify lucide--x-circle size-8"></span>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="mt-6">
    <x-filter-section
        id="filterForm"
        title="Filter Products"
        :action="route('catalog.products.all-products')"
        method="GET"
    >
        <x-slot name="filters">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-form.input
                    name="search"
                    label="Search"
                    placeholder="Search by name or SKU"
                    :value="request('search')"
                />

                <x-form.select
                    name="status"
                    label="Status"
                    :options="[
                        '' => 'All Status',
                        'active' => 'Active',
                        'inactive' => 'Inactive'
                    ]"
                    :value="request('status')"
                />

                <x-form.select
                    name="brand_id"
                    label="Brand"
                    :options="['' => 'All Brands']"
                    :value="request('brand_id')"
                    id="brandFilter"
                />

                <x-form.select
                    name="category_id"
                    label="Category"
                    :options="['' => 'All Categories']"
                    :value="request('category_id')"
                    id="categoryFilter"
                />

                <x-form.select
                    name="stock_status"
                    label="Stock Status"
                    :options="[
                        '' => 'All Stock',
                        'in_stock' => 'In Stock',
                        'low_stock' => 'Low Stock (≤10)',
                        'out_of_stock' => 'Out of Stock'
                    ]"
                    :value="request('stock_status')"
                />

                <x-form.input
                    name="min_price"
                    label="Min Price"
                    type="number"
                    placeholder="0"
                    :value="request('min_price')"
                />

                <x-form.input
                    name="max_price"
                    label="Max Price"
                    type="number"
                    placeholder="0"
                    :value="request('max_price')"
                />
            </div>
        </x-slot>

        <x-slot name="actions">
            <button type="submit" class="btn btn-primary btn-sm">
                <span class="iconify lucide--search size-4"></span>
                Apply Filter
            </button>
            <button type="button" id="clearFilters" class="btn btn-ghost btn-sm">
                <span class="iconify lucide--x size-4"></span>
                Reset
            </button>
        </x-slot>
    </x-filter-section>
</div>

<!-- Products Table -->
<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body p-0">
            <div class="flex flex-col gap-4 px-5 pt-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="inline-flex items-center gap-3 flex-wrap">
                    <label class="input input-sm">
                        <span class="iconify lucide--search text-base-content/80 size-3.5"></span>
                        <input
                            class="w-24 sm:w-36"
                            placeholder="Quick search..."
                            type="search"
                            id="searchInput" />
                    </label>
                </div>

                <div class="inline-flex items-center gap-2">
                    @if(hasPermission('catalog.products.add-products.create'))
                    <a href="{{ route('catalog.products.add-products') }}" class="btn btn-primary btn-sm">
                        <span class="iconify lucide--plus size-4"></span>
                        Add Product
                    </a>
                    @endif
                    @if(hasPermission('catalog.products.all-products.export'))
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
                            <th>Image</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Brand</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody">
                        <tr id="loadingRow">
                            <td colspan="8" class="text-center py-8">
                                <span class="loading loading-spinner loading-md"></span>
                                <p class="mt-2 text-base-content/60">Loading products...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div id="paginationContainer" class="p-4"></div>
        </div>
    </div>
</div>

<!-- Product Detail Modal -->
<x-modal id="productDetailModal" size="max-w-4xl">
    <x-slot name="title">
        <h3 class="font-bold text-lg">Product Details</h3>
    </x-slot>

    <div id="productDetailContent">
        <!-- Product details will be loaded here -->
    </div>

    <x-slot name="footer">
        <button type="button" class="btn btn-ghost" onclick="productDetailModal.close()">Close</button>
    </x-slot>
</x-modal>

@endsection

@section('customjs')
@vite(['resources/js/modules/catalog/all-products/index.js'])
@endsection
