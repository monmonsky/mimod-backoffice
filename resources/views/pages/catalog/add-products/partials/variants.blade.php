<div class="space-y-4">
    <div class="flex justify-end py-4">
        <button type="button" class="btn btn-primary btn-sm" id="addVariantBtn">
            <span class="iconify lucide--plus size-4"></span>
            Add Variant
        </button>
    </div>

    <!-- Variants Cards -->
    @if(isset($product->variants) && count($product->variants) > 0)
        <div class="grid grid-cols-1 gap-4">
            @foreach($product->variants as $variant)
            <div class="card bg-base-100 border border-base-300 shadow-sm">
                <div class="card-body p-4">
                    <!-- Variant Info -->
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-6 gap-4">
                            <div>
                                <div class="text-xs text-base-content/60 mb-1">SKU</div>
                                <code class="text-xs bg-base-200 px-2 py-1 rounded">{{ $variant->sku }}</code>
                            </div>
                            <div>
                                <div class="text-xs text-base-content/60 mb-1">Size</div>
                                <span class="badge badge-outline badge-sm">{{ $variant->size }}</span>
                            </div>
                            <div>
                                <div class="text-xs text-base-content/60 mb-1">Color</div>
                                @if($variant->color)
                                <span class="text-sm font-medium">{{ $variant->color }}</span>
                                @else
                                <span class="text-sm text-base-content/40">-</span>
                                @endif
                            </div>
                            <div>
                                <div class="text-xs text-base-content/60 mb-1">Price</div>
                                <div class="flex flex-col">
                                    <span class="font-semibold text-sm">Rp {{ number_format($variant->price, 0, ',', '.') }}</span>
                                    @if($variant->compare_at_price)
                                    <span class="text-xs text-base-content/60 line-through">
                                        Rp {{ number_format($variant->compare_at_price, 0, ',', '.') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-base-content/60 mb-1">Stock</div>
                                <span class="badge {{ $variant->stock_quantity > 10 ? 'badge-success' : ($variant->stock_quantity > 0 ? 'badge-warning' : 'badge-error') }} badge-sm">
                                    {{ $variant->stock_quantity }} units
                                </span>
                            </div>
                            <div>
                                <div class="text-xs text-base-content/60 mb-1">Weight</div>
                                <span class="text-sm">{{ $variant->weight_gram }}g</span>
                            </div>
                        </div>
                        <div class="flex gap-1">
                            <button type="button" class="btn btn-sm btn-ghost edit-variant-btn"
                                    data-id="{{ $variant->id }}"
                                    data-sku="{{ $variant->sku }}"
                                    data-size="{{ $variant->size }}"
                                    data-color="{{ $variant->color }}"
                                    data-weight="{{ $variant->weight_gram }}"
                                    data-price="{{ $variant->price }}"
                                    data-compare-price="{{ $variant->compare_at_price ?? '' }}"
                                    data-stock="{{ $variant->stock_quantity }}"
                                    data-barcode="{{ $variant->barcode ?? '' }}">
                                <span class="iconify lucide--pencil size-4"></span>
                            </button>
                            <button type="button" class="btn btn-sm btn-ghost text-error delete-variant-btn"
                                    data-id="{{ $variant->id }}">
                                <span class="iconify lucide--trash size-4"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="divider my-2"></div>

                    <!-- Variant Images Section -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="font-semibold text-sm flex items-center gap-2">
                                <span class="iconify lucide--image size-4"></span>
                                Variant Images
                            </h4>
                            <div class="flex items-center gap-2">
                                <input
                                    type="file"
                                    id="variantImages-{{ $variant->id }}"
                                    class="variant-images-input hidden"
                                    data-variant-id="{{ $variant->id }}"
                                    accept="image/*"
                                    multiple
                                >
                                <label for="variantImages-{{ $variant->id }}" class="btn btn-sm btn-ghost">
                                    <span class="iconify lucide--upload size-4"></span>
                                    Select Images
                                </label>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-primary upload-variant-images-btn"
                                    data-variant-id="{{ $variant->id }}"
                                    disabled
                                >
                                    <span class="iconify lucide--save size-4"></span>
                                    Upload
                                </button>
                            </div>
                        </div>

                        <!-- Image Preview -->
                        <div id="variantImagePreview-{{ $variant->id }}" class="grid grid-cols-6 gap-2 hidden"></div>

                        <!-- Existing Images -->
                        @if(isset($variant->images) && count($variant->images) > 0)
                        <div class="grid grid-cols-6 gap-2">
                            @foreach($variant->images as $image)
                            <div class="relative group border-2 border-base-300 rounded-lg overflow-hidden cursor-pointer variant-image-item" data-image-url="{{ asset('storage/' . $image->url) }}">
                                <img src="{{ asset('storage/' . $image->url) }}" alt="{{ $image->alt_text }}" class="w-full h-24 object-cover">
                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-1">
                                    <button
                                        type="button"
                                        class="btn btn-xs btn-ghost text-white preview-variant-image-btn"
                                        data-image-url="{{ asset('storage/' . $image->url) }}"
                                        title="Preview"
                                    >
                                        <span class="iconify lucide--eye size-3"></span>
                                    </button>
                                    @if(!$image->is_primary)
                                    <button
                                        type="button"
                                        class="btn btn-xs btn-success set-primary-variant-btn"
                                        data-variant-id="{{ $variant->id }}"
                                        data-image-id="{{ $image->id }}"
                                        title="Set as primary"
                                    >
                                        <span class="iconify lucide--star size-3"></span>
                                    </button>
                                    @endif
                                    <button
                                        type="button"
                                        class="btn btn-xs btn-error delete-variant-image-btn"
                                        data-variant-id="{{ $variant->id }}"
                                        data-image-id="{{ $image->id }}"
                                    >
                                        <span class="iconify lucide--trash size-3"></span>
                                    </button>
                                </div>
                                @if($image->is_primary)
                                <div class="absolute top-1 left-1">
                                    <span class="badge badge-xs badge-success">Primary</span>
                                </div>
                                @endif
                                <div class="absolute bottom-1 right-1">
                                    <span class="badge badge-xs badge-ghost bg-black/70 text-white border-0">
                                        #{{ $loop->iteration }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-6 bg-base-200/30 rounded-lg">
                            <span class="iconify lucide--image-off size-8 mx-auto mb-2 text-base-content/40"></span>
                            <p class="text-sm text-base-content/50">No images uploaded for this variant</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="border border-base-300 rounded-lg p-3">
                <div class="text-xs text-base-content/60">Total Variants</div>
                <div class="text-2xl font-semibold mt-1">{{ count($product->variants) }}</div>
            </div>
            <div class="border border-base-300 rounded-lg p-3">
                <div class="text-xs text-base-content/60">Total Stock</div>
                <div class="text-2xl font-semibold mt-1">{{ collect($product->variants)->sum('stock_quantity') }}</div>
            </div>
            <div class="border border-base-300 rounded-lg p-3">
                <div class="text-xs text-base-content/60">Low Stock Items</div>
                <div class="text-2xl font-semibold text-warning mt-1">
                    {{ collect($product->variants)->filter(fn($v) => $v->stock_quantity < 10)->count() }}
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-12 border border-base-300 rounded-lg">
            <div class="flex flex-col items-center gap-3 text-base-content/50">
                <span class="iconify lucide--package size-16"></span>
                <div>
                    <p class="font-medium">No variants added yet</p>
                    <p class="text-sm mt-1">Click "Add Variant" to create product variations</p>
                </div>
            </div>
        </div>
    @endif
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
