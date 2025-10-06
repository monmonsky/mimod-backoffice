@props([
    'type' => 'default', // default, success, error, warning, info, ghost, primary
    'label' => '',
    'size' => 'sm', // xs, sm, md, lg
])

@php
$typeClasses = [
    'default' => 'badge-default',
    'success' => 'badge-success',
    'error' => 'badge-error',
    'warning' => 'badge-warning',
    'info' => 'badge-info',
    'ghost' => 'badge-ghost',
    'primary' => 'badge-primary',
];

$badgeClass = $typeClasses[$type] ?? 'badge-default';
@endphp

<span class="badge {{ $badgeClass }} badge-{{ $size }}">{{ $label }}</span>
