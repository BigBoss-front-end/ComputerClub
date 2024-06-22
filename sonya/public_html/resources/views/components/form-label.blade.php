@props([
    'for' => '',
    'mb' => 1,
])

<label for="{{ $for }}" {{ $attributes->merge([
    'class' => 'block text-white mb-' . $mb . '',
]) }}>{{ $slot }}</label>
