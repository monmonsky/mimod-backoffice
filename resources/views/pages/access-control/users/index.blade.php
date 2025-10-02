@extends('layouts.app')

@section('title', 'Users')
@section('page_title', 'User')
@section('page_subtitle', 'User Management')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">User Management</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Access Control</li>
            <li class="opacity-80">Users</li>
        </ul>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Total Users</p>
                    <p class="text-2xl font-semibold mt-1">{{ $statistics['total'] }}</p>
                    <p class="text-xs text-base-content/60 mt-1">All registered users</p>
                </div>
                <div class="bg-primary/10 p-3 rounded-lg">
                    <span class="iconify lucide--users size-5 text-primary"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Active Users</p>
                    <p class="text-2xl font-semibold mt-1 text-success">{{ $statistics['active'] }}</p>
                    <p class="text-xs text-base-content/60 mt-1">Currently active</p>
                </div>
                <div class="bg-success/10 p-3 rounded-lg">
                    <span class="iconify lucide--user-check size-5 text-success"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Inactive Users</p>
                    <p class="text-2xl font-semibold mt-1 text-error">{{ $statistics['inactive'] }}</p>
                    <p class="text-xs text-base-content/60 mt-1">Not active</p>
                </div>
                <div class="bg-error/10 p-3 rounded-lg">
                    <span class="iconify lucide--user-x size-5 text-error"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">With Roles</p>
                    <p class="text-2xl font-semibold mt-1 text-info">{{ $statistics['with_roles'] }}</p>
                    <p class="text-xs text-base-content/60 mt-1">Assigned roles</p>
                </div>
                <div class="bg-info/10 p-3 rounded-lg">
                    <span class="iconify lucide--shield-check size-5 text-info"></span>
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
