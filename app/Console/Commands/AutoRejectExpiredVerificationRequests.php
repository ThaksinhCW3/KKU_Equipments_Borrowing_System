<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VerificationRequest;
use App\Models\User;
use App\Notifications\VerificationRequestAutoRejected;
use Illuminate\Support\Facades\Cache;

class AutoRejectExpiredVerificationRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verification:auto-reject';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto reject pending verification requests that have been pending for more than 3 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting auto-reject process for verification requests...');

        // Find pending verification requests that have been pending for more than 3 days
        $expiredPendingRequests = VerificationRequest::with(['user'])
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
                $request->user->notify(new VerificationRequestAutoRejected($request));
            }

            // Send notification to all admins
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new VerificationRequestAutoRejected($request));
            }

            // Clear cache
            Cache::flush();

            $this->line("Rejected verification request: #{$request->id} - {$request->user->name}");
        }

        $this->info("Auto-reject process completed. {$expiredPendingRequests->count()} verification requests rejected.");
    }
}
