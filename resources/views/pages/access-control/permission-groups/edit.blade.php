@extends('layouts.app')

@section('title', 'Edit Permission Group')
@section('page_title', 'Access Control')
@section('page_subtitle', 'Permission Group Management')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Edit Permission Group</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li><a href="{{ route('permission-group.index') }}">Permission Groups</a></li>
            <li class="opacity-80">Edit</li>
        </ul>
    </div>
</div>

<form id="editPermissionGroupForm" class="mt-6 space-y-6" data-id="{{ $group->id }}">
    @csrf
    @method('PUT')

    <!-- Basic Information -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Basic Information</h2>
            <p class="text-sm text-base-content/70 mb-4">Update permission group details</p>

            <div class="space-y-6">
                <!-- Name -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Name <span class="text-error">*</span></span>
                    </label>
                    <input type="text" name="name" class="input input-bordered w-full" required value="{{ $group->name }}" placeholder="e.g., User Management">
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Unique group name</span>
                    </label>
                </div>

                <!-- Description -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Description</span>
                    </label>
                    <textarea name="description" class="textarea textarea-bordered h-24 w-full" placeholder="Group description...">{{ $group->description }}</textarea>
                </div>

                <!-- Permissions Count -->
                <div class="alert alert-info">
                    <span class="iconify lucide--info size-5"></span>
                    <span>This group has <strong>{{ $group->permissions_count }}</strong> permission(s) assigned.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end gap-2">
        <a href="{{ route('permission-group.index') }}" class="btn btn-ghost">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <span class="iconify lucide--save size-4"></span>
            Update Group
        </button>
    </div>
</form>
@endsection

@section('customjs')
@vite(['resources/js/modules/permission-groups/edit.js'])
@endsection
