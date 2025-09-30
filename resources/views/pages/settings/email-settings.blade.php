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

<div class="mt-6 space-y-6">
    <!-- SMTP Configuration -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">SMTP Configuration</h2>
            <p class="text-sm text-base-content/70 mb-4">Configure SMTP server settings for sending emails</p>

            <form class="space-y-6">
                <!-- Mail Driver -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Mail Driver <span class="text-error">*</span></span>
                    </label>
                    <select class="select select-bordered w-full">
                        <option disabled>Select Driver</option>
                        <option selected>SMTP</option>
                        <option>Sendmail</option>
                        <option>Mailgun</option>
                        <option>SES</option>
                    </select>
                </div>

                <!-- SMTP Host -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">SMTP Host <span class="text-error">*</span></span>
                    </label>
                    <input type="text" placeholder="smtp.gmail.com" class="input input-bordered w-full" value="smtp.gmail.com" />
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">Your SMTP server hostname</span>
                    </label>
                </div>

                <!-- SMTP Port & Encryption -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">SMTP Port <span class="text-error">*</span></span>
                        </label>
                        <input type="number" placeholder="587" class="input input-bordered w-full" value="587" />
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Common: 587 (TLS), 465 (SSL), 25</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Encryption <span class="text-error">*</span></span>
                        </label>
                        <select class="select select-bordered w-full">
                            <option disabled>Select Encryption</option>
                            <option selected>TLS</option>
                            <option>SSL</option>
                            <option>None</option>
                        </select>
                    </div>
                </div>

                <!-- SMTP Username & Password -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">SMTP Username <span class="text-error">*</span></span>
                        </label>
                        <input type="text" placeholder="your-email@gmail.com" class="input input-bordered w-full" value="noreply@minimoda.com" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">SMTP Password <span class="text-error">*</span></span>
                        </label>
                        <input type="password" placeholder="••••••••" class="input input-bordered w-full" value="password123" />
                    </div>
                </div>

                <!-- From Address & Name -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">From Email Address <span class="text-error">*</span></span>
                        </label>
                        <input type="email" placeholder="noreply@yourstore.com" class="input input-bordered w-full" value="noreply@minimoda.com" />
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Default sender email address</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">From Name <span class="text-error">*</span></span>
                        </label>
                        <input type="text" placeholder="Your Store Name" class="input input-bordered w-full" value="Minimoda" />
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Default sender name</span>
                        </label>
                    </div>
                </div>

                <!-- Test Email -->
                <div class="alert alert-info">
                    <span class="iconify lucide--info size-5"></span>
                    <div class="flex-1">
                        <h4 class="font-medium">Test Email Configuration</h4>
                        <p class="text-sm">Send a test email to verify your SMTP settings</p>
                    </div>
                    <button type="button" class="btn btn-sm">
                        <span class="iconify lucide--send size-4"></span>
                        Send Test Email
                    </button>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-4"></span>
                        Save SMTP Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Email Templates -->
    {{-- <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Email Template Settings</h2>
            <p class="text-sm text-base-content/70 mb-4">Configure default email templates and styling</p>

            <form class="space-y-6">
                <!-- Logo for Emails -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Email Header Logo</span>
                    </label>
                    <div class="flex items-start gap-4">
                        <div class="avatar">
                            <div class="w-32 rounded">
                                <img src="https://placehold.co/400x100/png?text=EMAIL+LOGO" alt="Email Logo" />
                            </div>
                        </div>
                        <div class="flex-1">
                            <input type="file" class="file-input file-input-bordered w-full max-w-xs" accept="image/*" />
                            <label class="label">
                                <span class="label-text-alt text-base-content/60">Recommended size: 400x100px (PNG with transparent background)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Email Colors -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Primary Color</span>
                        </label>
                        <input type="color" class="input input-bordered w-full h-12" value="#3b82f6" />
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Main brand color for buttons and headers</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Secondary Color</span>
                        </label>
                        <input type="color" class="input input-bordered w-full h-12" value="#64748b" />
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Secondary color for text and accents</span>
                        </label>
                    </div>
                </div>

                <!-- Email Footer -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Email Footer Text</span>
                    </label>
                    <textarea class="textarea textarea-bordered h-24" placeholder="Enter footer text">© 2024 Minimoda. All rights reserved.
You received this email because you're a registered customer at Minimoda.</textarea>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">This text will appear at the bottom of all emails</span>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-4"></span>
                        Save Template Settings
                    </button>
                </div>
            </form>
        </div>
    </div> --}}

    <!-- Email Notifications -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Email Notification Settings</h2>
            <p class="text-sm text-base-content/70 mb-4">Enable or disable automatic email notifications</p>

            <form class="space-y-4">
                <!-- Order Notifications -->
                <div>
                    <h3 class="font-medium mb-3">Order Notifications</h3>
                    <div class="space-y-3">
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="toggle toggle-primary" checked />
                                <div>
                                    <span class="label-text font-medium">Order Confirmation</span>
                                    <p class="text-xs text-base-content/60">Send email when order is placed</p>
                                </div>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="toggle toggle-primary" checked />
                                <div>
                                    <span class="label-text font-medium">Payment Received</span>
                                    <p class="text-xs text-base-content/60">Send email when payment is confirmed</p>
                                </div>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="toggle toggle-primary" checked />
                                <div>
                                    <span class="label-text font-medium">Order Shipped</span>
                                    <p class="text-xs text-base-content/60">Send email with tracking information</p>
                                </div>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="toggle toggle-primary" checked />
                                <div>
                                    <span class="label-text font-medium">Order Delivered</span>
                                    <p class="text-xs text-base-content/60">Send email when order is delivered</p>
                                </div>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="toggle toggle-primary" />
                                <div>
                                    <span class="label-text font-medium">Order Cancelled</span>
                                    <p class="text-xs text-base-content/60">Send email when order is cancelled</p>
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
                                <input type="checkbox" class="toggle toggle-primary" checked />
                                <div>
                                    <span class="label-text font-medium">Welcome Email</span>
                                    <p class="text-xs text-base-content/60">Send welcome email to new customers</p>
                                </div>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="toggle toggle-primary" checked />
                                <div>
                                    <span class="label-text font-medium">Password Reset</span>
                                    <p class="text-xs text-base-content/60">Send password reset link via email</p>
                                </div>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="toggle toggle-primary" />
                                <div>
                                    <span class="label-text font-medium">Email Verification</span>
                                    <p class="text-xs text-base-content/60">Send email verification link</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Marketing Notifications -->
                <div>
                    <h3 class="font-medium mb-3">Marketing Notifications</h3>
                    <div class="space-y-3">
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="toggle toggle-primary" />
                                <div>
                                    <span class="label-text font-medium">Newsletter</span>
                                    <p class="text-xs text-base-content/60">Send promotional newsletters</p>
                                </div>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="toggle toggle-primary" />
                                <div>
                                    <span class="label-text font-medium">Abandoned Cart</span>
                                    <p class="text-xs text-base-content/60">Send reminder for abandoned carts</p>
                                </div>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" class="toggle toggle-primary" />
                                <div>
                                    <span class="label-text font-medium">Product Recommendations</span>
                                    <p class="text-xs text-base-content/60">Send personalized product suggestions</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="iconify lucide--save size-4"></span>
                        Save Notification Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('customjs')
<script>
    // Add any custom JavaScript for test email functionality
</script>
@endsection