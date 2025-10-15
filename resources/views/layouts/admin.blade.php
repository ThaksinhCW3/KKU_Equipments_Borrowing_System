<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>แดชบอร์ดผู้ดูแลระบบ - @yield('title', 'หน้าแอดมิน')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('enkku_logo_icon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('enkku_logo_icon.ico') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="//unpkg.com/alpinejs" defer></script>


    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-noto-sans-thai antialiased bg-gray-50">
    <div class="min-h-screen">
        <x-admin-header />
        <div class="flex">
            <div class="hidden md:block flex-shrink-0">
                <x-admin-sidebar />
            </div>
            <main id="admin-main" class="flex-1 min-w-0 px-4 sm:px-6 lg:px-8 py-6">
                {{ $slot }}
            </main>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('sidebar-toggle');
            const sidebarWrapper = document.querySelector('body .flex > .hidden.md\\:block');
            if (toggle && sidebarWrapper) {
                toggle.addEventListener('click', function() {
                    sidebarWrapper.classList.toggle('hidden');
                });
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            const logoutForms = document.querySelectorAll('.logout-form');

            logoutForms.forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'ยืนยันการออกจากระบบ',
                        text: 'คุณต้องการออกจากระบบใช่หรือไม่?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'ตกลง',
                        cancelButtonText: 'ยกเลิก',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
</body>

</html>
