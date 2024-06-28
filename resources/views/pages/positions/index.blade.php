@extends('layouts.app')

@section('scripts')
    @vite('resources/js/pages/positions/script.js')
@endsection

@section('content')
    <x-container>
        <div class="flex items-end gap-2 mb-4 mt-5">
            <div class="text-black text-3xl font-semibold">Позиции</div>
            <button data-remodal-target="add-position"
                class="bg-gradient-to-r from-orange-500 to-orange-400 hover:from-orange-600
            hover:to-orange-500 active:from-orange-700 active:to-orange-600 text-white font-bold aspect-square px-2 rounded">
                <span class="text-xl">+</span>
            </button>
        </div>
    </x-container>
    <div>
        <div class="mb-2" id="position-items">
            <div id="current-position-items">
                @isset($positions['not_deleted'])
                    @foreach ($positions['not_deleted'] as $position)
                        <div data-position data-sort="{{ $position->sort }}" data-id="{{ $position->id }}"
                            class="bg-white first:border-t-2 border-b-2 border-neutral-200 py-4 {{ $position->deleted_at != null ? 'bg-neutral-200 opacity-25 border-white' : '' }}">
                            <x-container>

                                <div class="flex items-center justify-between gap-x-2">
                                    <div class="flex items-center gap-x-2">
                                        <div class="handle select-none ">
                                            <x-burger />
                                        </div>
                                        <input type="text" name="name" value="{{ $position->name }}" required class="select-none bg-transparent outline-none" />
                                    </div>
                                    <button onclick="openDeletePositionModal({{ $position->id }})"
                                        class="bg-gradient-to-r from-red-500 to-red-400 hover:from-red-600
                            hover:to-red-500 active:from-red-700 active:to-red-600 w-7 h-7 rounded-md relative">
                                        <span
                                            class="block text-lg text-white absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">x</span>
                                    </button>

                                </div>
                            </x-container>
                        </div>
                    @endforeach
                @endisset

            </div>
            <div id="deleted-position-items">
                @isset($positions['deleted'])
                    @foreach ($positions['deleted'] as $position)
                        <div data-position data-sort="{{ $position->sort }}" data-id="{{ $position->id }}"
                            class="bg-white first:border-t-2 border-b-2 border-neutral-200 py-4 {{ $position->deleted_at != null ? 'bg-neutral-200 opacity-25 border-white hover:opacity-100' : '' }}">
                            <x-container>

                                <div class="flex items-center justify-between gap-x-2">
                                    <div class="flex items-center gap-x-2">
                                        <div class="handle select-none">
                                            <x-burger />
                                        </div>
                                        <input type="text" name="name" value="{{ $position->name }}" required class="select-none bg-transparent outline-none" />
                                    </div>
                                    <button onclick="restorePosition({{ $position->id }})"
                                        class="bg-gradient-to-r from-green-500 to-green-400 hover:from-green-600
                        hover:to-green-500 active:from-green-700 active:to-green-600 w-7 h-7 rounded-md relative flex items-center justify-center">
                                            <x-icon-restore class="w-6 h-6" />
                                    </button>

                                </div>
                            </x-container>
                        </div>
                    @endforeach
                @endisset

            </div>
        </div>
        <x-container>
            <button
                id="save-changes"
                onclick="saveChanges()"
                class="sending_button rounded-md w-full text-white font-semibold bg-gradient-to-r from-orange-500 to-orange-400 hover:from-orange-600
        hover:to-orange-500 active:from-orange-700 active:to-orange-600 p-4 mb-2">Сохранить
                изменения</button>
        </x-container>
    </div>

    <div class="remodal rounded-md remodal-history" data-remodal-id="add-position">
        <div class="text-black text-center text-2xl font-semibold">Новый товар</div>
        <form class="py-2 border-b-1 submit-prevent-default" id="add-position">
            <div class="mb-2">
                <div class="">
                    Название
                </div>
                <div class="">
                    <input type="text" name="name" class="w-full py-3 px-2 border border-neutral-200 rounded-md">
                </div>
            </div>
            <div class="mb-2">
                <div class="">
                    Начальный остаток
                </div>
                <div class="">
                    <input type="number" inputmode="decimal" min="0.01" step="any" name="beginning_balance" class="w-full py-3 px-2 border border-neutral-200 rounded-md">
                </div>
            </div>
            <button
                class="sending_button rounded-md w-full text-white font-semibold bg-gradient-to-r from-orange-500 to-orange-400 hover:from-orange-600
            hover:to-orange-500 active:from-orange-700 active:to-orange-600 p-4 mb-2">Добавить</button>
        </form>
    </div>

    <div class="remodal rounded-md remodal-history" data-remodal-id="delete-position">
        <div class="text-black text-center text-2xl font-semibold mb-4">Точно удалить?</div>
        <div class="flex gap-x-4 items-center justify-center" data-position data-id="">
            <button data-remodal-action="close"
                class="rounded-md w-full text-white font-semibold bg-gradient-to-r from-green-500 to-green-400 hover:from-green-600
            hover:to-green-500 active:from-green-700 active:to-green-600 p-4 mb-2">Отмена</button>
            <button id="delete-position-button"
                class="rounded-md w-full text-white font-semibold bg-gradient-to-r from-red-500 to-red-400 hover:from-red-600
            hover:to-red-500 active:from-red-700 active:to-red-600 p-4 mb-2">Удалить</button>
        </div>
    </div>

    <script id="position-row-template" type="text/template">
        <div data-position data-sort="<%= sort %>" data-id="<%= id %>"
            class="bg-white first:border-t-2 border-b-2 border-neutral-200 py-4 <%= deleted_at != null ? 'bg-neutral-200 opacity-25 border-white hover:opacity-100' : '' %>">
            <x-container>

                <div class="flex items-center justify-between gap-x-2">
                    <div class="flex items-center gap-x-2">
                        <div class="handle select-none">
                            <x-burger />
                        </div>
                        <input type="text" name="name" value="<%= name %>" required class="select-none bg-transparent outline-none" />
                    </div>
                    <% if(deleted_at != null) { %>
                        <button onclick="restorePosition(<%= id %>)"
                            class="bg-gradient-to-r from-green-500 to-green-400 hover:from-green-600
                            hover:to-green-500 active:from-green-700 active:to-green-600 w-7 h-7 rounded-md relative flex items-center justify-center">
                                                <x-icon-restore class="w-6 h-6" />
                                        </button>
                        </button>
                    <% } else { %>
                        <button onclick="openDeletePositionModal(<%= id %>)"
                            class="bg-gradient-to-r from-red-500 to-red-400 hover:from-red-600
                    hover:to-red-500 active:from-red-700 active:to-red-600 w-7 h-7 rounded-md relative">
                            <span
                                class="block text-lg text-white absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">x</span>
                        </button>
                    <% } %>
                </div>
            </x-container>
        </div>
    </script>
@endsection
