@extends('layouts.app')

@section('title', 'Edit Role')
@section('page_title', 'Access Control')
@section('page_subtitle', 'Role Management')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Edit Role</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li><a href="{{ route('role.index') }}">Role</a></li>
            <li class="opacity-80">Edit</li>
        </ul>
    </div>
</div>

<form id="editRoleForm" class="mt-6 space-y-6" data-id="{{ $role->id }}">
    @csrf
    @method('PUT')

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Basic Information</h2>
            <p class="text-sm text-base-content/70 mb-4">Update role details</p>

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Name <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="name" class="input input-bordered w-full" required value="{{ $role->name }}" {{ $role->is_system ? 'readonly' : '' }}>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Display Name <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="display_name" class="input input-bordered w-full" required value="{{ $role->display_name }}">
                    </div>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Description</span>
                    </label>
                    <textarea name="description" class="textarea textarea-bordered h-24 w-full">{{ $role->description }}</textarea>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Priority <span class="text-error">*</span></span>
                    </label>
                    <input type="number" name="priority" class="input input-bordered w-full" required min="0" max="100" value="{{ $role->priority }}">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="is_active" class="checkbox" {{ $role->is_active ? 'checked' : '' }} {{ $role->is_system ? 'disabled' : '' }} />
                            <div>
                                <span class="label-text font-medium">Active</span>
                            </div>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="is_system" class="checkbox" {{ $role->is_system ? 'checked' : '' }} disabled />
                            <div>
                                <span class="label-text font-medium">System Role</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Permission Access -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Permission Access</h2>
            <p class="text-sm text-base-content/70 mb-4">Assign granular permissions</p>

            @php
                $assignedPermissionIds = collect($assignedPermissions)->pluck('id')->toArray();
            @endphp

            @if($permissionGroups && count($permissionGroups) > 0)
                <div class="space-y-6">
                    @foreach($permissionGroups as $group)
                        <div class="border border-base-300 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h3 class="font-semibold text-base">{{ $group->display_name }}</h3>
                                    @if($group->description)
                                        <p class="text-xs text-base-content/60">{{ $group->description }}</p>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-xs btn-ghost select-all-group" data-group-id="{{ $group->id }}">
                                    Select All
                                </button>
                            </div>

                            @php
                                // Group permissions by module hierarchy
                                $groupedPermissions = collect($permissions)
                                    ->filter(function($permission) use ($group, $permissionGroupItems) {
                                        return isset($permissionGroupItems[$group->id]) && in_array($permission->id, $permissionGroupItems[$group->id]);
                                    });

                                // Build hierarchy structure
                                $hierarchy = [];
                                foreach ($groupedPermissions as $permission) {
                                    $parts = explode('.', $permission->module);

                                    // Handle single-level modules (e.g., dashboard)
                                    if (count($parts) === 1) {
                                        $parent = ucwords(str_replace(['-', '_'], ' ', $parts[0]));
                                        $child = 'Main'; // Default child for single-level

                                        if (!isset($hierarchy[$parent])) {
                                            $hierarchy[$parent] = [];
                                        }
                                        if (!isset($hierarchy[$parent][$child])) {
                                            $hierarchy[$parent][$child] = [];
                                        }
                                        $hierarchy[$parent][$child][] = $permission;
                                    }
                                    // Handle multi-level modules
                                    elseif (count($parts) >= 2) {
                                        $parent = ucwords(str_replace(['-', '_'], ' ', $parts[0]));
                                        $child = isset($parts[1]) ? ucwords(str_replace(['-', '_'], ' ', $parts[1])) : 'Other';
                                        $grandchild = isset($parts[2]) ? ucwords(str_replace(['-', '_'], ' ', $parts[2])) : null;

                                        if (!isset($hierarchy[$parent])) {
                                            $hierarchy[$parent] = [];
                                        }
                                        if (!isset($hierarchy[$parent][$child])) {
                                            $hierarchy[$parent][$child] = [];
                                        }
                                        if ($grandchild) {
                                            if (!isset($hierarchy[$parent][$child][$grandchild])) {
                                                $hierarchy[$parent][$child][$grandchild] = [];
                                            }
                                            $hierarchy[$parent][$child][$grandchild][] = $permission;
                                        } else {
                                            $hierarchy[$parent][$child][] = $permission;
                                        }
                                    }
                                }
                            @endphp

                            <div class="space-y-4">
                                @foreach($hierarchy as $parentName => $children)
                                    {{-- Parent Header --}}
                                    <div class="font-semibold mb-2">{{ $parentName }}</div>

                                    @foreach($children as $childName => $grandchildren)
                                        {{-- Child Card --}}
                                        <div class="border border-base-300 rounded-lg p-3 mb-3">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="font-medium text-sm">{{ $childName }}</div>
                                                <button type="button" class="btn btn-xs btn-ghost select-all-child"
                                                        data-parent="{{ $parentName }}"
                                                        data-child="{{ $childName }}"
                                                        data-group-id="{{ $group->id }}">
                                                    Select All
                                                </button>
                                            </div>

                                            @if(isset($grandchildren[0]) && is_object($grandchildren[0]))
                                                {{-- No grandchildren, show permissions directly --}}
                                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">
                                                    @foreach($grandchildren as $permission)
                                                        <label class="label cursor-pointer justify-start gap-2 p-1 hover:bg-base-200 rounded">
                                                            <input type="checkbox"
                                                                   name="permissions[]"
                                                                   value="{{ $permission->id }}"
                                                                   class="checkbox checkbox-sm permission-checkbox"
                                                                   data-group-id="{{ $group->id }}"
                                                                   data-parent="{{ $parentName }}"
                                                                   data-child="{{ $childName }}"
                                                                   data-module="{{ $permission->module }}"
                                                                   {{ in_array($permission->id, $assignedPermissionIds) ? 'checked' : '' }}>
                                                            <span class="label-text text-sm">{{ $permission->display_name }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @else
                                                {{-- Has grandchildren --}}
                                                <div class="space-y-2">
                                                    @foreach($grandchildren as $grandchildName => $grandchildPerms)
                                                        <div class="border border-base-300 rounded p-2">
                                                            <div class="text-sm font-medium mb-2">{{ $grandchildName }}</div>
                                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-1">
                                                                @foreach($grandchildPerms as $permission)
                                                                    <label class="label cursor-pointer justify-start gap-2 p-1 hover:bg-base-200 rounded">
                                                                        <input type="checkbox"
                                                                               name="permissions[]"
                                                                               value="{{ $permission->id }}"
                                                                               class="checkbox checkbox-sm permission-checkbox"
                                                                               data-group-id="{{ $group->id }}"
                                                                               data-parent="{{ $parentName }}"
                                                                               data-child="{{ $childName }}"
                                                                               data-module="{{ $permission->module }}"
                                                                               {{ in_array($permission->id, $assignedPermissionIds) ? 'checked' : '' }}>
                                                                        <span class="label-text text-sm">{{ $permission->display_name }}</span>
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert">
                    <span class="iconify lucide--info size-5"></span>
                    <span>No permission groups available</span>
                </div>
            @endif
        </div>
    </div>

    <div class="flex justify-end gap-2">
        <a href="{{ route('role.index') }}" class="btn btn-ghost">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <span class="iconify lucide--save size-4"></span>
            Update Role
        </button>
    </div>
</form>
@endsection

@section('customjs')
@vite(['resources/js/modules/access-control/roles/edit.js'])
@endsection
