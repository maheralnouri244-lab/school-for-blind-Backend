<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSubmission;
use App\Models\ExamStudentAnswer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherExamController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
            'duration_minutes' => 'required|integer|min:5',
            'questions' => 'required|array|min:1',
            'questions.*.type' => 'required|in:mcq,TF,TEXT',
            'questions.*.description' => 'required|string',
            'questions.*.points' => 'required|numeric|min:1',
            'questions.*.choices' => 'required_if:questions.*.type,mcq|array|min:2',
            'questions.*.choices.*.text' => 'required_with:questions.*.choices|string',
            'questions.*.choices.*.is_correct' => 'required_with:questions.*.choices|boolean',
            'questions.*.correct_answer' => 'required_unless:questions.*.type,mcq|string',
        ]);

        DB::beginTransaction();
        try {
            $exam = Exam::create([
                'title' => $request->title,
                'description' => $request->description,
                'subject_id' => $request->subject_id,
                'exam_date' => null,
                'duration_minutes' => $request->duration_minutes,
                'teacher_id' => auth()->id(),
                'is_published' => false,
            ]);

            foreach ($request->questions as $q) {
                $question = Question::create([
                    'teacher_id' => auth()->id(),
                    'type' => $q['type'],
                    'description' => $q['description'],
                    'correct_answer' => $q['type'] !== 'mcq' ? $q['correct_answer'] : null,
                    'points' => $q['points'],
                    'status' => 'publish',
                ]);

                if ($q['type'] === 'mcq' && isset($q['choices'])) {
                    foreach ($q['choices'] as $choice) {
                        $question->choices()->create([
                            'choice_text' => $choice['text'],
                            'is_correct' => $choice['is_correct'] ?? false,
                        ]);
                    }
                }
                $exam->questions()->attach($question->id);
            }

            DB::commit();
            $exam->load('questions.choices');

            return response()->json([
                'message' => 'تم رفع مقترح الامتحان للإدارة بنجاح!',
                'exam' => $exam
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'حدث خطأ أثناء حفظ الامتحان',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function getExamsPendingGrading()
    {
        $teacher_id = auth()->id();

        $exams = Exam::where('teacher_id', $teacher_id)
            ->whereHas('submissions', function ($query) {
                $query->where('status', 'pending_grading');
            })
            ->with(['subject'])
            ->withCount([
                'submissions' => function ($query) {
                    $query->where('status', 'pending_grading');
                }
            ])
            ->get();

        return response()->json([
            'message' => 'الامتحانات التي بانتظار التصحيح',
            'exams' => $exams
        ]);
    }

    public function getExamSubmissions($examId)
    {
        $exam = Exam::where('teacher_id', auth()->id())->findOrFail($examId);

        $submissions = ExamSubmission::with('student:id,fullname')
            ->where('exam_id', $examId)
            ->where('status', 'pending_grading')
            ->get();

        return response()->json([
            'exam_id' => $exam->id,
            'exam_title' => $exam->title,
            'submissions' => $submissions
        ]);
    }

    public function getPendingTextAnswers($examId, $studentId)
    {
        $submission = ExamSubmission::where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->firstOrFail();

        $textAnswers = ExamStudentAnswer::where('student_id', $studentId)
            ->where('exam_id', $examId)
            ->where('is_graded', false)
            ->whereHas('question', function ($q) {
                $q->where('type', 'TEXT');
            })
            ->with('question:id,description,points')
            ->get();

        return response()->json([
            'exam_id' => (int) $examId,
            'student_id' => (int) $studentId,
            'submission_status' => $submission->status,
            'current_score' => $submission->score,
            'answers_to_grade' => $textAnswers
        ]);
    }

    public function gradeTextAnswers(Request $request, $examId, $studentId)
    {
        $request->validate([
            'grades' => 'required|array',
            'grades.*.answer_id' => 'required|exists:exam_student_answers,id',
            'grades.*.points' => 'required|numeric|min:0',
        ]);

        $submission = ExamSubmission::where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->first();

        if (!$submission) {
            return response()->json(['error' => 'لم يتم العثور على التسليم.'], 404);
        }

        DB::beginTransaction();
        try {
            foreach ($request->grades as $gradeData) {
                $studentAnswer = ExamStudentAnswer::with('question')
                    ->where('id', $gradeData['answer_id'])
                    ->where('student_id', $studentId)
                    ->where('exam_id', $examId)
                    ->first();

                if ($studentAnswer) {
                    $maxPoints = $studentAnswer->question->points;

                    if ($gradeData['points'] > $maxPoints) {
                        DB::rollBack();
                        return response()->json([
                            'error' => "عذراً، لا يمكن إعطاء علامة أكبر من علامة السؤال الأصلية ({$maxPoints}).",
                        ], 400);
                    }

                    $studentAnswer->points_earned = $gradeData['points'];
                    $studentAnswer->is_correct = $gradeData['points'] > 0;
                    $studentAnswer->is_graded = true;
                    $studentAnswer->save();
                }
            }

            $ungradedAnswers = ExamStudentAnswer::where('student_id', $studentId)
                ->where('exam_id', $examId)
                ->where('is_graded', false)
                ->exists();

            $newTotalScore = ExamStudentAnswer::where('student_id', $studentId)
                ->where('exam_id', $examId)
                ->sum('points_earned');

            $submission->score = $newTotalScore;

            if (!$ungradedAnswers) {
                $submission->status = 'pending_approval';
            }

            $submission->save();
            DB::commit();

            return response()->json([
                'message' => 'تم رصد العلامات وتحويل الورقة للإدارة للاعتماد بنجاح!',
                'new_total_score' => $newTotalScore,
                'status' => $submission->status
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'حدث خطأ أثناء حفظ العلامات',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}