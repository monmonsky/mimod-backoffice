@props([
    'columns' => [],
    'data' => [],
    'actions' => true,
    'emptyMessage' => 'No data found',
    'emptyIcon' => 'inbox',
])

<div class="overflow-x-auto">
    <table class="table table-zebra">
        <thead>
            <tr>
                @foreach($columns as $column)
                <th>{{ $column['label'] ?? $column }}</th>
                @endforeach
                @if($actions)
                <th>Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
            <tr>
                {{ $slot }}
            </tr>
            @empty
            <tr>
                <td colspan="{{ count($columns) + ($actions ? 1 : 0) }}" class="text-center py-8 text-base-content/60">
                    <span class="iconify lucide--{{ $emptyIcon }} size-12 mx-auto block mb-2 opacity-20"></span>
                    {{ $emptyMessage }}
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
