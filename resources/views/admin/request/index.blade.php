<x-admin-layout>
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
