@extends('layouts.main')

@section('title')
    Авторизация
@endsection

@section('scripts')
    @vite('resources/js/pages/registration/script.js')
@endsection

@section('content')
    <div class="h-screen flex items-center justify-center" id="registration-form">
        <x-form :id="'login-form'">
            <div class="mb-4 text-xl text-center text-white">Регистрация</div>
            <x-form-error class="mb-2" :name="'login'"></x-form-error>
            <x-form-item>
                <x-form-label>Email</x-form-label>
                <x-form-input :type="'text'" :name="'email'" :placeholder="'Email'" />
                <x-form-error :name="'email'"></x-form-error>
            </x-form-item>
            <x-form-item>
                <x-form-label>Пароль</x-form-label>
                <x-form-input :type="'password'" :name="'password'" :placeholder="'Пароль'" />
                <x-form-error :name="'password'"></x-form-error>
            </x-form-item>
            <x-form-button :data="'data-submit-btn'">
                Войти
            </x-form-button>
        </x-form>
    </div>

    <div class="h-screen flex items-center justify-center" id="registration-code-form">
        <x-form :id="'login-form'">
            <div class="mb-4 text-xl text-center text-white">Код отправлен на почту</div>
            <x-form-error class="mb-2" :name="'login'"></x-form-error>
            <x-form-item>
                <x-form-label>Вставьте код</x-form-label>
                <x-form-input :type="'number'" :name="'code'" :placeholder="'Код'" />
                <x-form-error :name="'code'"></x-form-error>
            </x-form-item>
            <x-form-button :data="'data-submit-btn'">
                Войти
            </x-form-button>
        </x-form>
    </div>
@endsection
