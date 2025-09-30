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

            <form class="space-y-6 mt-3">
                <!-- Store Name -->
                <div class="form-control">
                    <label class="fieldset-label" for="name">
                        <span class="label-text">Store Name <span class="text-error">*</span></span>
                    </label>
                    <input type="text" placeholder="Enter store name" class="input input-bordered w-full" value="Minimoda" />
                </div>

                <!-- Store Description -->
                <div class="form-control">
                    <label class="fieldset-label" for="name">
                        <span class="label-text">Store Description</span>
                    </label>
                    <textarea placeholder="Description" id="description" class="textarea w-full">Premium children's clothing store offering quality fashion for kids aged 0-12 years.</textarea>
                </div>

                <!-- Store Logo -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Store Logo</span>
                    </label>
                    <div class="flex items-start gap-4">
                        <div class="flex-1">
                            <div id="image-preview-filepond-demo"></div>
                            <label class="label">
                                <span class="label-text-alt text-base-content/60">Recommended size: 200x200px (PNG, JPG)</span>
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
                    <input type="email" placeholder="store@example.com" class="input input-bordered w-full" value="contact@minimoda.com" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Primary email for customer communications</span>
                    </label>
                </div>

                <!-- Phone -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Phone Number <span class="text-error">*</span></span>
                    </label>
                    <input type="tel" placeholder="+62 812 3456 7890" class="input input-bordered w-full" value="+62 812 3456 7890" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Customer service contact number</span>
                    </label>
                </div>

                <!-- WhatsApp -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">WhatsApp Number</span>
                    </label>
                    <input type="tel" placeholder="+62 812 3456 7890" class="input input-bordered w-full" value="+62 812 3456 7890" />
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
                    <textarea placeholder="Description" id="description" class="textarea w-full">Jl. Sudirman No. 123.</textarea>
                </div>

                <!-- Province & City -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Province <span class="text-error">*</span></span>
                        </label>
                        <select class="select select-bordered w-full">
                            <option disabled>Select Province</option>
                            <option selected>DKI Jakarta</option>
                            <option>Jawa Barat</option>
                            <option>Jawa Tengah</option>
                            <option>Jawa Timur</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">City <span class="text-error">*</span></span>
                        </label>
                        <select class="select select-bordered w-full">
                            <option disabled>Select City</option>
                            <option selected>Jakarta Selatan</option>
                            <option>Jakarta Pusat</option>
                            <option>Jakarta Utara</option>
                        </select>
                    </div>
                </div>

                <!-- Subdistrict & Postal Code -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Subdistrict</span>
                        </label>
                        <select class="select select-bordered w-full">
                            <option disabled>Select Subdistrict</option>
                            <option selected>Kebayoran Baru</option>
                            <option>Kebayoran Lama</option>
                            <option>Cilandak</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Postal Code <span class="text-error">*</span></span>
                        </label>
                        <input type="text" placeholder="12345" class="input input-bordered w-full" value="12180" />
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
                            <input type="text" placeholder="@minimoda_official" class="grow" value="@minimoda_official" />
                        </label>
                    </div>

                    <!-- Facebook -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Facebook</span>
                        </label>
                        <label class="input input-bordered flex items-center gap-2">
                            <span class="iconify lucide--facebook size-4 text-base-content/60"></span>
                            <input type="text" placeholder="minimoda.official" class="grow" value="minimoda.official" />
                        </label>
                    </div>

                    <!-- Twitter -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Twitter/X</span>
                        </label>
                        <label class="input input-bordered flex items-center gap-2">
                            <span class="iconify lucide--twitter size-4 text-base-content/60"></span>
                            <input type="text" placeholder="@minimoda" class="grow" value="@minimoda" />
                        </label>
                    </div>

                    <!-- TikTok -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">TikTok</span>
                        </label>
                        <label class="input input-bordered flex items-center gap-2">
                            <span class="iconify lucide--video size-4 text-base-content/60"></span>
                            <input type="text" placeholder="@minimoda_id" class="grow" value="@minimoda_id" />
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
                    <textarea placeholder="Description" id="description" class="textarea w-full">Monday - Friday: 09:00 - 18:00
Saturday: 09:00 - 15:00
Sunday: Closed
                    </textarea>
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
<link
    href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css"
    rel="stylesheet" />
<script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="{{ asset('assets/js/components/filepond.js') }}"></script>
<script>
    // Add any custom JavaScript for file upload preview, etc.
</script>
@endsection