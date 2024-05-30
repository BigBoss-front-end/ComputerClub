@props([
    'mb' => 2,
])

<div {{ $attributes->merge([
    'class' => "mb-$mb relative",
]) }}>
    {{ $slot }}
</div>
