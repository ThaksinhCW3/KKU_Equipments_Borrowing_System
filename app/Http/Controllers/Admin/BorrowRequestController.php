<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BorrowRequest;
use App\Models\BorrowTransaction;
use App\Models\Log;
use Illuminate\Http\Request;
use App\Notifications\BorrowRequestApproved;
use App\Notifications\BorrowRequestRejected;
use Illuminate\Support\Carbon;
use App\Traits\ClearsDashboardCache;
use Illuminate\Support\Facades\Auth;

class BorrowRequestController extends Controller
{
    use ClearsDashboardCache;

    public function index()
    {
        $requests = BorrowRequest::with('user', 'equipment')
            ->latest()
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'req_id' => $r->req_id,
                    'uid' => $r->user->uid,
                    'user_name' => $r->user->name ?? 'N/A',
                    'equipment_name' => $r->equipment->name ?? 'N/A',
                    'equipment_photo' => $r->equipment->photo_path ?? null,
                    'start_at' => $r->start_at ? $r->start_at->format('d-m-Y') : '-',
                    'end_at' => $r->end_at ? $r->end_at->format('d-m-Y') : '-',
                    'date' => $r->created_at->format('d-m-Y'),
                    'status' => ucfirst($r->status),
                    'reason' => $r->reject_reason ?? $r->cancel_reason ?? '-',
                ];
            });

        return view('admin.request.index', compact('requests'));
    }

    public function show($req_id)
    {
        $requests = BorrowRequest::with(['user', 'equipment', 'transaction'])
            ->where('req_id', $req_id)
            ->firstOrFail();

        $tableRequests = BorrowRequest::with('user', 'equipment')
            ->latest()
            ->take(25)
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'req_id' => $r->req_id,
                    'uid' => $r->user->uid ?? null,
                    'user_name' => $r->user->name ?? 'N/A',
                    'equipment_name' => $r->equipment->name ?? 'N/A',
                    'equipment_photo' => $r->equipment->photo_path ?? null,
                    'start_at' => $r->start_at ? $r->start_at->format('d-m-Y') : '-',
                    'end_at' => $r->end_at ? $r->end_at->format('d-m-Y') : '-',
                    'date' => $r->created_at->format('d-m-Y'),
                    'status' => ucfirst($r->status),
                    'reason' => $r->reject_reason ?? $r->cancel_reason ?? '-',
                ];
            });

        return view('admin.request.show', [
            'requests' => $requests,
            'tableRequests' => $tableRequests,
        ]);
    }

    public function approve(Request $req, $req_id)
    {
        $borrowRequest = BorrowRequest::with('transaction', 'user', 'equipment')
            ->where('req_id', $req_id)
            ->firstOrFail();

        // Save old data before making changes
        $oldData = [
            'status' => $borrowRequest->status,
            'start_at' => $borrowRequest->start_at,
            'end_at' => $borrowRequest->end_at,
            'updated_at' => $borrowRequest->updated_at,
        ];

        $validated = $req->validate([
            'start_at' => ['nullable','date'],
            'end_at' => ['nullable','date','after_or_equal:start_at'],
        ]);

        // Validate business days for admin-approved dates
        if (array_key_exists('start_at', $validated) && $validated['start_at']) {
            $startDate = \Carbon\Carbon::parse($validated['start_at']);
            if ($startDate->isWeekend()) {
                return redirect()->back()
                    ->withErrors(['start_at' => 'วันเริ่มต้นต้องเป็นวันจันทร์-ศุกร์เท่านั้น'])
                    ->withInput();
            }
            $borrowRequest->start_at = $validated['start_at'];
        }
        if (array_key_exists('end_at', $validated) && $validated['end_at']) {
            $endDate = \Carbon\Carbon::parse($validated['end_at']);
            if ($endDate->isWeekend()) {
                return redirect()->back()
                    ->withErrors(['end_at' => 'วันสิ้นสุดต้องเป็นวันจันทร์-ศุกร์เท่านั้น'])
                    ->withInput();
            }
            $borrowRequest->end_at = $validated['end_at'];
        }

        $borrowRequest->status = 'approved';
        $borrowRequest->pickup_deadline = now()->addDays(3); // 3 days to pickup
        $borrowRequest->save();

        // Save new data after changes
        $newData = [
            'status' => $borrowRequest->status,
            'start_at' => $borrowRequest->start_at,
            'end_at' => $borrowRequest->end_at,
            'updated_at' => $borrowRequest->updated_at,
        ];


        // Log the action
        Log::create([
            'admin_id' => Auth::id() ?? 1,
            'action' => 'approve',
            'target_type' => 'borrow_request',
            'target_id' => $borrowRequest->id,
            'target_name' => $borrowRequest->req_id,
            'description' => "Approved borrow request: {$borrowRequest->req_id}",
            'module' => 'borrow_request',
            'severity' => 'info',
            'old_values' => $oldData,
            'new_values' => $newData
        ]);

        $user = $borrowRequest->user;
        if ($user) {
            $user->notify(new BorrowRequestApproved($borrowRequest));
        }

        $this->clearDashboardCache($borrowRequest);

        return redirect()->route('admin.requests.show', $borrowRequest->req_id)
            ->with('success', 'Request approved. Allowed dates saved.');
    }

    public function reject(Request $req, $req_id)
    {
        $request = BorrowRequest::where('req_id', $req_id)->firstOrFail();
        
        // Save old data before making changes
        $oldData = [
            'status' => $request->status,
            'reject_reason' => $request->reject_reason,
            'updated_at' => $request->updated_at,
        ];

        $request->status = 'rejected';
        $request->reject_reason = $req->input('reason');
        $request->save();

        // Make equipment available again
        $equipment = $request->equipment;
        if ($equipment) {
            $equipment->status = 'available';
            $equipment->save();
        }

        // Save new data after changes
        $newData = [
            'status' => $request->status,
            'reject_reason' => $request->reject_reason,
            'updated_at' => $request->updated_at,
        ];


        // Log the action
        Log::create([
            'admin_id' => Auth::id() ?? 1,
            'action' => 'reject',
            'target_type' => 'borrow_request',
            'target_id' => $request->id,
            'target_name' => $request->req_id,
            'description' => "Rejected borrow request: {$request->req_id}",
            'module' => 'borrow_request',
            'severity' => 'warning',
            'old_values' => $oldData,
            'new_values' => $newData
        ]);

        $user = $request->user;
        if ($user) {
            $user->notify(new BorrowRequestRejected($request));
        }

        $this->clearDashboardCache($request);

        return redirect()->route('admin.requests.index')
            ->with('success', 'Request rejected successfully.');
    }

public function update(Request $req, $req_id)
{
    // ✅ 1. Load the borrow request with its related transaction
    $borrowRequest = BorrowRequest::with('transaction')
        ->where('req_id', $req_id)
        ->firstOrFail();

    // ✅ 2. Validate penalty and notes (no need to validate date/time anymore)
    $validated = $req->validate([
        'penalty_amount' => ['nullable', 'numeric', 'min:0'],
        'notes' => ['nullable', 'string', 'max:1000'],
    ]);

    // ✅ 3. Prevent updating if not in allowed status
    if (!in_array($borrowRequest->status, ['approved', 'check_out'])) {
        return back()->withErrors([
            'status' => 'ต้องอยู่ในสถานะ "อนุมัติ" หรือ "เช็คเอาท์" เท่านั้นจึงจะสามารถบันทึกได้'
        ])->withInput();
    }

    // ✅ 4. Get existing transaction or create a new one
    $transaction = $borrowRequest->transaction ?? new BorrowTransaction();
    $transaction->borrow_requests_id = $borrowRequest->id;

    // ✅ 5. Automatically set current timestamp
    if ($borrowRequest->status === 'approved') {
        // When the item is being picked up
        $transaction->checked_out_at = Carbon::now();
        $borrowRequest->status = 'check_out';
        
        // Send notification to user
        $user = $borrowRequest->user;
        if ($user) {
            $user->notify(new \App\Notifications\EquipmentCheckedOut($borrowRequest));
        }
    } elseif ($borrowRequest->status === 'check_out') {
        // When the item is being returned
        $transaction->checked_in_at = Carbon::now();
        $borrowRequest->status = 'check_in';
        
        // Make equipment available again
        $equipment = $borrowRequest->equipment;
        if ($equipment) {
            $equipment->status = 'available';
            $equipment->save();
        }
        
        // Send notification to user
        $user = $borrowRequest->user;
        if ($user) {
            $user->notify(new \App\Notifications\EquipmentCheckedIn($borrowRequest));
        }
    }

    // ✅ 6. Save penalty and notes (if provided)
    if (array_key_exists('penalty_amount', $validated)) {
        $transaction->penalty_amount = $validated['penalty_amount'];
    }

    if (array_key_exists('notes', $validated)) {
        $transaction->notes = $validated['notes'];
    }

    // ✅ 7. Save both models
    $transaction->save();
    $borrowRequest->save();

    // ✅ 8. Clear dashboard cache (your existing helper)
    $this->clearDashboardCache($borrowRequest);

    // ✅ 9. Redirect with success message
    return redirect()
        ->route('admin.requests.index')
        ->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
}

}
