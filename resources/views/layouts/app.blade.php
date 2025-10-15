<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <title>ระบบยืมอุปกรณ์ - @yield('title', "หน้าแรก")</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('enkku_logo_icon.ico') }}">
        <link rel="apple-touch-icon" href="{{ asset('enkku_logo_icon.ico') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        @auth
        <script>
            // Check if user is banned
            document.addEventListener('DOMContentLoaded', function() {
                @if(auth()->user()->isBanned())
                    Swal.fire({
                        title: 'คุณถูกแบนจากระบบ',
                        text: 'บัญชีของคุณถูกระงับการใช้งาน',
                        icon: 'error',
                        confirmButtonText: 'ตกลง',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: true,
                        confirmButtonColor: '#dc2626'
                    }).then(() => {
                        window.location.href = '/banned';
                    });
                @endif
            });
        </script>
        @endauth
    </head>
    <body class="font-noto-sans-thai antialiased">
        <div class="flex flex-col min-h-screen">   
            <!-- Page Heading -->
            <x-header />
            <hr>
            
            <!-- Page Content -->
            <main class="flex-grow">
                {{ $slot }}
            </main>
            <x-footer />
        </div>
    </body>
</html>
