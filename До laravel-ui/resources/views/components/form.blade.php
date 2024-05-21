@props([
    'px' => 2,
    'py' => 2,
    'bg' => 'gray',
    'id' => '',
    'data' => '',
])

<form class="submit-prevent-default px-{{$px}} py-{{$py}} bg-{{$bg}}-500" id="{{$id}}" data-form-id="{{Str::uuid()}}" {{$data}}>
    @csrf
    {{$slot}}
</form>