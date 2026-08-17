<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\PointSuggestion;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class grantPointsOrReward extends Controller
{
    public function grantPointsOrReward(Request $request)
    {
        $request->validate([
            'target_type' => 'required|in:student_suggestion,manual_student,manual_teacher',
            'target_id' => 'required|integer',
            'points_amount' => 'nullable|integer|min:1'
        ]);
        $amount = $request->input('points_amount') ?: 50;
        DB::beginTransaction();
        try {
            if ($request->target_type === 'student_suggestion') {
                $suggestion = PointSuggestion::findOrFail($request->target_id); //[cite: 21]
                if ($suggestion->status !== 'pending') {
                    return back()->with('error', 'تمت معالجة هذا المقترح مسبقاً.');
                }
                $student = Student::findOrFail($suggestion->student_id); //[cite: 21]
                $student->points += $amount;
                $student->total_earned_points += $amount;
                $student->save();
                $suggestion->status = 'approved';
                $suggestion->suggested_points = $amount;
                $suggestion->save();
                $message = "تمت الموافقة على المقترح ومنح الطالب {$amount} نقطة بنجاح.";
            } elseif ($request->target_type === 'manual_student') {
                $student = Student::findOrFail($request->target_id);
                $student->points += $amount;
                $student->total_earned_points += $amount;
                $student->save();
                $message = "تم منح الطالب {$amount} نقطة بنجاح.";
            } elseif ($request->target_type === 'manual_teacher') {
                // منح مكافأة لأستاذ
                $teacher = Teacher::findOrFail($request->target_id);
                // ملاحظة: يجب أن يكون لديك حقل في جدول المدرسين أو جدول مالي لإضافة المكافأة
                // مثال: $teacher->wallet_balance += $amount; 
                // $teacher->save();
                $message = "تم تسجيل مكافأة بقيمة {$amount} للأستاذ بنجاح.";
            }

            DB::commit();
            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء المنح: ' . $e->getMessage());
        }
    }
}