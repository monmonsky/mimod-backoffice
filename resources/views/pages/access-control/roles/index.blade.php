@extends('layouts.app')

@section('title', 'Roles')
@section('page_title', 'Role')
@section('page_subtitle', 'Role Management')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Role Management</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Access Control</li>
            <li class="opacity-80">Roles</li>
        </ul>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Total Roles</p>
                    <p class="text-2xl font-semibold mt-1">{{ $statistics['total'] }}</p>
                    <p class="text-xs text-base-content/60 mt-1">All defined roles</p>
                </div>
                <div class="bg-primary/10 p-3 rounded-lg">
                    <span class="iconify lucide--shield size-5 text-primary"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Active Roles</p>
                    <p class="text-2xl font-semibold mt-1 text-success">{{ $statistics['active'] }}</p>
                    <p class="text-xs text-base-content/60 mt-1">Currently in use</p>
                </div>
                <div class="bg-success/10 p-3 rounded-lg">
                    <span class="iconify lucide--check-circle size-5 text-success"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">System Roles</p>
                    <p class="text-2xl font-semibold mt-1 text-warning">{{ $statistics['system'] }}</p>
                    <p class="text-xs text-base-content/60 mt-1">Protected roles</p>
                </div>
                <div class="bg-warning/10 p-3 rounded-lg">
                    <span class="iconify lucide--lock size-5 text-warning"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Users Assigned</p>
                    <p class="text-2xl font-semibold mt-1 text-info">{{ number_format($statistics['users_assigned']) }}</p>
                    <p class="text-xs text-base-content/60 mt-1">Total assignments</p>
                </div>
                <div class="bg-info/10 p-3 rounded-lg">
                    <span class="iconify lucide--users size-5 text-info"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body p-0">
            <div class="flex items-center justify-between px-5 pt-5">
                <div class="inline-flex items-center gap-3">
                    <label class="input input-sm">
                        <span class="iconify lucide--search text-base-content/80 size-3.5"></span>
                        <input
                            class="w-24 sm:w-36"
                            placeholder="Search roles"
                            aria-label="Search roles"
                            type="search" />
                    </label>
                </div>
                <div class="inline-flex items-center gap-3">
                    @if(hasPermission('access-control.roles.create'))
                    <a
                        aria-label="Create role link"
                        class="btn btn-primary btn-sm max-sm:btn-square"
                        href="{{ route('role.create') }}">
                        <span class="iconify lucide--plus size-4"></span>
                        <span class="hidden sm:inline">Add Role</span>
                    </a>
                    @endif
                </div>
            </div>

            <div class="mt-4 overflow-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Role Name</th>
                            <th>Display Name</th>
                            <th>Description</th>
                            <th>Priority</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Total Users</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                        <tr class="hover:bg-base-200/40 *:text-nowrap">
                            <td class="font-mono text-sm">{{ $role->name }}</td>
                            <td>{{ $role->display_name }}</td>
                            <td class="max-w-xs truncate">{{ $role->description ?: '-' }}</td>
                            <td>
                                <span class="badge badge-primary badge-sm">{{ $role->priority }}</span>
                            </td>
                            <td>
                                @if($role->is_system)
                                    <span class="badge badge-warning badge-sm badge-soft">System</span>
                                @else
                                    <span class="badge badge-ghost badge-sm">Custom</span>
                                @endif
                            </td>
                            <td>
                                @if(hasPermission('access-control.roles.update'))
                                <form action="{{ route('role.toggle-active', $role->id) }}" method="POST" class="inline toggle-form">
                                    @csrf
                                    <button type="submit" class="badge badge-sm {{ $role->is_active ? 'badge-success' : 'badge-error' }} cursor-pointer" {{ $role->is_system ? 'disabled' : '' }}>
                                        {{ $role->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                                @else
                                    <span class="badge badge-sm {{ $role->is_active ? 'badge-success' : 'badge-error' }}">
                                        {{ $role->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">{{ $role->users_count }}</td>
                            <td class="text-sm">{{ \Carbon\Carbon::parse($role->created_at)->format('Y-m-d') }}</td>
                            <td>
                                <div class="inline-flex">
                                    @if(hasPermission('access-control.roles.update'))
                                    <a
                                        aria-label="Edit role"
                                        class="btn btn-square btn-ghost btn-sm"
                                        href="{{ route('role.edit', $role->id) }}">
                                        <span class="iconify lucide--pencil text-base-content/80 size-4"></span>
                                    </a>
                                    @endif

                                    @if(hasPermission('access-control.roles.delete') && !$role->is_system)
                                    <form action="{{ route('role.destroy', $role->id) }}" method="POST" class="inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-square btn-error btn-outline btn-sm border-transparent">
                                            <span class="iconify lucide--trash size-4"></span>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-8">
                                <div class="text-base-content/60">
                                    <span class="iconify lucide--inbox size-12 mx-auto mb-2"></span>
                                    <p>No roles found</p>
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
@vite(['resources/js/modules/roles/index.js'])
@endsection
