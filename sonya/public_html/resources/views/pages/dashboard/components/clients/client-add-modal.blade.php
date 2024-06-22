<x-remodal class="p-0" :modalId="'client-add'">
    <x-form :id="'add-client-form'" :px="'10'" :py="'5'">
        <div class="mb-4 text-xl text-center text-white">Добавить клиента</div>
        <x-form-error class="mb-2" :name="'login'"></x-form-error>
        <div class="">
            <x-form-item class="max-w-52">
                <x-form-label>Имя</x-form-label>
                <x-form-input class="w-full" :type="'text'" :name="'name'" :placeholder="'Имя'" />
                <x-form-error :name="'name'"></x-form-error>
            </x-form-item>
            <x-form-item class="max-w-52">
                <x-form-label>Телефон</x-form-label>
                <x-form-input class="w-full" :type="'tel'" :name="'phone'" :placeholder="'Телефон'" />
                <x-form-error :name="'phone'"></x-form-error>
            </x-form-item>
            <x-form-item class="max-w-52">
                <x-form-label>Почта</x-form-label>
                <x-form-input class="w-full" :type="'email'" :name="'email'" :placeholder="'Почта'" />
                <x-form-error :name="'email'"></x-form-error>
            </x-form-item>
            <x-button-primary :data="'data-submit-btn'" class="w-full">
                Добавить
            </x-button-primary>
        </div>

    </x-form>
</x-remodal>