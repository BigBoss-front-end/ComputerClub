@props([
    'type' => 'submit',
    'data' => '',
    'id' => '',
    'btnId' => Str::uuid(),
])

<button type="{{ $type }}" id="{{ $id }}" {{ $data }}
    data-btn-id="{{ $btnId }}" {{$attributes->merge([
        'class' => 'px-2 py-2 text-gray-900 rounded-sm bg-gray-300 hover:bg-gray-200 active:bg-gray-400',
    ])}}>{{ $slot }}</button>
