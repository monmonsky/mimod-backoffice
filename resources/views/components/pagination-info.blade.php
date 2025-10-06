@props([
    'paginator' => null,
    'paginationView' => 'vendor.pagination.simple-custom',
])

@if($paginator && method_exists($paginator, 'total') && $paginator->total() > 0)
<div class="border-t border-base-300 px-5 py-4">
    <div class="flex flex-col items-center gap-3">
        <div class="text-sm text-base-content/70">
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
        </div>
        @if($paginator->hasPages())
        <div>
            {{ $paginator->links($paginationView) }}
        </div>
        @endif
    </div>
</div>
@endif
