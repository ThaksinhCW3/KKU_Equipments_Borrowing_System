<x-admin-layout>
    @section('title', 'รายการคำขอ')
    @if(request('user_email'))
        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="text-sm text-blue-700">กำลังแสดงคำขอของ: <strong>{{ request('user_email') }}</strong></span>
                </div>
                <a href="{{ route('admin.requests.index') }}" class="text-sm text-blue-600 hover:text-blue-800 underline">
                    ล้างตัวกรอง
                </a>
            </div>
        </div>
    @endif

    @if(request('status'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="text-sm text-green-700">กำลังแสดงคำขอสถานะ: <strong>{{ ucfirst(request('status')) }}</strong></span>
                </div>
                <a href="{{ route('admin.requests.index') }}" class="text-sm text-green-600 hover:text-green-800 underline">
                    ล้างตัวกรอง
                </a>
            </div>
        </div>
    @endif

<div id="admin-table" data-requests='@json($requests)'>
    <admin-approve-table :requests='@json($requests)'></admin-approve-table>
</div>

<script>
    // Show success/error alerts based on Laravel session flash messages
    function showSubmissionAlert() {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                text: '{{ session('error') }}',
                confirmButtonText: 'ตกลง'
            });
        @endif
    }

    // Show alerts on page load
    document.addEventListener('DOMContentLoaded', function() {
        showSubmissionAlert();
    });
</script>
</x-admin-layout>
