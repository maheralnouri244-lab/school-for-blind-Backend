<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Punishable;
use App\Models\Punishment;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PunishmentController extends Controller
{
    public function indexTypes()
    {
        $punishments = Punishment::orderBy('level', 'asc')->get();
        return view('pages.punishments.types.index', compact('punishments'));
    }

    public function storeType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1',
        ]);

        Punishment::create([
            'name' => $request->name,
            'level' => $request->level,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
        ]);

        return back()->with('success', 'تم إضافة نوع العقوبة بنجاح!');
    }

    public function apply(Request $request)
    {
        $request->validate([
            'punishable_id' => 'required|integer',
            'punishable_type' => 'required|string',
            'punishment_id' => 'required|exists:punishments,id',
            'duration_minutes' => 'nullable|integer|min:1',
            'report_id' => 'nullable|exists:reports,id',
        ]);

        $punishment = Punishment::findOrFail($request->punishment_id);

        $minutes = $request->duration_minutes ?? $punishment->duration_minutes;
        
        $expiresAt = $minutes ? Carbon::now()->addMinutes($minutes) : null;

        Punishable::create([
            'punishable_id' => $request->punishable_id,
            'punishable_type' => $request->punishable_type,
            'punishment_id' => $punishment->id,
            'admin_id' => Auth::guard('admin')->id(), 
            'expires_at' => $expiresAt,
        ]);

        if ($request->filled('report_id')) {
            Report::where('id', $request->report_id)->update(['status' => 'reviewed']);
            return back()->with('success', 'تم تطبيق العقوبة وإغلاق البلاغ بنجاح!');
        }

        return back()->with('success', 'تم تطبيق العقوبة على المستخدم بنجاح!');
    }

    public function activePunishments(Request $request)
    {
        $activePunishments = Punishable::with(['punishment', 'admin', 'punishable'])
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', Carbon::now());
            })
            ->latest()
            ->paginate(15);

        return view('pages.punishments.active', compact('activePunishments'));
    }

    public function revoke($id)
    {
        $punishable = Punishable::findOrFail($id);
        
        $punishable->update([
            'expires_at' => Carbon::now()
        ]);

        return back()->with('success', 'تم إلغاء العقوبة ورفع التقييد عن المستخدم.');
    }
}