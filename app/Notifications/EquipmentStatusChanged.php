<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class EquipmentStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $equipment;
    protected $oldStatus;
    protected $newStatus;

    public function __construct($equipment, $oldStatus, $newStatus)
    {
        $this->equipment = $equipment;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $statusText = $this->getStatusText($this->newStatus);
        $oldStatusText = $this->getStatusText($this->oldStatus);
        
        return (new MailMessage)
            ->subject('สถานะอุปกรณ์เปลี่ยนแปลง')
            ->line("สถานะของอุปกรณ์ {$this->equipment->name} ได้เปลี่ยนแปลง")
            ->line("จาก: {$oldStatusText}")
            ->line("เป็น: {$statusText}")
            ->line("รหัสอุปกรณ์: {$this->equipment->code}")
            ->action('ดูรายละเอียดอุปกรณ์', route('equipments/show', $this->equipment->code))
            ->line('กรุณาตรวจสอบสถานะอุปกรณ์ใหม่');
    }

    public function toDatabase($notifiable)
    {
        $statusText = $this->getStatusText($this->newStatus);
        $oldStatusText = $this->getStatusText($this->oldStatus);
        
        return [
            'equipment_id' => $this->equipment->id,
            'equipment_code' => $this->equipment->code,
            'equipment_name' => $this->equipment->name,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'old_status_text' => $oldStatusText,
            'new_status_text' => $statusText,
            'message' => "สถานะอุปกรณ์ {$this->equipment->name} เปลี่ยนจาก {$oldStatusText} เป็น {$statusText}",
            'url' => route('equipments/show', $this->equipment->code),
        ];
    }

    private function getStatusText($status)
    {
        switch ($status) {
            case 'available':
                return 'พร้อมใช้งาน';
            case 'unavailable':
                return 'ไม่พร้อมใช้งาน';
            case 'maintenance':
                return 'อยู่ระหว่างซ่อมบำรุง';
            case 'broken':
                return 'ชำรุด';
            default:
                return ucfirst($status);
        }
    }
}
