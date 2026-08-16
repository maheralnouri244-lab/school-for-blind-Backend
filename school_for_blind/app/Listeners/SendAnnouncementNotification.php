<?php

namespace App\Listeners;

use App\Events\AnnouncementCreated;
use App\Jobs\SendFcmNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Caregiver;

class SendAnnouncementNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(AnnouncementCreated $event): void
    {
        $announcement = $event->announcement;
        
        $title = "📢 إعلان جديد: " . $announcement->title;
        
        $body = is_string($announcement->content) && !is_array(json_decode($announcement->content, true))
            ? Str::limit(strip_tags($announcement->content), 60) 
            : "اضغط هنا لمعرفة تفاصيل الإعلان أو الجدول الجديد.";

        $dataPayload = [
            'type'            => 'announcement',
            'announcement_id' => (string) $announcement->id,
            'target_audience' => (string) $announcement->target_audience,
            'screen'          => 'AnnouncementDetailsScreen',
        ];

        $dispatchJobs = function ($query) use ($title, $body, $dataPayload) {
            $query->chunk(100, function ($users) use ($title, $body, $dataPayload) {
                foreach ($users as $user) {
                    SendFcmNotificationJob::dispatch(
                        $user->fcm_token, 
                        $title, 
                        $body, 
                        $dataPayload
                    );
                }
            });
        };

        if ($announcement->target_audience === 'student') {
            $query = Student::whereNotNull('fcm_token');
            if ($announcement->level && $announcement->level !== 'all') {
                $query->where('level', $announcement->level);
            }
            if ($announcement->class_id) {
                $query->where('class_id', $announcement->class_id);
            }
            $dispatchJobs($query);

        } elseif ($announcement->target_audience === 'teacher') {
            $query = Teacher::whereNotNull('fcm_token');
            if ($announcement->level && $announcement->level !== 'all') {
                $query->where('level', $announcement->level);
            }
            if ($announcement->class_id) {
                $query->whereHas('classes', function ($q) use ($announcement) {
                    $q->where('classes.id', $announcement->class_id);
                });
            }
            $dispatchJobs($query);

        } elseif ($announcement->target_audience === 'caregiver') {
            $query = Caregiver::whereNotNull('fcm_token');
            if ($announcement->level && $announcement->level !== 'all') {
           $query->whereHas('students', function ($q) use ($announcement) {
                    $q->where('level', $announcement->level);
                });
           
            }
            if ($announcement->class_id) {
                $query->whereHas('students', function ($q) use ($announcement) {
                    $q->where('class_id', $announcement->class_id);
                });
            }
            $dispatchJobs($query);
        }
    }
}