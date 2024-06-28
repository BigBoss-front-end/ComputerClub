@extends('layouts.app')

@section('scripts')
    @vite('resources/js/pages/users/script.js')
@endsection

@section('content')
    <x-container>
        <div class="flex items-end gap-2 mb-4 mt-5">
            <div class="text-black text-3xl font-semibold">Пользователи</div>
            <button data-remodal-target="add-user"
                class="bg-gradient-to-r from-orange-500 to-orange-400 hover:from-orange-600
            hover:to-orange-500 active:from-orange-700 active:to-orange-600 text-white font-bold aspect-square px-2 rounded">
                <span class="text-xl">+</span>
            </button>
        </div>

        <div id="user-items">
            @foreach ($users as $user)
                <div class="bg-white rounded-md p-4 mb-4" data-user data-id="{{ $user->id }}">
                    <div class="mb-4">
                        <div class="mb-2">
                            <div class="mb-1 text-sm">Имя</div>
                            <div class="text-md font-semibold">{{ $user->name }}</div>
                        </div>
                        <div class="mb-2">
                            <div class="mb-1 text-sm">Должость</div>
                            <div class="text-md font-semibold">{{ $user->role->name }}</div>
                        </div>
                        <div class="mb-2">
                            <div class="mb-1 text-sm">Посл. вход</div>
                            <div class="text-md font-semibold">{{ human_date($user->last_login_at) }}</div>
                        </div>
                    </div>
                    
                        <div class="mb-2 flex gap-x-2">
                            @if ($user->id != Auth::id())
                                <button
                                    onclick="{{ $user->is_blocked ? 'unBlockUser(' . $user->id . ')' : 'blockUser(' . $user->id . ')' }}"
                                    class="block transition p-2 rounded-md text-white bg-green-500 hover:bg-green-600 active:bg-green-700 font-semibold">
                                    {{ $user->is_blocked ? 'Открыть доступ' : 'Заблокировать' }}
                                </button>
                            @endif
                            <button onclick="userModal({{ $user->id }})"
                                class="block transition p-2 rounded-md text-white bg-blue-500 hover:bg-blue-600 active:bg-blue-700 font-semibold">Редактировать</button>
                        </div>
                    

                    @if ($user->id != Auth::id())
                        <button onclick="openDeleteUserModal({{ $user->id }})"
                            class="transition underline text-black hover:text-red-500">
                            Удалить
                        </button>
                    @endif

                </div>
            @endforeach
        </div>

    </x-container>


    <div class="remodal rounded-md remodal-history" data-remodal-id="add-user">
        <div class="text-black text-center text-2xl font-semibold">Новый пользователь</div>
        <form class="py-2 border-b-1 submit-prevent-default">
            <div class="mb-2">
                <div class="">
                    Введите имя
                </div>
                <div class="">
                    <input type="text" name="name" class="w-full py-3 px-2 border border-neutral-200 rounded-md">
                    <div class="input_error text-sm text-red-500"></div>
                </div>
            </div>
            <div class="mb-2">
                <div class="">
                    Выберите должность
                </div>
                <div class="">
                    <select name="role_id" class="bg-white w-full py-3 px-2 border border-neutral-200 rounded-md">
                        @foreach (DB::table('roles')->orderBy('name')->get() as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-2">
                <div class="">
                    Придумайте логин
                </div>
                <div class="">
                    <input type="email" name="email" class="w-full py-3 px-2 border border-neutral-200 rounded-md">
                    <div class="input_error text-sm text-red-500"></div>
                </div>
            </div>
            <div class="mb-2">
                <div class="">
                    Придумайте пароль
                </div>
                <div class="relative">
                    <input type="password" name="password"
                        class="w-full py-3 px-2 text-lg font-semibold border border-neutral-200 focus:border-neutral-500 active:outline-neutral-200 rounded-md">
                    <x-icon-open-eye
                        class="absolute top-2/4 z-10 cursor-pointer right-2 -translate-x-1/2 -translate-y-1/2 password-eye" />
                    <div class="input_error text-sm text-red-500"></div>
                </div>
            </div>
            <button onclick="addUser(this.closest('form'))"
                class="sending_button rounded-md w-full text-white font-semibold bg-gradient-to-r from-orange-500 to-orange-400 hover:from-orange-600
            hover:to-orange-500 active:from-orange-700 active:to-orange-600 p-4 mb-2">Добавить</button>
    </div>

    <script id="user-modal-template" type="text/template">
        <div class="text-black text-center text-2xl font-semibold"><%= name %></div>
        <form class="py-2 border-b-1 submit-prevent-default">
            <input type="hidden" name="id" value="<%= id %>">
            <div class="mb-2">
                <div class="">
                    Введите имя
                </div>
                <div class="">
                    <input type="text" name="name" value="<%= name %>" class="w-full py-3 px-2 border border-neutral-200 rounded-md">
                    <div class="input_error text-sm text-red-500"></div>
                </div>
            </div>
            <div class="mb-2">
                <div class="">
                    Выберите должность
                </div>
                <div class="">
                    <select name="role_id"  value="<%= role.id %>" class="bg-white w-full py-3 px-2 border border-neutral-200 rounded-md">
                        @foreach (DB::table('roles')->orderBy('name')->get() as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <div class="input_error text-sm text-red-500"></div>
                </div>
            </div>
            <div class="mb-2">
                <div class="">
                    Придумайте логин
                </div>
                <div class="">
                    <input type="email" name="email" value="<%= email %>" class="w-full py-3 px-2 border border-neutral-200 rounded-md">
                    <div class="input_error text-sm text-red-500"></div>
                </div>
            </div>
            <div class="mb-2">
                <div class="">
                    Придумайте пароль
                </div>
                <div class="relative">
                    <input type="password" name="password" value="" class="w-full py-3 px-2 text-lg font-semibold border border-neutral-200 focus:border-neutral-500 active:outline-neutral-200 rounded-md">
                    <x-icon-open-eye class="absolute top-2/4 z-10 cursor-pointer right-2 -translate-x-1/2 -translate-y-1/2 password-eye" />
                    <div class="input_error text-sm text-red-500"></div>
                </div>
            </div>
            <button
                onclick="updateUser(this.closest('form'))"
                class="sending_button rounded-md w-full text-white font-semibold bg-gradient-to-r from-orange-500 to-orange-400 hover:from-orange-600
            hover:to-orange-500 active:from-orange-700 active:to-orange-600 p-4 mb-2">Сохранить</button>
    </script>

    <div class="remodal rounded-md remodal-history" data-remodal-id="update-user">

    </div>
    
    <div class="remodal rounded-md remodal-history" data-remodal-id="delete-user">
        <div class="text-black text-center text-2xl font-semibold mb-4">Точно удалить?</div>
        <div class="flex gap-x-4 items-center justify-center" data-position data-id="">
            <button data-remodal-action="close"
                class="rounded-md w-full text-white font-semibold bg-gradient-to-r from-green-500 to-green-400 hover:from-green-600
            hover:to-green-500 active:from-green-700 active:to-green-600 p-4 mb-2">Отмена</button>
            <button id="delete-user-button"
                class="rounded-md w-full text-white font-semibold bg-gradient-to-r from-red-500 to-red-400 hover:from-red-600
            hover:to-red-500 active:from-red-700 active:to-red-600 p-4 mb-2">Удалить</button>
        </div>
    </div>


    <script id="user-template" type="text/template">
        <div class="bg-white rounded-md p-4" data-user data-id="<%= id %>">
            <div class="mb-4">
                <div class="mb-2">
                    <div class="mb-1 text-sm">Имя</div>
                    <div class="text-md font-semibold"><%= name %></div>
                </div>
                <div class="mb-2">
                    <div class="mb-1 text-sm">Должость</div>
                    <div class="text-md font-semibold"><%= role.name %></div>
                </div>
                <div class="mb-2">
                    <div class="mb-1 text-sm">Посл. вход</div>
                    <div class="text-md font-semibold"><%= last_login_at ? moment(last_login_at).format('yyyy.dd.mm hh:mm:ss') : 'ещё не входил' %></div>
                </div>
            </div>
            <div class="mb-2 flex gap-x-2">
                <% if(window.user.id != id) { %>
                <button onclick="<%= is_blocked ? 'unBlockUser('+id+')' : 'blockUser('+id+')' %>" class="block transition p-2 rounded-md text-white bg-green-500 hover:bg-green-600 active:bg-green-700 font-semibold">
                    <%= is_blocked ? 'Открыть доступ' : 'Заблокировать' %>
                </button>
                <% } %>
                <button onclick="userModal(<%= id %>)" class="block transition p-2 rounded-md text-white bg-blue-500 hover:bg-blue-600 active:bg-blue-700 font-semibold">Редактировать</button>
            </div>
            <% if(window.user.id != id) { %>
            <button onclick="openDeleteUserModal(<%= id %>)" class="transition underline text-black hover:text-red-500">
                Удалить
            </button>
            <% } %>
        </div>
    </script>
@endsection
