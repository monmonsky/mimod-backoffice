@props([
    'id' => '',
    'title' => '',
    'size' => 'max-w-2xl', // max-w-sm, max-w-md, max-w-lg, max-w-2xl, max-w-4xl, max-w-6xl
])

<dialog id="{{ $id }}" class="modal">
    <div class="modal-box {{ $size }}">
        @if($title)
        <h3 class="font-bold text-lg mb-4">{{ $title }}</h3>
        @endif

        <div class="modal-body">
            {{ $slot }}
        </div>

        @isset($footer)
        <div class="modal-action">
            {{ $footer }}
        </div>
        @endisset
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>
