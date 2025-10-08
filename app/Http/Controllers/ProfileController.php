<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the user's profile page.
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'uid' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users')->ignore($user->id),
            ],
            'phonenumber' => [
                'required',
                'string',
                'max:15',
                'regex:/^[0-9\-\+\(\)\s]+$/',
            ],
        ], [
            'name.required' => 'กรุณากรอกชื่อ',
            'name.string' => 'ชื่อต้องเป็นตัวอักษร',
            'name.max' => 'ชื่อต้องไม่เกิน 255 ตัวอักษร',
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',
            'uid.required' => 'กรุณากรอกรหัสนักศึกษา/รหัสพนักงาน',
            'uid.unique' => 'รหัสนักศึกษา/รหัสพนักงานนี้ถูกใช้งานแล้ว',
            'phonenumber.required' => 'กรุณากรอกเบอร์โทรศัพท์',
            'phonenumber.regex' => 'รูปแบบเบอร์โทรศัพท์ไม่ถูกต้อง',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'uid' => $request->uid,
            'phonenumber' => $request->phonenumber,
        ]);

        return redirect()->route('profile.show')->with('success', 'อัปเดตข้อมูลส่วนตัวเรียบร้อยแล้ว');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
        ], [
            'current_password.required' => 'กรุณากรอกรหัสผ่านปัจจุบัน',
            'current_password.current_password' => 'รหัสผ่านปัจจุบันไม่ถูกต้อง',
            'password.required' => 'กรุณากรอกรหัสผ่านใหม่',
            'password.confirmed' => 'การยืนยันรหัสผ่านไม่ตรงกัน',
            'password.min' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร',
            'password.regex' => 'รหัสผ่านต้องมีตัวอักษรเล็ก ตัวอักษรใหญ่ และตัวเลขอย่างน้อย 1 ตัว',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.show')->with('success', 'อัปเดตรหัสผ่านเรียบร้อยแล้ว');
    }
}
