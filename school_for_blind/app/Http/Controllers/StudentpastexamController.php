<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PastExam;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class StudentpastexamController extends Controller
{
    public function getPastExamsBySubject(Request $request): JsonResponse
    {
        $subjectId = $request->query('subject_id');
        $userId = Auth::id();

        if (!$subjectId) {
            return response()->json(['status' => 'error', 'message' => 'يرجى تحديد subject_id'], 400);
        }

        $exams = PastExam::where('subject_id', $subjectId)
            ->select('id', 'title', 'year', 'session')
            ->withExists(['favorites as is_favorited' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->get();

        $formattedExams = $exams->map(function ($exam) {
            $data = $exam->toArray();
            $data['is_favorited'] = (bool) $exam->is_favorited;
            return $data;
        });

        return response()->json([
            'status' => 'success',
            'data' => $formattedExams
        ]);
    }
  public function getQuestionsByPastExam($pastExamId): JsonResponse
{
    $pastExam = PastExam::find($pastExamId);

    if (!$pastExam) {
        return response()->json(['status' => 'error', 'message' => 'الدورة غير موجودة'], 404);
    }

    $questions = $pastExam->questions()->with('choices')->get();

    $formattedQuestions = $questions->map(function ($question) {
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

    return response()->json([
        'status' => 'success',
        'data' => $formattedQuestions
    ]);
}
public function getPastExamWithSolutions($pastExamId): JsonResponse
{
    $pastExam = PastExam::find($pastExamId);

    if (!$pastExam) {
        return response()->json(['status' => 'error', 'message' => 'الدورة غير موجودة'], 404);
    }

    $questions = $pastExam->questions()->with('choices')->get();

    $formattedQuestions = $questions->map(function ($question) {
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

    return response()->json([
        'status' => 'success',
        'data' => $formattedQuestions
    ]);
}
}