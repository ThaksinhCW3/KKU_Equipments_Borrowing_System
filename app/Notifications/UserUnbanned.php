<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class UserUnbanned extends Notification
{
    use Queueable;

    protected $unbannedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct($unbannedBy = null)
    {
        $this->unbannedBy = $unbannedBy;
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
        $unbannedByUser = $this->unbannedBy ? User::find($this->unbannedBy) : null;
        $adminName = $unbannedByUser ? $unbannedByUser->name : 'ผู้ดูแลระบบ';
        $adminEmail = $unbannedByUser ? $unbannedByUser->email : '';
        $adminPhone = $unbannedByUser ? $unbannedByUser->phonenumber : '';

        $mailMessage = (new MailMessage)
            ->subject('บัญชีของคุณได้รับการปลดแบน - KKU Equipment Borrowing System')
            ->greeting('สวัสดี ' . $notifiable->name)
            ->line('เรามีข่าวดีสำหรับคุณ! บัญชีของคุณในระบบยืมอุปกรณ์ KKU ได้รับการปลดแบนแล้ว')
            ->line('คุณสามารถเข้าใช้งานระบบได้ตามปกติแล้ว')
            ->line('ผู้ดำเนินการ: ' . $adminName)
            ->line('วันที่: ' . now()->format('d/m/Y H:i:s'));

        // Add admin contact information if available
        if ($adminEmail) {
            $mailMessage->line('อีเมลติดต่อ: ' . $adminEmail);
        }
        if ($adminPhone) {
            $mailMessage->line('เบอร์โทรติดต่อ: ' . $adminPhone);
        }

        $mailMessage->action('เข้าสู่ระบบ', url('/login'))
            ->line('ขอบคุณที่ให้ความร่วมมือกับเรา')
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
        $unbannedByUser = $this->unbannedBy ? User::find($this->unbannedBy) : null;
        $adminName = $unbannedByUser ? $unbannedByUser->name : 'ผู้ดูแลระบบ';
        $adminEmail = $unbannedByUser ? $unbannedByUser->email : '';
        $adminPhone = $unbannedByUser ? $unbannedByUser->phonenumber : '';

        return [
            'title' => 'บัญชีของคุณได้รับการปลดแบน',
            'message' => 'บัญชีของคุณได้รับการปลดแบนแล้ว คุณสามารถเข้าใช้งานระบบได้ตามปกติ',
            'type' => 'unban',
            'unbanned_by' => $adminName,
            'unbanned_by_email' => $adminEmail,
            'unbanned_by_phone' => $adminPhone,
            'unbanned_at' => now()->toISOString(),
            'action_url' => url('/login'),
        ];
    }
}
