<?php

namespace App\Listeners;

use App\Events\CallStarted;
use App\Jobs\SendFcmNotificationJob;
use App\Models\Student;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCallNotification implements ShouldQueue
{
    public function handle(CallStarted $event): void
    {
        $class_id = $event->class_id;
        $teacher_name = $event->teacher_name;
        
        $title = "🎥 مكالمة مرئية جديدة!";
        $body = "بدأت للتو حصة الأستاذ/ة {$teacher_name}، سارع بالانضمام للمحاضرة الآن.";

        Student::whereNotNull('fcm_token')
            ->where('class_id', $class_id)
            ->chunk(100, function ($students) use ($title, $body) {
                foreach ($students as $student) {
                    SendFcmNotificationJob::dispatch($student->fcm_token, $title, $body);
                }
            });
    }
}