<x-remodal class="p-0" :modalId="'computer-edit'">



</x-remodal>


<script>
    var allClients = {!! \App\Models\Client::all() !!};
</script>

<script id="computer-edit-modal" type="text/template">
    <x-form :id="'computer-edit-form'" :px="'10'" :py="'5'">
        <x-form-item class="max-w-52">
            <x-form-label class="">Имя</x-form-label>
            <x-form-input :type="'text'" :name="'name'" :value="'<%= computer.name %>'" class="" oninput="debouncedChangeComputerName(<%= computer.id %>, this.value)"  />
            <x-form-error :name="'name'"></x-form-error>
        </x-form-item>
        <x-link-primary class="text-right mt-4" onclick="deleteComputer(<%= computer.id %>, this.closest('form'))">
            Удалить
        </x-link-primary>
    </x-form>
</script>
