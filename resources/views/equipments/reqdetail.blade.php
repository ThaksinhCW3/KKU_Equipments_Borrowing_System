<x-app-layout>
    @section('title', 'คำขอยืมอุปกรณ์')
    <div class="max-w-screen-2xl mx-auto py-6 px-3 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">คำขอยืมอุปกรณ์</h1>

        @if (session('success'))
            <script>
                Swal.fire({
                    title: 'สำเร็จ!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonText: 'ตกลง',
                    timer: 3000,
                    timerProgressBar: true
                });
            </script>
        @endif
        @php
            $allRequests = $reQuests;
        @endphp
        @if ($allRequests->isEmpty())
            <div class="text-center py-10 text-gray-500 ">
                <svg class="mx-auto mb-4 w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6M9 8l6 6">
                    </path>
                </svg>
                <p class="font-medium">คุณยังไม่มีคำขอ</p>
            </div>
        @else
            @foreach ($allRequests as $req)
                <div class="bg-white rounded-2xl shadow p-3 mb-10">

                    @if ($req->status === 'pending')
                        <div class="mb-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-r-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-yellow-400 mr-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                    </path>
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-yellow-800">คำขอของคุณกำลังรอการอนุมัติ</h3>
                                    <p class="text-sm text-yellow-700 mt-1">กรุณารอการพิจารณาจากเจ้าหน้าที่
                                        ระบบจะแจ้งเตือนเมื่อแอดมินตอบรับคำขอแล้วผ่าน email</p>
                                </div>
                            </div>
                        </div>
                    @elseif ($req->status === 'approved')
                        @if ($req->pickup_deadline && $req->pickup_deadline > now())
                                            <div class="mb-3 p-2 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start space-x-2">
                        <div class="text-yellow-600 mt-0.5">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        
                        <div class="text-xs text-yellow-800">
                            <p class="font-medium mb-0.5"> เวลารับอุปกรณ์</p>
                            <p class="text-xs leading-relaxed">
                                หลังจากได้รับการอนุมัติ กรุณามารับอุปกรณ์ภายในเวลาที่กำหนด<br>
                                <span class="font-medium">เช้า: 09:00-10:00 น. | บ่าย: 14:00-15:00 น.</span>
                            </p>
                        </div>
                    </div>
                </div>
                            <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-400 rounded-r-lg">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>

                                    <div>
                                        <h3 class="text-sm font-medium text-green-800">คำขอของคุณได้รับการอนุมัติแล้ว!
                                        </h3>
                                        <p class="text-sm text-green-700 mt-1">
                                            กรุณามารับอุปกรณ์ภายในวันที่
                                            <strong>{{ $req->pickup_deadline->format('d/m/Y') }}</strong>

                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mb-4 p-4 bg-orange-50 border-l-4 border-orange-400 rounded-r-lg">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-orange-400 mr-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <h3 class="text-sm font-medium text-orange-800">หมดเขตการรับอุปกรณ์แล้ว</h3>
                                        <p class="text-sm text-orange-700 mt-1">
                                            คำขอของคุณอาจถูกยกเลิกอัตโนมัติเนื่องจากไม่มารับอุปกรณ์ภายในเวลาที่กำหนด</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @elseif ($req->status === 'rejected')
                        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-400 rounded-r-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-red-400 mr-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-red-800">คำขอของคุณถูกปฏิเสธ</h3>
                                    <p class="text-sm text-red-700 mt-1">กรุณาตรวจสอบเหตุผลการปฏิเสธด้านล่าง
                                        หากต้องการยืมอุปกรณ์อื่น กรุณาส่งคำขอใหม่</p>
                                </div>
                            </div>
                        </div>
                    @elseif ($req->status === 'cancelled')
                        <div class="mb-4 p-4 bg-gray-50 border-l-4 border-gray-400 rounded-r-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-800">คำขอของคุณถูกยกเลิกแล้ว</h3>
                                    <p class="text-sm text-gray-700 mt-1">หากต้องการยืมอุปกรณ์ กรุณาส่งคำขอใหม่</p>
                                </div>
                            </div>
                        </div>
                    @elseif ($req->status === 'check_out')
                        <div class="mb-4 p-4 bg-blue-50 border-l-4 border-blue-400 rounded-r-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-400 mr-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-blue-800">คุณได้รับอุปกรณ์แล้ว</h3>
                                    <p class="text-sm text-blue-700 mt-1">กรุณาคืนอุปกรณ์ภายในวันที่กำหนด
                                        เพื่อหลีกเลี่ยงค่าปรับ</p>
                                </div>
                            </div>
                        </div>
                    @elseif ($req->status === 'check_in')
                        <div class="mb-4 p-4 bg-purple-50 border-l-4 border-purple-400 rounded-r-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-purple-400 mr-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-purple-800">การยืมเสร็จสิ้น</h3>
                                    <p class="text-sm text-purple-700 mt-1">คุณได้คืนอุปกรณ์เรียบร้อยแล้ว
                                        ขอบคุณที่ใช้งานระบบยืมอุปกรณ์</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mb-4 p-4 bg-gray-50 border-l-4 border-gray-400 rounded-r-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-800">สถานะ: {{ ucfirst($req->status) }}
                                    </h3>
                                    <p class="text-sm text-gray-700 mt-1">
                                        กรุณาติดต่อเจ้าหน้าที่หากต้องการข้อมูลเพิ่มเติม</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    <!-- Header -->
                    <div class="flex justify-between items-center border-b pb-4 mb-4">
                        <div class="lg:flex">
                            <h2 class="text-lg font-semibold text-gray-800">คำขอเลขที่: &nbsp</h2>
                            <h2 class="text-lg font-semibold text-gray-800">
                                #{{ $req->req_id }}
                            </h2>
                        </div>
                        <span
                            class="px-4 py-2 text-sm font-semibold rounded-lg border-2
                                @if ($req->status === 'pending') bg-yellow-50 text-yellow-800 border-yellow-300
                                @elseif($req->status === 'approved') bg-green-50 text-green-800 border-green-300
                                @elseif($req->status === 'rejected') bg-red-50 text-red-800 border-red-300
                                @elseif($req->status === 'cancelled') bg-gray-50 text-gray-800 border-gray-300
                                @elseif($req->status === 'check_out') bg-blue-50 text-blue-800 border-blue-300
                                @elseif($req->status === 'check_in') bg-purple-50 text-purple-800 border-purple-300
                                @else bg-gray-50 text-gray-800 border-gray-300 @endif">
                            @if ($req->status === 'pending')
                                <span class="inline-block w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>รออนุมัติ
                            @elseif($req->status === 'approved')
                                <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-2"></span>อนุมัติ
                            @elseif($req->status === 'rejected')
                                <span class="inline-block w-2 h-2 bg-red-500 rounded-full mr-2"></span>ปฏิเสธ
                            @elseif($req->status === 'cancelled')
                                <span class="inline-block w-2 h-2 bg-gray-500 rounded-full mr-2"></span>ยกเลิกแล้ว
                            @elseif($req->status === 'check_out')
                                <span class="inline-block w-2 h-2 bg-blue-500 rounded-full mr-2"></span>รับแล้ว
                            @elseif($req->status === 'check_in')
                                <span class="inline-block w-2 h-2 bg-purple-500 rounded-full mr-2"></span>คืนแล้ว
                            @else
                                <span
                                    class="inline-block w-2 h-2 bg-gray-500 rounded-full mr-2"></span>{{ ucfirst($req->status) }}
                            @endif
                        </span>
                    </div>
                    <!-- Penalty Information -->
                    @if ($req->transaction && $req->transaction->penalty_amount > 0)
                        <div class="mb-4">
                            <div class="p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-r-lg">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-yellow-400 mr-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                        </path>
                                    </svg>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-yellow-800">มีค่าปรับ</h4>
                                        <p class="text-sm text-yellow-700 mt-1">
                                            <strong>จำนวน:
                                                ฿{{ number_format($req->transaction->penalty_amount, 2) }}</strong>
                                        </p>
                                        @if ($req->transaction->penalty_check === 'paid')
                                            <p class="text-sm text-green-600 mt-1">
                                                <span
                                                    class="inline-block w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                                สถานะ: ชำระแล้ว
                                            </p>
                                        @else
                                            <p class="text-sm text-red-600 mt-1">
                                                <span class="inline-block w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                                สถานะ: ยังไม่ชำระ
                                            </p>
                                            <p class="text-xs text-yellow-600 mt-1">
                                                กรุณาชำระค่าปรับตามที่กำหนด
                                            </p>
                                        @endif
                                        @if ($req->transaction->notes)
                                            <p class="text-xs text-yellow-600 mt-1">
                                                <strong>หมายเหตุ:</strong> {{ $req->transaction->notes }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <!-- Equipment -->
                    <div class="mb-4 flex flex-col sm:flex-row gap-4">
                        @php
                            $photos = json_decode($req->equipment->photo_path ?? '[]', true);
                            $firstPhoto =
                                is_array($photos) && count($photos) > 0 ? $photos[0] : $req->equipment->photo_path;
                        @endphp
                        <img src="{{ $firstPhoto }}" alt="equipment photo"
                            class="w-full sm:w-32 h-40 sm:h-32 object-contain bg-gray-100 rounded-lg shadow max-w-full cursor-pointer hover:opacity-80 transition-opacity"
                            onclick="openImageModal('{{ $firstPhoto }}', '{{ $req->equipment->name }}')">
                        <div class="space-y-3 flex-1">
                            <h3 class="font-semibold text-gray-800 break-words">{{ $req->equipment->name }}</h3>
                            <p class="text-sm text-gray-500 break-words">รหัส: {{ $req->equipment->code }}</p>
                            <p class="text-sm text-gray-500 break-words">หมวดหมู่:
                                {{ $req->equipment->category->name }}</p>
                            <div class="text-gray-500 text-sm">
                                <div x-data="{ expanded: false }" class="block md:hidden">
                                    <span x-show="!expanded" class="break-words">
                                        {{ \Illuminate\Support\Str::limit($req->equipment->description, 80, '...') }}
                                    </span>
                                    <span x-show="expanded" class="break-words">
                                        {{ $req->equipment->description }}
                                    </span>
                                    <button @click="expanded = !expanded"
                                        class="ml-2 text-blue-600 underline focus:outline-none">
                                        <span x-show="!expanded">ดูเพี่มเตีม</span>
                                        <span x-show="expanded">แสดงน้อยลง</span>
                                    </button>
                                </div>

                                <div class="hidden md:block">
                                    <span class="break-words">{{ $req->equipment->description }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Accessories -->
                    @php
                        $accessories = $req->equipment->accessories;
                        // Handle both array and string formats
                        if (is_string($accessories)) {
                            $accessories = json_decode($accessories, true) ?: [];
                        }
                        if (!is_array($accessories)) {
                            $accessories = [];
                        }
                    @endphp
                    @if (!empty($accessories))
                        <div class="mb-4">
                            <h3 class="font-semibold text-gray-700">อุปกรณ์ที่ติดมากับเครื่อง</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach ($accessories as $accessory)
                                    <div class="flex items-center space-x-2 p-2 bg-gray-50 rounded-lg">
                                        <span class="text-sm text-gray-700">{{ $accessory }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mb-4">
                        <h3 class="font-semibold text-gray-700">ระยะเวลายืม</h3>
                        <p class="text-sm text-gray-600">เริ่มต้น: {{ $req->start_at->format('d-m-Y') }}</p>
                        <p class="text-sm text-gray-600">สิ้นสุด: {{ $req->end_at->format('d-m-Y') }}</p>
                    </div>

                    <!-- Check Out/In Status -->
                    @if ($req->transaction && !in_array($req->status, ['cancelled', 'rejected']))
                        <div class="mb-4">
                            <h3 class="font-semibold text-gray-700">สถานะการยืม</h3>
                            @if ($req->transaction->checked_out_at)
                                <p class="text-sm text-green-600">
                                    <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                    รับอุปกรณ์: {{ $req->transaction->checked_out_at->format('d/m/Y') }}
                                </p>
                            @else
                                <p class="text-sm text-gray-500">
                                    <span class="inline-block w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                                    ยังไม่ได้รับอุปกรณ์
                                </p>
                            @endif

                            @if ($req->transaction->checked_in_at)
                                <p class="text-sm text-blue-600">
                                    <span class="inline-block w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                    คืนอุปกรณ์: {{ $req->transaction->checked_in_at->format('d/m/Y') }}
                                </p>
                            @elseif($req->transaction->checked_out_at)
                                <p class="text-sm text-orange-600">
                                    <span class="inline-block w-2 h-2 bg-orange-500 rounded-full mr-2"></span>
                                    ยังไม่คืนอุปกรณ์
                                </p>
                            @endif
                        </div>
                    @endif

                    @if ($req->request_reason)
                        <div class="mb-4">
                            <h3 class="font-semibold text-gray-700">เหตุผลในการยืม</h3>
                            <p class="text-sm text-gray-600 mb-1">
                                <span class="text-gray-500">ประเภท:</span>
                                @if ($req->request_reason === 'assignment')
                                    งานมอบหมาย/การบ้าน
                                @elseif($req->request_reason === 'personal')
                                    ใช้ส่วนตัว
                                @elseif($req->request_reason === 'others')
                                    อื่นๆ
                                @else
                                    {{ $req->request_reason }}
                                @endif
                            </p>
                            @if ($req->request_reason_detail)
                                <p class="text-sm text-gray-600">
                                    <span class="text-gray-500">รายละเอียด:</span> {{ $req->request_reason_detail }}
                                </p>
                            @endif
                        </div>
                    @endif

                    @if ($req->status === 'rejected')
                        <div class="mb-4">
                            <h1 class="text-lg text-red-600 font-semibold">เหตุผลปฏิเสธ</h1>
                            <p class="text-sm text-red-600">{{ $req->reject_reason }}</p>
                        </div>
                    @endif

                    @if ($req->status === 'cancelled')
                        <div class="mb-4">
                            <h1 class="text-lg text-gray-600 font-semibold">เหตุผลการยกเลิก</h1>
                            <p class="text-sm text-gray-600">{{ $req->cancel_reason }}</p>
                        </div>
                    @endif

                    <!-- Status Notifications -->

                    <div class="mt-6">
                        <div class="flex flex-col sm:flex-row gap-3 justify-between">
                            <a href="{{ route('borrower.equipments.myreq') }}"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition text-center">
                                กลับ
                            </a>
                            @if ($req->status === 'pending')
                                <button onclick="showCancelModal({{ $req->id }})"
                                    class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                                    ยกเลิกคำขอ
                                </button>
                            @endif
                        </div>

                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="relative max-w-4xl max-h-full p-4">
            <button onclick="closeImageModal()" class="absolute top-2 right-2 text-white text-2xl font-bold hover:text-gray-300 z-10">
                ×
            </button>
            <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg">
            <div class="text-center mt-4">
                <h3 id="modalTitle" class="text-white text-lg font-semibold"></h3>
            </div>
        </div>
    </div>

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                text: '{{ session('error') }}',
                timer: 2500,
                showConfirmButton: false
            });
        </script>
    @endif

    <script>
        // Image modal functions
        window.openImageModal = function(imageSrc, equipmentName) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('modalTitle').textContent = equipmentName;
            document.getElementById('imageModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        window.closeImageModal = function() {
            document.getElementById('imageModal').classList.add('hidden');
            document.body.style.overflow = 'auto'; // Restore scrolling
        }

        // Close modal when clicking outside the image
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });

        // SweetAlert cancel modal function
        window.showCancelModal = function(requestId) {
            Swal.fire({
                title: 'เลือกเหตุผลการยกเลิก',
                html: `
                    <div class="text-left space-y-2">
                      <label class="flex items-center gap-2"><input type="radio" name="reason" value="เปลี่ยนใจ"> เปลี่ยนใจ</label>
                      <label class="flex items-center gap-2"><input type="radio" name="reason" value="เลือกอุปกรณ์ผิด"> เลือกอุปกรณ์ผิด</label>
                      <label class="flex items-center gap-2"><input type="radio" name="reason" value="อื่นๆ"> อื่นๆ</label>
                      <input id="reason-text" type="text" placeholder="ระบุเหตุผลอื่น..." maxlength="50" class="w-full border rounded px-2 py-1" />
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'ยืนยันการยกเลิก',
                cancelButtonText: 'ปิด',
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
                    // Create and submit form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/borrower/requests/${requestId}/cancel`;

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'PATCH';

                    const reasonField = document.createElement('input');
                    reasonField.type = 'hidden';
                    reasonField.name = 'cancel_reason[]';
                    reasonField.value = result.value;

                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    form.appendChild(reasonField);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>
