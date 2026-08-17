<?php

namespace App\Listeners;

use App\Events\QuizAutoGraded;
use App\Jobs\SendFcmNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendQuizAutoGradedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(QuizAutoGraded $event): void
    {
        $submission = $event->submission;
        
        $submission->load(['student.parent', 'quiz.subject']);

        $student = $submission->student;
        $quiz = $submission->quiz;

        if (!$student || !$quiz) {
            return;
        }

        $subjectName = $quiz->subject->name ?? 'المادة';
        $studentScore = $submission->total_score;
        $maxMark = $quiz->totalmark;

        // 1. إرسال الإشعار للطالب
        if (!empty($student->fcm_token)) {
            $studentTitle = "🎉 نتيجتك في الكويز!";
            $studentBody = "لقد حصلت على {$studentScore} من {$maxMark} في كويز مادة {$subjectName}.";
            
            SendFcmNotificationJob::dispatch($student->fcm_token, $studentTitle, $studentBody);
        }

        $parent = $student->parent;
        if ($parent && !empty($parent->fcm_token)) {
            $parentTitle = "📊 نتيجة كويز: {$student->fullname}";
            $parentBody = "أنهى الطالب {$student->fullname} كويز مادة {$subjectName} بدرجة ({$studentScore} / {$maxMark}).";

            SendFcmNotificationJob::dispatch($parent->fcm_token, $parentTitle, $parentBody);
        }
    }
}