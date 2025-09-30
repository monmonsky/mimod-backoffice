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

<form id="seoMetaForm" action="{{ route('settings.seo-meta.update') }}" method="POST" class="mt-6 space-y-6">
    @csrf

    <!-- Basic SEO Settings -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Basic SEO Settings</h2>
            <p class="text-sm text-base-content/70 mb-4">Configure meta tags and SEO information for your store</p>

            <div class="space-y-6">
                <!-- Site Title -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Site Title <span class="text-error">*</span></span>
                    </label>
                    <input type="text" name="site_title" placeholder="Enter site title" class="input input-bordered w-full" value="{{ $seoBasic['site_title'] ?? 'Minimoda - Fashion for Little Stars' }}" required />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">This appears in browser tabs and search results</span>
                    </label>
                </div>

                <!-- Meta Description -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Meta Description <span class="text-error">*</span></span>
                    </label>
                    <textarea name="meta_description" class="textarea textarea-bordered w-full h-24" placeholder="Enter meta description" required>{{ $seoBasic['meta_description'] ?? 'Premium children fashion e-commerce platform in Indonesia' }}</textarea>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Recommended: 150-160 characters</span>
                    </label>
                </div>

                <!-- Meta Keywords -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Meta Keywords</span>
                    </label>
                    <input type="text" name="meta_keywords" placeholder="Enter keywords separated by commas" class="input input-bordered w-full" value="{{ $seoBasic['meta_keywords'] ?? 'kids fashion, children clothing, baby clothes, fashion anak' }}" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Separate keywords with commas</span>
                    </label>
                </div>

                <!-- Analytics & Tracking -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Google Analytics ID</span>
                        </label>
                        <input type="text" name="google_analytics_id" placeholder="G-XXXXXXXXXX" class="input input-bordered w-full" value="{{ $seoBasic['google_analytics_id'] ?? '' }}" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Google Search Console</span>
                        </label>
                        <input type="text" name="google_search_console" placeholder="Verification code" class="input input-bordered w-full" value="{{ $seoBasic['google_search_console'] ?? '' }}" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Facebook Pixel ID</span>
                        </label>
                        <input type="text" name="facebook_pixel_id" placeholder="Facebook Pixel ID" class="input input-bordered w-full" value="{{ $seoBasic['facebook_pixel_id'] ?? '' }}" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Open Graph Settings -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Open Graph (Facebook)</h2>
            <p class="text-sm text-base-content/70 mb-4">Control how your content appears when shared on Facebook</p>

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">OG Title</span>
                        </label>
                        <input type="text" name="og_title" placeholder="Enter Open Graph title" class="input input-bordered w-full" value="{{ $seoOpengraph['og_title'] ?? 'Minimoda - Fashion for Little Stars' }}" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">OG Type</span>
                        </label>
                        <select name="og_type" class="select select-bordered w-full">
                            <option value="website" {{ ($seoOpengraph['og_type'] ?? 'website') == 'website' ? 'selected' : '' }}>Website</option>
                            <option value="article" {{ ($seoOpengraph['og_type'] ?? '') == 'article' ? 'selected' : '' }}>Article</option>
                            <option value="product" {{ ($seoOpengraph['og_type'] ?? '') == 'product' ? 'selected' : '' }}>Product</option>
                        </select>
                    </div>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">OG Description</span>
                    </label>
                    <textarea name="og_description" class="textarea textarea-bordered w-full" placeholder="Enter Open Graph description">{{ $seoOpengraph['og_description'] ?? 'Premium children fashion e-commerce' }}</textarea>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">OG Image URL</span>
                    </label>
                    <input type="url" name="og_image" placeholder="https://example.com/og-image.jpg" class="input input-bordered w-full" value="{{ $seoOpengraph['og_image'] ?? '' }}" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Recommended: 1200x630px</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Twitter Card Settings -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Twitter Card</h2>
            <p class="text-sm text-base-content/70 mb-4">Customize how your content appears on Twitter/X</p>

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Card Type</span>
                        </label>
                        <select name="twitter_card" class="select select-bordered w-full">
                            <option value="summary_large_image" {{ ($seoTwitter['twitter_card'] ?? 'summary_large_image') == 'summary_large_image' ? 'selected' : '' }}>Summary Large Image</option>
                            <option value="summary" {{ ($seoTwitter['twitter_card'] ?? '') == 'summary' ? 'selected' : '' }}>Summary</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Twitter Site</span>
                        </label>
                        <input type="text" name="twitter_site" placeholder="@username" class="input input-bordered w-full" value="{{ $seoTwitter['twitter_site'] ?? '@minimoda' }}" />
                    </div>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Twitter Title</span>
                    </label>
                    <input type="text" name="twitter_title" placeholder="Enter Twitter title" class="input input-bordered w-full" value="{{ $seoTwitter['twitter_title'] ?? 'Minimoda - Fashion for Little Stars' }}" />
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Twitter Description</span>
                    </label>
                    <textarea name="twitter_description" class="textarea textarea-bordered w-full" placeholder="Enter Twitter description">{{ $seoTwitter['twitter_description'] ?? 'Premium children fashion e-commerce' }}</textarea>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Twitter Image URL</span>
                    </label>
                    <input type="url" name="twitter_image" placeholder="https://example.com/twitter-image.jpg" class="input input-bordered w-full" value="{{ $seoTwitter['twitter_image'] ?? '' }}" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Recommended: 1200x675px</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end gap-2">
        <button type="button" class="btn btn-ghost">Cancel</button>
        <button type="submit" class="btn btn-primary">
            <span class="iconify lucide--save size-4"></span>
            Save SEO Settings
        </button>
    </div>
</form>
@endsection

@section('customjs')
<script>
    // Toast Notification Function
    function showToast(message, type = 'success') {
        const existing = document.querySelector('.custom-toast-container');
        if (existing) existing.remove();

        const toastContainer = document.createElement('div');
        toastContainer.className = 'custom-toast-container';
        toastContainer.style.cssText = 'position: fixed; top: 1.5rem; right: 1.5rem; z-index: 99999;';

        const borderColor = type === 'error' ? 'border-error' : 'border-success';
        const textColor = type === 'error' ? 'text-error' : 'text-success';
        const iconClass = type === 'error' ? 'lucide--circle-x' : 'lucide--circle-check';

        toastContainer.innerHTML = `
            <div class="bg-base-100 border-l-4 ${borderColor} rounded shadow-md flex items-center gap-3 px-4 py-3 min-w-[300px] max-w-md">
                <span class="iconify ${iconClass} size-5 ${textColor} flex-shrink-0"></span>
                <span class="text-sm text-base-content flex-1">${message}</span>
                <button type="button" class="btn btn-ghost btn-xs btn-square opacity-60 hover:opacity-100" onclick="this.closest('.custom-toast-container').remove()">
                    <span class="iconify lucide--x size-4"></span>
                </button>
            </div>
        `;

        document.body.appendChild(toastContainer);

        requestAnimationFrame(() => {
            toastContainer.style.animation = 'slideIn 0.2s ease-out';
        });

        setTimeout(() => {
            if (toastContainer.parentElement) {
                toastContainer.style.opacity = '0';
                toastContainer.style.transition = 'opacity 0.2s ease-out';
                setTimeout(() => toastContainer.remove(), 200);
            }
        }, 4000);
    }

    // Add CSS animation
    if (!document.querySelector('#toast-animations')) {
        const style = document.createElement('style');
        style.id = 'toast-animations';
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(20px); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    }

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        const form = document.getElementById('seoMetaForm');

        if (!form) {
            console.error('Form #seoMetaForm not found!');
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
                    showToast(data.message || 'SEO settings saved successfully!', 'success');
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