@props([
    'name' => '',
    'id' => '',
    'placeholder' => '',
    'attr' => '',
    'data' => '',
    'value' => '',
])

<select class="px-2 py-2 rounded-sm outline-none" name="{{ $name }}"
    value="{!! $value !!}" id="{{ $id }}" placeholder="{{ $placeholder }}" {{ $attr }}
    {{ $data }} data-input-id="{{ Str::uuid(); }}" autocomplete="off">
    {{$slot}}
</select>