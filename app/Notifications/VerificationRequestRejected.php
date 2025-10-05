<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationRequestRejected extends Notification implements ShouldQueue
{
    use Queueable;

    protected $verificationRequest;

    public function __construct($verificationRequest)
    {
        $this->verificationRequest = $verificationRequest;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        try {
            return (new MailMessage)
                ->subject('การยืนยันตัวตนของคุณถูกปฏิเสธ')
                ->greeting("สวัสดี {$notifiable->name}")
                ->line("การยืนยันตัวตนของคุณถูกปฏิเสธ")
                ->line('สถานะ: ถูกปฏิเสธ')
                ->line('เหตุผล: ' . ($this->verificationRequest->reject_note ?: '-'))
                ->when($this->verificationRequest->process_at, function ($mail) {
                    return $mail->line("วันที่ปฏิเสธ: " . $this->verificationRequest->process_at->format('d/m/Y'));
                })
                ->line("กรุณาอัปโหลดรูปบัตรนักศึกษาใหม่และส่งคำขอใหม่")
                ->action('ส่งคำขอใหม่', route('profile/verification'));
        } catch (\Exception $e) {
            \Log::error('VerificationRequestRejected mail failed: ' . $e->getMessage());
            return null;
        }
    }

    public function toDatabase($notifiable)
    {
        try {
            return [
                'verification_id' => $this->verificationRequest->id,
                'status' => 'rejected',
                'message' => 'การยืนยันตัวตนของคุณถูกปฏิเสธ',
                'reason' => $this->verificationRequest->reject_note,
                'type' => 'verification_rejected',
                'url' => route('profile/verification'),
                'created_at' => now()->toDateTimeString(),
                'extra' => [
                    'rejected_at' => $this->verificationRequest->process_at ? $this->verificationRequest->process_at->format('Y-m-d') : null,
                    'processed_by' => $this->verificationRequest->processedBy->name ?? 'ผู้ดูแลระบบ',
                ],
            ];
        } catch (\Exception $e) {
            \Log::error('VerificationRequestRejected database notification failed: ' . $e->getMessage());
            return [
                'verification_id' => $this->verificationRequest->id,
                'status' => 'rejected',
                'message' => 'การยืนยันตัวตนของคุณถูกปฏิเสธ',
                'type' => 'verification_rejected',
                'created_at' => now()->toDateTimeString(),
            ];
        }
    }
}
