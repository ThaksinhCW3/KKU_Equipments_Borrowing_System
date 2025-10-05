<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Log;
use App\Models\Category;
use App\Models\Equipment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipments = Cache::remember('equipments_with_category', 600, function () {
            return Equipment::with('category')->get()->map(function ($equipment) {
                // Ensure accessories is properly cast to array
                if ($equipment->accessories) {
                    if (is_string($equipment->accessories)) {
                        $equipment->accessories = json_decode($equipment->accessories, true) ?: [];
                    }
                } else {
                    $equipment->accessories = [];
                }
                return $equipment;
            });
        });

        $categories = Cache::remember('all_categories', 600, function () {
            return Category::all();
        });

        return view('admin.equipment.index', compact('equipments', 'categories'));
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                "code" => "required|string|unique:equipments,code|max:10",
                "name" => "required|string",
                "description" => "nullable|string",
                "accessories" => "nullable|string",
                "categories_id" => "required|integer|exists:categories,id",
                "status" => "required|in:available,retired,maintenance",
                "images.*" => "image|mimes:jpg,jpeg,png,webp,gif|max:5120",
                "selectedProfileImage" => "nullable|integer|min:0",
            ]);

            // Convert accessories string to JSON array
            if (!empty($data['accessories'])) {
                // Split by common delimiters and clean up
                $accessoriesArray = array_filter(array_map('trim', preg_split('/[,;\n\r]+/', $data['accessories'])));
                $data['accessories'] = json_encode($accessoriesArray);
            } else {
                $data['accessories'] = json_encode([]);
            }

            $paths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    // Save directly to public/storage/equipments/
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('storage/equipments'), $filename);
                    $paths[] = '/storage/equipments/' . $filename;
                }
            }

            // Handle profile image selection
            $selectedProfileImage = $request->input('selectedProfileImage');
            if ($selectedProfileImage !== null && isset($paths[$selectedProfileImage])) {
                // Move selected image to front of array
                $selectedPath = $paths[$selectedProfileImage];
                unset($paths[$selectedProfileImage]);
                array_unshift($paths, $selectedPath);
            }

            $data['photo_path'] = json_encode($paths);
            $equipment = Equipment::create($data);

            Log::create([
                'admin_id' => Auth::id() ?? 1,
                'action' => 'create',
                'target_type' => 'equipment',
                'target_id' => $equipment->id,
                'target_name' => $equipment->name,
                'description' => "สร้างอุปกรณ์: {$equipment->name} (ID {$equipment->id})",
                'module' => 'equipment',
                'severity' => 'info',
                'user_agent' => $request->userAgent() ?? 'Unknown',
                'ip_address' => $request->ip() ?? 'Unknown'
            ]);


            Cache::forget('equipments_with_category');

            return response()->json([
                "status" => true,
                "message" => "Equipment created successfully",
                "data" => $equipment->load('category')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            $errorMessages = [];
            foreach ($errors as $field => $messages) {
                $errorMessages = array_merge($errorMessages, $messages);
            }
            return response()->json([
                "status" => false,
                "message" => implode(', ', $errorMessages),
                "errors" => $errors
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Equipment creation failed: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                "status" => false,
                "message" => "Server error: " . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $equipment = Equipment::findOrFail($id);

            $validatedData = $request->validate([
                "code" => "nullable|string|max:10",
                "name" => "required|string",
                "description" => "nullable|string",
                "accessories" => "nullable|string",
                "categories_id" => "required|integer|exists:categories,id",
                "status" => "required|in:available,retired,maintenance",
                "images.*" => "image|mimes:jpg,jpeg,png,webp,gif|max:5120", 
                "images_to_delete" => "nullable|array",
                "images_to_delete.*" => "string", 
                "selected_main_identifier" => "nullable|string", 
            ]);

            // Convert accessories string to JSON array
            \Log::info('Update request accessories:', ['accessories' => $request->input('accessories')]);
            \Log::info('Validated accessories:', ['accessories' => $validatedData['accessories'] ?? 'null']);
            
            $accessoriesValue = $validatedData['accessories'] ?? '';
            if (!empty($accessoriesValue)) {
                // Split by common delimiters and clean up
                $accessoriesArray = array_filter(array_map('trim', preg_split('/[,;\n\r]+/', $accessoriesValue)));
                $validatedData['accessories'] = json_encode($accessoriesArray);
                \Log::info('Processed accessories array:', ['accessories' => $accessoriesArray]);
            } else {
                $validatedData['accessories'] = json_encode([]);
            }
            
            \Log::info('Final accessories JSON:', ['accessories' => $validatedData['accessories']]);

            $currentPhotos = json_decode($equipment->photo_path, true) ?? [];

            if ($request->filled('images_to_delete')) {
                $imagesToDelete = $request->input('images_to_delete');
                $currentPhotos = array_values(array_diff($currentPhotos, $imagesToDelete));
                foreach ($imagesToDelete as $imageUrl) {
                    // Delete from public/storage/equipments/
                    $filename = basename($imageUrl);
                    $filePath = public_path('storage/equipments/' . $filename);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }

            $newlyUploadedPhotos = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    // Save directly to public/storage/equipments/
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('storage/equipments'), $filename);
                    $newlyUploadedPhotos[] = [
                        'original_name' => $file->getClientOriginalName(),
                        'path' => '/storage/equipments/' . $filename,
                    ];
                }
            }
            $newPhotoPaths = array_column($newlyUploadedPhotos, 'path');
            $finalPhotoList = array_merge(
                is_array($currentPhotos) ? $currentPhotos : [],
                is_array($newPhotoPaths) ? $newPhotoPaths : []
            );
            
            $mainPhotoIdentifier = $request->input('selected_main_identifier');
            $mainPhotoUrl = null;
            if ($mainPhotoIdentifier) {
                if (in_array($mainPhotoIdentifier, $finalPhotoList)) {
                    $mainPhotoUrl = $mainPhotoIdentifier;
                } else {
                    foreach ($newlyUploadedPhotos as $uploadedPhoto) {
                        if ($uploadedPhoto['original_name'] === $mainPhotoIdentifier) {
                            $mainPhotoUrl = $uploadedPhoto['path'];
                            break;
                        }
                    }
                }
            }
            if ($mainPhotoUrl) {
                $finalPhotoList = array_values(array_diff($finalPhotoList, [$mainPhotoUrl]));
                array_unshift($finalPhotoList, $mainPhotoUrl);
            }

            $updatePayload = $validatedData;
            $updatePayload['photo_path'] = json_encode(array_values($finalPhotoList));
            unset($updatePayload['images'], $updatePayload['images_to_delete'], $updatePayload['selected_main_identifier']);
            
            // Store old values for logging
            $oldValues = [
                'name' => $equipment->name,
                'code' => $equipment->code,
                'description' => $equipment->description,
                'accessories' => $equipment->accessories,
                'categories_id' => $equipment->categories_id,
                'status' => $equipment->status,
                'photo_path' => $equipment->photo_path
            ];

            $equipment->update($updatePayload);

            // Check if status changed and notify users with pending requests
            if (isset($updatePayload['status']) && $updatePayload['status'] !== $oldValues['status']) {
                $this->notifyUsersOnStatusChange($equipment, $oldValues['status'], $updatePayload['status']);
            }

            Cache::forget('equipments_with_category');
            
            $updatedEquipment = $equipment->fresh()->load('category');
            
            // Log the action
            Log::create([
                'admin_id' => Auth::id() ?? 1,
                'action' => 'update',
                'target_type' => 'equipment',
                'target_id' => $equipment->id,
                'target_name' => $equipment->name,
                'description' => "แก้ไขอุปกรณ์: {$equipment->name} (ID {$equipment->code})",
                'module' => 'equipment',
                'severity' => 'info',
                'old_values' => $oldValues,
                'new_values' => $updatePayload,
                'user_agent' => $request->userAgent() ?? 'Unknown',
                'ip_address' => $request->ip() ?? 'Unknown'
            ]);
            
            \Log::info('Updated equipment data:', ['equipment' => $updatedEquipment->toArray()]);

            return response()->json([
                "status" => true,
                "message" => "Equipment updated successfully",
                "data" => $updatedEquipment
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                "status" => false,
                "message" => "Validation error",
                "errors" => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Server error: " . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $equipment = Equipment::findOrFail($id);

        if ($equipment->photo_path) {
            $oldPhotos = json_decode($equipment->photo_path, true) ?? [];
            foreach ($oldPhotos as $photo) {
                $path = str_replace('/storage/', '', $photo);
                Storage::disk('public')->delete($path);
            }
        }

        Log::create([
            'admin_id' => Auth::id() ?? 1,
            'action' => 'delete',
            'target_type' => 'equipment',
            'target_id' => $id,
            'target_name' => $equipment->name,
            'description' => "ลบอุปกรณ์: {$equipment->name} (ID {$equipment->code})",
            'module' => 'equipment',
            'severity' => 'info',
            'user_agent' => request()->userAgent() ?? 'Unknown',
            'ip_address' => request()->ip() ?? 'Unknown'
        ]);

        $equipment->delete();

        Cache::forget('equipments_with_category');

        return response()->json([
            "status" => true,
            "message" => "Equipment deleted successfully"
        ]);
    }

    /**
     * Notify users with pending requests when equipment status changes
     */
    private function notifyUsersOnStatusChange($equipment, $oldStatus, $newStatus)
    {
        // Only notify if status changes to maintenance or unavailable
        if (!in_array($newStatus, ['maintenance', 'unavailable'])) {
            return;
        }

        // Get users with pending requests for this equipment
        $pendingRequests = \App\Models\BorrowRequest::with('user')
            ->where('equipments_id', $equipment->id)
            ->where('status', 'pending')
            ->get();

        foreach ($pendingRequests as $request) {
            if ($request->user) {
                $request->user->notify(new \App\Notifications\EquipmentStatusChanged($equipment, $oldStatus, $newStatus));
            }
        }
    }
}
