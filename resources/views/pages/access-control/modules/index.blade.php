@extends('layouts.app')

@section('title', 'Module Management')
@section('page_title', 'Access Control')
@section('page_subtitle', 'Module Management')

@section('content')
<x-page-header
    title="Module Management"
    :breadcrumbs="[
        ['label' => 'Nexus', 'url' => route('dashboard')],
        ['label' => 'Modules']
    ]"
/>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <x-stat-card
        title="Total Modules"
        :value="$statistics['total']"
        subtitle="All menu modules"
        icon="layout-grid"
        icon-color="primary"
    />

    <x-stat-card
        title="Active Modules"
        :value="$statistics['active']"
        subtitle="Currently active"
        icon="check-circle-2"
        icon-color="success"
    />

    <x-stat-card
        title="Visible in Menu"
        :value="$statistics['visible']"
        subtitle="Shown in sidebar"
        icon="eye"
        icon-color="info"
    />

    <x-stat-card
        title="Parent Modules"
        :value="$statistics['parents']"
        subtitle="Root level items"
        icon="folders"
        icon-color="warning"
    />
</div>

<!-- Save Button (Hidden by default) -->
<div id="saveOrderContainer" class="mb-4 mt-6 hidden">
    <div class="alert alert-warning">
        <span class="iconify lucide--info size-5"></span>
        <span>Order has been changed. Click Save to apply changes.</span>
        <button id="saveOrderBtn" class="btn btn-sm btn-primary">
            <span class="iconify lucide--save size-4"></span>
            Save Order
        </button>
    </div>
</div>

<!-- Modules Table -->
<div class="card bg-base-100 shadow-sm mt-6">
    <div class="card-body">
        <div class="flex items-center justify-between mb-4">
            <h2 class="card-title">Module Groups</h2>
            <div class="flex gap-2">
                <button id="collapseAllBtn" class="btn btn-ghost btn-sm">
                    <span class="iconify lucide--chevron-down size-4"></span>
                    Expand All
                </button>
                @if(hasPermission('access-control.modules.create'))
                <a href="{{ route('modules.create') }}" class="btn btn-primary btn-sm">
                    <span class="iconify lucide--plus size-4"></span>
                    Add Module
                </a>
                @endif
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th width="30"><span class="iconify lucide--grip-vertical size-4"></span></th>
                        <th width="30"></th>
                        <th>Name</th>
                        <th>Route</th>
                        <th>Component</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Visibility</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="sortableTable">
                    @forelse($groupedModules as $groupModule)
                        @php
                            $groupName = $groupModule->group_name ?? 'ungrouped';
                            // Get all modules in this group from cached data
                            $modulesInGroup = collect($allModules)
                                ->where('group_name', $groupName)
                                ->sortBy('sort_order')
                                ->values();
                            $moduleCount = $modulesInGroup->count();
                        @endphp

                        <!-- Group Header Row -->
                        <tr data-id="{{ $groupModule->id }}"
                            data-group="{{ $groupName }}"
                            class="sortable-row group-header hover:bg-base-200 bg-base-200/50 font-semibold cursor-pointer"
                            data-group-name="{{ $groupName }}">
                            <td>
                                <span class="iconify lucide--grip-vertical size-5 text-base-content/40 cursor-move group-drag-handle"></span>
                            </td>
                            <td>
                                <span class="iconify lucide--chevron-right size-5 text-base-content/60 transition-transform group-chevron"></span>
                            </td>
                            <td colspan="6">
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--folder size-5 text-primary"></span>
                                    <span class="font-semibold text-base">{{ ucwords(str_replace('_', ' ', $groupName)) }}</span>
                                    <span class="badge badge-primary badge-sm">{{ $moduleCount }} modules</span>
                                </div>
                            </td>
                            <td class="text-right" onclick="event.stopPropagation()">
                                <div class="flex justify-end gap-2">
                                    @if(hasPermission('access-control.modules.create'))
                                    <button class="btn btn-xs btn-ghost" title="Add module to this group">
                                        <span class="iconify lucide--plus size-3"></span>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Group Modules (Hidden by default) -->
                        @foreach($modulesInGroup as $module)
                            @php
                                // Check if this module has children using cached data
                                $isParent = collect($allModules)->where('parent_id', $module->id)->isNotEmpty();
                                $hasParent = $module->parent_id !== null;
                            @endphp

                            @if(!$hasParent)
                            <!-- Parent Module Row -->
                            <tr class="module-row hidden {{ $isParent ? 'cursor-pointer hover:bg-base-200' : '' }} sortable-module"
                                data-group-name="{{ $groupName }}"
                                data-module-id="{{ $module->id }}"
                                data-is-parent="{{ $isParent ? 'true' : 'false' }}">
                                <td>
                                    @php
                                        // Count parent modules in this group using cached data
                                        $parentModulesCount = collect($allModules)
                                            ->where('group_name', $groupName)
                                            ->whereNull('parent_id')
                                            ->count();
                                    @endphp
                                    @if($parentModulesCount > 1)
                                    <span class="iconify lucide--grip-vertical size-4 text-base-content/40 cursor-move module-drag-handle"></span>
                                    @endif
                                </td>
                                <td>
                                    @if($isParent)
                                    <span class="iconify lucide--chevron-right size-4 text-base-content/60 transition-transform module-chevron cursor-pointer"></span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center gap-2 ml-4">
                                        @if($module->icon)
                                            <span class="iconify {{ $module->icon }} size-4"></span>
                                        @endif
                                        <span>{{ $module->display_name }}</span>
                                    </div>
                                </td>
                                <td><span class="text-sm text-base-content/70">{{ $module->route ?: '-' }}</span></td>
                                <td><span class="text-sm text-base-content/70">{{ $module->component ?: '-' }}</span></td>
                                <td><span class="badge badge-ghost badge-sm">{{ $module->sort_order }}</span></td>
                                <td>
                                    @if(hasPermission('access-control.modules.update'))
                                    <form action="{{ route('modules.toggle-active', $module->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="badge {{ $module->is_active ? 'badge-success' : 'badge-error' }} badge-sm cursor-pointer">
                                            {{ $module->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                    @else
                                        <span class="badge {{ $module->is_active ? 'badge-success' : 'badge-error' }} badge-sm">
                                            {{ $module->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if(hasPermission('access-control.modules.update'))
                                    <form action="{{ route('modules.toggle-visible', $module->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="badge {{ $module->is_visible ? 'badge-success' : 'badge-error' }} badge-sm cursor-pointer">
                                            {{ $module->is_visible ? 'Visible' : 'Hidden' }}
                                        </button>
                                    </form>
                                    @else
                                        <span class="badge {{ $module->is_visible ? 'badge-success' : 'badge-error' }} badge-sm">
                                            {{ $module->is_visible ? 'Visible' : 'Hidden' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="flex justify-end gap-2">
                                        @if(hasPermission('access-control.modules.update'))
                                        <a href="{{ route('modules.edit', $module->id) }}" class="btn btn-sm btn-ghost">
                                            <span class="iconify lucide--pencil size-4"></span>
                                        </a>
                                        @endif

                                        @if(hasPermission('access-control.modules.delete'))
                                        <form action="{{ route('modules.destroy', $module->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-ghost text-error">
                                                <span class="iconify lucide--trash-2 size-4"></span>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            <!-- Child Modules (Hidden by default) -->
                            @php
                                // Get children from cached data
                                $children = collect($allModules)
                                    ->where('parent_id', $module->id)
                                    ->sortBy('sort_order')
                                    ->values();
                            @endphp
                            @foreach($children as $child)
                            <tr class="child-row hidden"
                                data-group-name="{{ $groupName }}"
                                data-parent-id="{{ $module->id }}">
                                <td></td>
                                <td></td>
                                <td>
                                    <div class="flex items-center gap-2 ml-8">
                                        <span class="text-base-content/40">└─</span>
                                        @if($child->icon)
                                            <span class="iconify {{ $child->icon }} size-4"></span>
                                        @endif
                                        <span>{{ $child->display_name }}</span>
                                    </div>
                                </td>
                                <td><span class="text-sm text-base-content/70">{{ $child->route ?: '-' }}</span></td>
                                <td><span class="text-sm text-base-content/70">{{ $child->component ?: '-' }}</span></td>
                                <td><span class="badge badge-ghost badge-sm">{{ $child->sort_order }}</span></td>
                                <td>
                                    @if(hasPermission('access-control.modules.update'))
                                    <form action="{{ route('modules.toggle-active', $child->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="badge {{ $child->is_active ? 'badge-success' : 'badge-error' }} badge-sm cursor-pointer">
                                            {{ $child->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                    @else
                                        <span class="badge {{ $child->is_active ? 'badge-success' : 'badge-error' }} badge-sm">
                                            {{ $child->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if(hasPermission('access-control.modules.update'))
                                    <form action="{{ route('modules.toggle-visible', $child->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="badge {{ $child->is_visible ? 'badge-success' : 'badge-error' }} badge-sm cursor-pointer">
                                            {{ $child->is_visible ? 'Visible' : 'Hidden' }}
                                        </button>
                                    </form>
                                    @else
                                        <span class="badge {{ $child->is_visible ? 'badge-success' : 'badge-error' }} badge-sm">
                                            {{ $child->is_visible ? 'Visible' : 'Hidden' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="flex justify-end gap-2">
                                        @if(hasPermission('access-control.modules.update'))
                                        <a href="{{ route('modules.edit', $child->id) }}" class="btn btn-sm btn-ghost">
                                            <span class="iconify lucide--pencil size-4"></span>
                                        </a>
                                        @endif

                                        @if(hasPermission('access-control.modules.delete'))
                                        <form action="{{ route('modules.destroy', $child->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-ghost text-error">
                                                <span class="iconify lucide--trash-2 size-4"></span>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8">
                                <div class="text-base-content/60">
                                    <span class="iconify lucide--badge-x size-12 mx-auto mb-2"></span>
                                    <p>No module groups found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('customjs')
@vite(['resources/js/modules/access-control/modules/index.js'])
@endsection
