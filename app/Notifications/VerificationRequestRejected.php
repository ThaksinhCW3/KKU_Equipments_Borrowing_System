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
        return (new MailMessage)
            ->subject('คำขอยืนยันตัวตนถูกปฏิเสธ')
            ->greeting("สวัสดี {$notifiable->name}")
            ->line("คำขอยืนยันตัวตนของคุณถูกปฏิเสธ")
            ->line("เหตุผล: {$this->verificationRequest->reject_note}")
            ->line("กรุณาตรวจสอบข้อมูลและส่งคำขอใหม่หากต้องการ")
            ->action('ส่งคำขอใหม่', route('profile.show'))
            ->line('หากมีข้อสงสัย กรุณาติดต่อผู้ดูแลระบบ');
    }

    public function toDatabase($notifiable)
    {
        return [
            'verification_request_id' => $this->verificationRequest->id,
            'status' => 'rejected',
            'message' => 'คำขอยืนยันตัวตนถูกปฏิเสธ',
            'type' => 'verification_request_rejected',
            'url' => route('profile.show'),
            'reject_note' => $this->verificationRequest->reject_note,
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
