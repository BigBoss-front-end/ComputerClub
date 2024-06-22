@props([
    'type' => 'text',
    'name' => '',
    'id' => '',
    'placeholder' => '',
    'attr' => '',
    'data' => '',
    'value' => '',
])

<input {{$attributes->merge([
    'class' => 'px-2 py-2 rounded-sm outline-none'
])}} type="{{ $type }}" name="{{ $name }}"
    value="{!! $value !!}" id="{{ $id }}" placeholder="{{ $placeholder }}" {{ $attr }}
    {{ $data }} data-input-id="{{ Str::uuid(); }}" autocomplete="off">
