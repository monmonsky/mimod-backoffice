<div class="space-y-4">
    <div class="flex justify-end py-4">
        <button type="button" class="btn btn-primary btn-sm" id="addVariantBtn">
            <span class="iconify lucide--plus size-4"></span>
            Add Variant
        </button>
    </div>

    <!-- Variants Table -->
    <div class="border border-base-300 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table" id="variantsTable">
                <thead class="bg-base-200">
                    <tr>
                        <th class="font-semibold">SKU</th>
                        <th class="font-semibold">Size</th>
                        <th class="font-semibold">Color</th>
                        <th class="font-semibold">Price</th>
                        <th class="font-semibold">Stock</th>
                        <th class="font-semibold">Weight</th>
                        <th class="font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($product->variants) && count($product->variants) > 0)
                        @foreach($product->variants as $variant)
                        <tr data-id="{{ $variant->id }}" class="hover:bg-base-200/50">
                            <td>
                                <code class="text-xs bg-base-200 px-2 py-1 rounded">{{ $variant->sku }}</code>
                            </td>
                            <td>
                                <span class="badge badge-outline badge-sm">{{ $variant->size }}</span>
                            </td>
                            <td>
                                @if($variant->color)
                                <span class="text-sm">{{ $variant->color }}</span>
                                @else
                                <span class="text-sm text-base-content/40">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex flex-col">
                                    <span class="font-medium text-sm">Rp {{ number_format($variant->price, 0, ',', '.') }}</span>
                                    @if($variant->compare_at_price)
                                    <span class="text-xs text-base-content/60 line-through">
                                        Rp {{ number_format($variant->compare_at_price, 0, ',', '.') }}
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="font-medium {{ $variant->stock_quantity < 10 ? 'text-warning' : '' }}">
                                        {{ $variant->stock_quantity }}
                                    </span>
                                    @if($variant->stock_quantity < 10)
                                    <span class="badge badge-warning badge-xs">Low</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="text-sm text-base-content/70">{{ $variant->weight_gram }}g</span>
                            </td>
                            <td>
                                <div class="inline-flex gap-1">
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
                            </td>
                        </tr>
                        @endforeach
                    @else
                    <tr>
                        <td colspan="7" class="text-center py-12">
                            <div class="flex flex-col items-center gap-3 text-base-content/50">
                                <span class="iconify lucide--package size-16"></span>
                                <div>
                                    <p class="font-medium">No variants added yet</p>
                                    <p class="text-sm mt-1">Click "Add Variant" to create product variations</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Summary Stats -->
    @if(isset($product->variants) && count($product->variants) > 0)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 py-4">
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
    @endif
</div>
