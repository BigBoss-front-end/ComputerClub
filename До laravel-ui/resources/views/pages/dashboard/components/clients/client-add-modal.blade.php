<x-modal :modalId="'client-add'">
    <x-form :id="'add-client-form'">
        <div class="mb-4 text-xl text-center text-white">Добавить клиента</div>
        <x-form-error class="mb-2" :name="'login'"></x-form-error>
        <div class="flex flex-wrap items-top justify-center gap-2">
            <x-form-item>
                <x-form-label>Имя</x-form-label>
                <x-form-input :type="'text'" :name="'name'" :placeholder="'name'" />
                <x-form-error :name="'name'"></x-form-error>
            </x-form-item>
            <x-form-item>
                <x-form-label>Телефон</x-form-label>
                <x-form-input :type="'tel'" :name="'phone'" :placeholder="'Телефон'" />
                <x-form-error :name="'phone'"></x-form-error>
            </x-form-item>
            <div class="w-full flex justify-center">
                <x-form-button :data="'data-submit-btn'">
                    Добавить
                </x-form-button>
            </div>
        </div>

    </x-form>
</x-modal>