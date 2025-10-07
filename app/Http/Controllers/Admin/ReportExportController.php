<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Log;

use App\Exports\MultiFilteredExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportExportController extends Controller
{
    public function export(Request $request, string $type)
    {
        // Log the export initiation
        Log::create([
            'admin_id' => Auth::id() ?? 1,
            'action' => 'export_initiated',
            'target_type' => 'system',
            'target_id' => 0,
            'target_name' => ucfirst($type) . ' Export',
            'description' => "เริ่มส่งออกข้อมูล {$type}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        try {
            $fileName = "{$type}-report-" . now()->format('Y-m-d-H-i-s') . ".xlsx";
            
            // Log successful export
            $response = Excel::download(new MultiFilteredExport($request, $type), $fileName);
            
            // Log export completion
            Log::create([
                'admin_id' => Auth::id() ?? 1,
                'action' => 'export_completed',
                'target_type' => 'system',
                'target_id' => 0,
                'target_name' => ucfirst($type) . ' Export',
                'description' => "ส่งออกข้อมูล {$type} เสร็จสิ้น: {$fileName}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            return $response;
            
        } catch (\Exception $e) {
            // Log export failure
            Log::create([
                'admin_id' => Auth::id() ?? 1,
                'action' => 'export_failed',
                'target_type' => 'system',
                'target_id' => 0,
                'target_name' => ucfirst($type) . ' Export',
                'description' => "ส่งออกข้อมูล {$type} ล้มเหลว: " . $e->getMessage(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            throw $e;
        }
    }
}
