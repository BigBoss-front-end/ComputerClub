@props([
    'type' => 'submit',
    'data' => '',
    'id' => '',
    'btnId' => Str::uuid(),
])

<button type="{{ $type }}" id="{{ $id }}" {{ $data }}
    data-btn-id="{{ $btnId }}" {{$attributes->merge([
        'class' => 'px-2 py-2 text-white rounded-sm bg-gray-900 hover:bg-gray-800 active:bg-gray-950',
    ])}}>{{ $slot }}</button>
