<?php

namespace App\Http\Controllers;
use Carbon\Carbon; 
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
   public function teacherSchedule(Request $request)
    {
        $teacherId = $request->user()->id; 
        $latestDate = Schedule::where('teacher_id', $teacherId)->max('created_at');
        if (!$latestDate) {
            return response()->json(['status' => 'success', 'data' => []]);
        }

        $latestDateOnly = Carbon::parse($latestDate)->toDateString();

        $schedules = Schedule::where('teacher_id', $teacherId)
           ->whereDate('created_at', $latestDateOnly) 
        ->with(['subject', 'studentClass']) 
        ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week'); 

        return response()->json([
            'status' => 'success',
            'data' => $schedules
        ]);
    }

    public function studentSchedule(Request $request)
    {
        $student = $request->user(); 
$latestDate = Schedule::where('class_id', $student->class_id)->max('created_at');

        if (!$latestDate) {
            return response()->json(['status' => 'success', 'data' => []]);
        }

        $latestDateOnly = Carbon::parse($latestDate)->toDateString();
        $schedules = Schedule::where('class_id', $student->class_id)
          ->whereDate('created_at', $latestDateOnly)
           ->with(['subject', 'studentClass']) 
        ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        return response()->json([
            'status' => 'success',
            'data' => $schedules
        ]);
    }

   public function caregiverSchedule(Request $request)
    {
        $caregiver = $request->user(); 
        
        $children = $caregiver->students; 

        $allSchedules = [];

        foreach ($children as $child) {
            $latestDate = Schedule::where('class_id', $child->class_id)->max('created_at');

            if ($latestDate) {
                $latestDateOnly = Carbon::parse($latestDate)->toDateString();
                $childSchedule = Schedule::where('class_id', $child->class_id)
                    ->whereDate('created_at', $latestDateOnly)
                    ->with(['subject', 'studentClass']) 
                    ->orderBy('day_of_week')
                    ->orderBy('start_time')
                    ->get()
                    ->groupBy('day_of_week');

                $allSchedules[] = [
                    'student_id' => $child->id,
                    'student_name' => $child->fullname, 
                    'class_id' => $child->class_id,
                    'schedule' => $childSchedule
                ];
            }
        } 

        return response()->json([
            'status' => 'success',
            'data' => $allSchedules
        ]);
    }}