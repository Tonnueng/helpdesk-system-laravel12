@props([
    'name',
    'type' => 'text',
    'label',
    'value' => '',
    'required' => false,
    'autocomplete' => null,
    'placeholder' => null,
    'error' => null
])

@php
$inputClasses = 'block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm';
$labelClasses = 'block font-medium text-sm text-gray-700';
$errorClasses = 'mt-2 text-sm text-red-600';
@endphp

<div>
    <label for="{{ $name }}" class="{{ $labelClasses }}">
        {{ $label }}
    </label>
    
    <input 
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ old($name, $value) }}"
        class="{{ $inputClasses }} @error($name) border-red-500 @enderror"
        @if($required) required @endif
        @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
    />
    
    @error($name)
        <div class="{{ $errorClasses }}">{{ $message }}</div>
    @enderror
</div>
