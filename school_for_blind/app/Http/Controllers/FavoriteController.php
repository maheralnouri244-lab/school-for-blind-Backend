<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\PastExam;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    private function getModelClass(string $type): string
    {
        return match ($type) {
            'lesson'   => Lesson::class,
            'quiz'     => Quiz::class,
            'PastExam' => PastExam::class,
            'Exam'     => Exam::class,
        };
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'id'   => 'required|integer',
            'type' => 'required|string|in:lesson,quiz,PastExam,Exam',
        ]);

        $userId = Auth::id();
        $model = $this->getModelClass($request->type);

        $favorite = Favorite::where('user_id', $userId)
            ->where('favorable_id', $request->id)
            ->where('favorable_type', $model) 
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['message' => 'Removed', 'is_favorite' => false]);
        }

        Favorite::create([
            'user_id'        => $userId,
            'favorable_id'   => $request->id,
            'favorable_type' => $model, 
        ]);

        return response()->json(['message' => 'Added', 'is_favorite' => true]);
    }

    public function addToFavorite(Request $request)
    {
        $request->validate([
            'id'   => 'required|integer',
            'type' => 'required|string|in:lesson,quiz,PastExam,Exam',
        ]);

        $model = $this->getModelClass($request->type);
        $userId = Auth::id();

        $exists = Favorite::where('user_id', $userId)
            ->where('favorable_id', $request->id)
            ->where('favorable_type', $model)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Item is already in favorites',
                'is_favorite' => true
            ]);
        }

        Favorite::create([
            'user_id'        => $userId,
            'favorable_id'   => $request->id,
            'favorable_type' => $model,
        ]);

        return response()->json([
            'message' => 'Added to favorites successfully',
            'is_favorite' => true
        ]);
    }

    public function index()
    {
        $favorites = Favorite::where('user_id', Auth::id())->get();

        return response()->json($favorites);
    }

    public function isFavorite(Request $request)
    {
        $request->validate([
            'favorable_id'   => 'required|integer',
            'favorable_type' => 'required|string',
        ]);

        $model = str_contains($request->favorable_type, '\\') 
            ? $request->favorable_type 
            : $this->getModelClass($request->favorable_type);

        $exists = Favorite::where('user_id', Auth::id())
            ->where('favorable_id', $request->favorable_id)
            ->where('favorable_type', $model)
            ->exists();

        return response()->json(['is_favorite' => $exists]);
    }

    public function favoriteLessons()
    {
        $favorites = Favorite::where('user_id', Auth::id())
            ->where('favorable_type', Lesson::class)
            ->with('favorable')
            ->get()
            ->pluck('favorable')
            ->filter()
            ->values();

        return response()->json($favorites);
    }

    public function favoriteQuizzes()
    {
        $favorites = Favorite::where('user_id', Auth::id())
            ->where('favorable_type', Quiz::class)
            ->with('favorable')
            ->get()
            ->pluck('favorable')
            ->filter()
            ->values();

        return response()->json($favorites);
    }

    public function favoriteExams()
    {
        $favorites = Favorite::where('user_id', Auth::id())
            ->where('favorable_type', Exam::class)
            ->with('favorable')
            ->get()
            ->pluck('favorable')
            ->filter()
            ->values();

        return response()->json($favorites);
    }

    public function favoritePastExams()
    {
        $favorites = Favorite::where('user_id', Auth::id())
            ->where('favorable_type', PastExam::class) 
            ->with('favorable')
            ->get()
            ->pluck('favorable')
            ->filter()
            ->values();

        return response()->json($favorites);
    }

    public function allFavorites()
    {
        $favorites = Favorite::where('user_id', Auth::id())
            ->with('favorable')
            ->get()
            ->filter(fn($fav) => $fav->favorable !== null)
            ->map(function ($fav) {
                return [
                    'type' => class_basename($fav->favorable_type),
                    'data' => $fav->favorable
                ];
            })
            ->values();

        return response()->json($favorites);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'id'   => 'required|integer',
            'type' => 'required|string|in:lesson,quiz,PastExam,Exam',
        ]);

        $userId = Auth::id();
        $model = $this->getModelClass($request->type);

        $favorite = Favorite::where('user_id', $userId)
            ->where('favorable_id', $request->id)
            ->where('favorable_type', $model)
            ->first();

        if (!$favorite) {
            return response()->json([
                'message' => 'Item is not in favorites',
                'is_favorite' => false
            ]);
        }

        $favorite->delete();

        return response()->json([
            'message' => 'Removed from favorites',
            'is_favorite' => false
        ]);
    }
}