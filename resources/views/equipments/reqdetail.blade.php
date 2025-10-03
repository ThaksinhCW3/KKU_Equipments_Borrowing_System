<x-app-layout>
    <div class="max-w-screen-2xl mx-auto py-6 px-3 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">คำขอยืมอุปกรณ์</h1>

        @if(session('success'))
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
                    <svg class="mx-auto mb-4 w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6M9 8l6 6">
                        </path>
                    </svg>
                    <p class="font-medium">คุณยังไม่มีคำขอ</p>
                </div>
            @else
                @foreach ($allRequests as $req)
                    <div class="bg-white rounded-2xl shadow p-3 mb-10" x-data="{ openModal: false }">
                        @if ($req->status === 'pending')
                            <div class="mb-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-r-lg">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    <div>
                                        <h3 class="text-sm font-medium text-yellow-800">คำขอของคุณกำลังรอการอนุมัติ</h3>
                                        <p class="text-sm text-yellow-700 mt-1">กรุณารอการพิจารณาจากเจ้าหน้าที่ ระบบจะแจ้งเตือนเมื่อแอดมินตอบรับคำขอแล้วผ่าน email</p>
                                    </div>
                                </div>
                            </div>
                        @elseif ($req->status === 'approved')
                            @if($req->pickup_deadline && $req->pickup_deadline > now())
                                <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-400 rounded-r-lg">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <h3 class="text-sm font-medium text-green-800">คำขอของคุณได้รับการอนุมัติแล้ว!</h3>
                                            <p class="text-sm text-green-700 mt-1">
                                                กรุณามารับอุปกรณ์ภายในวันที่ <strong>{{ $req->pickup_deadline->format('d/m/Y H:i') }}</strong>
                                                @if($req->pickup_deadline->diffInHours(now()) <= 24)
                                                    <span class="text-orange-600 font-semibold">(เหลือเวลาอีก {{ $req->pickup_deadline->diffInHours(now()) }} ชั่วโมง)</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="mb-4 p-4 bg-orange-50 border-l-4 border-orange-400 rounded-r-lg">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-orange-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <h3 class="text-sm font-medium text-orange-800">หมดเขตการรับอุปกรณ์แล้ว</h3>
                                            <p class="text-sm text-orange-700 mt-1">คำขอของคุณอาจถูกยกเลิกอัตโนมัติเนื่องจากไม่มารับอุปกรณ์ภายในเวลาที่กำหนด</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @elseif ($req->status === 'rejected')
                            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-400 rounded-r-lg">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <div>
                                        <h3 class="text-sm font-medium text-red-800">คำขอของคุณถูกปฏิเสธ</h3>
                                        <p class="text-sm text-red-700 mt-1">กรุณาตรวจสอบเหตุผลการปฏิเสธด้านล่าง หากต้องการยืมอุปกรณ์อื่น กรุณาส่งคำขอใหม่</p>
                                    </div>
                                </div>
                            </div>
                        @elseif ($req->status === 'cancelled')
                            <div class="mb-4 p-4 bg-gray-50 border-l-4 border-gray-400 rounded-r-lg">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-800">คำขอของคุณถูกยกเลิกแล้ว</h3>
                                        <p class="text-sm text-gray-700 mt-1">หากต้องการยืมอุปกรณ์ กรุณาส่งคำขอใหม่</p>
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
                            <span class="px-4 py-2 text-sm font-semibold rounded-lg border-2
                                @if ($req->status === 'pending') bg-yellow-50 text-yellow-800 border-yellow-300
                                @elseif($req->status === 'approved') bg-green-50 text-green-800 border-green-300
                                @elseif($req->status === 'rejected') bg-red-50 text-red-800 border-red-300
                                @elseif($req->status === 'cancelled') bg-gray-50 text-gray-800 border-gray-300
                                @else bg-gray-50 text-gray-800 border-gray-300 @endif">
                                @if ($req->status === 'pending')
                                    <span class="inline-block w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>รอดำเนินการ
                                @elseif($req->status === 'approved')
                                    <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-2"></span>อนุมัติ
                                @elseif($req->status === 'rejected')
                                    <span class="inline-block w-2 h-2 bg-red-500 rounded-full mr-2"></span>ปฏิเสธ
                                @elseif($req->status === 'cancelled')
                                    <span class="inline-block w-2 h-2 bg-gray-500 rounded-full mr-2"></span>ยกเลิกแล้ว
                                @else
                                    <span class="inline-block w-2 h-2 bg-gray-500 rounded-full mr-2"></span>{{ $req->status }}
                                @endif
                            </span>
                        </div>
                        <!-- Equipment -->
                        <div class="mb-4 flex flex-col sm:flex-row gap-4">
                            @php
                                $photos = json_decode($req->equipment->photo_path ?? '[]', true);
                                $firstPhoto = is_array($photos) && count($photos) > 0 ? $photos[0] : $req->equipment->photo_path;
                            @endphp
                            <img src="{{ $firstPhoto }}" alt="equipment photo"
                                class="w-full sm:w-28 h-28 object-cover rounded-lg shadow">
                            <div class="space-y-3 flex-1">
                                <h3 class="font-semibold text-gray-800 break-words">{{ $req->equipment->name }}</h3>
                                <p class="text-sm text-gray-500 break-words">รหัส: {{ $req->equipment->code }}</p>
                                <p class="text-sm text-gray-500 break-words">หมวดหมู่: {{ $req->equipment->category->name }}</p>
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
                        @if(!empty($accessories))
                            <div class="mb-4">
                                <h3 class="font-semibold text-gray-700">อุปกรณ์ที่ติดมากับเครื่อง</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($accessories as $accessory)
                                        <div class="flex items-center space-x-2 p-2 bg-gray-50 rounded-lg">
                                            <span class="text-sm text-gray-700">{{ $accessory }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="mb-4">
                            <h3 class="font-semibold text-gray-700">ระยะเวลายืม</h3>
                            <p class="text-sm text-gray-600">เริ่มต้น: {{ $req->start_at->format('d/m/Y') }}</p>
                            <p class="text-sm text-gray-600">สิ้นสุด: {{ $req->end_at->format('d/m/Y') }}</p>
                        </div>

                        <!-- Check Out/In Status -->
                        @if($req->transaction)
                            <div class="mb-4">
                                <h3 class="font-semibold text-gray-700">สถานะการยืม</h3>
                                @if($req->transaction->checked_out_at)
                                    <p class="text-sm text-green-600">
                                        <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                        รับอุปกรณ์: {{ $req->transaction->checked_out_at->format('d/m/Y H:i') }}
                                    </p>
                                @else
                                    <p class="text-sm text-gray-500">
                                        <span class="inline-block w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                                        ยังไม่ได้รับอุปกรณ์
                                    </p>
                                @endif

                                @if($req->transaction->checked_in_at)
                                    <p class="text-sm text-blue-600">
                                        <span class="inline-block w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                        คืนอุปกรณ์: {{ $req->transaction->checked_in_at->format('d/m/Y H:i') }}
                                    </p>
                                @elseif($req->transaction->checked_out_at)
                                    <p class="text-sm text-orange-600">
                                        <span class="inline-block w-2 h-2 bg-orange-500 rounded-full mr-2"></span>
                                        ยังไม่คืนอุปกรณ์
                                    </p>
                                @endif
                            </div>
                        @endif

                        @if($req->request_reason)
                            <div class="mb-4">
                                <h3 class="font-semibold text-gray-700">เหตุผลในการยืม</h3>
                                <p class="text-sm text-gray-600 mb-1">
                                    <span class="text-gray-500">ประเภท:</span> 
                                    @if($req->request_reason === 'assignment')
                                        งานมอบหมาย/การบ้าน
                                    @elseif($req->request_reason === 'personal')
                                        ใช้ส่วนตัว
                                    @elseif($req->request_reason === 'others')
                                        อื่นๆ
                                    @else
                                        {{ $req->request_reason }}
                                    @endif
                                </p>
                                @if($req->request_reason_detail)
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
                                <a href="{{route('borrower.equipments.myreq')}}"
                                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition text-center">
                                    กลับ
                                </a>
                                @if ($req->status === 'pending')
                                <button @click="openModal = true"
                                    class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                                    ยกเลิกคำขอ
                                </button>
                                @endif
                            </div>
                            
                            <!-- Modal -->
                            <div x-show="openModal"
                                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                                x-cloak>
                                <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                                    <h3 class="text-lg font-semibold mb-4">เลือกเหตุผลการยกเลิก</h3>

                                    <form action="{{ route('borrower.requests.cancel', $req->id) }}" method="POST"
                                        x-data="{ otherChecked: false }">
                                        @csrf
                                        @method('PATCH')
                                        <div class="space-y-2 mb-4">
                                            <label class="flex items-center gap-2">
                                                <input type="checkbox" name="cancel_reason[]" value="เปลี่ยนใจ"
                                                    class="text-red-600 rounded" @click="otherChecked = false">
                                                <span>เปลี่ยนใจ</span>
                                            </label>
                                            <label class="flex items-center gap-2">
                                                <input type="checkbox" name="cancel_reason[]" value="เลือกอุปกรณ์ผิด"
                                                    class="text-red-600 rounded" @click="otherChecked = false">
                                                <span>เลือกอุปกรณ์ผิด</span>
                                            </label>
                                            <label class="flex items-center gap-2">
                                                <input type="checkbox" name="cancel_reason[]" value="อื่น ๆ"
                                                    class="text-red-600 rounded" x-model="otherChecked">
                                                <span>อื่น ๆ</span>
                                            </label>
                                            <input type="text" name="cancel_reason[]" placeholder="ระบุเหตุผลอื่น..."
                                                class="mt-2 w-full border rounded px-3 py-2" x-show="otherChecked"
                                                x-transition>
                                        </div>

                                        <div class="flex justify-end gap-3">
                                            <button type="button" @click="openModal = false"
                                                class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">
                                                ปิด
                                            </button>
                                            <button type="submit"
                                                class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                                                ยืนยันการยกเลิก
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
    </div>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'ยกเลิกสำเร็จ!',
                text: '{{ session('success') }}',
                timer: 2500,
                showConfirmButton: false
            });
        </script>
    @endif

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
</x-app-layout>
