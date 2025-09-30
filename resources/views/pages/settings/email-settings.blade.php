@extends('layouts.app')

@section('title', 'Email Settings')
@section('page_title', 'Settings')
@section('page_subtitle', 'Email Configuration')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Email Settings</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Settings</li>
            <li class="opacity-80">Email Settings</li>
        </ul>
    </div>
</div>

<form id="emailSettingsForm" action="{{ route('settings.email-settings.update') }}" method="POST" class="mt-6 space-y-6">
    @csrf

    <!-- SMTP Configuration -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">SMTP Configuration</h2>
            <p class="text-sm text-base-content/70 mb-4">Configure SMTP server settings for sending emails</p>

            <div class="space-y-6">
                <!-- SMTP Host -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">SMTP Host</span>
                    </label>
                    <input type="text" name="smtp_host" placeholder="smtp.gmail.com" class="input input-bordered w-full" value="{{ $smtpSettings['host'] ?? 'smtp.mailtrap.io' }}" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Your SMTP server hostname</span>
                    </label>
                </div>

                <!-- SMTP Port & Encryption -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">SMTP Port</span>
                        </label>
                        <input type="number" name="smtp_port" placeholder="587" class="input input-bordered w-full" value="{{ $smtpSettings['port'] ?? 587 }}" />
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Common: 587 (TLS), 465 (SSL), 25</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Encryption</span>
                        </label>
                        <select name="smtp_encryption" class="select select-bordered w-full">
                            <option value="">None</option>
                            <option value="tls" {{ ($smtpSettings['encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ ($smtpSettings['encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                        </select>
                    </div>
                </div>

                <!-- SMTP Username & Password -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">SMTP Username</span>
                        </label>
                        <input type="text" name="smtp_username" placeholder="your-email@gmail.com" class="input input-bordered w-full" value="{{ $smtpSettings['username'] ?? '' }}" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">SMTP Password</span>
                        </label>
                        <input type="password" name="smtp_password" placeholder="••••••••" class="input input-bordered w-full" value="{{ $smtpSettings['password'] ?? '' }}" />
                    </div>
                </div>

                <!-- From Address & Name -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">From Email Address <span class="text-error">*</span></span>
                        </label>
                        <input type="email" name="from_email" placeholder="noreply@yourstore.com" class="input input-bordered w-full" value="{{ $smtpSettings['from_email'] ?? 'noreply@minimoda.com' }}" required />
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Default sender email address</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">From Name <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="from_name" placeholder="Your Store Name" class="input input-bordered w-full" value="{{ $smtpSettings['from_name'] ?? 'Minimoda' }}" required />
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Default sender name</span>
                        </label>
                    </div>
                </div>

                <!-- Test Email Connection -->
                <div class="alert alert-info">
                    <span class="iconify lucide--info size-5"></span>
                    <div class="flex-1">
                        <h4 class="font-medium">Test Email Configuration</h4>
                        <p class="text-sm">Verify your SMTP settings by sending a test email</p>
                    </div>
                    <button type="button" id="testEmailBtn" class="btn btn-sm btn-primary">
                        <span class="iconify lucide--send size-4"></span>
                        Send Test Email
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Notifications -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Email Notification Settings</h2>
            <p class="text-sm text-base-content/70 mb-4">Enable or disable automatic email notifications</p>

            <div class="space-y-4">
                <!-- Order Notifications -->
                <div>
                    <h3 class="font-medium mb-3">Order Notifications</h3>
                    <div class="space-y-3">
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="order_confirmation" class="toggle toggle-primary" {{ ($notifications['order_confirmation'] ?? true) ? 'checked' : '' }} />
                                <div>
                                    <span class="label-text font-medium">Order Confirmation</span>
                                    <p class="text-xs text-base-content/60">Send email when order is placed</p>
                                </div>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="order_shipped" class="toggle toggle-primary" {{ ($notifications['order_shipped'] ?? true) ? 'checked' : '' }} />
                                <div>
                                    <span class="label-text font-medium">Order Shipped</span>
                                    <p class="text-xs text-base-content/60">Send email with tracking information</p>
                                </div>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="order_delivered" class="toggle toggle-primary" {{ ($notifications['order_delivered'] ?? true) ? 'checked' : '' }} />
                                <div>
                                    <span class="label-text font-medium">Order Delivered</span>
                                    <p class="text-xs text-base-content/60">Send email when order is delivered</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Customer Notifications -->
                <div>
                    <h3 class="font-medium mb-3">Customer Notifications</h3>
                    <div class="space-y-3">
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="welcome_email" class="toggle toggle-primary" {{ ($notifications['welcome_email'] ?? true) ? 'checked' : '' }} />
                                <div>
                                    <span class="label-text font-medium">Welcome Email</span>
                                    <p class="text-xs text-base-content/60">Send welcome email to new customers</p>
                                </div>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="password_reset" class="toggle toggle-primary" {{ ($notifications['password_reset'] ?? true) ? 'checked' : '' }} />
                                <div>
                                    <span class="label-text font-medium">Password Reset</span>
                                    <p class="text-xs text-base-content/60">Send password reset link via email</p>
                                </div>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="newsletter" class="toggle toggle-primary" {{ ($notifications['newsletter'] ?? false) ? 'checked' : '' }} />
                                <div>
                                    <span class="label-text font-medium">Newsletter</span>
                                    <p class="text-xs text-base-content/60">Send promotional newsletters</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end gap-2">
        <button type="button" class="btn btn-ghost">Cancel</button>
        <button type="submit" class="btn btn-primary">
            <span class="iconify lucide--save size-4"></span>
            Save Settings
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
        const form = document.getElementById('emailSettingsForm');
        const testEmailBtn = document.getElementById('testEmailBtn');

        if (!form) {
            console.error('Form #emailSettingsForm not found!');
            return;
        }

        // Handle form submission
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
                    showToast(data.message || 'Email settings saved successfully!', 'success');
                } else {
                    if (data.errors) {
                        const errorMessages = Object.values(data.errors).flat().join(', ');
                        showToast(errorMessages, 'error');
                    } else {
                        showToast(data.message || 'Failed to save settings', 'error');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('An error occurred while saving settings', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });

        // Handle test email button
        if (testEmailBtn) {
            testEmailBtn.addEventListener('click', async function() {
                // Get email address for test
                const testEmail = prompt('Enter email address to send test email:', document.querySelector('input[name="from_email"]').value);

                if (!testEmail) {
                    return; // User cancelled
                }

                // Validate email format
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(testEmail)) {
                    showToast('Please enter a valid email address', 'error');
                    return;
                }

                const originalBtnText = testEmailBtn.innerHTML;

                // Show loading state
                testEmailBtn.disabled = true;
                testEmailBtn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Sending...';

                try {
                    const response = await fetch('{{ route("settings.email-settings.test") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ test_email: testEmail })
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        showToast(data.message || 'Test email sent successfully!', 'success');
                    } else {
                        showToast(data.message || 'Failed to send test email', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToast('An error occurred while sending test email', 'error');
                } finally {
                    testEmailBtn.disabled = false;
                    testEmailBtn.innerHTML = originalBtnText;
                }
            });
        }
    }
</script>
@endsection