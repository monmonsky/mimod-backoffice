@props([
    'name' => '',
    'label' => '',
    'value' => '',
    'placeholder' => '',
    'rows' => 3,
    'required' => false,
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
    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        class="textarea textarea-bordered"
        {{ $required ? 'required' : '' }}
        {{ $attributes }}
    >{{ old($name, $value) }}</textarea>
</div>
