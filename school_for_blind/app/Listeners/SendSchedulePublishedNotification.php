<?php

namespace App\Listeners;

use App\Events\SchedulePublished;
use App\Jobs\SendFcmNotificationJob;
use App\Models\Classes;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Schedule;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendSchedulePublishedNotification implements ShouldQueue
{
    public $tries = 3;

    public function handle(SchedulePublished $event): void
    {
        try {
            $classId = $event->classId;

            $studentClass = Classes::find($classId);
            $className = $studentClass ? $studentClass->name : '';

            $title = "📅 برنامج دراسي جديد";
            $body  = "تم نشر برنامج دراسي جديد لشعبة: {$className}";
            
            $data = [
                'type'     => 'new_schedule',
                'class_id' => (string) $classId,
                'screen'   => 'ScheduleScreen',
            ];

            $students = Student::where('class_id', $classId)->get();
            foreach ($students as $student) {
                if ($student && is_object($student)) {
                    SendFcmNotificationJob::dispatch($student, $title, $body, $data);
                }
            }

            $teacherIds = Schedule::where('class_id', $classId)
                ->whereNotNull('teacher_id')
                ->pluck('teacher_id')
                ->unique();

            if ($teacherIds->isNotEmpty()) {
                $teachers = Teacher::whereIn('id', $teacherIds)->get();
                foreach ($teachers as $teacher) {
                    if ($teacher && is_object($teacher)) {
                        SendFcmNotificationJob::dispatch($teacher, $title, $body, $data);
                    }
                }
            }

        } catch (\Throwable $e) {
            Log::error("SendSchedulePublishedNotification Error: " . $e->getMessage());
            $this->fail($e);
        }
    }
}