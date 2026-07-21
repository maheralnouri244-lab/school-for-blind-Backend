<?php

namespace App\Http\Controllers;

use App\Models\QuizSubmission;
use App\Models\StudentAnswer;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeacherQuizController extends Controller
{
 public function getPendingEssayAnswers(Request $request): JsonResponse
    {
$teacher = Auth::guard('teacher')->user();
   if (!$teacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مصرح لك بالوصول، يجب تسجيل الدخول كمعلم.'
            ], 401);
        }     

$teacherSectionIds = $teacher->classes()->pluck('class_id')->toArray();

        if (empty($teacherSectionIds)) {
            return response()->json([
                'status' => 'success',
                'message' => 'لا توجد شعب صفية مسندة إليك حالياً.',
                'data' => []
            ], 200);
        }

      $pendingAnswers = StudentAnswer::whereHas('question', function ($query) {
                $query->where('type', 'TEXT');
            })
            ->whereHas('student', function ($query) use ($teacherSectionIds) {
                $query->whereIn('class_id', $teacherSectionIds);
            })
            ->with([
                'student.class',          
                'question.quizzes' 
            ])
            ->get();

       $formattedData = $pendingAnswers->groupBy(function ($answer) {
            $quiz = $answer->question->quizzes->first();
            return $answer->student_id . '-' . ($quiz ? $quiz->id : 0);
        })->map(function ($answersGroup) {
            $firstAnswer = $answersGroup->first();
            $student = $firstAnswer->student;
            $question = $firstAnswer->question;
            $quiz = $question->quizzes->first();
            $lesson = $quiz ? $quiz->lesson : null;

            $realSubmission = QuizSubmission::where('student_id', $student->id)
                ->where('quiz_id', $quiz ? $quiz->id : 0)
                ->first();
              return [
                'submission_id'   => $realSubmission ? $realSubmission->id : null,
                'student_id'      => $student->id,
                'student_name'    => $student->fullname,
                'student_section' => $student->class_id ? ($student->class ? $student->class->name : 'غير محدد') : 'غير محدد',
                'lesson_id'       => $lesson ? $lesson->id : null,
                'lesson_title'    => $lesson ? $lesson->title : 'بدون عنوان',
                'quiz_id'         => $quiz->id,
                
                'essay_questions' => $answersGroup->map(function ($answer) {
                    $q = $answer->question;
                    return [
                        'answer_id'        => $answer->id,
                        'question_id'      => $q->id,
                        'question_mark'    => (float) $q->points,
                        'question_details' => [
                            'description'  => $q->description,
                        ],
                        'text_answer'      => $answer->text_answer,
                        'audio_answer_url' => $answer->audio_answer ? asset('storage/' . $answer->audio_answer) : null,
                    ];
                })->values()->toArray(),
            ];
        })->values(); 

        return response()->json([
            'status' => 'success',
            'data'   => $formattedData
        ], 200);
    }

    public function gradeFullQuizSubmission(Request $request): JsonResponse
{
    $request->validate([
        'submission_id'          => 'required|exists:quiz_submissions,id',
        'answers'                => 'required|array|min:1',
        'answers.*.answer_id'     => 'required|exists:student_answers,id',
        'answers.*.points_earned' => 'required|numeric|min:0',
    ]);

    DB::beginTransaction();
    try {
        $submission = QuizSubmission::findOrFail($request->submission_id);

        if ($submission->status === 'graded') {
            return response()->json([
                'status'  => 'error',
                'message' => 'تم تصحيح هذا الاختبار مسبقاً بالكامل ولا يمكن التعديل عليه.'
            ], 400);
        }

        $totalEssayScore = 0;

        foreach ($request->answers as $gradedAnswer) {
            $studentAnswer = StudentAnswer::with('question')->findOrFail($gradedAnswer['answer_id']);
            $question = $studentAnswer->question;

            if ($gradedAnswer['points_earned'] > $question->points) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "العلامة المدخلة للسؤال المقالي ({$question->text}) أكبر من حدها الأقصى وهو {$question->points}."
                ], 422);
            }

            $studentAnswer->update([
                'points_earned' => (float) $gradedAnswer['points_earned'],
                'is_correct'    => $gradedAnswer['points_earned'] > 0 ? 1 : 0,
                'is_graded'     => true
            ]);

            $totalEssayScore += (float) $gradedAnswer['points_earned'];
        }

        $submission->total_score += $totalEssayScore;
        
        $submission->status = 'graded'; 
        $submission->save();

        DB::commit();

        return response()->json([
            'status'  => 'success',
            'message' => 'تم رصد علامات الأسئلة المقالية وتحديث النتيجة النهائية للاختبار بنجاح!',
            'data'    => [
                'submission_id' => $submission->id,
                'student_id'    => $submission->student_id,
                'quiz_id'       => $submission->quiz_id,
                'final_score'   => $submission->total_score,
                'status'        => $submission->status
            ]
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status'  => 'error',
            'message' => 'حدث خطأ أثناء حفظ درجات التصحيح: ' . $e->getMessage()
        ], 500);
    }
}
}