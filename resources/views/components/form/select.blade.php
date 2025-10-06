@props([
    'name' => '',
    'label' => '',
    'value' => '',
    'options' => [], // ['value' => 'label'] or [['value' => '1', 'label' => 'Option 1']]
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
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        class="select select-bordered select-{{ $size }} w-full"
        {{ $required ? 'required' : '' }}
        {{ $attributes }}
    >
        @if($placeholder)
        <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $optValue => $optLabel)
            @if(is_array($optLabel))
                <option value="{{ $optLabel['value'] }}" {{ old($name, $value) == $optLabel['value'] ? 'selected' : '' }}>
                    {{ $optLabel['label'] }}
                </option>
            @else
                <option value="{{ $optValue }}" {{ old($name, $value) == $optValue ? 'selected' : '' }}>
                    {{ $optLabel }}
                </option>
            @endif
        @endforeach
    </select>
</div>
