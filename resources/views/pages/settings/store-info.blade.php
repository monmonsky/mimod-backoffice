@extends('layouts.app')

@section('title', 'Store Info')
@section('page_title', 'Settings')
@section('page_subtitle', 'Store Information')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Store Information</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Settings</li>
            <li class="opacity-80">Store Info</li>
        </ul>
    </div>
</div>

<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Basic Information</h2>
            <p class="text-sm text-base-content/70 mb-4">Configure your store's basic details and contact information</p>

            <form id="storeInfoForm" class="space-y-6 mt-3" action="{{ route('settings.store-info.update') }}" method="POST">
                @csrf
                <!-- Store Name -->
                <div class="form-control">
                    <label class="fieldset-label" for="name">
                        <span class="label-text">Store Name <span class="text-error">*</span></span>
                    </label>
                    <input type="text" name="store_name" placeholder="Enter store name" class="input input-bordered w-full" value="{{ $storeInfo['name'] ?? 'Minimoda' }}" required />
                </div>

                <!-- Store Description -->
                <div class="form-control">
                    <label class="fieldset-label" for="name">
                        <span class="label-text">Store Description</span>
                    </label>
                    <textarea name="description" placeholder="Description" id="description" class="textarea w-full">{{ $storeInfo['description'] ?? 'Premium children\'s clothing store offering quality fashion for kids aged 0-12 years.' }}</textarea>
                </div>

                <!-- Store Tagline -->
                <div class="form-control">
                    <label class="fieldset-label">
                        <span class="label-text">Tagline</span>
                    </label>
                    <input type="text" name="tagline" placeholder="Enter tagline" class="input input-bordered w-full" value="{{ $storeInfo['tagline'] ?? 'Fashion for Little Stars' }}" />
                </div>

                <!-- Store Logo -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Store Logo</span>
                    </label>
                    <div class="flex items-start gap-4">
                        <div class="flex-1">
                            <input type="file" id="store-logo-upload" name="logo" accept="image/png,image/jpeg,image/jpg,image/svg+xml" />
                            <label class="label">
                                <span class="label-text-alt text-base-content/60">Recommended size: 200x200px (PNG, JPG, SVG - Max: 2MB)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Contact Information -->
                <h3 class="text-lg font-medium">Contact Information</h3>

                <!-- Email -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Email Address <span class="text-error">*</span></span>
                    </label>
                    <input type="email" name="email" placeholder="store@example.com" class="input input-bordered w-full" value="{{ $storeContact['email'] ?? 'contact@minimoda.com' }}" required />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Primary email for customer communications</span>
                    </label>
                </div>

                <!-- Phone -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Phone Number <span class="text-error">*</span></span>
                    </label>
                    <input type="tel" name="phone" placeholder="+62 812 3456 7890" class="input input-bordered w-full" value="{{ $storeContact['phone'] ?? '+62 812 3456 7890' }}" required />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Customer service contact number</span>
                    </label>
                </div>

                <!-- WhatsApp -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">WhatsApp Number</span>
                    </label>
                    <input type="tel" name="whatsapp" placeholder="+62 812 3456 7890" class="input input-bordered w-full" value="{{ $storeContact['whatsapp'] ?? '+62 812 3456 7890' }}" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">WhatsApp contact for customer support</span>
                    </label>
                </div>

                <div class="divider"></div>

                <!-- Address Information -->
                <h3 class="text-lg font-medium">Address Information</h3>

                <!-- Street Address -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Street Address <span class="text-error">*</span></span>
                    </label>
                    <textarea name="street" placeholder="Street address" class="textarea w-full">{{ $storeAddress['street'] ?? 'Jl. Sudirman No. 123' }}</textarea>
                </div>

                <!-- City & State -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">City <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="city" placeholder="City" class="input input-bordered w-full" value="{{ $storeAddress['city'] ?? 'Jakarta' }}" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">State/Province <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="state" placeholder="State" class="input input-bordered w-full" value="{{ $storeAddress['state'] ?? 'DKI Jakarta' }}" />
                    </div>
                </div>

                <!-- Postal Code & Country -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Postal Code <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="postal_code" placeholder="12345" class="input input-bordered w-full" value="{{ $storeAddress['postal_code'] ?? '12180' }}" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Country</span>
                        </label>
                        <input type="text" name="country" placeholder="Country" class="input input-bordered w-full" value="{{ $storeAddress['country'] ?? 'Indonesia' }}" />
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Social Media -->
                <h3 class="text-lg font-medium">Social Media</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Instagram -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Instagram</span>
                        </label>
                        <label class="input input-bordered flex items-center gap-2">
                            <span class="iconify lucide--instagram size-4 text-base-content/60"></span>
                            <input type="text" name="instagram" placeholder="@minimoda_official" class="grow" value="{{ $storeSocial['instagram'] ?? '@minimoda_official' }}" />
                        </label>
                    </div>

                    <!-- Facebook -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Facebook</span>
                        </label>
                        <label class="input input-bordered flex items-center gap-2">
                            <span class="iconify lucide--facebook size-4 text-base-content/60"></span>
                            <input type="text" name="facebook" placeholder="minimoda.official" class="grow" value="{{ $storeSocial['facebook'] ?? 'minimoda.official' }}" />
                        </label>
                    </div>

                    <!-- Twitter -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Twitter/X</span>
                        </label>
                        <label class="input input-bordered flex items-center gap-2">
                            <span class="iconify lucide--twitter size-4 text-base-content/60"></span>
                            <input type="text" name="twitter" placeholder="@minimoda" class="grow" value="{{ $storeSocial['twitter'] ?? '@minimoda' }}" />
                        </label>
                    </div>

                    <!-- TikTok -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">TikTok</span>
                        </label>
                        <label class="input input-bordered flex items-center gap-2">
                            <span class="iconify lucide--video size-4 text-base-content/60"></span>
                            <input type="text" name="tiktok" placeholder="@minimoda_id" class="grow" value="{{ $storeSocial['tiktok'] ?? '@minimoda_id' }}" />
                        </label>
                    </div>

                    <!-- YouTube -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">YouTube</span>
                        </label>
                        <label class="input input-bordered flex items-center gap-2">
                            <span class="iconify lucide--youtube size-4 text-base-content/60"></span>
                            <input type="text" name="youtube" placeholder="Minimoda Official" class="grow" value="{{ $storeSocial['youtube'] ?? '' }}" />
                        </label>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Operating Hours -->
                <h3 class="text-lg font-medium">Operating Hours</h3>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Business Hours</span>
                    </label>
                    <textarea name="operating_hours" placeholder="Enter operating hours" class="textarea w-full">{{ $operatingHours['hours'] ?? "Monday - Friday: 09:00 - 18:00\nSaturday: 09:00 - 15:00\nSunday: Closed" }}</textarea>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Store operating hours for customer reference</span>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-4"></span>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('customjs')
<!-- CDN for FilePond -->
<link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />
<script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script>
    // Register FilePond plugins
    FilePond.registerPlugin(FilePondPluginImagePreview);

    // Initialize FilePond for logo upload
    const logoInput = document.querySelector('#store-logo-upload');

    const filepondConfig = {
        credits: false,
        allowImagePreview: true,
        imagePreviewHeight: 150,
        stylePanelLayout: 'compact',
        acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml'],
        maxFileSize: '2MB',
        server: {
            process: {
                url: '{{ route("settings.store-info.upload-logo") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                onload: (response) => {
                    const data = JSON.parse(response);
                    showToast('Logo uploaded successfully!', 'success');
                    return data.path;
                },
                onerror: (response) => {
                    const data = JSON.parse(response);
                    showToast(data.message || 'Failed to upload logo', 'error');
                    return response;
                }
            },
            revert: {
                url: '{{ route("settings.store-info.delete-logo") }}',
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                onload: (response) => {
                    const data = JSON.parse(response);
                    showToast('Logo deleted successfully!', 'success');
                },
                onerror: (response) => {
                    showToast('Failed to delete logo', 'error');
                }
            },
            load: (source, load, error, progress, abort, headers) => {
                console.log('FilePond loading file from:', '/storage/' + source);
                fetch('/storage/' + source)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to load image');
                        }
                        return response.blob();
                    })
                    .then(blob => {
                        console.log('File loaded successfully:', blob);
                        load(blob);
                    })
                    .catch(err => {
                        console.error('FilePond load error:', err);
                        error('Failed to load image');
                    });

                return {
                    abort: () => {
                        abort();
                    }
                };
            }
        }
    };

    @if(isset($storeInfo['logo']) && $storeInfo['logo'])
    // Load existing logo
    console.log('Loading existing logo:', '{{ $storeInfo["logo"] }}');
    filepondConfig.files = [{
        source: '{{ $storeInfo["logo"] }}',
        options: {
            type: 'local'
        }
    }];
    @endif

    const logoPond = FilePond.create(logoInput, filepondConfig);

    // Debug: Log when file is loaded
    logoPond.on('addfile', (error, file) => {
        if (error) {
            console.error('FilePond addfile error:', error);
        } else {
            console.log('FilePond file added:', file);
        }
    });

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        const form = document.getElementById('storeInfoForm');

        if (!form) {
            console.error('Form #storeInfoForm not found!');
            return;
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Saving...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showToast(data.message || 'Settings saved successfully!', 'success');
                } else {
                    if (data.errors) {
                        const errorMessages = Object.values(data.errors).flat().join(', ');
                        showToast(errorMessages, 'error');
                    } else {
                        showToast(data.message || 'Failed to save settings', 'error');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('An error occurred while saving settings', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    }
</script>
@endsection