@extends('layouts.app')

@section('title', 'Categories')
@section('page_title', 'Catalog')
@section('page_subtitle', 'Product Categories')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Product Categories</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Catalog</li>
            <li class="opacity-80">Categories</li>
        </ul>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Total Categories</p>
                    <p class="text-2xl font-semibold mt-1">{{ $statistics['total'] }}</p>
                    <p class="text-xs text-base-content/60 mt-1">All categories</p>
                </div>
                <div class="bg-primary/10 p-3 rounded-lg">
                    <span class="iconify lucide--folder size-5 text-primary"></span>
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
                    <p class="text-xs text-base-content/60 mt-1">Published categories</p>
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
                    <p class="text-sm text-base-content/70">Parent Categories</p>
                    <p class="text-2xl font-semibold mt-1 text-info">{{ $statistics['parents'] }}</p>
                    <p class="text-xs text-base-content/60 mt-1">Top level</p>
                </div>
                <div class="bg-info/10 p-3 rounded-lg">
                    <span class="iconify lucide--layers size-5 text-info"></span>
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
                    <p class="text-xs text-base-content/60 mt-1">Categorized products</p>
                </div>
                <div class="bg-warning/10 p-3 rounded-lg">
                    <span class="iconify lucide--box size-5 text-warning"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Save Order Button (Hidden by default) -->
<div id="saveOrderContainer" class="hidden mt-4">
    <div class="alert alert-warning">
        <span class="iconify lucide--alert-triangle size-5"></span>
        <span>You have unsaved changes to category order</span>
        <button id="saveOrderBtn" class="btn btn-sm btn-primary">
            <span class="iconify lucide--save"></span>
            Save Order
        </button>
    </div>
</div>

<!-- Categories Table -->
<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body p-0">
            <div class="flex items-center justify-between px-5 pt-5">
                <div class="inline-flex items-center gap-3">
                    <label class="input input-sm">
                        <span class="iconify lucide--search text-base-content/80 size-3.5"></span>
                        <input
                            class="w-24 sm:w-36"
                            placeholder="Search categories"
                            type="search"
                            id="searchInput" />
                    </label>
                </div>
                <div class="inline-flex items-center gap-3">
                    @if(hasPermission('catalog.products.categories.create'))
                    <button class="btn btn-outline btn-sm" id="addCategoryBtn">
                        <span class="iconify lucide--plus"></span>
                        Add Category
                    </button>
                    @endif
                </div>
            </div>

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
                                @if($category->is_active)
                                <span class="badge badge-success badge-sm">Active</span>
                                @else
                                <span class="badge badge-error badge-sm">Inactive</span>
                                @endif
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
        </div>
    </div>
</div>

<!-- Add/Edit Category Modal -->
<dialog id="categoryModal" class="modal">
    <div class="modal-box max-w-3xl">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>

        <div class="mb-6">
            <h3 class="font-bold text-lg" id="modalTitle">Add Category</h3>
            <p class="text-sm text-base-content/70 mt-1">Define category details</p>
        </div>

        <form id="categoryForm" class="space-y-6" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="categoryId" name="category_id">
            <input type="hidden" id="formMethod" value="POST">

            <!-- Basic Information -->
            <div class="space-y-4">
                <h4 class="font-semibold text-sm">Basic Information</h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Category Name <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="name" id="categoryName" class="input input-bordered w-full"
                               placeholder="e.g., T-Shirts" required>
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Full category name</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Slug <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="slug" id="categorySlug" class="input input-bordered w-full"
                               placeholder="e.g., t-shirts" required>
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Auto-generated from name</span>
                        </label>
                    </div>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Parent Category</span>
                    </label>
                    <select name="parent_id" id="parentCategory" class="select select-bordered w-full">
                        <option value="">None (Top Level)</option>
                        @foreach($parents as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </select>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Select parent for nested categories</span>
                    </label>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Description</span>
                    </label>
                    <textarea name="description" id="categoryDescription" class="textarea textarea-bordered h-20 w-full"
                              placeholder="Category description..."></textarea>
                </div>
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

            <!-- Actions -->
            <div class="flex justify-end gap-2">
                <button type="button" class="btn btn-ghost" onclick="categoryModal.close()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <span class="iconify lucide--save size-4"></span>
                    Save Category
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
@vite(['resources/js/modules/catalog/categories/index.js'])
@endsection
