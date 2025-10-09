@extends('layouts.app')

@section('title', isset($product) ? 'Edit Product' : 'Add Product')
@section('page_title', 'Catalog')
@section('page_subtitle', 'Product Management')

@section('content')
<x-page-header
    :title="isset($product) ? 'Edit Product' : 'Add New Product'"
    :breadcrumbs="[
        ['label' => 'Nexus', 'url' => route('dashboard')],
        ['label' => 'Products', 'url' => route('catalog.products.all-products')],
        ['label' => isset($product) ? 'Edit' : 'Add New']
    ]"
/>

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
    @if(isset($product))
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="card-title text-lg">Product Images</h2>
                    <p class="text-sm text-base-content/70">Upload and manage product images</p>
                </div>
                <div class="flex gap-2">
                    <input type="file" id="productImages" class="hidden" accept="image/*" multiple>
                    <label for="productImages" class="btn btn-sm btn-ghost">
                        <span class="iconify lucide--upload size-4"></span>
                        Select Images
                    </label>
                    <button type="button" id="uploadImagesBtn" class="btn btn-sm btn-primary" disabled>
                        <span class="iconify lucide--save size-4"></span>
                        Upload
                    </button>
                </div>
            </div>

            <div id="imagePreviewContainer" class="grid grid-cols-4 gap-3 mb-4 hidden"></div>
            <div id="productImagesContainer">
                <!-- Images will be rendered here by JavaScript -->
            </div>
        </div>
    </div>
    @endif

    <!-- Product Variants -->
    @if(isset($product))
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="card-title text-lg">Product Variants</h2>
                    <p class="text-sm text-base-content/70">Manage sizes, colors, and pricing</p>
                </div>
                <button type="button" class="btn btn-primary btn-sm" id="addVariantBtn">
                    <span class="iconify lucide--plus size-4"></span>
                    Add Variant
                </button>
            </div>

            <div id="productVariantsContainer">
                <!-- Variants will be rendered here by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <dialog id="imagePreviewModal" class="modal">
        <div class="modal-box max-w-4xl p-0">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 z-10">✕</button>
            </form>
            <div class="p-4">
                <img id="previewImage" src="" alt="Preview" class="w-full h-auto rounded-lg">
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
    @endif
</div>

<!-- Variant Modal -->
<x-modal id="variantModal" size="max-w-3xl">
    <x-slot name="title">
        <div>
            <h3 class="font-bold text-lg" id="variantModalTitle">Add Variant</h3>
            <p class="text-sm text-base-content/70 mt-1">Define variant details for this product</p>
        </div>
    </x-slot>

    <form id="variantForm" class="space-y-6">
        @csrf
        <input type="hidden" id="variantId" name="variant_id">
        <input type="hidden" id="variantMethod" value="POST">

        <!-- Basic Info -->
        <div class="space-y-4">
            <h4 class="font-semibold text-sm">Basic Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form.input
                    name="sku"
                    id="variantSku"
                    label="SKU"
                    placeholder="e.g., CTR-BL-6M"
                    required
                    helper="Unique stock keeping unit"
                />

                <x-form.input
                    name="size"
                    id="variantSize"
                    label="Size"
                    placeholder="e.g., 6M, 12M, 2Y"
                    required
                    helper="Product size"
                />

                <x-form.input
                    name="color"
                    id="variantColor"
                    label="Color"
                    placeholder="e.g., Blue"
                    helper="Optional color variant"
                />

                <x-form.input
                    type="number"
                    name="weight_gram"
                    id="variantWeight"
                    label="Weight (gram)"
                    placeholder="e.g., 150"
                    required
                    min="1"
                    helper="For shipping calculation"
                />
            </div>
        </div>

        <!-- Pricing & Stock -->
        <div class="space-y-4">
            <h4 class="font-semibold text-sm">Pricing & Inventory</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form.input
                    type="number"
                    name="price"
                    id="variantPrice"
                    label="Price"
                    placeholder="e.g., 125000"
                    required
                    min="0"
                    step="1000"
                    helper="Selling price"
                />

                <x-form.input
                    type="number"
                    name="compare_at_price"
                    id="variantComparePrice"
                    label="Compare At Price"
                    placeholder="e.g., 150000"
                    min="0"
                    step="1000"
                    helper="Original price (for discounts)"
                />

                <x-form.input
                    type="number"
                    name="stock_quantity"
                    id="variantStock"
                    label="Stock Quantity"
                    placeholder="e.g., 50"
                    required
                    min="0"
                    helper="Available stock"
                />

                <x-form.input
                    name="barcode"
                    id="variantBarcode"
                    label="Barcode"
                    placeholder="e.g., 1234567890123"
                    helper="Product barcode (optional)"
                />
            </div>
        </div>
    </form>

    <x-slot name="footer">
        <div class="flex justify-end gap-2">
            <button type="button" class="btn btn-ghost" onclick="variantModal.close()">Cancel</button>
            <button type="submit" form="variantForm" class="btn btn-primary" id="variantSubmitBtn">
                <span class="iconify lucide--save size-4"></span>
                Save Variant
            </button>
        </div>
    </x-slot>
</x-modal>
@endsection

@section('customjs')
@vite(['resources/js/modules/catalog/add-products/index.js'])
@endsection
