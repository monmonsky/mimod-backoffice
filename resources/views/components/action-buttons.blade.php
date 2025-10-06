@props([
    'id' => '',
    'viewPermission' => '',
    'editPermission' => '',
    'deletePermission' => '',
    'onView' => '',
    'onEdit' => '',
    'onDelete' => '',
    'showView' => true,
    'showEdit' => true,
    'showDelete' => true,
])

<div class="flex gap-1">
    @if($showView && (!$viewPermission || hasPermission($viewPermission)))
    <button class="btn btn-xs btn-ghost" onclick="{{ $onView }}({{ $id }})" title="View">
        <span class="iconify lucide--eye size-4"></span>
    </button>
    @endif

    @if($showEdit && (!$editPermission || hasPermission($editPermission)))
    <button class="btn btn-xs btn-ghost" onclick="{{ $onEdit }}({{ $id }})" title="Edit">
        <span class="iconify lucide--pencil size-4"></span>
    </button>
    @endif

    @if($showDelete && (!$deletePermission || hasPermission($deletePermission)))
    <button class="btn btn-xs btn-ghost text-error" onclick="{{ $onDelete }}({{ $id }}, event)" title="Delete">
        <span class="iconify lucide--trash size-4"></span>
    </button>
    @endif
</div>
