<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBannedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isBanned()) {
            // If it's an AJAX request, return JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'banned' => true,
                    'message' => 'คุณถูกแบนจากระบบ',
                    'ban_reason' => Auth::user()->ban_reason
                ], 403);
            }
            
            // For regular requests, redirect to a banned page or show error
            return redirect()->route('banned');
        }

        return $next($request);
    }
}
