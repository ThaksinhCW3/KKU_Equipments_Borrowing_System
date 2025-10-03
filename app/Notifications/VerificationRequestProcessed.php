<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationRequestProcessed extends Notification implements ShouldQueue
{
    use Queueable;

    protected $verificationRequest;
    protected $status;

    public function __construct($verificationRequest, $status)
    {
        $this->verificationRequest = $verificationRequest;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $subject = $this->status === 'approved' ? 'การยืนยันตัวตนได้รับการอนุมัติ' : 'การยืนยันตัวตนถูกปฏิเสธ';
        $message = $this->status === 'approved' 
            ? 'การยืนยันตัวตนของคุณได้รับการอนุมัติแล้ว คุณสามารถยืมอุปกรณ์ได้แล้ว'
            : 'การยืนยันตัวตนของคุณถูกปฏิเสธ กรุณาอัปโหลดรูปบัตรนักศึกษาใหม่';

        return (new MailMessage)
            ->subject($subject)
            ->greeting("สวัสดี {$notifiable->name}")
            ->line($message)
            ->when($this->verificationRequest->admin_note, function ($mail) {
                return $mail->line('หมายเหตุ: ' . $this->verificationRequest->admin_note);
            })
            ->action('ดูสถานะการยืนยัน', route('verification.index'));
    }

    public function toDatabase($notifiable)
    {
        $message = $this->status === 'approved' 
            ? 'การยืนยันตัวตนของคุณได้รับการอนุมัติแล้ว'
            : 'การยืนยันตัวตนของคุณถูกปฏิเสธ';

        return [
            'verification_id' => $this->verificationRequest->id,
            'status' => $this->status,
            'message' => $message,
            'admin_note' => $this->verificationRequest->admin_note,
            'processed_by' => $this->verificationRequest->processedBy->name ?? 'ผู้ดูแลระบบ',
            'type' => 'verification_processed',
            'url' => route('verification.index'),
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
