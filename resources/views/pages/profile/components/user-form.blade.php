<form id="user-form" class="group">
    <x-form-item>
        <label class="block text-gray-700 mb-1">Имя</label>
        <input type="text" name="name" value="{{ Auth::user()->name }}"
            class="p-0 bg-transparent rounded-sm outline-none text-white group-[.active]:bg-white group-[.active]:text-black group-[.active]:p-2">
    </x-form-item>
    <x-form-item class="group-[.active]:mb-2" :mb="'4'">
        <label class="block text-gray-700 mb-1">Email</label>
        <input type="text" name="email" value="{{ Auth::user()->email }}" disabled
            class="p-0 bg-transparent rounded-sm outline-none text-white">
    </x-form-item>
    <x-form-item class="hidden group-[.active]:block" :mb="'4'">
        <label class="block text-gray-700 mb-1">Новый пароль</label>
        <input type="text" name="new_password" value=""
            class="p-0 bg-transparent rounded-sm outline-none text-white group-[.active]:bg-white group-[.active]:text-black group-[.active]:p-2">
    </x-form-item>
    <div>
        <x-button-secondary :type="'button'" onclick="showUserForm()" class="group-[.active]:hidden">Редактировать</x-button-secondary>
        <div class="hidden group-[.active]:flex gap-x-2">
            <x-button-secondary :type="'submit'" >Сохранить</x-button-secondary>
            <x-button-danger :type="'button'" onclick="hideUserForm()">Отмена</x-button-secondary>
        </div>
    </div>
</form>
