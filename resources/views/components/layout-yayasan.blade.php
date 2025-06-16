<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Keuangan | Yayasan Nurul Huda') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo-yysn.png') }}">


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <main class='flex'>
            <div class="w-100">
                <div class="w-64 bg-gray-800 text-white h-screen transition-all duration-300" id="sidebar">
                    <x-navbar-yayasan></x-navbar-yayasan>
                </div>
            </div>
            <div class="w-full">
                @isset($header)
                    <header class="bg-green-800 h-12 sticky top-0">
                        <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8 flex items-center">
                            <!-- Logo Aplikasi -->
                            <img src="{{ asset('images/logo-yysn.png') }}" alt="Application Logo" class="h-5 mr-3">
                            <div class="flex flex-col">
                                <div class="text-white text-lg font-semibold">
                                    {{ $header }}
                                </div>
                                <div class="text-white text-sm">
                                    <p>Yayasan Nurul Huda</p>
                                </div>
                            </div>
                        </div>
                    </header>
                @endisset
                {{ $slot }}
            </div>
        </main>
    </div>
</body>

</html>
