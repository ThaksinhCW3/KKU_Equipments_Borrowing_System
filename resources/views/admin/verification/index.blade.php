<x-admin-layout>
        <div class="max-w-7xl mx-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">การยืนยันตัวตน</h1>
                        <div class="text-sm text-gray-500">
                            จัดการคำขอการยืนยันตัวตนของนักศึกษา
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    @endif

                    <!-- Vue Verification Table -->
                    <div id="verification-table-app">
                        <verification-table></verification-table>
                    </div>
                </div>
            </div>
        </div>

    @vite('resources/js/verification-app.js')
</x-admin-layout>
