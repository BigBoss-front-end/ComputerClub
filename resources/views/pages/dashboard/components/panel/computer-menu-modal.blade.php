<x-remodal class="p-10" :modalId="'computer-menu'">
    <div class="flex flex-col flex-wrap justify-center items-stretch gap-2 px-2">
        <x-button-primary class="" data-remodal-action="close" data-computer-menu-button onclick="openComputerManageModal(this.getAttribute('data-id'))" data-id="">Расписание</x-button-primary>
        <x-button-primary class="" data-remodal-action="close" data-computer-menu-button onclick="openComputerEditModal(this.getAttribute('data-id'))" data-id="">Редактировать</x-button-primary>
    </div>
</x-remodal>