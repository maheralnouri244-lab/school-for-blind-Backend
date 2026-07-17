<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AbsenceExcuse;
use App\Models\Report;
use App\Models\StudentSummary;
use App\Models\Subject;
use App\Services\ParentReportService;
use Illuminate\Http\Request;

class ParentReportController extends Controller
{
    protected $reportService;

    public function __construct(ParentReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function getYearlyReport(Request $request)
    {
        $caregiver = $request->user();
        $students = $caregiver->students;

        $year = $request->input('year', date('Y'));
        $response = [];

        foreach ($students as $student) {
            $reportData = $this->reportService->getYearlyReport($student->id, $year);
            $subjects = Subject::where('grade_level', $student->level)->get();
            $response[] = [
                'greeting_message' => "إلى ولي أمر الطالب: " . $student->fullname,
                'student_id' => $student->id,
                'student_name' => $student->fullname,
                'report_data' => $reportData,
                'subjects' => $subjects,
            ];
        }


        return response()->json([
            'status' => 'success',
            'data' => $response,
            // 'subjects' => $subjects,
        ]);
    }

    public function getMonthlyReport(Request $request)
    {
        $caregiver = $request->user();
        $students = $caregiver->students;
        $referenceDate = $request->input('reference_date', date('Y-m'));
        $response = [];

        foreach ($students as $student) {
            $summary = StudentSummary::where('student_id', $student->id)
                ->where('type', 'monthly')
                ->where('reference_date', $referenceDate)
                ->first();

            $response[] = [
                'greeting_message' => "إلى ولي أمر الطالب: " . $student->fullname,
                'student_id' => $student->id,
                'student_name' => $student->fullname,
                'report_data' => $summary ? json_decode($summary->data) : null,
                'message' => $summary ? 'تم جلب التقرير بنجاح' : 'التقرير الشهري غير متوفر بعد.'
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $response
        ]);
    }

    public function getDailyReport(Request $request)
    {
        $caregiver = $request->user();
        $students = $caregiver->students;
        $referenceDate = $request->input('reference_date', date('Y-m-d'));
        $response = [];

        foreach ($students as $student) {
            $summary = StudentSummary::where('student_id', $student->id)
                ->where('type', 'daily')
                ->where('reference_date', $referenceDate)
                ->first();

            $response[] = [
                'greeting_message' => "إلى ولي أمر الطالب: " . $student->fullname,
                'student_id' => $student->id,
                'student_name' => $student->fullname,
                'report_data' => $summary ? json_decode($summary->data) : null,
                'message' => $summary ? 'تم جلب التقرير بنجاح' : 'التقرير اليومي غير متوفر بعد.'
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $response
        ]);
    }
    public function submitAbsenceExcuse(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'room_id' => 'required|exists:rooms,id',
            'reason' => 'nullable|string|max:500',
        ]);

        $caregiver = $request->user();

        $student = $caregiver->students()->where('id', $request->student_id)->first();
        if (!$student) {
            return response()->json(['status' => 'error', 'message' => 'طالب غير صالح.'], 403);
        }

        $excuse = AbsenceExcuse::updateOrCreate(
            [
                'student_id' => $student->id,
                'caregiver_id' => $caregiver->id,
                'room_id' => $request->room_id,
            ],
            [
                'reason' => $request->reason,
                'status' => 'pending',
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'تم إرسال تبرير الغياب بنجاح بانتظار مراجعة الإدارة.',
            'data' => $excuse
        ]);
    }

    public function submitObjection(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'reason' => 'required|string|max:1000',
            'reportable_id' => 'nullable|integer',
            'reportable_type' => 'nullable|string',
        ]);

        $caregiver = $request->user();

        $student = $caregiver->students()->where('id', $request->student_id)->first();
        if (!$student) {
            return response()->json(['status' => 'error', 'message' => 'طالب غير صالح.'], 403);
        }

        $objection = Report::create([
            'reporter_type' => get_class($caregiver),
            'reporter_id' => $caregiver->id,
            'reported_type' => get_class($student),
            'reported_id' => $student->id,
            'reason' => $request->reason,
            'status' => 'pending',
            'reportable_id' => $request->reportable_id,
            'reportable_type' => $request->reportable_type,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم إرسال الاعتراض إلى الإدارة بنجاح وسيتم مراجعته.',
            'data' => $objection
        ]);
    }
}