@props([
    'name' => '',
    'label' => '',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'size' => 'sm', // xs, sm, md, lg
])

<div class="form-control">
    @if($label)
    <label class="label">
        <span class="label-text">
            {{ $label }}
            @if($required)
            <span class="text-error">*</span>
            @endif
        </span>
    </label>
    @endif
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        class="input input-bordered input-{{ $size }} w-full"
        {{ $required ? 'required' : '' }}
        {{ $attributes }}
    />
</div>
