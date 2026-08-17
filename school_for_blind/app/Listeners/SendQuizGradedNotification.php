<?php

namespace App\Listeners;

use App\Events\QuizGraded;
use App\Jobs\SendFcmNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendQuizGradedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(QuizGraded $event): void
    {
        $submission = $event->submission;

        $submission->loadMissing(['student.parent', 'quiz.subject', 'quiz.lesson']);

        $student = $submission->student;
        if (!$student) {
            return;
        }

        $quiz = $submission->quiz;

        $quizTitle = $quiz->title 
            ?? ($quiz->subject->name ? "مادة " . $quiz->subject->name : null)
            ?? ($quiz->lesson->name ? "درس " . $quiz->lesson->name : "الكويز");

        $score     = $submission->total_score ?? $submission->score ?? 0;
        $totalMark = $quiz->totalmark ?? 0;
        $studentName = $student->fullname ?? 'الطالب';

        $data = [
            'type'          => 'quiz_graded',
            'quiz_id'       => (string) $submission->quiz_id,
            'submission_id' => (string) $submission->id,
            'score'         => (string) $score,
            'screen'        => 'QuizResultScreen',
        ];

        $studentTitle = "📝 صدور نتيجة الاختبار";
        $studentBody  = "تم الانتهاء من تصحيح ({$quizTitle}). نتيجتك النهائية هي: {$score} من {$totalMark}";
        
        SendFcmNotificationJob::dispatch($student, $studentTitle, $studentBody, $data);

        $parent = $student->parent;

        if ($parent) {
            $parentTitle = "📊 تقرير أداء: {$studentName}";
            $parentBody  = "تم رصد نتيجة ({$quizTitle}) للطالب ({$studentName}) وحصل على: {$score} من {$totalMark}";

            $parentData = array_merge($data, [
                'student_id' => (string) $student->id,
            ]);

            SendFcmNotificationJob::dispatch($parent, $parentTitle, $parentBody, $parentData);
        }
    }
}