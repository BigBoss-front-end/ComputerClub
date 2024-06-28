@extends('layouts.app')

@section('scripts')
    @vite('resources/js/pages/balance/script.js')
@endsection

@section('content')
    @if (Session::has('error'))
        <div class="py-4">
            <x-container>
                <div class="errors text-sm text-white p-2 bg-red-400 rounded-md">
                    {{ Session::get('error') }}
                </div>
            </x-container>
        </div>
    @endif

    <div class="py-6">
        <x-container>

            <div class="flex items-end justify-between gap-x-6 mb-6">
                <div>
                    <div class="text-2xl text-black font-semibold mb-2">Остаток на</div>
                    <input type="hidden" id="filter-date" name="date" value="{{ date('Y-m-d H:i:s') }}" datepicker-input>
                    <input class="py-2 px-2 text-lg max-w-40 w-auto text-black rounded-md datepicker-default"
                        value="Сегодня в {{ date('H:i') }}" />
                </div>
                <button onclick="downloadExcel()" class="flex items-center bg-white rounded-md gap-x-2 py-2 px-4">
                    <div class="text-xl w-auto text-black">XLS</div>
                    <x-icon-download></x-icon-download>
                </button>
            </div>
            <div class="mb-2" id="position-rows">

            </div>
            <button id="load-btn" type="button" class="block w-full animate-pulse text-orange-500 text-right" disabled>
                Загрузка...
            </button>
            <div id="total" style="display: none" class="flex items-end justify-end gap-x-2">
                <div class="text-lg text-black">
                    Итого на складе:
                </div>
                <div class="text-orange-500 font-semibold text-2xl flex-shrink-0" id="total-count">
                    {{ $positions->sum(fn($p) => $p->total_sum_count > 0 ? $p->total_sum_count : 0) }}
                </div>
            </div>
        </x-container>
    </div>

    <script id="position-row" type="text/template">
        <a href="/position/<%= id %>" class="transition flex gap-x-4 items-center justify-between p-4 mb-2 bg-white rounded-md group hover:bg-orange-500">
            <div class="transition text-lg text-black group-hover:text-white">
                <%= name %>
            </div>
            <div class="transition text-orange-500 font-semibold text-xl flex-shrink-0 group-hover:text-white">
                <%= Number(sum_count).toFixed(1) %> кг
            </div>
        </a>
    </script>
@endsection
