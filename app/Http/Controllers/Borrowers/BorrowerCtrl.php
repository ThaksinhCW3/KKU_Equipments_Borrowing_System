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
use App\Notifications\BorrowRequestCancelled;

use App\Models\Equipment;
use App\Models\Category;
use App\Models\Log;
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
            'equipment.category:id,name',
            'transaction'
        )
            ->where('users_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    });

    return view('equipments.myreq', compact('reQuests'));
}

    public function myreqPaginated(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = Auth::id();
        $page = $request->get('page', 1);
        $perPage = 6; // Load 6 requests per page
        $offset = ($page - 1) * $perPage;
        $sort = $request->get('sort', 'desc'); // Default to desc

        $requests = BorrowRequest::with(
            'equipment:id,code,name,description,categories_id,photo_path',
            'user:id,uid,name,email,phonenumber',
            'equipment.category:id,name',
            'transaction'
        )
            ->where('users_id', $userId)
            ->orderBy('created_at', $sort)
            ->skip($offset)
            ->take($perPage)
            ->get();

        $hasMore = $requests->count() === $perPage;

        return response()->json([
            'requests' => $requests,
            'hasMore' => $hasMore,
            'page' => $page
        ]);
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

        // Log the borrow request cancellation
        Log::create([
            'admin_id' => Auth::id(),
            'action' => 'cancel',
            'target_type' => 'borrow_request',
            'target_id' => $req->id,
            'target_name' => $req->req_id,
            'description' => "ยกเลิกคำขอยืม: {$req->req_id} - เหตุผล: {$reasons}",
            'module' => 'borrow_request',
            'severity' => 'warning',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Make equipment available again
        $equipment = $req->equipment;
        if ($equipment) {
            $equipment->status = 'available';
            $equipment->save();
        }

        // Send notification to all admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new BorrowRequestCancelled($req));
        }

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
            'request_reason_detail' => 'nullable|string|max:500',
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
        $borrowRequest->request_reason_detail = $request->request_reason_detail;
        
        $borrowRequest->save();

        // Log the borrow request creation
        Log::create([
            'admin_id' => Auth::id(),
            'action' => 'create',
            'target_type' => 'borrow_request',
            'target_id' => $borrowRequest->id,
            'target_name' => $borrowRequest->req_id,
            'description' => "สร้างคำขอยืม: {$borrowRequest->req_id} สำหรับอุปกรณ์ {$equipment->name}",
            'module' => 'borrow_request',
            'severity' => 'info',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Make equipment unavailable
        $equipment->status = 'unavailable';
        $equipment->save();

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new BorrowRequestCreated($borrowRequest));
        }
        
        Cache::forget("myreq:" . Auth::id());
        
        return redirect()->route('borrower.equipments.reqdetail', $borrowRequest->req_id)
            ->with('success', 'ส่งคำขอยืมสำเร็จ');
    }
    public function search(Request $request)
    {
        try {
            $q = $request->query('q');
            
            // Get all equipment that match the search criteria
            $allEquipments = Equipment::with('category')
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
                ->get();

            // Group by name and show only one representative item per equipment type
            // Prioritize available items, but show unavailable if no available items exist
            $groupedEquipments = $allEquipments->groupBy('name')->map(function ($equipmentGroup) {
                $availableItem = $equipmentGroup->where('status', 'available')->first();
                $representative = $availableItem ?: $equipmentGroup->first();
                
                // Add quantity calculations
                $equipmentName = $representative->name;
                $representative->available_quantity = Equipment::where('name', $equipmentName)->where('status', 'available')->count();
                $representative->total_quantity = Equipment::where('name', $equipmentName)->count();
                
                return $representative;
            })->values();

            return response()->json(['data' => $groupedEquipments->take(20)]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getAllEquipment()
{
    try {
        $equipments = Cache::remember('all_equipment_list', 1800, function () { 
            // Get all equipment
            $allEquipments = Equipment::with('category')
                ->orderBy('name')
                ->get();

            // Group by name and show only one representative item per equipment type
            // Prioritize available items, but show unavailable if no available items exist
            $groupedEquipments = $allEquipments->groupBy('name')->map(function ($equipmentGroup) {
                $availableItem = $equipmentGroup->where('status', 'available')->first();
                $representative = $availableItem ?: $equipmentGroup->first();
                
                // Add quantity calculations
                $equipmentName = $representative->name;
                $representative->available_quantity = Equipment::where('name', $equipmentName)->where('status', 'available')->count();
                $representative->total_quantity = Equipment::where('name', $equipmentName)->count();
                
                return $representative;
            })->values();

            return $groupedEquipments->take(50);
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
            'equipment:id,code,name,description,categories_id,photo_path,accessories',
            'user:id,uid,name,email,phonenumber',
            'equipment.category:id,name'
        )
            ->where('req_id', $req_id)
            ->get();
    });

    return view('equipments.reqdetail', compact('reQuests'));
}
}


