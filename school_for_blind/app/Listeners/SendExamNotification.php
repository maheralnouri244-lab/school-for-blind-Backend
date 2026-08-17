<?php

namespace App\Listeners;

use App\Events\ExamPublished;
use App\Jobs\SendFcmNotificationJob;
use App\Models\Student;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendExamNotification implements ShouldQueue
{
    public function handle(ExamPublished $event): void
    {
        $exam = $event->exam;
        
        $subject = $exam->subject;
        $classId = $subject->class_id ?? null;

        $title = "📢 تم نشر موعد الامتحان!";
        $body = "تم نشر امتحان {$exam->title} لمادة " . ($subject->name ?? 'الدراسية') . ". يرجى مراجعة الجدول.";

        $query = Student::whereNotNull('fcm_token');

        if ($classId) {
            $query->where('class_id', $classId);
        }

        $query->chunk(100, function ($students) use ($title, $body) {
            foreach ($students as $student) {
                SendFcmNotificationJob::dispatch($student->fcm_token, $title, $body);
            }
        });
    }
}