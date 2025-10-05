<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\Equipment;
use App\Models\Category;
use App\Models\User;
use App\Models\BorrowRequest;

Route::get('/equipments', function () {
    return Equipment::with('category')->get();
});
Route::get('/categories', function () {
    $categories = Category::withCount('equipments')->get();
    return response()->json($categories);
});
Route::get('/users', function () {
    return User::all();
});


// Logs API route
Route::get('/logs', function (Request $request) {
    $logs = \App\Models\Log::with('admin')
        ->when($request->admin, function($q) use ($request) {
            // Try exact match first, then partial match
            $adminName = $request->admin;
            return $q->whereHas('admin', function($a) use ($adminName) {
                $a->where('name', $adminName)
                  ->orWhere('email', $adminName)
                  ->orWhere('uid', $adminName);
            });
        })
        ->when($request->action, fn($q) => $q->where('action', $request->action))
        ->when($request->target_type, fn($q) => $q->where('target_type', $request->target_type))
        ->when($request->module, fn($q) => $q->where('module', $request->module))
        ->when($request->severity, fn($q) => $q->where('severity', $request->severity))
        ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
        ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($log) {
            // Add missing fields with default values or derived values
            $log->module = $log->module ?: ($log->target_type === 'equipment' ? 'equipment' : 'system');
            
            // Extract target name from description
            if (!$log->target_name && $log->description) {
                if (preg_match('/: ([^(]+) \(/', $log->description, $matches)) {
                    $log->target_name = trim($matches[1]);
                } else {
                    $log->target_name = $log->target_type ? ucfirst($log->target_type) : '-';
                }
            } else {
                $log->target_name = $log->target_name ?: '-';
            }
            
            $log->severity = $log->severity ?: 'info';
            $log->ip_address = $log->ip_address ?: '-';
            return $log;
        });

    return response()->json($logs);
});


route::get('/requests', function () {
    return BorrowRequest::with('user', 'equipment')->get()->map(function ($req) {
        return [
            'id' => $req->id,
            'req_id' => $req->req_id,
            'user_name' => $req->user->name ?? 'N/A',
            'equipment_name' => $req->equipment->name ?? 'N/A',
            'start_at' => $req->start_at ? $req->start_at->format('Y-m-d') : '-',
            'end_at' => $req->end_at ? $req->end_at->format('Y-m-d') : '-',
            'status' => $req->status,
            'eject_reason' => $req->eject_reason ?? '-',
            'reason' => $req->reject_reason ?? $req->cancel_reason ?? '-',
            'created_at' => optional($req->created_at)->format('Y-m-d'),
        ];
    });
});

// Transactions API route
Route::get('/transactions', function (Request $request) {
    // For now, we'll create mock transaction data based on borrow requests
    // In a real application, you would have a dedicated Transaction model
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
});

