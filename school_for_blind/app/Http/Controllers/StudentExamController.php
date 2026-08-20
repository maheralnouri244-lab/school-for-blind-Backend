<?php

namespace App\Http\Controllers;

use App\Http\Requests\examRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\ExamStudentAnswer;
use App\Models\QuizSubmission;
use App\Models\Question;

use App\Models\StudentAnswer;
use App\Traits\RecordUploadTrait;
use App\Models\ExamSubmission;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;



class StudentExamController extends Controller
{
    
use RecordUploadTrait;
    

public function getExamsBySubject(Request $request)
{
    $subjectId = $request->query('subject_id');
    $userId = Auth::id();

    $exams = Exam::where('subject_id', $subjectId)
        ->where('is_published', true)
        ->select('exams.*')
        ->selectRaw('(EXISTS (
            SELECT 1 FROM favorites 
            WHERE favorites.favorable_id = exams.id 
            AND favorites.favorable_type = ? 
            AND favorites.user_id = ?
        )) as is_favorited', [Exam::class, $userId])
        ->get()
        ->map(function ($exam) {
            $data = $exam->toArray();
            $data['is_favorited'] = (bool) $exam->is_favorited;
            return $data;
        });

    return response()->json(['status' => 'success', 'data' => $exams]);  
}

public function getExamDetails($id): JsonResponse
{
    $userId = Auth::id();

    $exam = Exam::with('questions')
        ->withExists(['favorites as is_favorited' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])
        ->find($id);

    if (!$exam) {
        return response()->json([
            'status'  => 'error',
            'message' => 'عذراً، الامتحان المطلوب غير موجود.'
        ], 404);
    }

    return response()->json([
        'status'  => 'success',
        'message' => 'تم جلب تفاصيل الامتحان بنجاح.',
        'data'    => [
            'exam_id'          => $exam->id,
            'exam_title'       => $exam->title,
            'descreption'      => $exam->description,
            'exam_date'        => $exam->exam_date,
            'duration_minutes' => $exam->duration_minutes, 
            'total_questions'  => $exam->numofquestions,     
            'total_mark'       => $exam->totalmark,          
            'is_favorited'     => (bool) $exam->is_favorited, 
        ]
    ], 200);
}
public function getQuestionsByExam($examId)
{
    $exam = Exam::with('questions.choices')->findOrFail($examId);

    $formattedQuestions = $exam->questions->map(function ($question) use ($exam) {
        $data = [
            'id' => $question->id,
            'type' => $question->type,
            'description' => $question->description,
            'point'=>$question->points,
            'totalmark'=>$exam->totalmark,
        ];

        if ($question->type === 'mcq') {
            $data['choices'] = $question->choices->map(function($choice) {
                return [
                    'id' => $choice->id,
                    'choice_text' => $choice->choice_text
                ];
            });
        }
        return $data;
    });

    return response()->json(['status' => 'success', 'data' => $formattedQuestions]);
}
public function getExamWithSolutions($examId)
{
    $exam = Exam::with('questions.choices')->findOrFail($examId);

    $formattedQuestions = $exam->questions->map(function ($question) {
        $data = [
            'id' => $question->id,
            'type' => $question->type,
            'description' => $question->description,
'points' => $question->points,
        ];

        if ($question->type === 'mcq') {
            $data['choices'] = $question->choices->map(function($choice) {
                return [
                    'id' => $choice->id,
                    'choice_text' => $choice->choice_text,
                    'is_correct' => (bool) $choice->is_correct,

                ];
            });
        } else {
            $data['solution'] = $question->correct_answer;
        }
        return $data;
    });

    return response()->json(['status' => 'success', 'data' => $formattedQuestions]);
}

public function getSubmissionDetails($submissionId): JsonResponse
    {
        $studentId = Auth::id();

        $submission = ExamSubmission::with(['exam.questions.choices'])
            ->where('id', $submissionId)
            ->where('student_id', $studentId)
            ->firstOrFail();

        $studentAnswers = ExamStudentAnswer::where('student_id', $studentId)
            ->where('exam_id', $submission->exam_id) 
            ->get();

        $details = $submission->exam->questions->map(function ($question) use ($studentAnswers) {
            $answer = $studentAnswers->where('question_id', $question->id)->first();

            return [
                'question_id'          => $question->id,
                'question'             => $question->description,
                'type'                 => $question->type,
                'student_text_answer'  => $answer->text_answer,
                'student_audio_answer' => $answer && $answer->audio_answer ? asset('storage/' . $answer->audio_answer) : null,
                'is_correct'           => $answer ? ($answer->is_correct ?? 0) : 0,
                'points_earned'        => $answer ? ($answer->points_earned ?? 0) : 0,
                
                'correct_answer'       => $question->type === 'mcq' 
                    ? ($question->choices->where('is_correct', true)->first()->choice_text ?? 'غير محدد')
                    : $question->correct_answer,
                    
                'choices'              => $question->type === 'mcq' ? $question->choices->map(function($c) {
                    return ['id' => $c->id, 'text' => $c->choice_text];
                }) : null
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => [
                'submission_id' => $submission->id,
                'total_score'   => $submission->score, 
                'status'        => $submission->status,
                'details'       => $details
            ]
        ]);
    }
public function submitExam(examRequest $request): JsonResponse
{
    $studentId = Auth::id();

    $existingSubmission = ExamSubmission::where('student_id', $studentId)
        ->where('exam_id', $request->exam_id)
        ->first();

    if ($existingSubmission) {
        return response()->json([
            'status' => 'error', 
            'message' => 'عذراً، لقد قمت بتقديم هذا الاختبار مسبقاً.'
        ], 403);
    }

    DB::beginTransaction();
    try {
        $submission = ExamSubmission::create([
            'student_id'  => $studentId,
            'exam_id'     => $request->exam_id,
            'score' => 0,
            'status'      => 'pending_grading', 
        ]);

        $totalAutoScore = 0;
        $hasEssayQuestion = false;

        foreach ($request->answers as $index => $answerData) {
            $question = Question::with('choices')->find($answerData['question_id']);
            
            if (!$question) {
                continue;
            }

            $isCorrect = 0;
            $questionMarkEarned = 0.0;
            $audioPath = null;

            if ($question->type === 'mcq') {
                $correctChoice = $question->choices->where('is_correct', true)->first();
                if ($correctChoice && $correctChoice->id == ($answerData['choice_id'] ?? null)) {
                    $isCorrect = 1;
                    $questionMarkEarned = (float) $question->points;
                }
            } elseif ($question->type === 'TF') {
                if (trim($question->correct_answer) == trim($answerData['text_answer'] ?? '')) {
                    $isCorrect = 1;
                    $questionMarkEarned = (float) $question->points;
                }
            } elseif ($question->type === 'TEXT') {
                $hasEssayQuestion = true;
                if ($request->hasFile("answers.{$index}.audio_answer")) {
                    $audioPath = $this->uploadRecord($request->file("answers.{$index}.audio_answer"), 'student_audios');
                }
            }

            $totalAutoScore += $questionMarkEarned;

            ExamStudentAnswer::create([
                'student_id'   => $studentId,
                'exam_id'      => $request->exam_id,
                'question_id'  => $question->id,
                'choice_id'    => $answerData['choice_id'] ?? null,
                'text_answer'  => $answerData['text_answer'] ?? null,
                'is_correct'   => $isCorrect,
                'audio_answer' => $audioPath, 
                'points_earned'=> $questionMarkEarned,
                'is_graded'    => $question->type === 'TEXT' ? 0 : 1,
            ]);
        }

        $submission->score = $totalAutoScore; 
        if (!$hasEssayQuestion) {
            $submission->status = 'graded';
        }
        
        $submission->save();

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'تم تسليم الاختبار بنجاح وتصحيح الأسئلة المؤتمتة تلقائياً!',
            'data' => [
                'submission_id' => $submission->id,
                'score'    => $totalAutoScore,
                'status'        => $submission->status,
            ]
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => 'error',
            'message' => 'حدث خطأ أثناء حفظ إجابات الاختبار: ' . $e->getMessage()
        ], 500);
    }
}}
