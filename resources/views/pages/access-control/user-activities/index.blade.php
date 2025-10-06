@extends('layouts.app')

@section('title', 'User Activities')
@section('page_title', 'Access Control')
@section('page_subtitle', 'User Activities')

@section('content')
<x-page-header
    title="User Activities"
    :breadcrumbs="[
        ['label' => 'Nexus', 'url' => route('dashboard')],
        ['label' => 'User Activities']
    ]"
/>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <x-stat-card
        title="Total Activities"
        :value="number_format($statistics['total'])"
        subtitle="All user actions"
        icon="heart-pulse"
        icon-color="primary"
    />

    <x-stat-card
        title="Today"
        :value="number_format($statistics['today'])"
        subtitle="Activities today"
        icon="calendar-days"
        icon-color="success"
    />

    <x-stat-card
        title="This Week"
        :value="number_format($statistics['this_week'])"
        subtitle="Last 7 days"
        icon="calendar-range"
        icon-color="info"
    />

    <x-stat-card
        title="This Month"
        :value="number_format($statistics['this_month'])"
        subtitle="Current month"
        icon="calendar"
        icon-color="warning"
    />
</div>

<!-- Filters -->
<div class="mt-6">
    <x-filter-section
        title="Filters"
        :action="route('access-control.user-activities.index')">

        <x-slot name="headerAction">
            <div class="flex gap-2">
                <button id="export-logs-btn" class="btn btn-outline btn-sm">
                    <span class="iconify lucide--download size-4"></span>
                    Export CSV
                </button>
                @if(hasPermission('access-control.user-activities.clear'))
                <button id="clear-logs-btn" class="btn btn-error btn-sm">
                    <span class="iconify lucide--trash-2 size-4"></span>
                    Clear All Logs
                </button>
                @endif
            </div>
        </x-slot>

        <x-slot name="filters">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <x-form.input
                    name="search"
                    label="Search"
                    :value="$filters['search'] ?? ''"
                    placeholder="User name, email, or description..."
                />

                <x-form.select
                    name="action"
                    label="Action"
                    :value="$filters['action'] ?? ''"
                    placeholder="All Actions"
                    :options="[
                        'login' => 'Login',
                        'logout' => 'Logout',
                        'create' => 'Create',
                        'update' => 'Update',
                        'delete' => 'Delete',
                        'view' => 'View',
                        'export' => 'Export'
                    ]"
                />

                <x-form.select
                    name="subject_type"
                    label="Subject Type"
                    :value="$filters['subject_type'] ?? ''"
                    placeholder="All Types"
                    :options="[
                        'User' => 'User',
                        'Role' => 'Role',
                        'Permission' => 'Permission',
                        'Module' => 'Module',
                        'Settings' => 'Settings'
                    ]"
                />

                <x-form.input
                    type="date"
                    name="date_from"
                    label="Date From"
                    :value="$filters['date_from'] ?? ''"
                />

                <x-form.input
                    type="date"
                    name="date_to"
                    label="Date To"
                    :value="$filters['date_to'] ?? ''"
                />

                <x-form.select
                    name="per_page"
                    label="Per Page"
                    :value="$filters['per_page'] ?? 20"
                    :options="[
                        20 => '20',
                        50 => '50',
                        100 => '100'
                    ]"
                />
            </div>
        </x-slot>

        <x-slot name="actions">
            <button type="submit" class="btn btn-primary btn-sm">
                <span class="iconify lucide--search size-4"></span>
                Apply Filters
            </button>
            <button type="button" id="reset-filters" class="btn btn-ghost btn-sm">
                <span class="iconify lucide--x size-4"></span>
                Reset
            </button>
        </x-slot>
    </x-filter-section>
</div>

<!-- Activities Table -->
<div class="card bg-base-100 shadow-sm mt-6">
    <div class="card-body">
        <h2 class="card-title text-base mb-4">Activity List</h2>
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Subject</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th>Date</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $index => $activity)
                    <tr>
                        <td>{{ $activities->firstItem() + $index }}</td>
                        <td>
                            <div>
                                <div class="font-medium text-sm">{{ $activity->user_name }}</div>
                                <div class="text-xs text-base-content/60">{{ $activity->user_email }}</div>
                            </div>
                        </td>
                        <td>
                            @php
                                $actionColors = [
                                    'login' => 'success',
                                    'logout' => 'info',
                                    'create' => 'primary',
                                    'update' => 'warning',
                                    'delete' => 'error',
                                    'view' => 'ghost',
                                    'export' => 'secondary',
                                ];
                                $color = $actionColors[$activity->action] ?? 'ghost';
                            @endphp
                            <x-badge :type="$color" :label="$activity->action" />
                        </td>
                        <td>
                            @if($activity->subject_type)
                                <div class="text-sm">{{ $activity->subject_type }}</div>
                                @if($activity->subject_id)
                                    <div class="text-xs text-base-content/60">#{{ $activity->subject_id }}</div>
                                @endif
                            @else
                                <span class="text-base-content/40">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="max-w-xs truncate" title="{{ $activity->description }}">
                                {{ $activity->description ?? '-' }}
                            </div>
                        </td>
                        <td>
                            <span class="text-xs font-mono">{{ $activity->ip_address ?? '-' }}</span>
                        </td>
                        <td>
                            <div class="text-sm">{{ \Carbon\Carbon::parse($activity->created_at)->format('Y-m-d') }}</div>
                            <div class="text-xs text-base-content/60">{{ \Carbon\Carbon::parse($activity->created_at)->format('H:i:s') }}</div>
                        </td>
                        <td class="text-right">
                            <button onclick="viewActivityDetail({{ $activity->id }})"
                                class="btn btn-sm btn-ghost">
                                <span class="iconify lucide--eye size-4"></span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8">
                            <div class="text-base-content/60">
                                <span class="iconify lucide--inbox size-12 mx-auto mb-2"></span>
                                <p>No activity logs found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <x-pagination-info :paginator="$activities" />
    </div>
</div>

<!-- Activity Detail Modal -->
<x-modal id="activity_detail_modal" title="Activity Details" size="max-w-2xl">
    <div class="space-y-3">
        <!-- User & Action -->
        <div class="card border border-base-300">
            <div class="card-body p-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p id="detail-user" class="font-medium">-</p>
                        <p id="detail-email" class="text-sm text-base-content/60">-</p>
                    </div>
                    <div id="detail-action-badge" class="badge">-</div>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="card border border-base-300">
            <div class="card-body p-3">
                <label class="text-xs font-semibold text-base-content/60 mb-1 block">Description</label>
                <p id="detail-description" class="text-sm">-</p>
            </div>
        </div>

        <!-- Subject & Date -->
        <div class="grid grid-cols-2 gap-3">
            <div class="card border border-base-300">
                <div class="card-body p-3">
                    <label class="text-xs font-semibold text-base-content/60 mb-1 block">Subject</label>
                    <p id="detail-subject" class="text-sm">-</p>
                </div>
            </div>
            <div class="card border border-base-300">
                <div class="card-body p-3">
                    <label class="text-xs font-semibold text-base-content/60 mb-1 block">Date & Time</label>
                    <p id="detail-date" class="text-sm">-</p>
                </div>
            </div>
        </div>

        <!-- IP & User Agent -->
        <div class="grid grid-cols-2 gap-3">
            <div class="card border border-base-300">
                <div class="card-body p-3">
                    <label class="text-xs font-semibold text-base-content/60 mb-1 block">IP Address</label>
                    <p id="detail-ip" class="text-sm font-mono">-</p>
                </div>
            </div>
            <div class="card border border-base-300">
                <div class="card-body p-3">
                    <label class="text-xs font-semibold text-base-content/60 mb-1 block">User Agent</label>
                    <p id="detail-user-agent" class="text-xs font-mono break-all">-</p>
                </div>
            </div>
        </div>

        <!-- Additional Properties -->
        <div class="card border border-base-300">
            <div class="card-body p-3">
                <label class="text-xs font-semibold text-base-content/60 mb-2 block">Additional Properties</label>
                <div id="detail-properties" class="text-sm">
                    <p class="text-base-content/60">No additional data</p>
                </div>
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

@vite(['resources/js/modules/access-control/user-activities/index.js'])
