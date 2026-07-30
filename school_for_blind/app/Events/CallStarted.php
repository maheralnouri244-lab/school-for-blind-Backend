<?php

namespace App\Events;

use App\Models\Room;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallStarted implements ShouldBroadcast 
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;
    public $class_id;
    public $teacher_name;

    public function __construct(Room $room, $class_id, $teacher_name)
    {
        $this->room = $room;
        $this->class_id = $class_id;
        $this->teacher_name = $teacher_name;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel("calls.class.{$this->class_id}")
        ];
    }

    public function broadcastAs(): string
    {
        return 'new-call';
    }

    public function broadcastWith(): array
    {
        return [
            'room_name'    => $this->room->room_name,
            'teacher_name' => $this->teacher_name,
            'started_at'   => $this->room->created_at->toDateTimeString(),
            'message'      => "بدأت حصة الأستاذ/ة {$this->teacher_name}، انضم الآن!"
        ];
    }
}