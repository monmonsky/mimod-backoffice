@extends('layouts.app')

@section('title', 'Add Module')
@section('page_title', 'Access Control')
@section('page_subtitle', 'Module Management')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Add New Module</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li><a href="{{ route('modules.index') }}">Module</a></li>
            <li class="opacity-80">Add New</li>
        </ul>
    </div>
</div>

<form id="createModuleForm" class="mt-6 space-y-6">
    @csrf

    <!-- Basic Information -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Basic Information</h2>
            <p class="text-sm text-base-content/70 mb-4">Define module name and display settings</p>

            <div class="space-y-6">
                <!-- Name & Display Name -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Name <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="name" class="input input-bordered w-full" required placeholder="e.g., users, products">
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Unique identifier (lowercase, no spaces)</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Display Name <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="display_name" class="input input-bordered w-full" required placeholder="e.g., User Management">
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Name shown in sidebar</span>
                        </label>
                    </div>
                </div>

                <!-- Icon & Route -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Icon</span>
                        </label>
                        <input type="text" name="icon" class="input input-bordered w-full" placeholder="e.g., lucide--users">
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Iconify icon name</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Route</span>
                        </label>
                        <input type="text" name="route" class="input input-bordered w-full" placeholder="e.g., user.index">
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Laravel route name</span>
                        </label>
                    </div>
                </div>

                <!-- Description -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Description</span>
                    </label>
                    <textarea name="description" class="textarea textarea-bordered h-24 w-full" placeholder="Module description..."></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Module Structure -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Module Structure</h2>
            <p class="text-sm text-base-content/70 mb-4">Configure module hierarchy and ordering</p>

            <div class="space-y-6">
                <!-- Parent Module & Component -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Parent Module</span>
                        </label>
                        <select name="parent_id" class="select select-bordered w-full">
                            <option value="">None (Root Level)</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->display_name }}</option>
                            @endforeach
                        </select>
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Select parent for submenu</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Component</span>
                        </label>
                        <input type="text" name="component" class="input input-bordered w-full" placeholder="e.g., UserManagement">
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Frontend component name</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Module Status -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Status Settings</h2>
            <p class="text-sm text-base-content/70 mb-4">Control module visibility and activation</p>

            <div class="space-y-4">
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" name="is_active" class="toggle toggle-primary" checked>
                        <div>
                            <span class="label-text font-medium">Active</span>
                            <p class="text-xs text-base-content/60">Module is active and functional</p>
                        </div>
                    </label>
                </div>

                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" name="is_visible" class="toggle toggle-primary" checked>
                        <div>
                            <span class="label-text font-medium">Visible in Sidebar</span>
                            <p class="text-xs text-base-content/60">Show module in navigation menu</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end gap-2">
        <a href="{{ route('modules.index') }}" class="btn btn-ghost">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <span class="iconify lucide--save size-4"></span>
            Create Module
        </button>
    </div>
</form>
@endsection

@section('customjs')
@vite(['resources/js/modules/access-control/modules/create.js'])
@endsection
