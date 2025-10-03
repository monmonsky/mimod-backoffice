@extends('layouts.app')

@section('title', 'User Activities')
@section('page_title', 'Access Control')
@section('page_subtitle', 'User Activities')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">User Activities</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li class="opacity-80">User Activities</li>
        </ul>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <!-- Total Activities -->
    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Total Activities</p>
                    <p class="text-2xl font-semibold mt-1">{{ number_format($statistics['total']) }}</p>
                    <p class="text-xs text-base-content/60 mt-1">All user actions</p>
                </div>
                <div class="bg-primary/10 p-3 rounded-lg">
                    <span class="iconify lucide--activity size-5 text-primary"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Today -->
    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">Today</p>
                    <p class="text-2xl font-semibold mt-1">{{ number_format($statistics['today']) }}</p>
                    <p class="text-xs text-base-content/60 mt-1">Activities today</p>
                </div>
                <div class="bg-success/10 p-3 rounded-lg">
                    <span class="iconify lucide--calendar-days size-5 text-success"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- This Week -->
    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">This Week</p>
                    <p class="text-2xl font-semibold mt-1">{{ number_format($statistics['this_week']) }}</p>
                    <p class="text-xs text-base-content/60 mt-1">Last 7 days</p>
                </div>
                <div class="bg-info/10 p-3 rounded-lg">
                    <span class="iconify lucide--calendar-range size-5 text-info"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- This Month -->
    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/70">This Month</p>
                    <p class="text-2xl font-semibold mt-1">{{ number_format($statistics['this_month']) }}</p>
                    <p class="text-xs text-base-content/60 mt-1">Current month</p>
                </div>
                <div class="bg-warning/10 p-3 rounded-lg">
                    <span class="iconify lucide--calendar size-5 text-warning"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="card bg-base-100 shadow-sm mt-6">
    <div class="card-body">
        <div class="flex items-center justify-between mb-8">
            <h2 class="card-title text-base">
                <span class="iconify lucide--filter size-4"></span>
                Filters
            </h2>
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
        </div>

        <form id="filter-form" method="GET" action="{{ route('access-control.user-activities.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium mb-2">Search</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                        placeholder="User name, email, or description..."
                        class="input input-bordered w-full">
                </div>

                <!-- Action -->
                <div>
                    <label class="block text-sm font-medium mb-2">Action</label>
                    <select name="action" class="select select-bordered w-full">
                        <option value="">All Actions</option>
                        <option value="login" {{ ($filters['action'] ?? '') === 'login' ? 'selected' : '' }}>Login</option>
                        <option value="logout" {{ ($filters['action'] ?? '') === 'logout' ? 'selected' : '' }}>Logout</option>
                        <option value="create" {{ ($filters['action'] ?? '') === 'create' ? 'selected' : '' }}>Create</option>
                        <option value="update" {{ ($filters['action'] ?? '') === 'update' ? 'selected' : '' }}>Update</option>
                        <option value="delete" {{ ($filters['action'] ?? '') === 'delete' ? 'selected' : '' }}>Delete</option>
                        <option value="view" {{ ($filters['action'] ?? '') === 'view' ? 'selected' : '' }}>View</option>
                        <option value="export" {{ ($filters['action'] ?? '') === 'export' ? 'selected' : '' }}>Export</option>
                    </select>
                </div>

                <!-- Subject Type -->
                <div>
                    <label class="block text-sm font-medium mb-2">Subject Type</label>
                    <select name="subject_type" class="select select-bordered w-full">
                        <option value="">All Types</option>
                        <option value="User" {{ ($filters['subject_type'] ?? '') === 'User' ? 'selected' : '' }}>User</option>
                        <option value="Role" {{ ($filters['subject_type'] ?? '') === 'Role' ? 'selected' : '' }}>Role</option>
                        <option value="Permission" {{ ($filters['subject_type'] ?? '') === 'Permission' ? 'selected' : '' }}>Permission</option>
                        <option value="Module" {{ ($filters['subject_type'] ?? '') === 'Module' ? 'selected' : '' }}>Module</option>
                        <option value="Settings" {{ ($filters['subject_type'] ?? '') === 'Settings' ? 'selected' : '' }}>Settings</option>
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium mb-2">Date From</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                        class="input input-bordered w-full">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium mb-2">Date To</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                        class="input input-bordered w-full">
                </div>

                <!-- Per Page -->
                <div>
                    <label class="block text-sm font-medium mb-2">Per Page</label>
                    <select name="per_page" class="select select-bordered w-full">
                        <option value="20" {{ ($filters['per_page'] ?? 20) == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ ($filters['per_page'] ?? 20) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ ($filters['per_page'] ?? 20) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary btn-sm">
                    <span class="iconify lucide--search size-4"></span>
                    Apply Filters
                </button>
                <button type="button" id="reset-filters" class="btn btn-ghost btn-sm">
                    <span class="iconify lucide--x size-4"></span>
                    Reset
                </button>
            </div>
        </form>
    </div>
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
                            <span class="badge badge-{{ $color }} badge-sm">{{ $activity->action }}</span>
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
        <div class="mt-6">
            {{ $activities->appends(request()->except('page'))->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>

<!-- Activity Detail Modal -->
<dialog id="activity_detail_modal" class="modal">
    <div class="modal-box max-w-2xl">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>

        <h3 class="font-bold text-lg mb-4">Activity Details</h3>

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

        <div class="modal-action">
            <form method="dialog">
                <button class="btn btn-sm">Close</button>
            </form>
        </div>
    </div>
</dialog>
@endsection

@section('customjs')
@vite(['resources/js/modules/access-control/user-activities/index.js'])
@endsection
