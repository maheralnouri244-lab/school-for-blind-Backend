<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Room;
use App\Models\QuizSubmission;
use App\Models\ExamSubmission;
use App\Models\Punishable;
use App\Models\AbsenceExcuse;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ParentReportService
{
 public function getDailyReport(int $studentId, string $date): array
 {
  $formattedDate = Carbon::parse($date)->toDateString();
  $student = Student::findOrFail($studentId);

  return [
   'date' => $formattedDate,
   'attendance' => $this->getDailyAttendance($student, $formattedDate),
   'grades_today' => $this->getDailyGrades($studentId, $formattedDate),
   'punishments' => $this->getDailyPunishments($studentId, $formattedDate),
  ];
 }

 private function getDailyPunishments(int $studentId, string $date): array
 {
  return Punishable::where('punishable_id', $studentId)
   ->where('punishable_type', Student::class)
   ->whereDate('created_at', $date)
   ->with('punishment')
   ->get()
   ->map(function ($punishable) {
    return [
     'name' => $punishable->punishment->name,
     'level' => $punishable->punishment->level,
     'description' => $punishable->punishment->description,
    ];
   })->toArray();
 }

 private function getDailyGrades(int $studentId, string $date): array
 {
  $quizzes = QuizSubmission::where('student_id', $studentId)
   ->where('status', 'graded')
   ->whereDate('updated_at', $date)
   ->with('quiz.lesson.subject')
   ->get()
   ->map(function ($submission) {
    return [
     'type' => 'quiz',
     'title' => $submission->quiz->lesson->title ?? 'كويز',
     'subject_name' => $submission->quiz->lesson->subject->name ?? '',
     'score' => $submission->total_score,
    ];
   });

  $exams = ExamSubmission::where('student_id', $studentId)
   ->where('status', 'approved')
   ->whereDate('updated_at', $date)
   ->with('exam.subject')
   ->get()
   ->map(function ($submission) {
    return [
     'type' => 'exam',
     'title' => $submission->exam->title ?? 'اختبار',
     'subject_name' => $submission->exam->subject->name ?? '',
     'score' => $submission->score,
    ];
   });

  return collect($quizzes)->merge($exams)->toArray();
 }

 private function getDailyAttendance(Student $student, string $date): array
 {
  $rooms = Room::where('class_id', $student->class_id)
   ->whereDate('started_at', $date)
   ->where('status', 'ended')
   ->get();

  return $rooms->map(function ($room) use ($student) {
   $totalDurationMinutes = Carbon::parse($room->started_at)->diffInMinutes($room->ended_at);

   $attendance = Attendance::where('room_id', $room->id)
    ->where('participant_id', $student->id)
    ->where('participant_type', Student::class)
    ->first();

   $attendedMinutes = $attendance ? round($attendance->duration_seconds / 60) : 0;
   $hasAttended = $totalDurationMinutes > 0 ? ($attendedMinutes / $totalDurationMinutes) >= 0.8 : false;

   $excuse = AbsenceExcuse::where('student_id', $student->id)
    ->where('room_id', $room->id)
    ->first();

   return [
    'room_id' => $room->id,
    'room_name' => $room->room_name ?? 'درس عام',
    'is_attended' => $hasAttended,
    'total_room_minutes' => $totalDurationMinutes,
    'student_presence_minutes' => $attendedMinutes,
    'excuse_status' => $excuse ? $excuse->status : null,
    'can_excuse' => !$hasAttended && !$excuse,
   ];
  })->toArray();
 }

 public function getMonthlyReport(int $studentId, int $month, int $year): array
 {
  $student = Student::findOrFail($studentId);

  $rooms = Room::where('class_id', $student->class_id)
   ->whereMonth('started_at', $month)
   ->whereYear('started_at', $year)
   ->where('status', 'ended')
   ->get();

  $totalRooms = $rooms->count();
  $attendances = Attendance::whereIn('room_id', $rooms->pluck('id'))
   ->where('participant_id', $studentId)
   ->where('participant_type', Student::class)
   ->get()->keyBy('room_id');

  $attendedRoomsCount = $rooms->filter(function ($room) use ($attendances) {
   $totalDuration = Carbon::parse($room->started_at)->diffInMinutes($room->ended_at);
   if ($totalDuration <= 0)
    return false;
   $attendance = $attendances->get($room->id);
   $attendedMinutes = $attendance ? round($attendance->duration_seconds / 60) : 0;
   return ($attendedMinutes / $totalDuration) >= 0.8;
  })->count();

  $attendancePercentage = $totalRooms > 0 ? round(($attendedRoomsCount / $totalRooms) * 100) : 100;

  $totalQuizzes = DB::table('quizzes')
   ->join('lessons', 'quizzes.lesson_id', '=', 'lessons.id')
   ->where('lessons.class_id', $student->class_id)
   ->whereMonth('quizzes.created_at', $month)
   ->whereYear('quizzes.created_at', $year)
   ->count();

  $solvedQuizzesCount = QuizSubmission::where('student_id', $studentId)
   ->whereMonth('created_at', $month)
   ->whereYear('created_at', $year)
   ->count();

  $neglectedQuizzes = max(0, $totalQuizzes - $solvedQuizzesCount);

  $classSubjectIds = DB::table('lessons')
   ->where('class_id', $student->class_id)
   ->pluck('subject_id')
   ->unique();

  $totalExams = DB::table('exams')
   ->whereIn('subject_id', $classSubjectIds)
   ->where('is_published', true)
   ->whereMonth('exam_date', $month)
   ->whereYear('exam_date', $year)
   ->whereRaw('DATE_ADD(exam_date, INTERVAL duration_minutes MINUTE) < ?', [Carbon::now()])
   ->count();

  $solvedExamsCount = ExamSubmission::where('student_id', $studentId)
   ->whereMonth('created_at', $month)
   ->whereYear('created_at', $year)
   ->count();
  $neglectedExams = max(0, $totalExams - $solvedExamsCount);

  $quizAverages = QuizSubmission::where('quiz_submissions.student_id', $studentId)
   ->where('quiz_submissions.status', 'graded')
   ->whereMonth('quiz_submissions.updated_at', $month)
   ->whereYear('quiz_submissions.updated_at', $year)
   ->join('quizzes', 'quiz_submissions.quiz_id', '=', 'quizzes.id')
   ->join('lessons', 'quizzes.lesson_id', '=', 'lessons.id')
   ->join('subjects', 'lessons.subject_id', '=', 'subjects.id')
   ->select('subjects.name as subject_name', DB::raw('AVG(quiz_submissions.total_score) as avg_score'))
   ->groupBy('subjects.id', 'subjects.name')
   ->get();

  $examAverages = ExamSubmission::where('exam_submissions.student_id', $studentId)
   ->where('exam_submissions.status', 'approved')
   ->whereMonth('exam_submissions.updated_at', $month)
   ->whereYear('exam_submissions.updated_at', $year)
   ->join('exams', 'exam_submissions.exam_id', '=', 'exams.id')
   ->join('subjects', 'exams.subject_id', '=', 'subjects.id')
   ->select('subjects.name as subject_name', DB::raw('AVG(exam_submissions.score) as avg_score'))
   ->groupBy('subjects.id', 'subjects.name')
   ->get();

  $weakSubjects = collect($quizAverages)->merge($examAverages)->groupBy('subject_name')
   ->map(fn($items) => $items->avg('avg_score'))->filter(fn($avg) => $avg < 60)->keys()->toArray();

  return [
   'month' => $month,
   'year' => $year,
   'attendance_percentage' => $attendancePercentage,
   'solved_quizzes_count' => $solvedQuizzesCount,
   'neglected_quizzes' => $neglectedQuizzes,
   'neglected_exams' => $neglectedExams,
   'weak_subjects' => $weakSubjects,
  ];
 }

 public function getYearlyReport($studentId, $year)
 {
  $latestExamsSubmissions = ExamSubmission::where('student_id', $studentId)
   ->where('status', 'approved')
   ->join('exams', 'exam_submissions.exam_id', '=', 'exams.id')
   ->whereYear('exams.exam_date', $year)
   ->select(
    'exam_submissions.score as student_score',
    'exams.subject_id',
    'exams.totalmark as max_score'
   )
   ->orderBy('exam_submissions.created_at', 'desc')
   ->get()
   ->unique('subject_id');

  $totalStudentScore = $latestExamsSubmissions->sum('student_score');
  $totalMaxScore = $latestExamsSubmissions->sum(function ($submission) {
   return $submission->max_score ?? 100;
  });

  $finalAveragePercentage = $totalMaxScore > 0 ? round(($totalStudentScore / $totalMaxScore) * 100, 2) : 0;

  return [
   'academic_year' => $year,
   'total_student_score' => $totalStudentScore,
   'total_max_score' => $totalMaxScore,
   'final_average_percentage' => $finalAveragePercentage,
  ];
 }

 public function getSubjectGrades($studentId, $subjectId)
 {
  $student = Student::findOrFail($studentId);
  $quizzes = QuizSubmission::where('student_id', $studentId)
   ->where('status', 'graded')
   ->join('quizzes', 'quiz_submissions.quiz_id', '=', 'quizzes.id')
   ->join('lessons', 'quizzes.lesson_id', '=', 'lessons.id')
   ->where('lessons.subject_id', $subjectId)
   ->select(
    'lessons.title as title',
    'quiz_submissions.total_score as student_score',
    'quiz_submissions.teacher_assigned_mark',
    'quiz_submissions.updated_at as graded_at'
   )
   ->orderBy('quiz_submissions.updated_at', 'desc')
   ->get();

  $exams = ExamSubmission::where('student_id', $studentId)
   ->where('status', 'approved')
   ->join('exams', 'exam_submissions.exam_id', '=', 'exams.id')
   ->where('exams.subject_id', $subjectId)
   ->select(
    'exams.title',
    'exams.totalmark as max_score',
    'exam_submissions.score as student_score',
    'exam_submissions.updated_at as graded_at'
   )
   ->orderBy('exam_submissions.updated_at', 'desc')
   ->get();

  return [
   'student_name' => $student->fullname,
   'quizzes' => $quizzes,
   'exams' => $exams,
  ];
 }
}