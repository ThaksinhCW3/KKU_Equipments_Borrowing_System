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
        ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
        ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
        ->orderBy('created_at', 'desc')
        ->orderBy('id', 'desc')
        ->get()
        ->map(function ($log) {
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
    try {
        $transactions = \App\Models\BorrowTransaction::with(['borrowRequest.user', 'borrowRequest.equipment'])
            ->get();
        
        $mappedTransactions = $transactions->map(function ($transaction) {
            $request = $transaction->borrowRequest;
            return [
                'id' => $transaction->id,
                'transaction_id' => $request->req_id ?? 'N/A',
                'transaction_type' => 'borrow', // All are borrow transactions
                'user' => [
                    'name' => $request->user->name ?? 'N/A',
                    'email' => $request->user->email ?? 'N/A'
                ],
                'equipment' => [
                    'name' => $request->equipment->name ?? 'N/A',
                    'code' => $request->equipment->code ?? 'N/A'
                ],
                'status' => $request->status ?? 'pending',
                'amount' => $transaction->penalty_amount ?? 0,
                'start_date' => $request->start_at,
                'end_date' => $request->end_at,
                'checked_out_at' => $transaction->checked_out_at,
                'checked_in_at' => $transaction->checked_in_at,
                'description' => $request->request_reason ?? 'การยืมอุปกรณ์',
                'notes' => $transaction->notes,
                'created_at' => $transaction->created_at,
                'updated_at' => $transaction->updated_at
            ];
        });
        
        return response()->json($mappedTransactions->toArray());
    } catch (\Exception $e) {
        \Log::error('Transaction API Error:', ['error' => $e->getMessage()]);
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

