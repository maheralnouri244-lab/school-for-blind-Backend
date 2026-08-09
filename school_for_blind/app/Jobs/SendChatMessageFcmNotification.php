<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Str;

class SendChatMessageFcmNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function handle(FcmService $fcmService): void
    {
        $message = $this->message;
        $conversation = $message->conversation;
        $sender = $message->sender;

        if (!$conversation) {
            Log::warning('FCM Chat: Conversation not found for message ID: ' . $message->id);
            return;
        }

        $senderName = $sender->fullname ?? $sender->name ?? 'مستخدم';
        $senderType = class_basename($sender);

        Log::info("--- بدء معالجة إشعار المحادثة ---");
        Log::info("المرسل: {$senderName} ({$senderType}) - ID: {$sender->id}");

        $targetTokens = [];

        if ($sender instanceof Teacher) {
            $classIds = $sender->classes()->pluck('classes.id');

            $students = Student::whereIn('class_id', $classIds)
                ->whereNotNull('fcm_token')
                ->get(['id', 'fullname', 'fcm_token']);

            foreach ($students as $student) {
                Log::info("مستهدف (طالب): {$student->fullname} - ID: {$student->id}");
                $targetTokens[] = $student->fcm_token;
            }
        } 
        elseif ($sender instanceof Student) {
            $teacherId = $conversation->teacher_id ?? optional($conversation->parent)->teacher_id;
            
            if ($teacherId) {
                $teacher = Teacher::find($teacherId);
                if ($teacher && $teacher->fcm_token) {
                    Log::info("مستهدف (معلم): {$teacher->fullname} - ID: {$teacher->id}");
                    $targetTokens[] = $teacher->fcm_token;
                } else {
                    Log::warning("المعلم المسؤول (ID: {$teacherId}) ليس لديه fcm_token مسجل!");
                }
            } else {
                Log::warning("لم يتم العثور على teacher_id مرتبط بالمحادثة رقم: {$conversation->id}");
            }
        }

        $targetTokens = array_unique($targetTokens);

        if (empty($targetTokens)) {
            Log::warning("نتيجة: لم يتم العثور على أي fcm_token لإرسال الإشعار إليه.");
            return;
        }

        Log::info("عدد التوكنات المستهدفة: " . count($targetTokens));

        $bodyText = $message->body 
            ? Str::limit($message->body, 60) 
            : 'أرسل المرفق (' . ($message->attachment_type ?? 'ملف') . ')';

        $data = [
            'type' => 'chat_message',
            'conversation_id' => (string) $conversation->id,
            'sender_id' => (string) $sender->id,
            'url' => '/conversations/' . $conversation->id
        ];

        foreach ($targetTokens as $token) {
            $isSent = $fcmService->sendNotification(
                $token,
                'رسالة جديدة من ' . $senderName,
                $bodyText,
                $data
            );

            if ($isSent) {
                Log::info("تم إرسال الإشعار بنجاح للتوكن: " . Str::limit($token, 15));
            } else {
                Log::error("فشل إرسال الإشعار للتوكن: " . Str::limit($token, 15));
            }
        }
    }
}