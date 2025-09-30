@extends('layouts.app')

@section('title', 'System Config')
@section('page_title', 'Settings')
@section('page_subtitle', 'System Configuration')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">System Configuration</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Settings</li>
            <li class="opacity-80">System Config</li>
        </ul>
    </div>
</div>

<form id="systemConfigForm" action="{{ route('settings.system-config.update') }}" method="POST" class="mt-6 space-y-6">
    @csrf

    <!-- General Settings -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">General Settings</h2>
            <p class="text-sm text-base-content/70 mb-4">Basic system configuration</p>

            <div class="space-y-6">
                <!-- Timezone & Date Format -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Timezone <span class="text-error">*</span></span>
                        </label>
                        <select name="timezone" class="select select-bordered w-full" required>
                            <option value="Asia/Jakarta" {{ ($generalSettings['timezone'] ?? 'Asia/Jakarta') == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (GMT+7)</option>
                            <option value="Asia/Singapore" {{ ($generalSettings['timezone'] ?? '') == 'Asia/Singapore' ? 'selected' : '' }}>Asia/Singapore (GMT+8)</option>
                            <option value="UTC" {{ ($generalSettings['timezone'] ?? '') == 'UTC' ? 'selected' : '' }}>UTC</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Date Format</span>
                        </label>
                        <select name="date_format" class="select select-bordered w-full">
                            <option value="Y-m-d" {{ ($generalSettings['date_format'] ?? 'Y-m-d') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                            <option value="d/m/Y" {{ ($generalSettings['date_format'] ?? '') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                            <option value="m/d/Y" {{ ($generalSettings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                        </select>
                    </div>
                </div>

                <!-- Time Format & Language -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Time Format</span>
                        </label>
                        <select name="time_format" class="select select-bordered w-full">
                            <option value="H:i:s" {{ ($generalSettings['time_format'] ?? 'H:i:s') == 'H:i:s' ? 'selected' : '' }}>24 Hour (HH:MM:SS)</option>
                            <option value="h:i A" {{ ($generalSettings['time_format'] ?? '') == 'h:i A' ? 'selected' : '' }}>12 Hour (hh:mm AM/PM)</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Default Language</span>
                        </label>
                        <select name="default_language" class="select select-bordered w-full">
                            <option value="id" {{ ($generalSettings['default_language'] ?? 'id') == 'id' ? 'selected' : '' }}>Indonesia</option>
                            <option value="en" {{ ($generalSettings['default_language'] ?? '') == 'en' ? 'selected' : '' }}>English</option>
                        </select>
                    </div>
                </div>

                <!-- Currency Settings -->
                <div class="divider">Currency Settings</div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Currency <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="currency" placeholder="IDR" class="input input-bordered w-full" value="{{ $generalSettings['currency'] ?? 'IDR' }}" required />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Currency Symbol <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="currency_symbol" placeholder="Rp" class="input input-bordered w-full" value="{{ $generalSettings['currency_symbol'] ?? 'Rp' }}" required />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Currency Position</span>
                        </label>
                        <select name="currency_position" class="select select-bordered w-full">
                            <option value="before" {{ ($generalSettings['currency_position'] ?? 'before') == 'before' ? 'selected' : '' }}>Before (Rp 10,000)</option>
                            <option value="after" {{ ($generalSettings['currency_position'] ?? '') == 'after' ? 'selected' : '' }}>After (10,000 Rp)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Decimal Separator</span>
                        </label>
                        <input type="text" name="decimal_separator" maxlength="1" placeholder="," class="input input-bordered w-full" value="{{ $generalSettings['decimal_separator'] ?? ',' }}" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Thousand Separator</span>
                        </label>
                        <input type="text" name="thousand_separator" maxlength="1" placeholder="." class="input input-bordered w-full" value="{{ $generalSettings['thousand_separator'] ?? '.' }}" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Settings -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Security Settings</h2>
            <p class="text-sm text-base-content/70 mb-4">Password and authentication security</p>

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Min Password Length</span>
                        </label>
                        <input type="number" name="min_password_length" min="6" max="32" class="input input-bordered w-full" value="{{ $securitySettings['min_password_length'] ?? 8 }}" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Session Timeout (minutes)</span>
                        </label>
                        <input type="number" name="session_timeout" min="5" class="input input-bordered w-full" value="{{ $securitySettings['session_timeout'] ?? 120 }}" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Max Login Attempts</span>
                        </label>
                        <input type="number" name="max_login_attempts" min="3" max="10" class="input input-bordered w-full" value="{{ $securitySettings['max_login_attempts'] ?? 5 }}" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Lockout Duration (minutes)</span>
                        </label>
                        <input type="number" name="lockout_duration" min="5" class="input input-bordered w-full" value="{{ $securitySettings['lockout_duration'] ?? 15 }}" />
                    </div>
                </div>

                <div class="divider">Password Requirements</div>

                <div class="space-y-3">
                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="require_uppercase" class="toggle toggle-primary" {{ ($securitySettings['require_uppercase'] ?? true) ? 'checked' : '' }} />
                            <span class="label-text">Require uppercase letter</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="require_number" class="toggle toggle-primary" {{ ($securitySettings['require_number'] ?? true) ? 'checked' : '' }} />
                            <span class="label-text">Require number</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="require_special_char" class="toggle toggle-primary" {{ ($securitySettings['require_special_char'] ?? false) ? 'checked' : '' }} />
                            <span class="label-text">Require special character</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="enable_2fa" class="toggle toggle-primary" {{ ($securitySettings['enable_2fa'] ?? false) ? 'checked' : '' }} />
                            <span class="label-text">Enable Two-Factor Authentication (2FA)</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Maintenance Mode -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Maintenance Mode</h2>
            <p class="text-sm text-base-content/70 mb-4">Temporarily disable public access</p>

            <div class="space-y-6">
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" name="maintenance_mode" class="toggle toggle-warning" {{ ($maintenanceSettings['maintenance_mode'] ?? false) ? 'checked' : '' }} />
                        <div>
                            <span class="label-text font-medium">Enable Maintenance Mode</span>
                            <p class="text-xs text-base-content/60">Site will be unavailable to public users</p>
                        </div>
                    </label>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Maintenance Message</span>
                    </label>
                    <textarea name="maintenance_message" class="textarea textarea-bordered w-full" placeholder="Enter maintenance message">{{ $maintenanceSettings['maintenance_message'] ?? 'We are currently performing maintenance. Please check back soon.' }}</textarea>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Estimated End Time</span>
                    </label>
                    <input type="datetime-local" name="maintenance_end_time" class="input input-bordered w-full" value="{{ $maintenanceSettings['maintenance_end_time'] ?? '' }}" />
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end gap-2">
        <button type="button" class="btn btn-ghost">Cancel</button>
        <button type="submit" class="btn btn-primary">
            <span class="iconify lucide--save size-4"></span>
            Save Configuration
        </button>
    </div>
</form>
@endsection

@section('customjs')
<script>

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        const form = document.getElementById('systemConfigForm');

        if (!form) {
            console.error('Form #systemConfigForm not found!');
            return;
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Saving...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showToast(data.message || 'System configuration saved successfully!', 'success');
                } else {
                    if (data.errors) {
                        const errorMessages = Object.values(data.errors).flat().join(', ');
                        showToast(errorMessages, 'error');
                    } else {
                        showToast(data.message || 'Failed to save configuration', 'error');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('An error occurred while saving configuration', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    }
</script>
@endsection