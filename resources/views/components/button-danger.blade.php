@props([
    'type' => 'submit',
    'data' => '',
    'id' => '',
    'btnId' => Str::uuid(),
])

<button type="{{ $type }}" id="{{ $id }}" {{ $data }}
    data-btn-id="{{ $btnId }}" {{$attributes->merge([
        'class' => 'px-2 py-2 text-white rounded-sm bg-red-700 hover:bg-red-800 active:bg-red-900',
    ])}}>{{ $slot }}</button>
