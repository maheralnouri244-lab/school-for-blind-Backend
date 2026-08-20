<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Caregiver;
use App\Models\Note;
use App\Models\PointSuggestion;
use App\Models\Student;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MySpaceController extends Controller
{
    public function index()
    {
        if (!Auth::guard('admin')->check()) {
            abort(403);
        }

        $publicNotes = Note::with('admin')
            ->where('type', 'public')
            ->latest()
            ->take(30)
            ->get();

        $privateNotes = Note::where('type', 'private')
            ->where('admin_id', Auth::guard('admin')->id())
            ->latest()
            ->take(30)
            ->get();

        $role = Auth::guard('admin')->user()->role;

        if ($role === 'Super Admin') {
            $pendingSuggestions = PointSuggestion::with('student')
                ->where('status', 'pending')
                ->latest()
                ->take(30)
                ->get();

            $admins = Admin::latest()->get();
            return view('pages.my_space.super_admin', compact('publicNotes', 'privateNotes', 'pendingSuggestions', 'admins'));
        }

        return match ($role) {
            'Super Admin' => view('pages.my_space.super_admin', compact('publicNotes', 'privateNotes')),
            'Academic Manager' => view('pages.my_space.academic_manager', compact('publicNotes', 'privateNotes')),
            'Moderator' => view('pages.my_space.moderator', compact('publicNotes', 'privateNotes')),
            'Support Agent' => view('pages.my_space.support_agent', compact('publicNotes', 'privateNotes')),
            'Data Entry' => view('pages.my_space.data_entry', compact('publicNotes', 'privateNotes')),
            'Financial Manager' => view('pages.my_space.financial_manager', compact('publicNotes', 'privateNotes')),
            default => abort(403),
        };
    }

    public function storeNote(Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            abort(403);
        }

        $request->validate([
            'content' => 'required|string',
            'type' => 'required|in:public,private',
        ]);

        Note::create([
            'admin_id' => Auth::guard('admin')->id(),
            'type' => $request->type,
            'content' => $request->content,
        ]);

        return back();
    }

    public function searchUserForReward(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'type' => 'required|in:manual_student,manual_teacher'
        ]);

        if ($request->type === 'manual_student') {
            $user = \App\Models\Student::where('phone', $request->phone)
                ->first(['id', 'fullname as name', 'phone']);
        } else {
            $user = \App\Models\Teacher::where('phone', $request->phone)
                ->first(['id', 'full_name as name', 'phone']);
        }

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'لم يتم العثور على مستخدم بهذا الرقم.']);
        }

        return response()->json(['status' => 'success', 'data' => $user]);
    }

    public function resetStudentPassword(Request $request, WhatsAppService $whatsApp)
    {
        $role = Auth::guard('admin')->user()->role;
        if (!in_array($role, ['Super Admin', 'Academic Manager'])) {
            return response()->json(['success' => false, 'message' => 'غير مصرح لك بالقيام بهذه العملية.']);
        }
        $request->validate([
            'phone' => 'required|string|exists:students,phone'
        ], [
            'phone.exists' => 'لم يتم العثور على طالب بهذا الرقم.'
        ]);
        $student = Student::where('phone', $request->phone)->first();
        if (!$student->parent_id) {
            return response()->json(['success' => false, 'message' => 'هذا الطالب ليس لديه حساب ولي أمر مرتبط به.']);
        }
        try {
            DB::transaction(function () use ($student, $whatsApp) {
                $newPassword = Str::lower(Str::random(10));
                $caregiver = Caregiver::findOrFail($student->parent_id);
                $caregiver->password = Hash::make($newPassword);
                $caregiver->save();
                $whatsApp->sendStudentinfo($student->phone, $student->fullname, $student->parent_phone, $newPassword);
            });
            return response()->json(['success' => true, 'message' => 'تم إعادة تعيين كلمة المرور وإرسالها لولي الأمر بنجاح.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء العملية: ' . $e->getMessage()]);
        }
    }

    public function getStudentInfoForReset(Request $request)
    {
        $request->validate([
            'phone' => 'required|string'
        ]);

        $student = Student::where('phone', $request->phone)->first();

        if (!$student) {
            return response()->json(['status' => 'error', 'message' => 'لم يتم العثور على طالب بهذا الرقم.']);
        }

        if (!$student->parent_id) {
            return response()->json(['status' => 'error', 'message' => 'هذا الطالب ليس لديه حساب ولي أمر مرتبط به.']);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'name' => $student->fullname,
                'phone' => $student->phone,
                'parent_phone' => $student->parent_phone,
            ]
        ]);
    }

    public function storeAdmin(Request $request)
    {
        if (Auth::guard('admin')->user()->role !== 'Super Admin') {
            abort(403);
        }

        $request->validate([
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|min:6',
            'role' => 'required|in:Super Admin,Academic Manager,Moderator,Support Agent,Data Entry,Financial Manager'
        ]);

        Admin::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'email_verified_at' => now(),
        ]);

        return back()->with('success', 'تم إضافة المدير بنجاح!');
    }
    public function updateAdmin(Request $request, $id)
    {
        if (Auth::guard('admin')->user()->role !== 'Super Admin')
            abort(403);
        $admin = Admin::findOrFail($id);
        $request->validate([
            'role' => 'required|in:Super Admin,Academic Manager,Moderator,Support Agent,Data Entry,Financial Manager',
            'password' => 'nullable|min:6',
        ]);
        $admin->role = $request->role;
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
            $admin->setRememberToken(Str::random(60));
            DB::table('sessions')->where('user_id', $admin->id)->delete();
        }
        $admin->save();
        return back()->with('success', 'تم تعديل بيانات المدير بنجاح.');
    }
    public function deleteAdmin($id)
    {
        if (Auth::guard('admin')->user()->role !== 'Super Admin')
            abort(403);
        if (Auth::guard('admin')->id() == $id) {
            return back()->with('error', 'لا يمكنك حذف حسابك الشخصي!');
        }
        $admin = Admin::findOrFail($id);
        DB::table('sessions')->where('user_id', $admin->id)->delete();
        $admin->delete();
        return back()->with('success', 'تم حذف المدير بنجاح.');
    }
}