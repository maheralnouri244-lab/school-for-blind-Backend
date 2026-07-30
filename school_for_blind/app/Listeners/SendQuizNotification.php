<?php

namespace App\Listeners;

use App\Events\QuizCreated;
use App\Jobs\SendFcmNotificationJob;
use App\Models\Student;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendQuizNotification implements ShouldQueue
{
    public function handle(QuizCreated $event): void
    {
        $quiz = $event->quiz;
        $class_id = $event->class_id;
        
        $title = "📝 كويز جديد بانتظارك!";
        $body = "تم نشر كويز جديد لمادة " . ($quiz->subject_name ?? 'الدرس') . ". استعد واختبر معلوماتك!";

        Student::whereNotNull('fcm_token')
            ->where('class_id', $class_id)
            ->chunk(100, function ($students) use ($title, $body, $quiz) {
                foreach ($students as $student) {
                    SendFcmNotificationJob::dispatch($student->fcm_token, $title, $body);
                }
            });
    }
}