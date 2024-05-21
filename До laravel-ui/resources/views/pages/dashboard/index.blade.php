@extends('layouts.main')

@section('title')
    Панель
@endsection

@section('scripts')
    @vite('resources/js/pages/dashboard/script.js')
@endsection

@section('content')
    <div data-content-wrapper class="h-screen p-2 flex flex-col">
        <div class="flex items-center">
            <div class="text-xl text-white p-2 rounded-sm cursor-pointer [&.active]:bg-gray-700 hover:bg-gray-700 active"
                data-content-nav data-id="computers">Компьютеры</div>
            <div class="text-xl text-white p-2 rounded-sm cursor-pointer [&.active]:bg-gray-700 hover:bg-gray-700"
                data-content-nav data-id="clients">Клиенты</div>
        </div>

        @include('pages.dashboard.components.panel.index')

        @include('pages.dashboard.components.clients.index')

        @include('pages.dashboard.components.booking.index')

    </div>

    @include('pages.dashboard.components.clients.client-add-modal')
    @include('pages.dashboard.components.clients.client-edit-modal')
@endsection
