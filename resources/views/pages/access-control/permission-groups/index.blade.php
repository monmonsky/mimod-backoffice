@extends('layouts.app')

@section('title', 'Permission Groups')
@section('page_title', 'Permission Groups')
@section('page_subtitle', 'Manage permission groups')

@section('content')
<div class="container mx-auto p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold">Permission Group List</h2>
            <p class="text-base-content/60 text-sm mt-1">Group permissions for better organization</p>
        </div>
        <a href="{{ route('permission-group.create') }}" class="btn btn-primary">
            <span class="iconify lucide--plus size-5"></span>
            Add Group
        </a>
    </div>

    <!-- Groups Table -->
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Permissions Count</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groups as $group)
                            <tr>
                                <td><strong>{{ $group->name }}</strong></td>
                                <td>{{ $group->description ?: '-' }}</td>
                                <td>
                                    <span class="badge badge-primary">{{ $group->permissions_count ?? 0 }} permissions</span>
                                </td>
                                <td class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('permission-group.edit', $group->id) }}" class="btn btn-sm btn-ghost">
                                            <span class="iconify lucide--pencil size-4"></span>
                                        </a>
                                        <form action="{{ route('permission-group.destroy', $group->id) }}" method="POST" class="inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-ghost text-error">
                                                <span class="iconify lucide--trash-2 size-4"></span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8">
                                    <div class="text-base-content/60">
                                        <span class="iconify lucide--inbox size-12 mx-auto mb-2"></span>
                                        <p>No permission groups found</p>
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
@vite(['resources/js/modules/permission-groups/index.js'])
@endsection
