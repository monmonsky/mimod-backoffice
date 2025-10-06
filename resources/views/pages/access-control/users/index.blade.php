@extends('layouts.app')

@section('title', 'Users')
@section('page_title', 'User')
@section('page_subtitle', 'User Management')

@section('content')
<x-page-header
    title="User Management"
    :breadcrumbs="[
        ['label' => 'Nexus', 'url' => route('dashboard')],
        ['label' => 'Access Control'],
        ['label' => 'Users']
    ]"
/>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <x-stat-card
        title="Total Users"
        :value="$statistics['total']"
        subtitle="All registered users"
        icon="users"
        icon-color="primary"
    />

    <x-stat-card
        title="Active Users"
        :value="$statistics['active']"
        subtitle="Currently active"
        icon="user-round-check"
        icon-color="success"
    />

    <x-stat-card
        title="Inactive Users"
        :value="$statistics['inactive']"
        subtitle="Not active"
        icon="user-round-x"
        icon-color="error"
    />

    <x-stat-card
        title="With Roles"
        :value="$statistics['with_roles']"
        subtitle="Assigned roles"
        icon="shield-check"
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
                            placeholder="Search users"
                            aria-label="Search users"
                            type="search"
                            id="searchInput" />
                    </label>
                </div>
                <div class="inline-flex items-center gap-3">
                    @if(hasPermission('access-control.users.create'))
                    <a
                        aria-label="Create user link"
                        class="btn btn-primary btn-sm max-sm:btn-square"
                        href="{{ route('user.create') }}">
                        <span class="iconify lucide--plus size-4"></span>
                        <span class="max-sm:hidden">Add User</span>
                    </a>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="table" id="usersTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar">
                                        <div class="w-10 rounded-full">
                                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ $user->email }}" alt="{{ $user->name }}" />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-medium">{{ $user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-sm">{{ $user->email }}</span>
                            </td>
                            <td>
                                @if($user->role_name)
                                    <span class="badge badge-outline badge-sm">{{ $user->role_display_name }}</span>
                                @else
                                    <span class="text-xs text-base-content/40">No role</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    @if(hasPermission('access-control.users.update'))
                                    <input
                                        type="checkbox"
                                        class="toggle toggle-success toggle-sm"
                                        data-id="{{ $user->id }}"
                                        {{ $user->status === 'active' ? 'checked' : '' }}
                                        onchange="toggleStatus(this)" />
                                    <span class="badge badge-sm {{ $user->status === 'active' ? 'badge-success' : 'badge-error' }}">
                                        {{ $user->status === 'active' ? 'Active' : 'Inactive' }}
                                    </span>
                                    @else
                                        <span class="badge badge-sm {{ $user->status === 'active' ? 'badge-success' : 'badge-error' }}">
                                            {{ $user->status === 'active' ? 'Active' : 'Inactive' }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($user->last_login_at)
                                    <span class="text-xs">{{ \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() }}</span>
                                @else
                                    <span class="text-xs text-base-content/40">Never</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    @if(hasPermission('access-control.users.update'))
                                    <a
                                        href="{{ route('user.edit', $user->id) }}"
                                        class="btn btn-ghost btn-xs"
                                        aria-label="Edit user">
                                        <span class="iconify lucide--pencil size-4"></span>
                                    </a>
                                    @endif

                                    @if(hasPermission('access-control.users.delete'))
                                    <button
                                        onclick="deleteUser('{{ $user->id }}', '{{ $user->name }}')"
                                        class="btn btn-ghost btn-xs text-error"
                                        aria-label="Delete user">
                                        <span class="iconify lucide--trash-2 size-4"></span>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customjs')
@vite(['resources/js/modules/access-control/users/index.js'])
@endsection
