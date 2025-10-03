<?php

namespace App\Http\Controllers\Borrowers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BorrowRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Categories;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use App\Notifications\BorrowRequestCreated;

use App\Models\Equipment;
use App\Models\Category;
use Carbon\Carbon;

class BorrowerCtrl extends Controller
{
    public function show($code)
    {
        $equipment = Cache::remember("equipment:$code", 600, function () use ($code) {
            return Equipment::where('code', $code)->firstOrFail();
        });

        $hasBorrowed = false;
        if (Auth::check()) {
            $hasBorrowed = BorrowRequest::where('users_id', Auth::id())
                ->where('equipments_id', $equipment->id)
                ->whereIn('status', ['pending', 'approved', 'check_out'])
                ->exists();
        }

        $bookings = BorrowRequest::where('equipments_id', $equipment->id)
            ->whereIn('status', ['pending', 'approved', 'check_out'])
            ->orderBy('start_at')
            ->get();

        $currentDate = Carbon::now()->toDateString();
        return view('equipments.show', compact('equipment', 'bookings', 'hasBorrowed', 'currentDate'));
    }

    public function myreq()
{
    if (!Auth::check()) {
        return redirect()->back()->with('showLoginConfirm', true);
    }

    $userId = Auth::id();

    $reQuests = Cache::remember("myreq:{$userId}", 600, function () use ($userId) {
        return BorrowRequest::with(
            'equipment:id,code,name,description,categories_id,photo_path',
            'user:id,uid,name,email,phonenumber',
            'equipment.category:id,name'
        )
            ->where('users_id', $userId)
            ->get();
    });

    return view('equipments.myreq', compact('reQuests'));
}

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancel_reason' => 'required|array|min:1',
        ]);

        $reasons = implode(', ', $request->cancel_reason);

        $req = BorrowRequest::findOrFail($id);
        $req->status = 'cancelled';
        $req->cancel_reason = $reasons;
        $req->save();

        Cache::forget("myreq:{$req->users_id}");
        Cache::forget("reqdetail:{$req->req_id}");

        return redirect()->back()->with('success', 'คำขอถูกยกเลิกแล้ว');
    }

    public function myRequests(Request $request)
    
    {
    
        if (!Auth::check()) {
            return redirect()->back()->with('showLoginConfirm', true);
        }
            $start = $request->start_at ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->start_at)->format('Y-m-d') : null;
                $end = $request->end_at ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->end_at)->format('Y-m-d') : null;

    $request->merge([
        'start_at' => $start,
        'end_at' => $end,
    ]);
        $request->validate([
            'start_at' => 'required|date|after_or_equal:today',
            'end_at' => 'required|date|after_or_equal:start_at',
            'equipments_id' => 'required|exists:equipments,id',
            'request_reason' => 'required|string|max:255',
        ]);

        // Validate business days only (Monday-Friday)
        $startDate = \Carbon\Carbon::parse($request->start_at);
        $endDate = \Carbon\Carbon::parse($request->end_at);
        
        if ($startDate->isWeekend()) {
            return redirect()->back()
                ->withErrors(['start_at' => 'วันเริ่มต้นต้องเป็นวันจันทร์-ศุกร์เท่านั้น'])
                ->withInput();
        }
        
        if ($endDate->isWeekend()) {
            return redirect()->back()
                ->withErrors(['end_at' => 'วันสิ้นสุดต้องเป็นวันจันทร์-ศุกร์เท่านั้น'])
                ->withInput();
        }
        // Check if user is verified
        $user = Auth::user();
        if (!$user->verificationRequest || $user->verificationRequest->status !== 'approved') {
            return redirect()->back()
                ->with('error', 'กรุณายืนยันตัวตนก่อนยืมอุปกรณ์')
                ->with('verification_required', true);
        }

        $equipment = Equipment::findOrFail($request->equipments_id);
        if (in_array($equipment->status, ['maintenance'])) {
            return redirect()->back()
                ->with('error', 'อุปกรณ์นี้ไม่สามารถยืมได้ เพราะสถานะคือ ' . $equipment->status);
        }

        $start = $request->start_at;
        $end = $request->end_at;

        $overlapExists = BorrowRequest::where('equipments_id', $equipment->id)
            ->whereIn('status', ['pending', 'approved', 'check_out'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_at', [$start, $end])
                    ->orWhereBetween('end_at', [$start, $end])
                    ->orWhere(function ($query2) use ($start, $end) {
                        $query2->where('start_at', '<=', $start)
                            ->where('end_at', '>=', $end);
                    });
            })
            ->exists();

        if ($overlapExists) {
            return redirect()->back()->with('error', 'ช่วงเวลาที่เลือกถูกจองแล้ว กรุณาเลือกวันอื่น');
        }

        $borrowRequest = new BorrowRequest();
        $borrowRequest->users_id = Auth::id();
        $borrowRequest->equipments_id = $equipment->id;
        $borrowRequest->start_at = $start;
        $borrowRequest->end_at = $end;
        $borrowRequest->status = 'pending';
        $borrowRequest->request_reason = $request->request_reason;
        
        $borrowRequest->save();

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new BorrowRequestCreated($borrowRequest));
        }
            Cache::forget("myreq:" . Auth::id());
        return redirect()->back()
            ->with('success', 'ส่งคำขอยืมสำเร็จ');
    }
    public function search(Request $request)
    {
        try {
            $q = $request->query('q');
            $equipments = Equipment::with('category')
                ->when($q, function($query) use ($q) {
                    $query->where(function($subQuery) use ($q) {
                        $subQuery->where('name', 'like', "%$q%")
                            ->orWhere('code', 'like', "%$q%")
                            ->orWhere('status', 'like', "%$q%")
                            ->orWhereHas('category', function($catQuery) use ($q) {
                                $catQuery->where('name', 'like', "%$q%");
                            });
                    });
                })
                ->orderBy('name')
                ->limit(20)
                ->get();

            return response()->json(['data' => $equipments]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getAllEquipment()
{
    try {
        $equipments = Cache::remember('all_equipment_list', 1800, function () { 
            return Equipment::with('category')
                ->orderBy('name')
                ->limit(50)
                ->get();
        });

        return response()->json(['data' => $equipments]);
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
}
    public function reqdetail($req_id)
{
    if (!Auth::check()) {
        return redirect()->back()->with('showLoginConfirm', true);
    }
    $reQuests = Cache::remember("reqdetail:{$req_id}", 600, function () use ($req_id) { 
        return BorrowRequest::with(
            'equipment:id,code,name,description,categories_id,photo_path',
            'user:id,uid,name,email,phonenumber',
            'equipment.category:id,name'
        )
            ->where('req_id', $req_id)
            ->get();
    });

    return view('equipments.reqdetail', compact('reQuests'));
}
}


