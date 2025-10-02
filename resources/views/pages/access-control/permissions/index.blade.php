@extends('layouts.app')

@section('title', 'Permissions')
@section('page_title', 'Permission')
@section('page_subtitle', 'Permission Management')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Permission Management</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Access Control</li>
            <li class="opacity-80">Permissions</li>
        </ul>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-3">
    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Total Permissions</p>
                    <p class="text-2xl font-semibold mt-1">{{ $statistics['total'] }}</p>
                    <p class="text-xs text-base-content/60 mt-1">All system permissions</p>
                </div>
                <div class="bg-primary/10 p-3 rounded-lg">
                    <span class="iconify lucide--shield-check size-5 text-primary"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">In Groups</p>
                    <p class="text-2xl font-semibold mt-1 text-info">{{ $statistics['grouped'] }}</p>
                    <p class="text-xs text-base-content/60 mt-1">Organized permissions</p>
                </div>
                <div class="bg-info/10 p-3 rounded-lg">
                    <span class="iconify lucide--layers size-5 text-info"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Ungrouped</p>
                    <p class="text-2xl font-semibold mt-1 text-warning">{{ $statistics['ungrouped'] }}</p>
                    <p class="text-xs text-base-content/60 mt-1">Need categorization</p>
                </div>
                <div class="bg-warning/10 p-3 rounded-lg">
                    <span class="iconify lucide--alert-circle size-5 text-warning"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Permissions Table -->
<div class="card bg-base-100 shadow mt-6">
    <div class="card-body">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="card-title">Permission List</h3>
            <div class="flex gap-2">
                @if(hasPermission('access-control.permissions.view'))
                <a href="{{ route('permission-group.index') }}" class="btn btn-secondary btn-sm">
                    <span class="iconify lucide--layers size-4"></span>
                    Groups
                </a>
                @endif

                @if(hasPermission('access-control.permissions.create'))
                <a href="{{ route('permission.create') }}" class="btn btn-primary btn-sm">
                    <span class="iconify lucide--plus size-4"></span>
                    Add Permission
                </a>
                @endif
            </div>
        </div>
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Display Name</th>
                            <th>Group</th>
                            <th>Description</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($permissions as $permission)
                            <tr>
                                <td><code class="text-xs">{{ $permission->name }}</code></td>
                                <td>{{ $permission->display_name }}</td>
                                <td>
                                    @if($permission->permissionGroup)
                                        <span class="badge badge-ghost">{{ $permission->permissionGroup->name }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $permission->description ?: '-' }}</td>
                                <td class="text-right">
                                    <div class="flex justify-end gap-2">
                                        @if(hasPermission('access-control.permissions.update'))
                                        <a href="{{ route('permission.edit', $permission->id) }}" class="btn btn-sm btn-ghost">
                                            <span class="iconify lucide--pencil size-4"></span>
                                        </a>
                                        @endif

                                        @if(hasPermission('access-control.permissions.delete'))
                                        <form action="{{ route('permission.destroy', $permission->id) }}" method="POST" class="inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-ghost text-error">
                                                <span class="iconify lucide--trash-2 size-4"></span>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-8">
                                    <div class="text-base-content/60">
                                        <span class="iconify lucide--inbox size-12 mx-auto mb-2"></span>
                                        <p>No permissions found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('customjs')
@vite(['resources/js/modules/access-control/permissions/index.js'])
@endsection
