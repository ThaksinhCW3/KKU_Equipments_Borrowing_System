<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\BorrowRequestController;
use App\Http\Controllers\Admin\ReportExportController;
use App\Http\Controllers\Admin\VerificationController as AdminVerificationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Borrowers\BorrowerCtrl;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/equipments', [HomeController::class, 'index'])->name('equipments.index');
Route::get('/equipments/search', [BorrowerCtrl::class, 'search']);
Route::get('/equipments/all', [BorrowerCtrl::class, 'getAllEquipment']);
Route::get('/equipments/{code}', [BorrowerCtrl::class, 'show'])->name('equipments.show');

// Authenticated routes
Route::middleware('auth')->group(function () {

    // Notifications
    Route::post('/notifications/mark-read/{id}', [NotificationController::class, 'markRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);

    // Borrower area
    Route::prefix('/borrower')->group(function () {
        Route::post('/borrow_request', [BorrowerCtrl::class, 'myRequests'])->name('borrower.borrow_request');
        Route::get('/myrequest', [BorrowerCtrl::class, 'myreq'])->name('borrower.equipments.myreq');
        Route::get('/myrequest/paginated', [BorrowerCtrl::class, 'myreqPaginated'])->name('borrower.equipments.myreq.paginated');
        Route::get('/reqdetail/{req_id}', [BorrowerCtrl::class, 'reqdetail'])->name('borrower.equipments.reqdetail');
        // fixed path (prefix already includes /borrower)
        Route::patch('/requests/{id}/cancel', [BorrowerCtrl::class, 'cancel'])->name('borrower.requests.cancel');
    });

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Verification routes
    Route::get('/profile/verification', [VerificationController::class, 'index'])->name('verification.index');
    Route::post('/verification', [VerificationController::class, 'store'])->name('verification.store');
    Route::put('/verification', [VerificationController::class, 'update'])->name('verification.update');

    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        // Equipment management
        Route::prefix('admin/equipment')->group(function () {
            Route::post('/store', [EquipmentController::class, 'store'])->name('admin.equipment.store');
            Route::put('/update/{id}', [EquipmentController::class, 'update'])->name('admin.equipment.update');
            Route::delete('/destroy/{id}', [EquipmentController::class, 'destroy'])->name('admin.equipment.destroy');
        });

        // Category management
        Route::prefix('admin/category')->group(function () {
            Route::post('/store', [CategoryController::class, 'store'])->name('admin.category.store');
            Route::put('/update/{id}', [CategoryController::class, 'update'])->name('admin.category.update');
            Route::delete('/destroy/{id}', [CategoryController::class, 'destroy'])->name('admin.category.destroy');
        });

        // Reports (subset)
        Route::prefix('admin/report')->group(function () {
            Route::get('/logs', [ReportController::class, 'logReport'])->name('admin.report.logs');
        });
    });

    // Staff and Admin routes
    Route::middleware('role:admin,staff')->group(function () {
        // Admin dashboard
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
        Route::get('/admin/export', [AdminController::class, 'exportCsv'])->name('admin.dashboard.export');

        // Full reports
        Route::prefix('admin/report')->group(function () {
            Route::get('/{type}', [ReportController::class, 'index'])->name('admin.report.index');
            Route::get('/export/{type}', [ReportExportController::class, 'export'])->name('admin.report.export');
        });

        // User management
        Route::prefix('admin/users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('admin.users.index');
        });

        // Requests
        Route::prefix('admin/requests')->group(function () {
            Route::get('/', [BorrowRequestController::class, 'index'])->name('admin.requests.index');
            Route::get('/{req_id}', [BorrowRequestController::class, 'show'])->name('admin.requests.show');
            Route::patch('/{req_id}', [BorrowRequestController::class, 'update'])->name('admin.requests.update');
            Route::match(['post', 'patch'], '/{req_id}/approve', [BorrowRequestController::class, 'approve'])->name('admin.requests.approve');
            Route::post('/{req_id}/reject', [BorrowRequestController::class, 'reject'])->name('admin.requests.reject');
        });

        // Verification management
        Route::prefix('admin/verification')->group(function () {
            Route::get('/', [AdminVerificationController::class, 'index'])->name('admin.verification.index');
            Route::get('/api', [AdminVerificationController::class, 'api'])->name('admin.verification.api');
            Route::get('/{id}', [AdminVerificationController::class, 'show'])->name('admin.verification.show');
            Route::post('/{id}/approve', [AdminVerificationController::class, 'approve'])->name('admin.verification.approve');
            Route::post('/{id}/reject', [AdminVerificationController::class, 'reject'])->name('admin.verification.reject');
        });

        // Category and Equipment index pages
        Route::prefix('admin/category')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('admin.category.index');
        });
        Route::prefix('admin/equipment')->group(function () {
            Route::get('/', [EquipmentController::class, 'index'])->name('admin.equipment.index');
        });
    });
});

// Notification routes for Vue component
Route::middleware('auth')->group(function () {
    // Get user notifications
    Route::get('/api/notifications', function () {
        $user = Auth::user();
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at,
                    'created_at_human' => $notification->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $user->unreadNotifications->count()
        ]);
    });

    // Mark notification as read
    Route::patch('/api/notifications/{id}/read', function ($id) {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);
        
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['error' => 'Notification not found'], 404);
    });

    // Mark all notifications as read
    Route::patch('/api/notifications/mark-all-read', function () {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        
        return response()->json(['success' => true]);
    });
});

require __DIR__ . '/auth.php';
