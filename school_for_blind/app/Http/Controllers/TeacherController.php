<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeacherLoginRequest;
use App\Http\Requests\TeacherRegisterRequest;
use App\Models\Exam;
use App\Models\ExamSubmission;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\Teacher;
use App\Traits\UploadFileTrait;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
class TeacherController extends Controller
{

    use UploadFileTrait;
    public function uploadfile($file, $folder)
    {
        if ($file && $file->isValid()) {
            return $file->store($folder, 'public');
        }
        return null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'data' => Teacher::paginate(15),
            'message' => 'قائمة بجميع المدرسين',
        ], 200);
    }

    public function register(TeacherRegisterRequest $request)
    {
        // $deviceFingerprint = md5($request->ip() . $request->header('User-Agent'));
        // $cacheKey = 'otp_verified_' . $request->phone . '_' . $deviceFingerprint;
        // \Log::info('' . $request->phone . '  ' . $cacheKey);
        // $isVerified = Cache::pull($cacheKey);
        // if (!$isVerified) {
        //     return response()->json([
        //         'message' => 'طلب غير مصرح به، أو انتهت مهلة التحقق.'
        //     ], 403);
        // }

        $path = $this->uploadfile($request->file('cv'), 'teahcers/CVS');
        \Log::info('path : ' . $path);

        $teacherdata = [
            'full_name' => $request->full_name,
            'password' => Hash::make($request->password),
            'subjects' => $request->subjects,
            'level' => $request->level,
            'cv_path' => $path,
            'status' => 'pending',
        ];
        $existingTeacher = clone Teacher::query();
        if (method_exists($existingTeacher, 'withTrashed')) {
            $existingTeacher = $existingTeacher->withTrashed();
        }
        $teacherRecord = $existingTeacher->where('phone', $request->phone)->first();
        if ($teacherRecord) {
            $wasDismissed = ($teacherRecord->status === 'dismissed');
            if (method_exists($teacherRecord, 'trashed') && $teacherRecord->trashed()) {
                $teacherRecord->restore();
            }
            $teacherdata['was_dismissed_before'] = $wasDismissed ? true : $teacherRecord->was_dismissed_before;
            $teacherRecord->update($teacherdata);
            $teacher = $teacherRecord;
        } else {
            $teacherdata['phone'] = $request->phone;
            $teacher = Teacher::create($teacherdata);
        }
        return response()->json([
            'message' => 'تم ارسال طلب لانشاء الحساب بنجاح',
            'data' => $teacher
        ], 201);
    }
    public function login(TeacherLoginRequest $request)
    {
        $teacher = Teacher::where('phone', $request->phone)->first();

        if (!$teacher) {
            return response()->json([
                'message' => 'لا يوجد حساب بهذا الرقم'
            ], 404);
        }

        if (!Hash::check($request->password, $teacher->password)) {
            return response()->json([
                'message' => 'كلمة المرور خاطئة'
            ], 401);
        }

        if ($teacher->status == 'pending') {
            return response()->json(['message' => 'انتظر حتى يوافق احد المشرفين على حسابك'], 403);
        }

        if ($teacher->status == 'dismissed') {
            return response()->json(['message' => 'عذراً، تم فصل حسابك نهائياً من النظام ولا يمكنك تسجيل الدخول.'], 403);
        }

        if ($teacher->status == 'rejected') {
            return response()->json(['message' => 'لقد تم رفض طلب انضمامك للنظام.'], 403);
        }

        try {
            $token = $teacher->createToken('api-token')->plainTextToken;
            $teacher->fcm_token = $request->fcm_token;
            $teacher->save();
        } catch (Exception $e) {
            return response()->json(['message' => 'فشل في توليد التوكن حاول مجددا'], 500);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $teacher
        ]);
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }


    public function info()
    {
        $teacher = auth()->user();

        $teacher->cv_link = asset('storage/' . $teacher->cv_path);

        $teacher->load([
            'classes' => function ($query) {
                $query->select('classes.id', 'classes.name');
            },
            'subjects' => function ($query) {
                $query->select('subjects.id', 'subjects.name');
            }
        ]);

        // $teacher->classes->makeHidden(['pivot', 'created_at', 'updated_at', 'level', 'number']);
        // $teacher->subjects->makeHidden(['pivot', 'created_at', 'updated_at', 'grade_level', 'number_of_lessons', 'total_lessons', 'deleted_at']);

        return response()->json([
            'message' => 'بيانات المدرس',
            'data' => $teacher
        ], 200);
    }

    // public function showCv()
    // {
    //     $teacher = auth()->user();

    //     if (!$teacher->cv_path || !Storage::exists($teacher->cv_path)) {
    //         return response()->json(['message' => 'ملف السيرة الذاتية غير موجود'], 404);
    //     }

    //     return Storage::response($teacher->cv_path);
    // }

    public function getAllSubjectsStats(Request $request)
    {
        $teacher = $request->user();
        $subjects = $teacher->subjects()->get();

        if ($subjects->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'هذا الأستاذ غير مرتبط بأي مادة حالياً.',
                'data' => []
            ], 200);
        }

        $allSubjectsResponse = [];

        foreach ($subjects as $subject) {

            $latestQuiz = Quiz::where('subject_id', $subject->id)
                ->where('teacher_id', $teacher->id)->latest()->first();
            $latestExam = Exam::where('subject_id', $subject->id)
                ->where('teacher_id', $teacher->id)
                ->where('is_published', true)
                ->where(function ($query) {

                    $query
                        ->whereNull('exam_date')
                        ->Where('exam_date', '<=', now());
                })
                ->latest('exam_date')
                ->first();

            $latestAssessment = null;
            $assessmentType = null;

            if ($latestQuiz && $latestExam) {
                if ($latestQuiz->created_at > $latestExam->created_at) {
                    $latestAssessment = $latestQuiz;
                    $assessmentType = 'quiz';
                } else {
                    $latestAssessment = $latestExam;
                    $assessmentType = 'exam';
                }
            } elseif ($latestQuiz) {
                $latestAssessment = $latestQuiz;
                $assessmentType = 'quiz';
            } elseif ($latestExam) {
                $latestAssessment = $latestExam;
                $assessmentType = 'exam';
            }

            $subjectStats = [
                'subject_id' => $subject->id,
                'subject_name' => $subject->name,
                'has_assessments' => false,
                'latest_assessment' => null,
                'critical_assessments' => []
            ];

            if ($latestAssessment && $latestAssessment->totalmark > 0) {
                $subjectStats['has_assessments'] = true;

                $submissions = $assessmentType === 'quiz'
                    ? QuizSubmission::where('quiz_id', $latestAssessment->id)
                        ->where('status', 'graded')
                        ->get()
                    : ExamSubmission::where('exam_id', $latestAssessment->id)
                        ->where('status', 'approved')
                        ->get();

                $totalStudents = $submissions->count();
                $passedCount = 0;
                $failedCount = 0;

                $total = $latestAssessment->totalmark;
                $step = $total / 5;

                $b1 = "0 - " . round($step, 1);
                $b2 = ">" . round($step, 1) . " - " . round($step * 2, 1);
                $b3 = ">" . round($step * 2, 1) . " - " . round($step * 3, 1);
                $b4 = ">" . round($step * 3, 1) . " - " . round($step * 4, 1);
                $b5 = ">" . round($step * 4, 1) . " - " . round($total, 1);

                $brackets = [$b1 => 0, $b2 => 0, $b3 => 0, $b4 => 0, $b5 => 0];

                foreach ($submissions as $sub) {
                    $score = $assessmentType === 'quiz' ? $sub->total_score : $sub->score;

                    if ($score < ($step * 2)) {
                        $failedCount++;
                    } else {
                        $passedCount++;
                    }

                    if ($score <= $step) {
                        $brackets[$b1]++;
                    } elseif ($score <= $step * 2) {
                        $brackets[$b2]++;
                    } elseif ($score <= $step * 3) {
                        $brackets[$b3]++;
                    } elseif ($score <= $step * 4) {
                        $brackets[$b4]++;
                    } else {
                        $brackets[$b5]++;
                    }
                }

                $bracketsPercentages = [];
                if ($totalStudents > 0) {
                    foreach ($brackets as $key => $count) {
                        $bracketsPercentages[$key] = round(($count / $totalStudents) * 100, 2);
                    }
                } else {
                    $bracketsPercentages = [$b1 => 0, $b2 => 0, $b3 => 0, $b4 => 0, $b5 => 0];
                }

                $subjectStats['latest_assessment'] = [
                    'id' => $latestAssessment->id,
                    'type' => $assessmentType,
                    'title' => $assessmentType === 'exam' ? $latestAssessment->title : 'Quiz #' . $latestAssessment->id,
                    'total_mark' => $total,
                    'total_students_took_it' => $totalStudents,
                    'passed_count' => $passedCount,
                    'failed_count' => $failedCount,
                    'score_brackets_percentages' => $bracketsPercentages,
                ];
            }

            $lowPerformingItems = [];

            $allQuizzes = Quiz::where('subject_id', $subject->id)->where('teacher_id', $teacher->id)->get();
            foreach ($allQuizzes as $quiz) {
                if ($quiz->totalmark > 0) {
                    $subs = QuizSubmission::where('quiz_id', $quiz->id)
                        ->where('status', 'graded')
                        ->get();
                    if ($subs->count() > 0) {
                        $lowScorers = $subs->filter(fn($s) => ($s->total_score / $quiz->totalmark) * 100 < 40)->count();
                        $failRate = ($lowScorers / $subs->count()) * 100;
                        if ($failRate > 60) {
                            $lowPerformingItems[] = [
                                'id' => $quiz->id,
                                'type' => 'quiz',
                                'fail_rate' => round($failRate, 2)
                            ];
                        }
                    }
                }
            }

            $allExams = Exam::where('subject_id', $subject->id)->where('teacher_id', $teacher->id)->get();
            foreach ($allExams as $exam) {
                if ($exam->totalmark > 0) {
                    $subs = ExamSubmission::where('exam_id', $exam->id)
                        ->where('status', 'approved')
                        ->get();
                    if ($subs->count() > 0) {
                        $lowScorers = $subs->filter(fn($s) => ($s->score / $exam->totalmark) * 100 < 40)->count();
                        $failRate = ($lowScorers / $subs->count()) * 100;
                        if ($failRate > 60) {
                            $lowPerformingItems[] = [
                                'id' => $exam->id,
                                'type' => 'exam',
                                'title' => $exam->title,
                                'fail_rate' => round($failRate, 2)
                            ];
                        }
                    }
                }
            }

            $subjectStats['critical_assessments'] = $lowPerformingItems;

            $allSubjectsResponse[] = $subjectStats;
        }

        return response()->json([
            'status' => 'success',
            'data' => $allSubjectsResponse
        ], 200);
    }
}
