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
    <x-stat-card
        title="Total Products"
        :value="$statistics['total']"
        subtitle="All products"
        icon="package"
        icon-color="primary"
    />

    <x-stat-card
        title="Active"
        :value="$statistics['active']"
        subtitle="Published products"
        icon="check-circle-2"
        icon-color="success"
    />

    <x-stat-card
        title="Low Stock"
        :value="$statistics['low_stock']"
        subtitle="Items below 10"
        icon="triangle-alert"
        icon-color="warning"
    />

    <x-stat-card
        title="Total Stock"
        :value="number_format($statistics['total_stock'])"
        subtitle="All variants"
        icon="package"
        icon-color="info"
    />
</div>

<!-- Filter Section -->
<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium">Filter Products</h3>
                @if(hasPermission('catalog.products.add-products.create'))
                <a href="{{ route('catalog.products.add-products') }}" class="btn btn-primary btn-sm">
                    <span class="iconify lucide--plus"></span>
                    Add Product
                </a>
                @endif
            </div>

            <form action="{{ route('catalog.products.all-products') }}" method="GET">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Product Name -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Name</span>
                        </label>
                        <input type="text" name="name" placeholder="Search by name"
                               class="input input-bordered input-sm w-full"
                               value="{{ request('name') }}">
                    </div>

                    <!-- Brand -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Brand</span>
                        </label>
                        <select name="brand" class="select select-bordered select-sm w-full">
                            <option value="">All Brands</option>
                            @if(isset($brands) && $brands)
                                @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Category -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Category</span>
                        </label>
                        <select name="category" class="select select-bordered select-sm w-full">
                            <option value="">All Categories</option>
                            @if(isset($categories) && $categories)
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Variants -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Variants</span>
                        </label>
                        <select name="has_variants" class="select select-bordered select-sm w-full">
                            <option value="">All Products</option>
                            <option value="yes" {{ request('has_variants') == 'yes' ? 'selected' : '' }}>With Variants</option>
                            <option value="no" {{ request('has_variants') == 'no' ? 'selected' : '' }}>Without Variants</option>
                        </select>
                    </div>

                    <!-- Stock -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Stock</span>
                        </label>
                        <select name="stock" class="select select-bordered select-sm w-full">
                            <option value="">All Stock</option>
                            <option value="in_stock" {{ request('stock') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                            <option value="low_stock" {{ request('stock') == 'low_stock' ? 'selected' : '' }}>Low Stock (&lt; 10)</option>
                            <option value="out_of_stock" {{ request('stock') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>

                    <!-- Min Price -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Min Price</span>
                        </label>
                        <input type="number" name="min_price" placeholder="0"
                               class="input input-bordered input-sm w-full"
                               value="{{ request('min_price') }}" min="0">
                    </div>

                    <!-- Max Price -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Max Price</span>
                        </label>
                        <input type="number" name="max_price" placeholder="999999999"
                               class="input input-bordered input-sm w-full"
                               value="{{ request('max_price') }}" min="0">
                    </div>

                    <!-- Status -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Status</span>
                        </label>
                        <select name="status" class="select select-bordered select-sm w-full">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <span class="iconify lucide--search size-4"></span>
                        Apply Filter
                    </button>
                    <a href="{{ route('catalog.products.all-products') }}" class="btn btn-ghost btn-sm">
                        <span class="iconify lucide--x size-4"></span>
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Products Table -->
<div class="mt-4">
    <div class="bg-base-100 card shadow">
        <div class="card-body p-0">

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
                                    @foreach($product->categories->take(2) as $category)
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
                                <x-badge type="success" label="Active" />
                                @elseif($product->status == 'inactive')
                                <x-badge type="error" label="Inactive" />
                                @else
                                <x-badge type="ghost" label="Draft" />
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
                                                <span class="iconify lucide--check-circle-2"></span> Set Active
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

            <!-- Pagination Info & Links -->
            <x-pagination-info :paginator="$products" />
        </div>
    </div>
</div>
@endsection

@section('customjs')
@vite(['resources/js/modules/catalog/all-products/index.js'])
@endsection