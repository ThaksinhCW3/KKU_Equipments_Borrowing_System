<?php

namespace App\Http\Controllers;

use App\Models\VerificationRequest;
use App\Models\User;
use App\Notifications\VerificationRequestSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $verificationRequest = $user->verificationRequest;
            return view('profile.verification', compact('verificationRequest'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
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
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/verification'), $fileName);
            $imagePath = '/storage/verification/' . $fileName;
        }

        // Create verification request
        $verificationRequest = VerificationRequest::create([
            'users_id' => $user->id,
            'status' => VerificationRequest::STATUS_PENDING,
            'student_id_image_path' => $imagePath,
        ]);

        // Send notification to all admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new VerificationRequestSubmitted($user));
        }

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
            $oldFileName = basename($verificationRequest->student_id_image_path);
            $oldFilePath = public_path('storage/verification/' . $oldFileName);
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }

        // Handle new file upload
        $imagePath = null;
        if ($request->hasFile('student_id_image')) {
            $file = $request->file('student_id_image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/verification'), $fileName);
            $imagePath = '/storage/verification/' . $fileName;
        }

        // Update verification request
        $verificationRequest->update([
            'status' => VerificationRequest::STATUS_PENDING,
            'student_id_image_path' => $imagePath,
            'admin_note' => null,
            'processed_by' => null,
            'process_at' => null,
        ]);

        return redirect()->back()->with('success', 'อัปเดตคำขอการยืนยันตัวตนเรียบร้อยแล้ว กรุณารอการอนุมัติจากผู้ดูแลระบบ');
    }
}
