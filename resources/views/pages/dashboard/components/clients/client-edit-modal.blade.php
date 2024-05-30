<x-remodal class="p-0" :modalId="'client-edit'">

</x-remodal>


<script>
    var allClients = {!! \App\Models\Client::all() !!};
</script>

<script id="computer-edit-modal-template" type="text/template">
    <x-form :id="'client-edit-form'" :px="'10'" :py="'5'">
        <input type="hidden" name="id" value="<%= client.id  %>">
        <x-form-item class="max-w-52">
            <x-form-label class="">Имя</x-form-label>
            <x-form-input :type="'text'" :name="'name'" :value="'<%= client.name %>'" class="w-full" />
            <x-form-error :name="'name'"></x-form-error>
        </x-form-item>
        <x-form-item class="max-w-52">
            <x-form-label class="">Имя</x-form-label>
            <x-form-input :type="'text'" :name="'phone'" :value="'<%= client.phone %>'" class="w-full"  />
            <x-form-error :name="'phone'"></x-form-error>
        </x-form-item>
        <x-form-item class="max-w-52">
            <x-form-label class="">Имя</x-form-label>
            <x-form-input :type="'text'" :name="'email'" :value="'<%= client.email %>'" class="w-full"  />
            <x-form-error :name="'email'"></x-form-error>
        </x-form-item>
        <x-button-primary :data="'data-submit-btn'" :type="'submit'" class="block w-full" onclick="saveClient(this.closest('form'))">Сохранить</x-button-primary>
        <x-link-primary class="text-right mt-4" onclick="deleteClient(<%= client.id %>, this.closest('form'))">
            Удалить
        </x-link-primary>
    </x-form>
</script>