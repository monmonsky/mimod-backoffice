@extends('layouts.app')

@section('title', 'Brands')
@section('page_title', 'Catalog')
@section('page_subtitle', 'Product Brands')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Product Brands</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Catalog</li>
            <li class="opacity-80">Brands</li>
        </ul>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Total Brands</p>
                    <p class="text-2xl font-semibold mt-1">{{ $statistics['total'] }}</p>
                    <p class="text-xs text-base-content/60 mt-1">All brands</p>
                </div>
                <div class="bg-primary/10 p-3 rounded-lg">
                    <span class="iconify lucide--tag size-5 text-primary"></span>
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
                    <p class="text-xs text-base-content/60 mt-1">Published brands</p>
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
                    <p class="text-sm text-base-content/70">Inactive</p>
                    <p class="text-2xl font-semibold mt-1 text-error">{{ $statistics['inactive'] }}</p>
                    <p class="text-xs text-base-content/60 mt-1">Unpublished brands</p>
                </div>
                <div class="bg-error/10 p-3 rounded-lg">
                    <span class="iconify lucide--x-circle size-5 text-error"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Total Products</p>
                    <p class="text-2xl font-semibold mt-1 text-warning">{{ $statistics['total_products'] }}</p>
                    <p class="text-xs text-base-content/60 mt-1">Branded products</p>
                </div>
                <div class="bg-warning/10 p-3 rounded-lg">
                    <span class="iconify lucide--box size-5 text-warning"></span>
                </div>
            </div>
        </div>
    </div>
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
                                @if($brand->is_active)
                                <span class="badge badge-success badge-sm">Active</span>
                                @else
                                <span class="badge badge-error badge-sm">Inactive</span>
                                @endif
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
<dialog id="brandModal" class="modal">
    <div class="modal-box max-w-3xl">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>

        <div class="mb-6">
            <h3 class="font-bold text-lg" id="modalTitle">Add Brand</h3>
            <p class="text-sm text-base-content/70 mt-1">Define brand details</p>
        </div>

        <form id="brandForm" class="space-y-6" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="brandId" name="brand_id">
            <input type="hidden" id="formMethod" value="POST">

            <!-- Basic Information -->
            <div class="space-y-4">
                <h4 class="font-semibold text-sm">Basic Information</h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Brand Name <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="name" id="brandName" class="input input-bordered w-full"
                               placeholder="e.g., Carter's" required>
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Full brand name</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Slug <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="slug" id="brandSlug" class="input input-bordered w-full"
                               placeholder="e.g., carters" required>
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Auto-generated from name</span>
                        </label>
                    </div>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Description</span>
                    </label>
                    <textarea name="description" id="brandDescription" class="textarea textarea-bordered h-20 w-full"
                              placeholder="Brand description..."></textarea>
                </div>
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

            <!-- Actions -->
            <div class="flex justify-end gap-2">
                <button type="button" class="btn btn-ghost" onclick="brandModal.close()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <span class="iconify lucide--save size-4"></span>
                    Save Brand
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
@vite(['resources/js/modules/catalog/brands/index.js'])
@endsection
