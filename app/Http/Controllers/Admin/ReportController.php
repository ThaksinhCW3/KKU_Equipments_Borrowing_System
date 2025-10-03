<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MultiFilteredExport;
use App\Models\Equipment;
use App\Models\Category;
use App\Models\User;
use App\Models\BorrowRequest;
use App\Models\Log;

class ReportController extends Controller
{
    //! Vue
    public function index(string $type)
    {
        return view('admin.report.index', ['type' => $type]);
    }
    //! Report
    public function userReport()
    {
        $users = User::all()->map(fn($user) => [
            'uid' => $user->uid,
            'name' => $user->name,
            'email' => $user->email,
            'phonenumber' => $user->phonenumber ?? '-',
            'created_at' => optional($user->created_at)->format('d/m/Y'),
        ]);
        return response()->json($users);
    }
    //! Equipment
    public function equipmentReport()
    {
        $equipments = Equipment::with('category')->get()->map(fn($eq) => [
            'id' => $eq->id,
            'code' => $eq->code,
            'name' => $eq->name,
            'category_name' => $eq->category->name ?? 'N/A',
            'created_at' => optional($eq->created_at)->format('d/m/Y'),
        ]);
        return response()->json($equipments);
    }
    //! Category
    public function categoryReport()
    {
        $categories = Category::all()->map(fn($cat) => [
            'id' => $cat->id,
            'name' => $cat->name,
        ]);
        return response()->json($categories);
    }
    //! Request
    public function requestReport()
    {
        $requests = BorrowRequest::with(['user', 'equipment'])
            ->whereIn('status', ['approved', 'rejected', 'cancelled'])
            ->latest()
            ->get()
            ->map(fn($req) => [
                'req_id' => $req->req_id,
                'user_name' => $req->user->name ?? 'N/A',
                'equipment_name' => $req->equipment->name ?? 'N/A',
                'date' => $req->created_at->format('Y-m-d'),
                'status' => ucfirst($req->status),
                'reason' => $req->reject_reason ?? $req->cancel_reason ?? '-',
            ]);
        return response()->json($requests);
    }
    //! Log
    public function logReport(Request $request)
    {
        $logs = Log::with('admin')
            ->when($request->admin, fn($q) => $q->whereHas('admin', fn($a) => $a->where('name', 'like', "%{$request->admin}%")))
            ->when($request->action, fn($q) => $q->where('action', $request->action))
            ->when($request->target_type, fn($q) => $q->where('target_type', $request->target_type))
            ->when($request->module, fn($q) => $q->where('module', $request->module))
            ->when($request->severity, fn($q) => $q->where('severity', $request->severity))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($logs);
    }

    //! Transaction
    public function transactionReport(Request $request)
    {
        // Generate transaction data from borrow requests
        $requests = BorrowRequest::with('user', 'equipment')->get();
        
        $transactions = collect();
        
        // Generate transactions from borrow requests
        foreach ($requests as $request) {
            // Borrow transaction
            $transactions->push([
                'id' => $request->id * 1000 + 1,
                'transaction_id' => 'TXN' . str_pad($request->id, 6, '0', STR_PAD_LEFT) . 'B',
                'transaction_type' => 'borrow',
                'user' => $request->user,
                'equipment' => $request->equipment,
                'status' => $request->status === 'approved' ? 'completed' : 
                           ($request->status === 'rejected' ? 'cancelled' : 'pending'),
                'amount' => 0, // Free borrowing
                'start_date' => $request->start_at,
                'end_date' => $request->end_at,
                'description' => 'การยืมอุปกรณ์: ' . ($request->equipment->name ?? 'N/A'),
                'notes' => $request->request_reason,
                'created_at' => $request->created_at,
                'updated_at' => $request->updated_at
            ]);
            
            // Return transaction (if status is check_in)
            if ($request->status === 'check_in') {
                $transactions->push([
                    'id' => $request->id * 1000 + 2,
                    'transaction_id' => 'TXN' . str_pad($request->id, 6, '0', STR_PAD_LEFT) . 'R',
                    'transaction_type' => 'return',
                    'user' => $request->user,
                    'equipment' => $request->equipment,
                    'status' => 'completed',
                    'amount' => 0,
                    'start_date' => $request->end_at,
                    'end_date' => $request->end_at,
                    'description' => 'การคืนอุปกรณ์: ' . ($request->equipment->name ?? 'N/A'),
                    'notes' => 'คืนอุปกรณ์ตามกำหนด',
                    'created_at' => $request->updated_at,
                    'updated_at' => $request->updated_at
                ]);
            }
            
            // Penalty transaction (if late return)
            if ($request->status === 'check_in' && $request->end_at && $request->updated_at) {
                $endDate = \Carbon\Carbon::parse($request->end_at);
                $returnDate = \Carbon\Carbon::parse($request->updated_at);
                
                if ($returnDate->gt($endDate)) {
                    $daysLate = $returnDate->diffInDays($endDate);
                    $penaltyAmount = $daysLate * 50; // 50 baht per day
                    
                    $transactions->push([
                        'id' => $request->id * 1000 + 3,
                        'transaction_id' => 'TXN' . str_pad($request->id, 6, '0', STR_PAD_LEFT) . 'P',
                        'transaction_type' => 'penalty',
                        'user' => $request->user,
                        'equipment' => $request->equipment,
                        'status' => 'completed',
                        'amount' => $penaltyAmount,
                        'start_date' => $request->end_at,
                        'end_date' => $request->updated_at,
                        'description' => 'ค่าปรับการคืนล่าช้า: ' . ($request->equipment->name ?? 'N/A') . ' (' . $daysLate . ' วัน)',
                        'notes' => 'คืนล่าช้า ' . $daysLate . ' วัน',
                        'created_at' => $request->updated_at,
                        'updated_at' => $request->updated_at
                    ]);
                }
            }
        }
        
        // Apply filters
        $filteredTransactions = $transactions->filter(function ($transaction) use ($request) {
            if ($request->transaction_type && $transaction['transaction_type'] !== $request->transaction_type) {
                return false;
            }
            
            if ($request->status && $transaction['status'] !== $request->status) {
                return false;
            }
            
            if ($request->user_name && !str_contains(strtolower($transaction['user']['name'] ?? ''), strtolower($request->user_name))) {
                return false;
            }
            
            if ($request->equipment_name && !str_contains(strtolower($transaction['equipment']['name'] ?? ''), strtolower($request->equipment_name))) {
                return false;
            }
            
            if ($request->date_from && $transaction['created_at'] < $request->date_from) {
                return false;
            }
            
            if ($request->date_to && $transaction['created_at'] > $request->date_to) {
                return false;
            }
            
            return true;
        });
        
        return response()->json($filteredTransactions->values()->all());
    }

    // //! Log Statistics
    // public function logStats()
    // {
    //     $stats = [
    //         'total_logs' => Log::count(),
    //         'today_logs' => Log::whereDate('created_at', today())->count(),
    //         'this_week_logs' => Log::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
    //         'this_month_logs' => Log::whereMonth('created_at', now()->month)->count(),
    //         'top_actions' => Log::selectRaw('action, COUNT(*) as count')
    //             ->groupBy('action')
    //             ->orderBy('count', 'desc')
    //             ->limit(10)
    //             ->get(),
    //         'top_modules' => Log::selectRaw('module, COUNT(*) as count')
    //             ->groupBy('module')
    //             ->orderBy('count', 'desc')
    //             ->limit(10)
    //             ->get(),
    //         'severity_distribution' => Log::selectRaw('severity, COUNT(*) as count')
    //             ->groupBy('severity')
    //             ->orderBy('count', 'desc')
    //             ->get(),
    //         'recent_activities' => Log::with('admin')
    //             ->latest()
    //             ->limit(10)
    //             ->get()
    //     ];

    //     return response()->json($stats);
    // }

    //? Export
    public function export(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:user,equipment,category,request,log,transaction',
        ]);

        $type = $request->input('type');
        $filename = "{$type}-report.xlsx";

        return Excel::download(new MultiFilteredExport($request, $type), $filename);
    }
}