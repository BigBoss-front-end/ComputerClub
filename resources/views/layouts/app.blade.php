<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>@yield('title', 'Sonya')</title>

    @vite('resources/css/app.scss')
    @yield('styles')

    <script src="https://cdn.tailwindcss.com"></script>
    @vite('resources/js/app.js')
    @vite(['resources/css/app.scss', 'resources/sass/app.scss', 'resources/js/app.js'])
    @yield('scripts')
</head>

<body>
    <div id="app" class="h-screen overflow-auto">
        <nav class="bg-white shadow-sm py-3">
            <div class="container flex itemx-center justify-between mx-auto">
                <a class="" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button id="menu-btn" class="relative group" type="button" aria-label="{{ __('Toggle navigation') }}">
                    <div class="space-y-2">
                        <div class="h-0.5 w-8 bg-gray-600"></div>
                        <div class="h-0.5 w-8 bg-gray-600"></div>
                        <div class="h-0.5 w-8 bg-gray-600"></div>
                      </div>
                    <div class="hidden group-[.active]:block absolute top-full right-0">
                        <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav me-auto">
    
                        </ul>
    
                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ms-auto">
                            <!-- Authentication Links -->
                            @guest
                                @if (Route::has('login'))
                                    <li class="bg-white px-2 py-2">
                                        <a class="text-black" href="{{ route('login') }}">{{ __('Вход') }}</a>
                                    </li>
                                @endif
    
                                @if (Route::has('register'))
                                    <li class="bg-white px-2 py-2">
                                        <a class="text-black" href="{{ route('register') }}">{{ __('Регистрация') }}</a>
                                    </li>
                                @endif
                            @else
                                <li class="bg-white px-2 py-2">
                                    <a id="navbarDropdown" class="text-black" href="#" role="button"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ Auth::user()->name }}
                                    </a>
    
                                    <div class="bg-white px-2 py-2">
                                        <a class="text-black" href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                        document.getElementById('logout-form').submit();">
                                            {{ __('Выйти') }}
                                        </a>
    
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </button>

                
            </div>
        </nav>

        <main class="py-4 bg-dots-lighter bg-gray-900 h-screen">
            <div class="container mx-auto">
                @yield('content')
            </div>
        </main>
    </div>

</body>

</html>
