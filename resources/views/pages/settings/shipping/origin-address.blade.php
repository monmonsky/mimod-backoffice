@extends('layouts.app')

@section('title', 'Origin Address')
@section('page_title', 'Settings')
@section('page_subtitle', 'Origin Address')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Origin Address Configuration</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Settings</li>
            <li>Shipping</li>
            <li class="opacity-80">Origin Address</li>
        </ul>
    </div>
</div>

<div class="mt-6 space-y-6">
    <!-- Information Alert -->
    <div class="alert alert-info">
        <span class="iconify lucide--info size-5"></span>
        <div class="flex-1">
            <h4 class="font-medium">Origin Address for Shipping Calculation</h4>
            <p class="text-sm">Configure the warehouse or store address used as the origin point for shipping cost calculations with RajaOngkir</p>
        </div>
    </div>

    <!-- Primary Origin Address -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Primary Origin Address</h2>
            <p class="text-sm text-base-content/70 mb-4">Main warehouse or store address for shipping</p>

            <form class="space-y-6">
                <!-- Location Name -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Location Name <span class="text-error">*</span></span>
                    </label>
                    <input type="text" placeholder="e.g., Main Warehouse" class="input input-bordered w-full" value="Main Warehouse - Jakarta" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Internal name for this location</span>
                    </label>
                </div>

                <!-- Contact Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Contact Person <span class="text-error">*</span></span>
                        </label>
                        <input type="text" placeholder="Enter contact name" class="input input-bordered w-full" value="John Doe" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Phone Number <span class="text-error">*</span></span>
                        </label>
                        <input type="tel" placeholder="+62 812 3456 7890" class="input input-bordered w-full" value="+62 812 3456 7890" />
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Address Information -->
                <h3 class="font-medium">Address Details</h3>

                <!-- Province & City -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Province <span class="text-error">*</span></span>
                        </label>
                        <select class="select select-bordered w-full" id="province">
                            <option disabled>Select Province</option>
                            <option value="6" selected>DKI Jakarta</option>
                            <option value="9">Jawa Barat</option>
                            <option value="10">Jawa Tengah</option>
                            <option value="11">Jawa Timur</option>
                        </select>
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Province ID: <span class="font-mono">6</span></span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">City / Regency <span class="text-error">*</span></span>
                        </label>
                        <select class="select select-bordered w-full" id="city">
                            <option disabled>Select City</option>
                            <option value="151" selected>Jakarta Selatan</option>
                            <option value="152">Jakarta Pusat</option>
                            <option value="153">Jakarta Utara</option>
                            <option value="154">Jakarta Barat</option>
                            <option value="155">Jakarta Timur</option>
                        </select>
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">City ID: <span class="font-mono">151</span></span>
                        </label>
                    </div>
                </div>

                <!-- Subdistrict -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Subdistrict (Kecamatan)</span>
                    </label>
                    <select class="select select-bordered w-full">
                        <option disabled>Select Subdistrict</option>
                        <option selected>Kebayoran Baru</option>
                        <option>Kebayoran Lama</option>
                        <option>Cilandak</option>
                        <option>Pasar Minggu</option>
                        <option>Mampang Prapatan</option>
                    </select>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Optional: Used for more accurate shipping calculations</span>
                    </label>
                </div>

                <!-- Street Address -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Street Address <span class="text-error">*</span></span>
                    </label>
                    <textarea class="textarea textarea-bordered" rows="3" placeholder="Enter complete street address">Jl. Sudirman No. 123, Kebayoran Baru</textarea>
                </div>

                <!-- Postal Code -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Postal Code <span class="text-error">*</span></span>
                    </label>
                    <input type="text" placeholder="12180" class="input input-bordered w-full" value="12180" />
                </div>

                <!-- Coordinates (Optional) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Latitude (Optional)</span>
                        </label>
                        <input type="text" placeholder="-6.2088" class="input input-bordered w-full" value="-6.2088" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Longitude (Optional)</span>
                        </label>
                        <input type="text" placeholder="106.8456" class="input input-bordered w-full" value="106.8456" />
                    </div>
                </div>

                <div class="alert alert-warning">
                    <span class="iconify lucide--map-pin size-5"></span>
                    <div class="flex-1">
                        <p class="text-sm">Use Google Maps to find exact coordinates of your warehouse location</p>
                    </div>
                    <a href="https://www.google.com/maps" target="_blank" class="btn btn-sm btn-outline">
                        <span class="iconify lucide--external-link size-4"></span>
                        Open Maps
                    </a>
                </div>

                <div class="divider"></div>

                <!-- Additional Settings -->
                <h3 class="font-medium">Additional Settings</h3>

                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" class="toggle toggle-primary" checked />
                        <div>
                            <span class="label-text font-medium">Set as default origin</span>
                            <p class="text-xs text-base-content/60">Use this address as the default shipping origin</p>
                        </div>
                    </label>
                </div>

                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" class="checkbox checkbox-primary" checked />
                        <span class="label-text">Active</span>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-4"></span>
                        Save Origin Address
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Additional Origin Addresses -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="card-title text-lg">Additional Origin Addresses</h2>
                    <p class="text-sm text-base-content/70">Manage multiple warehouse or store locations</p>
                </div>
                <button type="button" class="btn btn-primary btn-sm" onclick="add_origin_modal.showModal()">
                    <span class="iconify lucide--plus size-4"></span>
                    Add Origin Address
                </button>
            </div>

            <!-- Origin Address List -->
            <div class="space-y-3">
                <!-- Secondary Warehouse -->
                <div class="border border-base-300 rounded-lg p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-medium">Secondary Warehouse - Bandung</h3>
                                <span class="badge badge-outline badge-sm">Secondary</span>
                            </div>
                            <div class="space-y-1 text-sm text-base-content/70">
                                <p><span class="iconify lucide--map-pin size-3.5 inline"></span> Jl. Asia Afrika No. 45, Bandung</p>
                                <p><span class="iconify lucide--building-2 size-3.5 inline"></span> Bandung, Jawa Barat (23) - City ID: 23</p>
                                <p><span class="iconify lucide--phone size-3.5 inline"></span> +62 822 3456 7890</p>
                            </div>
                            <div class="flex gap-2 mt-3">
                                <span class="badge badge-success badge-xs">Active</span>
                            </div>
                        </div>
                        <div class="inline-flex gap-1">
                            <button class="btn btn-square btn-ghost btn-sm">
                                <span class="iconify lucide--pencil size-4"></span>
                            </button>
                            <button class="btn btn-square btn-ghost btn-sm">
                                <span class="iconify lucide--copy size-4"></span>
                            </button>
                            <button class="btn btn-square btn-error btn-outline btn-sm border-transparent">
                                <span class="iconify lucide--trash size-4"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Store Surabaya -->
                <div class="border border-base-300 rounded-lg p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-medium">Store - Surabaya</h3>
                                <span class="badge badge-outline badge-sm">Store</span>
                            </div>
                            <div class="space-y-1 text-sm text-base-content/70">
                                <p><span class="iconify lucide--map-pin size-3.5 inline"></span> Jl. Tunjungan No. 88, Surabaya</p>
                                <p><span class="iconify lucide--building-2 size-3.5 inline"></span> Surabaya, Jawa Timur (444) - City ID: 444</p>
                                <p><span class="iconify lucide--phone size-3.5 inline"></span> +62 831 3456 7890</p>
                            </div>
                            <div class="flex gap-2 mt-3">
                                <span class="badge badge-error badge-xs">Inactive</span>
                            </div>
                        </div>
                        <div class="inline-flex gap-1">
                            <button class="btn btn-square btn-ghost btn-sm">
                                <span class="iconify lucide--pencil size-4"></span>
                            </button>
                            <button class="btn btn-square btn-ghost btn-sm">
                                <span class="iconify lucide--copy size-4"></span>
                            </button>
                            <button class="btn btn-square btn-error btn-outline btn-sm border-transparent">
                                <span class="iconify lucide--trash size-4"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Testing Tool -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Test Shipping Cost Calculation</h2>
            <p class="text-sm text-base-content/70 mb-4">Test shipping cost from your origin address to any destination</p>

            <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Origin</span>
                        </label>
                        <select class="select select-bordered w-full">
                            <option selected>Main Warehouse - Jakarta (151)</option>
                            <option>Secondary Warehouse - Bandung (23)</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Destination City ID</span>
                        </label>
                        <input type="number" placeholder="e.g., 444" class="input input-bordered w-full" value="444" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Weight (gram)</span>
                        </label>
                        <input type="number" placeholder="1000" class="input input-bordered w-full" value="1000" />
                    </div>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Courier</span>
                    </label>
                    <select class="select select-bordered w-full">
                        <option selected>JNE</option>
                        <option>J&T Express</option>
                        <option>SiCepat</option>
                        <option>All Couriers</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="button" class="btn btn-primary">
                        <span class="iconify lucide--calculator size-4"></span>
                        Calculate Shipping Cost
                    </button>
                </div>

                <!-- Test Results -->
                <div class="alert alert-success hidden" id="test-results">
                    <div class="flex-1">
                        <h4 class="font-medium mb-2">Shipping Cost Results</h4>
                        <div class="space-y-1 text-sm">
                            <p>• JNE REG: Rp 15,000 (3-4 days)</p>
                            <p>• JNE YES: Rp 35,000 (1-2 days)</p>
                            <p>• JNE OKE: Rp 12,000 (4-5 days)</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Add Origin Address -->
<dialog id="add_origin_modal" class="modal">
    <div class="modal-box max-w-2xl">
        <div class="flex items-center justify-between text-lg font-medium mb-4">
            Add Origin Address
            <form method="dialog">
                <button class="btn btn-sm btn-ghost btn-circle" aria-label="Close modal">
                    <span class="iconify lucide--x size-4"></span>
                </button>
            </form>
        </div>

        <form class="space-y-4">
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Location Name <span class="text-error">*</span></span>
                </label>
                <input type="text" placeholder="e.g., Warehouse 2 - Medan" class="input input-bordered w-full" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Contact Person</span>
                    </label>
                    <input type="text" placeholder="Contact name" class="input input-bordered w-full" />
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Phone</span>
                    </label>
                    <input type="tel" placeholder="+62 812 3456 7890" class="input input-bordered w-full" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Province <span class="text-error">*</span></span>
                    </label>
                    <select class="select select-bordered w-full">
                        <option disabled selected>Select Province</option>
                        <option>DKI Jakarta</option>
                        <option>Jawa Barat</option>
                        <option>Jawa Tengah</option>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">City <span class="text-error">*</span></span>
                    </label>
                    <select class="select select-bordered w-full">
                        <option disabled selected>Select City</option>
                    </select>
                </div>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Street Address <span class="text-error">*</span></span>
                </label>
                <textarea class="textarea textarea-bordered" placeholder="Complete street address"></textarea>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Postal Code</span>
                </label>
                <input type="text" placeholder="12180" class="input input-bordered w-full" />
            </div>

            <div class="modal-action">
                <form method="dialog">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                </form>
                <button type="submit" class="btn btn-primary">
                    <span class="iconify lucide--plus size-4"></span>
                    Add Address
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
<script>
    // Province and City selection logic
    document.getElementById('province')?.addEventListener('change', function() {
        // Fetch cities based on province
        console.log('Province changed:', this.value);
    });
</script>
@endsection