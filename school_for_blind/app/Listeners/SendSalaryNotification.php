<?php

namespace App\Listeners;

use App\Events\SalaryTransferred;
use App\Jobs\SendFcmNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendSalaryNotification implements ShouldQueue
{
    public function handle(SalaryTransferred $event): void
    {
        $teacher = $event->teacher;
        $amount = $event->amount;
        
        $title = "💰 وصول دفعة مالية!";
        $body = "تم تحويل راتبك بقيمة {$amount} بنجاح. شكراً لجهودك!";

        if ($teacher->fcm_token) {
            SendFcmNotificationJob::dispatch($teacher->fcm_token, $title, $body);
        }
    }
}