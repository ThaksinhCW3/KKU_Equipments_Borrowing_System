<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationRequestApproved extends Notification implements ShouldQueue
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
                ->subject('การยืนยันตัวตนของคุณได้รับการอนุมัติ')
                ->greeting("สวัสดี {$notifiable->name}")
                ->line("การยืนยันตัวตนของคุณได้รับการอนุมัติเรียบร้อยแล้ว")
                ->line("คุณสามารถยืมอุปกรณ์ได้แล้ว")
                ->when($this->verificationRequest->process_at, function ($mail) {
                    return $mail->line("วันที่อนุมัติ: " . $this->verificationRequest->process_at->format('d/m/Y'));
                })
                ->action('ดูสถานะการยืนยัน', route('profile/verification'))
                ->line('ขอบคุณที่ใช้บริการของเรา');
        } catch (\Exception $e) {
            \Log::error('VerificationRequestApproved mail failed: ' . $e->getMessage());
            return null;
        }
    }

    public function toDatabase($notifiable)
    {
        try {
            return [
                'verification_id' => $this->verificationRequest->id,
                'message' => 'การยืนยันตัวตนของคุณได้รับการอนุมัติแล้ว',
                'status' => 'approved',
                'type' => 'verification_approved',
                'url' => route('profile/verification'),
                'created_at' => now()->toDateTimeString(),
                'extra' => [
                    'approved_at' => $this->verificationRequest->process_at ? $this->verificationRequest->process_at->format('Y-m-d') : null,
                    'processed_by' => $this->verificationRequest->processedBy->name ?? 'ผู้ดูแลระบบ',
                ],
            ];
        } catch (\Exception $e) {
            \Log::error('VerificationRequestApproved database notification failed: ' . $e->getMessage());
            return [
                'verification_id' => $this->verificationRequest->id,
                'message' => 'การยืนยันตัวตนของคุณได้รับการอนุมัติแล้ว',
                'status' => 'approved',
                'type' => 'verification_approved',
                'created_at' => now()->toDateTimeString(),
            ];
        }
    }
}
