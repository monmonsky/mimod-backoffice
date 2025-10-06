<div class="space-y-4">
    @if(!isset($product))
    <div class="alert alert-info">
        <span class="iconify lucide--info size-5"></span>
        <span>Categories will be saved when you create the product. Save the Basic Information first.</span>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($categories as $category)
            @if(!$category->parent_id)
            <!-- Parent Category Card -->
            <div class="border border-base-300 rounded-lg p-4">
                <label class="label cursor-pointer justify-start gap-3 mb-3 p-0">
                    <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                           class="checkbox checkbox-sm category-checkbox parent-category"
                           data-parent-id="{{ $category->id }}"
                           {{ (isset($selectedCategories) && in_array($category->id, $selectedCategories)) ? 'checked' : '' }}>
                    <span class="label-text font-semibold">{{ $category->name }}</span>
                </label>

                <!-- Child Categories -->
                @php
                    $children = $categories->filter(fn($c) => $c->parent_id == $category->id);
                @endphp
                @if($children->count() > 0)
                <div class="ml-6 space-y-1 border-l-2 border-base-300 pl-3">
                    @foreach($children as $child)
                    <label class="label cursor-pointer justify-start gap-2 py-1 px-2 hover:bg-base-200 rounded">
                        <input type="checkbox" name="categories[]" value="{{ $child->id }}"
                               class="checkbox checkbox-xs category-checkbox child-category"
                               data-parent-id="{{ $category->id }}"
                               {{ (isset($selectedCategories) && in_array($child->id, $selectedCategories)) ? 'checked' : '' }}>
                        <span class="label-text text-sm">{{ $child->name }}</span>
                    </label>
                    @endforeach
                </div>
                @endif
            </div>
            @endif
        @endforeach
    </div>

    @if(isset($product))
    <div class="flex justify-end gap-2">
        <button type="button" class="btn btn-primary" id="saveCategoriesBtn">
            <span class="iconify lucide--save size-4"></span>
            Save Categories
        </button>
    </div>
    @endif
</div>
