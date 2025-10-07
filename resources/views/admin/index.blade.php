<x-admin-layout>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Analytics Section -->
        <div class="lg:col-span-12 ">
                <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.dashboard.export', ['type' => 'borrow_requests', 'year' => $selectedYear, 'month' => $selectedMonth]) }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm">
                         Borrow Requests
                    </a>
                    <a href="{{ route('admin.dashboard.export', ['type' => 'device_usage', 'year' => $selectedYear, 'month' => $selectedMonth]) }}" 
                       class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm">
                         Device Usage
                    </a>
                    <a href="{{ route('admin.dashboard.export', ['type' => 'user_activity', 'year' => $selectedYear, 'month' => $selectedMonth]) }}" 
                       class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-md text-sm">
                         User Activity
                    </a>
                </div>
        </div>
        <!-- KPI Cards -->
        <div class="lg:col-span-12 space-y-4">
            <!-- Equipment Cards Row -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.equipment.index') }}" class="bg-white rounded-lg border p-4 hover:shadow-md transition-shadow cursor-pointer">
                    <div class="text-2xl font-semibold mt-1 text-purple-600">{{ $equipmentStats['TotalEquipment'] }}</div>
                    <div class="text-xs text-gray-500">อุปกรณ์ทั้งหมด</div>
                </a>
                <a href="{{ route('admin.equipment.index') }}?status=available" class="bg-white rounded-lg border p-4 hover:shadow-md transition-shadow cursor-pointer">
                    <div class="text-2xl font-semibold mt-1 text-green-600">{{ $equipmentStats['AvailableEquipment'] }}</div>
                    <div class="text-xs text-gray-500">อุปกรณ์ที่พร้อมใช้งาน</div>
                </a>
                <a href="{{ route('admin.equipment.index') }}?status=unavailable" class="bg-white rounded-lg border p-4 hover:shadow-md transition-shadow cursor-pointer">
                    <div class="text-2xl font-semibold mt-1 text-blue-600">{{ $equipmentStats['BorrowedEquipment'] }}</div>
                    <div class="text-xs text-gray-500">อุปกรณ์ที่ถูกยืม</div>
                </a>
                <a href="{{ route('admin.equipment.index') }}?status=maintenance" class="bg-white rounded-lg border p-4 hover:shadow-md transition-shadow cursor-pointer">
                    <div class="text-2xl font-semibold mt-1 text-orange-600">{{ $equipmentStats['MaintenanceEquipment'] }}</div>
                    <div class="text-xs text-gray-500">อุปกรณ์ที่ซ่อมบำรุง</div>
                </a>
            </div>
            
            <!-- Request Cards Row -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.requests.index') }}" class="bg-white rounded-lg border p-4 hover:shadow-md transition-shadow cursor-pointer">
                    <div class="text-2xl font-semibold mt-1 text-blue-600">{{ $borrowStatus['TotalRequests'] }}</div>
                    <div class="text-xs text-gray-500">คำขอทั้งหมด</div>
                </a>
                <a href="{{ route('admin.requests.index') }}?status=check_in" class="bg-white rounded-lg border p-4 hover:shadow-md transition-shadow cursor-pointer">
                    <div class="text-2xl font-semibold mt-1 text-purple-600">{{ $borrowStatus['checkinReq'] }}</div>
                    <div class="text-xs text-gray-500">การยืมที่สำเร็จ</div>
                </a>
                <a href="{{ route('admin.requests.index') }}?status=pending" class="bg-white rounded-lg border p-4 hover:shadow-md transition-shadow cursor-pointer">
                    <div class="text-2xl font-semibold mt-1 text-yellow-600">{{ $borrowStatus['Pending'] }}</div>
                    <div class="text-xs text-gray-500">คำขอที่รอดำเนินการ</div>
                </a>
                <a href="{{ route('admin.requests.index') }}?status=rejected" class="bg-white rounded-lg border p-4 hover:shadow-md transition-shadow cursor-pointer">
                    <div class="text-2xl font-semibold mt-1 text-red-600">{{ $borrowStatus['Rejected'] }}</div>
                    <div class="text-xs text-gray-500">คำขอที่ถูกปฏิเสธ</div>
                </a>
            </div>
        </div>

        <!-- Year + Month Filter -->
        <div class="lg:col-span-12 bg-white rounded-lg border p-4">
            <div class="mb-2">
                <span class="text-sm text-gray-600">กรองข้อมูลสำหรับกราฟและสถิติด้านล่าง:</span>
            </div>
            <form method="GET" action="{{ route('admin.index') }}" class="flex flex-wrap gap-3 items-end">
                {{-- Year --}}
                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                    <select name="year" id="year" onchange="this.form.submit()" class="mt-1 block w-32 border-gray-300 rounded-md shadow-sm">
                        @foreach($availableYears as $yearOption)
                            <option value="{{ $yearOption }}" {{ $selectedYear == $yearOption ? 'selected' : '' }}>
                                {{ $yearOption }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Month --}}
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700">Month</label>
                    <select name="month" id="month" onchange="this.form.submit()" class="mt-1 block w-32 border-gray-300 rounded-md shadow-sm">
                        <option value="">All</option>
                        @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $index => $monthName)
                            @php $monthNumber = $index + 1; @endphp
                            <option value="{{ $monthNumber }}" {{ (isset($selectedMonth) && $selectedMonth == $monthNumber) ? 'selected' : '' }}>
                                {{ $monthName }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <!-- Chart -->
        <div id="dashboard-chart" class="lg:col-span-7 bg-white rounded-lg border p-4" data-chart='@json($chartData)'>
            <canvas></canvas>
        </div>

        <!-- Category Chart -->
        <div class="lg:col-span-5 bg-white rounded-lg border p-4">
            <div class="text-sm font-medium mb-4">สถานะอุปกรณ์ตามหมวดหมู่</div>
            <div style="height: 300px; width: 100%;">
                <canvas id="categoryBar"></canvas>
                @if($categoryCounts->isEmpty())
                    <div class="flex items-center justify-center h-full text-gray-500">
                        ไม่มีข้อมูลหมวดหมู่
                    </div>
                @endif
            </div>
        </div>


        <!-- Popular Equipment -->
        <div class="lg:col-span-4 bg-white rounded-lg border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">อุปกรณ์ยอดนิยม</h3>
            <div class="space-y-3">
                @forelse($popularEquipment as $index => $equipment)
                    <a href="{{ route('admin.equipment.index') }}?search={{ urlencode($equipment['equipment_name']) }}" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                        <div class="flex-1">
                            <div class="font-medium text-sm text-blue-600 hover:text-blue-800">{{ $equipment['equipment_name'] }}</div>
                            <div class="text-xs text-gray-500">{{ $equipment['equipment_code'] }} • {{ $equipment['category'] }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-blue-600">{{ $equipment['borrow_count'] }}</div>
                            <div class="text-xs text-gray-500">ครั้ง</div>
                        </div>
                    </a>
                @empty
                    <div class="text-center text-gray-500 py-8">ไม่มีข้อมูลการยืม</div>
                @endforelse
            </div>
        </div>

        <!-- Frequent Users -->
        <div class="lg:col-span-4 bg-white rounded-lg border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">ผู้ใช้ที่ส่งคำขอบ่อย</h3>
            <div class="space-y-3">
                @forelse($frequentUsers as $index => $item)
                    <a href="{{ route('admin.requests.index') }}?user_email={{ urlencode($item['user']->email ?? '') }}" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                        <div class="flex-1">
                            <div class="font-medium text-sm text-blue-600 hover:text-blue-800">{{ $item['user']->name ?? 'ไม่ระบุชื่อ' }}</div>
                            <div class="text-xs text-gray-500">{{ $item['user']->email ?? 'ไม่ระบุอีเมล' }} • ผู้ขอยืม</div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-blue-600">{{ $item['request_count'] }}</div>
                            <div class="text-xs text-gray-500">ครั้ง</div>
                        </div>
                    </a>
                @empty
                    <div class="text-center text-gray-500 py-8">ไม่มีข้อมูลผู้ใช้</div>
                @endforelse
            </div>
        </div>

        <!-- Pending Verifications -->
        <div class="lg:col-span-4 bg-white rounded-lg border p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">การยืนยันตัวตนที่รอดำเนินการ</h2>
                <a href="{{ route('admin.verification.index') }}" class="text-xs text-blue-600 hover:text-blue-800">ดูทั้งหมด</a>
            </div>
            
            @if(isset($pendingVerifications) && $pendingVerifications->count() > 0)
                <div class="space-y-3">
                    @foreach($pendingVerifications as $verification)
                        <div class="flex items-center justify-between p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex-1">
                                <div class="font-medium text-sm text-gray-900">
                                    {{ $verification->user->name ?? 'N/A' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $verification->user->email ?? 'N/A' }}
                                </div>
                                <div class="text-xs text-yellow-600 mt-1">
                                    {{ $verification->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="ml-3">
                                <a href="{{ route('admin.verification.show', $verification->id) }}" 
                                   class="inline-flex items-center px-2 py-1 text-xs font-medium text-yellow-800 bg-yellow-100 hover:bg-yellow-200 rounded-md transition-colors">
                                    ตรวจสอบ
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-gray-500 py-8">
                    <div class="text-lg mb-2"></div>
                    <div>ไม่มีคำขอการยืนยันตัวตนที่รอดำเนินการ</div>
                </div>
            @endif
        </div>

        <!-- Recent Requests -->
        <div class="lg:col-span-12 bg-white rounded-lg border p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">คำขอล่าสุด</h2>
                <a href="{{ route('admin.requests.index') }}" class="text-xs text-blue-600 hover:text-blue-800">ดูทั้งหมด</a>
            </div>
            
            @if($recentRequests->count() > 0)
                <div id="recent-activities" data-requests='@json($recentRequests)' class="min-w-full"></div>
            @else
                <div class="text-center text-gray-500 py-8">
                    <div class="text-lg mb-2">📋</div>
                    <div>ไม่มีคำขอล่าสุด</div>
                </div>
            @endif
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite('resources/js/app.js')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ---------- Category Chart ----------
            const barEl = document.getElementById('categoryBar');
            if (barEl && @json($categoryCounts->count()) > 0) {
                new Chart(barEl, {
                    type: 'bar',
                    data: {
                        labels: @json($categoryCounts->pluck('name')),
                        datasets: [
                            {
                                label: 'อุปกรณ์ทั้งหมด',
                                data: @json($categoryCounts->pluck('equipments_count')),
                                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                                borderColor: '#3b82f6',
                                borderWidth: 1
                            },
                            {
                                label: 'พร้อมใช้งาน',
                                data: @json($categoryCounts->pluck('available_equipments_count')),
                                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                                borderColor: '#22c55e',
                                borderWidth: 1
                            },
                            {
                                label: 'ถูกยืม',
                                data: @json($categoryCounts->pluck('borrowed_equipments_count')),
                                backgroundColor: 'rgba(239, 68, 68, 0.8)',
                                borderColor: '#ef4444',
                                borderWidth: 1
                            },
                            {
                                label: 'ซ่อมบำรุง',
                                data: @json($categoryCounts->pluck('maintenance_equipments_count')),
                                backgroundColor: 'rgba(249, 115, 22, 0.8)',
                                borderColor: '#f97316',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: { 
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { 
                            legend: { 
                                display: true,
                                position: 'bottom'
                            } 
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }

            // ---------- Dashboard Chart ----------
            const chartEl = document.getElementById('dashboard-chart');
            if (!chartEl) return;

            const chartData = JSON.parse(chartEl.dataset.chart);
            const ctx = chartEl.querySelector('canvas').getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        { label: 'คำขอทั้งหมด', data: chartData.TotalRequests, backgroundColor: 'rgba(59, 130, 246, 0.6)', borderColor: '#3b82f6', borderWidth: 1 },
                        { label: 'คำขอที่ถูกตอบรับแล้ว', data: chartData.Approved, backgroundColor: 'rgba(34, 197, 94, 0.6)', borderColor: '#22c55e', borderWidth: 1 },
                        { label: 'การยืมที่สำเร็จ', data: chartData.checkinReq, backgroundColor: 'rgba(168, 85, 247, 0.6)', borderColor: '#a855f7', borderWidth: 1 },
                        { label: 'คำขอที่รอดำเนินการ', data: chartData.Pending, backgroundColor: 'rgba(251, 191, 36, 0.6)', borderColor: '#fbbf24', borderWidth: 1 },
                        { label: 'คำขอที่ถูกปฏิเสธ', data: chartData.Rejected, backgroundColor: 'rgba(239, 68, 68, 0.6)', borderColor: '#ef4444', borderWidth: 1 },
                        { label: 'คำขอที่ยกเลิก', data: chartData.Cancelled, backgroundColor: 'rgba(107, 114, 128, 0.6)', borderColor: '#6b7280', borderWidth: 1 },
                    ]
                },
                options: { 
                    responsive: true, 
                    plugins: { 
                        legend: { position: 'bottom' } 
                    }, 
                    scales: { 
                        y: { 
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        } 
                    } 
                }
            });

        });
    </script>
</x-admin-layout>
