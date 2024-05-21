<x-modal :modalId="'computer-edit'">



</x-modal>


<script>
    var allClients = {!! \App\Models\Client::all() !!};
</script>

<script id="computer-edit-modal" type="text/template">
    <x-form :id="'computer-edit-form'">
        <x-form-item>
            <x-form-label class="px-2">Имя</x-form-label>
            <x-form-input :type="'text'" :name="'name'" :value="'<%= computer.name %>'" class="bg-transparent text-white" oninput="debouncedChangeComputerName(<%= computer.id %>, this.value)"  />
            <x-form-error :name="'name'"></x-form-error>
        </x-form-item>
        <x-form-button :data="'data-submit-btn'" :type="'button'" onclick="deteteComputer(<%= computer.id %>, this.closest('form'))" class="mx-2">Удалить</x-form-button>
    </x-form>
</script>
