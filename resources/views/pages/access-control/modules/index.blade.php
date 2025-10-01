@extends('layouts.app')

@section('title', 'Module Management')
@section('page_title', 'Module Management')
@section('page_subtitle', 'Manage sidebar menu modules')

@section('content')
<div class="container mx-auto p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold">Module List</h2>
            <p class="text-base-content/60 text-sm mt-1">Manage all sidebar menu modules</p>
        </div>
        @if(hasPermission('access-control.modules.create'))
        <a href="{{ route('modules.create') }}" class="btn btn-primary">
            <span class="iconify lucide--plus size-5"></span>
            Add Module
        </a>
        @endif
    </div>

    <!-- Save Button (Hidden by default) -->
    <div id="saveOrderContainer" class="mb-4 hidden">
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
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th width="30"><span class="iconify lucide--grip-vertical size-4"></span></th>
                            <th>Icon</th>
                            <th>Name</th>
                            <th>Display Name</th>
                            <th>Route</th>
                            <th>Parent</th>
                            <th>Order</th>
                            <th>Active</th>
                            <th>Visible</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sortableTable">
                        @forelse($modules as $module)
                            <tr data-id="{{ $module->id }}" class="sortable-row">
                                <td class="cursor-move">
                                    <span class="iconify lucide--grip-vertical size-5 text-base-content/40"></span>
                                </td>
                                <td>
                                    @if($module->icon)
                                        <span class="iconify {{ $module->icon }} size-5"></span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $module->name }}</td>
                                <td>{{ $module->display_name }}</td>
                                <td>{{ $module->route ?: '-' }}</td>
                                <td>-</td>
                                <td><span class="badge badge-ghost">{{ $module->sort_order }}</span></td>
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
                            @if(isset($module->children) && count($module->children) > 0)
                                @foreach($module->children as $child)
                                    <tr data-id="{{ $child->id }}" data-parent-id="{{ $module->id }}" class="child-row">
                                        <td></td>
                                        <td>
                                            @if($child->icon)
                                                <span class="iconify {{ $child->icon }} size-5"></span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td><span class="inline-block ml-4">└─</span> {{ $child->name }}</td>
                                        <td>{{ $child->display_name }}</td>
                                        <td>{{ $child->route ?: '-' }}</td>
                                        <td>{{ $module->display_name }}</td>
                                        <td><span class="badge badge-ghost">{{ $child->sort_order }}</span></td>
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
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-8">
                                    <div class="text-base-content/60">
                                        <span class="iconify lucide--inbox size-12 mx-auto mb-2"></span>
                                        <p>No modules found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customjs')
@vite(['resources/js/modules/modules/index.js'])
@endsection

