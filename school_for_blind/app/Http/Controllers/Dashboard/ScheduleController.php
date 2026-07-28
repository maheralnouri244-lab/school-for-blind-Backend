<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function index()
    {
        $classSchedules = Announcement::whereIn('type', ['school_timetable', 'exam_schedule'])
            ->whereNotNull('class_id')
            ->with('class')
            ->latest('created_at')
            ->get()
            ->unique('class_id');

        $teachers = Teacher::where('status', 'approved')->get();

        $allLatestSchedules = $classSchedules->map(function ($sched) {
            return [
                'class_id' => $sched->class_id,
                'class_name' => $sched->class ? $sched->class->name . ' (شعبة ' . $sched->class->number . ')' : '',
                'content' => is_string($sched->content) ? json_decode($sched->content, true) : $sched->content
            ];
        });

        return view('pages.schedules.index', compact('classSchedules', 'teachers', 'allLatestSchedules'));
    }

    public function create()
    {
        return view('pages.schedules.create');
    }

    public function workspace(Request $request)
    {
        $type = $request->query('type');
        $level = $request->query('level');

        if (!$type || !$level) {
            return redirect()->route('dashboard.schedules.create')->with('error', 'الرجاء تحديد نوع الجدول والمرحلة.');
        }

        $classes = Classes::where('level', $level)->get();
        $subjects = Subject::where('grade_level', $level)->get();
        $teachers = Teacher::where('status', 'approved')
            ->where('level', $level)
            ->with(['subjects:id,name', 'classes:id'])
            ->get();

        return view('pages.schedules.workspace', compact('type', 'level', 'classes', 'subjects', 'teachers'));
    }

    public function storeBulk(Request $request)
    {
        $request->validate([
            'type' => 'required|in:school_timetable,exam_schedule',
            'level' => 'required|in:ninth,twelfth',
            'target_audience' => 'required|in:student,teacher,parent,all',
            'schedules' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->schedules as $classId => $scheduleData) {
                $contentJson = json_encode($scheduleData, JSON_UNESCAPED_UNICODE);
                Announcement::updateOrCreate(
                    [
                        'type' => $request->type,
                        'class_id' => $classId,
                    ],
                    [
                        'title' => 'برنامج الدوام الأسبوعي - ' . ($request->level == 'ninth' ? 'الصف التاسع' : 'البكالوريا'),
                        'level' => $request->level,
                        'target_audience' => $request->target_audience,
                        'content' => $contentJson,
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'تم حفظ ونشر جميع الجداول بنجاح!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء الحفظ: ' . $e->getMessage()
            ], 500);
        }
    }
}