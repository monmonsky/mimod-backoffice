@extends('layouts.app')

@section('title', 'Brands')
@section('page_title', 'Catalog')
@section('page_subtitle', 'Product Brands')

@section('content')
<x-page-header
    title="Product Brands"
    :breadcrumbs="[
        ['label' => 'Nexus', 'url' => route('dashboard')],
        ['label' => 'Catalog'],
        ['label' => 'Brands']
    ]"
/>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Total Brands</p>
                    <p class="text-2xl font-bold" id="statTotalBrands">...</p>
                    <p class="text-xs text-base-content/60">All brands</p>
                </div>
                <span class="iconify lucide--badge-info size-8 text-base-content/20"></span>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-sm">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Active</p>
                    <p class="text-2xl font-bold" id="statActiveBrands">...</p>
                    <p class="text-xs text-base-content/60">Published brands</p>
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
                    <p class="text-2xl font-bold" id="statInactiveBrands">...</p>
                    <p class="text-xs text-base-content/60">Unpublished brands</p>
                </div>
                <span class="iconify lucide--x-circle size-8"></span>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-sm">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Total Products</p>
                    <p class="text-2xl font-bold" id="statTotalProducts">...</p>
                    <p class="text-xs text-base-content/60">Branded products</p>
                </div>
                <span class="iconify lucide--box size-8"></span>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="mt-6">
    <x-filter-section
        id="filterForm"
        title="Filter Brands"
        :action="route('catalog.products.brands')"
        method="GET"
    >
        <x-slot name="filters">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form.input
                    name="search"
                    label="Search"
                    placeholder="Search by name or slug"
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

<!-- Brands Table -->
<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body p-0">
            <div class="flex items-center justify-end px-5 pt-5">
                @if(hasPermission('catalog.products.brands.create'))
                <button class="btn btn-sm btn-primary" id="addBrandBtn">
                    <span class="iconify lucide--plus size-4"></span>
                    Add Brand
                </button>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="table table-xs md:table-sm">
                    <thead>
                        <tr>
                            <th>Brand</th>
                            <th>Slug</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="brandsTableBody">
                        <tr id="loadingRow">
                            <td colspan="5" class="text-center py-8">
                                <span class="loading loading-spinner loading-md"></span>
                                <p class="mt-2 text-base-content/60">Loading brands...</p>
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

<!-- Add/Edit Brand Modal -->
<x-modal id="brandModal" title="Create Brand" size="lg">
    <form id="brandForm" enctype="multipart/form-data">
        <input type="hidden" id="brandId" name="brand_id">
        <input type="hidden" id="formMethod" value="POST">

        <div class="space-y-4">
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Brand Name <span class="text-error">*</span></span>
                </label>
                <input type="text" name="name" id="brandName" class="input input-bordered w-full" placeholder="e.g., Carter's" required>
                <label class="label">
                    <span class="label-text-alt">Full brand name</span>
                </label>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Slug <span class="text-error">*</span></span>
                </label>
                <input type="text" name="slug" id="brandSlug" class="input input-bordered w-full" placeholder="e.g., carters" required>
                <label class="label">
                    <span class="label-text-alt">Auto-generated from name</span>
                </label>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Description</span>
                </label>
                <textarea name="description" id="brandDescription" class="textarea textarea-bordered w-full" rows="3" placeholder="Brand description..."></textarea>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text font-semibold">Brand Logo</span>
                </label>

                <div class="border-2 border-dashed border-primary/40 rounded-lg p-6 bg-base-200/30 hover:bg-base-200/50 transition-colors cursor-pointer" onclick="document.getElementById('brandLogo').click()">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center">
                            <span class="iconify lucide--upload size-8 text-primary"></span>
                        </div>
                        <div class="text-center">
                            <p class="font-semibold text-base mb-1">Click to upload image</p>
                            <p class="text-sm text-base-content/60">or drag and drop here</p>
                        </div>
                        <div class="text-xs text-base-content/50">
                            <span class="iconify lucide--info size-3 inline"></span>
                            Max 2MB | Recommended 200x200px | JPG, PNG, GIF
                        </div>
                    </div>
                </div>

                <input type="file" name="logo" id="brandLogo" class="hidden" accept="image/*">

                <!-- File name display -->
                <div id="fileName" class="mt-2 text-sm text-base-content/70 hidden"></div>

                <!-- Logo Preview -->
                <div id="logoPreview" class="mt-3 hidden">
                    <div class="border-2 border-dashed border-success/30 rounded-lg p-3 inline-block bg-base-200/50">
                        <img src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg">
                    </div>
                </div>
            </div>

            <div class="form-control">
                <label class="label cursor-pointer justify-start gap-2">
                    <input type="checkbox" name="is_active" id="brandActive" class="checkbox" checked>
                    <span class="label-text">Active</span>
                </label>
            </div>
        </div>

        <div class="modal-action">
            <button type="button" class="btn" onclick="brandModal.close()">Cancel</button>
            <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
        </div>
    </form>
</x-modal>

<!-- View Brand Modal -->
<x-modal id="viewBrandModal" title="Brand Details" size="lg">
    <div id="brandDetails"></div>
</x-modal>
@endsection

@section('customjs')
@vite(['resources/js/modules/catalog/brands/index.js'])
@endsection
