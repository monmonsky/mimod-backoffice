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
            <li>Access Control</li>
            <li><a href="{{ route('user.index') }}">Users</a></li>
            <li class="opacity-80">Create</li>
        </ul>
    </div>
</div>

<div class="mt-6">
    <form action="#" method="POST" class="space-y-6">
        @csrf

        <!-- Personal Information -->
        <div class="bg-base-100 card shadow">
            <div class="card-body">
                <h2 class="card-title text-lg">Personal Information</h2>
                <p class="text-sm text-base-content/70 mb-4">Basic user information and contact details</p>

                <div class="space-y-4">
                    <!-- Full Name -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Full Name <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="name" placeholder="Enter full name" class="input input-bordered w-full" required />
                    </div>

                    <!-- Email & Phone -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Email Address <span class="text-error">*</span></span>
                            </label>
                            <label class="input input-bordered flex items-center gap-2">
                                <span class="iconify lucide--mail size-4 text-base-content/60"></span>
                                <input type="email" name="email" placeholder="user@example.com" class="grow" required />
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Phone Number</span>
                            </label>
                            <label class="input input-bordered flex items-center gap-2">
                                <span class="iconify lucide--phone size-4 text-base-content/60"></span>
                                <input type="tel" name="phone" placeholder="+62 812 3456 7890" class="grow" />
                            </label>
                        </div>
                    </div>

                    <!-- Profile Picture -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Profile Picture</span>
                        </label>
                        <div class="flex items-start gap-4">
                            <div class="avatar placeholder">
                                <div class="bg-neutral text-neutral-content rounded-full w-20">
                                    <span class="text-3xl">
                                        <span class="iconify lucide--user size-10"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <input type="file" name="avatar" class="file-input file-input-bordered w-full max-w-xs" accept="image/*" />
                                <label class="label">
                                    <span class="label-text-alt text-base-content/60">Recommended: Square image, max 2MB (JPG, PNG)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Settings -->
        <div class="bg-base-100 card shadow">
            <div class="card-body">
                <h2 class="card-title text-lg">Account Settings</h2>
                <p class="text-sm text-base-content/70 mb-4">Configure user account and access</p>

                <div class="space-y-4">
                    <!-- Password -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Password <span class="text-error">*</span></span>
                            </label>
                            <label class="input input-bordered flex items-center gap-2">
                                <span class="iconify lucide--lock size-4 text-base-content/60"></span>
                                <input type="password" id="password" name="password" placeholder="Enter password" class="grow" required />
                                <button type="button" onclick="togglePassword('password')" class="btn btn-ghost btn-xs btn-square">
                                    <span class="iconify lucide--eye size-4"></span>
                                </button>
                            </label>
                            <label class="label">
                                <span class="label-text-alt text-base-content/60">Minimum 8 characters</span>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Confirm Password <span class="text-error">*</span></span>
                            </label>
                            <label class="input input-bordered flex items-center gap-2">
                                <span class="iconify lucide--lock size-4 text-base-content/60"></span>
                                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm password" class="grow" required />
                                <button type="button" onclick="togglePassword('password_confirmation')" class="btn btn-ghost btn-xs btn-square">
                                    <span class="iconify lucide--eye size-4"></span>
                                </button>
                            </label>
                        </div>
                    </div>

                    <!-- Status & Email Verification -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Account Status <span class="text-error">*</span></span>
                            </label>
                            <select name="status" class="select select-bordered w-full" required>
                                <option value="active" selected>Active</option>
                                <option value="suspended">Suspended</option>
                                <option value="deleted">Deleted</option>
                            </select>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="email_verified" class="toggle toggle-primary" />
                                <div>
                                    <span class="label-text font-medium">Email Verified</span>
                                    <p class="text-xs text-base-content/60">Mark email as verified</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Two Factor Authentication -->
                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="two_factor_enabled" class="toggle toggle-primary" />
                            <div>
                                <span class="label-text font-medium">Enable Two-Factor Authentication</span>
                                <p class="text-xs text-base-content/60">Require 2FA for this user</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Roles & Permissions -->
        <div class="bg-base-100 card shadow">
            <div class="card-body">
                <h2 class="card-title text-lg">Roles & Permissions</h2>
                <p class="text-sm text-base-content/70 mb-4">Assign roles and permissions to this user</p>

                <div class="space-y-4">
                    <!-- Assign Roles -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Assign Roles <span class="text-error">*</span></span>
                        </label>
                        <div class="space-y-2">
                            <label class="label cursor-pointer justify-start gap-3 border border-base-300 rounded-lg p-3">
                                <input type="checkbox" name="roles[]" value="super_admin" class="checkbox checkbox-primary" />
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium">Super Administrator</span>
                                        <span class="badge badge-error badge-xs">Highest Access</span>
                                    </div>
                                    <p class="text-xs text-base-content/60">Full system access and control</p>
                                </div>
                            </label>

                            <label class="label cursor-pointer justify-start gap-3 border border-base-300 rounded-lg p-3">
                                <input type="checkbox" name="roles[]" value="admin" class="checkbox checkbox-primary" />
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium">Administrator</span>
                                        <span class="badge badge-warning badge-xs">High Access</span>
                                    </div>
                                    <p class="text-xs text-base-content/60">Admin panel access with some restrictions</p>
                                </div>
                            </label>

                            <label class="label cursor-pointer justify-start gap-3 border border-base-300 rounded-lg p-3">
                                <input type="checkbox" name="roles[]" value="staff" class="checkbox checkbox-primary" />
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium">Staff</span>
                                        <span class="badge badge-info badge-xs">Limited Access</span>
                                    </div>
                                    <p class="text-xs text-base-content/60">Limited admin access for daily operations</p>
                                </div>
                            </label>

                            <label class="label cursor-pointer justify-start gap-3 border border-base-300 rounded-lg p-3">
                                <input type="checkbox" name="roles[]" value="customer" class="checkbox checkbox-primary" checked />
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium">Customer</span>
                                        <span class="badge badge-success badge-xs">Default</span>
                                    </div>
                                    <p class="text-xs text-base-content/60">Regular customer account</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Role Expiry (Optional) -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Role Expiry Date (Optional)</span>
                        </label>
                        <input type="datetime-local" name="role_expires_at" class="input input-bordered w-full" />
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Leave empty for permanent role assignment</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="bg-base-100 card shadow">
            <div class="card-body">
                <h2 class="card-title text-lg">Additional Information</h2>
                <p class="text-sm text-base-content/70 mb-4">Optional user notes and metadata</p>

                <div class="space-y-4">
                    <!-- Notes -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Internal Notes</span>
                        </label>
                        <textarea name="notes" class="textarea textarea-bordered h-24" placeholder="Add any internal notes about this user..."></textarea>
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">These notes are only visible to administrators</span>
                        </label>
                    </div>

                    <!-- Send Welcome Email -->
                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="send_welcome_email" class="checkbox checkbox-primary" checked />
                            <div>
                                <span class="label-text font-medium">Send Welcome Email</span>
                                <p class="text-xs text-base-content/60">Send account details and welcome message to user's email</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('user.index') }}" class="btn btn-ghost">
                <span class="iconify lucide--x size-4"></span>
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <span class="iconify lucide--user-plus size-4"></span>
                Create User
            </button>
        </div>
    </form>
</div>
@endsection

@section('customjs')
<script>
    function togglePassword(id) {
        const input = document.getElementById(id);
        const icon = input.parentElement.querySelector('.iconify');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('lucide--eye');
            icon.classList.add('lucide--eye-off');
        } else {
            input.type = 'password';
            icon.classList.remove('lucide--eye-off');
            icon.classList.add('lucide--eye');
        }
    }
</script>
@endsection