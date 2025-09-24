@props([
    'name',
    'label',
    'value' => '1',
    'checked' => false
])

@php
$checkboxClasses = 'rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500';
$labelClasses = 'ml-2 text-sm text-gray-600';
@endphp

<div class="flex items-center">
    <input 
        type="checkbox" 
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ $value }}"
        class="{{ $checkboxClasses }}"
        @if(old($name, $checked)) checked @endif
    />
    <label for="{{ $name }}" class="{{ $labelClasses }}">
        {{ $label }}
    </label>
</div>
