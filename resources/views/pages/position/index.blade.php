@extends('layouts.app')

@section('scripts')
    @vite('resources/js/pages/position/script.js')
@endsection

@section('content')
    <div class="py-6">

        <x-container>
            <a href="{{ route('balance') }}"
                class="transition inline-flex items-center gap-x-2 mb-6 py-2 px-3 bg-white rounded-md group hover:bg-orange-500">
                <x-icon-arrow-left class="transition fill-black group-hover:fill-white" />
                <div class="transition block text-lg text-black font-semibold group-hover:text-white">Вернуться на главную
                </div>
            </a>
        </x-container>
        <x-container>
            <form class="mb-5" id="history-filter">
                <input type="hidden" name="position_id" value="{{ $position->id }}">
                <div class="mb-2 text-black text-3xl font-semibold">{{ $position->name }}</div>
                <div class="bg-white rounded-md p-4 flex items-center justify-between gap-x-1">
                    <div>
                        <div>
                            <div class="text-lg text-black font-semibold mb-1">Остаток на дату</div>
                            <input type="hidden" id="filter-date" name="date_from" value="{{ date('Y-m-d 23:59:00') }}" datepicker-input>
                            <input
                                class="border border-gray-200 mb-1 py-2 px-2 text-md max-w-36 w-auto text-black rounded-md datepicker-default"
                                value="Сегодня в {{ date('H:i', strtotime(date('Y-m-d 23:59:00'))) }}" />
                            <div class="text-lg text-black font-semibold mb-1">
                                <span class="saldo">
                                    {{ $position->sum_count }}
                                </span> кг
                            </div>
                        </div>
                    </div>
                    @if (Auth::user()->hasRole(['admin', 'director', 'storekeeper']))
                        <div class="inline-flex flex-col gap-y-2">
                            <button data-remodal-target="add-history-plus"
                                class="flex items-center gap-x-2 bg-green-500 rounded-md p-2 text-md text-white hover:bg-green-600 active:bg-green-700">
                                <span class="w-3 text-xl font-bold">+</span>
                                <span>Приход</span>
                            </button>
                            <button data-remodal-target="add-history-minus"
                                class="flex items-center gap-x-2 bg-red-500 rounded-md p-2 text-md text-white hover:bg-red-600 active:bg-red-700">
                                <span class="w-3 text-xl font-bold">-</span>
                                <span>Расход</span>
                            </button>
                        </div>
                    @endif
                    
                </div>
            </form>
        </x-container>
        <div class="mb-2">
            <x-container>
                <div class="mb-2 text-black text-xl sm:text-3xl font-semibold">
                    Движения за период
                </div>
            </x-container>

            <div class="bg-white">
                <div class="bg-neutral-300 py-3">
                    <x-container>
                        <div class="flex justify-between items-center gap-x-3">
                            <div class="shrink-0 w-full max-w-28 text-sm text-black font-semibold">
                                Дата
                            </div>
                            <div class="shrink-0 w-full max-w-20 text-sm text-black font-semibold">
                                Изменение
                            </div>
                            <div class="shrink-0 w-full max-w-20 text-sm text-black font-semibold text-right">
                                Остаток
                            </div>
                        </div>
                    </x-container>
                </div>
                <div id="history-items">
                    @php
                        $mount = $position->total_sum_count;
                    @endphp
                    @forelse ($position->histories as $history)
                        <div class="border-b-2 border-gray-200 group hover:bg-neutral-200 py-3 cursor-pointer"
                            data-history-row id="{{ $history->id }}">
                            <x-container>
                                <div class="flex justify-between items-center gap-x-3">
                                    <div class="shrink-0 w-full max-w-28 text-sm text-black">
                                        {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $history->date)->format('d.m.y в H:i') }}
                                    </div>
                                    <div
                                        class="shrink-0 w-full max-w-20 text-sm font-semibold text-{{ $history->is_revision ? 'blue-700' : ($history->count > 0 ? 'green-700' : ($history->count == 0 ? 'black' : 'red-700')) }}">
                                        {{ $history->count < 0 ? '' : ($history->count == 0 ? 0 : '+') }}{{ human_decimal($history->count) }}
                                        кг
                                    </div>
                                    <div class="shrink-0 w-full max-w-20 text-sm font-semibold text-black text-right">
                                        {{ human_decimal($mount) }} кг
                                    </div>
                                </div>

                            </x-container>
                        </div>
                        @php
                            $mount = $mount - $history->count;
                        @endphp
                    @empty
                        <x-container>
                            <div class="py-2">
                                Нет ничего
                            </div>
                        </x-container>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="fixed mb-2 bottom-0 left-0 w-full z-10">
            <x-container>
                <div class="bg-white rounded-md py-4 px-5 flex items-center justify-between gap-x-4">
                    <div>
                        <div class="transition text-orange-500 font-semibold text-xl flex-shrink-0">
                            <span class="saldo">
                                {{ $position->sum_count > 0 ? '+' : '' }}{{ $position->sum_count }}
                            </span> кг
                        </div>
                        <div>
                            сальдо за период
                        </div>
                    </div>
                    <button onclick="downloadExcel()"  class="flex items-center bg-white rounded-md gap-x-2 py-2 px-4 border-neutral-400 shadow-md">
                        <div class="text-xl w-auto text-black">XLS</div>
                        <x-icon-download></x-icon-download>
                    </button>
                </div>
            </x-container>
        </div>


    </div>

    <div class="remodal rounded-md remodal-history remodal-history-add" data-remodal-id="add-history-plus">
        <div class="flex items-start justify-between mb-3 border-b-2 border-neutral-200 pb-5">
            <div class="text-green-500 text-md font-semibold">
                + Приход
            </div>
            <div class="text-right">
                <div class="text-gray-500 text-sm">внес изменения</div>
                <div>{{ Auth::getUser()->name }}</div>
            </div>
        </div>
        <form class="submit-prevent-default py-2 border-b-1">
            <input type="hidden" name="type" value="increment">
            <div class="flex justify-between items-center mb-6 border-b-2 border-neutral-200 pb-5">
                <div class="">
                    Позиция
                </div>
                <div class="">
                    <select name="position_id" class="bg-white p-2 text-right border border-neutral-200 rounded-md">
                        <option value="{{ $position->id }}">{{ $position->name }}</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-between items-center mb-6 border-b-2 border-neutral-200 pb-5">
                <div class="">
                    Когда
                </div>
                <input type="hidden" name="date" datepicker-input value="{{ date('Y-m-d H:i:s') }}">
                <input type="text"
                    value="{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->format('d.m.y в H:i') }}"
                    class="datepicker-default p-2 text-right border border-neutral-200 rounded-md datepicker-default">
            </div>
            <div class="flex gap-x-4 justify-between items-center mb-6 border-b-2 border-neutral-200 pb-5">
                <div class="">
                    Сумма
                </div>
                <div>
                    <input type="number" inputmode="decimal" name="count" min="0.01" step="any" value="" required
                        class="w-full p-2 text-right border border-neutral-200 rounded-md">
                    <div class="input_error text-sm text-red-500"></div>
                </div>
            </div>
            @if (Auth::user()->hasRole(['admin']))
                <div class="flex mb-6">
                    <div class="self-end flex items-center gap-x-2">
                        <input id="is_revision" name="is_revision" type="checkbox">
                        <label for="is_revision">Ревизия?</label>
                    </div>
                </div>
            @endif

            <div class="hidden errors text-sm text-white p-2 bg-red-400 rounded-md my-2"></div>

            <button
                class="sending_button rounded-md w-full text-white font-semibold bg-gradient-to-r from-orange-500 to-orange-400 hover:from-orange-600
            hover:to-orange-500 active:from-orange-700 active:to-orange-600 p-4 mb-2">Добавить</button>
        </form>
    </div>




    <div class="remodal rounded-md remodal-history remodal-history-add" data-remodal-id="add-history-minus">
        <div class="flex items-start justify-between mb-3 border-b-2 border-neutral-200 pb-5">
            <div class="text-red-500 text-md font-semibold">
                - Расход
            </div>
            <div class="text-right">
                <div class="text-gray-500 text-sm">внес изменения</div>
                <div>{{ Auth::getUser()->name }}</div>
            </div>
        </div>
        <form class="submit-prevent-default py-2 border-b-1">
            <input type="hidden" name="type" value="decrement">
            <div class="flex justify-between items-center mb-6 border-b-2 border-neutral-200 pb-5">
                <div class="">
                    Позиция
                </div>
                <div class="">
                    <select name="position_id" class="bg-white p-2 text-right border border-neutral-200 rounded-md">
                        <option value="{{ $position->id }}">{{ $position->name }}</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-between items-center mb-6 border-b-2 border-neutral-200 pb-5">
                <div class="">
                    Когда
                </div>
                <input type="hidden" name="date" datepicker-input value="{{ date('Y-m-d H:i:s') }}">
                <input type="text"
                    value="{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->format('d.m.y в H:i') }}"
                    class="datepicker-default p-2 text-right border border-neutral-200 rounded-md datepicker-default">
            </div>
            <div class="flex gap-x-4 justify-between items-center mb-6 border-b-2 border-neutral-200 pb-5">
                <div class="">
                    Сумма
                </div>
                <div>
                    <input type="number" inputmode="decimal" name="count" min="0.01" step="any" value="" required
                        class="w-full p-2 text-right border border-neutral-200 rounded-md">
                    <div class="input_error text-sm text-red-500"></div>
                </div>
            </div>
            @if (Auth::user()->hasRole(['admin']))
                <div class="flex mb-6">
                    <div class="self-end flex items-center gap-x-2">
                        <input id="is_revision" name="is_revision" type="checkbox">
                        <label for="is_revision">Ревизия?</label>
                    </div>
                </div>
            @endif

            <div class="hidden errors text-sm text-white p-2 bg-red-400 rounded-md my-2"></div>

            <button
                class="sending_button rounded-md w-full text-white font-semibold bg-gradient-to-r from-orange-500 to-orange-400 hover:from-orange-600
            hover:to-orange-500 active:from-orange-700 active:to-orange-600 p-4 mb-2">Добавить</button>
        </form>
    </div>

    @if (Auth::user()->hasRole(['admin', 'director']))
        <div class="remodal rounded-md remodal-history remodal-history-update" data-remodal-id="history-info">

        </div>

        <script id="history-info-modal-template" type="text/template">
            <div class="flex items-start justify-between mb-3 border-b-2 border-neutral-200 pb-5">
                <div class="text-<%= count >= 0 ? 'green' : 'red' %>-500 text-md font-semibold">
                    + <%= count >= 0 ? 'Приход' : 'Расход' %>
                </div>
                <div class="text-right">
                    <div class="text-gray-500 text-sm">внес изменения</div>
                    <div><%= user.name %></div>
                </div>
            </div>
            <form class="submit-prevent-default py-2 border-b-1" id="save-history-form">
                <input type="hidden" name="id" value="<%= id %>">
                <input type="hidden" name="type" value="<%= count >= 0 ? 'increment' : 'decrement' %>">
                <div class="flex justify-between items-center mb-6 border-b-2 border-neutral-200 pb-5">
                    <div class="">
                        Позиция
                    </div>
                    <div class="">
                        <select name="position_id" class="bg-white p-2 text-right border border-neutral-200 rounded-md">
                            @foreach (DB::table('positions')->orderBy('name')->get() as $pos)
                                <option {{$position->id == $pos->id ? 'selected' : ''}} value="{{$pos->id}}">{{$pos->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-between items-center mb-6 border-b-2 border-neutral-200 pb-5">
                    <div class="">
                        Когда
                    </div>
                    <input type="hidden" name="date" datepicker-input value="<%= date %>">
                    <input type="text"
                        value="<%= moment(date).format('DD.MM.YYYY HH:mm') %>"
                        class="datepicker-default p-2 text-right border border-neutral-200 rounded-md datepicker-default">
                </div>
                <div class="flex gap-x-4 justify-between items-center mb-6 border-b-2 border-neutral-200 pb-5">
                    <div class="">
                        Сумма
                    </div>
                    <div>
                        <input type="number" inputmode="decimal" name="count" min="0.01" step="any" value="<%= count < 0 ? (count * -1).toFixed(1) : count.toFixed(1) %>" required
                            class="w-full p-2 text-right border border-neutral-200 rounded-md">
                        <div class="input_error text-sm text-red-500"></div>
                    </div>
                </div>
                @if (Auth::user()->hasRole(['admin']))
                    <div class="flex mb-6">
                        <div class="self-end flex items-center gap-x-2">
                            <label>
                                <input id="is_revision" name="is_revision" type="checkbox" <%= is_revision ? 'checked' : '' %>>
                                <span>Ревизия?</span>
                            </label>
                        </div>
                    </div>
                @endif

                <div class="hidden errors text-sm text-white p-2 bg-red-400 rounded-md my-2"></div>

                @if (Auth::user()->hasRole(['admin', 'director']))
                    <button
                        class="sending_button rounded-md w-full text-white font-semibold bg-gradient-to-r from-orange-500 to-orange-400 hover:from-orange-600
                    hover:to-orange-500 active:from-orange-700 active:to-orange-600 p-4 mb-2">Редактировать</button>
                    <button class="underline" onclick="openDeleteHistoryModal(<%= id %>)">Удалить</button>
                @endif
            </form>
        </script>
    @endif

    <script id="history-item-template" type="text/template">
        <div class="border-b-2 border-gray-200 group hover:bg-neutral-200 py-3 cursor-pointer"
            data-history-row id="<%= id %>">
            <x-container>
                <div class="flex justify-between items-center gap-x-3">
                    <div class="shrink-0 w-full max-w-28 text-sm text-black">
                        <%= formatDateTime(date) %>
                    </div>
                    <div
                        class="shrink-0 w-full max-w-20 text-sm font-semibold text-<%= is_revision ? 'blue-700' : (count > 0 ? 'green-700' : (count == 0 ? 'black' : 'red-700')) %>">
                        <%= count < 0 ? '' : (count == 0 ? 0 : '+') %><%= count.toFixed(1) %>
                        кг
                    </div>
                    <div class="shrink-0 w-full max-w-20 text-sm font-semibold text-black text-right">
                        <%= mount.toFixed(1) %> кг
                    </div>
                </div>

            </x-container>
        </div>
    </script>

    <script id="empty-template" type="text/template">
        <x-container>Нет ничего</x-container>
    </script>

    <div class="remodal rounded-md remodal-history" data-remodal-id="delete-history">
        <div class="text-black text-center text-2xl font-semibold mb-4">Точно удалить?</div>
        <div class="flex gap-x-4 items-center justify-center" data-position data-id="">
            <button data-remodal-target="history-info"
                class="rounded-md w-full text-white font-semibold bg-gradient-to-r from-green-500 to-green-400 hover:from-green-600
            hover:to-green-500 active:from-green-700 active:to-green-600 p-4 mb-2">Отмена</button>
            <button id="delete-history-button"
                class="rounded-md w-full text-white font-semibold bg-gradient-to-r from-red-500 to-red-400 hover:from-red-600
            hover:to-red-500 active:from-red-700 active:to-red-600 p-4 mb-2">Удалить</button>
        </div>
    </div>
@endsection
