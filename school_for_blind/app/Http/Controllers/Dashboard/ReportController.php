<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Punishment;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with(['reporter', 'reported', 'reportable']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending');
        }

        $punishmentQuery = Punishment::query();
        if ($request->filled('user_type')) {
            $type = $request->user_type === 'student' ? 'App\Models\Student' : 'App\Models\Teacher';
            $query->where('reported_type', $type);
            $targetType = $request->user_type === 'student' ? 'student' : 'teacher';
            $punishmentQuery->whereIn('target_type', [$targetType, 'all']);
        }

        $reports = $query->latest()->paginate(10);
        $punishments = $punishmentQuery->orderBy('level', 'asc')->get();

        return view('pages.reports.index', compact('reports', 'punishments'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:reviewed,dismissed'
        ]);

        $report = Report::findOrFail($id);
        $report->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'تم تحديث حالة البلاغ بنجاح.');
    }
}