<?php

namespace App\Listeners;

use App\Events\AnnouncementCreated;
use App\Jobs\SendFcmNotificationJob;
use Illuminate\Support\Str;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Caregiver;

class SendAnnouncementNotification
{
    public function handle(AnnouncementCreated $event)
    {
        $announcement = $event->announcement;
        
        $title = "📢 إعلان جديد: " . $announcement->title;
        
        $body = is_string($announcement->content) && !is_array(json_decode($announcement->content, true))
            ? Str::limit($announcement->content, 60) 
            : "اضغط هنا لمعرفة تفاصيل الإعلان أو الجدول الجديد.";

        $tokens = [];

        if ($announcement->target_audience === 'student') {
            $query = Student::whereNotNull('fcm_token');
            if ($announcement->level !== 'all') {
                $query->where('level', $announcement->level);
            }
            if ($announcement->class_id) {
                $query->where('class_id', $announcement->class_id);
            }
            $tokens = $query->pluck('fcm_token')->toArray();

        } elseif ($announcement->target_audience === 'teacher') {
            $query = Teacher::whereNotNull('fcm_token');
            
            if ($announcement->level !== 'all') {
                $query->where('level', $announcement->level);
            }
            
            if ($announcement->class_id) {
                $query->whereHas('classes', function ($q) use ($announcement) {
                    $q->where('classes.id', $announcement->class_id);
                });
            }
            
            $tokens = $query->pluck('fcm_token')->toArray();

        } elseif ($announcement->target_audience === 'caregiver') {
            $query = Caregiver::whereNotNull('fcm_token');
            if ($announcement->level !== 'all') {
                $query->where('level', $announcement->level);
            }
            $tokens = $query->pluck('fcm_token')->toArray();
        }

        foreach ($tokens as $token) {
            SendFcmNotificationJob::dispatch($token, $title, $body);
        }
    }
}