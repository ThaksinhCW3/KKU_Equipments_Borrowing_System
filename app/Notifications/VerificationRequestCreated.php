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
        try {
            return (new MailMessage)
                ->subject('มีคำขอการยืนยันตัวตนใหม่')
                ->line("ผู้ใช้ {$this->verificationRequest->user->name} ได้ส่งคำขอการยืนยันตัวตน")
                ->line("อีเมล: {$this->verificationRequest->user->email}")
                ->action('ดูรายละเอียด', route('admin.verification.show', $this->verificationRequest->id))
                ->line('กรุณาตรวจสอบคำขอนี้ด้วยครับ');
        } catch (\Exception $e) {
            \Log::error('VerificationRequestCreated mail failed: ' . $e->getMessage());
            return null;
        }
    }

    public function toDatabase($notifiable)
    {
        try {
            return [
                'verification_id' => $this->verificationRequest->id,
                'user_id' => $this->verificationRequest->user->id,
                'user_name' => $this->verificationRequest->user->name,
                'user_email' => $this->verificationRequest->user->email,
                'status' => 'created',
                'message' => 'มีคำขอการยืนยันตัวตนใหม่',
                'type' => 'verification_created',
                'url' => route('admin.verification.show', $this->verificationRequest->id),
                'created_at' => now()->toDateTimeString(),
            ];
        } catch (\Exception $e) {
            \Log::error('VerificationRequestCreated database notification failed: ' . $e->getMessage());
            return [
                'verification_id' => $this->verificationRequest->id,
                'message' => 'มีคำขอการยืนยันตัวตนใหม่',
                'type' => 'verification_created',
                'created_at' => now()->toDateTimeString(),
            ];
        }
    }
}
