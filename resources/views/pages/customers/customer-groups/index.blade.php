@extends('layouts.app')

@section('title', 'Customer Groups')
@section('page_title', 'Customers')
@section('page_subtitle', 'Customer Groups')

@section('content')
<x-page-header
    title="Customer Groups"
    :breadcrumbs="[
        ['label' => 'Nexus', 'url' => route('dashboard')],
        ['label' => 'Customers'],
        ['label' => 'Customer Groups']
    ]"
/>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <x-stat-card
        title="Total Groups"
        :value="number_format($statistics->total_groups ?? 0)"
        subtitle="All groups"
        icon="users-round"
        icon-color="primary"
    />

    <x-stat-card
        title="Active Groups"
        :value="number_format($statistics->active_groups ?? 0)"
        subtitle="Currently active"
        icon="check-circle"
        icon-color="success"
        value-color="text-success"
    />

    <x-stat-card
        title="Total Members"
        :value="number_format($statistics->total_members ?? 0)"
        subtitle="Across all groups"
        icon="user-plus"
        icon-color="info"
        value-color="text-info"
    />

    <x-stat-card
        title="Avg Members/Group"
        :value="number_format($statistics->avg_members_per_group ?? 0, 1)"
        subtitle="Average size"
        icon="bar-chart-2"
        icon-color="warning"
        value-color="text-warning"
    />
</div>

<!-- Filter Section -->
<div class="mt-6">
    <x-filter-section title="Filter Groups" :action="route('customers.groups.index')">
        <x-slot name="headerAction">
            @if(hasPermission('customers.customer-groups.create'))
            <button type="button" class="btn btn-sm btn-primary" id="addGroupBtn">
                <span class="iconify lucide--plus size-4"></span>
                Add Group
            </button>
            @endif
        </x-slot>

        <x-slot name="filters">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <x-form.input
                    name="name"
                    label="Name"
                    :value="request('name')"
                    placeholder="Search by name"
                />

                <x-form.input
                    name="code"
                    label="Code"
                    :value="request('code')"
                    placeholder="Search by code"
                />

                <x-form.select
                    name="is_active"
                    label="Status"
                    :value="request('is_active')"
                    placeholder="All Status"
                    :options="['1' => 'Active', '0' => 'Inactive']"
                />
            </div>
        </x-slot>

        <x-slot name="actions">
            <button type="submit" class="btn btn-sm btn-primary">
                <span class="iconify lucide--search size-4"></span>
                Apply Filter
            </button>
            @if(request()->hasAny(['name', 'code', 'is_active']))
            <a href="{{ route('customers.groups.index') }}" class="btn btn-sm btn-ghost">
                <span class="iconify lucide--x size-4"></span>
                Clear All
            </a>
            @endif
        </x-slot>
    </x-filter-section>
</div>

<!-- Groups Table -->
<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra" id="groupsTable">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Members</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groups as $group)
                        <tr>
                            <td>
                                <span class="font-mono text-sm">{{ $group->code }}</span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    @if($group->color)
                                    <span class="badge badge-sm" style="background-color: {{ $group->color }}; color: white;">
                                        {{ $group->name }}
                                    </span>
                                    @else
                                    <span class="font-medium">{{ $group->name }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="text-sm text-base-content/70">{{ Str::limit($group->description, 50) ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="font-medium">{{ number_format($group->member_count) }}</span>
                            </td>
                            <td>
                                <x-badge
                                    :type="$group->is_active ? 'success' : 'error'"
                                    :label="$group->is_active ? 'Active' : 'Inactive'"
                                />
                            </td>
                            <td>
                                <span class="text-sm">{{ \Carbon\Carbon::parse($group->created_at)->format('d M Y') }}</span>
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    @if(hasPermission('customers.customer-groups.view'))
                                    <a href="{{ route('customers.customer-groups.members', $group->id) }}" class="btn btn-ghost btn-xs">
                                        <span class="iconify lucide--users size-4"></span>
                                        Members
                                    </a>
                                    @endif

                                    @if(hasPermission('customers.customer-groups.update'))
                                    <button type="button" class="btn btn-ghost btn-xs edit-group-btn" data-id="{{ $group->id }}">
                                        <span class="iconify lucide--pencil size-4"></span>
                                        Edit
                                    </button>
                                    @endif

                                    @if(hasPermission('customers.customer-groups.delete'))
                                    <button type="button" class="btn btn-ghost btn-xs text-error delete-group-btn" data-id="{{ $group->id }}">
                                        <span class="iconify lucide--trash-2 size-4"></span>
                                        Delete
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-base-content/60">
                                <span class="iconify lucide--users-round size-12 mx-auto block mb-2 opacity-20"></span>
                                No groups found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Info & Links -->
            <x-pagination-info :paginator="$groups" />
        </div>
    </div>
</div>

<!-- Add/Edit Group Modal -->
<x-modal id="groupModal" title="Add Group">
    <form id="groupForm">
        <input type="hidden" id="group_id">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-form.input
                name="name"
                label="Group Name"
                required
                placeholder="e.g., Wholesale Customers"
            />

            <x-form.input
                name="code"
                label="Code"
                required
                placeholder="e.g., WHOLESALE"
            />

            <x-form.input
                name="color"
                label="Color"
                type="color"
                placeholder="#000000"
            />

            <div class="md:col-span-1">
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-2">
                        <input type="checkbox" name="is_active" class="checkbox checkbox-primary" checked />
                        <span class="label-text">Active</span>
                    </label>
                </div>
            </div>

            <div class="md:col-span-2">
                <x-form.textarea
                    name="description"
                    label="Description"
                    :rows="3"
                    placeholder="Describe this group"
                />
            </div>
        </div>

        <div class="modal-action">
            <button type="button" class="btn btn-ghost" onclick="document.getElementById('groupModal').close()">Cancel</button>
            <button type="submit" class="btn btn-primary">
                <span class="iconify lucide--save size-4"></span>
                Save Group
            </button>
        </div>
    </form>
</x-modal>
@endsection

@vite(['resources/js/modules/customers/customer-groups/index.js'])
