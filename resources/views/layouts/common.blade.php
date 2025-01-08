<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href="{{ asset('logofavicon.png') }}" type="image/x-icon">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script src="{{ url("/assets/js/jquery-3.7.1.min.js") }}" rel="script" ></script>

    </head>
    <body class="font-sans antialiased dark:bg-black dark:text-white/50">
        <x-nav-header></x-nav-header>
        <main class="overflow-hidden">
            {{ $slot }}
        </main>
    </body>
</html>