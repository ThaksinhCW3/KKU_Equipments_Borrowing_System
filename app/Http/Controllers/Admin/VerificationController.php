<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VerificationRequest;
use App\Models\User;
use App\Models\Log;
use App\Notifications\VerificationRequestApproved;
use App\Notifications\VerificationRequestRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    public function index()
    {
        $verificationRequests = VerificationRequest::with(['user', 'processedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.verification.index', compact('verificationRequests'));
    }

    public function api(Request $request)
    {
        $query = VerificationRequest::with(['user', 'processedBy']);

        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        // Map frontend sort fields to database fields
        $sortFieldMap = [
            'name' => 'users.name',
            'email' => 'users.email',
            'status' => 'verification_requests.status',
            'created_at' => 'verification_requests.created_at'
        ];

        $sortField = $sortFieldMap[$sortBy] ?? 'verification_requests.created_at';

        // Handle sorting for user fields (name, email)
        if (in_array($sortBy, ['name', 'email'])) {
            $query->join('users', 'verification_requests.users_id', '=', 'users.id')
                  ->orderBy($sortField, $sortOrder)
                  ->select('verification_requests.*'); // Select only verification_requests columns
        } else {
            $query->orderBy($sortField, $sortOrder);
        }

        $verificationRequests = $query->paginate(10);

        return response()->json($verificationRequests);
    }

    public function show($id)
    {
        $verificationRequest = VerificationRequest::with(['user', 'processedBy'])->findOrFail($id);
        return view('admin.verification.show', compact('verificationRequest'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'reject_note' => 'nullable|string|max:1000',
        ]);

        $verificationRequest = VerificationRequest::findOrFail($id);

        $verificationRequest->update([
            'status' => VerificationRequest::STATUS_APPROVED,
            'reject_note' => $request->reject_note,
            'processed_by' => Auth::id(),
            'process_at' => now(),
        ]);

        // Log the verification approval
        Log::create([
            'admin_id' => Auth::id(),
            'action' => 'verification_request_approved',
            'target_type' => 'verification_request',
            'target_id' => $verificationRequest->id,
            'target_name' => $verificationRequest->user->name,
            'description' => "อนุมัติการยืนยันตัวตนของผู้ใช้ {$verificationRequest->user->name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Clear relevant caches
        \Illuminate\Support\Facades\Cache::flush();

        // Send notification to user
        $verificationRequest->load('processedBy');
        $verificationRequest->user->notify(new VerificationRequestApproved($verificationRequest));

        return redirect()->back()->with('success', 'อนุมัติการยืนยันตัวตนเรียบร้อยแล้ว');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reject_note' => 'required|string|max:1000',
        ]);

        $verificationRequest = VerificationRequest::findOrFail($id);

        $verificationRequest->update([
            'status' => VerificationRequest::STATUS_REJECTED,
            'reject_note' => $request->reject_note,
            'processed_by' => Auth::id(),
            'process_at' => now(),
        ]);

        // Log the verification rejection
        Log::create([
            'admin_id' => Auth::id(),
            'action' => 'verification_request_rejected',
            'target_type' => 'verification_request',
            'target_id' => $verificationRequest->id,
            'target_name' => $verificationRequest->user->name,
            'description' => "ปฏิเสธการยืนยันตัวตนของผู้ใช้ {$verificationRequest->user->name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Clear relevant caches
        \Illuminate\Support\Facades\Cache::flush();

        // Send notification to user
        $verificationRequest->load('processedBy');
        $verificationRequest->user->notify(new VerificationRequestRejected($verificationRequest));

        return redirect()->back()->with('success', 'ปฏิเสธการยืนยันตัวตนเรียบร้อยแล้ว');
    }

    public function banUser(Request $request, $id)
    {
        $request->validate([
            'ban_reason' => 'required|string|max:1000',
        ]);

        $verificationRequest = VerificationRequest::findOrFail($id);

        // Only allow banning of approved verifications
        if ($verificationRequest->status !== VerificationRequest::STATUS_APPROVED) {
            return response()->json(['error' => 'Can only ban users with approved verifications'], 400);
        }

        $user = $verificationRequest->user;
        
        // Ban the user
        $user->ban($request->ban_reason, Auth::id());

        // Revoke verification status when user is banned
        $verificationRequest->update([
            'status' => VerificationRequest::STATUS_REJECTED,
            'reject_note' => "ผู้ใช้ถูกแบน: {$request->ban_reason}",
            'processed_by' => Auth::id(),
            'process_at' => now(),
        ]);

        // Log the user ban
        Log::create([
            'admin_id' => Auth::id(),
            'action' => 'user_banned',
            'target_type' => 'user',
            'target_id' => $user->id,
            'target_name' => $user->name,
            'description' => "แบนผู้ใช้ {$user->name} เนื่องจาก: {$request->ban_reason}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Log the verification revocation
        Log::create([
            'admin_id' => Auth::id(),
            'action' => 'verification_revoked_due_to_ban',
            'target_type' => 'verification_request',
            'target_id' => $verificationRequest->id,
            'target_name' => $user->name,
            'description' => "ยกเลิกการยืนยันตัวตนของผู้ใช้ {$user->name} เนื่องจากถูกแบน",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Clear relevant caches
        \Illuminate\Support\Facades\Cache::flush();

        return response()->json(['success' => true, 'message' => 'แบนผู้ใช้เรียบร้อยแล้ว']);
    }

    public function unbanUser(Request $request, $id)
    {
        $verificationRequest = VerificationRequest::findOrFail($id);

        // Only allow unbanning of banned users
        if (!$verificationRequest->user->isBanned()) {
            return response()->json(['error' => 'User is not banned'], 400);
        }

        $user = $verificationRequest->user;
        
        // Unban the user
        $user->unban();

        // Restore verification status when user is unbanned
        $verificationRequest->update([
            'status' => VerificationRequest::STATUS_APPROVED,
            'reject_note' => null,
            'processed_by' => Auth::id(),
            'process_at' => now(),
        ]);

        // Log the user unban
        Log::create([
            'admin_id' => Auth::id(),
            'action' => 'user_unbanned',
            'target_type' => 'user',
            'target_id' => $user->id,
            'target_name' => $user->name,
            'description' => "ยกเลิกการแบนผู้ใช้ {$user->name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Log the verification restoration
        Log::create([
            'admin_id' => Auth::id(),
            'action' => 'verification_restored_due_to_unban',
            'target_type' => 'verification_request',
            'target_id' => $verificationRequest->id,
            'target_name' => $user->name,
            'description' => "คืนสถานะการยืนยันตัวตนของผู้ใช้ {$user->name} เนื่องจากยกเลิกการแบน",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Clear relevant caches
        \Illuminate\Support\Facades\Cache::flush();

        return response()->json(['success' => true, 'message' => 'ยกเลิกการแบนผู้ใช้เรียบร้อยแล้ว']);
    }
}
