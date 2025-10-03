<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\BorrowRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
            ];

            // Chart data
            if ($month) {
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

                $dailyData = BorrowRequest::selectRaw('DAY(created_at) as day, COUNT(*) as total, SUM(status="check_in") as checkin, SUM(status="pending") as pending, SUM(status="rejected") as rejected')
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->groupBy('day')
                    ->orderBy('day')
                    ->get()
                    ->keyBy('day');

                $chartData = [
                    'labels' => array_map(fn($d) => "Day $d", range(1, $daysInMonth)),
                    'TotalRequests' => [],
                    'checkinReq' => [],
                    'Pending' => [],
                    'Rejected' => [],
                ];

                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $chartData['TotalRequests'][] = $dailyData[$d]->total ?? 0;
                    $chartData['checkinReq'][] = $dailyData[$d]->checkin ?? 0;
                    $chartData['Pending'][] = $dailyData[$d]->pending ?? 0;
                    $chartData['Rejected'][] = $dailyData[$d]->rejected ?? 0;
                }
            } else {
                $months = range(1, 12);

                $monthlyData = BorrowRequest::selectRaw('MONTH(created_at) as month, COUNT(*) as total, SUM(status="check_in") as checkin, SUM(status="pending") as pending, SUM(status="rejected") as rejected')
                    ->whereYear('created_at', $year)
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get()
                    ->keyBy('month');

                $chartData = [
                    'labels' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                    'TotalRequests' => [],
                    'checkinReq' => [],
                    'Pending' => [],
                    'Rejected' => [],
                ];

                foreach ($months as $m) {
                    $chartData['TotalRequests'][] = $monthlyData[$m]->total ?? 0;
                    $chartData['checkinReq'][] = $monthlyData[$m]->checkin ?? 0;
                    $chartData['Pending'][] = $monthlyData[$m]->pending ?? 0;
                    $chartData['Rejected'][] = $monthlyData[$m]->rejected ?? 0;
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
                'chartData' => $chartData,
                'availableYears' => $availableYears,
                'selectedYear' => $year,
                'selectedMonth' => $month,
                'recentRequests' => $recentRequests,
                'categoryCounts' => Category::withCount(['equipments' => function($query) {
                    $query->where('status', 'available');
                }])->get(),
                // Analytics data
                'popularEquipment' => $popularEquipment,
                'borrowingTrends' => $borrowingTrends,
            ];
        });

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
        $query = BorrowRequest::query();
        $query->whereYear('created_at', $year);
        $query->where('status', 'check_in');
        
        if ($month) {
            $query->whereMonth('created_at', $month);
        }

        if ($month) {
            // Daily data for specific month
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $data = $query->selectRaw('DAY(created_at) as day, COUNT(*) as total_borrows')
                ->groupBy('day')
                ->orderBy('day')
                ->get()
                ->keyBy('day');

            $labels = [];
            $values = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $labels[] = "Day $d";
                $values[] = $data[$d]->total_borrows ?? 0;
            }

            return [
                'labels' => $labels,
                'data' => $values,
                'title' => "ยอดยืมรายวัน - " . date('F Y', mktime(0, 0, 0, $month, 1, $year))
            ];
        } else {
            // Monthly data
            $data = $query->selectRaw('MONTH(created_at) as month, COUNT(*) as total_borrows')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');

            $months = range(1, 12);
            $labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            $values = [];

            foreach ($months as $m) {
                $values[] = $data[$m]->total_borrows ?? 0;
            }

            return [
                'labels' => $labels,
                'data' => $values,
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

        if ($type === 'trends') {
            $data = $this->getBorrowingTrends($year, $month);
            $csvData = [];
            $csvData[] = ['Period', 'Borrow Count'];
            
            foreach ($data['labels'] as $index => $label) {
                $csvData[] = [$label, $data['data'][$index]];
            }
        } else { // popular
            $popularEquipment = $this->getPopularEquipment($year, $month);
            $csvData = [];
            $csvData[] = ['Equipment Name', 'Equipment Code', 'Borrow Count', 'Category'];
            
            foreach ($popularEquipment as $item) {
                $csvData[] = [
                    $item['equipment_name'],
                    $item['equipment_code'],
                    $item['borrow_count'],
                    $item['category']
                ];
            }
        }

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
