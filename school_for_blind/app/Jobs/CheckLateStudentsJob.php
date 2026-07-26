<?php

namespace App\Jobs;

use App\Models\Room;
use App\Models\StudentSummary;
use App\Models\Punishment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckLateStudentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $room;

    /**
     * Create a new job instance.
     */
    public function __construct(Room $room)
    {
        $this->room = $room;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->room->load(['schoolClass.students', 'participants']);
        $presentStudentIds = $this->room->participants->pluck('student_id')->toArray();
        $allClassStudents = $this->room->schoolClass->students;
        $absentStudents = $allClassStudents->whereNotIn('id', $presentStudentIds);
        if ($absentStudents->isEmpty()) {
            return;
        }
        $latePunishment = Punishment::where('name', 'Warning')
            ->where('description', 'like', '%تأخر%')
            ->first();

        $date = Carbon::today()->toDateString();
        foreach ($absentStudents as $student) {
            $reportData = [
                'attendance' => [],
                'grades_today' => [],
                'punishments' => [
                    [
                        'id' => $latePunishment->id ?? null,
                        'name' => $latePunishment->name ?? 'Warning',
                        'level' => $latePunishment->level ?? 1,
                        'description' => $latePunishment->description ?? 'إنذار أكاديمي تأخر عن درس يقام حاليا)',
                        'issued_at' => now()->toDateTimeString(),
                        'room_name' => $this->room->room_name
                    ]
                ]
            ];
            StudentSummary::create([
                'student_id' => $student->id,
                'type' => 'late_warning',
                'reference_date' => $date,
                'data' => json_encode($reportData)
            ]);
        }
    }
}