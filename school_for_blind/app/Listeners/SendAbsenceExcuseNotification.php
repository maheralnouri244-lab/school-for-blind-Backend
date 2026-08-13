<?php

namespace App\Listeners;

use App\Events\AbsenceExcuseStatusUpdated;
use App\Jobs\SendFcmNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendAbsenceExcuseNotification implements ShouldQueue
{
    public function handle(AbsenceExcuseStatusUpdated $event): void
    {
        $excuse = $event->excuse;

        $caregiver = $excuse->caregiver;

        if (!$caregiver || !is_object($caregiver)) {
            Log::warning("No caregiver Model instance found for AbsenceExcuse ID: {$excuse->id}");
            return;
        }

        $studentName = $excuse->student ? $excuse->student->fullname : 'ابنكم';

        if ($excuse->status === 'approved') {
            $title = "تم قبول تبرير الغياب 🟢";
            $body  = "ولي الأمر المحترم، تمت الموافقة على طلب تبرير الغياب المقدم للطالب ({$studentName}).";
        } else {
            $title = "تم رفض تبرير الغياب 🔴";
            $body  = "ولي الأمر المحترم، تم رفض طلب تبرير الغياب المقدم للطالب ({$studentName}).";
        }

        $data = [
            'type'      => 'absence_excuse_status',
            'excuse_id' => (string) $excuse->id,
            'status'    => $excuse->status,
            'screen'    => 'ExcuseDetailsScreen',
        ];

        SendFcmNotificationJob::dispatch($caregiver, $title, $body, $data);
    }
}