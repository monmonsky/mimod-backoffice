<div class="space-y-4">
    <!-- Upload Section -->
    <div class="border border-base-300 rounded-lg p-4 bg-base-200/50">
        <div class="mb-3">
            <h3 class="font-semibold text-sm">Upload New Images</h3>
            <p class="text-xs text-base-content/60 mt-1">Max 2MB per image, JPEG/PNG/WebP</p>
        </div>

        <!-- Custom File Input -->
        <div class="space-y-3">
            <label for="productImages" class="btn btn-outline btn-sm w-full cursor-pointer">
                <span class="iconify lucide--image-plus size-4"></span>
                Choose Images
            </label>
            <input type="file" id="productImages" class="hidden" accept="image/*" multiple>

            <!-- Selected Files Info -->
            <div id="selectedFilesInfo" class="hidden">
                <div class="bg-base-100 border border-base-300 rounded-lg p-3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium">Selected Files</span>
                        <span class="text-xs text-base-content/60" id="fileCount">0 files</span>
                    </div>
                    <div id="fileList" class="space-y-1 max-h-32 overflow-y-auto"></div>
                </div>
            </div>

            <!-- Preview Container -->
            <div id="imagePreviewContainer" class="hidden grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3"></div>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-xs text-base-content/60">
                    <span class="iconify lucide--info size-3"></span>
                    <span>You can upload multiple images at once</span>
                </div>
                <button type="button" id="uploadImagesBtn" class="btn btn-sm btn-primary" disabled>
                    <span class="iconify lucide--upload size-4"></span>
                    Upload Images
                </button>
            </div>
        </div>
    </div>

    <!-- Images Grid -->
    @if(isset($product->images) && count($product->images) > 0)
    <div>
        <div class="flex items-center justify-between mb-3">
            <div class="text-sm font-medium">Product Images ({{ count($product->images) }})</div>
            <div class="text-xs text-base-content/60">
                <span class="iconify lucide--move size-3 inline"></span>
                Drag to reorder
            </div>
        </div>

        <div id="imagesGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($product->images as $image)
            <div class="image-item relative group border-2 {{ $image->is_primary ? 'border-warning' : 'border-base-300' }} rounded-lg overflow-hidden hover:border-primary transition-colors cursor-move"
                 data-id="{{ $image->id }}"
                 data-sort="{{ $image->sort_order }}">

                <!-- Image -->
                <div class="aspect-square bg-base-200">
                    <img src="{{ asset('storage/' . $image->url) }}"
                         alt="Product Image"
                         class="w-full h-full object-cover">
                </div>

                <!-- Overlay Actions -->
                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                    <button type="button" class="btn btn-sm btn-circle btn-warning set-primary-btn"
                            data-image-id="{{ $image->id }}"
                            title="{{ $image->is_primary ? 'Primary Image' : 'Set as Primary' }}">
                        <span class="iconify lucide--star size-4 {{ $image->is_primary ? 'fill-current' : '' }}"></span>
                    </button>
                    <button type="button" class="btn btn-sm btn-circle btn-error delete-image-btn"
                            data-image-id="{{ $image->id }}"
                            title="Delete Image">
                        <span class="iconify lucide--trash size-4"></span>
                    </button>
                </div>

                <!-- Primary Badge -->
                @if($image->is_primary)
                <div class="absolute top-2 left-2">
                    <span class="badge badge-warning badge-sm gap-1">
                        <span class="iconify lucide--star size-3"></span>
                        Primary
                    </span>
                </div>
                @endif

                <!-- Sort Order Badge -->
                <div class="absolute bottom-2 right-2">
                    <span class="badge badge-sm badge-ghost bg-black/50 text-white border-0">
                        #{{ $loop->iteration }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="border-2 border-dashed border-base-300 rounded-lg p-12">
        <div class="flex flex-col items-center gap-3 text-base-content/50">
            <span class="iconify lucide--image size-16"></span>
            <div class="text-center">
                <p class="font-medium">No images uploaded yet</p>
                <p class="text-sm mt-1">Upload images to showcase your product</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Image Guidelines -->
    <div class="border border-base-300 rounded-lg p-4">
        <div class="flex items-start gap-3">
            <span class="iconify lucide--lightbulb size-5 text-warning mt-0.5"></span>
            <div class="flex-1">
                <h4 class="font-semibold text-sm mb-2">Image Guidelines</h4>
                <ul class="text-xs text-base-content/70 space-y-1">
                    <li>• Use high-quality images with good lighting</li>
                    <li>• Recommended resolution: at least 800x800 pixels</li>
                    <li>• The first image (primary) will be shown in product listings</li>
                    <li>• Show product from different angles</li>
                    <li>• Use white or neutral background for best results</li>
                </ul>
            </div>
        </div>
    </div>
</div>
