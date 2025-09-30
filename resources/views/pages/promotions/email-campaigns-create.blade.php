@extends('layouts.app')

@section('title', 'Create Email Campaign')
@section('page_title', 'Promotions')
@section('page_subtitle', 'Create Email Campaign')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Create New Email Campaign</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li><a href="{{ route('promotions.email-campaigns') }}">Email Campaigns</a></li>
            <li class="opacity-80">Create</li>
        </ul>
    </div>
</div>

<!-- Campaign Form -->
<form action="#" method="POST">
    @csrf

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-base-100 card shadow">
                <div class="card-body">
                    <h3 class="card-title text-base mb-4">Basic Information</h3>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Campaign Name <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="campaign_name" placeholder="e.g., Weekend Flash Sale" class="input input-bordered" required />
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Internal name for your campaign</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Campaign Type <span class="text-error">*</span></span>
                        </label>
                        <select name="campaign_type" class="select select-bordered" required>
                            <option value="">Select campaign type</option>
                            <option value="promotional">Promotional</option>
                            <option value="newsletter">Newsletter</option>
                            <option value="transactional">Transactional</option>
                            <option value="abandoned_cart">Abandoned Cart</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Description</span>
                        </label>
                        <textarea name="description" rows="3" class="textarea textarea-bordered" placeholder="Brief description of this campaign..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Email Content -->
            <div class="bg-base-100 card shadow">
                <div class="card-body">
                    <h3 class="card-title text-base mb-4">Email Content</h3>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Email Template <span class="text-error">*</span></span>
                        </label>
                        <select name="template_id" class="select select-bordered" required>
                            <option value="">Select email template</option>
                            <option value="1">Flash Sale Promo</option>
                            <option value="2">Product Launch</option>
                            <option value="3">Abandoned Cart</option>
                            <option value="4">Welcome Email</option>
                        </select>
                        <label class="label">
                            <span class="label-text-alt">
                                <a href="{{ route('promotions.email-templates') }}" class="link link-primary">Manage templates</a>
                            </span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Subject Line <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="subject" placeholder="e.g., Don't miss our weekend flash sale!" class="input input-bordered" required />
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Keep it short and engaging (40-50 characters)</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Preview Text</span>
                        </label>
                        <input type="text" name="preview_text" placeholder="Text that appears after the subject line..." class="input input-bordered" />
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Optional preview text shown in inbox</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">From Name <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="from_name" value="Nexus Store" class="input input-bordered" required />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">From Email <span class="text-error">*</span></span>
                        </label>
                        <input type="email" name="from_email" value="noreply@nexusstore.com" class="input input-bordered" required />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Reply To Email</span>
                        </label>
                        <input type="email" name="reply_to" placeholder="support@nexusstore.com" class="input input-bordered" />
                    </div>
                </div>
            </div>

            <!-- Recipients -->
            <div class="bg-base-100 card shadow">
                <div class="card-body">
                    <h3 class="card-title text-base mb-4">Recipients</h3>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Send To <span class="text-error">*</span></span>
                        </label>
                        <select name="recipient_type" class="select select-bordered" required id="recipient-type">
                            <option value="">Select recipient type</option>
                            <option value="all">All Customers</option>
                            <option value="segment">Customer Segment</option>
                            <option value="specific">Specific Customers</option>
                            <option value="list">Import Email List</option>
                        </select>
                    </div>

                    <div class="form-control" id="segment-selector" style="display: none;">
                        <label class="label">
                            <span class="label-text font-medium">Select Segments</span>
                        </label>
                        <div class="space-y-2">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="segments[]" value="vip" class="checkbox checkbox-primary" />
                                <span>VIP Customers (892)</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="segments[]" value="frequent" class="checkbox checkbox-primary" />
                                <span>Frequent Buyers (1,854)</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="segments[]" value="regular" class="checkbox checkbox-primary" />
                                <span>Regular Customers (3,421)</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="segments[]" value="new" class="checkbox checkbox-primary" />
                                <span>New Customers (2,375)</span>
                            </label>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <span class="iconify lucide--info size-4"></span>
                        <div>
                            <p class="font-semibold text-sm">Estimated Recipients</p>
                            <p class="text-xs">Approximately <span class="font-bold">8,542</span> customers will receive this campaign</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scheduling -->
            <div class="bg-base-100 card shadow">
                <div class="card-body">
                    <h3 class="card-title text-base mb-4">Schedule & Send Options</h3>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">When to Send <span class="text-error">*</span></span>
                        </label>
                        <div class="space-y-2">
                            <label class="label cursor-pointer justify-start gap-3 border border-base-300 rounded-lg p-3">
                                <input type="radio" name="send_type" value="now" class="radio radio-primary" checked />
                                <div>
                                    <p class="font-medium">Send Now</p>
                                    <p class="text-xs text-base-content/60">Campaign will be sent immediately</p>
                                </div>
                            </label>
                            <label class="label cursor-pointer justify-start gap-3 border border-base-300 rounded-lg p-3">
                                <input type="radio" name="send_type" value="scheduled" class="radio radio-primary" />
                                <div>
                                    <p class="font-medium">Schedule for Later</p>
                                    <p class="text-xs text-base-content/60">Choose specific date and time</p>
                                </div>
                            </label>
                            <label class="label cursor-pointer justify-start gap-3 border border-base-300 rounded-lg p-3">
                                <input type="radio" name="send_type" value="draft" class="radio radio-primary" />
                                <div>
                                    <p class="font-medium">Save as Draft</p>
                                    <p class="text-xs text-base-content/60">Save for later without sending</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-control" id="schedule-datetime" style="display: none;">
                        <label class="label">
                            <span class="label-text font-medium">Schedule Date & Time</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <input type="date" name="schedule_date" class="input input-bordered" />
                            <input type="time" name="schedule_time" class="input input-bordered" />
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="track_opens" class="checkbox checkbox-primary" checked />
                            <div>
                                <span class="label-text font-medium">Track Opens</span>
                                <p class="text-xs text-base-content/60">Track when recipients open this email</p>
                            </div>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="track_clicks" class="checkbox checkbox-primary" checked />
                            <div>
                                <span class="label-text font-medium">Track Clicks</span>
                                <p class="text-xs text-base-content/60">Track link clicks in this email</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Preview -->
            <div class="bg-base-100 card shadow">
                <div class="card-body">
                    <h3 class="card-title text-base mb-4">Preview</h3>
                    <div class="bg-base-200 rounded-lg p-4 min-h-[200px] flex items-center justify-center">
                        <div class="text-center text-base-content/60">
                            <span class="iconify lucide--mail size-12 mb-2"></span>
                            <p class="text-sm">Select a template to preview</p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline btn-sm w-full mt-3">
                        <span class="iconify lucide--eye size-4"></span>
                        Full Preview
                    </button>
                </div>
            </div>

            <!-- Test Email -->
            <div class="bg-base-100 card shadow">
                <div class="card-body">
                    <h3 class="card-title text-base mb-4">Test Email</h3>
                    <p class="text-sm text-base-content/60 mb-3">Send a test email to check how it looks</p>
                    <div class="form-control">
                        <input type="email" placeholder="your@email.com" class="input input-bordered input-sm" />
                    </div>
                    <button type="button" class="btn btn-outline btn-sm w-full mt-3">
                        <span class="iconify lucide--send size-4"></span>
                        Send Test
                    </button>
                </div>
            </div>

            <!-- Campaign Summary -->
            <div class="bg-base-100 card shadow">
                <div class="card-body">
                    <h3 class="card-title text-base mb-4">Campaign Summary</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-base-content/60">Status:</span>
                            <span class="badge badge-warning badge-sm">Draft</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-base-content/60">Recipients:</span>
                            <span class="font-semibold">0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-base-content/60">Template:</span>
                            <span class="font-semibold">Not selected</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-base-content/60">Send Time:</span>
                            <span class="font-semibold">Immediately</span>
                        </div>
                    </div>

                    <div class="divider my-3"></div>

                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-sm">
                            <span class="iconify lucide--circle text-base-content/60 size-4"></span>
                            <span class="text-base-content/60">Campaign name</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <span class="iconify lucide--circle text-base-content/60 size-4"></span>
                            <span class="text-base-content/60">Email template</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <span class="iconify lucide--circle text-base-content/60 size-4"></span>
                            <span class="text-base-content/60">Recipients</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <span class="iconify lucide--circle text-base-content/60 size-4"></span>
                            <span class="text-base-content/60">Subject line</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-base-100 card shadow">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-full">
                        <span class="iconify lucide--send size-4"></span>
                        Create Campaign
                    </button>
                    <button type="button" class="btn btn-outline w-full">
                        <span class="iconify lucide--save size-4"></span>
                        Save as Draft
                    </button>
                    <a href="{{ route('promotions.email-campaigns') }}" class="btn btn-ghost w-full">
                        <span class="iconify lucide--x size-4"></span>
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('customjs')
<script>
    // Recipient type handler
    document.getElementById('recipient-type').addEventListener('change', function() {
        const segmentSelector = document.getElementById('segment-selector');
        if (this.value === 'segment') {
            segmentSelector.style.display = 'block';
        } else {
            segmentSelector.style.display = 'none';
        }
    });

    // Send type handler
    document.querySelectorAll('input[name="send_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const scheduleDiv = document.getElementById('schedule-datetime');
            if (this.value === 'scheduled') {
                scheduleDiv.style.display = 'block';
            } else {
                scheduleDiv.style.display = 'none';
            }
        });
    });
</script>
@endsection