<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use App\Models\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'exists:users,email'],
            'password' => ['required', Password::defaults()],
        ], 
        [
            'email.required' => 'กรุณากรอกอีเมล',
            'email.string'   => 'อีเมลต้องเป็นตัวอักษร',
            'email.email'    => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.exists' => 'ไม่พบอีเมลนี้ในระบบ',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.min' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร',
            'password.confirmed' => 'รหัสผ่านไม่ถูกต้อง',
            'password.regex' => 'รหัสผ่านต้องมีตัวอักษรใหญ่ (A-Z) อย่างน้อยหนึ่งตัว',
        ]);

        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();
        $defaultRoute = route('home', absolute: false);

        // Log login action
        Log::create([
            'admin_id' => $user->id,
            'action' => 'login',
            'target_type' => 'user',
            'target_id' => $user->id,
            'target_name' => $user->name,
            'description' => "User logged in: {$user->name} ({$user->email})",
            'module' => 'authentication',
            'severity' => 'info',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        if (in_array($user->role, ['admin', 'staff'])) {
            $defaultRoute = route('admin.index', absolute: false);
        }

        return redirect()->intended($defaultRoute);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        // Log logout action before logout
        if ($user) {
            Log::create([
                'admin_id' => $user->id,
                'action' => 'logout',
                'target_type' => 'user',
                'target_id' => $user->id,
                'target_name' => $user->name,
                'description' => "User logged out: {$user->name} ({$user->email})",
                'module' => 'authentication',
                'severity' => 'info',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
