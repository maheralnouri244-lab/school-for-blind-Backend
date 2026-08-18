<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PastExam;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class StudentpastexamController extends Controller
{
  public function getPastExamsBySubject(Request $request)
{
    $subjectId = $request->query('subject_id');
    $userId = Auth::id();

    $exams = PastExam::where('subject_id', $subjectId)
        ->select('past_exams.*')
        ->selectRaw('(EXISTS (
            SELECT 1 FROM favorites 
            WHERE favorites.favorable_id = past_exams.id 
            AND favorites.favorable_type LIKE "%PastExam%" 
            AND favorites.user_id = ?
        )) as is_favorited', [$userId])
        ->get()
        ->map(function ($exam) {
            $exam->is_favorited = (bool) $exam->is_favorited;
            return $exam;
        });

    return response()->json([
        'status' => 'success',
        'data'   => $exams
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