@extends('layouts.app')

@section('title', 'All Products')
@section('page_title', 'Catalog')
@section('page_subtitle', 'All Products')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">All Products</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Catalog</li>
            <li class="opacity-80">All Products</li>
        </ul>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Total Products</p>
                    <p class="text-2xl font-semibold mt-1">{{ $statistics['total'] }}</p>
                    <p class="text-xs text-base-content/60 mt-1">All products</p>
                </div>
                <div class="bg-primary/10 p-3 rounded-lg">
                    <span class="iconify lucide--package size-5 text-primary"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Active</p>
                    <p class="text-2xl font-semibold mt-1 text-success">{{ $statistics['active'] }}</p>
                    <p class="text-xs text-base-content/60 mt-1">Published products</p>
                </div>
                <div class="bg-success/10 p-3 rounded-lg">
                    <span class="iconify lucide--check-circle size-5 text-success"></span>
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
                    <p class="text-xs text-base-content/60 mt-1">Items below 10</p>
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
                    <p class="text-sm text-base-content/70">Total Stock</p>
                    <p class="text-2xl font-semibold mt-1 text-info">{{ number_format($statistics['total_stock']) }}</p>
                    <p class="text-xs text-base-content/60 mt-1">All variants</p>
                </div>
                <div class="bg-info/10 p-3 rounded-lg">
                    <span class="iconify lucide--boxes size-5 text-info"></span>
                </div>
            </div>
        </div>
    </div>
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
                            placeholder="Search products"
                            type="search"
                            id="searchInput" />
                    </label>

                    <select class="select select-sm select-bordered" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
                <div class="inline-flex items-center gap-3">
                    @if(hasPermission('catalog.products.add-products.create'))
                    <a href="{{ route('catalog.products.add-products') }}" class="btn btn-primary btn-sm">
                        <span class="iconify lucide--plus"></span>
                        Add Product
                    </a>
                    @endif
                </div>
            </div>

            <div class="mt-4 overflow-auto">
                <table class="table" id="productsTable">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Brand</th>
                            <th>Categories</th>
                            <th>Variants</th>
                            <th>Stock</th>
                            <th>Price Range</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr data-status="{{ $product->status }}">
                            <td>
                                <div class="flex items-center gap-3">
                                    @if($product->primary_image)
                                    <img src="{{ asset('storage/' . $product->primary_image->url) }}"
                                         alt="{{ $product->name }}"
                                         class="w-12 h-12 rounded object-cover">
                                    @else
                                    <div class="w-12 h-12 rounded bg-base-200 flex items-center justify-center">
                                        <span class="iconify lucide--image size-6 text-base-content/40"></span>
                                    </div>
                                    @endif
                                    <div>
                                        <p class="font-medium">{{ $product->name }}</p>
                                        <p class="text-xs text-base-content/60">SKU: {{ $product->slug }}</p>
                                        @if($product->is_featured)
                                        <span class="badge badge-warning badge-xs mt-1">Featured</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($product->brand_name)
                                <span class="text-sm">{{ $product->brand_name }}</span>
                                @else
                                <span class="text-sm text-base-content/40">-</span>
                                @endif
                            </td>
                            <td>
                                @if($product->categories && count($product->categories) > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach(array_slice($product->categories, 0, 2) as $category)
                                    <span class="badge badge-sm badge-ghost">{{ $category->name }}</span>
                                    @endforeach
                                    @if(count($product->categories) > 2)
                                    <span class="badge badge-sm badge-ghost">+{{ count($product->categories) - 2 }}</span>
                                    @endif
                                </div>
                                @else
                                <span class="text-sm text-base-content/40">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-sm {{ $product->variants_count > 0 ? 'badge-info' : 'badge-ghost' }}">
                                    {{ $product->variants_count }}
                                </span>
                            </td>
                            <td>
                                <span class="font-medium {{ $product->total_stock < 10 ? 'text-warning' : '' }}">
                                    {{ $product->total_stock }}
                                </span>
                            </td>
                            <td>
                                @if($product->min_price > 0)
                                <div class="text-sm">
                                    @if($product->min_price == $product->max_price)
                                    Rp {{ number_format($product->min_price, 0, ',', '.') }}
                                    @else
                                    Rp {{ number_format($product->min_price, 0, ',', '.') }} -
                                    Rp {{ number_format($product->max_price, 0, ',', '.') }}
                                    @endif
                                </div>
                                @else
                                <span class="text-sm text-base-content/40">No variants</span>
                                @endif
                            </td>
                            <td>
                                @if($product->status == 'active')
                                <span class="badge badge-success badge-sm">Active</span>
                                @elseif($product->status == 'inactive')
                                <span class="badge badge-error badge-sm">Inactive</span>
                                @else
                                <span class="badge badge-ghost badge-sm">Draft</span>
                                @endif
                            </td>
                            <td>
                                <div class="inline-flex gap-2">
                                    @if(hasPermission('catalog.products.all-products.update'))
                                    <a href="{{ route('catalog.products.edit', $product->id) }}" class="btn btn-sm btn-ghost">
                                        <span class="iconify lucide--pencil size-4"></span>
                                    </a>

                                    <button class="btn btn-sm btn-ghost toggle-featured-btn" data-id="{{ $product->id }}"
                                            title="{{ $product->is_featured ? 'Remove from featured' : 'Add to featured' }}">
                                        <span class="iconify lucide--star size-4 {{ $product->is_featured ? 'text-warning' : '' }}"></span>
                                    </button>

                                    <div class="dropdown dropdown-end">
                                        <button tabindex="0" class="btn btn-sm btn-ghost">
                                            <span class="iconify lucide--more-vertical size-4"></span>
                                        </button>
                                        <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                            <li><a class="change-status-btn" data-id="{{ $product->id }}" data-status="active">
                                                <span class="iconify lucide--check-circle"></span> Set Active
                                            </a></li>
                                            <li><a class="change-status-btn" data-id="{{ $product->id }}" data-status="inactive">
                                                <span class="iconify lucide--x-circle"></span> Set Inactive
                                            </a></li>
                                            <li><a class="change-status-btn" data-id="{{ $product->id }}" data-status="draft">
                                                <span class="iconify lucide--file-edit"></span> Set Draft
                                            </a></li>
                                        </ul>
                                    </div>
                                    @endif

                                    @if(hasPermission('catalog.products.all-products.delete'))
                                    <form action="{{ route('catalog.products.destroy', $product->id) }}" method="POST" class="delete-form inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-ghost text-error">
                                            <span class="iconify lucide--trash size-4"></span>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-8">
                                <div class="flex flex-col items-center gap-2 text-base-content/50">
                                    <span class="iconify lucide--package size-12"></span>
                                    <p>No products found</p>
                                    <p class="text-xs">Create your first product to get started</p>
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
@vite(['resources/js/modules/catalog/all-products/index.js'])
@endsection