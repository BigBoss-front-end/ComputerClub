@extends('layouts.main')

@section('title')
    Панель
@endsection

@section('scripts')
    @vite('resources/js/pages/profile/script.js')
@endsection

@section('content')
    <a href="{{ route('home') }}" class="mb-4 block">
        <x-button-secondary>
            Перейти в Панель
        </x-button-secondary>
    </a>

    <div class="border border-white rounded-sm p-4">

        <div>
            <div id="common-info">

            </div>
            @include('pages.profile.components.user-form')
        </div>

        <div></div>

    </div>
@endsection
