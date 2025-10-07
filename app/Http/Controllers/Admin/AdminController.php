<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\BorrowRequest;
use App\Models\Equipment;
use App\Models\Log;
use App\Models\User;
use App\Models\VerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month'); // optional

        $cacheKey = "admin_dashboard_{$year}_{$month}";

        $data = Cache::remember($cacheKey, 300, function () use ($year, $month) {
            $query = BorrowRequest::query();
            $query->whereYear('created_at', $year);

            if ($month) {
                $query->whereMonth('created_at', $month);
            }

            // KPI Cards
            $borrowStatus = [
                'TotalRequests' => (clone $query)->count(),
                'checkinReq' => (clone $query)->where('status', 'check_in')->count(),
                'Approved' => (clone $query)->where('status', 'approved')->count(),
                'Rejected' => (clone $query)->where('status', 'rejected')->count(),
                'Pending' => (clone $query)->where('status', 'pending')->count(),
                'Cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            ];

            // Equipment stats
            $equipmentStats = [
                'TotalEquipment' => Equipment::count(),
                'AvailableEquipment' => Equipment::where('status', 'available')->count(),
                'BorrowedEquipment' => Equipment::where('status', 'unavailable')->count(),
                'MaintenanceEquipment' => Equipment::where('status', 'maintenance')->count(),
            ];

            // Chart data
            if ($month) {
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

                $dailyData = BorrowRequest::selectRaw('DAY(created_at) as day, COUNT(*) as total, SUM(status="approved") as approved, SUM(status="check_in") as checkin, SUM(status="pending") as pending, SUM(status="rejected") as rejected, SUM(status="cancelled") as cancelled')
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->groupBy('day')
                    ->orderBy('day')
                    ->get()
                    ->keyBy('day');

                $chartData = [
                    'labels' => array_map(fn($d) => "Day $d", range(1, $daysInMonth)),
                    'TotalRequests' => [],
                    'Approved' => [],
                    'checkinReq' => [],
                    'Pending' => [],
                    'Rejected' => [],
                    'Cancelled' => [],
                ];

                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $chartData['TotalRequests'][] = $dailyData[$d]->total ?? 0;
                    $chartData['Approved'][] = $dailyData[$d]->approved ?? 0;
                    $chartData['checkinReq'][] = $dailyData[$d]->checkin ?? 0;
                    $chartData['Pending'][] = $dailyData[$d]->pending ?? 0;
                    $chartData['Rejected'][] = $dailyData[$d]->rejected ?? 0;
                    $chartData['Cancelled'][] = $dailyData[$d]->cancelled ?? 0;
                }
            } else {
                $months = range(1, 12);

                $monthlyData = BorrowRequest::selectRaw('MONTH(created_at) as month, COUNT(*) as total, SUM(status="approved") as approved, SUM(status="check_in") as checkin, SUM(status="pending") as pending, SUM(status="rejected") as rejected, SUM(status="cancelled") as cancelled')
                    ->whereYear('created_at', $year)
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get()
                    ->keyBy('month');

                $chartData = [
                    'labels' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                    'TotalRequests' => [],
                    'Approved' => [],
                    'checkinReq' => [],
                    'Pending' => [],
                    'Rejected' => [],
                    'Cancelled' => [],
                ];

                foreach ($months as $m) {
                    $chartData['TotalRequests'][] = $monthlyData[$m]->total ?? 0;
                    $chartData['Approved'][] = $monthlyData[$m]->approved ?? 0;
                    $chartData['checkinReq'][] = $monthlyData[$m]->checkin ?? 0;
                    $chartData['Pending'][] = $monthlyData[$m]->pending ?? 0;
                    $chartData['Rejected'][] = $monthlyData[$m]->rejected ?? 0;
                    $chartData['Cancelled'][] = $monthlyData[$m]->cancelled ?? 0;
                }
            }

            $availableYears = BorrowRequest::selectRaw('YEAR(created_at) as year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');

            $recentRequests = (clone $query)->with('user', 'equipment')->latest()->take(5)->get()->map(function($r) {
                return [
                    'id' => $r->req_id,
                    'user_name' => $r->user->name ?? 'N/A',
                    'equipment_name' => $r->equipment->name ?? 'N/A',
                    'start' => optional($r->start_at)->format('Y-m-d'),
                    'end' => optional($r->end_at)->format('Y-m-d'),
                    'status' => ucfirst($r->status),
                ];
            });

            // Analytics data for dashboard
            $popularEquipment = $this->getPopularEquipment($year, $month);
            $borrowingTrends = $this->getBorrowingTrends($year, $month);

            return [
                'borrowStatus' => $borrowStatus,
                'equipmentStats' => $equipmentStats,
                'chartData' => $chartData,
                'availableYears' => $availableYears,
                'selectedYear' => $year,
                'selectedMonth' => $month,
                'recentRequests' => $recentRequests,
                'categoryCounts' => Category::withCount([
                    'equipments',
                    'equipments as available_equipments_count' => function($query) {
                        $query->where('status', 'available');
                    },
                    'equipments as borrowed_equipments_count' => function($query) {
                        $query->where('status', 'unavailable');
                    },
                    'equipments as maintenance_equipments_count' => function($query) {
                        $query->where('status', 'maintenance');
                    }
                ])->get(),
                // Analytics data
                'popularEquipment' => $popularEquipment,
                'borrowingTrends' => $borrowingTrends,
                'frequentUsers' => BorrowRequest::with('user')
                    ->selectRaw('users_id, COUNT(*) as request_count')
                    ->groupBy('users_id')
                    ->orderByDesc('request_count')
                    ->limit(5)
                    ->get()
                    ->map(function($item) {
                        return [
                            'user' => $item->user,
                            'request_count' => $item->request_count
                        ];
                    }),
            ];
        });

        // Get pending verification requests (outside cache for real-time data)
        $pendingVerifications = VerificationRequest::with('user')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // Add pending verifications to the data array
        $data['pendingVerifications'] = $pendingVerifications;

        return view('admin.index', $data);
    }

    private function getPopularEquipment($year, $month)
    {
        $query = BorrowRequest::with('equipment')
            ->where('status', 'check_in')
            ->whereYear('created_at', $year);

        if ($month) {
            $query->whereMonth('created_at', $month);
        }

        return $query->selectRaw('equipments_id, COUNT(*) as borrow_count')
            ->groupBy('equipments_id')
            ->orderBy('borrow_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'equipment_name' => $item->equipment->name ?? 'Unknown',
                    'equipment_code' => $item->equipment->code ?? 'N/A',
                    'borrow_count' => $item->borrow_count,
                    'category' => $item->equipment->category->name ?? 'N/A'
                ];
            });
    }


    private function getBorrowingTrends($year, $month)
    {
        $baseQuery = BorrowRequest::query();
        $baseQuery->whereYear('created_at', $year);
        
        if ($month) {
            $baseQuery->whereMonth('created_at', $month);
        }

        if ($month) {
            // Daily data for specific month
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            
            // Get completed borrows
            $completedData = (clone $baseQuery)->where('status', 'check_in')
                ->selectRaw('DAY(created_at) as day, COUNT(*) as total_borrows')
                ->groupBy('day')
                ->orderBy('day')
                ->get()
                ->keyBy('day');

            // Get canceled requests
            $canceledData = (clone $baseQuery)->where('status', 'cancelled')
                ->selectRaw('DAY(created_at) as day, COUNT(*) as total_canceled')
                ->groupBy('day')
                ->orderBy('day')
                ->get()
                ->keyBy('day');

            $labels = [];
            $completedValues = [];
            $canceledValues = [];
            
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $labels[] = "Day $d";
                $completedValues[] = $completedData[$d]->total_borrows ?? 0;
                $canceledValues[] = $canceledData[$d]->total_canceled ?? 0;
            }

            return [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'ยืมสำเร็จ',
                        'data' => $completedValues,
                        'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                        'borderColor' => 'rgba(34, 197, 94, 1)',
                        'borderWidth' => 1
                    ],
                    [
                        'label' => 'ยกเลิก',
                        'data' => $canceledValues,
                        'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                        'borderColor' => 'rgba(239, 68, 68, 1)',
                        'borderWidth' => 1
                    ]
                ],
                'title' => "ยอดยืมรายวัน - " . date('F Y', mktime(0, 0, 0, $month, 1, $year))
            ];
        } else {
            // Monthly data
            $completedData = (clone $baseQuery)->where('status', 'check_in')
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as total_borrows')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');

            $canceledData = (clone $baseQuery)->where('status', 'cancelled')
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as total_canceled')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');

            $months = range(1, 12);
            $labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            $completedValues = [];
            $canceledValues = [];

            foreach ($months as $m) {
                $completedValues[] = $completedData[$m]->total_borrows ?? 0;
                $canceledValues[] = $canceledData[$m]->total_canceled ?? 0;
            }

            return [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'ยืมสำเร็จ',
                        'data' => $completedValues,
                        'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                        'borderColor' => 'rgba(34, 197, 94, 1)',
                        'borderWidth' => 1
                    ],
                    [
                        'label' => 'ยกเลิก',
                        'data' => $canceledValues,
                        'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                        'borderColor' => 'rgba(239, 68, 68, 1)',
                        'borderWidth' => 1
                    ]
                ],
                'title' => "ยอดยืมรายเดือน - $year"
            ];
        }
    }

    public function exportCsv(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month');
        $type = $request->input('type', 'trends'); // trends, popular

        $filename = "dashboard_export_{$type}_{$year}" . ($month ? "_{$month}" : "") . ".csv";

        // Log the export action
        Log::create([
            'admin_id' => Auth::id() ?? 1,
            'action' => 'export_initiated',
            'target_type' => 'system',
            'target_id' => 0,
            'target_name' => 'Dashboard Export',
            'description' => "เริ่มส่งออกข้อมูล CSV: {$type} สำหรับปี {$year}" . ($month ? " เดือน {$month}" : ""),
            'module' => 'export',
            'severity' => 'info',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent() ?? 0
        ]);

        switch ($type) {
            case 'borrow_requests':
                $query = BorrowRequest::with(['user', 'equipment.category']);
                
                if ($year) {
                    $query->whereYear('created_at', $year);
                }
                if ($month) {
                    $query->whereMonth('created_at', $month);
                }
                
                $borrowRequests = $query->orderBy('created_at', 'desc')->get();
                
                $csvData = [];
                $csvData[] = ['Request ID', 'User Email', 'User Role', 'Device Name', 'Device Code', 'Category', 'Start Date', 'End Date', 'Status'];
                
                foreach ($borrowRequests as $borrowRequest) {
                    $csvData[] = [
                        $borrowRequest->req_id,
                        $borrowRequest->user->email ?? 'N/A',
                        'ผู้ขอยืม', // Default role
                        $borrowRequest->equipment->name ?? 'N/A',
                        $borrowRequest->equipment->code ?? 'N/A',
                        $borrowRequest->equipment->category->name ?? 'N/A',
                        $borrowRequest->start_at ? $borrowRequest->start_at->format('Y-m-d') : '',
                        $borrowRequest->end_at ? $borrowRequest->end_at->format('Y-m-d') : '',
                        $this->getStatusText($borrowRequest->status)
                    ];
                }
                break;

            case 'device_usage':
                $query = BorrowRequest::with(['equipment.category'])
                    ->where('status', 'check_in'); // Only completed borrows
                
                if ($year) {
                    $query->whereYear('created_at', $year);
                }
                if ($month) {
                    $query->whereMonth('created_at', $month);
                }
                
                $deviceUsage = $query->selectRaw('equipments_id, COUNT(*) as times_borrowed')
                    ->groupBy('equipments_id')
                    ->orderByDesc('times_borrowed')
                    ->get();
                
                $csvData = [];
                $csvData[] = ['Device Name', 'Device Code', 'Category', 'Times Borrowed'];
                
                foreach ($deviceUsage as $usage) {
                    $equipment = Equipment::with('category')->find($usage->equipments_id);
                    if ($equipment) {
                        $csvData[] = [
                            $equipment->name,
                            $equipment->code,
                            $equipment->category->name ?? 'N/A',
                            $usage->times_borrowed
                        ];
                    }
                }
                break;

            case 'user_activity':
                $query = BorrowRequest::with('user');
                
                if ($year) {
                    $query->whereYear('created_at', $year);
                }
                if ($month) {
                    $query->whereMonth('created_at', $month);
                }
                
                $userActivity = $query->selectRaw('users_id, COUNT(*) as total_requests, SUM(status = "check_in") as completed, SUM(status = "cancelled") as canceled')
                    ->groupBy('users_id')
                    ->orderByDesc('total_requests')
                    ->get();
                
                $csvData = [];
                $csvData[] = ['User Email', 'Role', 'Total Requests', 'Completed', 'Canceled'];
                
                foreach ($userActivity as $activity) {
                    $user = User::find($activity->users_id);
                    if ($user) {
                        $csvData[] = [
                            $user->email,
                            'ผู้ขอยืม', // Default role
                            $activity->total_requests,
                            $activity->completed,
                            $activity->canceled
                        ];
                    }
                }
                break;

            default:
                $csvData = [];
                $csvData[] = ['No data available'];
                break;
        }

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper encoding
            fwrite($file, "\xEF\xBB\xBF");
            
            foreach ($csvData as $row) {
                fputcsv($file, $row, ',', '"');
            }
            fclose($file);
        };

        // Log successful export
        Log::create([
            'admin_id' => Auth::id() ?? 1,
            'action' => 'export_completed',
            'target_type' => 'system',
            'target_id' => 0,
            'target_name' => 'Dashboard Export',
            'description' => "ส่งออกข้อมูล CSV เสร็จสิ้น: {$filename}",
            'module' => 'export',
            'severity' => 'success',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent() ?? 0
        ]);

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function getStatusText($status)
    {
        switch (strtolower($status)) {
            case 'pending':
                return 'รออนุมัติ';
            case 'approved':
                return 'อนุมัติแล้ว';
            case 'rejected':
                return 'ปฏิเสธ';
            case 'check_out':
                return 'รับแล้ว';
            case 'check_in':
                return 'คืนแล้ว';
            case 'cancelled':
                return 'ยกเลิก';
            default:
                return ucfirst($status);
        }
    }
}
