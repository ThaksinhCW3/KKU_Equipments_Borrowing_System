<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'หน้าแรก')</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('enkku_logo_icon.ico') }}">
        <link rel="apple-touch-icon" href="{{ asset('enkku_logo_icon.ico') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 text-black font-noto-sans-thai antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white text-black shadow-lg rounded-xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
