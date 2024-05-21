<x-modal :modalId="'computer-menu'">

    <div class="flex flex-col flex-wrap justify-center items-stretch gap-2 py-20 px-2">
        <x-form-button data-computer-menu-button onclick="openComputerManageModal(this.getAttribute('data-id'))" data-id="">Расписание</x-form-button>
        <x-form-button data-computer-menu-button onclick="openComputerEditModal(this.getAttribute('data-id'))" data-id="">Редактировать</x-form-button>
    </div>
    
</x-modal>