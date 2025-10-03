<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BorrowRequest;
use App\Models\Equipment;
use App\Models\User;
use App\Notifications\BorrowRequestAutoRejected;
use Illuminate\Support\Facades\Cache;

class AutoRejectExpiredPendingRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'requests:auto-reject';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto reject pending requests that have been pending for more than 3 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
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
    }
}
