@extends('layouts.app')

@section('title', 'Permission Groups')
@section('page_title', 'Permission Group')
@section('page_subtitle', 'Permission Group Management')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Permission Group Management</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Access Control</li>
            <li class="opacity-80">Permission Groups</li>
        </ul>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-3">
    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Total Groups</p>
                    <p class="text-2xl font-semibold mt-1">{{ $statistics['total'] }}</p>
                    <p class="text-xs text-base-content/60 mt-1">All permission groups</p>
                </div>
                <div class="bg-primary/10 p-3 rounded-lg">
                    <span class="iconify lucide--layers size-5 text-primary"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Total Permissions</p>
                    <p class="text-2xl font-semibold mt-1 text-info">{{ $statistics['total_permissions'] }}</p>
                    <p class="text-xs text-base-content/60 mt-1">In all groups</p>
                </div>
                <div class="bg-info/10 p-3 rounded-lg">
                    <span class="iconify lucide--shield-check size-5 text-info"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Average Size</p>
                    <p class="text-2xl font-semibold mt-1 text-success">{{ number_format($statistics['average_size'], 1) }}</p>
                    <p class="text-xs text-base-content/60 mt-1">Permissions per group</p>
                </div>
                <div class="bg-success/10 p-3 rounded-lg">
                    <span class="iconify lucide--bar-chart size-5 text-success"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Groups Table -->
<div class="card bg-base-100 shadow mt-6">
    <div class="card-body">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="card-title">Permission Group List</h3>
            @if(hasPermission('access-control.permission-groups.create'))
            <a href="{{ route('permission-group.create') }}" class="btn btn-primary btn-sm">
                <span class="iconify lucide--plus size-4"></span>
                Add Group
            </a>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Permissions Count</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($groups as $group)
                        <tr>
                            <td><strong>{{ $group->name }}</strong></td>
                            <td>{{ $group->description ?: '-' }}</td>
                            <td>
                                <span class="badge badge-primary">{{ $group->permissions_count ?? 0 }} permissions</span>
                            </td>
                            <td class="text-right">
                                <div class="flex justify-end gap-2">
                                    @if(hasPermission('access-control.permission-groups.update'))
                                    <a href="{{ route('permission-group.edit', $group->id) }}" class="btn btn-sm btn-ghost">
                                        <span class="iconify lucide--pencil size-4"></span>
                                    </a>
                                    @endif

                                    @if(hasPermission('access-control.permission-groups.delete'))
                                    <form action="{{ route('permission-group.destroy', $group->id) }}" method="POST" class="inline delete-form">
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
                            <td colspan="4" class="text-center py-8">
                                <div class="text-base-content/60">
                                    <span class="iconify lucide--inbox size-12 mx-auto mb-2"></span>
                                    <p>No permission groups found</p>
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
@vite(['resources/js/modules/access-control/permission-groups/index.js'])
@endsection
