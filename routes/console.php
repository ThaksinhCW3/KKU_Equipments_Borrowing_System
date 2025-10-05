<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Models\BorrowRequest;
use App\Models\Equipment;
use App\Models\User;
use App\Notifications\BorrowReturnReminder;
use App\Notifications\BorrowRequestAutoCancelled;
use App\Notifications\BorrowRequestAutoRejected;
use Illuminate\Support\Facades\Cache;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send return reminders: one day before end_at and at 12:00 on end_at
Artisan::command('reminders:returns', function () {
    $now = now();
    $today = $now->toDateString();
    $tomorrow = $now->copy()->addDay()->toDateString();

    // Only consider active/approved/checked out requests
    $activeStatuses = ['approved', 'check_out', 'pending'];

    // Due tomorrow (any time of day)
    $dueTomorrow = BorrowRequest::with(['user', 'equipment'])
        ->whereDate('end_at', $tomorrow)
        ->whereIn('status', $activeStatuses)
        ->get();

    foreach ($dueTomorrow as $req) {
        if ($req->user) {
            $req->user->notify(new BorrowReturnReminder($req, 'due_tomorrow'));
        }
    }

    // Due today at 12:00 (noon). We run only around noon; rely on scheduler to run at 12:00
    if ((int) $now->format('H') === 12) {
        $dueToday = BorrowRequest::with(['user', 'equipment'])
            ->whereDate('end_at', $today)
            ->whereIn('status', $activeStatuses)
            ->get();

        foreach ($dueToday as $req) {
            if ($req->user) {
                $req->user->notify(new BorrowReturnReminder($req, 'due_today'));
            }
        }
    }

    $this->info('Return reminders processed at ' . $now);
})->purpose('Send reminders for equipment return deadlines');

// Auto cancel approved requests that have passed pickup deadline
Artisan::command('requests:auto-cancel', function () {
    $this->info('Starting auto-cancel process...');

    // Find approved requests that have passed pickup deadline
    $expiredRequests = BorrowRequest::with(['user', 'equipment'])
        ->where('status', 'approved')
        ->where('pickup_deadline', '<', now())
        ->get();

    if ($expiredRequests->isEmpty()) {
        $this->info('No expired requests found.');
        return;
    }

    $this->info("Found {$expiredRequests->count()} expired requests.");

    foreach ($expiredRequests as $request) {
        // Update request status
        $request->status = 'cancelled';
        $request->cancel_reason = 'ไม่มารับอุปกรณ์ภายใน 3 วัน';
        $request->save();

        // Make equipment available again
        $equipment = $request->equipment;
        if ($equipment) {
            $equipment->status = 'available';
            $equipment->save();
        }

        // Send notification to user
        if ($request->user) {
            $request->user->notify(new BorrowRequestAutoCancelled($request));
        }

        // Send notification to all admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new BorrowRequestAutoCancelled($request));
        }

        // Clear cache
        Cache::forget("myreq:{$request->users_id}");
        Cache::forget("reqdetail:{$request->req_id}");

        $this->line("Cancelled request: #{$request->req_id} - {$request->equipment->name}");
    }

    $this->info("Auto-cancel process completed. {$expiredRequests->count()} requests cancelled.");
})->purpose('Auto cancel approved requests that have passed pickup deadline');

// Auto reject pending requests that have been pending for more than 3 days
Artisan::command('requests:auto-reject', function () {
    $this->info('Starting auto-reject process...');

    // Find pending requests that have been pending for more than 3 days
    $expiredPendingRequests = BorrowRequest::with(['user', 'equipment'])
        ->where('status', 'pending')
        ->where('created_at', '<', now()->subDays(3))
        ->get();

    if ($expiredPendingRequests->isEmpty()) {
        $this->info('No expired pending requests found.');
        return;
    }

    $this->info("Found {$expiredPendingRequests->count()} expired pending requests.");

    foreach ($expiredPendingRequests as $request) {
        // Update request status
        $request->status = 'rejected';
        $request->reject_reason = 'ไม่มีการดำเนินการจากผู้ดูแลภายใน 3 วัน';
        $request->save();

        // Make equipment available again
        $equipment = $request->equipment;
        if ($equipment) {
            $equipment->status = 'available';
            $equipment->save();
        }

        // Send notification to user
        if ($request->user) {
            $request->user->notify(new BorrowRequestAutoRejected($request));
        }

        // Send notification to all admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new BorrowRequestAutoRejected($request));
        }

        // Clear cache
        Cache::forget("myreq:{$request->users_id}");
        Cache::forget("reqdetail:{$request->req_id}");

        $this->line("Rejected request: #{$request->req_id} - {$request->equipment->name}");
    }

    $this->info("Auto-reject process completed. {$expiredPendingRequests->count()} requests rejected.");
})->purpose('Auto reject pending requests that have been pending for more than 3 days');

// Auto reject pending verification requests that have been pending for more than 3 days
Artisan::command('verification:auto-reject', function () {
    $this->info('Starting auto-reject process for verification requests...');

    // Find pending verification requests that have been pending for more than 3 days
    $expiredPendingRequests = \App\Models\VerificationRequest::with(['user'])
        ->where('status', 'pending')
        ->where('created_at', '<', now()->subDays(3))
        ->get();

    if ($expiredPendingRequests->isEmpty()) {
        $this->info('No expired pending verification requests found.');
        return;
    }

    $this->info("Found {$expiredPendingRequests->count()} expired pending verification requests.");

    foreach ($expiredPendingRequests as $request) {
        // Update request status
        $request->status = 'rejected';
        $request->reject_note = 'ไม่มีการดำเนินการจากผู้ดูแลภายใน 3 วัน';
        $request->processed_by = 1; // System user
        $request->process_at = now();
        $request->save();

        // Send notification to user
        if ($request->user) {
            $request->load('processedBy');
            $request->user->notify(new \App\Notifications\VerificationRequestAutoRejected($request));
        }

        // Send notification to all admins
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $request->load('processedBy');
            $admin->notify(new \App\Notifications\VerificationRequestAutoRejected($request));
        }

        // Clear cache
        \Illuminate\Support\Facades\Cache::flush();

        $this->line("Rejected verification request: #{$request->id} - {$request->user->name}");
    }

    $this->info("Auto-reject process completed. {$expiredPendingRequests->count()} verification requests rejected.");
})->purpose('Auto reject pending verification requests that have been pending for more than 3 days');
