<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Caregiver;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ArchiveController extends Controller
{
    public function getArchivedStudents(Request $request): JsonResponse
    {
        $students = Student::onlyTrashed()
            ->with(['parent' => function ($query) {
                $query->withTrashed();
            }, 'class'])
            ->when($request->search, function ($query, $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('fullname', 'like', "%{$term}%")
                      ->orWhere('phone', 'like', "%{$term}%");
                });
            })
            ->latest('deleted_at')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $students
        ]);
    }

    public function archiveStudent($id): JsonResponse
    {
        $student = Student::findOrFail($id);
        $student->delete(); 

        return response()->json([
            'status' => 'success',
            'message' => 'تم نقل الطالب إلى الأرشيف بنجاح'
        ]);
    }

    public function restoreStudent($id): JsonResponse
    {
        $student = Student::onlyTrashed()->findOrFail($id);
        $student->restore();

        if ($student->parent_id) {
            Caregiver::onlyTrashed()->where('id', $student->parent_id)->restore();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تم استعادة الطالب بنجاح',
            'data' => $student
        ]);
    }

    public function forceDeleteStudent($id): JsonResponse
    {
        $student = Student::onlyTrashed()->findOrFail($id);
        
        $student->forceDelete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف الطالب نهائياً من قاعدة البيانات'
        ]);
    }

    public function getArchivedCaregivers(): JsonResponse
    {
        $caregivers = Caregiver::onlyTrashed()
            ->with(['students' => function ($query) {
                $query->withTrashed();
            }])
            ->latest('deleted_at')
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $caregivers
        ]);
    }

    public function restoreCaregiver($id): JsonResponse
    {
        $caregiver = Caregiver::onlyTrashed()->findOrFail($id);
        $caregiver->restore();

        return response()->json([
            'status' => 'success',
            'message' => 'تم استعادة حساب ولي الأمر بنجاح'
        ]);
    }
}