<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\StudentSummary;
use App\Models\Room;
use App\Models\Lesson;
use App\Services\ParentReportService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateMonthlyReports extends Command
{
    protected $signature = 'reports:generate-monthly {date?}';
    protected $description = 'توليد وتخزين التقارير الشهرية للأهل بناءً على نشاط المدرسة الفعلي وإهمال الطلاب';

    public function handle(ParentReportService $reportService)
    {
        $targetDate = $this->argument('date') ? Carbon::parse($this->argument('date')) : Carbon::today();
        $month = $targetDate->month;
        $year = $targetDate->year;
        $referenceDate = $targetDate->format('Y-m');

        $students = Student::where('status', 'approved')->get();

        $this->info("بدء توليد التقارير الشهرية لشهر: {$referenceDate}");

        foreach ($students as $student) {
            $hasRooms = Room::where('class_id', $student->class_id)
                ->whereMonth('started_at', $month)
                ->whereYear('started_at', $year)
                ->where('status', 'ended')
                ->exists();

            $hasQuizzes = DB::table('quizzes')
                ->join('lessons', 'quizzes.lesson_id', '=', 'lessons.id')
                ->where('lessons.class_id', $student->class_id)
                ->whereMonth('quizzes.created_at', $month)
                ->whereYear('quizzes.created_at', $year)
                ->exists();

            $classSubjectIds = DB::table('lessons')->where('class_id', $student->class_id)->pluck('subject_id')->unique();
            $hasExams = DB::table('exams')
                ->whereIn('subject_id', $classSubjectIds)
                ->where('is_published', true)
                ->whereMonth('exam_date', $month)
                ->whereYear('exam_date', $year)
                ->exists();

            if (!$hasRooms && !$hasQuizzes && !$hasExams) {
                continue;
            }

            $reportData = $reportService->getMonthlyReport($student->id, $month, $year);

            StudentSummary::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'type' => 'monthly',
                    'reference_date' => $referenceDate,
                ],
                [
                    'data' => json_encode($reportData)
                ]
            );
        }

        $this->info('تمت عملية توليد وتخزين التقارير الشهرية بنجاح.');
    }
}