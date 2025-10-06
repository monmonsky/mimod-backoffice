@props([
    'title' => '',
    'breadcrumbs' => [], // [['label' => 'Home', 'url' => '/'], ['label' => 'Customers']]
])

<div class="flex items-center justify-between">
    <p class="text-lg font-medium">{{ $title }}</p>
    @if(count($breadcrumbs) > 0)
    <x-breadcrumb :items="$breadcrumbs" />
    @endif
</div>
