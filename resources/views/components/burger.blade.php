@props([
    'color' => 'black',
    'hover' => 'black-500',
    'active' => 'black-600'
])

<div {{$attributes->merge([
    'class' => 'cursor-pointer group'
])}} class="cursor-pointer">
    <div class="mb-1 w-8 h-1 bg-{{ $color }} group-hover:{{ $hover }} group-active:{{ $active }} rounded-md"></div>
    <div class="mb-1 w-8 h-1 bg-{{ $color }} group-hover:{{ $hover }} group-active:{{ $active }} rounded-md"></div>
    <div class="w-8 h-1 bg-{{ $color }} group-hover:{{ $hover }} group-active:{{ $active }} rounded-md"></div>
</div>