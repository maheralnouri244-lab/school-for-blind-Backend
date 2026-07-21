<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ParentReportService;
use App\Models\Student;
use App\Models\StudentSummary;
use Carbon\Carbon;

class GenerateDailyReports extends Command
{
    protected $signature = 'reports:generate-daily';
    protected $description = 'توليد التقارير اليومية للأهل وحفظها في قاعدة البيانات';
    public function handle(ParentReportService $reportService)
    {
        $date = Carbon::today()->toDateString();
        $students = Student::where('status', 'approved')->get();

        foreach ($students as $student) {
            $reportData = $reportService->getDailyReport($student->id, $date);

            if (empty($reportData['attendance']) && empty($reportData['grades_today']) && empty($reportData['punishments'])) {
                continue;
            }

            StudentSummary::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'type' => 'daily',
                    'reference_date' => $date,
                ],
                [
                    'data' => json_encode($reportData)
                ]
            );
        }
        $this->info('تم توليد التقارير اليومية بنجاح!');
    }
}