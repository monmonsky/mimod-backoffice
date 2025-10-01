@extends('layouts.app')

@section('title', 'Permission Management')
@section('page_title', 'Permission Management')
@section('page_subtitle', 'Manage system permissions')

@section('content')
<div class="container mx-auto p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold">Permission List</h2>
            <p class="text-base-content/60 text-sm mt-1">Manage all system permissions</p>
        </div>
        <div class="flex gap-2">
            @if(hasPermission('access-control.permissions.view'))
            <a href="{{ route('permission-group.index') }}" class="btn btn-secondary">
                <span class="iconify lucide--layers size-5"></span>
                Permission Groups
            </a>
            @endif

            @if(hasPermission('access-control.permissions.create'))
            <a href="{{ route('permission.create') }}" class="btn btn-primary">
                <span class="iconify lucide--plus size-5"></span>
                Add Permission
            </a>
            @endif
        </div>
    </div>

    <!-- Permissions Table -->
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
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
</div>
@endsection

@section('customjs')
@vite(['resources/js/modules/permissions/index.js'])
@endsection
