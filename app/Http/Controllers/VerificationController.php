<?php

namespace App\Http\Controllers;

use App\Models\VerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    public function index()
    {
        $verificationRequest = Auth::user()->verificationRequest;
        return view('profile.verification', compact('verificationRequest'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $user = Auth::user();

        // Check if user already has a verification request
        if ($user->verificationRequest) {
            return redirect()->back()->with('error', 'คุณได้ส่งคำขอการยืนยันตัวตนแล้ว กรุณารอการอนุมัติ');
        }

        // Handle file upload
        $imagePath = null;
        if ($request->hasFile('student_id_image')) {
            $file = $request->file('student_id_image');
            $imagePath = $file->store('verification', 'public');
        }

        // Create verification request
        VerificationRequest::create([
            'users_id' => $user->id,
            'status' => VerificationRequest::STATUS_PENDING,
            'student_id_image_path' => $imagePath ? '/storage/' . $imagePath : null,
        ]);

        return redirect()->back()->with('success', 'ส่งคำขอการยืนยันตัวตนเรียบร้อยแล้ว กรุณารอการอนุมัติจากผู้ดูแลระบบ');
    }

    public function update(Request $request)
    {
        $request->validate([
            'student_id_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $user = Auth::user();
        $verificationRequest = $user->verificationRequest;

        if (!$verificationRequest) {
            return redirect()->back()->with('error', 'ไม่พบคำขอการยืนยันตัวตน');
        }

        // Delete old image if exists
        if ($verificationRequest->student_id_image_path) {
            $oldPath = str_replace('/storage/', '', $verificationRequest->student_id_image_path);
            Storage::disk('public')->delete($oldPath);
        }

        // Handle new file upload
        $imagePath = null;
        if ($request->hasFile('student_id_image')) {
            $file = $request->file('student_id_image');
            $imagePath = $file->store('verification', 'public');
        }

        // Update verification request
        $verificationRequest->update([
            'status' => VerificationRequest::STATUS_PENDING,
            'student_id_image_path' => $imagePath ? '/storage/' . $imagePath : null,
            'admin_note' => null,
            'processed_by' => null,
            'process_at' => null,
        ]);

        return redirect()->back()->with('success', 'อัปเดตคำขอการยืนยันตัวตนเรียบร้อยแล้ว กรุณารอการอนุมัติจากผู้ดูแลระบบ');
    }
}
