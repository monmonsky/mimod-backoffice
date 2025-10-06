@extends('layouts.app')

@section('title', 'Product Variants')
@section('page_title', 'Catalog')
@section('page_subtitle', 'Product Variants')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Product Variants</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Catalog</li>
            <li class="opacity-80">Variants</li>
        </ul>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Total Variants</p>
                    <p class="text-2xl font-semibold mt-1">{{ $statistics['total'] }}</p>
                    <p class="text-xs text-base-content/60 mt-1">All variants</p>
                </div>
                <div class="bg-primary/10 p-3 rounded-lg">
                    <span class="iconify lucide--layers size-5 text-primary"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Total Stock</p>
                    <p class="text-2xl font-semibold mt-1 text-success">{{ number_format($statistics['total_stock']) }}</p>
                    <p class="text-xs text-base-content/60 mt-1">All variants</p>
                </div>
                <div class="bg-success/10 p-3 rounded-lg">
                    <span class="iconify lucide--package size-5 text-success"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Low Stock</p>
                    <p class="text-2xl font-semibold mt-1 text-warning">{{ $statistics['low_stock'] }}</p>
                    <p class="text-xs text-base-content/60 mt-1">Below 10 items</p>
                </div>
                <div class="bg-warning/10 p-3 rounded-lg">
                    <span class="iconify lucide--alert-triangle size-5 text-warning"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Out of Stock</p>
                    <p class="text-2xl font-semibold mt-1 text-error">{{ $statistics['out_of_stock'] }}</p>
                    <p class="text-xs text-base-content/60 mt-1">Zero stock</p>
                </div>
                <div class="bg-error/10 p-3 rounded-lg">
                    <span class="iconify lucide--x-circle size-5 text-error"></span>
                </div>
            </div>
        </div>
    </div>
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
                                <span class="badge badge-error badge-sm">Out of Stock</span>
                                @elseif($variant->stock_quantity <= 10)
                                <span class="badge badge-warning badge-sm">{{ $variant->stock_quantity }} items</span>
                                @else
                                <span class="badge badge-success badge-sm">{{ $variant->stock_quantity }} items</span>
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
