<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\QuizSubmission;
use App\Models\Question;

use App\Models\StudentAnswer;
use App\Traits\RecordUploadTrait;
use App\Models\ExamSubmission;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;



class StudentExamController extends Controller
{use RecordUploadTrait;
    public function getExamsBySubject(Request $request)
{
    $subjectId = $request->query('subject_id');
    $userId = Auth::id();

    $exams = Exam::where('subject_id', $subjectId)
        ->where('is_published', true) 
        ->withExists(['favorites as is_favorited' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])
        ->get();

    $formattedExams = $exams->map(function ($exam) {
        $data = $exam->toArray();
        $data['is_favorited'] = (bool) $exam->is_favorited;
        return $data;
    });

    return response()->json(['status' => 'success', 'data' => $formattedExams]);
}
public function getQuestionsByExam($examId)
{
    $exam = Exam::with('questions.choices')->findOrFail($examId);

    $formattedQuestions = $exam->questions->map(function ($question) {
        $data = [
            'id' => $question->id,
            'type' => $question->type,
            'description' => $question->description,
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
        ];

        if ($question->type === 'mcq') {
            $data['choices'] = $question->choices->map(function($choice) {
                return [
                    'id' => $choice->id,
                    'choice_text' => $choice->choice_text,
                    'is_correct' => (bool) $choice->is_correct
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

    $submission = QuizSubmission::with(['quiz.questions.choices'])
        ->where('id', $submissionId)
        ->where('student_id', $studentId)
        ->firstOrFail();

    $studentAnswers = StudentAnswer::where('student_id', $studentId)
        ->where('exam_id', $submission->exam_id) 
        ->get();

    $details = $submission->quiz->questions->map(function ($question) use ($studentAnswers) {
        $answer = $studentAnswers->where('question_id', $question->id)->first();

        return [
            'question_id' => $question->id,
            'question'    => $question->description,
            'type'        => $question->type,
            'student_text_answer'  => $answer->text_answer ?? null,
            'student_audio_answer' => $answer->audio_answer ?? null,
            'is_correct'  => $answer->is_correct ?? 0,
            
            'correct_answer' => $question->type === 'mcq' 
                ? $question->choices->where('is_correct', true)->first()->choice_text ?? 'غير محدد'
                : $question->correct_answer,
                
            'choices' => $question->type === 'mcq' ? $question->choices->map(function($c) {
                return ['id' => $c->id, 'text' => $c->choice_text];
            }) : null
        ];
    });

    return response()->json([
        'status' => 'success',
        'data' => [
            'submission_id' => $submission->id,
            'total_score'   => $submission->total_score,
            'details'       => $details
        ]
    ]);
}
public function submitExam(Request $request): JsonResponse
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
            'student_id' => $studentId,
            'exam_id'    => $request->exam_id,
            'total_score'=> 0,
            'status'     => 'pending_grading', 
        ]);

        $totalAutoScore = 0;
        $hasEssayQuestion = false;

        foreach ($request->answers as $index => $answerData) {
            $question = Question::with('choices')->find($answerData['question_id']);
            
            $isCorrect = 0;
            $questionMarkEarned = 0.0;
            $audioPath = null;

            if ($question->type === 'mcq') {
                $correctChoice = $question->choices()->where('is_correct', true)->first();
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

            \App\Models\ExamStudentAnswer::create([
                'student_id'  => $studentId,
                'exam_id'     => $request->exam_id,
                'question_id' => $question->id,
                'choice_id'   => $answerData['choice_id'] ?? null,
                'text_answer' => $answerData['text_answer'] ?? null,
                'is_correct'  => $isCorrect,
                'audio_answer'=> $audioPath,
            ]);
        }

        $submission->update(['total_score' => $totalAutoScore, 'status' => $hasEssayQuestion ? 'pending' : 'graded']);

        DB::commit();
        return response()->json(['status' => 'success', 'message' => 'تم تسليم الاختبار بنجاح.']);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
}
}
