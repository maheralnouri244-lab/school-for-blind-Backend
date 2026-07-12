<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $query = SupportTicket::with('sender');

        $isSuperOrSupport = in_array($admin->role, ['Support Agent', 'Super Admin']);

        if (!$isSuperOrSupport) {
            $query->where('assigned_department', $admin->role);
        }

        if ($request->has('statuses') && is_array($request->statuses) && count($request->statuses) > 0) {
            $query->whereIn('status', $request->statuses);
        }

        if ($request->has('priorities') && is_array($request->priorities) && count($request->priorities) > 0) {
            $query->whereIn('priority', $request->priorities);
        }

        if ($isSuperOrSupport && $request->has('departments') && is_array($request->departments) && count($request->departments) > 0) {

            $inputDepartments = $request->departments;
            if (in_array('unassigned', $inputDepartments)) {
                $deps = array_diff($inputDepartments, ['unassigned']);
                $query->where(function ($q) use ($deps) {
                    $q->whereNull('assigned_department');
                    if (!empty($deps)) {
                        $q->orWhereIn('assigned_department', $deps);
                    }
                });
            } else {
                $query->whereIn('assigned_department', $inputDepartments);
            }
        }

        if ($request->filled('sort_by_priority')) {
            $direction = $request->sort_by_priority === 'asc' ? 'ASC' : 'DESC';
            $query->orderByRaw("FIELD(priority, 'low', 'medium', 'high', 'urgent') $direction")
                ->orderBy('created_at', 'desc');
        } else {
            $query->latest();
        }

        $tickets = $query->paginate(15)->appends($request->query());

        return view('pages.support.index', compact('tickets', 'isSuperOrSupport'));
    }

    public function assign(Request $request, $id)
    {
        $ticket = SupportTicket::findOrFail($id);

        $request->validate([
            'assigned_department' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $ticket->update([
            'assigned_department' => $request->assigned_department,
            'priority' => $request->priority,
            'classified_by' => Auth::guard('admin')->id(),
            'status' => 'in_progress'
        ]);

        return redirect()->back()->with('success', 'تم تصنيف الطلب وتوجيهه للقسم المختص بنجاح.');
    }

    public function updateStatus(Request $request, $id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'تم تحديث حالة التذكرة بنجاح.');
    }
}