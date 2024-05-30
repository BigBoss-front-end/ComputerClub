@props([
    'modalId' => '',
])

<div class="modal bg-gray-500 w-full h-full fixed left-0" data-modal data-modal-id="{{ $modalId }}">
    <div class="container mx-auto relative">
        <button data-modal-close class="modal-close absolute top-2 right-2"></button>
        <div data-modal-content>
            {{ $slot }}
        </div>
    </div>
</div>
