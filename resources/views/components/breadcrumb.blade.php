@props([
    'items' => [], // [['label' => 'Home', 'url' => '/'], ['label' => 'Customers']]
])

<div class="breadcrumbs hidden p-0 text-sm sm:inline">
    <ul>
        @foreach($items as $item)
        <li class="{{ $loop->last ? 'opacity-80' : '' }}">
            @if(isset($item['url']) && !$loop->last)
            <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
            @else
            {{ $item['label'] }}
            @endif
        </li>
        @endforeach
    </ul>
</div>
