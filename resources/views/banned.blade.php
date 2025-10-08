<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คุณถูกแบนจากระบบ</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="font-noto-sans-thai min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-500 to-purple-600">
    <div class="bg-white rounded-2xl p-10 shadow-2xl text-center max-w-lg w-11/12">
        <div class="text-7xl text-red-600 mb-5">🚫</div>
        <h1 class="text-red-600 text-2xl md:text-3xl font-bold mb-4">คุณถูกแบนจากระบบ</h1>
        <p class="text-gray-500 text-base md:text-lg mb-5 leading-relaxed">
            บัญชีของคุณถูกระงับการใช้งานจากระบบ<br>
            กรุณาติดต่อผู้ดูแลระบบเพื่อขอข้อมูลเพิ่มเติม
        </p>

        @if (auth()->check() && auth()->user()->ban_reason)
            <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 my-6 text-red-600 font-medium">
                <strong>เหตุผล:</strong> {{ auth()->user()->ban_reason }}
            </div>
        @endif

        <div class="bg-gray-100 rounded-lg px-4 py-3 mt-6 text-gray-600 text-sm">
            <strong>ช่องทางการติดต่อผู้ดูแลระบบเพื่อยื่นขออุทธรณ์:</strong><br>
            อีเมล: unik09john@gmail.com<br>
            โทรศัพท์: 06-xxx-xxxx
        </div>
    </div>

    <script>
        // Prevent user from navigating away
        window.history.pushState(null, null, window.location.href);
        window.addEventListener('popstate', function() {
            window.history.pushState(null, null, window.location.href);
        });

        // Prevent all navigation
        window.addEventListener('beforeunload', function(e) {
            e.preventDefault();
            e.returnValue = '';
        });

        // Disable all links and forms
        document.addEventListener('click', function(e) {
            if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON') {
                e.preventDefault();
                alert('คุณถูกแบนจากระบบ - ไม่สามารถดำเนินการได้');
            }
        });
    </script>
</body>

</html>
