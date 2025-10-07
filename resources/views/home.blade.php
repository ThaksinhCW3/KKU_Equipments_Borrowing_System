<x-app-layout>
    <div class=" ">
        <div id="header-search"></div>
        <x-filter />
        <hr>
    </div>
    <div class="max-w-screen-2xl mx-auto py-6 px-3 sm:px-6 lg:px-8">
        @if($equipments->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2 sm:gap-6 lg:gap-5 my-5">
                @foreach ($equipments as $equipment)
                    <a href="{{ route('equipments.show', $equipment->code) }}" 
                       class="block equipment-card" 
                       data-equipment-code="{{ $equipment->code }}"
                       @if(auth()->user() && (!auth()->user()->verificationRequest || auth()->user()->verificationRequest->status !== 'approved'))
                           onclick="showVerificationWarning(event, '{{ $equipment->code }}')"
                       @endif>
                        <div
                            class="bg-white rounded-lg sm:rounded-2xl shadow-md hover:shadow-lg transition overflow-hidden group border border-gray-150">
                            <div class="relative">
                                @php
                                    $photos = json_decode($equipment->photo_path ?? '[]', true);
                                    $firstPhoto = is_array($photos) && count($photos) > 0 ? $photos[0] : $equipment->photo_path;
                                @endphp
                                <img src="{{ $firstPhoto }}" alt="{{ $equipment->name }}"
                                    class="w-full h-32 sm:h-48 lg:h-60 object-cover group-hover:scale-105 transition-transform" />
                                @if ($equipment->status === 'maintenance')
                                    <span
                                        class="absolute top-2 right-2 bg-red-600 text-white text-xs px-1.5 py-0.5 rounded">
                                        {{ ucfirst($equipment->status) }}
                                    </span>
                                @elseif ($equipment->status === 'retired')
                                    <span
                                        class="absolute top-2 right-2 bg-gray-600 text-white text-xs px-1.5 py-0.5 rounded">
                                        {{ ucfirst($equipment->status) }}
                                    </span>
                                @elseif ($equipment->available_quantity > 0)
                                    <span
                                        class="absolute top-2 right-2 bg-green-600 text-white text-xs px-1.5 py-0.5 rounded">
                                        พร้อมให้ยืม
                                    </span>
                                @else
                                    <span
                                        class="absolute top-2 right-2 bg-red-600 text-white text-xs px-1.5 py-0.5 rounded">
                                        ไม่พร้อมให้ยืม
                                    </span>
                                @endif
                            </div>
                            <div class="p-2 sm:p-4 p-5 pb-0">
                                <h3 class="text-sm sm:text-base lg:text-lg font-bold text-gray-900 mb-1 truncate">
                                    {{ $equipment->name }}</h3>
                                <p class="text-xs sm:text-sm text-gray-500 mb-1 sm:mb-2">{{ $equipment->category->name }}
                                </p>
                                <p class="text-xs text-gray-400 mb-2 line-clamp-2">
                                    {{ $equipment->description }}
                                </p>

                                <div class="flex justify-between items-center">
                                    <div class="text-xs text-gray-500">
                                        <span class="inline-block px-2 py-1 rounded-full mr-1 {{ $equipment->available_quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            พร้อมใช้งาน: {{ $equipment->available_quantity }}
                                        </span>
                                        @if($equipment->total_quantity > 1)
                                            <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                                ทั้งหมด: {{ $equipment->total_quantity }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <!-- No Results Message -->
            <div class="text-center py-12">
                <div class="mb-6">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.009-5.824-2.5M15 8.291A7.962 7.962 0 0112 6c-2.34 0-4.29 1.009-5.824 2.5"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">ไม่พบอุปกรณ์</h3>
                <p class="text-gray-500 mb-4">
                    @php
                        $activeFilters = [];
                        if(request('category')) {
                            $category = \App\Models\Category::where('cate_id', request('category'))->first();
                            $activeFilters[] = 'หมวดหมู่: ' . ($category ? $category->name : request('category'));
                        }
                        if(request('availability')) {
                            $availabilityLabels = [
                                'available' => 'ที่มีให้ยืม',
                                'unavailable' => 'ที่ไม่มีให้ยืม',
                                'maintenance' => 'ที่อยู่ระหว่างซ่อม'
                            ];
                            $activeFilters[] = 'ความพร้อมใช้งาน: ' . ($availabilityLabels[request('availability')] ?? request('availability'));
                        }
                        if(request('name')) {
                            $activeFilters[] = 'ชื่อ-นามสกุล: "' . request('name') . '"';
                        }
                    @endphp
                    
                    @if(count($activeFilters) > 0)
                        ไม่พบอุปกรณ์ที่ตรงกับเงื่อนไข:
                        <br>
                        <span class="font-medium text-gray-700">{{ implode(', ', $activeFilters) }}</span>
                    @else
                        ไม่มีอุปกรณ์ในระบบในขณะนี้
                    @endif
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        ล้างตัวกรองทั้งหมด
                    </a>
                    <button onclick="document.querySelector('[data-filter-panel]')?.click()" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M7 8h10M10 12h4"></path>
                        </svg>
                        เปลี่ยนตัวกรอง
                    </button>
                </div>
            </div>
        @endif

        <!-- Pagination -->
        @if ($equipments->hasPages())
            <div id="pagination" data-current-page="{{ $equipments->currentPage() }}"
                data-total-pages="{{ $equipments->lastPage() }}" data-per-page="{{ $equipments->perPage() }}"
                data-total="{{ $equipments->total() }}">
            </div>
        @endif
    </div>
    <div class="">
        <x-pagination />
    </div>
</x-app-layout>

<script>
function showVerificationWarning(event, equipmentCode) {
    event.preventDefault();
    
    Swal.fire({
        title: 'ยืนยันตัวตนก่อนยืมอุปกรณ์',
        text: 'คุณต้องยืนยันตัวตนก่อนที่จะสามารถยืมอุปกรณ์ได้',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ไปยืนยันตัวตน',
        cancelButtonText: 'ดูรายละเอียด',
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect to verification page
            window.location.href = "{{ route('verification.index') }}";
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            // Continue to equipment page
            window.location.href = "{{ url('equipments') }}/" + equipmentCode;
        }
    });
}
</script>
