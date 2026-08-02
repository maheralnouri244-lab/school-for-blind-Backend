<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Caregiver;
use App\Models\Classes;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherAvailability;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Conversation;
use App\Models\Admin;

class UserManagerController extends Controller
{
    public function index(Request $request)
    {
        $classes = Classes::all();
        return view('pages.users.index', compact('classes'));
    }

    public function filterUsers(Request $request)
    {
        $type = $request->type ?? 'student';
        $status = $request->status;
        $level = $request->level;
        $classId = $request->class_id;
        $search = $request->search;

        if ($type === 'student') {
            $query = Student::with('class');
            if ($status)
                $query->where('status', $status);
            if ($level)
                $query->byLevel($level);
            if ($classId)
                $query->byClass($classId);
            if ($search)
                $query->search($search);

            $users = $query->latest()->paginate(15);
            $html = view('pages.users.partials.student_table', compact('users'))->render();
        } elseif ($type === 'caregiver') {
            $query = Caregiver::with('students.class');

            if ($status || $level || $classId) {
                $query->whereHas('students', function ($q) use ($status, $level, $classId) {
                    if ($status)
                        $q->where('status', $status);
                    if ($level)
                        $q->byLevel($level);
                    if ($classId)
                        $q->byClass($classId);
                });
            }

            if ($search) {
                $query->where('phone', 'like', "%{$search}%")
                    ->orWhereHas('students', function ($q) use ($search) {
                        $q->search($search);
                    });
            }

            $users = $query->latest()->paginate(15);
            $html = view('pages.users.partials.caregiver_table', compact('users'))->render();
        } else {
            $query = Teacher::with('classes');
            if ($status)
                $query->where('status', $status);
            if ($level)
                $query->byLevel($level);
            if ($search)
                $query->search($search);
            if ($classId) {
                $query->whereHas('classes', function ($q) use ($classId) {
                    $q->where('classes.id', $classId);
                });
            }

            $users = $query->latest()->paginate(15);
            $html = view('pages.users.partials.teacher_table', compact('users'))->render();
        }

        return response()->json([
            'html' => $html,
            'pagination' => (string) $users->links()
        ]);
    }

    public function getUserDetails($type, $id)
    {
        if ($type === 'teacher') {
            $user = Teacher::with(['subjects', 'classes', 'availabilities'])->findOrFail($id);
            $html = view('pages.users.partials.teacher_details', compact('user'))->render();
            return response()->json(['html' => $html, 'name' => $user->full_name, 'level' => $user->level]);
        } elseif ($type === 'caregiver') {
            $user = Caregiver::with('students.class')->findOrFail($id);
            $html = view('pages.users.partials.caregiver_details', compact('user'))->render();
            return response()->json(['html' => $html, 'name' => 'ولي الأمر: ' . $user->phone, 'level' => null]);
        } else {
            $user = Student::with('class')->findOrFail($id);
            $html = view('pages.users.partials.student_details', compact('user'))->render();
            return response()->json(['html' => $html, 'name' => $user->fullname, 'level' => $user->level]);
        }
    }

    public function updateStatus(Request $request, $type, $id, WhatsAppService $whatsApp)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        if ($type === 'student' && $request->status === 'approved') {
            $request->validate([
                'class_id' => 'required|integer|exists:classes,id'
            ]);
        }

        $model = $type === 'teacher' ? Teacher::class : Student::class;
        $user = $model::findOrFail($id);

        DB::transaction(function () use ($request, $type, $user, $whatsApp) {
            $user->status = $request->status;

            if ($request->status === 'approved' && $type === 'student') {
                $password = Str::lower(Str::random(10));
                $caregiver = Caregiver::firstOrCreate(
                    ['phone' => $user->parent_phone],
                    ['password' => Hash::make($password)]
                );
                $user->parent_id = $caregiver->id;
                $user->class_id = $request->class_id;
                $whatsApp->sendStudentinfo($user->phone, $user->fullname, $user->parent_phone, $password);
            }
            $user->save();
        });

        return response()->json(['success' => true]);
    }

    public function fetchDataByLevel(Request $request)
    {
        $level = $request->level;
        $classes = Classes::where('level', $level)->get();
        $subjects = Subject::where('grade_level', $level)->get();

        return response()->json([
            'classes' => $classes,
            'subjects' => $subjects
        ]);
    }

    public function teacherSetupForm($id)
    {
        $teacher = Teacher::with(['classes', 'subjects', 'availabilities'])->findOrFail($id);
        $activeLevel = in_array($teacher->level, ['ninth', 'twelfth']) ? $teacher->level : 'ninth';
        $classes = Classes::where('level', $activeLevel)->get();
        $subjects = Subject::where('grade_level', $activeLevel)->get();
        \Log::info($subjects);
        $availabilities = $teacher->availabilities->groupBy('day_of_week')->map(function ($day) {
            return $day->pluck('period_number')->toArray();
        })->toArray();
        return view('pages.users.teacher_setup', compact('teacher', 'classes', 'subjects', 'availabilities', 'activeLevel'));
    }

    public function getUserPunishments($type, $id)
    {
        $model = $type === 'teacher' ? Teacher::class : Student::class;
        $user = $model::with([
            'punishments' => function ($q) {
                $q->withPivot(['id', 'admin_id', 'expires_at', 'created_at']);
            }
        ])->findOrFail($id);

        $html = view('pages.users.partials.punishments_list', compact('user', 'type'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    public function completeTeacherSetup(Request $request, $id, WhatsAppService $whatsApp)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|unique:teachers,phone,' . $id,
            'level' => 'required|in:ninth,twelfth',
            'stripe_account_id' => 'nullable|string|max:255',
            'classes' => 'required|array|min:1',
            'classes.*' => 'exists:classes,id',
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'exists:subjects,id',
            'prices' => 'required|array',
            'availabilities' => 'nullable|array'
        ]);

        $teacher = Teacher::findOrFail($id);

        DB::transaction(function () use ($request, $teacher, $whatsApp) {
            $selectedSubjects = Subject::whereIn('id', $request->subjects)->get();

            $teacher->update([
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'level' => $request->level,
                'stripe_account_id' => $request->stripe_account_id,
                'status' => 'approved',
                'subjects' => implode(', ', $selectedSubjects->pluck('name')->toArray()),
            ]);

            $teacher->classes()->sync($request->classes);

            $syncData = [];
            foreach ($request->subjects as $subjectId) {
                $syncData[$subjectId] = [
                    'price_for_lesson' => $request->prices[$subjectId] ?? 0
                ];
            }
            $teacher->subjects()->sync($syncData);

            $teacher->availabilities()->delete();

            if ($request->has('availabilities')) {
                $availRecords = [];
                foreach ($request->availabilities as $day => $periods) {
                    foreach ($periods as $period) {
                        $availRecords[] = [
                            'teacher_id' => $teacher->id,
                            'day_of_week' => $day,
                            'period_number' => $period,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }
                TeacherAvailability::insert($availRecords);
            }

            foreach ($selectedSubjects as $subject) {
                $channel = Conversation::firstOrCreate([
                    'type' => 'channel',
                    'teacher_id' => $teacher->id,
                    'subject_id' => $subject->id,
                ], [
                    'name' => 'قناة مادة ' . $subject->name . ' - ' . $teacher->full_name,
                ]);

                Conversation::firstOrCreate([
                    'type' => 'discussion',
                    'teacher_id' => $teacher->id,
                    'subject_id' => $subject->id,
                    'parent_id' => $channel->id,
                ], [
                    'name' => 'مناقشة مادة ' . $subject->name . ' ' . $subject->level . ' - ' . $teacher->full_name,
                ]);
            }

            $admins = Admin::whereIn('role', ['Super Admin', 'Academic Manager'])->get();
            foreach ($admins as $admin) {
                Conversation::firstOrCreate([
                    'type' => 'teacher_admin',
                    'teacher_id' => $teacher->id,
                    'admin_id' => $admin->id,
                ], [
                    'name' => 'محادثة الإدارة - ' . $admin->role . $teacher->full_name,
                ]);
            }

            $whatsApp->sendTeacherinfo($teacher->phone, $teacher->full_name);
        });

        return redirect()->route('dashboard.users.index')->with('success', 'تم اعتماد وتفعيل بيانات المعلم بنجاح.');
    }

    public function updateStudent(Request $request, $id)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'fathersname' => 'nullable|string|max:255',
            'phone' => 'required|string|unique:students,phone,' . $id,
            'parent_phone' => 'required|string',
            'level' => 'required|in:ninth,twelfth',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        $student = Student::findOrFail($id);
        $student->update($request->only(['fullname', 'fathersname', 'phone', 'parent_phone', 'level']));

        return response()->json(['success' => true, 'message' => 'تم تحديث بيانات الطالب بنجاح']);
    }
}