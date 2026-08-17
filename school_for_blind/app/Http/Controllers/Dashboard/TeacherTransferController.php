<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherTransferController extends Controller
{
    public function getTeacherByPhone(Request $request)
    {
        $request->validate(['phone' => 'required|string']);

        $teacher = Teacher::where('phone', $request->phone)
            ->where('status', 'approved')
            ->withCount(['classes', 'quizzes'])
            ->first(['id', 'full_name', 'phone']);

        if (!$teacher) {
            return response()->json(['status' => 'error', 'message' => 'لم يتم العثور على معلم فعال بهذا الرقم.'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $teacher]);
    }

    public function dismissAndTransfer(Request $request)
    {
        $request->validate([
            'old_teacher_id' => 'required|exists:teachers,id',
            'new_teacher_id' => 'required|exists:teachers,id|different:old_teacher_id',
            'confirmation' => 'required|in:تأكيد'
        ]);

        DB::beginTransaction();
        try {
            $oldTeacher = Teacher::findOrFail($request->old_teacher_id);
            $newTeacher = Teacher::findOrFail($request->new_teacher_id);
            $oldTeacher->status = 'dismissed';
            $oldTeacher->save();
            $oldTeacher->subjects()->detach();
            $oldClassIds = $oldTeacher->classes()->pluck('classes.id')->toArray();
            if (!empty($oldClassIds)) {
                $newTeacher->classes()->syncWithoutDetaching($oldClassIds);
                $oldTeacher->classes()->detach();
            }

            Quiz::where('teacher_id', $oldTeacher->id)
                ->update(['teacher_id' => $newTeacher->id]);

            Question::where('teacher_id', $oldTeacher->id)
                ->update(['teacher_id' => $newTeacher->id]);

            DB::commit();

            return back()->with('success', 'تم فصل الأستاذ ونقل بياناته بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء النقل: ' . $e->getMessage());
        }
    }
}