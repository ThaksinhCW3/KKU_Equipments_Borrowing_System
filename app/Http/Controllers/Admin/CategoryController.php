<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    //index
    public function index()
    {
        $categories = Category::withCount('equipments')->get();
        return view('admin.category.index', compact('categories'));
    }

    //store logic
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::create($validated);

        // Log the action
        Log::create([
            'admin_id' => Auth::id() ?? 1,
            'action' => 'create',
            'target_type' => 'category',
            'target_id' => $category->id,
            'target_name' => $category->name,
            'description' => "สร้างหมวดหมู่: {$category->name} (ID {$category->id})",
            'module' => 'equipment',
            'severity' => 'info',
            'user_agent' => request()->userAgent() ?? 0,
            'ip_address' => request()->ip()
        ]);

        // Clear relevant caches
        \Illuminate\Support\Facades\Cache::forget('all_categories');
        \Illuminate\Support\Facades\Cache::flush(); // Clear all cache to ensure fresh data

        return response()->json([
            'status' => true,
            'category' => $category,
            'message' => 'Category created successfully'
        ]);
    }

    //Update logic
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::findOrFail($id);
        
        // Store old values for logging
        $oldValues = [
            'name' => $category->name
        ];
        
        $category->update($validated);

        // Log the action
        Log::create([
            'admin_id' => Auth::id() ?? 1,
            'action' => 'update',
            'target_type' => 'category',
            'target_id' => $category->id,
            'target_name' => $category->name,
            'description' => "แก้ไขหมวดหมู่: {$category->name} (ID {$category->id})",
            'module' => 'equipment',
            'severity' => 'info',
            'old_values' => $oldValues,
            'new_values' => $validated,
            'user_agent' => request()->userAgent() ?? 0,
            'ip_address' => request()->ip()
        ]);

        // Clear relevant caches
        \Illuminate\Support\Facades\Cache::forget('all_categories');
        \Illuminate\Support\Facades\Cache::flush(); // Clear all cache to ensure fresh data

        return response()->json([
            'status' => true,
            'category' => $category,
            'message' => 'Category updated successfully'
        ]);
    }

    //delete logic
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        
        // Log the action before deletion
        Log::create([
            'admin_id' => Auth::id() ?? 1,
            'action' => 'delete',
            'target_type' => 'category',
            'target_id' => $category->id,
            'target_name' => $category->name,
            'description' => "ลบหมวดหมู่: {$category->name} (ID {$category->id})",
            'module' => 'equipment',
            'severity' => 'warning',
            'user_agent' => request()->userAgent() ?? 0,
            'ip_address' => request()->ip()
        ]);
        
        $category->delete();

        // Clear relevant caches
        \Illuminate\Support\Facades\Cache::forget('all_categories');
        \Illuminate\Support\Facades\Cache::flush(); // Clear all cache to ensure fresh data

        return response()->json(
            [
                "status" => true,
                "message" => "Category deleted successfully"
            ]
        );
    }
}
