@extends('layouts.app')

@section('title', 'Edit User')
@section('page_title', 'User')
@section('page_subtitle', 'Edit User')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Edit User</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li><a href="{{ route('user.index') }}">Users</a></li>
            <li class="opacity-80">Edit</li>
        </ul>
    </div>
</div>

<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <form id="editUserForm" data-user-id="{{ $user->id }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Name -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Full Name <span class="text-error">*</span></span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            class="input input-bordered w-full"
                            placeholder="Enter full name"
                            value="{{ $user->name }}"
                            required />
                        <label class="label">
                            <span class="label-text-alt text-error hidden" id="error-name"></span>
                        </label>
                    </div>

                    <!-- Email -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Email Address <span class="text-error">*</span></span>
                        </label>
                        <input
                            type="email"
                            name="email"
                            class="input input-bordered w-full"
                            placeholder="Enter email address"
                            value="{{ $user->email }}"
                            required />
                        <label class="label">
                            <span class="label-text-alt text-error hidden" id="error-email"></span>
                        </label>
                    </div>

                    <!-- Phone -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Phone Number <span class="text-error">*</span></span>
                        </label>
                        <input
                            type="text"
                            name="phone"
                            class="input input-bordered w-full"
                            placeholder="Enter phone number"
                            value="{{ $user->phone }}"
                            required />
                        <label class="label">
                            <span class="label-text-alt text-error hidden" id="error-phone"></span>
                        </label>
                    </div>

                    <!-- Password -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">New Password</span>
                        </label>
                        <input
                            type="password"
                            name="password"
                            class="input input-bordered w-full"
                            placeholder="Leave blank to keep current password" />
                        <label class="label">
                            <span class="label-text-alt">Minimum 8 characters (leave blank to keep current)</span>
                            <span class="label-text-alt text-error hidden" id="error-password"></span>
                        </label>
                    </div>

                    <!-- Password Confirmation -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Confirm New Password</span>
                        </label>
                        <input
                            type="password"
                            name="password_confirmation"
                            class="input input-bordered w-full"
                            placeholder="Confirm new password" />
                        <label class="label">
                            <span class="label-text-alt text-error hidden" id="error-password_confirmation"></span>
                        </label>
                    </div>

                    <!-- Role -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Role</span>
                        </label>
                        <select name="role_id" class="select select-bordered w-full">
                            <option value="">No Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ $userRole && $userRole->id == $role->id ? 'selected' : '' }}>
                                    {{ $role->display_name }}
                                </option>
                            @endforeach
                        </select>
                        <label class="label">
                            <span class="label-text-alt text-error hidden" id="error-role_id"></span>
                        </label>
                    </div>

                    <!-- Is Active -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Status</span>
                        </label>
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="is_active" class="toggle toggle-success" {{ $user->status === 'active' ? 'checked' : '' }} />
                            <span class="label-text">Active</span>
                        </label>
                        <label class="label">
                            <span class="label-text-alt">Enable or disable user account</span>
                        </label>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- User Info -->
                <div class="alert alert-info mb-6">
                    <span class="iconify lucide--info size-5"></span>
                    <div class="flex-1">
                        <p class="text-sm">
                            <strong>User ID:</strong> {{ $user->id }}<br>
                            <strong>Created:</strong> {{ \Carbon\Carbon::parse($user->created_at)->format('d M Y H:i') }}<br>
                            <strong>Last Login:</strong> {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : 'Never' }}
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('user.index') }}" class="btn btn-ghost">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="iconify lucide--save size-4"></span>
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('customjs')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@vite(['resources/js/modules/access-control/users/edit.js'])
@endsection
