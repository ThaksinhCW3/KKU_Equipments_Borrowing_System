<x-admin-layout>
    @section('title', 'รายการการยืนยัน')
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Vue Verification Table -->
    <div id="verification-table-app">
        <verification-table></verification-table>
    </div>

    @vite('resources/js/verification-app.js')
</x-admin-layout>
