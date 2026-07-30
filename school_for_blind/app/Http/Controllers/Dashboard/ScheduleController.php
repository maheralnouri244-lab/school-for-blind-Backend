<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function index()
    {
        $classes = Classes::all()->map(function ($class) {
            $latestDate = Schedule::where('class_id', $class->id)->max('updated_at');
            if ($latestDate) {
                $class->latest_schedules = Schedule::where('class_id', $class->id)
                    ->with(['subject', 'teacher'])
                    ->get();
                $class->schedule_date = $latestDate;
            } else {
                $class->latest_schedules = collect();
            }
            return $class;
        })->filter(function ($class) {
            return $class->latest_schedules->isNotEmpty();
        });

        $teachers = Teacher::where('status', 'approved')->get()->map(function ($teacher) {
            $latestDate = Schedule::where('teacher_id', $teacher->id)->max('updated_at');
            if ($latestDate) {
                $teacher->latest_schedules = Schedule::where('teacher_id', $teacher->id)
                    ->with(['subject', 'studentClass'])
                    ->get();
            } else {
                $teacher->latest_schedules = collect();
            }
            return $teacher;
        })->filter(function ($teacher) {
            return $teacher->latest_schedules->isNotEmpty();
        });

        return view('pages.schedules.index', compact('classes', 'teachers'));
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
        \Log::info('classes : ' . $classes);
        \Log::info('subjects : ' . $subjects);

        $teachers = Teacher::where('status', 'approved')
            ->where('level', $level)
            ->with(['classes'])
            ->get()
            ->map(function ($teacher) {
                $teacher->subject_list = $teacher->subjects()->get();
                return $teacher;
            });

        $classIds = $classes->pluck('id');
        $existingSchedules = Schedule::whereIn('class_id', $classIds)
            ->with(['subject', 'teacher'])
            ->get();

        return view('pages.schedules.workspace', compact('type', 'level', 'classes', 'subjects', 'teachers', 'existingSchedules'));
    }

    public function storeBulk(Request $request)
    {
        $request->validate([
            'schedules' => 'required|array',
        ]);

        $timeSlots = [
            1 => ['start' => '08:00:00', 'end' => '08:45:00'],
            2 => ['start' => '08:45:00', 'end' => '09:30:00'],
            3 => ['start' => '09:30:00', 'end' => '10:15:00'],
            4 => ['start' => '10:15:00', 'end' => '11:00:00'],
            5 => ['start' => '11:00:00', 'end' => '11:45:00'],
            6 => ['start' => '11:45:00', 'end' => '12:30:00'],
            7 => ['start' => '12:30:00', 'end' => '13:15:00'],
            8 => ['start' => '13:15:00', 'end' => '14:00:00'],
        ];

        DB::beginTransaction();
        try {
            foreach ($request->schedules as $slot) {
                if (empty($slot['subject_id']) || empty($slot['teacher_id'])) {
                    Schedule::where('class_id', $slot['class_id'])
                        ->where('day_of_week', $slot['day'])
                        ->where('period_number', $slot['period'])
                        ->delete();
                } else {
                    Schedule::updateOrCreate(
                        [
                            'class_id' => $slot['class_id'],
                            'day_of_week' => $slot['day'],
                            'period_number' => $slot['period'],
                        ],
                        [
                            'teacher_id' => $slot['teacher_id'],
                            'subject_id' => $slot['subject_id'],
                            'start_time' => $timeSlots[$slot['period']]['start'],
                            'end_time' => $timeSlots[$slot['period']]['end'],
                        ]
                    );
                }
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