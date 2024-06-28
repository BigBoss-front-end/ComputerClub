@extends('layouts.app')

@section('scripts')
    @vite('resources/js/pages/history/script.js')
@endsection

@section('content')
    <div class="mb-3">
        <x-container>
            <div class="mb-4 text-black text-3xl font-semibold mt-5">Движение по позициям</div>
            <form class="submit-prevent-default" id="history-filter">
                <div class="max-w-80 mb-2">
                    <div class="mb-1">Период</div>
                    <input type="hidden" id="filter-date" name="date_from" value="" datepicker-input>
                    <input type="text" id="filter-date-input" value="За все время"
                        class="datepicker-pediod-custom py-2 px-2 text-lg w-full text-black rounded-md">
                </div>
                <div class="max-w-80">
                    <div class="mb-1">Позиции</div>
                    <select name="position_id" class="bg-white py-2 px-2 text-lg w-full text-black rounded-md">
                        <option value="">Все позиции</option>
                        @foreach (\App\Models\Position::query()->orderBy('name')->get() as $position)
                            <option value="{{ $position->id }}">{{ $position->name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </x-container>
    </div>

    <div id="history-groups">

    </div>

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
                            <option value="{{$pos->id}}">{{$pos->name}}</option>
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
            <div class="flex justify-between items-center mb-6 border-b-2 border-neutral-200 pb-5">
                <div class="">
                    Сумма
                </div>
                <div>
                    <input type="number" inputmode="decimal" name="count" min="0.01" step="any" required value="<%= count < 0 ? (count * -1).toFixed(1) : count.toFixed(1) %>"
                        class="p-2 text-right border border-neutral-200 rounded-md">
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
            @if (Auth::user()->hasRole(['admin', 'director']))

            <div class="hidden errors text-sm text-white p-2 bg-red-400 rounded-md my-2"></div>

            <button
                class="sending_button rounded-md w-full text-white font-semibold bg-gradient-to-r from-orange-500 to-orange-400 hover:from-orange-600
            hover:to-orange-500 active:from-orange-700 active:to-orange-600 p-4 mb-2">Сохранить</button>
            <button class="underline" onclick="openDeleteHistoryModal(<%= id %>)">Удалить</button>
            @endif
        </form>
    </script>

    <script id="history-group-template" type="text/template">
        <div data-history-group>
            <div class="text-black text-3xl font-semibold py-2 bg-neutral-200">
                <x-container>
                    <%= moment(date).format('DD MMM YY') %>
                </x-container>
            </div>
            <div data-history-items>
                <% items.map(item => { %>
                    <div class="py-4 bg-white border-b-2 border-neutral-100 active:bg-neutral-200 hover:bg-neutral-100 cursor-pointer" data-history-row id="<%= item.id %>">
                        <x-container>
                            <div class="text-sm text-black">
                                <span><%= moment(item.date).format('HH:mm') %></span>
                                <span>- изменил <%= item.user.name %></span>
                            </div>
                            <div class="flex items-center justify-between text-lg text-black font-semibold">
                                <div>
                                    <%= item.position.name %>
                                </div>
                                <div
                                    class="text-<%= item.is_revision ? 'blue' : (item.count > 0 ? 'green' : (item.count < 0 ? 'red' : '')) %>-500">
                                    <%= item.count > 0 ? '+' : '' %> <%= item.count.toFixed(1) %> кг
                                </div>
                            </div>
                        </x-container>
                    </div>
                <% }) %>
            </div>
        </div>
    </script>

    <script id="history-item-template" type="text/template">
        <div class="new-item animate__animated animate__fadeIn py-4 bg-white border-b-2 border-neutral-100 active:bg-neutral-200 hover:bg-neutral-100 cursor-pointer" data-history-row id="<%= id %>">
            <x-container>
                <div class="text-sm text-black">
                    <span><%= moment(date).format('HH:mm') %></span>
                    <span>- изменил <%= user.name %></span>
                </div>
                <div class="flex items-center justify-between text-lg text-black font-semibold">
                    <div>
                        <%= position.name %>
                    </div>
                    <div
                        class="text-<%= is_revision ? 'blue' : (count > 0 ? 'green' : (count < 0 ? 'red' : '')) %>-500">
                        <%= count > 0 ? '+' : '' %> <%= count.toFixed(1) %> кг
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
