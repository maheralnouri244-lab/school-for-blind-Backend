<?php

namespace App\Events;

use App\Models\Teacher;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SalaryTransferred implements ShouldBroadcast 
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $teacher;
    public $amount;

    public function __construct(Teacher $teacher, $amount)
    {
        $this->teacher = $teacher;
        $this->amount = $amount;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel("teacher.{$this->teacher->id}.notifications")
        ];
    }

    public function broadcastAs(): string
    {
        return 'salary-transferred';
    }

    public function broadcastWith(): array
    {
        return [
            'title' => 'إشعار تحويل مالي',
            'message' => "تم تحويل مبلغ {$this->amount} ل.س إلى حسابك بنجاح.",
            'date' => now()->toDateTimeString(),
        ];
    }
}