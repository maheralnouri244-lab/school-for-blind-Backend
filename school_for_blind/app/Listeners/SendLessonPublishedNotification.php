<?php

namespace App\Listeners;

use App\Events\LessonPublished;
use App\Jobs\SendFcmNotificationJob;
use App\Models\Student;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendLessonPublishedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 3;

    public function handle(LessonPublished $event): void
    {
        $lesson = $event->lesson;
        $lesson->loadMissing(['subject', 'teacher']);

        $teacherName = $lesson->teacher->full_name ?? 'أستاذ المادة';
        $subjectName = $lesson->subject->name ?? 'المادة';
        $lessonTitle = $lesson->title;

        $students = Student::where('class_id', $lesson->class_id)
            ->where('status', 'approved')
            ->get();

        if ($students->isEmpty()) {
            return;
        }

        $title = "📚 درس جديد: {$subjectName}";
        $body  = "قام {$teacherName} بنشر درس جديد بعنوان ({$lessonTitle}).";

        $data = [
            'type'       => 'new_lesson',
            'lesson_id'  => (string) $lesson->id,
            'subject_id' => (string) $lesson->subject_id,
            'screen'     => 'LessonDetailsScreen',
        ];

        foreach ($students as $student) {
            SendFcmNotificationJob::dispatch($student, $title, $body, $data);
        }
    }
}