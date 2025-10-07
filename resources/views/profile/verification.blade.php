<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">การยืนยันตัวตน</h1>
                        <div class="text-sm text-gray-500">
                            จำเป็นต้องยืนยันตัวตนก่อนยืมอุปกรณ์
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex">
                                <div class="text-green-600">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex">
                                <div class="text-red-600">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Verification Status -->
                    <div class="mb-6">
                        @if(!$verificationRequest)
                            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-center">
                                    <div class="text-yellow-600">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">ยังไม่ได้ยืนยันตัวตน</h3>
                                        <p class="text-sm text-yellow-700 mt-1">กรุณาอัปโหลดรูปบัตรนักศึกษาเพื่อยืนยันตัวตน</p>
                                    </div>
                                </div>
                            </div>
                        @elseif($verificationRequest->status === 'pending')
                            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-center">
                                    <div class="text-blue-600">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800">รอการอนุมัติ</h3>
                                        <p class="text-sm text-blue-700 mt-1">คำขอการยืนยันตัวตนของคุณกำลังรอการอนุมัติจากผู้ดูแลระบบ</p>
                                    </div>
                                </div>
                            </div>
                        @elseif($verificationRequest->status === 'approved')
                            <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center">
                                    <div class="text-green-600">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-green-800">ยืนยันตัวตนสำเร็จ</h3>
                                        <p class="text-sm text-green-700 mt-1">คุณสามารถยืมอุปกรณ์ได้แล้ว</p>
                                    </div>
                                </div>
                            </div>
                        @elseif($verificationRequest->status === 'rejected')
                            <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                                <div class="flex items-center">
                                    <div class="text-red-600">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">การยืนยันตัวตนถูกปฏิเสธ</h3>
                                        <p class="text-sm text-red-700 mt-1">กรุณาอัปโหลดรูปบัตรนักศึกษาใหม่</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Admin Note -->
                    @if($verificationRequest && $verificationRequest->reject_note)
                        <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-900 mb-2">หมายเหตุจากผู้ดูแลระบบ</h3>
                            <p class="text-sm text-gray-700">{{ $verificationRequest->reject_note }}</p>
                            @if($verificationRequest->processed_by && $verificationRequest->process_at)
                                <p class="text-xs text-gray-500 mt-2">
                                    โดย: {{ $verificationRequest->processedBy->name ?? 'ผู้ดูแลระบบ' }} 
                                    เมื่อ: {{ $verificationRequest->process_at->format('d/m/Y') }}
                                </p>
                            @endif
                        </div>
                    @endif

                    <!-- Upload Form -->
                    @if(!$verificationRequest || $verificationRequest->status === 'rejected')
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4">
                                {{ !$verificationRequest ? 'อัปโหลดรูปบัตรนักศึกษา' : 'อัปโหลดรูปบัตรนักศึกษาใหม่' }}
                            </h2>
                            
                            <form action="{{ $verificationRequest ? route('verification.update') : route('verification.store') }}" 
                                  method="POST" enctype="multipart/form-data" id="verificationForm">
                                @csrf
                                @if($verificationRequest)
                                    @method('PUT')
                                @endif

                                <div class="mb-4">
                                    <label for="student_id_image" class="block text-sm font-medium text-gray-700 mb-2">
                                        รูปบัตรนักศึกษา
                                    </label>
                                    <input type="file" 
                                           id="student_id_image" 
                                           name="student_id_image" 
                                           accept="image/*"
                                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                           required
                                           onchange="previewImage(event)">
                                    <p class="text-xs text-gray-500 mt-1">
                                        รองรับไฟล์: JPG, JPEG, PNG, WEBP (ขนาดไม่เกิน 5MB)
                                    </p>
                                    
                                    <!-- Image Preview -->
                                    <div id="image-preview" class="mt-4 hidden">
                                        <p class="text-sm font-medium text-gray-700 mb-2">ตัวอย่างรูปภาพ:</p>
                                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                            <img id="preview-img" 
                                                 src="" 
                                                 alt="Student ID Preview" 
                                                 class="max-w-full h-auto max-h-64 mx-auto rounded-lg shadow-sm">
                                        </div>
                                        <button type="button" 
                                                onclick="removePreview()" 
                                                class="mt-2 text-sm text-red-600 hover:text-red-800">
                                             ลบรูปภาพ
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <h3 class="text-sm font-medium text-blue-900 mb-2">ข้อกำหนดการอัปโหลด</h3>
                                    <ul class="text-xs text-blue-800 space-y-1">
                                        <li>• รูปภาพต้องชัดเจน อ่านได้ง่าย</li>
                                        <li>• แสดงข้อมูลส่วนตัวชัดเจน (ชื่อ-นามสกุน, รหัสนักศึกษา)</li>
                                        <li>• ไม่มีการแก้ไขหรือบิดเบือน</li>
                                        <li>• ขนาดไฟล์ไม่เกิน 5MB</li>
                                    </ul>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" 
                                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                        {{ !$verificationRequest ? 'ส่งคำขอยืนยันตัวตน' : 'อัปเดตคำขอ' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- Current Image Display -->
                    @if($verificationRequest && $verificationRequest->student_id_image_path)
                        <div class="mt-6 bg-white border border-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">รูปบัตรนักศึกษาที่อัปโหลด</h3>
                            <div class="flex justify-center">
                                <img src="{{ $verificationRequest->student_id_image_path }}" 
                                     alt="Student ID" 
                                     class="max-w-full h-auto max-h-96 rounded-lg shadow-lg">
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');
            
            if (file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('กรุณาเลือกไฟล์รูปภาพเท่านั้น (JPG, JPEG, PNG, WEBP)');
                    event.target.value = '';
                    return;
                }
                
                // Validate file size (5MB = 5 * 1024 * 1024 bytes)
                if (file.size > 5 * 1024 * 1024) {
                    alert('ขนาดไฟล์ต้องไม่เกิน 5MB');
                    event.target.value = '';
                    return;
                }
                
                // Create FileReader to preview image
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('hidden');
            }
        }
        
        function removePreview() {
            const fileInput = document.getElementById('student_id_image');
            const preview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');
            
            fileInput.value = '';
            previewImg.src = '';
            preview.classList.add('hidden');
        }
    </script>
</x-app-layout>
