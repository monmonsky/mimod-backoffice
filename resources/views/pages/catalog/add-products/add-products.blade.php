@extends('layouts.app')

@section('title', isset($product) ? 'Edit Product' : 'Add Product')
@section('page_title', 'Catalog')
@section('page_subtitle', 'Product Management')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">{{ isset($product) ? 'Edit Product' : 'Add New Product' }}</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li><a href="{{ route('catalog.products.all-products') }}">Products</a></li>
            <li class="opacity-80">{{ isset($product) ? 'Edit' : 'Add New' }}</li>
        </ul>
    </div>
</div>

<!-- Hidden product ID for JavaScript -->
@if(isset($product))
<input type="hidden" name="product_id" value="{{ $product->id }}">
@endif

<div class="mt-6 space-y-6">
    <!-- Basic Information -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Basic Information</h2>
            <p class="text-sm text-base-content/70 mb-4">Define product details</p>
            @include('pages.catalog.add-products.partials.basic-info')
        </div>
    </div>

    <!-- Categories -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Product Categories</h2>
            <p class="text-sm text-base-content/70 mb-4">Select one or more categories</p>
            @include('pages.catalog.add-products.partials.categories')
        </div>
    </div>

    <!-- Product Images -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Product Images</h2>
            <p class="text-sm text-base-content/70 mb-4">Upload and manage product images</p>
            @if(isset($product))
                @include('pages.catalog.add-products.partials.images')
            @else
                <div class="alert alert-info">
                    <span class="iconify lucide--info size-5"></span>
                    <div>
                        <p class="font-semibold">Save Product First</p>
                        <p class="text-sm">You need to save the product before you can upload images.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Product Variants -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Product Variants</h2>
            <p class="text-sm text-base-content/70 mb-4">Manage sizes, colors, and pricing</p>
            @if(isset($product))
                @include('pages.catalog.add-products.partials.variants')
            @else
                <div class="alert alert-info">
                    <span class="iconify lucide--info size-5"></span>
                    <div>
                        <p class="font-semibold">Save Product First</p>
                        <p class="text-sm">You need to save the product before you can add variants.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Variant Modal -->
<dialog id="variantModal" class="modal">
    <div class="modal-box max-w-3xl">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>

        <div class="mb-6 px-2">
            <h3 class="font-bold text-lg" id="variantModalTitle">Add Variant</h3>
            <p class="text-sm text-base-content/70 mt-1">Define variant details for this product</p>
        </div>

        <form id="variantForm" class="space-y-6">
            @csrf
            <input type="hidden" id="variantId" name="variant_id">
            <input type="hidden" id="variantMethod" value="POST">

            <!-- Basic Info -->
            <div class="space-y-4">
                <h4 class="font-semibold text-sm">Basic Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">SKU <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="sku" id="variantSku" class="input input-bordered w-full"
                               placeholder="e.g., CTR-BL-6M" required>
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Unique stock keeping unit</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Size <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="size" id="variantSize" class="input input-bordered w-full"
                               placeholder="e.g., 6M, 12M, 2Y" required>
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Product size</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Color</span>
                        </label>
                        <input type="text" name="color" id="variantColor" class="input input-bordered w-full"
                               placeholder="e.g., Blue">
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Optional color variant</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Weight (gram) <span class="text-error">*</span></span>
                        </label>
                        <input type="number" name="weight_gram" id="variantWeight" class="input input-bordered w-full"
                               placeholder="e.g., 150" required min="1">
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">For shipping calculation</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Pricing & Stock -->
            <div class="space-y-4">
                <h4 class="font-semibold text-sm">Pricing & Inventory</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Price <span class="text-error">*</span></span>
                        </label>
                        <input type="number" name="price" id="variantPrice" class="input input-bordered w-full"
                               placeholder="e.g., 125000" required min="0" step="1000">
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Selling price</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Compare At Price</span>
                        </label>
                        <input type="number" name="compare_at_price" id="variantComparePrice" class="input input-bordered w-full"
                               placeholder="e.g., 150000" min="0" step="1000">
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Original price (for discounts)</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Stock Quantity <span class="text-error">*</span></span>
                        </label>
                        <input type="number" name="stock_quantity" id="variantStock" class="input input-bordered w-full"
                               placeholder="e.g., 50" required min="0">
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Available stock</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Barcode</span>
                        </label>
                        <input type="text" name="barcode" id="variantBarcode" class="input input-bordered w-full"
                               placeholder="e.g., 1234567890123">
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Product barcode (optional)</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-2">
                <button type="button" class="btn btn-ghost" onclick="variantModal.close()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="variantSubmitBtn">
                    <span class="iconify lucide--save size-4"></span>
                    Save Variant
                </button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>
@endsection

@section('customjs')
@vite(['resources/js/modules/catalog/add-products/index.js'])
@endsection
