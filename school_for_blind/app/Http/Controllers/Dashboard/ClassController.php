<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\Absence;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $classes = Classes::withCount(['students', 'teachers'])->get();
        return view('pages.classes.index', compact('classes'));
    }

    public function show($id)
    {
        $class = Classes::with(['students', 'teachers.subjects'])->findOrFail($id);

        $allClasses = Classes::where('id', '!=', $id)
            ->where('level', $class->level)
            ->get();

        $availableTeachers = Teacher::where('level', $class->level)
            ->whereDoesntHave('classes', function ($query) use ($id) {
                $query->where('classes.id', $id);
            })->get();

        return view('pages.classes.show', compact('class', 'allClasses', 'availableTeachers'));
    }

    public function transferStudent(Request $request, $class_id)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'new_class_id' => 'required|exists:classes,id',
        ]);

        $student = Student::findOrFail($request->student_id);

        if ($student->class_id != $class_id) {
            return redirect()->back()->with('error', 'الطالب لا ينتمي لهذه الشعبة.');
        }

        $newClass = Classes::findOrFail($request->new_class_id);

        if ($newClass->id == $class_id) {
            return redirect()->back()->with('error', 'لا يمكن نقل الطالب إلى نفس الشعبة.');
        }

        if ($student->level !== $newClass->level) {
            return redirect()->back()->with('error', 'لا يمكن نقل الطالب إلى شعبة بمستوى دراسي مختلف.');
        }

        $student->class_id = $request->new_class_id;
        $student->save();

        return redirect()->back()->with('success', 'تم نقل الطالب بنجاح.');
    }

    public function attachTeacher(Request $request, $id)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
        ]);
        $class = Classes::findOrFail($id);
        $class->teachers()->syncWithoutDetaching([$request->teacher_id]);
        return redirect()->back()->with('success', 'تم إضافة الأستاذ إلى الشعبة بنجاح.');
    }
    public function detachTeacher($class_id, $teacher_id)
    {
        $class = Classes::findOrFail($class_id);
        $class->teachers()->detach($teacher_id);
        return redirect()->back()->with('success', 'تم إلغاء ربط الأستاذ من الشعبة.');
    }

    public function suggestPoints(Request $request, $class_id)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'reason' => 'required|string|max:500',
            'suggested_points' => 'nullable|integer|min:1',
        ]);

        \App\Models\PointSuggestion::create([
            'student_id' => $request->student_id,
            'reason' => $request->reason,
            'suggested_points' => $request->suggested_points,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'تم إرسال مقترح إضافة النقاط بنجاح بانتظار موافقة الإدارة.');
    }

    public function getStudentExcuses($student_id)
    {
        $excuses = \App\Models\AbsenceExcuse::with(['room', 'caregiver'])
            ->where('student_id', $student_id)
            ->latest()
            ->get();

        $html = view('pages.classes.partials.excuses_list', compact('excuses'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    public function updateExcuseStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $excuse = \App\Models\AbsenceExcuse::findOrFail($id);
        $excuse->update([
            'status' => $request->status
        ]);

        return response()->json(['success' => true, 'message' => 'تم تحديث حالة التبرير بنجاح.']);
    }

    public function allExcuses(Request $request)
    {
        $query = \App\Models\AbsenceExcuse::with(['student.class', 'caregiver', 'room']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('fullname', 'like', '%' . $request->search . '%');
            });
        }

        $excuses = $query->latest()->paginate(15);

        return view('pages.excuses.index', compact('excuses'));
    }

    public function getAllAbsences($studentId, Request $request)
    {
        $query = Absence::where('student_id', $studentId)->with(['room', 'room.class']);

        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('date', [$request->from_date, $request->to_date]);
        }

        $absences = $query->latest('date')->get();

        return response()->json([
            'status' => 'success',
            'data' => $absences
        ]);
    }

    public function studentAbsences(Request $request, $student_id)
    {
        $student = Student::with('class')->findOrFail($student_id);

        $query = Absence::with([
            'room.subject', 
            'excuse' => function ($q) use ($student_id) {
                $q->where('student_id', $student_id);
            }
        ])->where('student_id', $student_id);

        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        if ($request->filled('status')) {
            $status = $request->status;

            if ($status === 'approved') {
                $query->whereHas('excuse', function ($q) {
                    $q->where('status', 'approved');
                });
            } elseif ($status === 'pending') {
                $query->whereHas('excuse', function ($q) {
                    $q->where('status', 'pending');
                });
            } elseif ($status === 'unexcused') {
                $query->where(function ($q) {
                    $q->whereDoesntHave('excuse')
                        ->orWhereHas('excuse', function ($excuseQuery) {
                            $excuseQuery->where('status', 'rejected');
                        });
                });
            }
        }

        $absences = $query->latest('date')->paginate(15)->withQueryString();

        return view('pages.students.absences', compact('student', 'absences'));
    }
}