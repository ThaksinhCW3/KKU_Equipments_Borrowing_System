<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VerificationRequest;
use App\Models\User;
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

    public function show($id)
    {
        $verificationRequest = VerificationRequest::with(['user', 'processedBy'])->findOrFail($id);
        return view('admin.verification.show', compact('verificationRequest'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $verificationRequest = VerificationRequest::findOrFail($id);

        $verificationRequest->update([
            'status' => VerificationRequest::STATUS_APPROVED,
            'admin_note' => $request->admin_note,
            'processed_by' => Auth::id(),
            'process_at' => now(),
        ]);

        return redirect()->back()->with('success', 'อนุมัติการยืนยันตัวตนเรียบร้อยแล้ว');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_note' => 'required|string|max:1000',
        ]);

        $verificationRequest = VerificationRequest::findOrFail($id);

        $verificationRequest->update([
            'status' => VerificationRequest::STATUS_REJECTED,
            'admin_note' => $request->admin_note,
            'processed_by' => Auth::id(),
            'process_at' => now(),
        ]);

        return redirect()->back()->with('success', 'ปฏิเสธการยืนยันตัวตนเรียบร้อยแล้ว');
    }
}
