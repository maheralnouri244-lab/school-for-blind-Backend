<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PointSuggestion;

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

            return view('pages.my_space.super_admin', compact('publicNotes', 'privateNotes', 'pendingSuggestions'));
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
}