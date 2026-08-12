<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Stripe\StripeClient;

class FinancialDashboardController extends Controller
{
    private function getTransactionsTable()
    {
        return Schema::hasTable('school_transactions') ? 'school_transactions' : 'transactions';
    }

    private function getWalletsTable()
    {
        return Schema::hasTable('school_wallets') ? 'school_wallets' : 'wallets';
    }

    private function calculateTeacherLessonsSalary($teacherId)
    {
        $teacher = Teacher::with('subjects')->find($teacherId);

        if (!$teacher) {
            return ['salary' => 0, 'completed' => 0, 'unassigned' => 0];
        }

        $rooms = Room::where('creator_type', Teacher::class)
            ->where('creator_id', $teacherId)
            ->where('status', 'ended')
            ->where('payment_status', 'unpaid')
            ->get();

        $calculatedSalary = 0;
        $completedLessons = 0;
        $unassignedRooms = 0;

        foreach ($rooms as $room) {
            if (!$room->subject_id) {
                $unassignedRooms++;
                continue;
            }

            $subjectPivot = $teacher->subjects()->get()->where('id', $room->subject_id)->first();
            $priceForLesson = $subjectPivot ? $subjectPivot->pivot->price_for_lesson : 0;

            $calculatedSalary += $priceForLesson;
            $completedLessons++;
        }

        return [
            'salary' => $calculatedSalary,
            'completed' => $completedLessons,
            'unassigned' => $unassignedRooms,
        ];
    }

    public function index()
    {
        $txTable = $this->getTransactionsTable();
        $walletsTable = $this->getWalletsTable();

        $walletBalance = DB::table($walletsTable)->where('id', 1)->value('balance') ?? 0;
        $totalDonations = DB::table('donations')->where('status', 'completed')->sum('amount');
        $paidSalaries = DB::table($txTable)->where('type', 'withdrawal')->where('description', 'like', '%راتب%')->sum('amount');
        $pendingRewards = DB::table('point_redemption_requests')->where('status', 'pending')->count();

        $teachers = Teacher::all();
        $pendingTeachers = collect();

        foreach ($teachers as $teacher) {
            $calculation = $this->calculateTeacherLessonsSalary($teacher->id);
            $teacher->salary = $calculation['salary'];
            $teacher->completed_lessons = $calculation['completed'];
            $teacher->unassigned_lessons = $calculation['unassigned'];
            if ($teacher->salary > 0) {
                $pendingTeachers->push($teacher);
            }
        }

        $recentTransactions = DB::table($txTable)->orderBy('created_at', 'desc')->limit(5)->get();

        $chartLabels = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'];
        $chartDeposits = [
            DB::table($txTable)->where('type', 'deposit')->whereMonth('created_at', 1)->sum('amount'),
            DB::table($txTable)->where('type', 'deposit')->whereMonth('created_at', 2)->sum('amount'),
            DB::table($txTable)->where('type', 'deposit')->whereMonth('created_at', 3)->sum('amount'),
            DB::table($txTable)->where('type', 'deposit')->whereMonth('created_at', 4)->sum('amount'),
            DB::table($txTable)->where('type', 'deposit')->whereMonth('created_at', 5)->sum('amount'),
            DB::table($txTable)->where('type', 'deposit')->whereMonth('created_at', 6)->sum('amount'),
        ];
        $chartWithdrawals = [
            DB::table($txTable)->where('type', 'withdrawal')->whereMonth('created_at', 1)->sum('amount'),
            DB::table($txTable)->where('type', 'withdrawal')->whereMonth('created_at', 2)->sum('amount'),
            DB::table($txTable)->where('type', 'withdrawal')->whereMonth('created_at', 3)->sum('amount'),
            DB::table($txTable)->where('type', 'withdrawal')->whereMonth('created_at', 4)->sum('amount'),
            DB::table($txTable)->where('type', 'withdrawal')->whereMonth('created_at', 5)->sum('amount'),
            DB::table($txTable)->where('type', 'withdrawal')->whereMonth('created_at', 6)->sum('amount'),
        ];

        return view('pages.financial.index', compact(
            'walletBalance',
            'totalDonations',
            'paidSalaries',
            'pendingRewards',
            'pendingTeachers',
            'recentTransactions',
            'chartLabels',
            'chartDeposits',
            'chartWithdrawals'
        ));
    }

    public function donations(Request $request)
    {
        $query = DB::table('donations')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where('donor_name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('target')) {
            $query->where('donation_target', $request->target);
        }

        $donations = $query->paginate(10);

        $completedAmount = DB::table('donations')->where('status', 'completed')->sum('amount');
        $pendingAmount = DB::table('donations')->where('status', 'pending')->sum('amount');
        $donorsCount = DB::table('donations')->whereNotNull('donor_name')->distinct('donor_name')->count('donor_name');
        $avgDonation = DB::table('donations')->where('status', 'completed')->avg('amount') ?? 0;

        return view('pages.financial.donations', compact(
            'donations',
            'completedAmount',
            'pendingAmount',
            'donorsCount',
            'avgDonation'
        ));
    }

    public function salaries()
    {
        $txTable = $this->getTransactionsTable();

        $allTeachers = Teacher::all();
        $pendingTeachers = collect();
        $totalPendingSalaries = 0;

        foreach ($allTeachers as $teacher) {
            $calc = $this->calculateTeacherLessonsSalary($teacher->id);
            $teacher->salary = $calc['salary'];
            $teacher->completed_lessons = $calc['completed'];
            $teacher->unassigned_lessons = $calc['unassigned'];

            $totalPendingSalaries += $teacher->salary;
            $pendingTeachers->push($teacher);
        }

        $totalPaidSalaries = DB::table($txTable)->where('type', 'withdrawal')->where('description', 'like', '%راتب%')->sum('amount');
        $teachersCount = $allTeachers->count();

        return view('pages.financial.salaries', compact(
            'pendingTeachers',
            'totalPendingSalaries',
            'totalPaidSalaries',
            'teachersCount'
        ));
    }

    public function paySalary(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $teacherId = $request->teacher_id;
        $amountInEur = $request->amount;
        $amountInCents = $amountInEur * 100;

        $teacher = Teacher::findOrFail($teacherId);

        if (!$teacher->stripe_account_id) {
            return redirect()->back()->with('error', 'الأستاذ ليس لديه حساب بنكي مربوط في Stripe');
        }

        $walletsTable = $this->getWalletsTable();
        $txTable = $this->getTransactionsTable();

        $wallet = DB::table($walletsTable)->where('id', 1)->first();
        if (!$wallet || $wallet->balance < $amountInEur) {
            return redirect()->back()->with('error', 'رصيد المدرسة غير كافٍ لتحويل الراتب');
        }

        try {
            $stripeSecret = config('cashier.secret') ?? env('STRIPE_SECRET');
            $stripe = new StripeClient($stripeSecret);

            $transfer = $stripe->transfers->create([
                'amount' => $amountInCents,
                'currency' => 'eur',
                'destination' => $teacher->stripe_account_id,
                'description' => 'راتب مستحق للأستاذ: ' . $teacher->full_name,
            ]);

            DB::table($walletsTable)->where('id', 1)->decrement('balance', $amountInEur);

            DB::table($txTable)->insert([
                'type' => 'withdrawal',
                'amount' => $amountInEur,
                'description' => 'تم تحويل راتب للأستاذ ' . $teacher->full_name . ' برقم عملية: ' . $transfer->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Room::where('creator_type', Teacher::class)
                ->where('creator_id', $teacherId)
                ->where('status', 'ended')
                ->where('payment_status', 'unpaid')
                ->whereNotNull('subject_id')
                ->update(['payment_status' => 'paid']);

            return redirect()->back()->with('success', 'تم تحويل الراتب بنجاح للأستاذ ' . $teacher->full_name);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'فشل تحويل الراتب: ' . $e->getMessage());
        }
    }

    public function rewards(Request $request)
    {
        $query = DB::table('point_redemption_requests')
            ->join('students', 'point_redemption_requests.student_id', '=', 'students.id')
            ->select(
                'point_redemption_requests.*',
                'students.fullname as student_name',
                'students.phone as student_phone',
                'students.level as student_level'
            )
            ->orderBy('point_redemption_requests.created_at', 'desc');

        if ($request->filled('search')) {
            $query->where('students.fullname', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('point_redemption_requests.status', $request->status);
        }

        $rewardRequests = $query->paginate(10);

        $pendingRequestsCount = DB::table('point_redemption_requests')->where('status', 'pending')->count();
        $approvedRequestsCount = DB::table('point_redemption_requests')->where('status', 'approved')->count();
        $totalPointsRedeemed = DB::table('point_redemption_requests')->where('status', 'approved')->sum('points_to_redeem');
        $totalRewardsCost = DB::table('point_redemption_requests')->where('status', 'approved')->sum('amount_paid');

        return view('pages.financial.rewards', compact(
            'rewardRequests',
            'pendingRequestsCount',
            'approvedRequestsCount',
            'totalPointsRedeemed',
            'totalRewardsCost'
        ));
    }

    public function approveReward($id)
    {
        DB::table('point_redemption_requests')->where('id', $id)->update([
            'status' => 'approved',
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'تمت الموافقة على طلب المكافأة بنجاح.');
    }

    public function rejectReward($id)
    {
        DB::table('point_redemption_requests')->where('id', $id)->update([
            'status' => 'rejected',
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'تم رفض طلب المكافأة.');
    }

    public function transactions(Request $request)
    {
        $txTable = $this->getTransactionsTable();

        $query = DB::table($txTable)->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                    ->orWhere('id', $request->search);
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate(15);

        $totalDeposits = DB::table($txTable)->where('type', 'deposit')->sum('amount');
        $totalWithdrawals = DB::table($txTable)->where('type', 'withdrawal')->sum('amount');
        $transactionsCount = DB::table($txTable)->count();

        return view('pages.financial.transactions', compact(
            'transactions',
            'totalDeposits',
            'totalWithdrawals',
            'transactionsCount'
        ));
    }
}