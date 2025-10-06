@extends('layouts.app')

@section('title', 'Categories')
@section('page_title', 'Catalog')
@section('page_subtitle', 'Product Categories')

@section('content')
<x-page-header
    title="Product Categories"
    :breadcrumbs="[
        ['label' => 'Nexus', 'url' => route('dashboard')],
        ['label' => 'Catalog'],
        ['label' => 'Categories']
    ]"
/>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <x-stat-card
        title="Total Categories"
        :value="$statistics['total']"
        subtitle="All categories"
        icon="folder"
        icon-color="primary"
    />

    <x-stat-card
        title="Active"
        :value="$statistics['active']"
        subtitle="Published categories"
        icon="check-circle-2"
        icon-color="success"
    />

    <x-stat-card
        title="Parent Categories"
        :value="$statistics['parents']"
        subtitle="Top level"
        icon="layers"
        icon-color="info"
    />

    <x-stat-card
        title="Total Products"
        :value="$statistics['total_products']"
        subtitle="Categorized products"
        icon="box"
        icon-color="warning"
    />
</div>

<!-- Save Order Button (Hidden by default) -->
<div id="saveOrderContainer" class="hidden mt-6">
    <div class="alert alert-warning">
        <span class="iconify lucide--alert-triangle size-5"></span>
        <span>You have unsaved changes to category order</span>
        <button id="saveOrderBtn" class="btn btn-sm btn-primary">
            <span class="iconify lucide--save"></span>
            Save Order
        </button>
    </div>
</div>

<!-- Filter Section -->
<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium">Filter Categories</h3>
                @if(hasPermission('catalog.products.categories.create'))
                <button class="btn btn-primary btn-sm" id="addCategoryBtn">
                    <span class="iconify lucide--plus"></span>
                    Add Category
                </button>
                @endif
            </div>

            <form action="{{ route('catalog.products.categories') }}" method="GET">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Name</span>
                        </label>
                        <input type="text" name="search" placeholder="Search by name"
                               class="input input-bordered input-sm w-full"
                               value="{{ request('search') }}">
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
                        </select>
                    </div>

                    <!-- Parent Category -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Parent Category</span>
                        </label>
                        <select name="parent" class="select select-bordered select-sm w-full">
                            <option value="">All Categories</option>
                            <option value="0" {{ request('parent') === '0' ? 'selected' : '' }}>Top Level Only</option>
                            @foreach($parents ?? [] as $parent)
                            <option value="{{ $parent->id }}" {{ request('parent') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <span class="iconify lucide--search size-4"></span>
                        Apply Filter
                    </button>
                    <a href="{{ route('catalog.products.categories') }}" class="btn btn-ghost btn-sm">
                        <span class="iconify lucide--x size-4"></span>
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Categories Table -->
<div class="mt-4">
    <div class="bg-base-100 card shadow">
        <div class="card-body p-0">

            <div class="mt-4 overflow-auto">
                <table class="table" id="categoriesTable">
                    <thead>
                        <tr>
                            <th class="w-12">
                                <span class="iconify lucide--grip-vertical size-4 text-base-content/40"></span>
                            </th>
                            <th>Category</th>
                            <th>Slug</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sortableCategories">
                        @forelse($categories as $category)
                        <tr class="sortable-category" data-category-id="{{ $category->id }}" data-parent-id="{{ $category->parent_id }}">
                            <td>
                                <span class="iconify lucide--grip-vertical size-5 text-base-content/40 cursor-move drag-handle"></span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    @if($category->image)
                                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-10 h-10 rounded object-cover">
                                    @else
                                    <div class="w-10 h-10 rounded bg-base-200 flex items-center justify-center">
                                        <span class="iconify lucide--image size-5 text-base-content/40"></span>
                                    </div>
                                    @endif
                                    <div>
                                        <p class="font-medium">{{ $category->name }}</p>
                                        @if($category->parent_id)
                                        <p class="text-xs text-base-content/60">Sub-category</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code class="text-xs bg-base-200 px-2 py-1 rounded">{{ $category->slug }}</code>
                            </td>
                            <td>
                                <span class="badge badge-sm badge-ghost">{{ $category->product_count ?? 0 }} products</span>
                            </td>
                            <td>
                                <x-badge :type="$category->is_active ? 'success' : 'error'" :label="$category->is_active ? 'Active' : 'Inactive'" />
                            </td>
                            <td>
                                <div class="inline-flex gap-2">
                                    @if(hasPermission('catalog.products.categories.update'))
                                    <button class="btn btn-sm btn-ghost edit-category-btn" data-id="{{ $category->id }}">
                                        <span class="iconify lucide--pencil size-4"></span>
                                    </button>

                                    <form action="{{ route('catalog.products.categories.toggle-active', $category->id) }}" method="POST" class="toggle-form inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-ghost">
                                            @if($category->is_active)
                                            <span class="iconify lucide--eye-off size-4"></span>
                                            @else
                                            <span class="iconify lucide--eye size-4"></span>
                                            @endif
                                        </button>
                                    </form>
                                    @endif

                                    @if(hasPermission('catalog.products.categories.delete'))
                                    <form action="{{ route('catalog.products.categories.destroy', $category->id) }}" method="POST" class="delete-form inline">
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
                            <td colspan="6" class="text-center py-8">
                                <div class="flex flex-col items-center gap-2 text-base-content/50">
                                    <span class="iconify lucide--folder-open size-12"></span>
                                    <p>No categories found</p>
                                    <p class="text-xs">Create your first category to get started</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Info & Links -->
            <x-pagination-info :paginator="$categories" />
        </div>
    </div>
</div>

<!-- Add/Edit Category Modal -->
<x-modal id="categoryModal" size="max-w-3xl">
    <x-slot name="title">
        <div>
            <h3 class="font-bold text-lg" id="modalTitle">Add Category</h3>
            <p class="text-sm text-base-content/70 mt-1">Define category details</p>
        </div>
    </x-slot>

    <form id="categoryForm" class="space-y-6" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="categoryId" name="category_id">
        <input type="hidden" id="formMethod" value="POST">

        <!-- Basic Information -->
        <div class="space-y-4">
            <h4 class="font-semibold text-sm">Basic Information</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form.input
                    name="name"
                    id="categoryName"
                    label="Category Name"
                    placeholder="e.g., T-Shirts"
                    required
                    helper="Full category name"
                />

                <x-form.input
                    name="slug"
                    id="categorySlug"
                    label="Slug"
                    placeholder="e.g., t-shirts"
                    required
                    helper="Auto-generated from name"
                />
            </div>

            <x-form.select
                name="parent_id"
                id="parentCategory"
                label="Parent Category"
                helper="Select parent for nested categories"
            >
                <option value="">None (Top Level)</option>
                @foreach($parents as $parent)
                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                @endforeach
            </x-form.select>

            <x-form.textarea
                name="description"
                id="categoryDescription"
                label="Description"
                placeholder="Category description..."
                rows="3"
            />
        </div>

        <!-- Category Image -->
        <div class="space-y-4">
            <h4 class="font-semibold text-sm">Category Image</h4>

            <div class="form-control">
                <input type="file" name="image" id="categoryImage" class="file-input file-input-bordered w-full" accept="image/*">
                <label class="label">
                    <span class="label-text-alt text-base-content/60">Max 2MB, recommended 500x500px</span>
                </label>

                <!-- Image Preview -->
                <div id="imagePreview" class="mt-3 hidden">
                    <div class="border border-base-300 rounded-lg p-2 inline-block">
                        <img src="" alt="Preview" class="w-32 h-32 object-cover rounded">
                    </div>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="form-control">
            <label class="label cursor-pointer justify-start gap-3">
                <input type="checkbox" name="is_active" id="categoryActive" class="checkbox" checked>
                <div>
                    <span class="label-text font-medium">Active</span>
                    <p class="text-xs text-base-content/60">Category is visible and active</p>
                </div>
            </label>
        </div>
    </form>

    <x-slot name="footer">
        <div class="flex justify-end gap-2">
            <button type="button" class="btn btn-ghost" onclick="categoryModal.close()">Cancel</button>
            <button type="submit" form="categoryForm" class="btn btn-primary" id="submitBtn">
                <span class="iconify lucide--save size-4"></span>
                Save Category
            </button>
        </div>
    </x-slot>
</x-modal>
@endsection

@section('customjs')
@vite(['resources/js/modules/catalog/categories/index.js'])
@endsection
