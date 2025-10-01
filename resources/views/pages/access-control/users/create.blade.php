@extends('layouts.app')

@section('title', 'Create User')
@section('page_title', 'User')
@section('page_subtitle', 'Create New User')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Create New User</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li><a href="{{ route('user.index') }}">Users</a></li>
            <li class="opacity-80">Create</li>
        </ul>
    </div>
</div>

<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <form id="createUserForm">
                @csrf
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
                            required />
                        <label class="label">
                            <span class="label-text-alt text-error hidden" id="error-email"></span>
                        </label>
                    </div>

                    <!-- Password -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Password <span class="text-error">*</span></span>
                        </label>
                        <input
                            type="password"
                            name="password"
                            class="input input-bordered w-full"
                            placeholder="Enter password"
                            required />
                        <label class="label">
                            <span class="label-text-alt">Minimum 8 characters</span>
                            <span class="label-text-alt text-error hidden" id="error-password"></span>
                        </label>
                    </div>

                    <!-- Password Confirmation -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Confirm Password <span class="text-error">*</span></span>
                        </label>
                        <input
                            type="password"
                            name="password_confirmation"
                            class="input input-bordered w-full"
                            placeholder="Confirm password"
                            required />
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
                                <option value="{{ $role->id }}">{{ $role->display_name }}</option>
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
                            <input type="checkbox" name="is_active" class="toggle toggle-success" checked />
                            <span class="label-text">Active</span>
                        </label>
                        <label class="label">
                            <span class="label-text-alt">Enable or disable user account</span>
                        </label>
                    </div>
                </div>

                <div class="divider"></div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('user.index') }}" class="btn btn-ghost">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="iconify lucide--save size-4"></span>
                        Create User
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

@vite(['resources/js/modules/access-control/users/create.js'])
@endsection
