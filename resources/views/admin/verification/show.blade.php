<x-admin-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">รายละเอียดการยืนยันตัวตน</h1>
                        <a href="{{ route('admin.verification.index') }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm">
                            ← กลับไปรายการ
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    @endif

                    <!-- User Information -->
                    <div class="mb-6 bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">ข้อมูลผู้ใช้</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">ชื่อ-นามสกุน</p>
                                <p class="text-sm font-medium text-gray-900">{{ $verificationRequest->user->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">รหัสนักศึกษา</p>
                                <p class="text-sm font-medium text-gray-900">{{ $verificationRequest->user->uid }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">อีเมล</p>
                                <p class="text-sm font-medium text-gray-900">{{ $verificationRequest->user->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">เบอร์โทรศัพท์</p>
                                <p class="text-sm font-medium text-gray-900">{{ $verificationRequest->user->phonenumber ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">วันที่ส่งคำขอ</p>
                                <p class="text-sm font-medium text-gray-900">{{ $verificationRequest->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Verification Status -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">สถานะการยืนยัน</h3>
                        <div class="p-4 rounded-lg border
                            @if($verificationRequest->status === 'pending') bg-yellow-50 border-yellow-200
                            @elseif($verificationRequest->status === 'approved') bg-green-50 border-green-200
                            @elseif($verificationRequest->status === 'rejected') bg-red-50 border-red-200
                            @endif">
                            @if($verificationRequest->status === 'pending')
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-yellow-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-medium text-yellow-800">รอการอนุมัติ</h4>
                                        <p class="text-sm text-yellow-700">คำขอการยืนยันตัวตนรอการพิจารณา</p>
                                    </div>
                                </div>
                            @elseif($verificationRequest->status === 'approved')
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-medium text-green-800">อนุมัติแล้ว</h4>
                                        <p class="text-sm text-green-700">การยืนยันตัวตนได้รับการอนุมัติ</p>
                                    </div>
                                </div>
                            @elseif($verificationRequest->status === 'rejected')
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-medium text-red-800">ปฏิเสธแล้ว</h4>
                                        <p class="text-sm text-red-700">การยืนยันตัวตนถูกปฏิเสธ</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Student ID Image -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">รูปบัตรนักศึกษา</h3>
                        @if($verificationRequest->student_id_image_path)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <img src="{{ $verificationRequest->student_id_image_path }}" 
                                     alt="Student ID" 
                                     class="max-w-full h-auto rounded-lg shadow-lg">
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-lg p-4 text-center text-gray-500">
                                ไม่มีรูปภาพ
                            </div>
                        @endif
                    </div>

                    <!-- Processing Information -->
                    @if($verificationRequest->processed_by || $verificationRequest->reject_note)
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">ข้อมูลการกระทำการ</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                @if($verificationRequest->processedBy)
                                    <div class="mb-3">
                                        <p class="text-sm text-gray-500">ผู้กระทำการ</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $verificationRequest->processedBy->name }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="text-sm text-gray-500">วันที่กระทำการ</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $verificationRequest->process_at->format('d/m/Y') }}</p>
                                    </div>
                                @endif
                                @if($verificationRequest->reject_note)
                                    <div>
                                        <p class="text-sm text-gray-500">หมายเหตุ</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $verificationRequest->reject_note }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    @if($verificationRequest->status === 'pending')
                        <div class="flex space-x-4">
                            <!-- Approve Form -->
                            <form action="{{ route('admin.verification.approve', $verificationRequest->id) }}" method="POST" class="flex-1">
                                @csrf
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-green-800 mb-3">อนุมัติการยืนยันตัวตน</h4>
                                    <button type="submit" 
                                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                                        อนุมัติ
                                    </button>
                                </div>
                            </form>

                            <!-- Reject Form -->
                            <div class="flex-1">
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-red-800 mb-3">ปฏิเสธการยืนยันตัวตน</h4>
                                    <button type="button" 
                                            onclick="showRejectModal()"
                                            class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
                                        ปฏิเสธ
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($verificationRequest->status === 'pending')
        <form id="reject-form" action="{{ route('admin.verification.reject', $verificationRequest->id) }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="reject_note" id="reject-reason" />
        </form>
    @endif
</x-admin-layout>

<script>
    window.showRejectModal = function() {
        Swal.fire({
            title: 'ปฏิเสธการยืนยันตัวตน',
            html: `
                <div class="text-left space-y-2">
                  <label class="flex items-center gap-2"><input type="radio" name="reason" value="รูปไม่ชัด"> รูปไม่ชัด</label>
                  <label class="flex items-center gap-2"><input type="radio" name="reason" value="รูปไม่ใช่บัตรนักศึกษา"> รูปไม่ใช่บัตรนักศึกษา</label>
                  <label class="flex items-center gap-2"><input type="radio" name="reason" value="ข้อมูลไม่ตรงกับบัตร"> ข้อมูลไม่ตรงกับบัตร</label>
                  <label class="flex items-center gap-2"><input type="radio" name="reason" value="อื่นๆ"> อื่นๆ</label>
                  <input id="reason-text" type="text" placeholder="ระบุเหตุผลเพิ่มเติม (ถ้าเลือก อื่นๆ)" maxlength="50" class="w-full border rounded px-2 py-1" />
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'ปฏิเสธ',
            cancelButtonText: 'ยกเลิก',
            focusConfirm: false,
            preConfirm: () => {
                const selected = document.querySelector('input[name="reason"]:checked');
                const text = (document.getElementById('reason-text') || {}).value || '';
                let reason = selected ? selected.value : '';
                if (!reason) {
                    Swal.showValidationMessage('กรุณาเลือกเหตุผล');
                    return false;
                }
                if (reason === 'อื่นๆ') {
                    if (!text.trim()) {
                        Swal.showValidationMessage('กรุณาระบุเหตุผลเพิ่มเติม');
                        return false;
                    }
                    reason = text.trim();
                }
                return reason;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('reject-form');
                document.getElementById('reject-reason').value = result.value;
                form.submit();
            }
        });
    }

    // Character counter function for textareas (global scope)
    function updateCharCount(textareaId, counterId) {
        const textarea = document.getElementById(textareaId);
        const charCount = document.getElementById(counterId);
        if (textarea && charCount) {
            const currentLength = textarea.value.length;
            const maxLength = parseInt(textarea.getAttribute('maxlength')) || 255;
            
            charCount.textContent = currentLength;
            
            // Change color based on remaining characters
            if (currentLength > maxLength * 0.9) {
                charCount.style.color = '#ef4444'; // red
            } else if (currentLength > maxLength * 0.8) {
                charCount.style.color = '#f59e0b'; // orange
            } else {
                charCount.style.color = '#6b7280'; // gray
            }
        }
    }
</script>
