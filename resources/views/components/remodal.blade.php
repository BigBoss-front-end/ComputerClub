@props([
    'modalId' => '',
])

<div {{$attributes->merge([
    'class' => 'remodal'
])}} data-remodal-id="{{ $modalId }}">
    
    <div class="">
        <button data-remodal-action="close" class="remodal-close"></button>
        <div data-modal-content>
            {{ $slot }}
        </div>
    </div>
</div>
