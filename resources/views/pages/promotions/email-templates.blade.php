@extends('layouts.app')

@section('title', 'Email Templates')
@section('page_title', 'Promotions')
@section('page_subtitle', 'Email Templates')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Email Templates</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Promotions</li>
            <li class="opacity-80">Email Templates</li>
        </ul>
    </div>
</div>

<!-- Statistics Cards -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Total Templates</p>
                    <h3 class="text-2xl font-bold mt-1">18</h3>
                    <p class="text-xs text-info mt-2">All templates</p>
                </div>
                <div class="bg-info/10 rounded-full p-3">
                    <span class="iconify lucide--layout-template size-6 text-info"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Active Templates</p>
                    <h3 class="text-2xl font-bold mt-1">15</h3>
                    <p class="text-xs text-success mt-2">Ready to use</p>
                </div>
                <div class="bg-success/10 rounded-full p-3">
                    <span class="iconify lucide--check-circle size-6 text-success"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Custom Templates</p>
                    <h3 class="text-2xl font-bold mt-1">12</h3>
                    <p class="text-xs text-primary mt-2">User created</p>
                </div>
                <div class="bg-primary/10 rounded-full p-3">
                    <span class="iconify lucide--sparkles size-6 text-primary"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Times Used</p>
                    <h3 class="text-2xl font-bold mt-1">342</h3>
                    <p class="text-xs text-warning mt-2">This month</p>
                </div>
                <div class="bg-warning/10 rounded-full p-3">
                    <span class="iconify lucide--trending-up size-6 text-warning"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters & Actions -->
<div class="mt-6 bg-base-100 card shadow">
    <div class="card-body">
        <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                <div class="form-control">
                    <input type="text" placeholder="Search templates..." class="input input-bordered w-full sm:w-64" />
                </div>
                <select class="select select-bordered w-full sm:w-auto">
                    <option selected>All Categories</option>
                    <option>Promotional</option>
                    <option>Newsletter</option>
                    <option>Transactional</option>
                    <option>Welcome</option>
                    <option>Abandoned Cart</option>
                </select>
                <select class="select select-bordered w-full sm:w-auto">
                    <option selected>All Status</option>
                    <option>Active</option>
                    <option>Draft</option>
                    <option>Archived</option>
                </select>
            </div>
            <a href="{{ route('promotions.email-templates.create') }}" class="btn btn-primary w-full lg:w-auto">
                <span class="iconify lucide--plus size-4"></span>
                Create Template
            </a>
        </div>
    </div>
</div>

<!-- Template Gallery -->
<div class="mt-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-medium">Template Gallery</h3>
        <div class="btn-group">
            <button class="btn btn-sm btn-active">Grid</button>
            <button class="btn btn-sm">List</button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Template Card 1 -->
        <div class="bg-base-100 card shadow hover:shadow-lg transition-shadow">
            <figure class="px-4 pt-4">
                <div class="bg-gradient-to-br from-primary to-secondary rounded-lg w-full h-48 flex items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-white/10 backdrop-blur-sm p-4">
                        <div class="bg-white rounded p-3 mb-2">
                            <div class="h-2 bg-primary/20 rounded w-3/4 mb-2"></div>
                            <div class="h-2 bg-primary/10 rounded w-1/2"></div>
                        </div>
                        <div class="bg-white rounded p-3 mb-2">
                            <div class="h-16 bg-primary/30 rounded mb-2"></div>
                            <div class="h-2 bg-primary/10 rounded w-full mb-1"></div>
                            <div class="h-2 bg-primary/10 rounded w-4/5"></div>
                        </div>
                        <div class="bg-primary rounded p-2 text-center">
                            <div class="h-2 bg-white/50 rounded w-20 mx-auto"></div>
                        </div>
                    </div>
                </div>
            </figure>
            <div class="card-body">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="card-title text-base">Flash Sale Promo</h3>
                        <p class="text-sm text-base-content/60">Promotional template</p>
                    </div>
                    <span class="badge badge-success badge-sm">Active</span>
                </div>

                <div class="flex gap-2 mt-3">
                    <span class="badge badge-outline badge-sm">Responsive</span>
                    <span class="badge badge-outline badge-sm">Modern</span>
                </div>

                <div class="flex items-center justify-between text-sm mt-3 pt-3 border-t border-base-300">
                    <div class="flex items-center gap-1 text-base-content/60">
                        <span class="iconify lucide--mail size-4"></span>
                        <span>Used 45 times</span>
                    </div>
                    <div class="text-base-content/60">Last: 2h ago</div>
                </div>

                <div class="card-actions justify-end mt-3">
                    <button class="btn btn-sm btn-ghost">
                        <span class="iconify lucide--eye size-4"></span>
                        Preview
                    </button>
                    <a href="{{ route('promotions.email-templates.edit', 1) }}" class="btn btn-sm btn-primary">
                        <span class="iconify lucide--pencil size-4"></span>
                        Edit
                    </a>
                    <div class="dropdown dropdown-end">
                        <button tabindex="0" class="btn btn-sm btn-ghost btn-square">
                            <span class="iconify lucide--more-vertical size-4"></span>
                        </button>
                        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-40 p-2 shadow">
                            <li><a><span class="iconify lucide--copy size-4"></span> Duplicate</a></li>
                            <li><a><span class="iconify lucide--send size-4"></span> Test Send</a></li>
                            <li><a class="text-error"><span class="iconify lucide--trash-2 size-4"></span> Delete</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Template Card 2 -->
        <div class="bg-base-100 card shadow hover:shadow-lg transition-shadow">
            <figure class="px-4 pt-4">
                <div class="bg-gradient-to-br from-success to-info rounded-lg w-full h-48 flex items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-white/10 backdrop-blur-sm p-4">
                        <div class="bg-white rounded-t p-3 mb-2">
                            <div class="h-12 bg-success/30 rounded mb-2"></div>
                        </div>
                        <div class="bg-white p-3 mb-2">
                            <div class="h-2 bg-success/20 rounded w-3/4 mb-2"></div>
                            <div class="h-2 bg-success/10 rounded w-full mb-1"></div>
                            <div class="h-2 bg-success/10 rounded w-4/5 mb-2"></div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="h-12 bg-success/20 rounded"></div>
                                <div class="h-12 bg-success/20 rounded"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </figure>
            <div class="card-body">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="card-title text-base">Product Launch</h3>
                        <p class="text-sm text-base-content/60">Newsletter template</p>
                    </div>
                    <span class="badge badge-success badge-sm">Active</span>
                </div>

                <div class="flex gap-2 mt-3">
                    <span class="badge badge-outline badge-sm">Clean</span>
                    <span class="badge badge-outline badge-sm">Professional</span>
                </div>

                <div class="flex items-center justify-between text-sm mt-3 pt-3 border-t border-base-300">
                    <div class="flex items-center gap-1 text-base-content/60">
                        <span class="iconify lucide--mail size-4"></span>
                        <span>Used 32 times</span>
                    </div>
                    <div class="text-base-content/60">Last: 1d ago</div>
                </div>

                <div class="card-actions justify-end mt-3">
                    <button class="btn btn-sm btn-ghost">
                        <span class="iconify lucide--eye size-4"></span>
                        Preview
                    </button>
                    <a href="{{ route('promotions.email-templates.edit', 1) }}" class="btn btn-sm btn-primary">
                        <span class="iconify lucide--pencil size-4"></span>
                        Edit
                    </a>
                    <div class="dropdown dropdown-end">
                        <button tabindex="0" class="btn btn-sm btn-ghost btn-square">
                            <span class="iconify lucide--more-vertical size-4"></span>
                        </button>
                        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-40 p-2 shadow">
                            <li><a><span class="iconify lucide--copy size-4"></span> Duplicate</a></li>
                            <li><a><span class="iconify lucide--send size-4"></span> Test Send</a></li>
                            <li><a class="text-error"><span class="iconify lucide--trash-2 size-4"></span> Delete</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Template Card 3 -->
        <div class="bg-base-100 card shadow hover:shadow-lg transition-shadow">
            <figure class="px-4 pt-4">
                <div class="bg-gradient-to-br from-warning to-error rounded-lg w-full h-48 flex items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-white/10 backdrop-blur-sm p-4">
                        <div class="bg-white rounded p-3 mb-2">
                            <div class="flex justify-between items-center mb-2">
                                <div class="h-8 w-8 bg-warning/30 rounded"></div>
                                <div class="h-2 bg-warning/20 rounded w-20"></div>
                            </div>
                        </div>
                        <div class="bg-white rounded p-3 mb-2">
                            <div class="h-3 bg-warning/30 rounded w-2/3 mb-2"></div>
                            <div class="h-2 bg-warning/10 rounded w-full mb-1"></div>
                            <div class="h-2 bg-warning/10 rounded w-4/5 mb-3"></div>
                            <div class="bg-warning/20 rounded p-2">
                                <div class="h-2 bg-warning/30 rounded w-1/2 mx-auto"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </figure>
            <div class="card-body">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="card-title text-base">Abandoned Cart</h3>
                        <p class="text-sm text-base-content/60">Transactional template</p>
                    </div>
                    <span class="badge badge-success badge-sm">Active</span>
                </div>

                <div class="flex gap-2 mt-3">
                    <span class="badge badge-outline badge-sm">Automated</span>
                    <span class="badge badge-outline badge-sm">High CTR</span>
                </div>

                <div class="flex items-center justify-between text-sm mt-3 pt-3 border-t border-base-300">
                    <div class="flex items-center gap-1 text-base-content/60">
                        <span class="iconify lucide--mail size-4"></span>
                        <span>Used 128 times</span>
                    </div>
                    <div class="text-base-content/60">Last: 30m ago</div>
                </div>

                <div class="card-actions justify-end mt-3">
                    <button class="btn btn-sm btn-ghost">
                        <span class="iconify lucide--eye size-4"></span>
                        Preview
                    </button>
                    <a href="{{ route('promotions.email-templates.edit', 1) }}" class="btn btn-sm btn-primary">
                        <span class="iconify lucide--pencil size-4"></span>
                        Edit
                    </a>
                    <div class="dropdown dropdown-end">
                        <button tabindex="0" class="btn btn-sm btn-ghost btn-square">
                            <span class="iconify lucide--more-vertical size-4"></span>
                        </button>
                        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-40 p-2 shadow">
                            <li><a><span class="iconify lucide--copy size-4"></span> Duplicate</a></li>
                            <li><a><span class="iconify lucide--send size-4"></span> Test Send</a></li>
                            <li><a class="text-error"><span class="iconify lucide--trash-2 size-4"></span> Delete</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Template Card 4 -->
        <div class="bg-base-100 card shadow hover:shadow-lg transition-shadow">
            <figure class="px-4 pt-4">
                <div class="bg-gradient-to-br from-info to-primary rounded-lg w-full h-48 flex items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-white/10 backdrop-blur-sm p-4">
                        <div class="bg-white rounded p-3 mb-2">
                            <div class="h-3 bg-info/30 rounded w-1/2 mb-2"></div>
                            <div class="h-2 bg-info/10 rounded w-3/4 mb-1"></div>
                            <div class="h-2 bg-info/10 rounded w-2/3"></div>
                        </div>
                        <div class="bg-white rounded p-3">
                            <div class="h-2 bg-info/10 rounded w-full mb-1"></div>
                            <div class="h-2 bg-info/10 rounded w-5/6 mb-3"></div>
                            <div class="bg-info rounded p-2 text-center">
                                <div class="h-2 bg-white/50 rounded w-24 mx-auto"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </figure>
            <div class="card-body">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="card-title text-base">Welcome Email</h3>
                        <p class="text-sm text-base-content/60">Welcome template</p>
                    </div>
                    <span class="badge badge-success badge-sm">Active</span>
                </div>

                <div class="flex gap-2 mt-3">
                    <span class="badge badge-outline badge-sm">Friendly</span>
                    <span class="badge badge-outline badge-sm">Simple</span>
                </div>

                <div class="flex items-center justify-between text-sm mt-3 pt-3 border-t border-base-300">
                    <div class="flex items-center gap-1 text-base-content/60">
                        <span class="iconify lucide--mail size-4"></span>
                        <span>Used 89 times</span>
                    </div>
                    <div class="text-base-content/60">Last: 5h ago</div>
                </div>

                <div class="card-actions justify-end mt-3">
                    <button class="btn btn-sm btn-ghost">
                        <span class="iconify lucide--eye size-4"></span>
                        Preview
                    </button>
                    <a href="{{ route('promotions.email-templates.edit', 1) }}" class="btn btn-sm btn-primary">
                        <span class="iconify lucide--pencil size-4"></span>
                        Edit
                    </a>
                    <div class="dropdown dropdown-end">
                        <button tabindex="0" class="btn btn-sm btn-ghost btn-square">
                            <span class="iconify lucide--more-vertical size-4"></span>
                        </button>
                        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-40 p-2 shadow">
                            <li><a><span class="iconify lucide--copy size-4"></span> Duplicate</a></li>
                            <li><a><span class="iconify lucide--send size-4"></span> Test Send</a></li>
                            <li><a class="text-error"><span class="iconify lucide--trash-2 size-4"></span> Delete</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Template Card 5 (Draft) -->
        <div class="bg-base-100 card shadow hover:shadow-lg transition-shadow opacity-75">
            <figure class="px-4 pt-4">
                <div class="bg-gradient-to-br from-base-300 to-base-200 rounded-lg w-full h-48 flex items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-white/10 backdrop-blur-sm p-4 flex items-center justify-center">
                        <div class="text-center">
                            <span class="iconify lucide--layout-template size-12 text-base-content/30 mb-2"></span>
                            <p class="text-sm text-base-content/60">No preview available</p>
                        </div>
                    </div>
                </div>
            </figure>
            <div class="card-body">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="card-title text-base">Valentine Special</h3>
                        <p class="text-sm text-base-content/60">Promotional template</p>
                    </div>
                    <span class="badge badge-ghost badge-sm">Draft</span>
                </div>

                <div class="flex gap-2 mt-3">
                    <span class="badge badge-outline badge-sm badge-ghost">Incomplete</span>
                </div>

                <div class="flex items-center justify-between text-sm mt-3 pt-3 border-t border-base-300">
                    <div class="flex items-center gap-1 text-base-content/60">
                        <span class="iconify lucide--clock size-4"></span>
                        <span>Not published</span>
                    </div>
                    <div class="text-base-content/60">Created: 2d ago</div>
                </div>

                <div class="card-actions justify-end mt-3">
                    <a href="{{ route('promotions.email-templates.edit', 1) }}" class="btn btn-sm btn-primary w-full">
                        <span class="iconify lucide--pencil size-4"></span>
                        Continue Editing
                    </a>
                </div>
            </div>
        </div>

        <!-- Template Card 6 (Blank) -->
        <div class="bg-base-100 card shadow hover:shadow-lg transition-shadow border-2 border-dashed border-base-300">
            <div class="card-body items-center justify-center min-h-[400px]">
                <span class="iconify lucide--plus-circle size-16 text-primary mb-4"></span>
                <h3 class="card-title text-base">Create New Template</h3>
                <p class="text-sm text-base-content/60 text-center">Start from scratch or use a pre-built layout</p>
                <div class="card-actions justify-center mt-4">
                    <a href="{{ route('promotions.email-templates.create') }}" class="btn btn-primary">
                        <span class="iconify lucide--plus size-4"></span>
                        Create Template
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Template List Table (Alternative View) -->
<div class="mt-6 bg-base-100 card shadow hidden">
    <div class="card-body">
        <div class="flex items-center justify-between mb-4">
            <h3 class="card-title text-base">All Templates</h3>
            <button class="btn btn-ghost btn-sm">
                <span class="iconify lucide--download size-4"></span>
                Export
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Template Name</th>
                        <th>Category</th>
                        <th>Times Used</th>
                        <th>Last Used</th>
                        <th>Created</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="iconify lucide--layout-template text-primary size-4"></span>
                                <span class="font-medium">Flash Sale Promo</span>
                            </div>
                        </td>
                        <td><span class="badge badge-primary badge-sm">Promotional</span></td>
                        <td>45</td>
                        <td>2 hours ago</td>
                        <td>Jan 10, 2024</td>
                        <td><span class="badge badge-success badge-sm">Active</span></td>
                        <td>
                            <div class="flex gap-1">
                                <button class="btn btn-ghost btn-sm btn-square">
                                    <span class="iconify lucide--eye size-4"></span>
                                </button>
                                <button class="btn btn-ghost btn-sm btn-square">
                                    <span class="iconify lucide--pencil size-4"></span>
                                </button>
                                <button class="btn btn-ghost btn-sm btn-square">
                                    <span class="iconify lucide--trash-2 size-4"></span>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection