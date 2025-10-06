<form id="productForm" class="space-y-6">
    @csrf
    <input type="hidden" id="productId" value="{{ $product->id ?? '' }}">

    <div class="space-y-6">
        <!-- Name & Slug -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Product Name <span class="text-error">*</span></span>
                </label>
                <input type="text" name="name" id="productName" class="input input-bordered w-full"
                       value="{{ $product->name ?? '' }}" placeholder="e.g., Carter's Baby Boy Cotton Bodysuit" required>
                <label class="label">
                    <span class="label-text-alt text-base-content/60">Full product name</span>
                </label>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Slug <span class="text-error">*</span></span>
                </label>
                <input type="text" name="slug" id="productSlug" class="input input-bordered w-full bg-base-200"
                       value="{{ $product->slug ?? '' }}" placeholder="e.g., carters-baby-boy-cotton-bodysuit" required readonly>
                <label class="label">
                    <span class="label-text-alt text-base-content/60">Auto-generated from product name</span>
                </label>
            </div>
        </div>

        <!-- Description -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Description</span>
            </label>
            <textarea name="description" id="productDescription" class="textarea textarea-bordered h-24 w-full"
                      placeholder="Product description...">{{ $product->description ?? '' }}</textarea>
        </div>

        <!-- Brand & Status -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Brand</span>
                </label>
                <select name="brand_id" id="productBrand" class="select select-bordered w-full">
                    <option value="">No Brand</option>
                    @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" {{ (isset($product) && $product->brand_id == $brand->id) ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Status <span class="text-error">*</span></span>
                </label>
                <select name="status" id="productStatus" class="select select-bordered w-full" required>
                    <option value="draft" {{ (isset($product) && $product->status == 'draft') ? 'selected' : '' }}>Draft</option>
                    <option value="active" {{ (!isset($product) || $product->status == 'active') ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ (isset($product) && $product->status == 'inactive') ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        <!-- Age Range -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Min Age (months)</span>
                </label>
                <input type="number" name="age_min" id="productAgeMin" class="input input-bordered w-full"
                       value="{{ $product->age_min ?? '' }}" placeholder="e.g., 0" min="0">
                <label class="label">
                    <span class="label-text-alt text-base-content/60">Minimum age in months</span>
                </label>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Max Age (months)</span>
                </label>
                <input type="number" name="age_max" id="productAgeMax" class="input input-bordered w-full"
                       value="{{ $product->age_max ?? '' }}" placeholder="e.g., 24" min="0">
                <label class="label">
                    <span class="label-text-alt text-base-content/60">Maximum age in months</span>
                </label>
            </div>
        </div>

        <!-- Tags -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Tags</span>
            </label>
            <input type="text" name="tags" id="productTags" class="input input-bordered w-full"
                   value="{{ isset($product->tags) ? (is_string($product->tags) ? $product->tags : implode(', ', json_decode($product->tags, true) ?? [])) : '' }}"
                   placeholder="e.g., baby, cotton, bodysuit, summer">
            <label class="label">
                <span class="label-text-alt text-base-content/60">Comma-separated tags for search and filtering</span>
            </label>
        </div>

        <!-- Featured -->
        <div class="form-control">
            <label class="label cursor-pointer justify-start gap-3">
                <input type="checkbox" name="is_featured_checkbox" id="productFeatured" class="checkbox"
                       {{ (isset($product) && $product->is_featured) ? 'checked' : '' }}>
                <input type="hidden" name="is_featured" id="productFeaturedValue" value="0">
                <div>
                    <span class="label-text font-medium">Featured Product</span>
                    <p class="text-xs text-base-content/60">Show this product in featured sections</p>
                </div>
            </label>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-end gap-2">
        <a href="{{ route('catalog.products.all-products') }}" class="btn btn-ghost">Cancel</a>
        <button type="submit" class="btn btn-primary" id="saveProductBtn">
            <span class="iconify lucide--save size-4"></span>
            {{ isset($product) ? 'Update' : 'Create' }} Product
        </button>
    </div>
</form>
