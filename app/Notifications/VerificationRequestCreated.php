<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerificationRequestCreated extends Notification implements ShouldQueue
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
            ->subject('มีคำขอการยืนยันตัวตนใหม่')
            ->line("ผู้ใช้ " . ($this->verificationRequest && $this->verificationRequest->user ? $this->verificationRequest->user->name : 'ผู้ใช้') . " ได้ส่งคำขอการยืนยันตัวตน")
            ->line("อีเมล: " . ($this->verificationRequest && $this->verificationRequest->user ? $this->verificationRequest->user->email : '-'))
            ->action('ดูรายละเอียด', route('admin.verification.show', $this->verificationRequest->id))
            ->line('กรุณาตรวจสอบคำขอนี้ด้วยครับ');
    }

    public function toDatabase($notifiable)
    {
        return [
            'verification_id' => $this->verificationRequest->id,
            'user_id' => $this->verificationRequest && $this->verificationRequest->user ? $this->verificationRequest->user->id : null,
            'user_name' => $this->verificationRequest && $this->verificationRequest->user ? $this->verificationRequest->user->name : 'ผู้ใช้',
            'user_email' => $this->verificationRequest && $this->verificationRequest->user ? $this->verificationRequest->user->email : '-',
            'status' => 'created',
            'message' => 'มีคำขอการยืนยันตัวตนใหม่',
            'type' => 'verification_created',
            'url' => route('admin.verification.show', $this->verificationRequest->id),
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
