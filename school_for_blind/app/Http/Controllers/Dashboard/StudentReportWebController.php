<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentSummary;
use App\Services\ParentReportService;
use Illuminate\Http\Request;

class StudentReportWebController extends Controller
{
    protected $reportService;

    public function __construct(ParentReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(Request $request, Student $student)
    {
        $reportType = $request->input('report_type');
        $referenceDate = $request->input('reference_date');

        $summariesQuery = StudentSummary::where('student_id', $student->id);

        if (!empty($reportType) && $reportType !== 'all') {
            if (in_array($reportType, ['daily', 'monthly'])) {
                $summariesQuery->where('type', $reportType);
            }
        }

        if (!empty($referenceDate)) {
            $summariesQuery->where('reference_date', 'like', "%{$referenceDate}%");
        }

        $summaries = collect();
        if (empty($reportType) || $reportType === 'all' || in_array($reportType, ['daily', 'monthly'])) {
            $summaries = $summariesQuery->orderBy('reference_date', 'desc')->get();
        }

        $yearlyReports = [];
        if (empty($reportType) || $reportType === 'all' || $reportType === 'yearly') {
            $year = !empty($referenceDate) ? (strlen($referenceDate) >= 4 ? substr($referenceDate, 0, 4) : $referenceDate) : date('Y');

            if (is_numeric($year)) {
                $yearlyData = $this->reportService->getYearlyReport($student->id, $year);
                if ($yearlyData) {
                    $yearlyReports[] = [
                        'academic_year' => $year,
                        'data' => $yearlyData
                    ];
                }
            }
        }

        $message = ($summaries->count() > 0 || count($yearlyReports) > 0)
            ? 'تم جلب التقارير بنجاح.'
            : 'لا توجد تقارير مطابقة للمعايير المحددة.';

        return view('pages.students.reports', compact('student', 'reportType', 'referenceDate', 'summaries', 'yearlyReports', 'message'));
    }
}