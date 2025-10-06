@extends('layouts.app')

@section('title', 'Product Variants')
@section('page_title', 'Catalog')
@section('page_subtitle', 'Product Variants')

@section('content')
<x-page-header
    title="Product Variants"
    :breadcrumbs="[
        ['label' => 'Nexus', 'url' => route('dashboard')],
        ['label' => 'Catalog'],
        ['label' => 'Variants']
    ]"
/>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <x-stat-card
        title="Total Variants"
        :value="$statistics['total']"
        subtitle="All variants"
        icon="layers"
        icon-color="primary"
    />

    <x-stat-card
        title="Total Stock"
        :value="number_format($statistics['total_stock'])"
        subtitle="All variants"
        icon="package"
        icon-color="success"
    />

    <x-stat-card
        title="Low Stock"
        :value="$statistics['low_stock']"
        subtitle="Below 10 items"
        icon="triangle-alert"
        icon-color="warning"
    />

    <x-stat-card
        title="Out of Stock"
        :value="$statistics['out_of_stock']"
        subtitle="Zero stock"
        icon="x-circle"
        icon-color="error"
    />
</div>

<!-- Info Alert -->
<div class="alert alert-info mt-6">
    <span class="iconify lucide--info size-5"></span>
    <div>
        <p class="font-semibold">Manage Product Variants</p>
        <p class="text-sm">To add, edit, or delete variants, go to the individual product edit page.</p>
    </div>
    <a href="{{ route('catalog.products.all-products') }}" class="btn btn-sm btn-ghost">
        View Products
        <span class="iconify lucide--arrow-right size-4"></span>
    </a>
</div>

<!-- Variants Table -->
<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body p-0">
            <div class="flex flex-col gap-4 px-5 pt-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="inline-flex items-center gap-3 flex-wrap">
                    <label class="input input-sm">
                        <span class="iconify lucide--search text-base-content/80 size-3.5"></span>
                        <input
                            class="w-24 sm:w-36"
                            placeholder="Search variants"
                            type="search"
                            id="searchInput" />
                    </label>

                    <select class="select select-sm select-bordered" id="stockFilter">
                        <option value="">All Stock</option>
                        <option value="low">Low Stock (&lt; 10)</option>
                        <option value="out">Out of Stock</option>
                        <option value="available">In Stock</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 overflow-auto">
                <table class="table" id="variantsTable">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Size</th>
                            <th>Color</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Weight</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($variants as $variant)
                        <tr>
                            <td>
                                <div>
                                    <a href="{{ route('catalog.products.edit', $variant->product_id) }}"
                                       class="font-medium hover:text-primary">
                                        {{ $variant->product_name }}
                                    </a>
                                    @if($variant->brand_name)
                                    <p class="text-xs text-base-content/60">{{ $variant->brand_name }}</p>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <code class="text-xs bg-base-200 px-2 py-1 rounded">{{ $variant->sku }}</code>
                            </td>
                            <td>
                                <span class="badge badge-sm badge-ghost">{{ $variant->size }}</span>
                            </td>
                            <td>
                                @if($variant->color)
                                <span class="text-sm">{{ $variant->color }}</span>
                                @else
                                <span class="text-sm text-base-content/40">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex flex-col">
                                    <span class="font-medium">Rp {{ number_format($variant->price, 0, ',', '.') }}</span>
                                    @if($variant->compare_at_price && $variant->compare_at_price > $variant->price)
                                    <span class="text-xs text-base-content/60 line-through">
                                        Rp {{ number_format($variant->compare_at_price, 0, ',', '.') }}
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($variant->stock_quantity == 0)
                                <x-badge type="error" label="Out of Stock" />
                                @elseif($variant->stock_quantity <= 10)
                                <x-badge type="warning" :label="$variant->stock_quantity . ' items'" />
                                @else
                                <x-badge type="success" :label="$variant->stock_quantity . ' items'" />
                                @endif
                            </td>
                            <td>
                                <span class="text-sm">{{ $variant->weight_gram }}g</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-12">
                                <div class="flex flex-col items-center gap-3 text-base-content/50">
                                    <span class="iconify lucide--layers size-16"></span>
                                    <div>
                                        <p class="font-medium">No variants found</p>
                                        <p class="text-sm mt-1">Variants will appear here from all products</p>
                                    </div>
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
@endsection

@section('customjs')
@vite(['resources/js/modules/catalog/variants/index.js'])
@endsection
