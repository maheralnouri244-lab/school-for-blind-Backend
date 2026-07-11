<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Punishable;

class ContentMonitorController extends Controller
{
    public function index()
    {
        $pendingReportsCount = Report::where('status', 'pending')->count();
        $activePunishmentsCount = Punishable::whereNull('expires_at')->orWhere('expires_at', '>', now())->count();

        return view('pages.content-monitor.index', [
            'pendingReportsCount' => $pendingReportsCount,
            'activePunishmentsCount' => $activePunishmentsCount,
        ]);
    }
}