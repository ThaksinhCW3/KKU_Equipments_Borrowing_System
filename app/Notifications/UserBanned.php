<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class UserBanned extends Notification
{
    use Queueable;

    protected $banReason;
    protected $bannedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct($banReason, $bannedBy = null)
    {
        $this->banReason = $banReason;
        $this->bannedBy = $bannedBy;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $bannedByUser = $this->bannedBy ? User::find($this->bannedBy) : null;
        $adminName = $bannedByUser ? $bannedByUser->name : 'ผู้ดูแลระบบ';
        $adminEmail = $bannedByUser ? $bannedByUser->email : '';
        $adminPhone = $bannedByUser ? $bannedByUser->phonenumber : '';

        $mailMessage = (new MailMessage)
            ->subject('บัญชีของคุณถูกแบน - KKU Equipment Borrowing System')
            ->greeting('สวัสดี ' . $notifiable->name)
            ->line('เราต้องแจ้งให้ทราบว่าบัญชีของคุณในระบบยืมอุปกรณ์ KKU ได้ถูกแบนแล้ว')
            ->line('เหตุผล: ' . $this->banReason)
            ->line('ผู้ดำเนินการ: ' . $adminName)
            ->line('วันที่: ' . now()->format('d/m/Y H:i:s'));

        // Add admin contact information if available
        if ($adminEmail) {
            $mailMessage->line('อีเมลติดต่อ: ' . $adminEmail);
        }
        if ($adminPhone) {
            $mailMessage->line('เบอร์โทรติดต่อ: ' . $adminPhone);
        }

        $mailMessage->line('หากคุณคิดว่าการแบนนี้เป็นข้อผิดพลาด กรุณาติดต่อผู้ดูแลระบบ')
            ->salutation('ขอแสดงความนับถือ, ระบบยืมอุปกรณ์ KKU');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $bannedByUser = $this->bannedBy ? User::find($this->bannedBy) : null;
        $adminName = $bannedByUser ? $bannedByUser->name : 'ผู้ดูแลระบบ';
        $adminEmail = $bannedByUser ? $bannedByUser->email : '';
        $adminPhone = $bannedByUser ? $bannedByUser->phonenumber : '';

        return [
            'title' => 'บัญชีของคุณถูกแบน',
            'message' => 'บัญชีของคุณถูกแบนเนื่องจาก: ' . $this->banReason,
            'type' => 'ban',
            'ban_reason' => $this->banReason,
            'banned_by' => $adminName,
            'banned_by_email' => $adminEmail,
            'banned_by_phone' => $adminPhone,
            'banned_at' => now()->toISOString(),
            'action_url' => route('banned'),
        ];
    }
}
