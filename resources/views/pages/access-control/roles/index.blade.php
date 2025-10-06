@extends('layouts.app')

@section('title', 'Roles')
@section('page_title', 'Role')
@section('page_subtitle', 'Role Management')

@section('content')
<x-page-header
    title="Role Management"
    :breadcrumbs="[
        ['label' => 'Nexus', 'url' => route('dashboard')],
        ['label' => 'Access Control'],
        ['label' => 'Roles']
    ]"
/>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <x-stat-card
        title="Total Roles"
        :value="$statistics['total']"
        subtitle="All defined roles"
        icon="shield"
        icon-color="primary"
    />

    <x-stat-card
        title="Active Roles"
        :value="$statistics['active']"
        subtitle="Currently in use"
        icon="check-circle-2"
        icon-color="success"
    />

    <x-stat-card
        title="System Roles"
        :value="$statistics['system']"
        subtitle="Protected roles"
        icon="lock"
        icon-color="warning"
    />

    <x-stat-card
        title="Users Assigned"
        :value="number_format($statistics['users_assigned'])"
        subtitle="Total assignments"
        icon="users"
        icon-color="info"
    />
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
                            type="search"
                            id="searchInput" />
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
                <table id="rolesTable" class="table">
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
                                    <button
                                        aria-label="View details"
                                        class="btn btn-square btn-ghost btn-sm"
                                        onclick="showRoleDetail({{ $role->id }})">
                                        <span class="iconify lucide--eye text-base-content/80 size-4"></span>
                                    </button>

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

<!-- Role Detail Modal -->
<x-modal id="roleDetailModal" title="Role Details" size="max-w-3xl">
    <!-- Loading State -->
    <div id="modalLoading" class="flex justify-center items-center py-12">
        <span class="loading loading-spinner loading-lg text-primary"></span>
    </div>

    <!-- Content -->
    <div id="modalContent" class="hidden">
        <!-- Role Info -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label class="text-sm font-medium text-base-content/60">Role Name</label>
                <p id="roleName" class="text-base font-medium"></p>
            </div>
            <div>
                <label class="text-sm font-medium text-base-content/60">Display Name</label>
                <p id="roleDisplayName" class="text-base font-medium"></p>
            </div>
            <div>
                <label class="text-sm font-medium text-base-content/60">Priority</label>
                <p id="rolePriority" class="text-base font-medium"></p>
            </div>
            <div>
                <label class="text-sm font-medium text-base-content/60">Status</label>
                <p id="roleStatus"></p>
            </div>
            <div class="col-span-2">
                <label class="text-sm font-medium text-base-content/60">Description</label>
                <p id="roleDescription" class="text-base"></p>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Permissions -->
        <div>
            <h4 class="font-semibold mb-3">Permissions (<span id="permissionCount">0</span>)</h4>
            <div id="permissionsList" class="max-h-96 overflow-y-auto">
                <!-- Permissions will be loaded here -->
            </div>
        </div>
    </div>

    <x-slot name="footer">
        <form method="dialog">
            <button class="btn btn-sm">Close</button>
        </form>
    </x-slot>
</x-modal>
@endsection

@section('customjs')
@vite(['resources/js/modules/access-control/roles/index.js'])
@endsection
