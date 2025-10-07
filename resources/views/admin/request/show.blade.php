<x-admin-layout>
    <div class="max-w-7xl mx-auto bg-white p-4 sm:p-6 rounded shadow">
        <div class="flex flex-col lg:flex-row items-start gap-6">
            @if ($requests->equipment?->photo_path)
                @php
                    $photos = json_decode($requests->equipment->photo_path ?? '[]', true);
                    $firstPhoto =
                        is_array($photos) && count($photos) > 0 ? $photos[0] : $requests->equipment->photo_path;
                @endphp
                <img src="{{ $firstPhoto }}" alt="{{ $requests->equipment->name }}"
                    class="w-full sm:w-40 h-40 object-cover rounded border" />
            @endif
            <div class="flex-1 w-full">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <h2 class="text-xl sm:text-2xl font-bold">คำขอยืม #{{ $requests->req_id }}</h2>
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm {{ $requests->status === 'approved'
                            ? 'bg-green-100 text-green-700'
                            : ($requests->status === 'rejected'
                                ? 'bg-red-100 text-red-700'
                                : ($requests->status === 'check_out'
                                    ? 'bg-blue-100 text-blue-700'
                                    : ($requests->status === 'check_in'
                                        ? 'bg-purple-100 text-purple-700'
                                        : 'bg-yellow-100 text-yellow-700'))) }}">
                        @if ($requests->status === 'pending')
                            รออนุมัติ
                        @elseif($requests->status === 'approved')
                            อนุมัติแล้ว
                        @elseif($requests->status === 'rejected')
                            ปฏิเสธ
                        @elseif($requests->status === 'check_out')
                            รับแล้ว
                        @elseif($requests->status === 'check_in')
                            คืนแล้ว
                        @else
                        {{ ucfirst($requests->status) }}
                        @endif
                    </span>
                </div>
                <p class="text-gray-500 mt-1">สร้างเมื่อ {{ $requests->created_at->format('d-m-Y') }}</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2">ข้อมูลผู้ใช้</h3>
                        <div class="space-y-2 text-sm">
                            <p><span class="text-gray-500">ชื่อ-นามสกุล:</span> {{ $requests->user->name ?? '-' }}</p>
                            <p><span class="text-gray-500">UID:</span> {{ $requests->user->uid ?? '-' }}</p>
                            <p><span class="text-gray-500">อีเมล:</span> {{ $requests->user->email ?? '-' }}</p>


                        </div>
                        <div class="my-3 font-semibold ">
                            <span class="text-gray-500 my-2">วันที่รับ:</span>
                            <span
                                class="font-medium my-2">{{ $requests->start_at ? $requests->start_at->format('d-m-Y') : '-' }}</span><br>
                            <span class="font-medium my @if ($requests->status === 'pending') hidden @endif">วันที่ส่ง:
                                {{ $requests->end_at ? $requests->end_at->format('d-m-Y') : '-' }}</span>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2">ข้อมูลอุปกรณ์</h3>
                        <div class="space-y-1 text-sm">
                            <p><span class="text-gray-500">ชื่ออุปกรณ์:</span> {{ $requests->equipment->name ?? '-' }}
                            </p>
                            <p><span class="text-gray-500">รหัสอุปกรณ์:</span> {{ $requests->equipment->code ?? '-' }}
                            </p>
                            <p><span class="text-gray-500">หมวดหมู่:</span>
                                {{ $requests->equipment->category->name ?? '-' }}</p>

                            @php
                                $accessories = json_decode($requests->equipment->accessories ?? '[]', true);
                            @endphp

                            @if (!empty($accessories) && is_array($accessories))
                                <div class="mt-2">
                                    <p class="text-gray-500 mb-1">เซ็ตของที่ติดมากับเครื่อง:</p>
                                    <div class="space-y-1">
                                        @foreach ($accessories as $accessory)
                                            <div class="flex items-center">
                                                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-2"></span>
                                                <span class="text-gray-700 text-xs">{{ $accessory }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <p class="text-gray-500 text-xs mt-2">เซ็ตของที่ติดมากับเครื่อง: ไม่มี</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="font-semibold text-gray-700 mb-2">เหตุผลในการยืม</h3>
                    <div class="bg-gray-50 rounded p-4">
                        @if ($requests->request_reason)
                            <p class="text-sm mb-2">
                                <span class="text-gray-500">ประเภทเหตุผล:</span> 
                                <span class="font-medium">
                                    @if ($requests->request_reason === 'assignment')
                                        งานมอบหมาย/การบ้าน
                                    @elseif($requests->request_reason === 'personal')
                                        ใช้ส่วนตัว
                                    @elseif($requests->request_reason === 'others')
                                        อื่นๆ
                                    @else
                                        {{ $requests->request_reason }}
                                    @endif
                                </span>
                            </p>
                            
                            @if ($requests->request_reason_detail)
                                <p class="text-sm">
                                    <span class="text-gray-500">รายละเอียด:</span> 
                                    <span class="font-medium">{{ $requests->request_reason_detail }}</span>
                                </p>
                            @endif
                        @else
                            <p class="text-sm text-gray-500">ไม่ระบุเหตุผล</p>
                        @endif
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="font-semibold text-gray-700 mb-2">ธุรกรรม</h3>
                    <div class="bg-gray-50 rounded p-4">
                        <div class="space-y-3">

                            <!-- Created Date -->
                            <div class="">
                                <span class="text-gray-500">วันที่สร้างคำขอ:</span>
                                <span class="font-medium">{{ $requests->created_at->format('d-m-Y') }}</span>
                            </div>

                            <!-- Approved Date -->
                            @if ($requests->status !== 'pending' && $requests->status !== 'rejected')
                                <div class="">
                                    <span class="text-gray-500">วันที่อนุมัติ:</span>
                                    <span class="font-medium">{{ $requests->updated_at->format('d-m-Y') }}</span>
                                </div>
                            @endif

                            <!-- Rejected Date and Reason -->
                            @if ($requests->status === 'rejected')
                                <div class="">
                                    <span class="text-gray-500">วันที่ปฏิเสธ:</span>
                                    <span class="font-medium">{{ $requests->updated_at->format('d-m-Y') }}</span>
                                </div>
                                @if ($requests->reject_reason)
                                    <div class="flex items-start justify-between">
                                        <span class="text-gray-500">เหตุผลการปฏิเสธ:</span>
                                        <span class="font-medium text-red-600">{{ $requests->reject_reason }}</span>
                                    </div>
                                @endif
                            @endif

                            <!-- Check Out Date -->
                            @if ($requests->status === 'check_out' || $requests->status === 'check_in')
                                @if ($requests->transaction && $requests->transaction->checked_out_at)
                                    <div class="">
                                        <span class="text-gray-500">วันที่มารับของ:</span>
                                        <span
                                            class="font-medium">{{ $requests->transaction->checked_out_at->format('d-m-Y') }}</span>
                                    </div>
                                @endif
                            @endif

                            <!-- Check In Date -->
                            @if ($requests->status === 'check_in')
                                @if ($requests->transaction && $requests->transaction->checked_in_at)
                                    <div class="">
                                        <span class="text-gray-500">วันที่มาคืนของ:</span>
                                        <span
                                            class="font-medium">{{ $requests->transaction->checked_in_at->format('d-m-Y') }}</span>
                                    </div>
                                @endif
                            @endif

                            <!-- Penalty Information -->
                            @if ($requests->transaction && $requests->transaction->penalty_amount > 0)
                                <div class="">
                                    <span class="text-gray-500">ค่าปรับ:</span>
                                    <span
                                        class="font-medium text-red-600">฿{{ number_format($requests->transaction->penalty_amount, 2) }}</span>
                                </div>
                            @endif

                            <!-- Notes -->
                            @if ($requests->transaction && $requests->transaction->notes)
                                <div class="">
                                    <span class="text-gray-500">หมายเหตุ:</span>
                                    <span class="font-medium text-gray-700">{{ $requests->transaction->notes }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>


                <div class="mt-8 relative">
                    <form action="{{ route('admin.requests.approve', $requests->req_id) }}" method="POST"
                        class="space-y-5">
                        @csrf
                        @method('PATCH')
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="mt-6 @if ($requests->status !== 'pending') hidden @endif">
                                <h3 class="font-semibold text-gray-700 mb-2">วันที่ขอใช้</h3>
                                <div class="bg-gray-50 rounded p-4 ">
                                    <div>
                                        <input type="text" id="start_at" name="start_at"
                                            class="w-full border rounded hidden px-2 py-1"
                                            value="{{ optional($requests->start_at)->format('d-m-Y') }}" readonly />
                                        <span class="text-gray-500">วันที่รับ:</span>
                                        <span
                                            class="font-medium">{{ $requests->start_at ? $requests->start_at->format('d-m-Y') : '-' }}</span>
                                    </div>
                                    <span
                                        class="font-medium @if ($requests->status === 'pending') hidden @endif">วันที่ส่ง:
                                        {{ $requests->end_at ? $requests->start_at->format('d-m-Y') : '-' }}</span>
                                    <div class="@if ($requests->status !== 'pending') hidden @endif">
                                        <span class="text-gray-500">วันที่ส่ง:</span>
                                        <div class="relative ">
                                            <input type="text" id="end_at" name="end_at"
                                                class="w-full border rounded px-2 py-1 pr-10"
                                                value="{{ optional($requests->end_at)->format('d-m-Y') }}" />
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer"
                                                onclick="toggleCalendar('end')">
                                                <svg class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                        </div>
                            </div>

                                    <div class="mt-2 text-sm">
                                        <p class="text-gray-500">รวมวันที่จะยืมทั้งสิ้น</p>
                                        <p id="total-days" class="font-medium">0 วัน</p>
                            </div>

                                </div>
                            </div>
                            @if ($requests->status != 'pending')
                                @if ($requests->status === 'check_out')
                                    {{-- When item is already checked out and waiting to be returned --}}
                                <div class="bg-gray-50 rounded p-4">
                                    <label class="text-gray-500 text-sm block mb-1">ค่าปรับ</label>
                                    <input type="number" step="0.01" min="0" name="penalty_amount"
                                        class="w-full border rounded px-2 py-1"
                                            value="{{ $requests->transaction->penalty_amount ?? 0 }}" />
                                </div>

                                <div class="bg-gray-50 rounded p-4 md:col-span-2">
                                    <label class="text-gray-500 text-sm block mb-1">หมายเหตุ</label>
                                    <textarea name="notes" id="notes" class="w-full border rounded px-2 py-1" rows="2" maxlength="255" oninput="updateCharCount('notes', 'charCount')">{{ $requests->transaction->notes ?? '' }}</textarea>
                                    <div class="text-right text-xs text-gray-500 mt-1">
                                        <span id="charCount">{{ strlen($requests->transaction->notes ?? '') }}</span>/255
                                    </div>
                                </div>
                                @endif
                            @endif

                            {{-- Buttons --}}
                            @if (!in_array($requests->status, ['check_in', 'rejected', 'cancelled']))
                                <div class="absolute bottom-[-20px] right-4 flex gap-2">
                            @if ($requests->status == 'pending')
                                        {{-- Pending: Approve or Reject --}}
                                <button type="button"
                                            class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-md text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out shadow-sm"
                                    onclick="showRejectModal()">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                    ปฏิเสธ
                                </button>

                                <button type="submit"
                                    formaction="{{ route('admin.requests.approve', $requests->req_id) }}"
                                    formmethod="post"
                                            class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-md text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out shadow-sm"
                                    onclick="event.preventDefault(); 
                                        const f=this.closest('form'); 
                                        const m=document.createElement('input'); 
                                        m.type='hidden'; 
                                        m.name='_method'; 
                                        m.value='PATCH'; 
                                        f.appendChild(m); 
                                                f.action='{{ route('admin.requests.approve', $requests->req_id) }}'; 
                                        f.submit();">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            อนุมัติ
                                </button>
                                    @elseif ($requests->status !== 'pending' && $requests->status !== 'rejected')
                                        {{-- Approved or Checked Out: Check out or Check in --}}
                                <button type="submit"
                                    formaction="{{ route('admin.requests.update', $requests->req_id) }}"
                                    formmethod="post"
                                            class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-md text-white bg-emerald-500 hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition duration-150 ease-in-out shadow-sm"
                                    onclick="event.preventDefault(); 
                                        const f=this.closest('form'); 
                                        const m=document.createElement('input'); 
                                        m.type='hidden'; 
                                        m.name='_method'; 
                                        m.value='PATCH'; 
                                        f.appendChild(m); 
                                        f.action='{{ route('admin.requests.update', $requests->req_id) }}'; 
                                        f.submit();">
                                            @if($requests->status === 'approved')
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                                </svg>
                                                รับแล้ว
                                            @else
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                                                </svg>
                                                คืนแล้ว
                                            @endif
                                </button>
                            @endif
                        </div>
                            @endif


                    </form>

                    @if ($requests->status == 'pending')
                        <form id="reject-form" action="{{ route('admin.requests.reject', $requests->req_id) }}"
                            method="POST" class="hidden">
                            @csrf
                            <input type="hidden" name="reason" id="reject-reason" />
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 bg-white p-4 sm:p-6 rounded shadow">
        <h3 class="text-lg font-semibold mb-4">คำขอล่าสุด</h3>
        <div id="admin-table" data-requests='@json($tableRequests)'></div>
    </div>
</x-admin-layout>

{{-- Calendar Modal --}}
<div id="calendarModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-sm p-4">
        <div id="calendar"></div>
        <div class="flex justify-between mt-2 pt-2 border-t">
            <button onclick="setToday()" class="text-sm text-blue-600 hover:underline">วันนี้</button>
            <button onclick="clearDate()" class="text-sm text-red-600 hover:underline">ล้าง</button>
            <button onclick="closeCalendar()" class="text-sm text-gray-600 hover:underline">ปิด</button>
        </div>
    </div>
</div>

<script>
    window.showRejectModal = function() {
        Swal.fire({
            title: 'ปฏิเสธคำขอ',
            html: `
                <div class="text-left space-y-2">
                  <label class="flex items-center gap-2"><input type="radio" name="reason" value="ไม่ตรงตามเงื่อนไขการยืม"> ไม่ตรงตามเงื่อนไขการยืม</label>
                  <label class="flex items-center gap-2"><input type="radio" name="reason" value="อุปกรณ์ไม่พร้อมใช้งาน"> อุปกรณ์ไม่พร้อมใช้งาน</label>
                  <label class="flex items-center gap-2"><input type="radio" name="reason" value="อื่นๆ"> อื่นๆ</label>
                  <input id="reason-text" type="text" placeholder="ระบุเหตุผลเพิ่มเติม (ถ้าเลือก อื่นๆ)" class="w-full border rounded px-2 py-1" />
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

    let currentInput = null;
    let calendar = null;

    class Calendar {
        constructor(container, borrowedDates = []) {
            this.container = container;
            this.borrowedDates = borrowedDates.map(b => ({
                start: new Date(b.start_at + 'T00:00:00'),
                end: new Date(b.end_at + 'T00:00:00')
            }));
            this.currentDate = new Date();
            this.currentDate.setDate(1);
            this.render();
        }
        render() {
            const y = this.currentDate.getFullYear();
            const m = this.currentDate.getMonth();
            const monthNames = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม',
                'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
            ];
            const firstDay = new Date(y, m, 1);
            const lastDay = new Date(y, m + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startWeek = firstDay.getDay();
            let html =
                `<div class="bg-white border-gray-200 rounded-lg"><div class="flex justify-between items-center p-2 bg-gray-50 border-b"><button onclick="calendar.prev()" class="px-2 py-1 rounded-full hover:bg-gray-200 transition">‹</button><div class="text-sm font-medium">${monthNames[m]} ${y}</div><button onclick="calendar.next()" class="px-2 py-1 rounded-full hover:bg-gray-200 transition">›</button></div><div class="grid grid-cols-7 bg-gray-50 border-b text-xs font-semibold text-gray-500">${['อา','จ','อ','พ','พฤ','ศ','ส'].map(d => `<div class="p-2 text-center">${d}</div>`).join('')}</div><div class="grid grid-cols-7 text-center">`;
            for (let i = 0; i < startWeek; i++) html += `<div></div>`;
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            // Get current selected dates
            const startAtInput = document.getElementById('start_at');
            const startAtValue = startAtInput ? startAtInput.value : '';
            const endAtValue = document.getElementById('end_at')?.value || '';


            for (let d = 1; d <= daysInMonth; d++) {
                const date = new Date(y, m, d);
                const dateStr = `${y}-${String(m + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                const formattedDate = `${String(d).padStart(2, '0')}-${String(m + 1).padStart(2, '0')}-${y}`;
                const isPast = date < today;
                const isBorrowed = this.borrowedDates.some(b => date >= b.start && date <= b.end);
                const isWeekend = date.getDay() === 0 || date.getDay() === 6; // Sunday = 0, Saturday = 6
                const isStartDate = startAtValue === formattedDate;
                const isEndDate = endAtValue === formattedDate;

                let cls = "h-10 w-10 flex items-center justify-center text-sm mx-auto rounded-full ";
                let clickHandler = `onclick="selectDate('${dateStr}')"`;
                let title = '';

                if (isBorrowed) {
                    cls += "bg-red-200 text-red-600 cursor-not-allowed opacity-70 line-through";
                    clickHandler = '';
                    title = 'วันที่นี้ถูกจองแล้ว';
                } else if (isWeekend) {
                    cls += "bg-gray-300 text-gray-500 cursor-not-allowed opacity-60";
                    clickHandler = '';
                    title = 'ยืมและคืนได้เฉพาะวันจันทร์-ศุกร์';
                } else if (isPast) {
                    cls += "text-gray-400 cursor-not-allowed opacity-60";
                    clickHandler = '';
                    title = 'ไม่สามารถเลือกวันที่ในอดีตได้';
                } else {
                    cls += "cursor-pointer hover:bg-blue-100 transition";
                }

                // Highlight selected dates
                if (isStartDate) {
                    cls += " bg-green-500 text-white font-bold";
                    title = 'วันที่รับ';
                }
                if (isEndDate) {
                    cls += " bg-orange-500 text-white font-bold";
                    title = 'วันที่ส่ง';
                }
                if (isStartDate && isEndDate) {
                    cls =
                        "h-10 w-10 flex items-center justify-center text-sm mx-auto rounded-full bg-purple-500 text-white font-bold cursor-pointer hover:bg-purple-600 transition";
                    title = 'วันที่รับและส่ง (วันเดียวกัน)';
                }

                // Highlight today only if it's not a weekend and not selected
                if (date.getTime() === today.getTime() && !isWeekend && !isStartDate && !isEndDate) {
                    cls += " bg-blue-600 text-white font-bold"
                }

                html +=
                    `<div class="py-1"><div class="${cls}" data-date="${dateStr}" ${clickHandler} title="${title}">${d}</div></div>`;
            }
            html += "</div></div>";
            this.container.innerHTML = html;
        }
        prev() {
            this.currentDate.setMonth(this.currentDate.getMonth() - 1);
            this.render();
        }
        next() {
            this.currentDate.setMonth(this.currentDate.getMonth() + 1);
            this.render();
        }
    }

    function toggleCalendar(input) {
        currentInput = input;
        const modal = document.getElementById('calendarModal');
        modal.classList.remove('hidden');
        if (!calendar) {
            const borrowed = []; // Empty array for admin view since we don't need borrowed dates
            calendar = new Calendar(document.getElementById('calendar'), borrowed);
        }
    }

    function closeCalendar() {
        document.getElementById('calendarModal').classList.add('hidden');
    }

    function selectDate(dateStr) {
        if (currentInput) {
            const d = new Date(dateStr + 'T00:00:00');
            const isWeekend = d.getDay() === 0 || d.getDay() === 6; // Sunday = 0, Saturday = 6

            // Prevent weekend selection
            if (isWeekend) {
                Swal.fire({
                    icon: 'warning',
                    title: 'ไม่สามารถเลือกวันหยุดได้',
                    text: 'ยืมและคืนอุปกรณ์ได้เฉพาะวันจันทร์-ศุกร์',
                    timer: 3000,
                    showConfirmButton: false
                });
                return;
            }

            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();

            document.getElementById(currentInput + '_at').value = `${day}-${month}-${year}`;
            calculateDays();
            // Re-render calendar to show updated date highlighting
            if (calendar) {
                calendar.render();
            }
            closeCalendar();
        }
    }

    function clearDate() {
        if (currentInput) {
            document.getElementById(currentInput + '_at').value = '';
            calculateDays();
            // Re-render calendar to show updated date highlighting
            if (calendar) {
                calendar.render();
            }
        }
    }

    function setToday() {
        if (currentInput) {
            const t = new Date();
            selectDate(
                `${t.getFullYear()}-${String(t.getMonth() + 1).padStart(2, '0')}-${String(t.getDate()).padStart(2, '0')}`
            );
        }
    }

    function calculateDays() {
        // Get start date from the hidden input or the displayed span
        const startInput = document.getElementById('start_at');
        const startDateSpan = startInput ? startInput.value : '';
        const endInput = document.getElementById('end_at').value;
        const output = document.getElementById('total-days');

        if (startDateSpan && endInput) {
            // Parse d-m-Y format
            const s_parts = startDateSpan.split('-');
            const e_parts = endInput.split('-');
            const startDate = new Date(s_parts[2], s_parts[1] - 1, s_parts[0]);
            const endDate = new Date(e_parts[2], e_parts[1] - 1, e_parts[0]);
            const diffTime = endDate - startDate;
            const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;

            if (diffDays >= 0) {
                output.textContent = diffDays + ' วัน';
                output.classList.remove('text-red-700');
            } else {
                output.textContent = '0 วัน';
                output.classList.add('text-red-700');
                
                // Show alert for invalid date range
                Swal.fire({
                    icon: 'warning',
                    title: 'ช่วงวันที่ไม่ถูกต้อง!',
                    text: 'วันที่สิ้นสุดต้องอยู่หลังวันที่เริ่มต้น',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        } else {
            output.textContent = '0 วัน';
            output.classList.remove('text-red-700');
        }
    }

    // Add validation for past dates
    function validateDateInput(inputId, label) {
        const input = document.getElementById(inputId);
        const value = input.value;
        
        if (value) {
            const selectedDate = new Date(value);
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Reset time to start of day
            
            if (selectedDate < today) {
                Swal.fire({
                    icon: 'warning',
                    title: 'วันที่ไม่ถูกต้อง!',
                    text: `${label}ไม่สามารถเป็นวันที่ในอดีตได้`,
                    timer: 3000,
                    showConfirmButton: false
                });
                input.value = '';
                return false;
            }
        }
        return true;
    }

    document.getElementById('start_at').addEventListener('change', function() {
        validateDateInput('start_at', 'วันที่เริ่ม');
        calculateDays();
    });
    document.getElementById('end_at').addEventListener('change', function() {
        validateDateInput('end_at', 'วันที่สิ้นสุด');
        calculateDays();
    });
    calculateDays();

    // Add validation for check-out date
    function validateCheckoutDate() {
        const startDate = document.getElementById('start_at').value;
        const endDate = document.getElementById('end_at').value;
        const checkoutDate = document.getElementById('checked_out_at').value;
        const hint = document.getElementById('checkout-date-hint');
        
        if (!startDate || !endDate) {
            return;
        }
        
        if (checkoutDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const checkout = new Date(checkoutDate);
            
            // Set min and max attributes for the input
            document.getElementById('checked_out_at').min = startDate + 'T00:00';
            document.getElementById('checked_out_at').max = endDate + 'T23:59';
            
            if (checkout < start || checkout > end) {
                hint.textContent = 'วันที่มาเเอาของต้องอยู่ในช่วงวันที่เริ่มถึงวันที่สิ้นสุดที่อนุญาต';
                hint.classList.remove('text-gray-500');
                hint.classList.add('text-red-500');
                document.getElementById('checked_out_at').classList.add('border-red-500');
                
                // Show alert for invalid date selection
                Swal.fire({
                    icon: 'warning',
                    title: 'วันที่ไม่ถูกต้อง!',
                    text: `วันที่มาเเอาของต้องอยู่ในช่วง ${startDate} ถึง ${endDate}`,
                    timer: 3000,
                    showConfirmButton: false
                });
                
                // Clear the invalid date
                document.getElementById('checked_out_at').value = '';
            } else {
                hint.textContent = 'วันที่มาเเอาของอยู่ในช่วงที่อนุญาต';
                hint.classList.remove('text-red-500');
                hint.classList.add('text-green-500');
                document.getElementById('checked_out_at').classList.remove('border-red-500');
            }
        } else {
            hint.textContent = 'ต้องอยู่ในช่วงวันที่เริ่มถึงวันที่สิ้นสุดที่อนุญาต';
            hint.classList.remove('text-red-500', 'text-green-500');
            hint.classList.add('text-gray-500');
            document.getElementById('checked_out_at').classList.remove('border-red-500');
        }
    }

    // Set initial constraints and add event listeners
    function setCheckoutConstraints() {
        const startDate = document.getElementById('start_at').value;
        const endDate = document.getElementById('end_at').value;
        
        if (startDate && endDate) {
            document.getElementById('checked_out_at').min = startDate + 'T00:00';
            document.getElementById('checked_out_at').max = endDate + 'T23:59';
        }
    }

    document.getElementById('start_at').addEventListener('change', setCheckoutConstraints);
    document.getElementById('end_at').addEventListener('change', setCheckoutConstraints);
    document.getElementById('checked_out_at').addEventListener('change', function() {
        validateCheckoutDate();
        setCheckinConstraints();
    });
    
    // Add validation for check-in date
    function validateCheckinDate() {
        const checkoutDate = document.getElementById('checked_out_at').value;
        const checkinDate = document.getElementById('checked_in_at').value;
        const hint = document.getElementById('checkin-date-hint');
        
        if (!checkoutDate || !checkinDate) {
            return;
        }
        
        const checkout = new Date(checkoutDate);
        const checkin = new Date(checkinDate);
        
        if (checkin <= checkout) {
            hint.textContent = 'วันที่มาส่งต้องอยู่หลังวันที่มาเเอาของ';
            hint.classList.remove('text-gray-500');
            hint.classList.add('text-red-500');
            document.getElementById('checked_in_at').classList.add('border-red-500');
            
            // Show alert for invalid date selection
            Swal.fire({
                icon: 'warning',
                title: 'วันที่ไม่ถูกต้อง!',
                text: 'วันที่มาส่งต้องอยู่หลังวันที่มาเเอาของ',
                timer: 3000,
                showConfirmButton: false
            });
            
            // Clear the invalid date
            document.getElementById('checked_in_at').value = '';
        } else {
            hint.textContent = 'วันที่มาส่งถูกต้อง';
            hint.classList.remove('text-red-500');
            hint.classList.add('text-green-500');
            document.getElementById('checked_in_at').classList.remove('border-red-500');
        }
    }

    // Set minimum date for check-in based on check-out date
    function setCheckinConstraints() {
        const checkoutDate = document.getElementById('checked_out_at').value;
        const checkinInput = document.getElementById('checked_in_at');
        
        if (checkoutDate && checkinInput) {
            // Set minimum date to the day after checkout date
            const checkout = new Date(checkoutDate);
            const nextDay = new Date(checkout);
            nextDay.setDate(checkout.getDate() + 1);
            
            // Format for datetime-local input (YYYY-MM-DDTHH:MM)
            const minDate = nextDay.toISOString().slice(0, 16);
            checkinInput.min = minDate;
            
            // Clear current value if it's before the minimum date
            if (checkinInput.value && new Date(checkinInput.value) <= checkout) {
                checkinInput.value = '';
                validateCheckinDate();
            }
        }
    }

    // Add event listener for check-in date
    const checkinInput = document.getElementById('checked_in_at');
    if (checkinInput) {
        checkinInput.addEventListener('change', validateCheckinDate);
    }
    // Initialize constraints
    setCheckoutConstraints();
    validateCheckoutDate();
    setCheckinConstraints();

    // Add form submission validation
    function validateFormSubmission() {
        const startDate = document.getElementById('start_at').value;
        const endDate = document.getElementById('end_at').value;
        const checkoutDate = document.getElementById('checked_out_at').value;
        const checkinDate = document.getElementById('checked_in_at')?.value;
        
        if (!startDate || !endDate) {
            Swal.fire({
                icon: 'error',
                title: 'ข้อมูลไม่ครบถ้วน!',
                text: 'กรุณาระบุวันที่เริ่มและวันที่สิ้นสุดที่อนุญาต',
                confirmButtonText: 'ตกลง'
            });
            return false;
        }
        
        if (checkoutDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const checkout = new Date(checkoutDate);
            
            if (checkout < start || checkout > end) {
                Swal.fire({
                    icon: 'error',
                    title: 'วันที่ไม่ถูกต้อง!',
                    text: `วันที่มาเเอาของต้องอยู่ในช่วง ${startDate} ถึง ${endDate}`,
                    confirmButtonText: 'ตกลง'
                });
                return false;
            }
        }
        
        if (checkinDate && checkoutDate) {
            const checkout = new Date(checkoutDate);
            const checkin = new Date(checkinDate);
            
            // Check if check-in is at least one day after check-out
            const nextDay = new Date(checkout);
            nextDay.setDate(checkout.getDate() + 1);
            nextDay.setHours(0, 0, 0, 0);
            
            if (checkin < nextDay) {
                Swal.fire({
                    icon: 'error',
                    title: 'วันที่ไม่ถูกต้อง!',
                    text: 'วันที่มาส่งต้องเป็นวันถัดไปจากวันที่มาเเอาของ',
                    confirmButtonText: 'ตกลง'
                });
                return false;
            }
        }
        
        return true;
    }

    // Show success/error alerts based on URL parameters
    window.showSubmissionAlert = function() {
        const urlParams = new URLSearchParams(window.location.search);
        const success = urlParams.get('success');
        const error = urlParams.get('error');

        if (success) {
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: decodeURIComponent(success),
                timer: 3000,
                showConfirmButton: false
            });
        } else if (error) {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                text: decodeURIComponent(error),
                confirmButtonText: 'ตกลง'
            });
        }
    };

    // Add event listener to form submission
    document.addEventListener('DOMContentLoaded', function() {
        // Show success/error alerts on page load
        showSubmissionAlert();

        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!validateFormSubmission()) {
                    e.preventDefault();
                    return false;
                }
            });
        });

        // Add event listener for calendar modal
        const calendarModal = document.getElementById('calendarModal');
        if (calendarModal) {
            calendarModal.addEventListener('click', function(e) {
                if (e.target === calendarModal) {
                    closeCalendar();
                }
            });
        }
    });

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
