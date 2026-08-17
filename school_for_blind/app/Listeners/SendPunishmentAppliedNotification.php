<?php

namespace App\Listeners;

use App\Events\PunishmentApplied;
use App\Jobs\SendFcmNotificationJob;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPunishmentAppliedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 3;

    public function handle(PunishmentApplied $event): void
    {
        $user = $event->user;
        $reason = $event->reason;
        $userName = $user->fullname ?? $user->name ?? 'المستخدم';

        if ($user instanceof Teacher) {
            $teacherTitle = "⚠️ تنبيه إداري";
            $teacherBody  = "الأستاذ المحترم {$userName}، تم تطبيق عقوبة إدارية بحق حسابكم بسبب: ({$reason}). يرجى مراجعة الإدارة.";

            $data = [
                'type'   => 'teacher_punishment_applied',
                'screen' => 'TeacherProfileScreen',
            ];

            SendFcmNotificationJob::dispatch($user, $teacherTitle, $teacherBody, $data);
            return;
        }

        if ($user instanceof Student) {
            $user->loadMissing('parent');

            $data = [
                'type'   => 'punishment_applied',
                'screen' => 'PunishmentsScreen',
            ];

            $studentTitle = "⚠️ تنبيه إداري بخصوص عقوبة";
            $studentBody  = "تم فرض عقوبة عليك بسبب: ({$reason}). يرجى الالتزام بالتعليمات.";
            SendFcmNotificationJob::dispatch($user, $studentTitle, $studentBody, $data);

            $parent = $user->parent;
            if ($parent) {
                $parentTitle = "🚨 تنبيه سلوكي: {$userName}";
                $parentBody  = "ولي الأمر المحترم، نود إعلامكم بفرض عقوبة على الطالب ({$userName}) بسبب: ({$reason}).";

                $parentData = array_merge($data, [
                    'student_id' => (string) $user->id,
                ]);

                SendFcmNotificationJob::dispatch($parent, $parentTitle, $parentBody, $parentData);
            }
        }
    }
}