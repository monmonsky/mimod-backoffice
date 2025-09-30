@extends('layouts.app')

@section('title', 'Edit Email Template')
@section('page_title', 'Promotions')
@section('page_subtitle', 'Edit Email Template')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Edit Email Template</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li><a href="{{ route('promotions.email-templates') }}">Email Templates</a></li>
            <li class="opacity-80">Edit</li>
        </ul>
    </div>
</div>

<!-- Template Form -->
<form action="#" method="POST">
    @csrf
    @method('PUT')

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Template Information -->
            <div class="bg-base-100 card shadow">
                <div class="card-body">
                    <h3 class="card-title text-base mb-4">Template Information</h3>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Template Name <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="template_name" value="Flash Sale Promo" placeholder="e.g., Flash Sale Promo" class="input input-bordered" required />
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Internal name for identification</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Category <span class="text-error">*</span></span>
                        </label>
                        <select name="category" class="select select-bordered" required>
                            <option value="">Select category</option>
                            <option value="promotional" selected>Promotional</option>
                            <option value="newsletter">Newsletter</option>
                            <option value="transactional">Transactional</option>
                            <option value="welcome">Welcome</option>
                            <option value="abandoned_cart">Abandoned Cart</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Description</span>
                        </label>
                        <textarea name="description" rows="2" class="textarea textarea-bordered" placeholder="Brief description of this template...">Email template for flash sale promotions with bright colors and call-to-action buttons.</textarea>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Tags</span>
                        </label>
                        <input type="text" name="tags" value="sale, discount, promotion, flash" placeholder="e.g., sale, discount, promotion" class="input input-bordered" />
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Comma-separated tags for easy searching</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Email Design -->
            <div class="bg-base-100 card shadow">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="card-title text-base">Email Design</h3>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-sm btn-active">Visual Editor</button>
                            <button type="button" class="btn btn-sm">HTML Code</button>
                        </div>
                    </div>

                    <!-- Visual Editor -->
                    <div id="visual-editor">
                        <div class="alert alert-info mb-4">
                            <span class="iconify lucide--info size-4"></span>
                            <span class="text-sm">Drag and drop components to build your email template</span>
                        </div>

                        <!-- Components Palette -->
                        <div class="border border-base-300 rounded-lg p-4 mb-4">
                            <p class="font-medium mb-3">Components</p>
                            <div class="grid grid-cols-4 gap-2">
                                <button type="button" class="btn btn-outline btn-sm">
                                    <span class="iconify lucide--type size-4"></span>
                                    Text
                                </button>
                                <button type="button" class="btn btn-outline btn-sm">
                                    <span class="iconify lucide--image size-4"></span>
                                    Image
                                </button>
                                <button type="button" class="btn btn-outline btn-sm">
                                    <span class="iconify lucide--mouse-pointer-click size-4"></span>
                                    Button
                                </button>
                                <button type="button" class="btn btn-outline btn-sm">
                                    <span class="iconify lucide--columns size-4"></span>
                                    Columns
                                </button>
                                <button type="button" class="btn btn-outline btn-sm">
                                    <span class="iconify lucide--minus size-4"></span>
                                    Divider
                                </button>
                                <button type="button" class="btn btn-outline btn-sm">
                                    <span class="iconify lucide--space size-4"></span>
                                    Spacer
                                </button>
                                <button type="button" class="btn btn-outline btn-sm">
                                    <span class="iconify lucide--shopping-bag size-4"></span>
                                    Product
                                </button>
                                <button type="button" class="btn btn-outline btn-sm">
                                    <span class="iconify lucide--share-2 size-4"></span>
                                    Social
                                </button>
                            </div>
                        </div>

                        <!-- Canvas -->
                        <div class="border-2 border-dashed border-base-300 rounded-lg p-6 bg-base-200 min-h-[400px]">
                            <div class="bg-white rounded-lg shadow-lg mx-auto" style="max-width: 600px;">
                                <!-- Header Example -->
                                <div class="bg-gradient-to-r from-primary to-secondary p-6 text-white text-center relative group">
                                    <h1 class="text-2xl font-bold mb-2">Your Email Title</h1>
                                    <p>Subtitle or tagline goes here</p>
                                    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button type="button" class="btn btn-xs btn-circle">
                                            <span class="iconify lucide--edit size-3"></span>
                                        </button>
                                        <button type="button" class="btn btn-xs btn-circle">
                                            <span class="iconify lucide--trash-2 size-3"></span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Content Example -->
                                <div class="p-6 relative group">
                                    <p class="mb-4">Hi [Customer Name],</p>
                                    <p class="mb-4">This is your email content. You can customize this text and add more components.</p>
                                    <div class="text-center my-6">
                                        <button class="btn btn-primary">Call to Action</button>
                                    </div>
                                    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button type="button" class="btn btn-xs btn-circle">
                                            <span class="iconify lucide--edit size-3"></span>
                                        </button>
                                        <button type="button" class="btn btn-xs btn-circle">
                                            <span class="iconify lucide--trash-2 size-3"></span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Footer Example -->
                                <div class="bg-base-200 p-6 text-center text-sm relative group">
                                    <p class="text-base-content/60">© 2024 Your Company. All rights reserved.</p>
                                    <p class="mt-2">
                                        <a href="#" class="link">Unsubscribe</a> |
                                        <a href="#" class="link">View in browser</a>
                                    </p>
                                    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button type="button" class="btn btn-xs btn-circle">
                                            <span class="iconify lucide--edit size-3"></span>
                                        </button>
                                        <button type="button" class="btn btn-xs btn-circle">
                                            <span class="iconify lucide--trash-2 size-3"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-6">
                                <button type="button" class="btn btn-outline btn-sm">
                                    <span class="iconify lucide--plus size-4"></span>
                                    Add Component
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- HTML Editor (Hidden by default) -->
                    <div id="html-editor" style="display: none;">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">HTML Code</span>
                            </label>
                            <textarea name="html_content" rows="20" class="textarea textarea-bordered font-mono text-sm" placeholder="<html>...</html>"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div class="bg-base-100 card shadow">
                <div class="card-body">
                    <h3 class="card-title text-base mb-4">Template Settings</h3>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Background Color</span>
                        </label>
                        <div class="flex gap-2">
                            <input type="color" name="bg_color" value="#f3f4f6" class="input input-bordered w-20" />
                            <input type="text" name="bg_color_hex" value="#f3f4f6" class="input input-bordered flex-1" />
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Primary Color</span>
                        </label>
                        <div class="flex gap-2">
                            <input type="color" name="primary_color" value="#3b82f6" class="input input-bordered w-20" />
                            <input type="text" name="primary_color_hex" value="#3b82f6" class="input input-bordered flex-1" />
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Font Family</span>
                        </label>
                        <select name="font_family" class="select select-bordered">
                            <option value="arial">Arial</option>
                            <option value="helvetica">Helvetica</option>
                            <option value="georgia">Georgia</option>
                            <option value="times">Times New Roman</option>
                            <option value="verdana">Verdana</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Content Width</span>
                        </label>
                        <select name="content_width" class="select select-bordered">
                            <option value="500">500px (Mobile-first)</option>
                            <option value="600" selected>600px (Standard)</option>
                            <option value="700">700px (Wide)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Preview -->
            <div class="bg-base-100 card shadow">
                <div class="card-body">
                    <h3 class="card-title text-base mb-4">Preview</h3>
                    <div class="btn-group btn-group-sm w-full mb-3">
                        <button type="button" class="btn btn-sm btn-active flex-1">Desktop</button>
                        <button type="button" class="btn btn-sm flex-1">Mobile</button>
                    </div>
                    <div class="border border-base-300 rounded-lg overflow-hidden">
                        <div class="bg-base-200 p-2 text-xs text-center">Live Preview</div>
                        <div class="bg-white p-4 h-96 overflow-y-auto text-xs">
                            <div class="bg-gradient-to-r from-primary to-secondary p-4 text-white text-center rounded mb-2">
                                <p class="font-bold">Your Email Title</p>
                            </div>
                            <div class="mb-2">
                                <p>Hi Customer,</p>
                                <p>Email content preview...</p>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-primary btn-xs">Button</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Variables -->
            <div class="bg-base-100 card shadow">
                <div class="card-body">
                    <h3 class="card-title text-base mb-4">Available Variables</h3>
                    <p class="text-sm text-base-content/60 mb-3">Use these in your template:</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between p-2 bg-base-200 rounded">
                            <code class="text-xs">{customer_name}</code>
                            <button type="button" class="btn btn-xs btn-ghost">
                                <span class="iconify lucide--copy size-3"></span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-base-200 rounded">
                            <code class="text-xs">{customer_email}</code>
                            <button type="button" class="btn btn-xs btn-ghost">
                                <span class="iconify lucide--copy size-3"></span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-base-200 rounded">
                            <code class="text-xs">{store_name}</code>
                            <button type="button" class="btn btn-xs btn-ghost">
                                <span class="iconify lucide--copy size-3"></span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-base-200 rounded">
                            <code class="text-xs">{unsubscribe_link}</code>
                            <button type="button" class="btn btn-xs btn-ghost">
                                <span class="iconify lucide--copy size-3"></span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-base-200 rounded">
                            <code class="text-xs">{current_year}</code>
                            <button type="button" class="btn btn-xs btn-ghost">
                                <span class="iconify lucide--copy size-3"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test Email -->
            <div class="bg-base-100 card shadow">
                <div class="card-body">
                    <h3 class="card-title text-base mb-4">Test Template</h3>
                    <p class="text-sm text-base-content/60 mb-3">Send test email with sample data</p>
                    <div class="form-control">
                        <input type="email" placeholder="your@email.com" class="input input-bordered input-sm" />
                    </div>
                    <button type="button" class="btn btn-outline btn-sm w-full mt-3">
                        <span class="iconify lucide--send size-4"></span>
                        Send Test
                    </button>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-base-100 card shadow">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-full">
                        <span class="iconify lucide--save size-4"></span>
                        Update Template
                    </button>
                    <button type="button" class="btn btn-outline btn-error w-full" onclick="if(confirm('Are you sure you want to delete this template?')) { document.getElementById('delete-form').submit(); }">
                        <span class="iconify lucide--trash-2 size-4"></span>
                        Delete Template
                    </button>
                    <a href="{{ route('promotions.email-templates') }}" class="btn btn-ghost w-full">
                        <span class="iconify lucide--x size-4"></span>
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Delete Form -->
<form id="delete-form" action="#" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection