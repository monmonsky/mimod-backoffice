@props([
    'title' => 'Filter',
    'action' => '',
    'method' => 'GET',
    'id' => 'filterForm',
])

<div class="bg-base-100 card shadow">
    <div class="card-body">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-lg">{{ $title }}</h3>
            @isset($headerAction)
            {{ $headerAction }}
            @endisset
        </div>

        <form method="{{ $method }}" action="{{ $action }}" id="{{ $id }}">
            @if($method !== 'GET')
            @csrf
            @method($method)
            @endif

            @isset($filters)
            {{ $filters }}
            @endisset

            @isset($actions)
            <div class="flex items-center gap-3 mt-4">
                {{ $actions }}
            </div>
            @endisset
        </form>
    </div>
</div>
