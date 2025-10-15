<x-app-layout>
    @section('title', 'รายละเอียดอุปกรณ์')
    {{-- Session messages for success and error ----}}
    @if (session('success'))
        <script>
            Swal.fire({ icon: 'success', title: 'สำเร็จ!', text: '{{ session('success') }}', timer: 2500, showConfirmButton: false });
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด!', text: '{{ session('error') }}', timer: 2500, showConfirmButton: false });
        </script>
    @endif

    <div class="max-w-screen-2xl mx-auto py-4 px-3 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-4 items-start">

            {{-- Responsive Image Gallery --}}
            <div class="w-full lg:w-1/2">
                @php
                    $photos = json_decode($equipment->photo_path ?? '[]', true);
                    if (empty($photos) && !is_array($photos) && $equipment->photo_path) {
                        $photos = [$equipment->photo_path];
                    }
                @endphp

                @if(!empty($photos))
                    <div 
                        x-data="{ 
                            photos: {{ json_encode($photos) }},
                            currentIndex: 0,
                            isDragging: false,
                            startX: 0,
                            currentTranslate: 0,
                            prevTranslate: 0,
                            setActive(index) {
                                this.currentIndex = index;
                                if (this.$refs[`thumb-desktop-${index}`]) {
                                    this.$nextTick(() => this.$refs[`thumb-desktop-${index}`].scrollIntoView({ behavior: 'smooth', block: 'nearest' }));
                                }
                            },
                            next() { this.setActive((this.currentIndex + 1) % this.photos.length); },
                            prev() { this.setActive((this.currentIndex - 1 + this.photos.length) % this.photos.length); },
                            scrollUp() { this.$refs.thumbnails.scrollBy({ top: -120, behavior: 'smooth' }); },
                            scrollDown() { this.$refs.thumbnails.scrollBy({ top: 120, behavior: 'smooth' }); },
                            startDrag(event) {
                                this.isDragging = true;
                                this.startX = event.type.includes('mouse') ? event.pageX : event.touches[0].clientX;
                                this.prevTranslate = this.currentIndex * -this.$refs.slider.offsetWidth;
                                this.$refs.slider.style.transition = 'none';
                            },
                            drag(event) {
                                if (!this.isDragging) return;
                                const currentX = event.type.includes('mouse') ? event.pageX : event.touches[0].clientX;
                                const move = currentX - this.startX;
                                this.currentTranslate = this.prevTranslate + move;
                                this.$refs.slider.style.transform = `translateX(${this.currentTranslate}px)`;
                            },
                            endDrag() {
                                if (!this.isDragging) return;
                                this.isDragging = false;
                                this.$refs.slider.style.transition = 'transform 0.3s ease-in-out';
                                const movedBy = this.currentTranslate - this.prevTranslate;
                                if (movedBy < -50 && this.currentIndex < this.photos.length - 1) {
                                    this.currentIndex++;
                                }
                                if (movedBy > 50 && this.currentIndex > 0) {
                                    this.currentIndex--;
                                }
                                this.$refs.slider.style.transform = `translateX(-${this.currentIndex * 100}%)`;
                            }
                        }"
                    >
                        {{-- DESKTOP GALLERY --}}
                        <div class="hidden md:flex gap-3">
                            <div class="relative flex-shrink-0 w-20">
                                <button x-show="photos.length > 4" @click="scrollUp()" class="absolute top-0 left-1/2 -translate-x-1/2 z-10 w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-lg hover:scale-110 transition-transform" aria-label="Scroll up"><svg class="w-4 h-4 text-gray-800" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" /></svg></button>
                                <div x-ref="thumbnails" class="absolute inset-0 overflow-auto scrollbar-hide pt-10 pb-10">
                                    <div class="flex flex-col items-center gap-2">
                                        <template x-for="(photo, index) in photos" :key="index">
                                            <div class="flex-shrink-0">
                                                <div class="w-16 h-16 relative overflow-hidden rounded-lg border-2 cursor-pointer transition-all" :class="currentIndex === index ? 'border-blue-500 shadow-md scale-110' : 'border-transparent hover:border-gray-300'" @click="setActive(index)">
                                                    <img :src="photo" :x-ref="`thumb-desktop-${index}`" alt="Thumbnail" class="absolute inset-0 w-full h-full object-cover" />
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <button x-show="photos.length > 4" @click="scrollDown()" class="absolute bottom-0 left-1/2 -translate-x-1/2 z-10 w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-lg hover:scale-110 transition-transform" aria-label="Scroll down"><svg class="w-4 h-4 text-gray-800" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg></button>
                            </div>
                            <div class="w-full flex-grow relative overflow-hidden bg-gray-100 rounded-lg shadow-inner">
                                <div class="flex transition-transform duration-500 ease-in-out" :style="`transform: translateX(-${currentIndex * 100}%)`">
                                    <template x-for="(photo, index) in photos" :key="index">
                                        <div class="w-full flex-shrink-0">
                                            <div class="w-full h-96 relative">
                                                <img :src="photo" alt="Equipment Image" class="absolute inset-0 w-full h-full object-contain bg-white" />
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <template x-if="photos.length > 1">
                                    <div>
                                        <button @click="prev()" class="absolute top-1/2 left-3 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center bg-white/80 rounded-full shadow-lg hover:bg-white hover:scale-110 transition" aria-label="Previous"><svg class="w-6 h-6 text-gray-800" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg></button>
                                        <button @click="next()" class="absolute top-1/2 right-3 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center bg-white/80 rounded-full shadow-lg hover:bg-white hover:scale-110 transition" aria-label="Next"><svg class="w-6 h-6 text-gray-800" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg></button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="md:hidden">
                            <div class="relative overflow-hidden bg-gray-100 rounded-lg shadow-lg cursor-grab max-h-[250px]"
                                @mousedown.prevent="startDrag" @touchstart.prevent="startDrag" @mousemove.prevent="drag"
                                @touchmove.prevent="drag" @mouseup="endDrag" @touchend="endDrag" @mouseleave="endDrag">
                                <div x-ref="slider" class="flex"
                                    :style="{ transform: `translateX(-${currentIndex * 100}%)` }">
                                    <template x-for="(photo, index) in photos" :key="index">
                                        <div class="w-full flex-shrink-0">
                                            <div class="w-full h-64 relative">
                                                <img :src="photo" alt="Equipment Image"
                                                    class="absolute inset-0 w-full h-full object-contain bg-white pointer-events-none">
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <template x-if="photos.length > 1">
                                <div class="mt-1 overflow-x-auto scrollbar-hide">
                                    <div class="flex space-x-1 pb-1">
                                        <template x-for="(photo, index) in photos.slice(0,6)" :key="index">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 relative overflow-hidden rounded-md border-2 cursor-pointer" :class="currentIndex === index ? 'border-blue-500' : 'border-gray-200'" @click="currentIndex=index">
                                                    <img :src="photo" alt="Thumbnail" class="absolute inset-0 w-full h-full object-cover" />
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                @else
                    <div class="w-full aspect-w-4 aspect-h-3 bg-gray-100 rounded-lg flex items-center justify-center"><div class="text-center text-gray-500"><svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg><p>ไม่มีรูปภาพ</p></div></div>
                @endif
            </div>

            <div class="w-full lg:w-1/2 flex flex-col justify-start gap-3 p-3 sm:p-4 border border-gray-200 rounded-lg shadow-sm">
                <h1 class="text-xl md:text-2xl font-bold text-gray-900 break-words">{{ $equipment->name }}</h1>
                <p class="text-gray-500 text-sm break-words">หมายเลขครุภัณฑ์: {{ $equipment->code }}</p>
                <p class="text-gray-500 text-sm break-words">หมวดหมู่: {{ $equipment->category->name }}</p>
                <p class="text-gray-600 text-sm leading-relaxed break-words">{{ $equipment->description }}</p>
                
                @php
                    $accessories = json_decode($equipment->accessories ?? '[]', true);
                @endphp
                
                @if(!empty($accessories) && is_array($accessories))
                <div class="text-gray-600 text-sm leading-relaxed">
                        <p class="font-medium text-gray-700 mb-1">เซ็ตอุปกรณ์ที่ติดมากับเครื่อง:</p>
                        <div class="space-y-0.5">
                            @foreach($accessories as $accessory)
                                <div class="flex items-center">
                                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-2"></span>
                                    <span class="text-gray-700 text-sm">{{ $accessory }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="text-gray-600 text-sm leading-relaxed">เซ็ตอุปกรณ์ที่ติดมากับเครื่อง: ไม่มี</p>
                @endif
                <div class="mt-3 p-3 sm:p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    @php
                                    // Find all equipment with the same name
                                    $sameNameEquipment = \App\Models\Equipment::where('name', $equipment->name)->get();
                                    
                                    // Get all active borrow requests for equipment with same name
                                    $activeRequests = \App\Models\BorrowRequest::whereIn('equipments_id', $sameNameEquipment->pluck('id'))
                                        ->whereIn('status', ['pending', 'approved'])
                                        ->where('end_at', '>=', now())
                                        ->orderBy('end_at', 'asc')
                                        ->get();
                                    
                                    // Find the earliest return date
                                    $earliestReturn = $activeRequests->min('end_at');
                                @endphp
                                
                                @if($earliestReturn)
                                    <div class="m-2 p-2 bg-blue-50 border border-blue-200 rounded-lg">
                                        <div class="flex items-start space-x-2">
                                            <div class="text-blue-600 mt-0.5">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                            <div class="text-xs text-blue-800">
                                                <p class="text-xs leading-relaxed">
                                                    อุปกรณ์{{ $equipment->name }} จะว่างเร็วที่สุดในวันที่<br>
                                                    <span class="font-medium text-blue-900">{{ \Carbon\Carbon::parse($earliestReturn)->format('d-m-Y') }}</span>
                                                    @php
                                                        $daysUntilAvailable = \Carbon\Carbon::parse($earliestReturn)->diffInDays(now());
                                                    @endphp
                                                    @if($daysUntilAvailable == 0)
                                                        <span class="text-green-700">(วันนี้)</span>
                                                    @elseif($daysUntilAvailable == 1)
                                                        <span class="text-green-700">(พรุ่งนี้)</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                    <!-- Pickup Time Information -->
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
                    <div class="flex items-center justify-between mb-3">
                        <p class="font-semibold text-base">เลือกวันที่รับ-ส่ง :</p>
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                             จันทร์-ศุกร์เท่านั้น
                        </span>
                    </div>
                    <form action="{{ route('borrower.borrow_request', $equipment) }}" method="POST" id="borrowForm">
                        @csrf
                        <input type="hidden" name="equipments_id" value="{{ $equipment->id }}">
                        
                        <!-- Request Reason Dropdown -->
                        <div class="mb-3">
                            <label for="reason_type" class="block text-sm font-medium text-gray-700 mb-1">
                                เหตุผลในการยืม
                                <span class="text-red-500">*</span>
                            </label>
                            <select id="reason_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" @if ($hasBorrowed) disabled @endif required>
                                <option value="">-- เลือกเหตุผล --</option>
                                <option value="assignment">งานมอบหมาย/การบ้าน</option>
                                <option value="personal">ใช้ส่วนตัว</option>
                                <option value="others">อื่นๆ</option>
                            </select>
                        </div>

                        <!-- Request Details Input (Conditional) -->
                        <div id="request-details" class="hidden mb-3">
                            <label for="request_details" class="block text-sm font-medium text-gray-700 mb-1">
                                <span id="details-label">หมายเหตุเพิ่มเติม</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                id="request_details" 
                                name="request_reason_detail" 
                                maxlength="250"
                                rows="2"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                oninput="updateCharCount('request_details', 'request_details_count')" 
                                placeholder="ระบุเพิ่มเติมเช่น คณิตศาสตร์, ฟิสิกส์, โปรเจคถ่ายภาพ"
                                @if ($hasBorrowed) disabled @endif
                            ></textarea>
                            <div class="text-right text-xs text-gray-500 mt-1">
                                <span id="request_details_count">0</span>/250
                            </div>

                            <input type="hidden" id="request_reason" name="request_reason" value="">

                        </div>


                        <!-- Date Selection -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                            <div>
                                <label for="start_at" class="block text-sm font-medium text-gray-700">วันที่รับ</label>
                                <input 
                                    type="text" 
                                    id="start_at" 
                                    name="start_at" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm cursor-pointer" 
                                    @if ($hasBorrowed) disabled @endif 
                                    readonly 
                                    onclick="toggleCalendar('start')" 
                                    placeholder="วันที่ / เดือน"
                                >
                            </div>
                            <div>
                                <label for="end_at" class="block text-sm font-medium text-gray-700">วันที่ส่ง</label>
                                <input 
                                    type="text" 
                                    id="end_at" 
                                    name="end_at" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm cursor-pointer" 
                                    @if ($hasBorrowed) disabled @endif 
                                    readonly 
                                    onclick="toggleCalendar('end')" 
                                    placeholder="วันที่ / เดือน"
                                >
                            </div>
                        </div>
                        <div id="message" class="text-sm text-red-500 mb-3 h-4"></div>
                    
                        @if ($hasBorrowed)
                            <a href="{{ route('borrower.equipments.reqdetail',$borrowRequest->req_id) }}" class="block w-full text-center bg-yellow-500 text-white font-bold py-2 rounded-lg hover:bg-yellow-600 transition">ไปยังคำขอของฉัน</a>
                        @elseif (!auth()->user() || !auth()->user()->verificationRequest || auth()->user()->verificationRequest->status !== 'approved')
                            <a href="{{ route('verification.index') }}" class="block w-full text-center bg-orange-500 text-white font-bold py-2 rounded-lg hover:bg-orange-600 transition">
                                 ยืนยันตัวตนก่อนยืมอุปกรณ์
                            </a>
                        @elseif ($equipment->status === 'maintenance')
                            <button type="button" class="w-full bg-red-500 text-white font-bold py-2 rounded-lg cursor-not-allowed" disabled>อุปกรณ์อยู่ระหว่างซ่อมบำรุง</button>
                        @elseif ($equipment->status !== 'available')
                            <div class="w-full"></div>
                            <button type="button" class="w-full bg-gray-400 text-white font-bold py-2 rounded-lg cursor-not-allowed" disabled>ไม่สามารถยืมได้ ({{ $equipment->status }})</button>
                            </div>
                        @else
                            <button type="submit" id="borrowButton" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 rounded-lg transition duration-200 disabled:opacity-50" disabled>ส่งคำขอการยืม</button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Calendar Modal --}}
    <div id="calendarModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"><div class="bg-white rounded-lg shadow-xl w-full max-w-sm p-4"><div id="calendar"></div><div class="flex justify-between mt-2 pt-2 border-t"><button onclick="setToday()" class="text-sm text-blue-600 hover:underline">วันนี้</button><button onclick="clearDate()" class="text-sm text-red-600 hover:underline">ล้าง</button><button onclick="closeCalendar()" class="text-sm text-gray-600 hover:underline">ปิด</button></div></div></div>
</x-app-layout>

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<script>
    let currentInput = null;
    let calendar = null;

    class Calendar {
        constructor(container, borrowedDates = []) {
            this.container = container;
            this.borrowedDates = borrowedDates.map(b => ({ start: new Date(b.start_at + 'T00:00:00'), end: new Date(b.end_at + 'T00:00:00') }));
            this.currentDate = new Date();
            this.currentDate.setDate(1);
            this.render();
        }
        render() {
            const y = this.currentDate.getFullYear();
            const m = this.currentDate.getMonth();
            const monthNames = ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
            const firstDay = new Date(y, m, 1);
            const lastDay = new Date(y, m + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startWeek = firstDay.getDay();
            let html = `<div class="bg-white border-gray-200 rounded-lg"><div class="flex justify-between items-center p-2 bg-gray-50 border-b"><button onclick="calendar.prev()" class="px-2 py-1 rounded-full hover:bg-gray-200 transition">‹</button><div class="text-sm font-medium">${monthNames[m]} ${y}</div><button onclick="calendar.next()" class="px-2 py-1 rounded-full hover:bg-gray-200 transition">›</button></div><div class="grid grid-cols-7 bg-gray-50 border-b text-xs font-semibold text-gray-500">${['อา','จ','อ','พ','พฤ','ศ','ส'].map(d => `<div class="p-2 text-center">${d}</div>`).join('')}</div><div class="grid grid-cols-7 text-center">`;
            for (let i = 0; i < startWeek; i++) html += `<div></div>`;
            const today = new Date(); today.setHours(0,0,0,0);
            for (let d = 1; d <= daysInMonth; d++) {
                const date = new Date(y, m, d);
                const dateStr = `${y}-${String(m + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                const isPast = date < today;
                const isBorrowed = this.borrowedDates.some(b => date >= b.start && date <= b.end);
                const isWeekend = date.getDay() === 0 || date.getDay() === 6; // Sunday = 0, Saturday = 6
                let cls = "h-10 w-10 flex items-center justify-center text-sm mx-auto rounded-full ";
                let clickHandler = `onclick="selectDate('${dateStr}')"`;
                let title = '';
                
                if (isBorrowed) {
                    cls += "bg-red-200 text-red-600 cursor-not-allowed opacity-70 line-through";
                    clickHandler = ''; title = 'วันที่นี้ถูกจองแล้ว';
                } else if (isWeekend) {
                    cls += "bg-gray-300 text-gray-500 cursor-not-allowed opacity-60";
                    clickHandler = ''; title = 'ยืมและคืนได้เฉพาะวันจันทร์-ศุกร์';
                } else if (isPast) {
                    cls += "text-gray-400 cursor-not-allowed opacity-60";
                    clickHandler = ''; title = 'ไม่สามารถเลือกวันที่ในอดีตได้';
                } else { 
                    cls += "cursor-pointer hover:bg-blue-100 transition"; 
                }
                
                // Highlight today only if it's not a weekend
                if(date.getTime() === today.getTime() && !isWeekend){ 
                    cls += " bg-blue-600 text-white font-bold" 
                }
                
                html += `<div class="py-1"><div class="${cls}" data-date="${dateStr}" ${clickHandler} title="${title}">${d}</div></div>`;
            }
            html += "</div></div>"; this.container.innerHTML = html;
        }
        prev() { this.currentDate.setMonth(this.currentDate.getMonth() - 1); this.render(); }
        next() { this.currentDate.setMonth(this.currentDate.getMonth() + 1); this.render(); }
    }

    function toggleCalendar(input) {
        currentInput = input;
        const modal = document.getElementById('calendarModal');
        modal.classList.remove('hidden');
        if (!calendar) {
            const borrowed = @json($bookings->map(fn($b) => ['start_at' => $b->start_at, 'end_at' => $b->end_at]));
            calendar = new Calendar(document.getElementById('calendar'), borrowed);
        }
    }

    function closeCalendar() { document.getElementById('calendarModal').classList.add('hidden'); }

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

            document.getElementById(currentInput + '_at').value = `${day}/${month}/${year}`;
            closeCalendar();
            validateDates();
        }
    }
    function clearDate(){ if(currentInput){ document.getElementById(currentInput+'_at').value = ''; validateDates(); } }
    function setToday(){ if(currentInput){ const t = new Date(); selectDate(`${t.getFullYear()}-${String(t.getMonth() + 1).padStart(2, '0')}-${String(t.getDate()).padStart(2, '0')}`); }}
    
    function validateDates() {
        const s_input = document.getElementById('start_at');
        const e_input = document.getElementById('end_at');
        const button = document.getElementById('borrowButton');
        const msg = document.getElementById('message');
        
        if (!button) return;

        if (!s_input.value || !e_input.value) {
            button.disabled = true;
            msg.textContent = '';
            return;
        }
        
        const s_parts = s_input.value.split('/');
        const e_parts = e_input.value.split('/');
        const startDate = new Date(`${s_parts[2]}-${s_parts[1]}-${s_parts[0]}`);
        const endDate = new Date(`${e_parts[2]}-${e_parts[1]}-${e_parts[0]}`);
        
        // Check for weekends
        const startIsWeekend = startDate.getDay() === 0 || startDate.getDay() === 6;
        const endIsWeekend = endDate.getDay() === 0 || endDate.getDay() === 6;
        
        if (startIsWeekend || endIsWeekend) {
            msg.textContent = 'ยืมและคืนอุปกรณ์ได้เฉพาะวันจันทร์-ศุกร์';
            button.disabled = true;
        } else if (endDate < startDate) {
            msg.textContent = 'วันส่งไม่สามารถอยู่ก่อนวันรับได้';
            button.disabled = true;
        } else {
            msg.textContent = '';
            button.disabled = false;
        }
    }


    // Handle reason type dropdown and show details input
    function toggleRequestDetails() {
        const reasonType = document.getElementById('reason_type');
        const requestDetails = document.getElementById('request-details');
        const detailsLabel = document.getElementById('details-label');
        const detailsInput = document.getElementById('request_details');
        const hiddenInput = document.getElementById('request_reason');
        
        if (reasonType && requestDetails) {
            if (reasonType.value && reasonType.value !== '') {
                requestDetails.classList.remove('hidden');
                detailsInput.required = true;
                
                // Update label and placeholder based on selection
                if (reasonType.value === 'assignment') {
                    detailsLabel.textContent = 'หมายเหตุเพิ่มเติม';
                    detailsInput.placeholder = 'ระบุเพิ่มเติมเช่น วิชา/รายวิชา ,รายละเอียดการใช้งาน';
                } else if (reasonType.value === 'personal') {
                    detailsLabel.textContent = 'หมายเหตุเพิ่มเติม';
                    detailsInput.placeholder = 'ระบุเพิ่มเติมเช่น, งานอดิเรก, การเรียนรู้';
                } else if (reasonType.value === 'others') {
                    detailsLabel.textContent = 'หมายเหตุเพิ่มเติม';
                    detailsInput.placeholder = 'ระบุเพิ่มเติมเช่น กิจกรรมพิเศษ, โครงการส่วนตัว';
                }
            } else {
                requestDetails.classList.add('hidden');
                detailsInput.required = false;
                detailsInput.value = '';
                hiddenInput.value = '';
            }
        }
    }

    // Set request_reason to just the type (assignment, personal, others)
    function updateRequestReason() {
        const reasonType = document.getElementById('reason_type');
        const hiddenInput = document.getElementById('request_reason');
        
        if (reasonType && hiddenInput) {
            const type = reasonType.value;
            hiddenInput.value = type;
        }
    }

    // Enhanced form validation
    function validateForm() {
        const reasonType = document.getElementById('reason_type');
        const detailsInput = document.getElementById('request_details');
        const startDate = document.getElementById('start_at');
        const endDate = document.getElementById('end_at');
        const button = document.getElementById('borrowButton');
        const msg = document.getElementById('message');
        
        if (!button) return;

        let isValid = true;
        let errorMessage = '';

        // Check reason type
        if (!reasonType.value) {
            isValid = false;
            errorMessage = 'กรุณาเลือกเหตุผลในการยืม';
        }
        // Check details input
        else if (!detailsInput.value.trim()) {
            isValid = false;
            errorMessage = 'กรุณาระบุหมายเหตุเพิ่มเติม';
        }
        // Check dates
        else if (!startDate.value || !endDate.value) {
            isValid = false;
            errorMessage = 'กรุณาเลือกวันที่รับและส่ง';
        }
        // Check date validity
        else if (startDate.value && endDate.value) {
            const s_parts = startDate.value.split('/');
            const e_parts = endDate.value.split('/');
            const startDateObj = new Date(`${s_parts[2]}-${s_parts[1]}-${s_parts[0]}`);
            const endDateObj = new Date(`${e_parts[2]}-${e_parts[1]}-${e_parts[0]}`);
            
            if (endDateObj < startDateObj) {
                isValid = false;
                errorMessage = 'วันส่งไม่สามารถอยู่ก่อนวันรับได้';
            }
        }

        button.disabled = !isValid;
        if (msg) {
            msg.textContent = errorMessage;
        }
        
        return isValid;
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Add event listener for reason type dropdown
        const reasonTypeSelect = document.getElementById('reason_type');
        if (reasonTypeSelect) {
            reasonTypeSelect.addEventListener('change', function() {
                toggleRequestDetails();
                validateForm();
            });
        }

        // Add event listener for details input
        const detailsInput = document.getElementById('request_details');
        if (detailsInput) {
            detailsInput.addEventListener('input', function() {
                updateRequestReason();
                validateForm();
            });
        }

        const borrowForm = document.getElementById('borrowForm');
        if (borrowForm) {
            borrowForm.addEventListener('submit', function(e) {
                @if (!Auth::check())
                    e.preventDefault();
                    Swal.fire({
                        title: 'คุณต้องเข้าสู่ระบบ', text: 'กด ตกลง เพื่อไปยังหน้าล็อกอิน', icon: 'warning',
                        showCancelButton: true, confirmButtonText: 'ตกลง', cancelButtonText: 'ยกเลิก'
                    }).then((result) => { if (result.isConfirmed) { window.location.href = "{{ route('login') }}"; } });
                @else
                    // Update request_reason before submission
                    updateRequestReason();
                    if (!validateForm()) {
                        e.preventDefault();
                        return false;
                    }
                @endif
            });
        }

        const s_input = document.getElementById('start_at');
        const e_input = document.getElementById('end_at');
        if (s_input && e_input) {
            validateDates(); 
        }
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
