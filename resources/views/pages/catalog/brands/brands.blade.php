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
    <x-stat-card
        title="Total Brands"
        :value="$statistics['total']"
        subtitle="All brands"
        icon="badge-info"
        icon-color="primary"
    />

    <x-stat-card
        title="Active"
        :value="$statistics['active']"
        subtitle="Published brands"
        icon="check-circle-2"
        icon-color="success"
    />

    <x-stat-card
        title="Inactive"
        :value="$statistics['inactive']"
        subtitle="Unpublished brands"
        icon="x-circle"
        icon-color="error"
    />

    <x-stat-card
        title="Total Products"
        :value="$statistics['total_products']"
        subtitle="Branded products"
        icon="box"
        icon-color="warning"
    />
</div>

<!-- Brands Table -->
<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body p-0">
            <div class="flex items-center justify-between px-5 pt-5">
                <div class="inline-flex items-center gap-3">
                    <label class="input input-sm">
                        <span class="iconify lucide--search text-base-content/80 size-3.5"></span>
                        <input
                            class="w-24 sm:w-36"
                            placeholder="Search brands"
                            type="search"
                            id="searchInput" />
                    </label>
                </div>
                <div class="inline-flex items-center gap-3">
                    @if(hasPermission('catalog.products.brands.create'))
                    <button class="btn btn-outline btn-sm" id="addBrandBtn">
                        <span class="iconify lucide--plus"></span>
                        Add Brand
                    </button>
                    @endif
                </div>
            </div>

            <div class="mt-4 overflow-auto">
                <table class="table" id="brandsTable">
                    <thead>
                        <tr>
                            <th>Brand</th>
                            <th>Slug</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($brands as $brand)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    @if($brand->logo)
                                    <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}" class="w-10 h-10 rounded object-cover">
                                    @else
                                    <div class="w-10 h-10 rounded bg-base-200 flex items-center justify-center">
                                        <span class="iconify lucide--image size-5 text-base-content/40"></span>
                                    </div>
                                    @endif
                                    <div>
                                        <p class="font-medium">{{ $brand->name }}</p>
                                        @if($brand->description)
                                        <p class="text-xs text-base-content/60 line-clamp-1">{{ $brand->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code class="text-xs bg-base-200 px-2 py-1 rounded">{{ $brand->slug }}</code>
                            </td>
                            <td>
                                <span class="badge badge-sm badge-ghost">{{ $brand->product_count ?? 0 }} products</span>
                            </td>
                            <td>
                                <x-badge :type="$brand->is_active ? 'success' : 'error'" :label="$brand->is_active ? 'Active' : 'Inactive'" />
                            </td>
                            <td>
                                <div class="inline-flex gap-2">
                                    @if(hasPermission('catalog.products.brands.update'))
                                    <button class="btn btn-sm btn-ghost edit-brand-btn" data-id="{{ $brand->id }}">
                                        <span class="iconify lucide--pencil size-4"></span>
                                    </button>

                                    <form action="{{ route('catalog.products.brands.toggle-active', $brand->id) }}" method="POST" class="toggle-form inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-ghost">
                                            @if($brand->is_active)
                                            <span class="iconify lucide--eye-off size-4"></span>
                                            @else
                                            <span class="iconify lucide--eye size-4"></span>
                                            @endif
                                        </button>
                                    </form>
                                    @endif

                                    @if(hasPermission('catalog.products.brands.delete'))
                                    <form action="{{ route('catalog.products.brands.destroy', $brand->id) }}" method="POST" class="delete-form inline">
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
                            <td colspan="5" class="text-center py-8">
                                <div class="flex flex-col items-center gap-2 text-base-content/50">
                                    <span class="iconify lucide--tag size-12"></span>
                                    <p>No brands found</p>
                                    <p class="text-xs">Create your first brand to get started</p>
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

<!-- Add/Edit Brand Modal -->
<x-modal id="brandModal" size="max-w-3xl">
    <x-slot name="title">
        <div>
            <h3 class="font-bold text-lg" id="modalTitle">Add Brand</h3>
            <p class="text-sm text-base-content/70 mt-1">Define brand details</p>
        </div>
    </x-slot>

    <form id="brandForm" class="space-y-6" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="brandId" name="brand_id">
        <input type="hidden" id="formMethod" value="POST">

        <!-- Basic Information -->
        <div class="space-y-4">
            <h4 class="font-semibold text-sm">Basic Information</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form.input
                    name="name"
                    id="brandName"
                    label="Brand Name"
                    placeholder="e.g., Carter's"
                    required
                    helper="Full brand name"
                />

                <x-form.input
                    name="slug"
                    id="brandSlug"
                    label="Slug"
                    placeholder="e.g., carters"
                    required
                    helper="Auto-generated from name"
                />
            </div>

            <x-form.textarea
                name="description"
                id="brandDescription"
                label="Description"
                placeholder="Brand description..."
                rows="3"
            />
        </div>

        <!-- Brand Logo -->
        <div class="space-y-4">
            <h4 class="font-semibold text-sm">Brand Logo</h4>

            <div class="form-control">
                <input type="file" name="logo" id="brandLogo" class="file-input file-input-bordered w-full" accept="image/*">
                <label class="label">
                    <span class="label-text-alt text-base-content/60">Max 2MB, recommended 200x200px</span>
                </label>

                <!-- Logo Preview -->
                <div id="logoPreview" class="mt-3 hidden">
                    <div class="border border-base-300 rounded-lg p-2 inline-block">
                        <img src="" alt="Preview" class="w-32 h-32 object-cover rounded">
                    </div>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="form-control">
            <label class="label cursor-pointer justify-start gap-3">
                <input type="checkbox" name="is_active" id="brandActive" class="checkbox" checked>
                <div>
                    <span class="label-text font-medium">Active</span>
                    <p class="text-xs text-base-content/60">Brand is visible and active</p>
                </div>
            </label>
        </div>
    </form>

    <x-slot name="footer">
        <div class="flex justify-end gap-2">
            <button type="button" class="btn btn-ghost" onclick="brandModal.close()">Cancel</button>
            <button type="submit" form="brandForm" class="btn btn-primary" id="submitBtn">
                <span class="iconify lucide--save size-4"></span>
                Save Brand
            </button>
        </div>
    </x-slot>
</x-modal>
@endsection

@section('customjs')
@vite(['resources/js/modules/catalog/brands/index.js'])
@endsection
