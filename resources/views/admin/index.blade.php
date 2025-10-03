<x-admin-layout>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- KPI Cards -->
        <div class="lg:col-span-12 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg border p-4">
                <div class="text-2xl font-semibold mt-1">{{ $borrowStatus['TotalRequests'] }}</div>
                <div class="text-xs text-gray-500">คำขอทั้งหมด</div>
            </div>
            <div class="bg-white rounded-lg border p-4">
                <div class="text-2xl font-semibold mt-1">{{ $borrowStatus['checkinReq'] }}</div>
                <div class="text-xs text-gray-500">การยืมที่สำเร็จ</div>
            </div>
            <div class="bg-white rounded-lg border p-4">
                <div class="text-2xl font-semibold mt-1">{{ $borrowStatus['Pending'] }}</div>
                <div class="text-xs text-gray-500">คำขอที่รอดำเนินการ</div>
            </div>
            <div class="bg-white rounded-lg border p-4">
                <div class="text-2xl font-semibold mt-1">{{ $borrowStatus['Rejected'] }}</div>
                <div class="text-xs text-gray-500">คำขอที่ถูกปฏิเสธ</div>
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
            <div class="text-sm font-medium mb-4">อุปกรณ์ที่พร้อมใช้งานตามหมวดหมู่</div>
            <div style="height: 300px; width: 100%;">
                <canvas id="categoryBar"></canvas>
                @if($categoryCounts->isEmpty())
                    <div class="flex items-center justify-center h-full text-gray-500">
                        ไม่มีข้อมูลหมวดหมู่
                    </div>
                @endif
            </div>
        </div>

        <!-- Analytics Section -->
        <div class="lg:col-span-12">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-900">Analytics</h2>
                <div class="flex gap-2">
                    <a href="{{ route('admin.dashboard.export', ['type' => 'trends', 'year' => $selectedYear, 'month' => $selectedMonth]) }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm">
                        Export Trends CSV
                    </a>
                    <a href="{{ route('admin.dashboard.export', ['type' => 'popular', 'year' => $selectedYear, 'month' => $selectedMonth]) }}" 
                       class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm">
                        Export Popular CSV
                    </a>
                </div>
            </div>
        </div>

        <!-- Borrowing Trends Chart -->
        <div class="lg:col-span-8 bg-white rounded-lg border p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ $borrowingTrends['title'] }}</h3>
                <div class="text-sm text-gray-500">
                    @if($selectedMonth)
                        {{ date('F Y', mktime(0, 0, 0, $selectedMonth, 1, $selectedYear)) }}
                    @else
                        {{ $selectedYear }}
                    @endif
                </div>
            </div>
            <div style="height: 300px; width: 100%;">
                <canvas id="borrowingTrendsChart"></canvas>
                @if(empty($borrowingTrends['data']) || array_sum($borrowingTrends['data']) == 0)
                    <div class="flex items-center justify-center h-full text-gray-500">
                        ไม่มีข้อมูลการยืมในช่วงเวลานี้
                    </div>
                @endif
            </div>
        </div>

        <!-- Popular Equipment -->
        <div class="lg:col-span-4 bg-white rounded-lg border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">อุปกรณ์ยอดนิยม</h3>
            <div class="space-y-3">
                @forelse($popularEquipment as $index => $equipment)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <div class="font-medium text-sm">{{ $equipment['equipment_name'] }}</div>
                            <div class="text-xs text-gray-500">{{ $equipment['equipment_code'] }} • {{ $equipment['category'] }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-blue-600">{{ $equipment['borrow_count'] }}</div>
                            <div class="text-xs text-gray-500">ครั้ง</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-8">ไม่มีข้อมูลการยืม</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Requests -->
        <div id="recent-activities" data-requests='@json($recentRequests)' class="lg:col-span-12 bg-white rounded-lg border"></div>
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
                        datasets: [{
                            label: 'อุปกรณ์ที่พร้อมใช้งาน',
                            data: @json($categoryCounts->pluck('equipments_count')),
                            backgroundColor: '#10b981',
                            borderColor: '#059669',
                            borderWidth: 1
                        }]
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
                        { label: 'คำขอทั้งหมด', data: chartData.TotalRequests, backgroundColor: 'rgba(74,222,128,0.6)', borderColor: '#4ade80', borderWidth: 1 },
                        { label: 'การยืมที่สำเร็จ', data: chartData.checkinReq, backgroundColor: 'rgba(34,197,94,0.6)', borderColor: '#22c55e', borderWidth: 1 },
                        { label: 'คำขอที่รอดำเนินการ', data: chartData.Pending, backgroundColor: 'rgba(251,191,36,0.6)', borderColor: '#fbbf24', borderWidth: 1 },
                        { label: 'คำขอที่ถูกปฏิเสธ', data: chartData.Rejected, backgroundColor: 'rgba(239,68,68,0.6)', borderColor: '#ef4444', borderWidth: 1 },
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

            // ---------- Borrowing Trends Chart ----------
            const trendsCtx = document.getElementById('borrowingTrendsChart');
            if (trendsCtx && @json($borrowingTrends['data']).length > 0) {
                new Chart(trendsCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($borrowingTrends['labels']),
                        datasets: [{
                            label: 'ยอดยืม',
                            data: @json($borrowingTrends['data']),
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            borderColor: '#3b82f6',
                            borderWidth: 1,
                            borderRadius: 4,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        aspectRatio: 2,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });
            }

        });
    </script>
</x-admin-layout>
