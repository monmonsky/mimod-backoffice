@props([
    'title' => '',
    'value' => 0,
    'subtitle' => '',
    'icon' => 'info',
    'iconColor' => 'primary',
    'valueColor' => '',
])

<div class="card bg-base-100 shadow">
    <div class="card-body p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-base-content/70">{{ $title }}</p>
                <p class="text-2xl font-semibold mt-1 {{ $valueColor }}">{{ $value }}</p>
                @if($subtitle)
                <p class="text-xs text-base-content/60 mt-1">{{ $subtitle }}</p>
                @endif
            </div>
            <div class="bg-base-200 rounded-box flex items-center p-2">
                <span class="iconify lucide--{{ $icon }} size-5"></span>
            </div>
        </div>
    </div>
</div>
