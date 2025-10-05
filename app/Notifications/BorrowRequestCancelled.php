<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BorrowRequestCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    protected $borrowRequest;

    public function __construct($borrowRequest)
    {
        $this->borrowRequest = $borrowRequest;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('คำขอยืมอุปกรณ์ถูกยกเลิก')
            ->line("ผู้ใช้ {$this->borrowRequest->user->name} ได้ยกเลิกคำขอยืมอุปกรณ์")
            ->line("อุปกรณ์: {$this->borrowRequest->equipment->name}")
            ->line("รหัสคำขอ: #{$this->borrowRequest->req_id}")
            ->line("เหตุผลการยกเลิก: {$this->borrowRequest->cancel_reason}")
            ->action('ดูรายละเอียด', route('admin/requests/show', $this->borrowRequest->req_id))
            ->line('กรุณาตรวจสอบรายละเอียดการยกเลิก');
    }

    public function toDatabase($notifiable)
    {
        return [
            'request_id' => $this->borrowRequest->id,
            'equipment' => $this->borrowRequest->equipment->name,
            'user' => $this->borrowRequest->user->name,
            'status' => 'cancelled',
            'message' => 'คำขอยืมอุปกรณ์ถูกยกเลิก',
            'reason' => $this->borrowRequest->cancel_reason,
            'type' => 'borrow_request_cancelled',
            'url' => route('admin/requests/show', $this->borrowRequest->req_id),
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
