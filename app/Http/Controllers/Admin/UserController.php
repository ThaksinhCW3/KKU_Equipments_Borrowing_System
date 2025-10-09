<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return view('admin.user.index', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phonenumber' => 'nullable|string|max:20',
            'uid' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,user'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phonenumber' => $request->phonenumber,
            'uid' => $request->uid,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phonenumber' => 'nullable|string|max:20',
            'uid' => 'required|string|max:255|unique:users,uid,' . $id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:admin,user'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phonenumber' => $request->phonenumber,
            'uid' => $request->uid,
            'role' => $request->role,
        ];

        if ($request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting the last admin
        if ($user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete the last admin user'
                ], 400);
            }
        }
        
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Ban a user
     */
    public function ban(Request $request, string $id)
    {
        $request->validate([
            'ban_reason' => 'required|string|max:1000',
        ]);

        $user = User::findOrFail($id);

        // Prevent banning admin users
        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot ban admin users'
            ], 400);
        }

        // Ban the user
        $user->ban($request->ban_reason, Auth::id());

        // Log the user ban
        Log::create([
            'admin_id' => Auth::id(),
            'action' => 'user_banned',
            'target_type' => 'User',
            'target_id' => $user->id,
            'target_name' => $user->name,
            'description' => "User banned: {$user->name} (ID: {$user->id}) - Reason: {$request->ban_reason}",
            'ip_address' => request()->ip(),
        ]);

        // Clear relevant caches
        \Illuminate\Support\Facades\Cache::flush();

        return response()->json([
            'success' => true,
            'message' => 'แบนผู้ใช้เรียบร้อยแล้ว'
        ]);
    }

    /**
     * Unban a user
     */
    public function unban(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // Check if user is actually banned
        if (!$user->isBanned()) {
            return response()->json([
                'success' => false,
                'message' => 'User is not banned'
            ], 400);
        }

        // Unban the user
        $user->unban();

        // Log the user unban
        Log::create([
            'admin_id' => Auth::id(),
            'action' => 'user_unbanned',
            'target_type' => 'User',
            'target_id' => $user->id,
            'target_name' => $user->name,
            'description' => "User unbanned: {$user->name} (ID: {$user->id})",
            'ip_address' => request()->ip(),
        ]);

        // Clear relevant caches
        \Illuminate\Support\Facades\Cache::flush();

        return response()->json([
            'success' => true,
            'message' => 'ยกเลิกการแบนผู้ใช้เรียบร้อยแล้ว'
        ]);
    }
}
