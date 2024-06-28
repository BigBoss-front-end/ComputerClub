@props([
    'name' => '',
    'maxW' => 'full',
])

<div data-form-error data-name="{{ $name }}" {{ $attributes->merge([
    'class' => "hidden text-sm text-red-500 max-w-$maxW break-all",
]) }}></div>
