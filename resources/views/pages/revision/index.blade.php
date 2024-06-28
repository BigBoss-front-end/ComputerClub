@extends('layouts.app')

@section('scripts')
    @vite('resources/js/pages/revision/script.js')
@endsection

@section('content')
    <x-container>
        <div class="flex items-end gap-2 mb-4 mt-5">
            <div class="text-black text-3xl font-semibold">Ревизия</div>
        </div>
    </x-container>
    <div>
        <div class="mb-2">
            @foreach ($positions as $position)
                <div class="bg-white first:border-t-2 border-b-2 border-neutral-200 py-3" data-position data-id="{{ $position->id }}">
                    <x-container>
                        <div class="flex items-center justify-between gap-x-2">
                            <div>{{ $position->name }}</div>
                            <div class="flex flex-col items-end">
                                <div class="relative max-w-32">
                                    <input type="number" inputmode="decimal" min="0" step="any" name="count" value="{{ human_decimal($position->histories_sum_count ?? 0) }}" class="w-full text-right p-2 py-1 pr-6 bg-neutral-200 border-2 border-neutral-200 rounded-md">
                                    <div class="absolute top-1/2 -translate-y-1/2 right-2">кг</div>
                                </div>
                                <div class="input_error text-sm text-red-500 max-w-32"></div>
                            </div>

                        </div>
                    </x-container>
                </div>
            @endforeach
        </div>
        <x-container>
            <button
                id="save-revisions"
                onclick="saveRevisions()"
                class="sending_button rounded-md w-full text-white font-semibold bg-gradient-to-r from-orange-500 to-orange-400 hover:from-orange-600
        hover:to-orange-500 active:from-orange-700 active:to-orange-600 p-4 mb-2">Сохранить
                изменения</button>
        </x-container>
    </div>
@endsection
