<?php

namespace App\Events;

use App\Models\Announcement;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnnouncementCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $announcement;

    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    
    public function broadcastOn(): array
    {
        $target = $this->announcement->target_audience; 
        $sectionId = $this->announcement->section_id;   

        if ($target === 'section' && $sectionId) {
            return [new Channel("announcements.section.{$sectionId}")];
        }

        return [new Channel("announcements.{$target}")];
    }

    public function broadcastAs(): string
    {
        return 'new-announcement';
    }

    
    public function broadcastWith(): array
    {
        $content = $this->announcement->content;
        if (is_string($content)) {
            $decoded = json_decode($content, true);
            $content = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $content;
        }

        return [
            'id'              => (string) $this->announcement->id,
            'type'            => (string) $this->announcement->type,
            'title'           => (string) $this->announcement->title,
            'content'         => $content,
            'target_audience' => (string) $this->announcement->target_audience,
            'section_id'      => $this->announcement->section_id ? (string) $this->announcement->section_id : null,
            'created_at'      => $this->announcement->created_at->toDateTimeString(),
        ];
    }
}