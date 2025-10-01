@extends('layouts.app')

@section('title', 'Add Permission')
@section('page_title', 'Access Control')
@section('page_subtitle', 'Permission Management')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Add New Permission</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li><a href="{{ route('permission.index') }}">Permission</a></li>
            <li class="opacity-80">Add New</li>
        </ul>
    </div>
</div>

<form id="createPermissionForm" class="mt-6 space-y-6">
    @csrf

    <!-- Basic Information -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Basic Information</h2>
            <p class="text-sm text-base-content/70 mb-4">Define permission details</p>

            <div class="space-y-6">
                <!-- Name & Display Name -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Name <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="name" class="input input-bordered w-full" required placeholder="e.g., users.create">
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Unique identifier (lowercase, dot notation)</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Display Name <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="display_name" class="input input-bordered w-full" required placeholder="e.g., Create Users">
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Human-readable name</span>
                        </label>
                    </div>
                </div>

                <!-- Permission Group -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Permission Group</span>
                    </label>
                    <select name="permission_group_id" class="select select-bordered w-full">
                        <option value="">None</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Group for better organization</span>
                    </label>
                </div>

                <!-- Description -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Description</span>
                    </label>
                    <textarea name="description" class="textarea textarea-bordered h-24 w-full" placeholder="Permission description..."></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end gap-2">
        <a href="{{ route('permission.index') }}" class="btn btn-ghost">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <span class="iconify lucide--save size-4"></span>
            Create Permission
        </button>
    </div>
</form>
@endsection

@section('customjs')
@vite(['resources/js/modules/permissions/create.js'])
@endsection
