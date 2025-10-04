<x-app-layout>
    <div class="max-w-screen-2xl mx-auto py-6 px-3 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">คำขอยืมอุปกรณ์</h1>

        @php
            $allRequests = $reQuests->sortByDesc('created_at');
        @endphp

        @if ($allRequests->isEmpty())
            <!-- Empty State -->
            <div class="text-center py-10 text-gray-500">
                <svg class="mx-auto mb-4 w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6M9 8l6 6" />
                </svg>
                <p class="font-medium">คุณยังไม่มีคำขอ</p>
            </div>
        @else
            <!-- Request History Section with Search and Filter -->
            @if($allRequests->count() > 0)
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">คำขอทั้งหมด</h2>
                    
                    <!-- Search and Filter Bar -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <!-- Search Bar -->
                            <div class="flex-1">
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        id="searchHistory" 
                                        placeholder="ค้นหาตามชื่ออุปกรณ์หรือรหัสคำขอ..."
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Filter Dropdown -->
                            <div class="sm:w-48">
                                <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">สถานะทั้งหมด</option>
                                    <option value="pending">รอดำเนินการ</option>
                                    <option value="approved">อนุมัติ</option>
                                    <option value="rejected">ปฏิเสธ</option>
                                    <option value="cancelled">ยกเลิกแล้ว</option>
                                </select>
                            </div>
                            
                            <!-- Sort Button -->
                            <div class="sm:w-48">
                                <button id="sortToggle" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2" data-sort="desc">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                    <span id="sortText">ใหม่ล่าสุด</span>
                                </button>
                            </div>
                             
                            <!-- Clear Filter Button -->
                            <button id="clearFilters" class="px-4 py-2 text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                ล้างตัวกรอง
                            </button>
                        </div>
                    </div>

                    <!-- Requests Grid -->
                    <div id="historyRequests" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                        @foreach ($allRequests as $req)
                            <div class="bg-white rounded-2xl shadow p-4 flex flex-col justify-between h-full request-card" 
                                 x-data="{ openModal: false }" 
                                 data-equipment-name="{{ strtolower($req->equipment->name) }}"
                                 data-request-id="{{ strtolower($req->req_id) }}"
                                 data-status="{{ $req->status }}"
                                 data-created-at="{{ $req->created_at->timestamp }}">
                                <!-- Card Content -->
                                <div>
                                    <!-- Header -->
                                    <div class="flex justify-between items-center border-b pb-3 mb-3">
                                        <div class="lg:flex">
                                            <h2 class="text-lg font-semibold text-gray-800">คำขอเลขที่:&nbsp;</h2>
                                            <h2 class="text-lg font-semibold text-gray-800">#{{ $req->req_id }}</h2>
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
                                    <div class="mb-4 grid grid-cols-1 sm:grid-cols-4 gap-4">
                                        @php
                                            $photos = json_decode($req->equipment->photo_path ?? '[]', true);
                                            $firstPhoto = is_array($photos) && count($photos) > 0 ? $photos[0] : $req->equipment->photo_path;
                                        @endphp
                                        <img src="{{ $firstPhoto }}" alt="equipment photo"
                                            class="w-full h-28 object-cover rounded-lg shadow col-span-1 sm:col-span-1">

                                        <div class="col-span-1 sm:col-span-3 space-y-3 relative">
                                            <h3 class="font-semibold text-gray-800 break-words">{{ $req->equipment->name }}</h3>
                                            <p class="text-sm text-gray-500 break-words">รหัส: {{ $req->equipment->code }}</p>
                                            <p class="text-sm text-gray-500 break-words">หมวดหมู่: {{ $req->equipment->category->name }}</p>

                                            <!-- Truncated Description with Tooltip -->
                                            <p class="text-gray-500 text-sm break-words relative" x-data="{ tooltip: false }"
                                               @mouseenter="tooltip = true" @mouseleave="tooltip = false">
                                                {{ \Illuminate\Support\Str::limit($req->equipment->description, 50, '...') }}

                                                <span x-show="tooltip" x-transition
                                                      class="absolute left-0 top-full mt-1 z-50 bg-gray-800 text-white text-xs p-2 rounded shadow-lg w-64 break-words">
                                                    {{ $req->equipment->description }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Duration -->
                                    <div class="mb-4">
                                        <h3 class="font-semibold text-gray-700">ระยะเวลายืม</h3>
                                        <p class="text-sm text-gray-600">เริ่มต้น: {{ $req->start_at->format('d-m-Y') }}</p>
                                        <p class="text-sm text-gray-600">สิ้นสุด: {{ $req->end_at->format('d-m-Y') }}</p>
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

                                    <!-- Reject Reason -->
                                    @if ($req->status === 'rejected')
                                        <h1 class="text-lg text-red-600 font-semibold">เหตุผลปฏิเสธ</h1>
                                        <p class="text-sm text-red-600">{{ $req->reject_reason }}</p>
                                    @endif
                                </div>

                                <!-- Card Footer -->
                                <div class="mt-6 flex justify-between items-center">
                                    <a href="{{ route('borrower.equipments.reqdetail', $req->req_id) }}"
                                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition text-center">
                                        ดูรายละเอียด
                                    </a>

                                    @if ($req->status === 'pending')
                                        <button @click="openModal = true"
                                            class="bg-red-600 text-black px-6 py-2 rounded-lg hover:bg-red-700 transition">
                                            ยกเลิก
                                        </button>
                                    @endif
                                </div>

                                <!-- Cancel Modal -->
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
                        @endforeach
                    </div>
                    
                    <!-- Loading Indicator -->
                    <div id="loadingIndicator" class="hidden text-center py-8">
                        <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-gray-500 bg-white">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            กำลังโหลด...
                        </div>
                    </div>
                    
                    <!-- No More Results -->
                    <div id="noMoreResults" class="hidden text-center py-8 text-gray-500">
                        <p class="font-medium">ไม่มีคำขอเพิ่มเติม</p>
                    </div>
                    
                    <!-- No Results Message -->
                    <div id="noResults" class="hidden text-center py-10 text-gray-500">
                        <svg class="mx-auto mb-4 w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6M9 8l6 6" />
                        </svg>
                        <p class="font-medium">ไม่พบคำขอที่ตรงกับการค้นหา</p>
                    </div>
                </div>
            @endif
        @endif
    </div>

    <!-- SweetAlert -->
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchHistory');
            const statusFilter = document.getElementById('statusFilter');
            const clearFiltersBtn = document.getElementById('clearFilters');
            const requestCards = document.querySelectorAll('.request-card');
            const noResultsDiv = document.getElementById('noResults');
            const loadingIndicator = document.getElementById('loadingIndicator');
            const noMoreResults = document.getElementById('noMoreResults');
            const historyRequestsContainer = document.getElementById('historyRequests');

            // Infinite scroll variables
            let currentPage = 1;
            let isLoading = false;
            let hasMoreData = true;
            let currentSort = 'desc'; // Default sort

            function filterRequests() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                const selectedStatus = statusFilter.value;
                let visibleCount = 0;

                requestCards.forEach(card => {
                    const equipmentName = card.getAttribute('data-equipment-name');
                    const requestId = card.getAttribute('data-request-id');
                    const status = card.getAttribute('data-status');

                    // Check search term
                    const matchesSearch = searchTerm === '' || 
                        equipmentName.includes(searchTerm) || 
                        requestId.includes(searchTerm);

                    // Check status filter
                    const matchesStatus = selectedStatus === '' || status === selectedStatus;

                    // Show/hide card
                    if (matchesSearch && matchesStatus) {
                        card.style.display = 'block';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Show/hide no results message
                if (visibleCount === 0) {
                    noResultsDiv.classList.remove('hidden');
                } else {
                    noResultsDiv.classList.add('hidden');
                }
            }

            // Toggle sort function
            function toggleSort() {
                const sortToggle = document.getElementById('sortToggle');
                const sortText = document.getElementById('sortText');
                const sortIcon = sortToggle.querySelector('svg path');
                
                if (currentSort === 'desc') {
                    currentSort = 'asc';
                    sortToggle.setAttribute('data-sort', 'asc');
                    sortText.textContent = 'เก่าสุด';
                    sortIcon.setAttribute('d', 'M5 15l7-7 7 7');
                } else {
                    currentSort = 'desc';
                    sortToggle.setAttribute('data-sort', 'desc');
                    sortText.textContent = 'ใหม่ล่าสุด';
                    sortIcon.setAttribute('d', 'M19 9l-7 7-7-7');
                }
                
                // Sort existing requests by creation date
                sortExistingRequests();
            }

            // Sort existing requests in the DOM
            function sortExistingRequests() {
                const requestCards = Array.from(historyRequestsContainer.querySelectorAll('.request-card'));
                
                // Get request data and sort
                const requestsWithData = requestCards.map(card => {
                    const createdAt = parseInt(card.dataset.createdAt) || 0;
                    return { card, createdAt };
                });
                
                // Sort by creation date
                requestsWithData.sort((a, b) => {
                    if (currentSort === 'desc') {
                        return b.createdAt - a.createdAt;
                    } else {
                        return a.createdAt - b.createdAt;
                    }
                });
                
                // Clear container and re-append sorted cards
                historyRequestsContainer.innerHTML = '';
                requestsWithData.forEach(({ card }) => {
                    historyRequestsContainer.appendChild(card);
                });
            }

            // Load more requests via AJAX
            function loadMoreRequests() {
                if (isLoading || !hasMoreData) return;

                isLoading = true;
                loadingIndicator.classList.remove('hidden');

                fetch(`/borrower/myrequest/paginated?page=${currentPage + 1}&sort=${currentSort}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.requests && data.requests.length > 0) {
                        // Append new requests to the container
                        data.requests.forEach(request => {
                            const requestCard = createRequestCard(request);
                            historyRequestsContainer.appendChild(requestCard);
                        });
                        
                        currentPage = data.page;
                        hasMoreData = data.hasMore;
                        
                        if (!hasMoreData) {
                            noMoreResults.classList.remove('hidden');
                        }
                    } else {
                        hasMoreData = false;
                        noMoreResults.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error loading more requests:', error);
                })
                .finally(() => {
                    isLoading = false;
                    loadingIndicator.classList.add('hidden');
                });
            }

            // Create request card HTML
            function createRequestCard(request) {
                const div = document.createElement('div');
                div.className = 'bg-white rounded-2xl shadow p-4 flex flex-col justify-between h-full request-card';
                div.setAttribute('x-data', '{ openModal: false }');
                div.setAttribute('data-equipment-name', request.equipment.name.toLowerCase());
                div.setAttribute('data-request-id', request.req_id.toLowerCase());
                div.setAttribute('data-status', request.status);
                div.setAttribute('data-created-at', new Date(request.created_at).getTime());

                // Get status display
                let statusDisplay = '';
                let statusClass = '';
                switch(request.status) {
                    case 'pending':
                        statusDisplay = 'รอดำเนินการ';
                        statusClass = 'bg-yellow-50 text-yellow-800 border-yellow-300';
                        break;
                    case 'approved':
                        statusDisplay = 'อนุมัติ';
                        statusClass = 'bg-green-50 text-green-800 border-green-300';
                        break;
                    case 'rejected':
                        statusDisplay = 'ปฏิเสธ';
                        statusClass = 'bg-red-50 text-red-800 border-red-300';
                        break;
                    case 'cancelled':
                        statusDisplay = 'ยกเลิกแล้ว';
                        statusClass = 'bg-gray-50 text-gray-800 border-gray-300';
                        break;
                }

                // Get equipment photo
                let photoSrc = '/storage/placeholder.png';
                if (request.equipment.photo_path) {
                    try {
                        const photos = JSON.parse(request.equipment.photo_path);
                        if (Array.isArray(photos) && photos.length > 0) {
                            photoSrc = photos[0];
                        } else {
                            photoSrc = request.equipment.photo_path;
                        }
                    } catch (e) {
                        photoSrc = request.equipment.photo_path;
                    }
                }

                // Get check out/in status
                let checkOutStatus = '';
                let checkInStatus = '';
                if (request.transaction) {
                    if (request.transaction.checked_out_at) {
                        const checkOutDate = new Date(request.transaction.checked_out_at);
                        checkOutStatus = `
                            <p class="text-sm text-green-600">
                                <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                รับอุปกรณ์: ${checkOutDate.toLocaleDateString('th-TH')} ${checkOutDate.toLocaleTimeString('th-TH', {hour: '2-digit', minute: '2-digit'})}
                            </p>`;
                    } else {
                        checkOutStatus = `
                            <p class="text-sm text-gray-500">
                                <span class="inline-block w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                                ยังไม่ได้รับอุปกรณ์
                            </p>`;
                    }

                    if (request.transaction.checked_in_at) {
                        const checkInDate = new Date(request.transaction.checked_in_at);
                        checkInStatus = `
                            <p class="text-sm text-blue-600">
                                <span class="inline-block w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                คืนอุปกรณ์: ${checkInDate.toLocaleDateString('th-TH')} ${checkInDate.toLocaleTimeString('th-TH', {hour: '2-digit', minute: '2-digit'})}
                            </p>`;
                    } else if (request.transaction.checked_out_at) {
                        checkInStatus = `
                            <p class="text-sm text-orange-600">
                                <span class="inline-block w-2 h-2 bg-orange-500 rounded-full mr-2"></span>
                                ยังไม่คืนอุปกรณ์
                            </p>`;
                    }
                }

                const startDate = new Date(request.start_at);
                const endDate = new Date(request.end_at);

                div.innerHTML = `
                    <div>
                        <div class="flex justify-between items-center border-b pb-3 mb-3">
                            <div class="lg:flex">
                                <h2 class="text-lg font-semibold text-gray-800">คำขอเลขที่:&nbsp;</h2>
                                <h2 class="text-lg font-semibold text-gray-800">#${request.req_id}</h2>
                            </div>
                            <span class="px-4 py-2 text-sm font-semibold rounded-lg border-2 ${statusClass}">
                                <span class="inline-block w-2 h-2 bg-${request.status === 'pending' ? 'yellow' : request.status === 'approved' ? 'green' : request.status === 'rejected' ? 'red' : 'gray'}-500 rounded-full mr-2"></span>${statusDisplay}
                            </span>
                        </div>

                        <div class="mb-4 grid grid-cols-1 sm:grid-cols-4 gap-4">
                            <img src="${photoSrc}" alt="equipment photo" class="w-full h-28 object-cover rounded-lg shadow col-span-1 sm:col-span-1">
                            <div class="col-span-1 sm:col-span-3 space-y-3 relative">
                                <h3 class="font-semibold text-gray-800 break-words">${request.equipment.name}</h3>
                                <p class="text-sm text-gray-500 break-words">รหัส: ${request.equipment.code}</p>
                                <p class="text-sm text-gray-500 break-words">หมวดหมู่: ${request.equipment.category.name}</p>
                                <p class="text-gray-500 text-sm break-words">${request.equipment.description ? request.equipment.description.substring(0, 50) + '...' : ''}</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h3 class="font-semibold text-gray-700">ระยะเวลายืม</h3>
                            <p class="text-sm text-gray-600">เริ่มต้น: ${startDate.toLocaleDateString('th-TH')}</p>
                            <p class="text-sm text-gray-600">สิ้นสุด: ${endDate.toLocaleDateString('th-TH')}</p>
                        </div>

                        ${request.transaction ? `
                        <div class="mb-4">
                            <h3 class="font-semibold text-gray-700">สถานะการยืม</h3>
                            ${checkOutStatus}
                            ${checkInStatus}
                        </div>
                        ` : ''}

                        ${request.status === 'rejected' ? `
                        <h1 class="text-lg text-red-600 font-semibold">เหตุผลปฏิเสธ</h1>
                        <p class="text-sm text-red-600">${request.reject_reason || ''}</p>
                        ` : ''}
                    </div>

                    <div class="mt-6 flex justify-between items-center">
                        <a href="/borrower/reqdetail/${request.req_id}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition text-center">
                            ดูรายละเอียด
                        </a>
                        ${request.status === 'pending' ? `
                        <button @click="openModal = true" class="bg-red-600 text-black px-6 py-2 rounded-lg hover:bg-red-700 transition">
                            ยกเลิก
                        </button>
                        ` : ''}
                    </div>

                    ${request.status === 'pending' ? `
                    <div x-show="openModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-cloak>
                        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                            <h3 class="text-lg font-semibold mb-4">เลือกเหตุผลการยกเลิก</h3>
                            <form action="/borrower/requests/${request.id}/cancel" method="POST" x-data="{ otherChecked: false }">
                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                                <input type="hidden" name="_method" value="PATCH">
                                <div class="space-y-2 mb-4">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="cancel_reason[]" value="เปลี่ยนใจ" class="text-red-600 rounded" @click="otherChecked = false">
                                        <span>เปลี่ยนใจ</span>
                                    </label>
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="cancel_reason[]" value="เลือกอุปกรณ์ผิด" class="text-red-600 rounded" @click="otherChecked = false">
                                        <span>เลือกอุปกรณ์ผิด</span>
                                    </label>
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="cancel_reason[]" value="อื่น ๆ" class="text-red-600 rounded" x-model="otherChecked">
                                        <span>อื่น ๆ</span>
                                    </label>
                                    <input type="text" name="cancel_reason[]" placeholder="ระบุเหตุผลอื่น..." class="mt-2 w-full border rounded px-3 py-2" x-show="otherChecked" x-transition>
                                </div>
                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="openModal = false" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">ปิด</button>
                                    <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">ยืนยันการยกเลิก</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    ` : ''}
                `;

                return div;
            }

            // Infinite scroll detection
            function handleScroll() {
                if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 1000) {
                    loadMoreRequests();
                }
            }

            // Event listeners
            if (searchInput) {
                searchInput.addEventListener('input', filterRequests);
            }

            if (statusFilter) {
                statusFilter.addEventListener('change', filterRequests);
            }

            if (clearFiltersBtn) {
                clearFiltersBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    statusFilter.value = '';
                    filterRequests();
                });
            }

            // Sort toggle event listener
            const sortToggle = document.getElementById('sortToggle');
            if (sortToggle) {
                sortToggle.addEventListener('click', toggleSort);
            }

            // Add scroll listener for infinite scroll
            window.addEventListener('scroll', handleScroll);

            // Initialize Alpine.js for dynamically added elements
            if (window.Alpine) {
                Alpine.start();
            }
        });
    </script>
</x-app-layout>
