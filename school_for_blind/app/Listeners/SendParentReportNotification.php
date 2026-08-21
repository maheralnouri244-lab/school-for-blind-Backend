<?php

namespace App\Listeners;

use App\Events\ParentReportGenerated;
use App\Jobs\SendFcmNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendParentReportNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 3;

   public function handle(ParentReportGenerated $event): void
{
    $student = $event->student;
    $student->loadMissing('parent');

    $parent = $student->parent;

    if (!$parent) {
        \Illuminate\Support\Facades\Log::warning("Report Notification Skipped: No parent found for student ID: {$student->id}");
        return;
    }

    \Illuminate\Support\Facades\Log::info("Parent found for student {$student->id}, parent token: " . ($parent->fcm_token ?? 'NULL'));

    $studentName = $student->fullname ?? 'الطالب';
    $reportType = $event->reportType;
    $refDate = $event->referenceDate;

    $typeArabic = match ($reportType) {
        'daily'   => 'اليومي',
        'monthly' => 'الشهري',
        'yearly'  => 'السنوي',
        default   => 'الدوري',
    };

    $title = "📊 صدور التقرير {$typeArabic}: {$studentName}";
    $body  = "ولي الأمر المحترم، تم إصدار التقرير {$typeArabic} للطالب ({$studentName}) لتاريخ ({$refDate}). يمكنك الاطلاع عليه الآن.";

    $data = [
        'type'           => 'parent_report_generated',
        'report_type'    => $reportType,
        'reference_date' => $refDate,
        'student_id'     => (string) $student->id,
        'screen'         => 'StudentReportScreen',
    ];

    SendFcmNotificationJob::dispatch($parent, $title, $body, $data);
}}