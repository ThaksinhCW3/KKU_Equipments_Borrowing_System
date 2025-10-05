<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BorrowRequestApproved extends Notification implements ShouldQueue
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
        $rangeMsg = '';
                if ($this->borrowRequest->start_at && $this->borrowRequest->end_at) {
                    $signed = $this->borrowRequest->start_at->diffInDays($this->borrowRequest->end_at, false) + 1;
                    $days = max($signed, 0);

                    $rangeMsg = "คุณสามารถยืมได้ {$days} วัน "
                        . $this->borrowRequest->start_at->format('d-m-Y')
                        . ' ถึง ' . $this->borrowRequest->end_at->format('d-m-Y');
                } else {
                    $rangeMsg = "ยังไม่ได้เลือกวันที่เริ่มและสิ้นสุด";
                }

        return (new MailMessage)
            ->subject('คำขอยืมของคุณได้รับการอนุมัติ')
            ->greeting("สวัสดี {$notifiable->name}")
            ->line("คำขอยืมอุปกรณ์: {$this->borrowRequest->equipment->name}")
            ->line("ได้รับการอนุมัติเรียบร้อยแล้ว ")
            ->when($rangeMsg !== '', function ($mail) use ($rangeMsg) {
                return $mail->line($rangeMsg);
            })
            ->action('ดูรายละเอียด', url('/borrower/reqdetail', $this->borrowRequest->req_id))
            ->line(' กรุณามารับอุปกรณ์ตามเวลาที่กำหนด:')
            ->line('เช้า: 09:00-10:00 น. | บ่าย: 14:00-15:00 น.')
            ->line('(เฉพาะวันจันทร์-ศุกร์)');
    }

    public function toDatabase($notifiable)
    {
        $extra = [];
        if ($this->borrowRequest->start_at && $this->borrowRequest->end_at) {
            $extra['start_at'] = $this->borrowRequest->start_at->format('d-m-Y');
            $extra['end_at'] = $this->borrowRequest->end_at->format('d-m-Y');
            $extra['days'] = $this->borrowRequest->end_at->diffInDays($this->borrowRequest->start_at) + 1;
            $extra['message_detail'] = "คุณสามารถยืมได้ {$extra['days']} วัน {$extra['start_at']} ถึง {$extra['end_at']}";
        }
        return [
            'request_id' => $this->borrowRequest->id,
            'equipment'  => $this->borrowRequest->equipment->name,
            'message'    => 'คำขอยืมของคุณได้รับการอนุมัติแล้ว',
            'status' => 'approved',
            'type'       => 'borrow_request_approved',
            'url'        => route('borrower/equipments/reqdetail', $this->borrowRequest->req_id),
            'created_at' => now()->toDateTimeString(),
            'extra' => $extra,
            'pickup_times' => 'เช้า: 09:00-10:00 น. | บ่าย: 14:00-15:00 น. (เฉพาะวันจันทร์-ศุกร์)',
        ];
    }
}
