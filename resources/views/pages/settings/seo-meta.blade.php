@extends('layouts.app')

@section('title', 'SEO & Meta')
@section('page_title', 'Settings')
@section('page_subtitle', 'SEO & Meta Tags')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">SEO & Meta Tags</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Settings</li>
            <li class="opacity-80">SEO & Meta</li>
        </ul>
    </div>
</div>

<div class="mt-6 space-y-6">
    <!-- Basic SEO Settings -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Basic SEO Settings</h2>
            <p class="text-sm text-base-content/70 mb-4">Configure meta tags and SEO information for your store</p>

            <form class="space-y-6">
                <!-- Site Title -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Site Title <span class="text-error">*</span></span>
                    </label>
                    <input type="text" placeholder="Enter site title" class="input input-bordered w-full" value="Minimoda - Premium Kids Fashion Store" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">
                            <span class="font-mono">60</span> characters • This appears in browser tabs and search results
                        </span>
                    </label>
                </div>

                <!-- Meta Description -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Meta Description <span class="text-error">*</span></span>
                    </label>
                    <textarea id="description" class="textarea w-full h-24" placeholder="Enter meta description">Shop premium quality children's clothing at Minimoda. Discover trendy and comfortable fashion for kids aged 0-12 years. Free shipping on orders over Rp 250,000.</textarea>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">
                            <span class="font-mono">155</span> characters • Recommended: 150-160 characters
                        </span>
                    </label>
                </div>

                <!-- Meta Keywords -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Meta Keywords</span>
                    </label>
                    <input type="text" placeholder="Enter keywords separated by commas" class="input input-bordered w-full" value="kids fashion, children clothing, baby clothes, toddler wear, minimoda" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Separate keywords with commas</span>
                    </label>
                </div>

                <!-- Canonical URL -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Canonical URL</span>
                    </label>
                    <input type="url" placeholder="https://www.minimoda.com" class="input input-bordered w-full" value="https://www.minimoda.com" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">The preferred URL for your website</span>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-4"></span>
                        Save SEO Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Open Graph Settings -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="card-title text-lg">Open Graph (Facebook)</h2>
                    <p class="text-sm text-base-content/70">Control how your content appears when shared on Facebook and other platforms</p>
                </div>
                <div class="form-control">
                    <label class="label cursor-pointer gap-2">
                        <span class="label-text text-sm">Enable OG</span>
                        <input type="checkbox" class="toggle toggle-primary" checked />
                    </label>
                </div>
            </div>

            <form class="space-y-6">
                <!-- OG Title -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">OG Title</span>
                    </label>
                    <input type="text" placeholder="Enter Open Graph title" class="input input-bordered w-full" value="Minimoda - Premium Kids Fashion Store" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Title for social media sharing</span>
                    </label>
                </div>

                <!-- OG Description -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">OG Description</span>
                    </label>
                    <textarea id="description" class="textarea w-full" placeholder="Enter Open Graph description">Discover premium quality children's clothing at Minimoda. Trendy, comfortable, and affordable fashion for kids aged 0-12 years.</textarea>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Description for social media sharing</span>
                    </label>
                </div>

                <!-- OG Image -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">OG Image</span>
                    </label>
                    <div class="flex items-start gap-4">
                        <div class="avatar">
                            <div class="w-32 rounded">
                                <img src="https://placehold.co/1200x630/png?text=OG+IMAGE" alt="OG Image" />
                            </div>
                        </div>
                        <div class="flex-1">
                            <input type="file" class="file-input file-input-bordered w-full max-w-xs" accept="image/*" />
                            <label class="label">
                                <span class="label-text-alt text-base-content/60">Recommended size: 1200x630px (PNG or JPG)</span>
                            </label>
                            <input type="url" placeholder="Or enter image URL" class="input input-bordered w-full mt-2" value="https://www.minimoda.com/og-image.jpg" />
                        </div>
                    </div>
                </div>

                <!-- OG Type & Site Name -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">OG Type</span>
                        </label>
                        <select class="select select-bordered w-full">
                            <option selected>website</option>
                            <option>article</option>
                            <option>product</option>
                            <option>business</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">OG Site Name</span>
                        </label>
                        <input type="text" placeholder="Enter site name" class="input input-bordered w-full" value="Minimoda" />
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-4"></span>
                        Save OG Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Twitter Card Settings -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="card-title text-lg">Twitter Card</h2>
                    <p class="text-sm text-base-content/70">Customize how your content appears when shared on Twitter/X</p>
                </div>
                <div class="form-control">
                    <label class="label cursor-pointer gap-2">
                        <span class="label-text text-sm">Enable Twitter Card</span>
                        <input type="checkbox" class="toggle toggle-primary" checked />
                    </label>
                </div>
            </div>

            <form class="space-y-6">
                <!-- Card Type -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Card Type</span>
                    </label>
                    <select class="select select-bordered w-full">
                        <option selected>summary_large_image</option>
                        <option>summary</option>
                        <option>app</option>
                        <option>player</option>
                    </select>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Type of Twitter card to display</span>
                    </label>
                </div>

                <!-- Twitter Title -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Twitter Title</span>
                    </label>
                    <input type="text" placeholder="Enter Twitter title" class="input input-bordered w-full" value="Minimoda - Premium Kids Fashion Store" />
                </div>

                <!-- Twitter Description -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Twitter Description</span>
                    </label>
                    <textarea id="description" class="textarea w-full" placeholder="Enter Twitter description">Shop premium quality children's clothing at Minimoda. Trendy and comfortable fashion for kids.</textarea>
                </div>

                <!-- Twitter Image -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Twitter Image</span>
                    </label>
                    <div class="flex items-start gap-4">
                        <div class="avatar">
                            <div class="w-32 rounded">
                                <img src="https://placehold.co/1200x675/png?text=TWITTER+IMAGE" alt="Twitter Image" />
                            </div>
                        </div>
                        <div class="flex-1">
                            <input type="file" class="file-input file-input-bordered w-full max-w-xs" accept="image/*" />
                            <label class="label">
                                <span class="label-text-alt text-base-content/60">Recommended size: 1200x675px (PNG or JPG)</span>
                            </label>
                            <input type="url" placeholder="Or enter image URL" class="input input-bordered w-full mt-2" value="https://www.minimoda.com/twitter-card.jpg" />
                        </div>
                    </div>
                </div>

                <!-- Twitter Site & Creator -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Twitter Site Handle</span>
                        </label>
                        <label class="input input-bordered flex items-center gap-2">
                            <span class="text-base-content/60">@</span>
                            <input type="text" placeholder="minimoda" class="grow" value="minimoda" />
                        </label>
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Your store's Twitter username</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Twitter Creator Handle</span>
                        </label>
                        <label class="input input-bordered flex items-center gap-2">
                            <span class="text-base-content/60">@</span>
                            <input type="text" placeholder="minimoda" class="grow" value="minimoda" />
                        </label>
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Content creator's Twitter username</span>
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-4"></span>
                        Save Twitter Card Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Structured Data (Schema.org) -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Structured Data (Schema.org)</h2>
            <p class="text-sm text-base-content/70 mb-4">Add structured data markup to help search engines understand your content</p>

            <form class="space-y-6">
                <!-- Organization Schema -->
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" class="toggle toggle-primary" checked />
                        <div>
                            <span class="label-text font-medium">Organization Schema</span>
                            <p class="text-xs text-base-content/60">Include organization information in structured data</p>
                        </div>
                    </label>
                </div>

                <!-- Product Schema -->
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" class="toggle toggle-primary" checked />
                        <div>
                            <span class="label-text font-medium">Product Schema</span>
                            <p class="text-xs text-base-content/60">Add product schema to product pages</p>
                        </div>
                    </label>
                </div>

                <!-- Breadcrumb Schema -->
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" class="toggle toggle-primary" checked />
                        <div>
                            <span class="label-text font-medium">Breadcrumb Schema</span>
                            <p class="text-xs text-base-content/60">Add breadcrumb navigation schema</p>
                        </div>
                    </label>
                </div>

                <!-- WebSite Schema -->
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" class="toggle toggle-primary" checked />
                        <div>
                            <span class="label-text font-medium">WebSite Schema</span>
                            <p class="text-xs text-base-content/60">Add website schema with search functionality</p>
                        </div>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-4"></span>
                        Save Structured Data Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Robots & Sitemap -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Robots & Sitemap</h2>
            <p class="text-sm text-base-content/70 mb-4">Configure robots.txt and sitemap settings</p>

            <form class="space-y-6">
                <!-- Robots.txt -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Robots.txt Content</span>
                    </label>
                    <textarea id="description" class="textarea w-full h-64" placeholder="Enter robots.txt content">User-agent: *
Disallow: /admin/
Disallow: /cart/
Allow: /

Sitemap: https://www.minimoda.com/sitemap.xml</textarea>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Rules for search engine crawlers</span>
                    </label>
                </div>

                <!-- Auto-generate Sitemap -->
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" class="toggle toggle-primary" checked />
                        <div>
                            <span class="label-text font-medium">Auto-generate XML Sitemap</span>
                            <p class="text-xs text-base-content/60">Automatically generate and update sitemap.xml</p>
                        </div>
                    </label>
                </div>

                <!-- Sitemap URL -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Sitemap URL</span>
                    </label>
                    <div class="flex gap-2">
                        <input type="url" placeholder="https://www.minimoda.com/sitemap.xml" class="input input-bordered flex-1" value="https://www.minimoda.com/sitemap.xml" readonly />
                        <button type="button" class="btn btn-outline">
                            <span class="iconify lucide--external-link size-4"></span>
                            View
                        </button>
                        <button type="button" class="btn btn-outline">
                            <span class="iconify lucide--refresh-cw size-4"></span>
                            Regenerate
                        </button>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-4"></span>
                        Save Robots & Sitemap Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('customjs')
<script>
    // Character counter for title and description
    document.addEventListener('DOMContentLoaded', function() {
        // Add character counting logic here
    });
</script>
@endsection