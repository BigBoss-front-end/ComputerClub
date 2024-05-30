@props([
    'name' => '',
])

<div data-form-error data-name="{{ $name }}" {{ $attributes->merge([
    'class' => 'text-sm text-red-500 max-w-full break-all',
]) }}></div>
