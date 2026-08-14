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

        if (!$teacher) {
            return;
        }

        $title = "💰 إشعار تحويل الراتب";
        $body  = "تم تحويل مستحقاتك المالية بمبلغ €{$event->amount} إلى حسابك المالي بنجاح.";

        $data = [
            'type'   => 'salary_transferred',
            'amount' => (string) $event->amount,
            'screen' => 'WalletScreen',
        ];

        SendFcmNotificationJob::dispatch($teacher, $title, $body, $data);
    }
}