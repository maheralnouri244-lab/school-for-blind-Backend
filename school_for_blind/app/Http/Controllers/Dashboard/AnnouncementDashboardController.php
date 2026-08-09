<?php

namespace App\Http\Controllers\Dashboard;

use App\Events\AnnouncementCreated;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementDashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::query();

        if ($request->filled('target_audience') && $request->target_audience !== 'all') {
            $query->whereIn('target_audience', [$request->target_audience, 'all']);
        }

        if ($request->filled('level') && $request->level !== 'all') {
            $query->whereIn('level', [$request->level, 'all']);
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $announcements = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json($announcements, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required',
            'type' => 'required|string',
            'target_audience' => 'required|array|min:1',
            'level' => 'required|array|min:1',
        ]);

        $audiences = $request->input('target_audience');
        $levels = $request->input('level');

        $finalAudiences = count($audiences) === 3 ? ['all'] : $audiences;

        $finalLevels = count($levels) === 2 ? ['all'] : $levels;

        $contentData = $request->input('content');
        if (is_array($contentData)) {
            $contentData = json_encode($contentData, JSON_UNESCAPED_UNICODE);
        }

        foreach ($finalAudiences as $audience) {
            foreach ($finalLevels as $level) {
                $announcement = Announcement::create([
                    'type' => $request->input('type'),
                    'title' => $request->input('title'),
                    'content' => $contentData,
                    'level' => $level,
                    'target_audience' => $audience,
                    'class_id' => $request->input('class_id'),
                    'teacher_id' => $request->input('teacher_id'),
                ]);

                event(new AnnouncementCreated($announcement));
            }
        }

        return response()->json([
            'message' => 'تم نشر الإعلان بنجاح وتوجيهه للفئات المحددة'
        ], 201);
    }
}