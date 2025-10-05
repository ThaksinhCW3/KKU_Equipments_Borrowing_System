<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationRequestAutoRejected extends Notification implements ShouldQueue
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
                ->subject('คำขอยืนยันตัวตนถูกปฏิเสธอัตโนมัติ')
                ->greeting("สวัสดี {$notifiable->name}")
                ->line("คำขอยืนยันตัวตนของคุณถูกปฏิเสธอัตโนมัติ")
                ->line("เหตุผล: ไม่มีการดำเนินการจากผู้ดูแลภายใน 3 วัน")
                ->line("วันที่ส่งคำขอ: {$this->verificationRequest->created_at->format('d/m/Y')}")
                ->line("วันที่ปฏิเสธ: " . now()->format('d/m/Y'))
                ->line("หากต้องการยืนยันตัวตน กรุณาส่งคำขอใหม่")
                ->action('ส่งคำขอใหม่', route('verification.index'))
                ->line('ขอบคุณที่ใช้บริการของเรา');
        } catch (\Exception $e) {
            \Log::error('VerificationRequestAutoRejected mail failed: ' . $e->getMessage());
            return null;
        }
    }

    public function toDatabase($notifiable)
    {
        try {
            return [
                'verification_id' => $this->verificationRequest->id,
                'message'    => 'คำขอยืนยันตัวตนถูกปฏิเสธอัตโนมัติ เนื่องจากไม่มีการดำเนินการจากผู้ดูแลภายใน 3 วัน',
                'status' => 'rejected',
                'type'       => 'verification_auto_rejected',
                'url'        => route('verification.index'),
                'created_at' => now()->toDateTimeString(),
                'extra' => [
                    'reason' => 'ไม่มีการดำเนินการจากผู้ดูแลภายใน 3 วัน',
                    'submitted_at' => $this->verificationRequest->created_at->format('Y-m-d'),
                    'rejected_at' => now()->format('Y-m-d'),
                ],
            ];
        } catch (\Exception $e) {
            \Log::error('VerificationRequestAutoRejected database notification failed: ' . $e->getMessage());
            return [
                'verification_id' => $this->verificationRequest->id,
                'message'    => 'คำขอยืนยันตัวตนถูกปฏิเสธอัตโนมัติ เนื่องจากไม่มีการดำเนินการจากผู้ดูแลภายใน 3 วัน',
                'status' => 'rejected',
                'type'       => 'verification_auto_rejected',
                'created_at' => now()->toDateTimeString(),
            ];
        }
    }
}
