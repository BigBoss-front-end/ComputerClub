<div class="bg-white py-4 fixed top-0 -left-full w-full h-full" id="nav">
    <x-container>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-x-2">
                <x-icon-avatar />
                <div>{{ Auth::user()->name }}</div>
            </div>
            <div class="flex items-center gap-x-6">
                <a href="{{ route('logout') }}" class="flex items-center gap-x-2 text-orange-500">
                    <div>Выйти</div>
                    <x-icon-exit />
                </a>
                <button data-close-nav class="bg-gradient-to-r from-red-500 to-red-400 hover:from-red-600
                hover:to-red-500 active:from-red-700 active:to-red-600 w-7 h-7 rounded-md relative">
                    <span class="block text-lg text-white absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">x</span>
                </button>
            </div>

        </div>
    </x-container>
    <div id="menu" class="">
        <x-container>
            <ul class="list-none py-6">
                <li class="group {{ Route::is('balance') ? 'active' : '' }}">
                    <a href="{{ route('balance') }}"
                        class="group-[.active]:text-orange-500 block mb-4 text-black font-semibold text-2xl">Текущие
                        остатки</a>
                </li>
                <li class="group {{ Route::is('history') ? 'active' : '' }}">
                    <a href="{{ route('history') }}"
                        class="group-[.active]:text-orange-500 block mb-4 text-black font-semibold text-2xl">Движения по
                        позициям</a>
                </li>
                @if (Auth::user()->hasRole(['admin']))
                    <li class="group {{ Route::is('revision') ? 'active' : '' }}">
                        <a href="{{ route('revision') }}"
                            class="group-[.active]:text-orange-500 block mb-4 text-black font-semibold text-2xl">Ревизия</a>
                    </li>
                    <li class="group {{ Route::is('positions') ? 'active' : '' }}">
                        <a href="{{ route('positions') }}"
                            class="group-[.active]:text-orange-500 block mb-4 text-black font-semibold text-2xl">Позиции</a>
                    </li>
                    <li class="group {{ Route::is('users') ? 'active' : '' }}">
                        <a href="{{ route('users') }}"
                            class="group-[.active]:text-orange-500 block mb-4 text-black font-semibold text-2xl">Пользователи</a>
                    </li>
                @endif
                
            </ul>
        </x-container>
    </div>
</div>
