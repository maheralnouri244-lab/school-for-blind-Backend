<?php
namespace App\Http\Controllers;

use App\Events\AnnouncementCreated;
use App\Http\Requests\StoreAnnouncementRequest;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Laravel\Firebase\Facades\Firebase;

class AnnouncementController extends Controller
{
    public function store(StoreAnnouncementRequest $request)
    {
        $contentData = $request->input('content');

        if (is_array($contentData)) {
            $contentData = json_encode($contentData, JSON_UNESCAPED_UNICODE);
        }
        $announcement = Announcement::create([
            'type' => $request->input('type'),
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'level' => $request->input('level'),
            'target_audience' => $request->input('target_audience'),
            'class_id'=> $request->input('class_id'), 
            'teacher_id' => $request->input('teacher_id'),
        ]);
        event(new AnnouncementCreated($announcement));
       
        return response()->json([
            'message' => 'تم نشر الإعلان بنجاح للجميع',
            'data' => [
                'id' => $announcement->id,
                'type' => $announcement->type,
                'content' => $announcement->content,
                'title' => $announcement->title,
                'created_at' => $announcement->created_at,
                'updated_at' => $announcement->updated_at,
            ]
        ], 201);
    }

  public function index()
    {
        $query = Announcement::query();

        if (auth()->guard('student')->check()) {
            $student = auth()->guard('student')->user();
            $query->where('target_audience', 'student')
                  ->whereIn('level', [$student->level, 'all']);

        } elseif (auth()->guard('caregiver')->check()) {
            $caregiver = auth()->guard('caregiver')->user();
            $query->where('target_audience', 'caregiver')
                  ->whereIn('level', [$caregiver->level, 'all']);

        } elseif (auth()->guard('teacher')->check()) {
            $teacher = auth()->guard('teacher')->user();
            $query->where('target_audience', 'teacher')
                  ->whereIn('level', [$teacher->level, 'all']);

        } elseif (auth()->guard('admin')->check()) {
            $admin = auth()->guard('admin')->user();
            if ($admin->role === 'teacher') {
                $query->where('target_audience', 'teacher')
                      ->whereIn('level', [$admin->level, 'all']);
            }
        } else {
            return response()->json(['message' => 'غير مصرح لك برؤية الإعلانات.'], 401);
        }

        $announcements = $query->orderBy('created_at', 'desc')->get();

        if ($announcements->isEmpty()) {
            return response()->json([
                'message' => 'لا توجد إعلانات متاحة حالياً.'
            ], 200);
        }

        $processedAnnouncements = $announcements->map(function ($announcement) {
            
            if ($announcement->type === 'exam_schedule') {
                return [
                    'id'              => $announcement->id,
                    'type'            => $announcement->type,
                    'title'           => $announcement->title,
                    'target_audience' => $announcement->target_audience,
                    'level'           => $announcement->level,
                    'content'         => 'تم نشر ' . $announcement->title . ' أنقر هنا للإطلاع عليه',
                    'created_at'      => $announcement->created_at,
                    'updated_at'      => $announcement->updated_at,
                ];
            }

            return [
                'id'              => $announcement->id,
                'type'            => $announcement->type,
                'title'           => $announcement->title,
                'target_audience' => $announcement->target_audience,
                'level'           => $announcement->level,
                'content'         => $announcement->content, 
                'created_at'      => $announcement->created_at,
                'updated_at'      => $announcement->updated_at,
            ];
        });

        return response()->json($processedAnnouncements, 200);
    }}