<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Caregiver;
use App\Models\Classes;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Lesson;
use App\Models\Room;
use App\Models\Attendance;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\Exam;
use App\Models\ExamSubmission;
use App\Models\Punishment;
use App\Models\Report;
use App\Models\StudentSummary;
use App\Services\ParentReportService;

class ParentReportTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $caregiver = Caregiver::firstOrCreate(
            ['phone' => '0911111111'],
            ['password' => Hash::make('12345678')]
        );

        $class = Classes::where('level', 'twelfth')->first();
        if (!$class) {
            $this->command->error('يرجى تشغيل ClassesTableSeeder أولاً.');
            return;
        }

        $student = Student::firstOrCreate(
            ['phone' => '0922222222'],
            [
                'fullname' => 'طالب تجريبي',
                'fathersname' => 'أب تجريبي',
                'parent_phone' => '0911111111',
                'parent_id' => $caregiver->id,
                'class_id' => $class->id,
                'level' => 'twelfth',
                'status' => 'approved',
                'DocumentaryEvidence' => 'dummy_doc.pdf',
                'points' => 150,
            ]
        );

        $teacher = Teacher::where('phone', '0943576695')->first();
        if (!$teacher) {
            $this->command->error('يرجى تشغيل SpecialTeacherSeeder أولاً.');
            return;
        }

        $subject1 = Subject::where('name', 'الفلسفة')->where('grade_level', 'twelfth')->first();
        $subject2 = Subject::where('name', 'اللغة العربية')->where('grade_level', 'twelfth')->first();

        if (!$subject1 || !$subject2) {
            $this->command->error('يرجى تشغيل SubjectSeeder أولاً.');
            return;
        }

        $lesson1 = Lesson::where('subject_id', $subject1->id)->first();
        $lesson2 = Lesson::where('subject_id', $subject2->id)->first();

        if (!$lesson1 || !$lesson2) {
            $this->command->error('يرجى تشغيل LessonSeeder أولاً.');
            return;
        }

        $room = Room::firstOrCreate(
            ['room_name' => 'بث الفلسفة المباشر'],
            [
                'creator_type' => Teacher::class,
                'creator_id' => $teacher->id,
                'class_id' => $class->id,
                'status' => 'ended',
                'started_at' => $now->copy()->subHours(2),
                'ended_at' => $now->copy()->subHours(1),
            ]
        );

        Attendance::firstOrCreate(
            [
                'room_id' => $room->id,
                'participant_id' => $student->id,
                'participant_type' => Student::class
            ],
            [
                'duration_seconds' => 30 * 60,
                'joined_at' => $now->copy()->subHours(2),
                'left_at' => $now->copy()->subMinutes(90),
            ]
        );

        $quiz = Quiz::firstOrCreate(
            [
                'subject_id' => $subject1->id,
                'lesson_id' => $lesson1->id,
                'teacher_id' => $teacher->id
            ],
            [
                'numofquestions' => 10,
                'timelimit' => 15,
                'totalmark' => 100,
                'created_at' => $now->copy()->subDays(1),
            ]
        );

        QuizSubmission::firstOrCreate(
            [
                'student_id' => $student->id,
                'quiz_id' => $quiz->id
            ],
            [
                'teacher_assigned_mark' => 85,
                'total_score' => 85,
                'status' => 'graded',
                'updated_at' => $now,
            ]
        );

        $exam1 = Exam::firstOrCreate(
            ['title' => 'مذاكرة الفلسفة'],
            [
                'subject_id' => $subject1->id,
                'teacher_id' => $teacher->id,
                'exam_date' => $now->copy()->subDays(3),
                'duration_minutes' => 60,
                'totalmark' => 200,
                'is_published' => true,
            ]
        );

        $exam2 = Exam::firstOrCreate(
            ['title' => 'مذاكرة اللغة العربية'],
            [
                'subject_id' => $subject2->id,
                'teacher_id' => $teacher->id,
                'exam_date' => $now->copy()->subDays(4),
                'duration_minutes' => 60,
                'totalmark' => 100,
                'is_published' => true,
            ]
        );

        ExamSubmission::firstOrCreate(
            [
                'student_id' => $student->id,
                'exam_id' => $exam1->id
            ],
            [
                'score' => 180,
                'status' => 'approved',
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now,
            ]
        );

        ExamSubmission::firstOrCreate(
            [
                'student_id' => $student->id,
                'exam_id' => $exam2->id
            ],
            [
                'score' => 90,
                'status' => 'approved',
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now,
            ]
        );

        $punishment = Punishment::firstOrCreate(
            ['name' => 'إنذار تأخر'],
            [
                'description' => 'إنذار بسبب التأخر عن البث',
                'level' => 1,
                'duration_minutes' => 1440
            ]
        );

        $hasPunishable = DB::table('punishables')
            ->where('punishment_id', $punishment->id)
            ->where('punishable_id', $student->id)
            ->where('punishable_type', Student::class)
            ->exists();

        if (!$hasPunishable) {
            DB::table('punishables')->insert([
                'punishment_id' => $punishment->id,
                'punishable_type' => Student::class,
                'punishable_id' => $student->id,
                'admin_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        Report::firstOrCreate(
            [
                'reporter_type' => Caregiver::class,
                'reporter_id' => $caregiver->id,
                'reported_type' => Student::class,
                'reported_id' => $student->id,
                'reason' => 'ابني كان مريضاً، أرجو إزالة الغياب.'
            ],
            [
                'status' => 'pending',
            ]
        );

        $reportService = app(ParentReportService::class);

        $dailyData = $reportService->getDailyReport($student->id, $now->toDateString());
        StudentSummary::updateOrCreate(
            ['student_id' => $student->id, 'type' => 'daily', 'reference_date' => $now->toDateString()],
            ['data' => json_encode($dailyData)]
        );

        $monthlyData = $reportService->getMonthlyReport($student->id, $now->month, $now->year);
        StudentSummary::updateOrCreate(
            ['student_id' => $student->id, 'type' => 'monthly', 'reference_date' => $now->format('Y-m')],
            ['data' => json_encode($monthlyData)]
        );

        $this->command->info('تم زراعة بيانات الأهل وإنشاء التقارير بنجاح باستخدام الداتا الحقيقية للمنصة!');
    }
}