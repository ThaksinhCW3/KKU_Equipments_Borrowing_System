<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EquipmentCheckedIn extends Notification implements ShouldQueue
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
        $equipment = $this->borrowRequest->equipment->name ?? '-';
        $reqId = $this->borrowRequest->req_id;
        $checkedInAt = optional($this->borrowRequest->transaction?->checked_in_at)->format('d/m/Y');
        $penaltyAmount = $this->borrowRequest->transaction?->penalty_amount ?? 0;
        $penaltyStatus = $this->borrowRequest->transaction?->penalty_check ?? 'unpaid';
        $notes = $this->borrowRequest->transaction?->notes;
        
        $hasPenalty = $penaltyAmount > 0;
        $isPenaltyPaid = $penaltyStatus === 'paid';

        $mail = (new MailMessage)
            ->subject('คืนอุปกรณ์เรียบร้อยแล้ว - ขอบคุณที่ใช้บริการ')
            ->greeting("สวัสดี {$notifiable->name}")
            ->line("**คืนอุปกรณ์เรียบร้อยแล้ว**")
            ->line("**อุปกรณ์**: {$equipment}")
            ->line("**เลขที่คำขอ**: #{$reqId}")
            ->line("**เวลาคืน**: {$checkedInAt}");

        // Add notes if available
        if ($notes) {
            $mail->line("")
                 ->line("**หมายเหตุ**: {$notes}");
        }

        // Add penalty information if applicable
        if ($hasPenalty) {
            $mail->line("")
                 ->line("**ค่าปรับ**: ฿" . number_format($penaltyAmount, 2))
                 ->line("**สถานะการชำระ**: " . ($isPenaltyPaid ? 'ชำระแล้ว' : 'ยังไม่ชำระ'));
            
            if (!$isPenaltyPaid) {
                $mail->line("• กรุณาชำระค่าปรับตามที่กำหนด");
            } else {
                $mail->line("• ขอบคุณที่ชำระค่าปรับเรียบร้อยแล้ว");
            }
        }

        $mail->line("")
             ->line("**ขอบคุณที่**:")
             ->line("• พยายามคืนอุปกรณ์ตรงเวลา")
             ->line("• พยายามดูแลรักษาอุปกรณ์เป็นอย่างดี")
             ->line("• พยายามใช้บริการระบบยืมอุปกรณ์");

        if ($hasPenalty && !$isPenaltyPaid) {
            $mail->line("")
                 ->line("**ติดต่อ**: หากมีข้อสงสัยเกี่ยวกับค่าปรับ กรุณาติดต่อเจ้าหน้าที่");
        }

        $mail->line("")
             ->line("**หากต้องการยืมอุปกรณ์อีกครั้ง** สามารถทำได้ผ่านระบบ")
             ->action('ดูประวัติการยืม', route('borrower.equipments.reqdetail', $this->borrowRequest->req_id))
             ->line("ขอบคุณที่ใช้บริการระบบยืมอุปกรณ์");

        return $mail;
    }

    public function toDatabase($notifiable)
    {
        $checkedInAt = optional($this->borrowRequest->transaction?->checked_in_at)->format('d/m/Y ');
        $penaltyAmount = $this->borrowRequest->transaction?->penalty_amount ?? 0;
        $penaltyStatus = $this->borrowRequest->transaction?->penalty_check ?? 'unpaid';
        $notes = $this->borrowRequest->transaction?->notes;
        
        $hasPenalty = $penaltyAmount > 0;
        $isPenaltyPaid = $penaltyStatus === 'paid';
        
        // Build message based on conditions
        $message = "คืนอุปกรณ์เรียบร้อยแล้ว - ขอบคุณที่ใช้บริการ";
        if ($hasPenalty) {
            $message .= " (มีค่าปรับ: ฿" . number_format($penaltyAmount, 2) . ")";
        }
        
        return [
            'request_id' => $this->borrowRequest->id,
            'equipment'  => $this->borrowRequest->equipment->name ?? '-',
            'message'    => $message,
            'status'     => $hasPenalty && !$isPenaltyPaid ? 'warning' : 'success',
            'type'       => 'equipment_checked_in',
            'url'        => route('borrower.equipments.reqdetail', $this->borrowRequest->req_id),
            'created_at' => now()->toDateTimeString(),
            'extra' => [
                'checked_in_at' => $this->borrowRequest->transaction?->checked_in_at?->format('Y-m-d'),
                'equipment_name' => $this->borrowRequest->equipment->name,
                'penalty_amount' => $penaltyAmount,
                'penalty_status' => $penaltyStatus,
                'is_penalty_paid' => $isPenaltyPaid,
                'has_penalty' => $hasPenalty,
                'notes' => $notes,
                'thank_you_message' => 'ขอบคุณที่คืนอุปกรณ์ตรงเวลาและดูแลรักษาเป็นอย่างดี'
            ],
        ];
    }

}
