<?php

namespace App\Listeners;

use App\Events\PunishmentRemoved;
use App\Jobs\SendFcmNotificationJob;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPunishmentRemovedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 3;

    public function handle(PunishmentRemoved $event): void
    {
        $user = $event->user;
        $punishmentName = $event->punishmentName;
        $userName = $user->fullname ?? $user->name ?? 'المستخدم';

        if ($user instanceof Teacher) {
            $teacherTitle = "✅ رفع التقييد الإداري";
            $teacherBody  = "الأستاذ المحترم {$userName}، تم رفع وإلغاء عقوبة ({$punishmentName}) عن حسابكم بنجاح.";

            $data = [
                'type'   => 'teacher_punishment_removed',
                'screen' => 'TeacherProfileScreen',
            ];

            SendFcmNotificationJob::dispatch($user, $teacherTitle, $teacherBody, $data);
            return;
        }

        if ($user instanceof Student) {
            $user->loadMissing('parent');

            $data = [
                'type'   => 'punishment_removed',
                'screen' => 'ProfileScreen',
            ];

            $studentTitle = "✅ إلغاء العقوبة";
            $studentBody  = "تم رفع وإلغاء عقوبة ({$punishmentName}) عنك. نرجو لك دوام التوفيق.";
            SendFcmNotificationJob::dispatch($user, $studentTitle, $studentBody, $data);

            $parent = $user->parent;
            if ($parent) {
                $parentTitle = "✅ إشعار إداري: {$userName}";
                $parentBody  = "ولي الأمر المحترم، تم رسمياً رفع وإلغاء عقوبة ({$punishmentName}) عن الطالب ({$userName}).";

                $parentData = array_merge($data, [
                    'student_id' => (string) $user->id,
                ]);

                SendFcmNotificationJob::dispatch($parent, $parentTitle, $parentBody, $parentData);
            }
        }
    }
}