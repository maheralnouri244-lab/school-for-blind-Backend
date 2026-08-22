<?php

namespace App\Listeners;

use App\Events\QuizNeedsGrading;
use App\Jobs\SendFcmNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendQuizNeedsGradingNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 3;

    public function handle(QuizNeedsGrading $event): void
    {
        $submission = $event->submission;
        $submission->loadMissing(['student', 'quiz.teacher', 'quiz.lesson', 'quiz.subject']);

        $student = $submission->student;
        $quiz = $submission->quiz;
        $teacher = $quiz?->teacher;

        if (!$teacher) {
            return;
        }

        $studentName = $student->fullname ?? 'أحد الطلاب';
        $quizTitle = $quiz->title 
            ?? ($quiz->lesson->name ? "درس " . $quiz->lesson->name : null)
            ?? ($quiz->subject->name ? "مادة " . $quiz->subject->name : "الكويز");

        $title = "📝 إجابة جديدة بحاجة للتصحيح";
        $body  = "قام الطالب ({$studentName}) بتسليم إجابات ({$quizTitle}) وهي بانتظار تصحيحك للأسئلة المقالية.";

        $data = [
            'type'          => 'quiz_needs_grading',
            'submission_id' => (string) $submission->id,
            'quiz_id'       => (string) $submission->quiz_id,
            'student_id'    => (string) $student?->id,
            'screen'        => 'TeacherGradingScreen',
        ];

        SendFcmNotificationJob::dispatch($teacher, $title, $body, $data);
    }
}