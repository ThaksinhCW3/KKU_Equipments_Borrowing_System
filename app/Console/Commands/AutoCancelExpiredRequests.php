<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BorrowRequest;
use App\Models\Equipment;
use App\Models\User;
use App\Notifications\BorrowRequestAutoCancelled;
use Illuminate\Support\Facades\Cache;

class AutoCancelExpiredRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'requests:auto-cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto cancel approved requests that have passed pickup deadline';

    /**
     * Execute the console command.
     */
    public function handle()
    {
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
    }
}
